<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    protected static string $resource = ClienteResource::class;
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        $action = parent::getCreateAnotherFormAction();
        $action->hidden(); // Oculta el botón
        return $action;
    }
}
