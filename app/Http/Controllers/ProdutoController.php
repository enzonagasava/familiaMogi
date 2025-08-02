<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\AnuncioImagem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProdutoController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/produtosConfig/ProdutosConfig');
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



        // Para este exemplo, só vamos retornar sucesso
        return redirect()->back()->with('success', 'Produto salvo com sucesso!');
    }
}
