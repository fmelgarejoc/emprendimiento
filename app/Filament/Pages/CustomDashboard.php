<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\VentasStatsWidget;
use App\Filament\Widgets\ProductosStockBajoWidget;
use App\Filament\Widgets\AlertasWidget;


class CustomDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard Principal';

    public function getHeading(): string
    {
        return 'Dashboard del Sistema';
    }

    public function getSubheading(): string
    {
        return 'Resumen de ventas, inventario y estadísticas';
    }

    // Solo sobrescribe los widgets, el resto lo hereda automáticamente
    public function getWidgets(): array
    {
        return [
            VentasStatsWidget::class,
            ProductosStockBajoWidget::class,
            AlertasWidget::class,
        ];
    }
}