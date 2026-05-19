<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta migración crea la tabla appointments (citas), que es el núcleo de la Fase 3.
     * Conecta al cliente, al barbero, el servicio solicitado y registra la hora de la cita.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            
            // Relación con el cliente que hace la reservación
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            
            // Relación con el barbero seleccionado
            $table->foreignId('barber_id')->constrained('users')->onDelete('cascade');
            
            // Relación con el servicio solicitado
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            
            // Fecha y horarios de la cita
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time'); // Calculado en base a la duración del servicio
            
            // Estado de la cita (ej. pendiente, confirmada, completada, cancelada)
            $table->string('status')->default('pendiente');
            
            // Notas opcionales que el cliente pueda dejar
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
