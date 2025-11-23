<?php

namespace App\Filament\Resources\AlertaSistemaResource\Pages;

use App\Filament\Resources\AlertaSistemaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAlertaSistema extends CreateRecord
{
    protected static string $resource = AlertaSistemaResource::class;
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        $action = parent::getCreateAnotherFormAction();
        $action->hidden(); // Oculta el botón
        return $action;
    }
}
