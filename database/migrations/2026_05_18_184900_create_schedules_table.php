<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de horarios de los barberos.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            // Relación con la tabla users (quién es el barbero)
            // onDelete('cascade') asegura que si el barbero es eliminado, sus horarios también.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // El día de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
            $table->integer('day_of_week');
            
            // Horas de inicio y fin (nullable porque si no trabaja ese día, puede ir vacío)
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // Booleano para saber si atiende o no este día (true = si, false = descanso)
            $table->boolean('is_working')->default(true);

            // Asegurar que un barbero no tenga dos horarios para el mismo día
            $table->unique(['user_id', 'day_of_week']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
