<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\AlertaSistema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeteccionDesviacionesService
{
    public function ejecutarDeteccion()
    {
        $this->detectarBajasVentas();
        $this->detectarExcesoStock();
        $this->detectarProductosSinVentas();
        $this->detectarStockBajoCritico();
        $this->detectarTendenciasNegativas();
    }

    /**
     * Detectar productos con bajas ventas en los últimos 30 días
     */
    public function detectarBajasVentas()
    {
        $fechaInicio = Carbon::now()->subDays(30);
        
        $productosBajasVentas = VentaDetalle::select(
                'producto_id',
                DB::raw('SUM(cantidad) as total_vendido'),
                DB::raw('COUNT(DISTINCT venta_id) as numero_ventas')
            )
            ->whereHas('venta', function ($query) use ($fechaInicio) {
                $query->where('fecha_venta', '>=', $fechaInicio)
                      ->where('estado', 'completada');
            })
            ->groupBy('producto_id')
            ->having('total_vendido', '<', 5) // Menos de 5 unidades vendidas
            ->having('numero_ventas', '<', 2) // Menos de 2 ventas diferentes
            ->get();

        foreach ($productosBajasVentas as $productoVenta) {
            $producto = Producto::find($productoVenta->producto_id);
            
            if ($producto) {
                $this->crearAlerta(
                    'bajas_ventas',
                    "Bajas ventas: {$producto->nombre}",
                    "El producto {$producto->nombre} ha vendido solo {$productoVenta->total_vendido} unidades en los últimos 30 días.",
                    'media',
                    $producto->id,
                    [
                        'total_vendido' => $productoVenta->total_vendido,
                        'numero_ventas' => $productoVenta->numero_ventas,
                        'periodo_dias' => 30
                    ]
                );
            }
        }
    }

    /**
     * Detectar exceso de stock (stock > 3 veces el promedio de ventas mensual)
     */
    public function detectarExcesoStock()
    {
        $productos = Producto::all();

        foreach ($productos as $producto) {
            $ventasPromedioMensual = $this->calcularVentasPromedioMensual($producto->id);
            
            if ($ventasPromedioMensual > 0) {
                $ratioStock = $producto->stock / $ventasPromedioMensual;
                
                if ($ratioStock > 3) { // Stock es más de 3 veces el promedio mensual
                    $this->crearAlerta(
                        'exceso_stock',
                        "Exceso de stock: {$producto->nombre}",
                        "El producto {$producto->nombre} tiene {$producto->stock} unidades en stock, pero el promedio de ventas mensual es de {$ventasPromedioMensual} unidades.",
                        'media',
                        $producto->id,
                        [
                            'stock_actual' => $producto->stock,
                            'ventas_promedio_mensual' => $ventasPromedioMensual,
                            'ratio_stock' => round($ratioStock, 2)
                        ]
                    );
                }
            }
        }
    }

    /**
     * Detectar productos sin ventas en los últimos 60 días
     */
    public function detectarProductosSinVentas()
    {
        $fechaInicio = Carbon::now()->subDays(60);
        
        $productosConVentas = VentaDetalle::whereHas('venta', function ($query) use ($fechaInicio) {
                $query->where('fecha_venta', '>=', $fechaInicio)
                      ->where('estado', 'completada');
            })
            ->pluck('producto_id')
            ->unique();

        $productosSinVentas = Producto::whereNotIn('id', $productosConVentas)->get();

        foreach ($productosSinVentas as $producto) {
            $this->crearAlerta(
                'producto_sin_ventas',
                "Producto sin ventas: {$producto->nombre}",
                "El producto {$producto->nombre} no ha tenido ventas en los últimos 60 días.",
                'alta',
                $producto->id,
                [
                    'dias_sin_ventas' => 60,
                    'stock_actual' => $producto->stock
                ]
            );
        }
    }

    /**
     * Detectar stock críticamente bajo
     */
    public function detectarStockBajoCritico()
    {
        $productosStockBajo = Producto::where('stock', '<', DB::raw('stock_minimo'))->get();

        foreach ($productosStockBajo as $producto) {
            $gravedad = $producto->stock == 0 ? 'critica' : 'alta';
            
            $this->crearAlerta(
                'stock_bajo_critico',
                "Stock crítico: {$producto->nombre}",
                "El producto {$producto->nombre} tiene solo {$producto->stock} unidades (mínimo: {$producto->stock_minimo}).",
                $gravedad,
                $producto->id,
                [
                    'stock_actual' => $producto->stock,
                    'stock_minimo' => $producto->stock_minimo
                ]
            );
        }
    }

    /**
     * Detectar tendencias negativas en ventas
     */
    public function detectarTendenciasNegativas()
    {
        $fechaFin = Carbon::now();
        $fechaInicioActual = $fechaFin->copy()->subDays(30);
        $fechaInicioAnterior = $fechaInicioActual->copy()->subDays(30);

        // Ventas del período actual
        $ventasActual = Venta::whereBetween('fecha_venta', [$fechaInicioActual, $fechaFin])
            ->where('estado', 'completada')
            ->count();

        // Ventas del período anterior
        $ventasAnterior = Venta::whereBetween('fecha_venta', [$fechaInicioAnterior, $fechaInicioActual])
            ->where('estado', 'completada')
            ->count();

        if ($ventasAnterior > 0) {
            $diferenciaPorcentual = (($ventasActual - $ventasAnterior) / $ventasAnterior) * 100;

            if ($diferenciaPorcentual < -20) { // Disminución mayor al 20%
                $this->crearAlerta(
                    'tendencia_negativa_ventas',
                    "Tendencia negativa en ventas",
                    "Las ventas han disminuido un " . abs(round($diferenciaPorcentual, 1)) . "% respecto al período anterior.",
                    'alta',
                    null,
                    [
                        'ventas_actual' => $ventasActual,
                        'ventas_anterior' => $ventasAnterior,
                        'diferencia_porcentual' => round($diferenciaPorcentual, 1),
                        'periodo_dias' => 30
                    ]
                );
            }
        }
    }

    /**
     * Calcular ventas promedio mensual de un producto
     */
    private function calcularVentasPromedioMensual($productoId)
    {
        $fechaInicio = Carbon::now()->subDays(90); // Últimos 90 días
        
        $totalVendido = VentaDetalle::where('producto_id', $productoId)
            ->whereHas('venta', function ($query) use ($fechaInicio) {
                $query->where('fecha_venta', '>=', $fechaInicio)
                      ->where('estado', 'completada');
            })
            ->sum('cantidad');

        return $totalVendido / 3; // Promedio mensual (90 días / 3 meses)
    }

    /**
     * Crear alerta en la base de datos
     */
    private function crearAlerta($tipo, $titulo, $descripcion, $gravedad, $productoId = null, $datos = null)
    {
        // Verificar si ya existe una alerta similar no resuelta
        $alertaExistente = AlertaSistema::where('tipo', $tipo)
            ->where('producto_id', $productoId)
            ->where('resuelta', false)
            ->first();

        if (!$alertaExistente) {
            AlertaSistema::create([
                'tipo' => $tipo,
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'gravedad' => $gravedad,
                'producto_id' => $productoId,
                'datos' => $datos,
            ]);
        }
    }
}