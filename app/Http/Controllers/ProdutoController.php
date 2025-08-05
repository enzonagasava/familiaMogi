<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\AnuncioImagem;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProdutoController extends Controller
{
    public function index()
    {
        $products = Anuncio::with('imagens')->get()->map(function ($product) {
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
        // Validação básica
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'estoque' => 'required|integer|min:0',
            'tamanhos' => 'required|string', // JSON string
            'imagens.*' => 'nullable|image|max:2048' // max 2MB
        ]);

        $imagensPaths = [];

        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $imagem) {
                $imagensPaths[] = $imagem->store('produtos', 'public');
            }
        }
        $produto = new Anuncio();
        $produto->user_id = auth()->id(); 
        $produto->nome = $validated['nome'];
        $produto->descricao = $validated['descricao'];
        $produto->estoque = $validated['estoque'];
        $produto->tamanhos = $validated['tamanhos'];
        $produto->save();

        if ($request->hasFile('imagens')) {
            $ordem = 1;
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('produtos', 'public');
                AnuncioImagem::create([
                    'anuncio_id' => $produto->id,
                    'user_id' => auth()->id(), // se quiser manter user_id na tabela
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
        $products = Anuncio::with('imagens')->where('id', $id)->first();
        return Inertia::render('admin/produtosConfig/EditarProduto')->with('products', $products); ;
    }

    public function update(Request $request, $id)
    {
        $produto = Anuncio::findOrFail($id);
        
        $produto->update($request->all());

        return Inertia::location(route('produtos.config'));
    }

    public function destroy($id)
    {
        $produto = Anuncio::findOrFail($id);
        $produto->delete();
    }
}
