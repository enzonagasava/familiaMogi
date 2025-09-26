<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Desabilita as restrições de chave estrangeira
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Trunca a tabela cargos
        DB::table('cargos')->truncate();

        // Reabilita as restrições
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insere os dados
        DB::table('cargos')->insert([
            [
                'id' => 1,
                'nome' => 'admin',
                'descricao' => 'Administrador do sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nome' => 'cliente',
                'descricao' => 'Usuário cliente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
