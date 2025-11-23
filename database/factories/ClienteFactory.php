<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        $esEmpresa = $this->faker->boolean(30); // 30% probabilidad de ser empresa
        
        if ($esEmpresa) {
            $nombre = $this->faker->randomElement([
                'Supermercado La Torre',
                'Tiendas Paiz',
                'Distribuidora Comercial',
                'Farmacias Kiefer',
                'Restaurante Hacienda Real',
                'Hotel Camino Real',
                'Constructora Maya',
                'Importadora Centroamericana',
                'Textiles del Valle',
                'Mueblería Nacional'
            ]);
        } else {
            $nombre = $this->faker->firstName() . ' ' . $this->faker->lastName() . ' ' . $this->faker->lastName();
        }

        return [
            'nombre' => $nombre,
            'nit' => $this->faker->unique()->numerify('###########'),
            'celular' => $this->faker->randomElement(['+502 5###-####', '+502 4###-####', '+502 3###-####']),
            'email' => $this->faker->unique()->safeEmail(),
            'direccion' => $this->generarDireccionGuatemala(),
            'created_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }

    private function generarDireccionGuatemala(): string
    {
        $zonas = ['Zona 1', 'Zona 2', 'Zona 3', 'Zona 4', 'Zona 5', 'Zona 6', 'Zona 7', 'Zona 9', 'Zona 10', 'Zona 11', 'Zona 12', 'Zona 13', 'Zona 14', 'Zona 15'];
        $calles = ['Calle Principal', 'Avenida Reforma', 'Boulevard Los Próceres', 'Calzada Roosevelt', 'Avenida Las Américas', 'Ruta al Atlántico', 'Calzada San Juan'];
        $colonias = ['Colonia El Naranjo', 'Colonia San José', 'Colonia El Carmen', 'Colonia La Florida', 'Colonia Vista Hermosa', 'Colonia San Cristóbal'];
        
        return $this->faker->randomElement($colonias) . ', ' . 
               $this->faker->randomElement($calles) . ', ' . 
               $this->faker->randomElement($zonas) . ', ' . 
               'Ciudad de Guatemala';
    }

    // Estado para clientes que son personas naturales
    public function personaNatural(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => $this->faker->firstName() . ' ' . $this->faker->lastName() . ' ' . $this->faker->lastName(),
        ]);
    }

    // Estado para clientes que son empresas
    public function empresa(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => $this->faker->randomElement([
                'Supermercado La Torre',
                'Tiendas Paiz', 
                'Distribuidora Comercial',
                'Farmacias Kiefer',
                'Restaurante Hacienda Real',
                'Hotel Camino Real',
                'Constructora Maya',
                'Importadora Centroamericana',
                'Textiles del Valle',
                'Mueblería Nacional'
            ]),
        ]);
    }
}