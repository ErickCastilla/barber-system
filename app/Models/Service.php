<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Service (Servicio)
 * Representa los diferentes servicios que ofrece la barbería (Ej: Corte, Barba, Cejas).
 */
class Service extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente (Mass Assignment).
     */
    protected $fillable = [
        'name',              // Nombre del servicio
        'description',       // Descripción o detalles del servicio
        'duration_minutes',  // Duración estimada en minutos (vital para la agenda)
        'price',             // Costo del servicio
    ];
}
