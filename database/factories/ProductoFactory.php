<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    public function definition(): array
    {
        $categorias = [
            'Laptop' => ['Gaming', 'Profesional', 'Estudiante', 'Ultrabook'],
            'Tablet' => ['Básica', 'Profesional', 'Gaming', 'Infantil'],
            'Smartphone' => ['Gama Alta', 'Gama Media', 'Económico', 'Gaming'],
            'Monitor' => ['Gaming', 'Oficina', 'Profesional', 'Curvo'],
            'Teclado' => ['Mecánico', 'Membrana', 'Inalámbrico', 'Gaming'],
            'Mouse' => ['Gaming', 'Oficina', 'Inalámbrico', 'Ergonómico'],
        ];

        $categoria = $this->faker->randomElement(array_keys($categorias));
        $tipo = $this->faker->randomElement($categorias[$categoria]);
        
        $precioCompra = $this->faker->numberBetween(50, 5000);
        $precioVenta = $precioCompra * 1.4; // 40% de margen
        
        return [
            'nombre' => "{$categoria} {$tipo} {$this->faker->randomElement(['Pro', 'Plus', 'Elite', 'Standard', 'Premium'])}",
            'descripcion' => $this->generarDescripcionProducto($categoria, $tipo),
            'precio_compra' => $precioCompra,
            'precio_venta' => $precioVenta,
            'stock' => $this->faker->numberBetween(0, 100),
            'stock_minimo' => $this->faker->numberBetween(5, 15),
            'proveedor_id' => \App\Models\Proveedor::factory(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', '-2 months'),
        ];
    }

    private function generarDescripcionProducto($categoria, $tipo): string
    {
        $descripciones = [
            'Laptop' => [
                'Laptop de alto rendimiento ideal para ' . ($tipo === 'Gaming' ? 'juegos y aplicaciones demandantes' : 'trabajo profesional'),
                'Equipo portátil con características avanzadas para ' . ($tipo === 'Estudiante' ? 'estudios y tareas' : 'uso diario'),
                'Computadora portátil con excelente desempeño para ' . ($tipo === 'Ultrabook' ? 'movilidad y productividad' : 'multitarea'),
            ],
            'Tablet' => [
                'Tablet versátil perfecta para ' . ($tipo === 'Profesional' ? 'reuniones y presentaciones' : 'entretenimiento y productividad'),
                'Dispositivo portátil con pantalla de alta resolución ideal para ' . ($tipo === 'Infantil' ? 'aprendizaje y juegos' : 'consumo de contenido'),
                'Tablet con características modernas para ' . ($tipo === 'Gaming' ? 'juegos móviles' : 'navegación web'),
            ],
            'Smartphone' => [
                'Teléfono inteligente con características de ' . ($tipo === 'Gama Alta' ? 'última generación' : 'excelente relación calidad-precio'),
                'Dispositivo móvil con capacidades avanzadas para ' . ($tipo === 'Gaming' ? 'juegos móviles' : 'comunicación y productividad'),
                'Smartphone con tecnología innovadora para ' . ($tipo === 'Económico' ? 'uso diario básico' : 'experiencia premium'),
            ],
            'Monitor' => [
                'Monitor de alta calidad para ' . ($tipo === 'Gaming' ? 'experiencia de juego inmersiva' : 'trabajo profesional'),
                'Pantalla con tecnología avanzada ideal para ' . ($tipo === 'Oficina' ? 'espacios de trabajo' : 'uso doméstico'),
                'Monitor con excelente claridad para ' . ($tipo === 'Curvo' ? 'experiencia envolvente' : 'tareas de diseño'),
            ],
            'Teclado' => [
                'Teclado ' . ($tipo === 'Mecánico' ? 'mecánico para escritura precisa y respuesta táctil' : 'de membrana silencioso'),
                'Periférico de entrada ' . ($tipo === 'Inalámbrico' ? 'inalámbrico para mayor libertad de movimiento' : 'con cable para respuesta inmediata'),
                'Teclado ' . ($tipo === 'Gaming' ? 'especializado para juegos con retroiluminación RGB' : 'ergonómico para uso prolongado'),
            ],
            'Mouse' => [
                'Mouse ' . ($tipo === 'Gaming' ? 'para gaming con alta precisión y botones programables' : 'de oficina cómodo y funcional'),
                'Ratón ' . ($tipo === 'Inalámbrico' ? 'inalámbrico para escritorio sin cables' : 'con cable para respuesta inmediata'),
                'Mouse ' . ($tipo === 'Ergonómico' ? 'ergonómico para prevenir fatiga en uso prolongado' : 'compacto para portabilidad'),
            ],
        ];

        // Si la categoría no existe en descripciones, usar una descripción genérica
        if (!isset($descripciones[$categoria])) {
            return "Producto de calidad {$categoria} {$tipo}. Ideal para múltiples usos. Incluye garantía del fabricante.";
        }

        return $descripciones[$categoria][array_rand($descripciones[$categoria])] . '. Incluye garantía del fabricante.';
    }

    // Estado para productos con stock bajo
    public function stockBajo(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $this->faker->numberBetween(0, 4),
            'stock_minimo' => 10,
        ]);
    }

    // Estado para productos con exceso de stock
    public function excesoStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $this->faker->numberBetween(100, 200),
            'stock_minimo' => 10,
        ]);
    }

    // Estado para productos sin stock
    public function sinStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'stock_minimo' => 5,
        ]);
    }

    // Estado para productos populares (más stock)
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $this->faker->numberBetween(50, 100),
            'stock_minimo' => 10,
        ]);
    }

    // Estado para productos específicos de tecnología
    public function laptop(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => 'Laptop ' . $this->faker->randomElement(['Gaming Pro', 'Business Elite', 'Student Plus', 'Ultrabook Premium']),
            'precio_compra' => $this->faker->numberBetween(2000, 8000),
            'precio_venta' => $this->faker->numberBetween(2800, 11200),
            'stock' => $this->faker->numberBetween(2, 15),
            'stock_minimo' => 3,
        ]);
    }

    public function smartphone(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => 'Smartphone ' . $this->faker->randomElement(['Ultra 5G', 'Pro Max', 'Lite Edition', 'Gaming Edition']),
            'precio_compra' => $this->faker->numberBetween(800, 4000),
            'precio_venta' => $this->faker->numberBetween(1120, 5600),
            'stock' => $this->faker->numberBetween(5, 30),
            'stock_minimo' => 5,
        ]);
    }
}