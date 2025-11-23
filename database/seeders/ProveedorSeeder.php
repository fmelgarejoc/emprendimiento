<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        // Crear 8 proveedores
        Proveedor::factory()->count(8)->create();

    
    }
}