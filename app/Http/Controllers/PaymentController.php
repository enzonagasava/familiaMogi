<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;


class PaymentController extends Controller
{
    public function index(){
        $cart = session()->get('cart', []);

        return inertia('PagePay', ['cart' => $cart]);
    }


}
