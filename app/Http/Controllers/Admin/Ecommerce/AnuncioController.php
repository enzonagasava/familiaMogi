<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ecommerce\StoreAnuncioRequest;
use App\Http\Requests\Admin\Ecommerce\UpdateAnuncioRequest;
use App\Models\Listing;
use App\Models\Produto;
use Inertia\Inertia;

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

    public function store(StoreAnuncioRequest $request)
    {
        $validated = $request->validated();

        Listing::create([
            'produto_id' => $validated['produto_id'],
            'anuncio_ativo' => $validated['anuncio_ativo'] ?? true,
            'anuncio_status' => $validated['anuncio_status'] ?? null,
            'anuncio_tipos' => $validated['anuncio_tipos'],
        ]);

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

    public function update(UpdateAnuncioRequest $request, Listing $listing)
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

        return redirect()->route('admin.anuncio.config')->with('success', 'Anúncio atualizado com sucesso.');
    }

    public function destroy(Listing $listing)
    {
        if (!$listing->produto_id) {
            abort(404);
        }

        $listing->delete();

        return redirect()->route('admin.anuncio.config')->with('success', 'Anúncio removido com sucesso.');
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
