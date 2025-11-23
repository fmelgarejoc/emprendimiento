<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class VentasStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $ventasHoy = Venta::whereDate('fecha_venta', today())->count();
        $totalVentasMes = Venta::whereMonth('fecha_venta', now()->month)->sum('total');
        $productosStockBajo = Producto::where('stock', '<', DB::raw('stock_minimo'))->count();
        $totalClientes = Cliente::count();

        return [
            Stat::make('Ventas Hoy', $ventasHoy)
                ->description('Número de ventas hoy')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Ventas del Mes', '$' . number_format($totalVentasMes, 2))
                ->description('Total vendido este mes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),

            Stat::make('Productos Stock Bajo', $productosStockBajo)
                ->description('Productos que necesitan reposición')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($productosStockBajo > 0 ? 'danger' : 'success'),

            Stat::make('Total Clientes', $totalClientes)
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}