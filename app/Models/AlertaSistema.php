<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaSistema extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'titulo',
        'descripcion',
        'gravedad',
        'producto_id',
        'datos',
        'resuelta',
        'fecha_resolucion'
    ];

    protected $casts = [
        'datos' => 'array',
        'fecha_resolucion' => 'datetime',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Scope para alertas no resueltas
    public function scopePendientes($query)
    {
        return $query->where('resuelta', false);
    }

    // Scope por tipo de alerta
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope por gravedad
    public function scopeGravedad($query, $gravedad)
    {
        return $query->where('gravedad', $gravedad);
    }
}