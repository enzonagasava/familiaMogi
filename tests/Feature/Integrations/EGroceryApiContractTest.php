<?php

use App\Models\Listing;
use App\Models\Produto;
use App\Models\ProdutoImagem;
use App\Models\Tamanho;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config(['services.api.auth_token' => 'test-token']);

    Storage::fake('public');

    $produto = Produto::query()->create([
        'user_id' => 1,
        'nome' => 'Arroz Tipo 1 5kg',
        'descricao' => 'Descricao longa do produto',
        'estoque' => 85,
    ]);

    $tamanho = Tamanho::query()->create(['nome' => '5kg']);
    $produto->tamanhos()->attach($tamanho->id, ['preco' => 29.90]);

    Storage::disk('public')->put('produtos/teste.jpg', 'fake-image-content');

    $imagem = ProdutoImagem::query()->create([
        'produto_id' => $produto->id,
        'user_id' => 1,
        'imagem_path' => 'produtos/teste.jpg',
        'ordem' => 1,
    ]);

    Listing::query()->create([
        'produto_id' => $produto->id,
        'anuncio_ativo' => true,
        'anuncio_status' => 'active',
        'anuncio_tipos' => ['Site_anuncio'],
    ]);

    $this->produto = $produto;
    $this->imagem = $imagem;
});

it('returns catalog endpoints with expected contract shape', function () {
    $anuncios = $this->withToken('test-token')->getJson('/api/v1/anuncios');
    $anuncios->assertOk()
        ->assertJsonStructure([
            'data' => [[
                'id',
                'title',
                'description',
                'status',
                'priority',
                'starts_at',
                'ends_at',
                'updated_at',
            ]],
            'meta' => ['next_cursor'],
        ]);

    $produtos = $this->withToken('test-token')->getJson('/api/v1/produtos');
    $produtos->assertOk()
        ->assertJsonStructure([
            'data' => [[
                'sku',
                'name',
                'category',
                'price',
                'stock',
                'status',
                'image_id',
                'updated_at',
            ]],
            'meta' => ['next_cursor'],
        ]);

    $produtoDetalhe = $this->withToken('test-token')->getJson('/api/v1/produtos/'.$this->produto->id);
    $produtoDetalhe->assertOk()
        ->assertJsonStructure([
            'sku',
            'name',
            'category',
            'price',
            'stock',
            'status',
            'image_id',
            'updated_at',
            'description_long',
            'weights' => ['value', 'unit'],
            'variations',
            'images',
        ]);

    $imageId = 'img_'.$this->imagem->id;
    $imagemDetalhe = $this->withToken('test-token')->getJson('/api/v1/imagens/'.$imageId);
    $imagemDetalhe->assertOk()
        ->assertJsonStructure([
            'id',
            'storage_key',
            'url',
            'mime_type',
            'width',
            'height',
            'checksum',
        ])
        ->assertJsonPath('id', $imageId);
});

it('requires bearer token on v1 integration endpoints', function () {
    $this->getJson('/api/v1/produtos')
        ->assertUnauthorized()
        ->assertJson([
            'message' => 'Unauthorized',
        ]);
});
