<?php

namespace App\Filament\Resources\ProveedorResource\Pages;

use App\Filament\Resources\ProveedorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProveedor extends CreateRecord
{
    protected static string $resource = ProveedorResource::class;
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        $action = parent::getCreateAnotherFormAction();
        $action->hidden(); // Oculta el botón
        return $action;
    }
}
