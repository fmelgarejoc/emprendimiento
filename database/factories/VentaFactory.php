<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VentaFactory extends Factory
{
    public function definition(): array
    {
        $fechaVenta = $this->faker->dateTimeBetween('-2 months', 'now');
        $subtotal = $this->faker->numberBetween(500, 10000);
        $impuesto = $subtotal * 0.12; // 12% de IVA
        $total = $subtotal + $impuesto;

        return [
            'numero_factura' => 'FAC-' . $fechaVenta->format('Ymd') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'cliente_id' => \App\Models\Cliente::factory(),
            'fecha_venta' => $fechaVenta,
            'subtotal' => $subtotal,
            'impuesto' => $impuesto,
            'total' => $total,
            'estado' => 'completada',
            'observaciones' => $this->faker->optional(0.3)->randomElement([
                'Venta al contado',
                'Cliente frecuente',
                'Promoción especial',
                'Pago con tarjeta',
                'Entrega programada',
                'Sin observaciones'
            ]),
            'created_at' => $fechaVenta,
            'updated_at' => $fechaVenta,
        ];
    }

    // Estado para ventas recientes (última semana)
    public function reciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_venta' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    // Estado para ventas del mes pasado
    public function mesPasado(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_venta' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'created_at' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
        ]);
    }

    // Estado para ventas pendientes
    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
            'observaciones' => 'Venta pendiente de pago',
        ]);
    }

    // Estado para ventas canceladas
    public function cancelada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'cancelada',
            'observaciones' => 'Venta cancelada por el cliente',
        ]);
    }

    // Estado para ventas de alto valor
    public function altoValor(): static
    {
        return $this->state(fn (array $attributes) => [
            'subtotal' => $this->faker->numberBetween(5000, 20000),
            'impuesto' => $this->faker->numberBetween(500, 2000),
            'total' => $this->faker->numberBetween(5500, 22000),
        ]);
    }
}