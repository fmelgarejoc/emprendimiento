<?php

namespace Database\Seeders;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();
        $productos = Producto::all();

        // Ventas del mes pasado (20 ventas)
        Venta::factory()
            ->count(20)
            ->mesPasado()
            ->create()
            ->each(function ($venta) use ($productos) {
                $this->crearDetallesVenta($venta, $productos);
            });

        // Ventas de este mes (30 ventas)
        Venta::factory()
            ->count(30)
            ->reciente()
            ->create()
            ->each(function ($venta) use ($productos) {
                $this->crearDetallesVenta($venta, $productos);
            });

        // Ventas pendientes (3 ventas)
        Venta::factory()
            ->count(3)
            ->pendiente()
            ->create()
            ->each(function ($venta) use ($productos) {
                $this->crearDetallesVenta($venta, $productos);
            });

        // Ventas canceladas (2 ventas)
        Venta::factory()
            ->count(2)
            ->cancelada()
            ->create()
            ->each(function ($venta) use ($productos) {
                $this->crearDetallesVenta($venta, $productos);
            });

        // Ventas específicas para testing de alertas
        $this->crearVentasEspecificas($clientes, $productos);
    }

    private function crearDetallesVenta($venta, $productos)
    {
        $numDetalles = rand(1, 4);
        $subtotalTotal = 0;

        for ($i = 0; $i < $numDetalles; $i++) {
            $producto = $productos->random();
            $cantidad = rand(1, 3);
            $precioUnitario = $producto->precio_venta;

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $cantidad * $precioUnitario,
                'created_at' => $venta->fecha_venta,
            ]);

            $subtotalTotal += $cantidad * $precioUnitario;
        }

        // Actualizar totales de la venta
        $impuesto = $subtotalTotal * 0.12;
        $venta->update([
            'subtotal' => $subtotalTotal,
            'impuesto' => $impuesto,
            'total' => $subtotalTotal + $impuesto,
        ]);
    }

    private function crearVentasEspecificas($clientes, $productos)
    {
        // Crear ventas para productos específicos (para testing de alertas)
        $productoLaptop = $productos->where('nombre', 'Laptop Gaming Pro')->first();
        $productoTablet = $productos->where('nombre', 'Tablet Básica 10"')->first();

        if ($productoLaptop) {
            // Solo 1 venta de laptop en 2 meses (bajas ventas)
            Venta::factory()
                ->create([
                    'cliente_id' => $clientes->random()->id,
                    'fecha_venta' => now()->subDays(45),
                    'subtotal' => $productoLaptop->precio_venta,
                    'impuesto' => $productoLaptop->precio_venta * 0.12,
                    'total' => $productoLaptop->precio_venta * 1.12,
                ])
                ->each(function ($venta) use ($productoLaptop) {
                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $productoLaptop->id,
                        'cantidad' => 1,
                        'precio_unitario' => $productoLaptop->precio_venta,
                        'subtotal' => $productoLaptop->precio_venta,
                        'created_at' => $venta->fecha_venta,
                    ]);
                });
        }

        if ($productoTablet) {
            // Varias ventas de tablet (producto popular)
            for ($i = 0; $i < 8; $i++) {
                Venta::factory()
                    ->create([
                        'cliente_id' => $clientes->random()->id,
                        'fecha_venta' => now()->subDays(rand(1, 60)),
                    ])
                    ->each(function ($venta) use ($productoTablet) {
                        $cantidad = rand(1, 2);
                        VentaDetalle::create([
                            'venta_id' => $venta->id,
                            'producto_id' => $productoTablet->id,
                            'cantidad' => $cantidad,
                            'precio_unitario' => $productoTablet->precio_venta,
                            'subtotal' => $cantidad * $productoTablet->precio_venta,
                            'created_at' => $venta->fecha_venta,
                        ]);
                    });
            }
        }
    }
}