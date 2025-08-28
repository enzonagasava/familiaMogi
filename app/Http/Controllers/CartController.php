<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;


class CartController extends Controller
{
    /**
     * Adiciona um produto ao carrinho na sessão.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return inertia('Cart', ['cart' => $cart]);
    }

  public function adicionar(Request $request)
    {
        // 1. Validar e obter os dados do produto e da porção
        $produtoId = $request->input('id');
        $porcao = $request->input('porcao');
        $produto = Produto::findOrFail($produtoId);
        // 2. Acessa o carrinho na sessão
        $cart = session()->get('cart', []);

        // 3. Cria um ID único para o item no carrinho
        $cartItemId = $produtoId . '_' . $porcao;

        // 4. Se o item já existe, incrementa a quantidade
        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantidade']++;
        } else {
            // 5. Se não existe, adiciona o novo item com todas as informações
            $cart[$cartItemId] = [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'porcao' => $porcao,
                'preco' => $produto->tamanhos->firstWhere('nome', $porcao)['preco'] ?? 0
            ];
        }

        // 6. Salva o carrinho atualizado de volta na sessão
        session()->put('cart', $cart);

        // 7. Redireciona para a rota GET, que irá exibir o carrinho atualizado
        return redirect()->route('carrinho.index');
    }

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