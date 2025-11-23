<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        $action = parent::getCreateAnotherFormAction();
        $action->hidden(); // Oculta el botón
        return $action;
    }

}
