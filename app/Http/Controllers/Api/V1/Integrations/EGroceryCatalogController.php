<?php

namespace App\Http\Controllers\Api\V1\Integrations;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Produto;
use App\Models\ProdutoImagem;
use App\Services\Integrations\EGroceryContractSerializer;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EGroceryCatalogController extends Controller
{
    public function __construct(private readonly EGroceryContractSerializer $serializer)
    {
    }

    public function anuncios(Request $request): JsonResponse
    {
        $limit = $this->resolveLimit($request);
        $updatedSince = $this->parseIsoDateTime($request->query('updated_since'));
        $statusFilter = strtolower((string) $request->query('status', ''));

        $query = Listing::query()
            ->with(['produto.imagens', 'produto.tamanhos'])
            ->whereNotNull('produto_id')
            ->orderBy('updated_at')
            ->orderBy('id');

        if ($updatedSince) {
            $query->where('updated_at', '>=', $updatedSince);
        }

        if ($statusFilter === 'active') {
            $query->where('anuncio_ativo', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('anuncio_ativo', false);
        }

        $this->applyCursor($query, (string) $request->query('cursor', ''));

        $rows = $query->limit($limit + 1)->get();
        $hasMore = $rows->count() > $limit;
        $slice = $rows->take($limit);

        $data = $slice->map(fn (Listing $listing) => $this->serializer->listingPayload($listing))->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'next_cursor' => $hasMore ? $this->buildCursorFromModel($slice->last()) : null,
            ],
        ]);
    }

    public function produtos(Request $request): JsonResponse
    {
        $limit = $this->resolveLimit($request);
        $updatedSince = $this->parseIsoDateTime($request->query('updated_since'));

        $query = Produto::query()
            ->with(['imagens', 'tamanhos'])
            ->orderBy('updated_at')
            ->orderBy('id');

        if ($updatedSince) {
            $query->where('updated_at', '>=', $updatedSince);
        }

        $active = $request->query('active');
        if ($active !== null && $active !== '') {
            $normalized = filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($normalized === true) {
                $query->where('estoque', '>', 0);
            } elseif ($normalized === false) {
                $query->where('estoque', '<=', 0);
            }
        }

        $this->applyCursor($query, (string) $request->query('cursor', ''));

        $rows = $query->limit($limit + 1)->get();
        $hasMore = $rows->count() > $limit;
        $slice = $rows->take($limit);

        $data = $slice->map(fn (Produto $product) => $this->serializer->productPayload($product))->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'next_cursor' => $hasMore ? $this->buildCursorFromModel($slice->last()) : null,
            ],
        ]);
    }

    public function produtoBySku(string $sku): JsonResponse
    {
        if (!ctype_digit($sku)) {
            return $this->error('Produto nao encontrado.', 'product_not_found', 404);
        }

        $product = Produto::query()->with(['imagens', 'tamanhos'])->find((int) $sku);

        if (!$product) {
            return $this->error('Produto nao encontrado.', 'product_not_found', 404);
        }

        return response()->json($this->serializer->productDetailPayload($product));
    }

    public function imagemById(string $imageId): JsonResponse
    {
        $internalImageId = $this->serializer->parseImageId($imageId);

        if (!$internalImageId) {
            return $this->error('Imagem nao encontrada.', 'image_not_found', 404);
        }

        $image = ProdutoImagem::query()->find($internalImageId);

        if (!$image) {
            return $this->error('Imagem nao encontrada.', 'image_not_found', 404);
        }

        return response()->json($this->serializer->imagePayload($image));
    }

    private function resolveLimit(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 100);

        return max(1, min($perPage, 100));
    }

    private function parseIsoDateTime(mixed $value): ?CarbonImmutable
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function applyCursor(Builder $query, string $cursor): void
    {
        if ($cursor === '') {
            return;
        }

        $decoded = json_decode((string) base64_decode($cursor, true), true);
        if (!is_array($decoded)) {
            return;
        }

        $updatedAt = $decoded['updated_at'] ?? null;
        $id = $decoded['id'] ?? null;

        if (!is_string($updatedAt) || !is_numeric($id)) {
            return;
        }

        $query->where(function (Builder $builder) use ($updatedAt, $id) {
            $builder->where('updated_at', '>', $updatedAt)
                ->orWhere(function (Builder $nested) use ($updatedAt, $id) {
                    $nested->where('updated_at', '=', $updatedAt)
                        ->where('id', '>', (int) $id);
                });
        });
    }

    private function buildCursorFromModel(mixed $model): ?string
    {
        if (!$model || !isset($model->updated_at, $model->id)) {
            return null;
        }

        return base64_encode(json_encode([
            'updated_at' => optional($model->updated_at)->toIso8601String(),
            'id' => (int) $model->id,
        ]));
    }

    private function error(string $message, string $code, int $status): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'code' => $code,
        ], $status);
    }
}
