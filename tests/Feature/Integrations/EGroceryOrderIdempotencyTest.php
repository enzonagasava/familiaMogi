<?php

use App\Models\Integrations\EGroceryOrderImport;
use App\Models\Produto;
use App\Models\Tamanho;

beforeEach(function () {
    config(['services.api.auth_token' => 'test-token']);

    $produto = Produto::query()->create([
        'user_id' => 1,
        'nome' => 'Arroz Tipo 1 5kg',
        'descricao' => 'Descricao longa do produto',
        'estoque' => 10,
    ]);

    $tamanho = Tamanho::query()->create(['nome' => '5kg']);
    $produto->tamanhos()->attach($tamanho->id, ['preco' => 29.90]);

    $this->sku = (string) $produto->id;
});

it('is idempotent by external_order_id on POST /api/v1/pedidos', function () {
    $payload = [
        'external_order_id' => 'fm-20260427-00091',
        'created_at' => '2026-04-27T12:30:10Z',
        'customer' => [
            'name' => 'Maria Silva',
            'phone' => '+55-11-99999-9999',
            'email' => 'maria@example.com',
        ],
        'items' => [[
            'sku' => $this->sku,
            'qty' => 2,
            'unit_price' => 29.90,
        ]],
        'totals' => [
            'subtotal' => 59.80,
            'delivery_fee' => 8.00,
            'discount' => 0,
            'grand_total' => 67.80,
        ],
        'payment' => [
            'method' => 'pix',
            'status' => 'paid',
        ],
        'delivery' => [
            'type' => 'delivery',
            'address' => [
                'zip' => '08770-000',
                'street' => 'Rua Exemplo',
                'number' => '123',
                'city' => 'Mogi das Cruzes',
                'state' => 'SP',
            ],
        ],
    ];

    $first = $this->withToken('test-token')->postJson('/api/v1/pedidos', $payload);

    $first->assertCreated()
        ->assertJsonStructure(['order_id', 'status', 'received_at'])
        ->assertJsonPath('status', 'received');

    $second = $this->withToken('test-token')->postJson('/api/v1/pedidos', $payload);

    $second->assertOk()
        ->assertJsonStructure(['order_id', 'status', 'received_at'])
        ->assertJsonPath('order_id', $first->json('order_id'));

    expect(EGroceryOrderImport::query()->where('external_order_id', 'fm-20260427-00091')->count())->toBe(1);
});
