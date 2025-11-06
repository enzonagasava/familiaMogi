<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\RedeSocial;
use App\Models\IntegracaoPagamento;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Empresa::firstOrCreate(
            ['id' => 1], // garante que só uma empresa seja criada
            [
                'nome' => 'Familia Mogi',
                'email' => 'familiamogi@gmail.com',
                'numero_wpp' => '(11) 99999-9999',
                'telefone' => '(11) 3333-3333',
                'cnpj' => '00.000.000/0001-00',
                'endereco' => 'Rua Exemplo, 123 - Centro',
                'cep' => '00000-000',
                'numero_endereco' => '123',
                'municipio' => 'São Paulo',
                'estado' => 'SP',
            ]
        );

        RedeSocial::firstOrCreate(
          ['id' => 1],
        );

        IntegracaoPagamento::firstOrCreate(
          ['empresa_id' => 1],
        );
    }
}
