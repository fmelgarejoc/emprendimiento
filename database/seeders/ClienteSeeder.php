<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Crear 15 clientes aleatorios
        Cliente::factory()->count(15)->create();

        
    }
}