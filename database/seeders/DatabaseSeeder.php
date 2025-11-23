<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProveedorSeeder::class,
            ClienteSeeder::class,
            ProductoSeeder::class,
            VentaSeeder::class,
        ]);
    }
}