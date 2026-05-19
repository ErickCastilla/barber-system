<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Appointment (Citas)
 * Representa una reservación hecha por un cliente.
 */
class Appointment extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'client_id',
        'barber_id',
        'service_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'notes'
    ];

    /**
     * Relación: Una cita pertenece a un Cliente (User)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Relación: Una cita pertenece a un Barbero (User)
     */
    public function barber()
    {
        return $this->belongsTo(User::class, 'barber_id');
    }

    /**
     * Relación: Una cita incluye un Servicio
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
