<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Citas para el Barbero
 * Permite al barbero visualizar su agenda personal.
 */
class AppointmentController extends Controller
{
    /**
     * Muestra la agenda de citas del barbero logueado.
     */
    public function index()
    {
        $barber = Auth::user();
        
        // Obtenemos todas las citas asignadas a este barbero
        // Se cargan las relaciones de cliente y servicio para mostrarlas en la vista
        $appointments = Appointment::with(['client', 'service'])
            ->where('barber_id', $barber->id)
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('barber.appointments.index', compact('appointments'));
    }
}
