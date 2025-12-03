<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
// Importamos o tipo base Response para resolver o TypeError
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use App\Mail\BemVindoEmail;
use Illuminate\Support\Facades\Mail;
use Inertia\Response as InertiaResponse; // Alias para Inertia Response

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * * CORREÇÃO: Adicionamos |Response para cobrir o retorno de Inertia::location()...->withCookie()
     */
    public function store(Request $request): RedirectResponse | InertiaResponse | Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cargo_id' => '2',
        ]);

        $credentials = $request->only('email', 'password');

        // Autenticação com JWT no guard correto
        $token = auth('api')->attempt($credentials);

        if (!$token) {
            return back()->withErrors(['email' => 'Credenciais inválidas']);
        }

        // Login Laravel
        Auth::login($user);

        // Cookie JWT
        $cookie = cookie(
            'jwt_token',
            $token,
            60,
            '/',
            null,
            false,
            true,
            false,
            'Strict'
        );

        // Definir rota
        $redirectUrl = $user->cargo_id == 1
            ? route('admin.dashboard')
            : route('cliente.dashboard');

        // Enviar email
        Mail::to($user->email)->send(new BemVindoEmail($user));

        // Redirecionamento normal (mantém sessão!)
        return redirect($redirectUrl)->withCookie($cookie);

    }
}
