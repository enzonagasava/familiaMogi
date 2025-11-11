<?php

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\InfoEmpresaController;
use App\Http\Controllers\Settings\PagamentoConfigController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['jwt.cookie', 'auth', 'verified'])->group(function () {

    //Configuração de Acesso
    Route::get('config/geral', [ProfileController::class, 'edit'])->name('config.geral');
    Route::patch('config/geral', [ProfileController::class, 'update'])->name('config.update');
    Route::delete('config/geral', [ProfileController::class, 'destroy'])->name('config.destroy');

    //Configuração da Informação da Empresa
    Route::prefix('empresa/config')->middleware(['auth'])->group(function () {
        Route::get('/geral', [InfoEmpresaController::class, 'geral'])->name('empresa.config.geral');
        Route::patch('/geral', [InfoEmpresaController::class, 'updateGeral'])->name('empresa.config.update.geral');

        Route::get('/logo', [InfoEmpresaController::class, 'Logo'])->name('empresa.config.logo');
        Route::post('/logo', [InfoEmpresaController::class, 'updateLogo'])->name('empresa.config.update.logo');

        Route::get('/redes-sociais', [InfoEmpresaController::class, 'RedesSociais'])->name('empresa.config.redes');
        Route::patch('/redes-sociais', [InfoEmpresaController::class, 'updateRedes'])->name('empresa.config.update.redes');

    });

    //Configuraçao Métodos de Pagamento
    Route::get('config/pagamento', [PagamentoConfigController::class, 'index'])->name('config.pagamento');
    Route::patch('config/pagamento', [PagamentoConfigController::class, 'update'])->name('config.pagamento.update');

});
