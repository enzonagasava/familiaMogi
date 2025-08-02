<?php

use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('admin/Dashboard');
        })->name('dashboard');

        Route::get('anuncio/config', function () {
            return Inertia::render('admin/AnuncioConfig');
        })->name('anuncio.config');

        Route::get('blog/config', function () {
            return Inertia::render('admin/BlogConfig');
        })->name('blog.config');

        //Produtos Configurações
        Route::get('produtos/config', [ProdutoController::class, 'index'])
        ->name('produtos.config');
        Route::get('produtos/addproduto', [ProdutoController::class, 'create'])
        ->name('produtos.add');
        Route::post('/produtos', [ProdutoController::class, 'store'])->name('produtos.store');

    }); 