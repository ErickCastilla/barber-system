<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Schedule (Horario)
 * Representa la disponibilidad de un barbero para un día específico de la semana.
 */
class Schedule extends Model
{
    use HasFactory;

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'user_id',      // Relación con el Barbero
        'day_of_week',  // Día de la semana (0=Domingo, 1=Lunes, etc.)
        'start_time',   // Hora en que inicia su turno
        'end_time',     // Hora en que termina su turno
        'is_working',   // Booleano: true = trabaja, false = descansa
    ];

    /**
     * Relación: Un horario pertenece a un Usuario (Barbero).
     */
    public function barber()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
