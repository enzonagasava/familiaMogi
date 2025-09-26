<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminExists = User::where('email', 'admin@teste.com')->exists();

        if (!$adminExists) {
            User::factory()->create([
                'name' => 'admin',
                'email' => 'admin@teste.com',
                'password' => bcrypt('123456789'), // Sempre use bcrypt para senha
                'cargo_id' => 1,
            ]);
        }
        
        $this->call(CargoSeeder::class);
    }
}
