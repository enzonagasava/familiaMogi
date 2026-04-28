<?php

namespace App\Services\Integrations;

use App\Models\GerenciarPedido;
use App\Models\Listing;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\ProdutoImagem;
use Illuminate\Support\Facades\Storage;

class EGroceryContractSerializer
{
    public function listingPayload(Listing $listing): array
    {
        $listing->loadMissing('produto.imagens', 'produto.tamanhos');
        $product = $listing->produto;

        return [
            'id' => 'ad_'.$listing->id,
            'title' => $product?->nome ?? 'Anuncio #'.$listing->id,
            'description' => $product?->descricao,
            'status' => $this->listingStatus($listing),
            'priority' => 10,
            'starts_at' => optional($listing->created_at)?->toIso8601String(),
            'ends_at' => null,
            'updated_at' => optional($listing->updated_at)?->toIso8601String(),
        ];
    }

    public function productPayload(Produto $product): array
    {
        $product->loadMissing('imagens', 'tamanhos');

        return [
            'sku' => (string) $product->id,
            'name' => $product->nome,
            'category' => 'Geral',
            'price' => $this->resolveProductPrice($product),
            'stock' => (int) $product->estoque,
            'status' => $this->productStatus($product),
            'image_id' => $product->imagens->first() ? $this->imageExternalId($product->imagens->first()) : null,
            'updated_at' => optional($product->updated_at)?->toIso8601String(),
        ];
    }

    public function productDetailPayload(Produto $product): array
    {
        $base = $this->productPayload($product);

        $variations = $product->tamanhos->map(function ($size) {
            return [
                'name' => $size->nome,
                'price' => is_numeric($size->pivot->preco ?? null) ? (float) $size->pivot->preco : null,
            ];
        })->values()->all();

        $images = $product->imagens->map(fn (ProdutoImagem $image) => $this->imagePayload($image))->values()->all();

        return array_merge($base, [
            'description_long' => $product->descricao,
            'weights' => [
                'value' => null,
                'unit' => null,
            ],
            'variations' => $variations,
            'images' => $images,
        ]);
    }

    public function imagePayload(ProdutoImagem $image): array
    {
        $path = (string) $image->imagem_path;
        $disk = Storage::disk('public');

        $mimeType = null;
        $width = null;
        $height = null;
        $checksum = null;

        try {
            if ($path !== '' && $disk->exists($path)) {
                $mimeType = $disk->mimeType($path) ?: null;

                $absolutePath = $disk->path($path);
                $size = @getimagesize($absolutePath);
                if (is_array($size)) {
                    $width = isset($size[0]) ? (int) $size[0] : null;
                    $height = isset($size[1]) ? (int) $size[1] : null;
                }

                $hash = @hash_file('sha256', $absolutePath);
                if (is_string($hash) && $hash !== '') {
                    $checksum = 'sha256:'.$hash;
                }
            }
        } catch (\Throwable) {
            // Keep response resilient even when local file metadata is unavailable.
        }

        return [
            'id' => $this->imageExternalId($image),
            'storage_key' => $path !== '' ? $path : null,
            'url' => $path !== '' ? asset('storage/'.$path) : null,
            'mime_type' => $mimeType,
            'width' => $width,
            'height' => $height,
            'checksum' => $checksum,
            'updated_at' => optional($image->updated_at)?->toIso8601String(),
        ];
    }

    public function orderPayload(GerenciarPedido $order): array
    {
        $order->loadMissing('cliente', 'CodPedidos.produto');

        $items = $order->CodPedidos->map(function (Pedido $item) {
            return [
                'sku' => (string) $item->produto_id,
                'name' => $item->produto?->nome,
                'qty' => (int) $item->quantidade,
                'unit_price' => (float) $item->valor_pedido,
            ];
        })->values()->all();

        return [
            'order_id' => 'eg-'.$order->id,
            'panel_order_code' => $order->cod_pedido,
            'status' => $order->status,
            'total' => (float) $order->valor,
            'customer' => [
                'name' => $order->cliente?->nome,
                'email' => $order->cliente?->email,
                'phone' => $order->cliente?->numero,
            ],
            'items' => $items,
            'updated_at' => optional($order->updated_at)?->toIso8601String(),
        ];
    }

    public function listingStatus(Listing $listing): string
    {
        if ($listing->anuncio_status === 'deleted') {
            return 'deleted';
        }

        if ((bool) $listing->anuncio_ativo) {
            return 'active';
        }

        return 'inactive';
    }

    public function productStatus(Produto $product): string
    {
        return ((int) $product->estoque) > 0 ? 'active' : 'inactive';
    }

    public function resolveProductPrice(Produto $product): float
    {
        $prices = $product->tamanhos
            ->map(fn ($size) => is_numeric($size->pivot->preco ?? null) ? (float) $size->pivot->preco : null)
            ->filter(fn ($value) => $value !== null)
            ->values();

        if ($prices->isEmpty()) {
            return 0.0;
        }

        return (float) $prices->min();
    }

    public function imageExternalId(ProdutoImagem $image): string
    {
        return 'img_'.$image->id;
    }

    public function parseImageId(string $imageId): ?int
    {
        if (str_starts_with($imageId, 'img_')) {
            $raw = substr($imageId, 4);
            return ctype_digit($raw) ? (int) $raw : null;
        }

        return ctype_digit($imageId) ? (int) $imageId : null;
    }
}
