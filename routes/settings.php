<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['jwt.cookie', 'auth', 'verified'])->group(function () {

    Route::get('config/geral', [ProfileController::class, 'edit'])->name('config.geral');

    Route::patch('config/geral', [ProfileController::class, 'update'])->name('config.update');
    Route::delete('config/geral', [ProfileController::class, 'destroy'])->name('config.destroy');
});
