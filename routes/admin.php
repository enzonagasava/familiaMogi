<?php

use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CalendarSettingsController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ChatSettingsController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\Ecommerce\AnuncioController;
use App\Http\Controllers\Admin\Ecommerce\DashboardController as EcommerceDashboardController;
use App\Http\Controllers\Admin\GoogleCalendarAuthController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\ProdutoController;
use App\Http\Controllers\App\DashboardController as ClienteDashboardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Settings\InfoEmpresaController;
use App\Http\Controllers\Settings\PagamentoConfigController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/dashboardRota', [AdminDashboardController::class, 'dashboardRota'])
    ->middleware('jwt.cookie', 'auth')
    ->name('dashboardRota');

Route::middleware(['jwt.cookie', 'auth', 'cliente'])
    ->prefix('cliente')
    ->name('cliente.')
    ->group(function () {
        Route::get('/dashboard', [ClienteDashboardController::class, 'index'])->name('dashboard');
    });

Route::middleware(['jwt.cookie', 'auth', 'tipo:ecommerce'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [EcommerceDashboardController::class, 'index'])->name('dashboard');

        Route::get('menu', function () {
            return Inertia::render('admin/ecommerce/MenuIndex');
        })->name('menu.index');

        Route::get('anuncio/config', [AnuncioController::class, 'index'])->name('anuncio.config');
        Route::get('anuncios/create', [AnuncioController::class, 'create'])->name('anuncios.create');
        Route::post('anuncios', [AnuncioController::class, 'store'])->name('anuncios.store');
        Route::get('anuncios/{listing}/edit', [AnuncioController::class, 'edit'])->name('anuncios.edit');
        Route::put('anuncios/{listing}', [AnuncioController::class, 'update'])->name('anuncios.update');
        Route::delete('anuncios/{listing}', [AnuncioController::class, 'destroy'])->name('anuncios.destroy');

        Route::get('paginas/config', function () {
            return Inertia::render('admin/ecommerce/paginasConfig/PaginasConfig');
        })->name('paginas.config');

        Route::get('blog/config', function () {
            return Inertia::render('admin/ecommerce/BlogConfig');
        })->name('blog.config');

        Route::get('produtos/config', [ProdutoController::class, 'index'])->name('produtos.config');
        Route::get('produtos/create-produto', [ProdutoController::class, 'create'])->name('produtos.create');
        Route::post('produtos/addprodutos', [ProdutoController::class, 'store'])->name('produtos.store');
        Route::get('produtos/edit-produto/{id}', [ProdutoController::class, 'edit'])->name('produtos.edit');
        Route::put('produtos/update-produto/{id}', [ProdutoController::class, 'update'])->name('produtos.update');
        Route::delete('produtos/delete-produto/{id}', [ProdutoController::class, 'destroy'])->name('produtos.destroy');

        Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('clientes/adicionarCliente', [ClienteController::class, 'create'])->name('adicionar.clientes');
        Route::get('clientes/editarCliente/{id}', [ClienteController::class, 'edit'])->name('editar.clientes');
        Route::post('clientes/atualizarCliente/{id}', [ClienteController::class, 'update'])->name('atualizar.clientes');
        Route::post('clientes/adicionarCliente', [ClienteController::class, 'store'])->name('clientes.store');
        Route::delete('clientes/deletar-cliente/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::get('clientes/buscar', [SearchController::class, 'buscarCliente'])->name('clientes.buscar');

        Route::put('pedidos/avancarStatus/{id}', [PedidoController::class, 'avancarStatus'])->name('pedidos.avancar.status');
        Route::get('pedidos/adicionarPedido', [PedidoController::class, 'create'])->name('pedidos.create');
        Route::post('pedidos/adicionarPedido', [PedidoController::class, 'store'])->name('pedidos.store');
        Route::get('pedidos/{pedido}/editar', [PedidoController::class, 'edit'])->name('pedidos.edit');
        Route::put('pedidos/{pedido}/editar', [PedidoController::class, 'update'])->name('pedidos.update');
        Route::get('pedidos/{pedido}/visualizar', [PedidoController::class, 'view'])->name('pedidos.view');
        Route::get('pedidos/buscarProduto', [SearchController::class, 'buscarProduto'])->name('pedidos.buscarProduto');
        Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');

        Route::middleware(['permissao:agenda.visualizar'])->group(function () {
            Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
            Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
            Route::post('calendar/events', [CalendarController::class, 'store'])->name('calendar.store');
            Route::put('calendar/events/{id}', [CalendarController::class, 'update'])->name('calendar.update');
            Route::delete('calendar/events/{id}', [CalendarController::class, 'destroy'])->name('calendar.destroy');
            Route::get('calendar/settings', [CalendarSettingsController::class, 'index'])->name('calendar.settings');
            Route::get('calendar/settings/data', [CalendarSettingsController::class, 'data'])->name('calendar.settings.data');
            Route::put('calendar/settings', [CalendarSettingsController::class, 'update'])->name('calendar.settings.update');
            Route::get('calendar/auth', [GoogleCalendarAuthController::class, 'redirect'])->name('calendar.auth');
            Route::get('calendar/callback', [GoogleCalendarAuthController::class, 'callback'])->name('calendar.callback');
            Route::post('calendar/disconnect', [GoogleCalendarAuthController::class, 'disconnect'])->name('calendar.disconnect');
        });

        Route::middleware(['permissao:chat.visualizar'])->group(function () {
            Route::get('chat', [ChatController::class, 'index'])->name('chat');
            Route::get('chat/conversations', [ChatController::class, 'getConversations'])->name('chat.conversations');
            Route::get('chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
            Route::post('chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
            Route::post('chat/mark-read', [ChatController::class, 'markAsRead'])->name('chat.markRead');
            Route::get('chat/settings', [ChatSettingsController::class, 'index'])->name('chat.settings');
            Route::put('chat/settings/config', [ChatSettingsController::class, 'updateConfig'])->name('chat.settings.config');
            Route::post('chat/settings/respostas-rapidas', [ChatSettingsController::class, 'storeRespostaRapida'])->name('chat.settings.respostas.store');
            Route::put('chat/settings/respostas-rapidas/{respostaRapida}', [ChatSettingsController::class, 'updateRespostaRapida'])->name('chat.settings.respostas.update');
            Route::delete('chat/settings/respostas-rapidas/{respostaRapida}', [ChatSettingsController::class, 'destroyRespostaRapida'])->name('chat.settings.respostas.destroy');
        });

        Route::get('config/geral', [ProfileController::class, 'edit'])->name('config.geral');
        Route::patch('config/geral', [ProfileController::class, 'update'])->name('config.update');
        Route::delete('config/geral', [ProfileController::class, 'destroy'])->name('config.destroy');

        Route::prefix('empresa/config')->group(function () {
            Route::get('geral', [InfoEmpresaController::class, 'geral'])->name('empresa.config.geral');
            Route::patch('geral', [InfoEmpresaController::class, 'updateGeral'])->name('empresa.config.update.geral');
            Route::get('logo', [InfoEmpresaController::class, 'Logo'])->name('empresa.config.logo');
            Route::post('logo', [InfoEmpresaController::class, 'updateLogo'])->name('empresa.config.update.logo');
            Route::get('redes-sociais', [InfoEmpresaController::class, 'RedesSociais'])->name('empresa.config.redes');
            Route::patch('redes-sociais', [InfoEmpresaController::class, 'updateRedes'])->name('empresa.config.update.redes');
        });

        Route::get('config/pagamento', [PagamentoConfigController::class, 'index'])->name('config.pagamento');
        Route::patch('config/pagamento', [PagamentoConfigController::class, 'update'])->name('config.pagamento.update');
    });
