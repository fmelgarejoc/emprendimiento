<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VentaDetalleFactory extends Factory
{
    public function definition(): array
    {
        $cantidad = $this->faker->numberBetween(1, 5);
        $precioUnitario = $this->faker->numberBetween(100, 3000);
        $subtotal = $cantidad * $precioUnitario;

        return [
            'venta_id' => \App\Models\Venta::factory(),
            'producto_id' => \App\Models\Producto::factory(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal,
            'created_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }

    // Estado para productos populares (más cantidad)
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'cantidad' => $this->faker->numberBetween(3, 8),
        ]);
    }

    // Estado para productos con poca cantidad
    public function pocaCantidad(): static
    {
        return $this->state(fn (array $attributes) => [
            'cantidad' => $this->faker->numberBetween(1, 2),
        ]);
    }

    // Estado para productos de alto valor
    public function altoValor(): static
    {
        return $this->state(fn (array $attributes) => [
            'precio_unitario' => $this->faker->numberBetween(2000, 8000),
            'cantidad' => 1, // Generalmente productos caros se venden en menor cantidad
        ]);
    }
}