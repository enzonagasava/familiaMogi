<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;
use App\Models\GerenciarPedido;
use App\Models\IntegracaoPagamento;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;




class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        try {

            $keys = IntegracaoPagamento::first();

            if (!$keys) {
                return response()->json(['error' => 'Configuração Mercado Pago não encontrada'], 500);
            }

            MercadoPagoConfig::setAccessToken($keys->access_key);

            $client = new PreferenceClient();

            $preference = $client->create([
                "items" => [
                    [
                        "id" => uniqid(),
                        "title" => $request->items[0]['title'],
                        "quantity" => 1,
                        "unit_price" => (float) $request->price,
                        "currency_id" => "BRL",
                    ]
                ],
                "payer" => [
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => [
                        "number"=> $request->phone
                      ],
                    "address"=> [
                        "street_name"=> $request->street_name,
                      ],
                ],
                "metadata" => [
                    "customer_name" => $request->name,
                    "customer_email" => $request->email,
                    "price" => $request->price,
                    "device_id" => $request->device_id,
                ],
                "payment_methods" => [ "installments" => 12, ],
                "back_urls" => [
                    "success" => "https://developfamiliamogi.eyiservicos.com.br/checkout/sucesso",
                    "failure" => "https://developfamiliamogi.eyiservicos.com.br/checkout/falha",
                    "pending" => "https://developfamiliamogi.eyiservicos.com.br/checkout/pendentes",
                ],
                "auto_return" => "approved",
            ]);

            return response()->json([
                "preference_id" => $preference->id,
                "init_point" => $preference->init_point,
                "sandbox_init_point" => $preference->sandbox_init_point,
            ]);

        } catch (MPApiException $e) {
            return response()->json([
                "message" => "Erro Mercado Pago",
                "error_raw" => $e->getMessage(),
            ], 500);
        }
    }


   public function webhook(Request $request)
    {
        try {
            Log::info("Webhook recebido", $request->all());

            $topic = $request->get('type') ?? $request->get('topic');

            if ($topic !== 'payment') {
                return response()->json(['status' => 'ignored'], 200);
            }

            $data = $request->get('data');
            if (is_string($data)) {
                $data = json_decode($data, true);
            }

            if (!isset($data['id'])) {
                Log::error("Webhook sem payment ID", $request->all());
                return response()->json(['error' => 'invalid data'], 400);
            }

            $paymentId = $data['id'];

            // Buscar pagamento
            $client = new PaymentClient();
            $payment = $client->get($paymentId);

            if (!$payment) {
                return response()->json(['error' => 'payment not found'], 404);
            }

            if ($payment->status !== "approved") {
                return response()->json(['status' => 'Pagamento não aprovado'], 200);
            }

            $meta = (array) $payment->metadata;

            // =================================
            // 1️⃣ Verificar se já processamos esse pagamento
            // =================================
            if (GerenciarPedido::where('payment_id', $paymentId)->exists()) {
                Log::warning("Webhook duplicado detectado", ['payment_id' => $paymentId]);
                return response()->json(['status' => 'already processed'], 200);
            }

            // =================================
            // 2️⃣ Criar cliente
            // =================================
            $cliente = Cliente::firstOrCreate(
                ['email' => $meta['customer_email']],
                [
                    'nome' => $meta['customer_name'],
                    'numero' => $meta['customer_number'] ?? null,
                ]
            );

            // =================================
            // 3️⃣ Criar GERENCIAR PEDIDO
            // =================================
            $codigoPedido = uniqid('PED-');

            $gerenciar = GerenciarPedido::create([
                'cliente_id' => $cliente->id,
                'cod_pedido' => $codigoPedido,
                'valor' => $meta['price'],
                'endereco' => $meta['customer_address'] ?? null,
                'status' => 'approved',
                'payment_id' => $paymentId,
                'plataforma_id'=> 1,
            ]);

            // =================================
            // 4️⃣ Criar Pedido(s) + Subtrair Estoque
            // =================================

            if (!empty($payment->additional_info->items)) {

                foreach ($payment->additional_info->items as $item) {

                    $produtoId = $item->id ?? null;

                    if ($produtoId) {
                        $produto = Produto::find($produtoId);

                        if ($produto) {

                            // Verificar estoque
                            if ($produto->estoque < $item->quantity) {
                                Log::error("Sem estoque suficiente", [
                                    'produto' => $produto->nome,
                                    'estoque' => $produto->estoque,
                                    'required' => $item->quantity
                                ]);

                                continue; // opcional: pode abortar o pedido
                            }

                            // Subtrai estoque
                            $produto->estoque -= $item->quantity;
                            $produto->save();
                        }
                    }

                    Pedido::create([
                        'produto_id' => $produtoId,
                        'quantidade' => $item->quantity,
                        'valor_pedido' => $item->unit_price,
                        'cod_pedido' => $codigoPedido,
                    ]);
                }
            }

            Log::info("Pedido criado via webhook", [
                'cod_pedido' => $codigoPedido,
                'cliente' => $cliente->email,
            ]);

            return response()->json(['status' => 'Pedido criado'], 200);

        } catch (\Exception $e) {

            Log::error("Erro no webhook", [
                "msg" => $e->getMessage(),
                "trace" => $e->getTraceAsString()
            ]);

            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function getKeys()
    {
    return IntegracaoPagamento::first();
    }
}
