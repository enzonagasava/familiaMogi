<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\RedeSocial;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        // Busca o tipo de painel do e-grocery no banco administrativo
        $tipoEcommerce = DB::connection('nexa_admin')
            ->table('tipo_painel')
            ->where('nome', 'CRM E-Grocery')
            ->first();
        
        // Empresa principal - E-commerce
        Empresa::firstOrCreate(
            ['id' => 1],
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
                'tipo_painel_id' => $tipoEcommerce->id ?? null,
            ]
        );

        logger()->info('EmpresaSeeder: Empresa principal criada ou já existente.');
    }
}