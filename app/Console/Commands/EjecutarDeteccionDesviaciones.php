<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DeteccionDesviacionesService;

class EjecutarDeteccionDesviaciones extends Command
{
    protected $signature = 'sistema:deteccion-desviaciones';
    protected $description = 'Ejecuta la detección de desviaciones en el sistema';

    public function handle()
    {
        $this->info('Iniciando detección de desviaciones...');
        
        $service = new DeteccionDesviacionesService();
        $service->ejecutarDeteccion();
        
        $this->info('Detección de desviaciones completada.');
        
        return Command::SUCCESS;
    }
}