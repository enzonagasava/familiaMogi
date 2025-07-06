<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/anuncio', function () {
    return Inertia::render('Anuncio');
})->name('anuncio');

Route::get('/carrinho', function () {
    return Inertia::render('Cart');
})->name('carrinho');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
