<?php

namespace App\Filament\Widgets;

use App\Models\Producto;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ProductosStockBajoWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Producto::query()->whereColumn('stock', '<', 'stock_minimo')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('stock_minimo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->money('USD')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('reponer')
                    ->label('Reponer')
                    ->icon('heroicon-o-plus')
                    //->url(fn (Producto $record) => ProductoResource::getUrl('edit', ['record' => $record])),
            ])
            ->heading('Productos con Stock Bajo')
            ->description('Productos que necesitan reposición urgente');
    }
}