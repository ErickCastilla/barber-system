<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

/**
 * Controlador de Citas para el Administrador
 * Permite una visión global de todas las citas agendadas en la barbería.
 */
class AppointmentController extends Controller
{
    /**
     * Muestra el panorama general de las citas.
     */
    public function index()
    {
        // Obtenemos todas las citas del sistema ordenadas por fecha
        // Cargamos a los clientes, barberos y servicios
        $appointments = Appointment::with(['client', 'barber', 'service'])
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('admin.appointments.index', compact('appointments'));
    }
}
