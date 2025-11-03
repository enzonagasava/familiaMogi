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
        $cargo = Cargo::create([
            'nome' => 'Admin',
            'descricao' => 'Cargo de administrador do sistema',
        ]);

        $this->assertDatabaseHas('cargos', [
            'id' => $cargo->id,
            'nome' => 'Admin',
        ]);
    }
}
