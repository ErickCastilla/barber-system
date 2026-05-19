<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Controlador de Citas para la Recepcionista (Fase 4)
 * Permite gestionar la agenda del día (marcar como completada o cancelada).
 */
class AppointmentController extends Controller
{
    /**
     * Muestra la agenda general (por defecto las de hoy).
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $filterDate = $request->input('date', $today);

        // Obtenemos todas las citas de la fecha seleccionada
        $appointments = Appointment::with(['client', 'barber', 'service'])
            ->whereDate('appointment_date', $filterDate)
            ->orderBy('start_time', 'asc')
            ->get();

        return view('receptionist.appointments.index', compact('appointments', 'filterDate'));
    }

    /**
     * Marca una cita como completada (el cliente pagó y se cortó).
     */
    public function complete(Appointment $appointment)
    {
        // Solo podemos completar citas que estén pendientes o confirmadas
        if (in_array($appointment->status, ['pendiente', 'confirmada'])) {
            $appointment->update(['status' => 'completada']);
            return redirect()->back()->with('success', 'Cita marcada como completada.');
        }

        return redirect()->back()->with('error', 'No se puede cambiar el estado de esta cita.');
    }

    /**
     * Cancela una cita (por inasistencia del cliente o error).
     */
    public function cancel(Appointment $appointment)
    {
        // Solo podemos cancelar citas pendientes o confirmadas
        if (in_array($appointment->status, ['pendiente', 'confirmada'])) {
            $appointment->update(['status' => 'cancelada']);
            return redirect()->back()->with('success', 'Cita marcada como cancelada.');
        }

        return redirect()->back()->with('error', 'No se puede cancelar esta cita.');
    }
}
