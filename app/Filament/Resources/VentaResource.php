<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Venta')
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->relationship('cliente', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nombre')->required(),
                                Forms\Components\TextInput::make('nit')->label('NIT'),
                                Forms\Components\TextInput::make('celular'),
                                Forms\Components\TextInput::make('email')->email(),
                                Forms\Components\Textarea::make('direccion')->columnSpanFull(),
                            ]),
                        Forms\Components\DatePicker::make('fecha_venta')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'completada' => 'Completada',
                                'cancelada' => 'Cancelada',
                            ])
                            ->default('completada')
                            ->required(),
                        Forms\Components\Textarea::make('observaciones')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detalles de la Venta')
    ->schema([

        Forms\Components\Repeater::make('detalles')
            ->relationship('detalles')
            ->schema([

                Forms\Components\Select::make('producto_id')
                    ->label('Producto')
                    ->options(Producto::pluck('nombre', 'id'))
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $producto = Producto::find($state);

                        if ($producto) {
                            $set('precio_unitario', $producto->precio_venta);
                            self::actualizarTotalesVenta($get, $set);
                        }
                    }),

                Forms\Components\TextInput::make('cantidad')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        self::actualizarTotalesVenta($get, $set);
                    }),

                Forms\Components\TextInput::make('precio_unitario')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        self::actualizarTotalesVenta($get, $set);
                    }),

            ])
            ->columns(3)
            ->defaultItems(1)
            ->createItemButtonLabel('Agregar Producto')
            ->deleteAction(fn ($action) =>
                $action->after(function (callable $get, callable $set) {
                    self::actualizarTotalesVenta($get, $set);
                })
            )
            ->afterStateUpdated(function (callable $get, callable $set) {
                self::actualizarTotalesVenta($get, $set);
            })
            ->columnSpanFull(),

        // BOTÓN CALCULAR (CORRECTO)
        Forms\Components\Actions::make([
            Forms\Components\Actions\Action::make('calcular')
                ->label('Calcular Totales')
                ->icon('heroicon-o-calculator')
                ->color('success')
                ->action(function (callable $get, callable $set) {
                    self::actualizarTotalesVenta($get, $set);
                }),
        ])->columnSpanFull(),

    ]),


                Forms\Components\Section::make('Resumen de la Venta')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal General')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('$')
                            ->dehydrated(true),
                        Forms\Components\TextInput::make('impuesto')
                            ->label('Impuesto (%)')
                            ->numeric()
                            ->default(12)
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                self::actualizarTotalesVenta($get, $set);
                            })
                            ->suffix('%'),
                        Forms\Components\TextInput::make('total_impuesto')
                            ->label('Total Impuesto')
                            ->numeric()
                            ->default(0)
                            ->prefix('$')
                            ->dehydrated(true),
                        Forms\Components\TextInput::make('total')
                            ->label('Total General')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('$')
                            ->dehydrated(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function actualizarTotalesVenta(callable $get, callable $set): void
    {
        $detalles = $get('detalles') ?? [];
        $subtotalTotal = 0;

        foreach ($detalles as $item) {
            $cantidad = floatval($item['cantidad'] ?? 0);
            $precio = floatval($item['precio_unitario'] ?? 0);
            $subtotalTotal += ($cantidad * $precio);
        }

        $porcentajeImpuesto = floatval($get('impuesto') ?? 12);
        $totalImpuesto = $subtotalTotal * ($porcentajeImpuesto / 100);
        $totalGeneral = $subtotalTotal + $totalImpuesto;

        $set('subtotal', number_format($subtotalTotal, 2, '.', ''));
        $set('total_impuesto', number_format($totalImpuesto, 2, '.', ''));
        $set('total', number_format($totalGeneral, 2, '.', ''));
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_factura')
                    ->searchable()
                    ->sortable()
                    ->label('Factura'),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_venta')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'completada' => 'success',
                        'cancelada' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('detalles_count')
                    ->counts('detalles')
                    ->label('Items')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}

    