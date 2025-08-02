<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class CartController extends Controller
{
    /**
     * Adiciona um produto ao carrinho na sessão.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        // 1. Validar os dados recebidos do frontend
        $validatedData = $request->validate([
            // Supondo que você tenha uma tabela 'products' com os IDs
            // Se os produtos são estáticos como no exemplo, a validação de 'exists' pode ser removida
            'product_id' => ['required', 'integer'], // 'exists:products,id'
            'portion' => ['required', 'string', 'max:255'],
        ]);

        $productId = $validatedData['product_id'];
        $portion = $validatedData['portion'];

        // Criar um ID único para o item no carrinho (produto + porção)
        // Ex: "product_1_200g"
        $cartItemId = 'product_' . $productId . '_' . str_replace(' ', '', $portion);

        // 2. Pegar o carrinho da sessão ou iniciar um array vazio
        $cart = session()->get('cart', []);

        // 3. Verificar se o item já existe no carrinho para incrementar a quantidade
        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity']++;
        } else {
            // Se não existir, adiciona o novo item
            // Em uma aplicação real, você buscaria o nome e preço do produto no banco de dados.
            // Por agora, vamos usar dados de exemplo.
            $cart[$cartItemId] = [
                'product_id' => $productId,
                'name' => 'Cogumelo ' . $productId, // Exemplo
                'portion' => $portion,
                'price' => 25.50, // Preço de exemplo
                'quantity' => 1,
            ];
        }

        // 4. Salvar o carrinho atualizado de volta na sessão
        session()->put('cart', $cart);
    }
}