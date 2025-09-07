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
            'estoque' => $produto->estoque,
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

public function addToCart(Request $request)
{
    $produtoId = $request->input('id');
    $porcao = $request->input('porcao', 'default'); // caso porção não seja enviada, usa 'default'
    $preco = $request->input('preco');
    $quantidade = (int) $request->input('quantidade', 1);

    $produto = Produto::with('tamanhos')->findOrFail($produtoId);

    $preco = null;

    foreach ($produto->tamanhos as $tamanho) {
        if ($tamanho->nome == $porcao) {
            $preco = $tamanho->pivot->preco;
            break;
        }
    }

    if ($preco === null) {
        $preco = 0;
    }

    $imagens = $produto->imagens->first()->imagem_path ?? null;

    $cart = $request->session()->get('cart', []);

    // Normaliza a porção para criar o ID único do item no carrinho
    $normalizedPorcao = strtolower(preg_replace('/\s+/', '', $porcao));
    $baseCartItemId = $produtoId . '_' . $normalizedPorcao;

    // Gera um hash único para o item no carrinho
    $uniqueString = $baseCartItemId . '_' . microtime(true);
    $cartItemId = $produtoId . '_' . substr(md5($uniqueString), 0, 8);

    // Se o item com essa chave existir, atualiza quantidade, senão cria novo item
    // Para evitar duplicatas, vamos buscar se algum item no carrinho tem o mesmo produto e porção
    $existingCartItemId = null;
    foreach ($cart as $key => $item) {
        if (isset($item['id'], $item['porcao'])
            && $item['id'] == $produtoId
            && strtolower(preg_replace('/\s+/', '', $item['porcao'])) == $normalizedPorcao) {
            $existingCartItemId = $key;
            break;
        }
    }

    if ($existingCartItemId) {
        // Atualiza a quantidade do item existente
        $cart[$existingCartItemId]['quantidade'] += $quantidade;
    } else {
        // Adiciona novo item no carrinho
        $cart[$cartItemId] = [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'estoque' => $produto->estoque,
            'porcao' => $porcao,
            'preco' => $preco,
            'quantidade' => $quantidade,
            'thumbnail' => $imagens ? asset('storage/' . $imagens) : null,
        ];
    }

    $request->session()->put('cart', $cart);

    return response()->json([
        'success' => true,
        'cart' => $cart,
    ]);
}
}