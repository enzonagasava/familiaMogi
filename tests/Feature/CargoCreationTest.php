<?php

namespace Tests\Feature;

use App\Models\Cargo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CargoCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_cargo()
    {
        // Cria um cargo usando factory
        $cargo = Cargo::create([
            'nome' => 'Admin',
            'descricao' => 'Cargo de administrador do sistema',
        ]);

        // Verifica se o cargo foi criado no banco de dados
        $this->assertDatabaseHas('cargos', [
            'id' => $cargo->id,
            'nome' => 'Admin',
        ]);
    }
}
