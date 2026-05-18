<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta migración modifica la tabla schedules para permitir bloques de tiempo
     * en lugar de solo una hora de entrada y salida por día.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // En MySQL, para eliminar un índice único que está en una columna con llave foránea,
            // a veces es necesario eliminar la foránea, luego el índice, y volver a crear la foránea.
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'day_of_week']);
            
            // Re-agregamos la llave foránea
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Eliminamos la columna is_working
            $table->dropColumn('is_working');
        });
    }

    /**
     * Reverse the migrations.
     * Revierte los cambios si es necesario.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('is_working')->default(true);
            $table->unique(['user_id', 'day_of_week']);
        });
    }
};
