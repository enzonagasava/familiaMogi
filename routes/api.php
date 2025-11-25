<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\CheckoutController;
use Illuminate\Support\Facades\Auth;


Route::post('/checkout/process', [CheckoutController::class, 'process'])
    ->name('checkout.process');

Route::post('/checkout/webhook', [CheckoutController::class, 'webhook'])
    ->name('checkout.webhook');

Route::middleware('web')->get('/auth/check', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user(),
    ]);
});

