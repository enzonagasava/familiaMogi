<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ecommerce\StoreAnuncioRequest;
use App\Http\Requests\Admin\Ecommerce\UpdateAnuncioRequest;
use App\Models\Listing;
use App\Models\Produto;
use App\Services\Integrations\EGroceryContractSerializer;
use App\Services\Integrations\FamiliaMogiWebhookPublisher;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Predis\Client as PredisClient;


class AnuncioController extends Controller
{
    private const ANUNCIO_TIPOS = [
        'Google_ads' => 'Google Ads',
        'Instagram_ads' => 'Instagram Ads',
        'Whatsapp_campaign' => 'Campanha de WhatsApp',
        'Site_anuncio' => 'Anúncio do Site',
    ];

    public function index()
    {
        $listings = Listing::with(['produto.imagens'])
            ->whereNotNull('produto_id')
            ->latest()
            ->get()
            ->map(function (Listing $listing) {
                $produto = $listing->produto;

                return [
                    'id' => $listing->id,
                    'produto_id' => $listing->produto_id,
                    'anuncio_ativo' => (bool) $listing->anuncio_ativo,
                    'anuncio_status' => $listing->anuncio_status,
                    'anuncio_tipos' => $listing->anuncio_tipos ?? [],
                    'created_at' => optional($listing->created_at)?->format('d/m/Y H:i'),
                    'produto' => $produto ? [
                        'id' => $produto->id,
                        'nome' => $produto->nome,
                        'descricao' => $produto->descricao,
                        'estoque' => $produto->estoque,
                        'imageUrl' => $produto->imagens->first()
                            ? asset('storage/' . $produto->imagens->first()->imagem_path)
                            : null,
                    ] : null,
                ];
            });

        return Inertia::render('admin/ecommerce/anuncios/AnunciosIndex', [
            'listings' => $listings,
            'anuncioTiposLabels' => self::ANUNCIO_TIPOS,
        ]);
    }

    public function create()
    {
        $selectedProdutoId = request()->integer('produto_id');

        return Inertia::render('admin/ecommerce/anuncios/AnunciosCreate', [
            'produtos' => $this->produtoOptions(),
            'anuncioTipos' => self::ANUNCIO_TIPOS,
            'selectedProdutoId' => $selectedProdutoId,
        ]);
    }

    public function store(
        StoreAnuncioRequest $request,
        FamiliaMogiWebhookPublisher $publisher,
        EGroceryContractSerializer $serializer
    )
    {
        $validated = $request->validated();

        $listing = Listing::create([
            'produto_id' => $validated['produto_id'],
            'anuncio_ativo' => $validated['anuncio_ativo'] ?? true,
            'anuncio_status' => $validated['anuncio_status'] ?? null,
            'anuncio_tipos' => $validated['anuncio_tipos'],
        ]);

        $anuncioTipo = $validated['anuncio_tipos'];

        if(in_array('Site_anuncio', $anuncioTipo)){
            $this->publishListingEvent('ad.created', $listing->fresh(['produto.imagens', 'produto.tamanhos']), $publisher, $serializer);            
        };

        return redirect()->route('admin.anuncio.config')->with('success', 'Anúncio criado com sucesso.');
    }

    public function edit(Listing $listing)
    {
        if (!$listing->produto_id) {
            abort(404);
        }

        $listing->load('produto');

        return Inertia::render('admin/ecommerce/anuncios/AnunciosEdit', [
            'listing' => [
                'id' => $listing->id,
                'produto_id' => $listing->produto_id,
                'anuncio_ativo' => (bool) $listing->anuncio_ativo,
                'anuncio_status' => $listing->anuncio_status,
                'anuncio_tipos' => $listing->anuncio_tipos ?? [],
            ],
            'produtos' => $this->produtoOptions(),
            'anuncioTipos' => self::ANUNCIO_TIPOS,
        ]);
    }

    public function update(
        UpdateAnuncioRequest $request,
        Listing $listing,
        FamiliaMogiWebhookPublisher $publisher,
        EGroceryContractSerializer $serializer
    )
    {

        if (!$listing->produto_id) {
            abort(404);
        }

        $validated = $request->validated();

        $listing->update([
            'produto_id' => $validated['produto_id'],
            'anuncio_ativo' => $validated['anuncio_ativo'] ?? false,
            'anuncio_status' => $validated['anuncio_status'] ?? null,
            'anuncio_tipos' => $validated['anuncio_tipos'],
        ]);

        $this->publishListingEvent('ad.updated', $listing->fresh(['produto.imagens', 'produto.tamanhos']), $publisher, $serializer);

        return redirect()->route('admin.anuncio.config')->with('success', 'Anúncio atualizado com sucesso.');
    }

    public function destroy(
        Listing $listing,
        FamiliaMogiWebhookPublisher $publisher,
        EGroceryContractSerializer $serializer
    )
    {
        if (!$listing->produto_id) {
            abort(404);
        }

        $listing->loadMissing('produto.imagens', 'produto.tamanhos');
        $payload = $serializer->listingPayload($listing);
        $listing->delete();
        $payload['status'] = 'deleted';

        try {
            $publisher->publish('ad.deleted', [
                'type' => 'ad',
                'id' => (string) $payload['id'],
                'version' => now()->timestamp,
            ], $payload);
        } catch (\Throwable $exception) {
            Log::channel('familia_mogi_integration')->warning('Failed to queue ad.deleted event', [
                'listing_id' => $listing->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('admin.anuncio.config')->with('success', 'Anúncio removido com sucesso.');
    }

    private function publishListingEvent(
        string $eventType,
        Listing $listing,
        FamiliaMogiWebhookPublisher $publisher,
        EGroceryContractSerializer $serializer
    ): void {
        try {
            $data = $serializer->listingPayload($listing);

            $publisher->publish($eventType, [
                'type' => 'ad',
                'id' => (string) $data['id'],
                'version' => (int) $listing->updated_at?->timestamp,
            ], $data);
        } catch (\Throwable $exception) {
            Log::channel('familia_mogi_integration')->warning('Failed to queue ad event', [
                'event_type' => $eventType,
                'listing_id' => $listing->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function produtoOptions()
    {
        return Produto::with(['imagens'])
            ->withCount('listings')
            ->latest()
            ->get()
            ->map(function (Produto $produto) {
                return [
                    'id' => $produto->id,
                    'nome' => $produto->nome,
                    'estoque' => $produto->estoque,
                    'listings_count' => $produto->listings_count,
                    'imageUrl' => $produto->imagens->first()
                        ? asset('storage/' . $produto->imagens->first()->imagem_path)
                        : null,
                ];
            });
    }
}
