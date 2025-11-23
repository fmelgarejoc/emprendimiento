<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement([
                'Tecnología Avanzada S.A.',
                'Suministros Industriales Ltda.',
                'Electrónica Centroamericana',
                'Importadora de Componentes',
                'Distribuidora de Hardware',
                'Soluciones Tecnológicas S.A.',
                'Proveedora de Equipos',
                'TecnoImport Guatemala',
                'ElectroSuministros S.A.',
                'Componentes y Accesorios Ltda.'
            ]),
            'contacto' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
            'telefono' => $this->faker->randomElement(['+502 2###-####', '+502 2###-####']),
            'email' => $this->faker->unique()->companyEmail(),
            'direccion' => $this->generarDireccionGuatemala(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', '-2 months'),
        ];
    }

    private function generarDireccionGuatemala(): string
    {
        $zonas = ['Zona 4', 'Zona 5', 'Zona 7', 'Zona 9', 'Zona 10', 'Zona 11', 'Zona 12', 'Zona 13'];
        $calles = ['Avenida Reforma', 'Boulevard Los Próceres', 'Calzada Roosevelt', 'Avenida Las Américas', 'Ruta al Atlántico'];
        $colonias = ['Colonia Industrial', 'Colonia El Naranjo', 'Colonia San José', 'Colonia El Carmen'];
        
        return $this->faker->randomElement($colonias) . ', ' . 
               $this->faker->randomElement($calles) . ', ' . 
               $this->faker->randomElement($zonas) . ', ' . 
               'Ciudad de Guatemala';
    }
}