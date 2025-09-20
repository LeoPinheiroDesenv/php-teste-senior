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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Executar seeder de dados de teste
        $this->call(TestDataSeeder::class);
        
        // Opcional: Executar seeder com factories para dados aleatórios
        // Descomente a linha abaixo se quiser usar factories
        $this->call(FactorySeeder::class);
    }
}
