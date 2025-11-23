<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlertaSistemaResource\Pages;
use App\Filament\Resources\AlertaSistemaResource\RelationManagers;
use App\Models\AlertaSistema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlertaSistemaResource extends Resource
{
    protected static ?string $model = AlertaSistema::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Alertas del Sistema';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('gravedad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'critica' => 'Crítica',
                    ])
                    ->required(),
                Forms\Components\Select::make('producto_id')
                    ->relationship('producto', 'nombre')
                    ->searchable()
                    ->preload(),
                Forms\Components\Toggle::make('resuelta')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bajas_ventas' => 'warning',
                        'exceso_stock' => 'info',
                        'producto_sin_ventas' => 'danger',
                        'stock_bajo_critico' => 'danger',
                        'tendencia_negativa_ventas' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('gravedad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baja' => 'gray',
                        'media' => 'warning',
                        'alta' => 'danger',
                        'critica' => 'red',
                    }),
                Tables\Columns\TextColumn::make('producto.nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('resuelta')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'bajas_ventas' => 'Bajas Ventas',
                        'exceso_stock' => 'Exceso Stock',
                        'producto_sin_ventas' => 'Producto Sin Ventas',
                        'stock_bajo_critico' => 'Stock Bajo Crítico',
                        'tendencia_negativa_ventas' => 'Tendencia Negativa',
                    ]),
                Tables\Filters\SelectFilter::make('gravedad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'critica' => 'Crítica',
                    ]),
                Tables\Filters\Filter::make('solo_pendientes')
                    ->label('Solo alertas pendientes')
                    ->query(fn (Builder $query): Builder => $query->where('resuelta', false)),
            ])
            ->actions([
                Tables\Actions\Action::make('marcar_resuelta')
                    ->label('Resolver')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (AlertaSistema $record) {
                        $record->update([
                            'resuelta' => true,
                            'fecha_resolucion' => now(),
                        ]);
                    })
                    ->hidden(fn (AlertaSistema $record) => $record->resuelta),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('marcar_resueltas')
                        ->label('Marcar como resueltas')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'resuelta' => true,
                                    'fecha_resolucion' => now(),
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlertaSistemas::route('/'),
            'create' => Pages\CreateAlertaSistema::route('/create'),
            'edit' => Pages\EditAlertaSistema::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('resuelta', false)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }
}
