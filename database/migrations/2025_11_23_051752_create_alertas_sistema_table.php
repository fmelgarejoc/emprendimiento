<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alerta_sistemas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // 'bajas_ventas', 'exceso_stock', 'stock_bajo', 'producto_sin_ventas'
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('gravedad'); // 'baja', 'media', 'alta', 'critica'
            $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('cascade');
            $table->json('datos')->nullable(); // Datos adicionales en JSON
            $table->boolean('resuelta')->default(false);
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerta_sistemas');
    }
};
