<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        $action = parent::getCreateAnotherFormAction();
        $action->hidden(); // Oculta el botón
        return $action;
    }
}
