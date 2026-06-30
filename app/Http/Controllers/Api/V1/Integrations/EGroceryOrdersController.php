<?php

namespace App\Http\Controllers\Api\V1\Integrations;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\GerenciarPedido;
use App\Models\Integrations\EGroceryOrderImport;
use App\Models\Pedido;
use App\Models\Plataforma;
use App\Models\Produto;
use App\Services\Integrations\EGroceryContractSerializer;
use App\Services\Integrations\FamiliaMogiWebhookPublisher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class EGroceryOrdersController extends Controller
{
    public function __construct(
        private readonly FamiliaMogiWebhookPublisher $webhookPublisher,
        private readonly EGroceryContractSerializer $serializer,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        if (!is_array($payload) || $payload === []) {
            return $this->error('Payload JSON invalido.', 'invalid_json_payload', 422);
        }

        $validator = Validator::make($payload, [
            'external_order_id' => ['nullable', 'string', 'max:255'],
            'created_at' => ['nullable', 'date'],
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.phone' => ['nullable', 'string', 'max:60'],
            'customer.email' => ['nullable', 'email', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['required', 'string', 'max:120'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'totals' => ['required', 'array'],
            'totals.grand_total' => ['required', 'numeric', 'min:0'],
            'payment' => ['nullable', 'array'],
            'payment.status' => ['nullable', 'string', 'max:50'],
            'delivery' => ['nullable', 'array'],
            'delivery.address' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Payload invalido para criacao de pedido.',
                'code' => 'invalid_order_payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        $externalOrderId = trim((string) ($payload['external_order_id'] ?? ''));
        if ($externalOrderId === '') {
            $externalOrderId = sprintf('fm-%s-%s', now()->format('YmdHis'), Str::lower(Str::random(6)));
        }

        $existing = EGroceryOrderImport::query()->where('external_order_id', $externalOrderId)->first();
        if ($existing) {
            $responsePayload = $existing->response_payload;
            if (!is_array($responsePayload) || $responsePayload === []) {
                $responsePayload = [
                    'order_id' => $existing->panel_order_id ? 'eg-'.$existing->panel_order_id : null,
                    'status' => $existing->status,
                    'received_at' => optional($existing->processed_at ?? $existing->created_at)->toIso8601String(),
                ];
            }

            return response()->json($responsePayload, 200);
        }

        try {
            $created = DB::connection('tenant_content')->transaction(function () use ($payload, $externalOrderId) {
                $normalizedPayload = $this->normalizePayload($payload, $externalOrderId);
                $customer = $this->resolveCustomer($normalizedPayload, $externalOrderId);
                $platform = Plataforma::query()->firstOrCreate(['nome' => 'familiaMogi']);

                $orderCode = $this->generatePanelOrderCode();
                $grandTotal = (float) ($normalizedPayload['totals']['grand_total'] ?? 0);
                $deliveryAddress = is_array($normalizedPayload['delivery']['address'] ?? null)
                    ? $normalizedPayload['delivery']['address']
                    : [];
                $fullAddress = trim(implode(', ', array_filter([
                    $deliveryAddress['street'] ?? null,
                    $deliveryAddress['number'] ?? null,
                    $deliveryAddress['neighborhood'] ?? null,
                    $deliveryAddress['city'] ?? null,
                    $deliveryAddress['state'] ?? null,
                    $deliveryAddress['zip'] ?? null,
                ])));

                $gerenciarPedido = GerenciarPedido::query()->create([
                    'cliente_id' => $customer->id,
                    'cod_pedido' => $orderCode,
                    'valor' => (int) round($grandTotal),
                    'endereco' => $fullAddress,
                    'status' => $this->mapOrderStatus((string) ($normalizedPayload['payment']['status'] ?? 'pending')),
                    'plataforma_id' => $platform->id,
                ]);

                $stockChangedProducts = [];

                foreach ((array) $normalizedPayload['items'] as $item) {
                    $sku = (string) ($item['sku'] ?? '');
                    if (!ctype_digit($sku)) {
                        throw new \RuntimeException('SKU invalido para este painel: '.$sku);
                    }

                    $product = Produto::query()->find((int) $sku);
                    if (!$product) {
                        throw new \RuntimeException('Produto nao encontrado para SKU: '.$sku);
                    }

                    $qty = (int) ($item['qty'] ?? 0);
                    if ($qty <= 0) {
                        throw new \RuntimeException('Quantidade invalida para SKU: '.$sku);
                    }

                    if ((int) $product->estoque < $qty) {
                        throw new \RuntimeException('Estoque insuficiente para SKU: '.$sku);
                    }

                    $product->estoque = (int) $product->estoque - $qty;
                    $product->save();

                    Pedido::query()->create([
                        'produto_id' => $product->id,
                        'quantidade' => $qty,
                        'cod_pedido' => $orderCode,
                        'valor_pedido' => (float) ($item['unit_price'] ?? 0),
                    ]);

                    $stockChangedProducts[] = $product->fresh(['imagens', 'tamanhos']);
                }

                $responsePayload = [
                    'order_id' => 'eg-'.$gerenciarPedido->id,
                    'status' => 'received',
                    'received_at' => now()->toIso8601String(),
                ];

                $orderImport = EGroceryOrderImport::query()->create([
                    'external_order_id' => $externalOrderId,
                    'source' => 'familiaMogi-api',
                    'status' => 'received',
                    'gerenciar_pedido_id' => $gerenciarPedido->id,
                    'panel_order_id' => $gerenciarPedido->cod_pedido,
                    'request_payload' => $payload,
                    'normalized_payload' => $normalizedPayload,
                    'response_payload' => $responsePayload,
                    'processed_at' => now(),
                ]);

                return [
                    'response_payload' => $responsePayload,
                    'gerenciar_pedido' => $gerenciarPedido->fresh(['cliente', 'CodPedidos.produto']),
                    'stock_changed_products' => $stockChangedProducts,
                    'order_import' => $orderImport,
                ];
            });
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'order_processing_error', 422);
        }

        $order = $created['gerenciar_pedido'];
        $orderData = $this->serializer->orderPayload($order);

        $this->safePublish('order.created', [
            'type' => 'order',
            'id' => (string) $order->cod_pedido,
            'version' => (int) $order->updated_at?->timestamp,
        ], $orderData);

        if (in_array((string) $order->status, ['approved', 'pago', 'paid'], true)) {
            $this->safePublish('order.paid', [
                'type' => 'order',
                'id' => (string) $order->cod_pedido,
                'version' => (int) $order->updated_at?->timestamp,
            ], $orderData);
        }

        foreach ($created['stock_changed_products'] as $product) {
            if (!$product instanceof Produto) {
                continue;
            }

            $this->safePublish('stock.updated', [
                'type' => 'product',
                'id' => (string) $product->id,
                'version' => (int) $product->updated_at?->timestamp,
            ], $this->serializer->productPayload($product));
        }

        return response()->json($created['response_payload'], 201);
    }

    private function normalizePayload(array $payload, string $externalOrderId): array
    {
        $customer = is_array($payload['customer'] ?? null) ? $payload['customer'] : [];
        $totals = is_array($payload['totals'] ?? null) ? $payload['totals'] : [];
        $payment = is_array($payload['payment'] ?? null) ? $payload['payment'] : [];
        $delivery = is_array($payload['delivery'] ?? null) ? $payload['delivery'] : [];
        $deliveryAddress = is_array($delivery['address'] ?? null) ? $delivery['address'] : [];

        return [
            'external_order_id' => $externalOrderId,
            'created_at' => is_string($payload['created_at'] ?? null) ? $payload['created_at'] : now()->toIso8601String(),
            'customer' => [
                'name' => (string) ($customer['name'] ?? ''),
                'phone' => (string) ($customer['phone'] ?? ''),
                'email' => (string) ($customer['email'] ?? ''),
            ],
            'items' => array_values((array) ($payload['items'] ?? [])),
            'totals' => [
                'subtotal' => (float) ($totals['subtotal'] ?? 0),
                'delivery_fee' => (float) ($totals['delivery_fee'] ?? 0),
                'discount' => (float) ($totals['discount'] ?? 0),
                'grand_total' => (float) ($totals['grand_total'] ?? 0),
            ],
            'payment' => [
                'method' => (string) ($payment['method'] ?? ''),
                'status' => (string) ($payment['status'] ?? 'pending'),
            ],
            'delivery' => [
                'type' => (string) ($delivery['type'] ?? 'delivery'),
                'address' => [
                    'zip' => $deliveryAddress['zip'] ?? null,
                    'street' => $deliveryAddress['street'] ?? null,
                    'number' => $deliveryAddress['number'] ?? null,
                    'neighborhood' => $deliveryAddress['neighborhood'] ?? null,
                    'city' => $deliveryAddress['city'] ?? null,
                    'state' => $deliveryAddress['state'] ?? null,
                ],
            ],
        ];
    }

    private function resolveCustomer(array $normalizedPayload, string $externalOrderId): Cliente
    {
        $name = trim((string) ($normalizedPayload['customer']['name'] ?? 'Cliente familiaMogi'));
        $email = trim((string) ($normalizedPayload['customer']['email'] ?? ''));
        $phone = trim((string) ($normalizedPayload['customer']['phone'] ?? ''));

        if ($email === '') {
            $email = strtolower(str_replace([' ', '/'], ['.', '-'], $externalOrderId)).'@familia-mogi.local';
        }

        if ($phone === '') {
            $phone = 'fm-'.$externalOrderId;
        }

        $address = is_array($normalizedPayload['delivery']['address'] ?? null)
            ? $normalizedPayload['delivery']['address']
            : [];

        return Cliente::query()->firstOrCreate(
            ['email' => $email],
            [
                'nome' => $name !== '' ? $name : 'Cliente familiaMogi',
                'numero' => $phone,
                'endereco' => $address['street'] ?? null,
                'cep' => $address['zip'] ?? null,
                'numero_endereco' => $address['number'] ?? null,
                'municipio' => $address['city'] ?? null,
                'estado' => $address['state'] ?? null,
            ]
        );
    }

    private function mapOrderStatus(string $paymentStatus): string
    {
        $normalized = strtolower(trim($paymentStatus));

        return match ($normalized) {
            'paid', 'approved', 'pago' => 'approved',
            'cancelled', 'canceled', 'cancelado' => 'cancelado',
            default => 'em-andamento',
        };
    }

    private function generatePanelOrderCode(): string
    {
        do {
            $code = 'EG-'.strtoupper(Str::random(10));
        } while (GerenciarPedido::query()->where('cod_pedido', $code)->exists());

        return $code;
    }

    private function safePublish(string $eventType, array $entity, array $data): void
    {
        try {
            $this->webhookPublisher->publish($eventType, $entity, $data);
        } catch (\Throwable) {
            // Never break main flow because of async webhook dispatching.
        }
    }

    private function error(string $message, string $code, int $status): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'code' => $code,
        ], $status);
    }
}
