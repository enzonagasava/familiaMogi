<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\App\DashboardController as ClienteDashboardController;
use App\Http\Controllers\Admin\ProdutoController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

    Route::middleware(['jwt.cookie', 'auth', 'verified'])->group(function () {

        Route::get('anuncio/config', function () {
            return Inertia::render('admin/AnuncioConfig');
        })->name('anuncio.config');


        Route::get('paginas/config', function () {
            return Inertia::render('admin/paginasConfig/PaginasConfig');
        })->name('paginas.config');

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

        //Rotas do Cliente
        Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('clientes/adicionarCliente', [ClienteController::class, 'create'])->name('adicionar.clientes');
        Route::get('clientes/editarCliente/{id}', [ClienteController::class, 'edit'])->name('editar.clientes');

        Route::post('clientes/atualizarCliente/{id}', [ClienteController::class, 'update'])->name('atualizar.clientes');
        Route::post('clientes/adicionarCliente', [ClienteController::class, 'store'])->name('clientes.store');
        Route::delete('clientes/deletar-cliente/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::get('/clientes/buscar', [SearchController::class, 'buscarCliente'])->name('clientes.buscar');


    });

    Route::middleware(['jwt.cookie', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        //Rotas Pedidos
        Route::put('/pedidos/avancarStatus/{id}', [PedidoController::class, 'avancarStatus'])
         ->name('pedidos.avancar.status');
        Route::get('/pedidos/adicionarPedido', [PedidoController::class, 'create'])->name('pedidos.create');
        Route::post('/pedidos/adicionarPedido', [PedidoController::class, 'store'])->name('pedidos.store');

        Route::get('/pedidos/{pedido}/editar', [PedidoController::class, 'edit'])
            ->name('pedidos.edit');
        Route::put('/pedidos/{pedido}/editar', [PedidoController::class, 'update'])
            ->name('pedidos.update');

        Route::get('/pedidos/{pedido}/visualizar', [PedidoController::class, 'view'])->name('pedidos.view');

        Route::get('/pedidos/buscarProduto', [SearchController::class, 'buscarProduto'])->name('pedidos.buscarProduto');

        Route::get('/pedidos/{status}', [PedidoController::class, 'index'])->where('status', 'em-andamento|a-caminho|finalizado')->name('pedidos.index');



    });

    Route::middleware(['jwt.cookie','auth', 'cliente'])->prefix('cliente')->name('cliente.')->group(function () {
        Route::get('/dashboard', [ClienteDashboardController::class, 'index'])->name('dashboard');
        // rotas cliente...
    });
