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

        event(new Registered($user));

        $credentials = $request->only('email', 'password');

        // Autenticação com JWT
        $token = auth()->attempt($credentials);

        if (!$token) {
            // back() retorna RedirectResponse, que está na lista de tipos
            return back()->withErrors(['email' => 'Credenciais inválidas']);
        }

        // Login padrão do Laravel (opcional, mas mantido para consistência)
        Auth::login($user);

        $cookie = cookie(
            'jwt_token',      // nome do cookie
            $token,           // valor do token
            60,               // tempo de vida em minutos
            null,             // path (default '/')
            null,             // domain (default)
            false,            // secure (false para localhost)
            true,             // httpOnly
            false,            // raw
            'Strict'          // SameSite
        );

        if ($user->cargo_id === 1) {
            $redirectUrl = route('admin.dashboard');
        } elseif ($user->cargo_id === 2) {
            $redirectUrl = route('cliente.dashboard');
        } else {
            $redirectUrl = route('home'); // fallback válido
        }

        // Inertia::location()...->withCookie() retorna Illuminate\Http\Response
        return Inertia::location($redirectUrl)->withCookie($cookie);
    }
}
