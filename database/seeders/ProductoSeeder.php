<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = Proveedor::all();

        // Productos con stock normal (20 productos)
        Producto::factory()->count(20)->create();

        // Productos con stock bajo (5 productos)
        Producto::factory()->count(5)->stockBajo()->create();

        // Productos con exceso de stock (3 productos)
        Producto::factory()->count(3)->excesoStock()->create();

        // Productos sin stock (2 productos)
        Producto::factory()->count(2)->sinStock()->create();

        // Productos específicos para testing de alertas
        Producto::create([
            'nombre' => 'Laptop Gaming Pro',
            'descripcion' => 'Laptop para gaming con RTX 4060, 16GB RAM, 1TB SSD',
            'precio_compra' => 7500,
            'precio_venta' => 10500,
            'stock' => 2, // Stock bajo
            'stock_minimo' => 5,
            'proveedor_id' => $proveedores->first()->id,
            'created_at' => now()->subMonths(2),
        ]);

        Producto::create([
            'nombre' => 'Tablet Básica 10"',
            'descripcion' => 'Tablet Android 10 pulgadas, 64GB, WiFi',
            'precio_compra' => 800,
            'precio_venta' => 1120,
            'stock' => 45, // Exceso de stock
            'stock_minimo' => 10,
            'proveedor_id' => $proveedores->first()->id,
            'created_at' => now()->subMonths(2),
        ]);

        Producto::create([
            'nombre' => 'Smartphone Ultra',
            'descripcion' => 'Teléfono inteligente 5G, 256GB, cámara 108MP',
            'precio_compra' => 3500,
            'precio_venta' => 4900,
            'stock' => 0, // Sin stock
            'stock_minimo' => 3,
            'proveedor_id' => $proveedores->first()->id,
            'created_at' => now()->subMonths(2),
        ]);
    }
}