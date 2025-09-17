<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('anuncio/config', function () {
            return Inertia::render('admin/AnuncioConfig');
        })->name('anuncio.config');

        Route::get('blog/config', function () {
            return Inertia::render('admin/BlogConfig');
        })->name('blog.config');

        //Produtos Configurações
        Route::get('produtos/config', [ProdutoController::class, 'index'], function () {
            return Inertia::render('admin/produtosConfig/ProdutosConfig');
        })->name('produtos.config');
        Route::get('produtos/create-produto', [ProdutoController::class, 'create'], function () {
            return Inertia::render('admin/produtosConfig/AdicionarProduto');
        })->name('produtos.create');
        Route::post('produtos/addprodutos', [ProdutoController::class, 'store'])->name('produtos.store');
        Route::get('produtos/edit-produto/{id}', [ProdutoController::class, 'edit'])->name('produtos.edit');
        Route::put('produtos/update-produto/{id}', [ProdutoController::class, 'update'])->name('produtos.update');
        Route::delete('produtos/delete-produto/{id}', [ProdutoController::class, 'destroy'])->name('produtos.destroy'); 
    });