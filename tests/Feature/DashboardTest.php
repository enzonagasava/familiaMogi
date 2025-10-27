<?php
use App\Models\Cargo;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get('/admin/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
        $cargo = Cargo::create([
            'nome' => 'Admin',
            'descricao' => 'Cargo de administrador do sistema',
        ]);
            $user = User::factory()->create([
        'cargo_id' => $cargo->id,
    ]);
    $response = $this->actingAs($user)->get('/admin/dashboard');
    $response->assertStatus(200);
});