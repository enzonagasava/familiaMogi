<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use Inertia\Inertia;

use function Pest\Laravel\call;

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
        $preco = $request->input('preco');
        $quantidade = $request->input('quantidade');
        $produto = Produto::findOrFail($produtoId);
        $imagens = $produto->imagens->first()->imagem_path;

        // 2. Acessa o carrinho na sessão
        $cart = session()->get('cart', []);

        // 3. Cria um ID único para o item no carrinho
        $normalizedPorcao = strtolower(preg_replace('/\s+/', '', $porcao));
        $baseCartItemId = $produtoId . '_' . $normalizedPorcao;

        // Gera um hash único baseado no ID base + microtime para garantir que seja diferente
        $uniqueString = $baseCartItemId . '_' . microtime(true);
        $cartItemId = $produtoId . '_' . substr(md5($uniqueString), 0, 8);

        // Caso queira garantir que não exista no carrinho (muito improvável)
        while (isset($cart[$cartItemId])) {
            $uniqueString = $baseCartItemId . '_' . microtime(true) . rand();
            $cartItemId = $produtoId . '_' . substr(md5($uniqueString), 0, 8);
        }

        // Adiciona o novo item sempre com quantidade 1
        $cart[$cartItemId] = [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'porcao' => $porcao,
            'preco' => $preco,
            'quantidade' => $quantidade,
            'thumbnail' => $imagens ? asset('storage/' . $imagens) : null,
        ];

        // 6. Salva o carrinho atualizado de volta na sessão
        session()->put('cart', $cart);
        \Log::info('Carrinho atualizado: ' . json_encode($cart));
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


    public function remover(Request $request, string $cartItemId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cartItemId])) {
            unset($cart[$cartItemId]);
            session()->put('cart', $cart);

            // Redireciona para a rota do carrinho com dados atualizados
 return response()->json([
        'success' => true,
        'cart' => $cart,
        'message' => 'Item removido do carrinho.',
    ]);        }
    }
}