<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;


class PaymentController extends Controller
{
    public function index(){
        $cart = session()->get('cart', []);

        return inertia('PagePay', ['cart' => $cart]);
    }
}
