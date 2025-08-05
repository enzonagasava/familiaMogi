<?php

namespace App\Http\Controllers;
use App\Models\Anuncio;
use Inertia\Inertia;

class HomeController extends Controller
{    /**
     * Base controller method for demonstration purposes.
     *
     * @return string
     */
    public function index(){
        $produto = Anuncio::with('imagens')->get()->map(function ($produto) {
            return [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'descricao' => $produto->descricao,
                'estoque' => $produto->estoque,
                'tamanhos' => $produto->tamanhos,
                'imageUrl' => $produto->imagens->first()
                    ? asset('storage/' . $produto->imagens->first()->imagem_path)
                    : null,
                'created_at' => $produto->created_at->format('d/m/Y H:i'),
            ];
        });

        return Inertia::render('Home', [
            'produto' => $produto,
        ]);
    }
}
