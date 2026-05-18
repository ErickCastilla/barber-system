<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'services' en la base de datos.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id(); // Llave primaria autoincremental
            
            // Campos específicos del servicio
            $table->string('name'); // Nombre del servicio (Ej: Corte Clásico)
            $table->text('description')->nullable(); // Descripción opcional
            $table->integer('duration_minutes'); // Cuánto tiempo dura (Ej: 30)
            $table->decimal('price', 8, 2); // Precio con 2 decimales (Ej: 15.00)
            
            $table->timestamps(); // create_at y updated_at automáticos
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla si revertimos la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
