<?php

namespace App\Filament\Widgets;

use App\Models\AlertaSistema;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AlertasWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAlertas = AlertaSistema::where('resuelta', false)->count();
        $alertasCriticas = AlertaSistema::where('resuelta', false)->where('gravedad', 'critica')->count();
        $alertasAltas = AlertaSistema::where('resuelta', false)->where('gravedad', 'alta')->count();

        return [
            Stat::make('Alertas Pendientes', $totalAlertas)
                ->description('Total de alertas sin resolver')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($totalAlertas > 0 ? 'danger' : 'success'),

            Stat::make('Alertas Críticas', $alertasCriticas)
                ->description('Requieren atención inmediata')
                ->descriptionIcon('heroicon-o-bell-alert')
                ->color($alertasCriticas > 0 ? 'danger' : 'gray'),

            Stat::make('Alertas Altas', $alertasAltas)
                ->description('Necesitan atención pronto')
                ->descriptionIcon('heroicon-o-bell')
                ->color($alertasAltas > 0 ? 'warning' : 'gray'),
        ];
    }
}