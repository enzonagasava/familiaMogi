<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoImagem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tamanho;

class ProdutoController extends Controller
{
    public function index()
    {
        $products = Produto::with('imagens')->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'nome' => $product->nome,
                'descricao' => $product->descricao,
                'estoque' => $product->estoque,
                'tamanhos' => $product->tamanhos,
                'imageUrl' => $product->imagens->first()
                    ? asset('storage/' . $product->imagens->first()->imagem_path)
                    : null,
                'created_at' => $product->created_at->format('d/m/Y H:i'),
            ];
        });
        
        return Inertia::render('admin/produtosConfig/ProdutosConfig') ->with('products', $products);

    }

    public function create()
    {
        return Inertia::render('admin/produtosConfig/AdicionarProduto');
    }

 public function store(Request $request)
    {
        // 1. Validação do request.
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'estoque' => 'required|integer|min:0',
            'tamanhos' => 'required|array', // Agora é um array
            'tamanhos.*.nome' => 'required|string|max:255', // Valida cada item do array
            'tamanhos.*.preco' => 'required|numeric|min:0', // Valida o preço
            'imagens.*' => 'nullable|image|max:2048'
        ]);

        // 2. Criação do produto principal
        $produto = new Produto();
        $produto->user_id = auth()->id();
        $produto->nome = $validated['nome'];
        $produto->descricao = $validated['descricao'];
        $produto->estoque = $validated['estoque'];
        $produto->save();

        // 3. Associar os tamanhos e preços (a parte crucial!)
        foreach ($validated['tamanhos'] as $tamanhoData) {
            // Encontre ou crie o tamanho no seu catálogo `tamanhos`
            $tamanho = Tamanho::firstOrCreate(['nome' => $tamanhoData['nome']]);
            
            // Anexar o tamanho ao produto com o preço na tabela intermediária
            $produto->tamanhos()->attach($tamanho->id, ['preco' => $tamanhoData['preco']]);
        }

        // 4. Salvar as imagens (sua lógica atual, que parece correta)
        if ($request->hasFile('imagens')) {
            $ordem = 1;
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('produtos', 'public');
                ProdutoImagem::create([
                    'produto_id' => $produto->id,
                    'user_id' => auth()->id(),
                    'imagem_path' => $path,
                    'ordem' => $ordem,
                ]);
                $ordem++;
            }
        }

        return Inertia::location(route('produtos.config'));
    }


    public function edit($id)
    {
        $products = Produto::with('imagens')->where('id', $id)->first();
        return Inertia::render('admin/produtosConfig/EditarProduto')->with('products', $products);
    }

    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);
        
        $produto->update($request->all());

        return Inertia::location(route('produtos.config'));
    }

    public function destroy($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();
    }

    public function anuncio($id){
        $produto = Produto::with(['imagens', 'tamanhos'])->findOrFail($id);
        $imagem = $produto->imagens;

        return Inertia::render('Anuncio')->with('produto', $produto, 'imagem', $imagem);
    }
}
