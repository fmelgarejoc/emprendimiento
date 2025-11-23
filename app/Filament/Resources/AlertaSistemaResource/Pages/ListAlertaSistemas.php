<?php

namespace App\Filament\Resources\AlertaSistemaResource\Pages;

use App\Filament\Resources\AlertaSistemaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlertaSistemas extends ListRecords
{
    protected static string $resource = AlertaSistemaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
