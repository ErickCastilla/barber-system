<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador ScheduleController (para el Barbero)
 * Gestiona los horarios de disponibilidad del barbero en bloques de 1 hora.
 */
class ScheduleController extends Controller
{
    /**
     * Muestra la cuadrícula de horarios.
     */
    public function index()
    {
        $barber = Auth::user();

        // Obtenemos todos los horarios de este barbero
        // Ahora un barbero puede tener múltiples registros para el mismo día (múltiples bloques)
        $schedules = Schedule::where('user_id', $barber->id)->get();

        // Creamos un array tridimensional para saber fácilmente en la vista si una hora está marcada.
        // Estructura: $activeSlots[dia][hora_inicio] = true
        $activeSlots = [];
        foreach ($schedules as $schedule) {
            // Ejemplo: start_time viene como "08:00:00", lo cortamos a "08:00"
            $time = substr($schedule->start_time, 0, 5);
            $activeSlots[$schedule->day_of_week][$time] = true;
        }

        // Definimos los días de la semana (Lunes a Domingo)
        $days = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            0 => 'Domingo'
        ];

        // Definimos los bloques de horas (de 08:00 a 19:00, asumiendo cortes de 1 hora)
        $timeSlots = [];
        for ($i = 8; $i < 20; $i++) {
            $start = sprintf('%02d:00', $i);
            $end = sprintf('%02d:00', $i + 1);
            $timeSlots[] = [
                'start' => $start,
                'end' => $end,
                'label' => "$start - $end"
            ];
        }

        return view('barber.schedules.index', compact('activeSlots', 'days', 'timeSlots'));
    }

    /**
     * Guarda los bloques de horas seleccionados en la cuadrícula.
     */
    public function store(Request $request)
    {
        $barber = Auth::user();
        
        // El formulario envía un arreglo 'schedules' donde las llaves son "dia_horainicio".
        // Ejemplo: si marcó Lunes de 8 a 9, llega schedules["1_08:00"] = "on"
        $selectedSlots = $request->input('schedules', []);

        // 1. Eliminamos todos los horarios actuales de este barbero para "limpiar la pizarra"
        Schedule::where('user_id', $barber->id)->delete();

        // 2. Iteramos sobre cada casilla que el usuario marcó
        foreach ($selectedSlots as $key => $value) {
            // Separamos la llave "1_08:00" en día (1) y hora de inicio (08:00)
            list($day, $startTime) = explode('_', $key);

            // Calculamos la hora de fin sumando 1 hora a la hora de inicio
            $endTime = sprintf('%02d:00', intval(substr($startTime, 0, 2)) + 1);

            // Creamos el nuevo bloque en la base de datos
            Schedule::create([
                'user_id' => $barber->id,
                'day_of_week' => $day,
                'start_time' => $startTime . ':00', // Formato de base de datos H:i:s
                'end_time' => $endTime . ':00',
            ]);
        }

        return redirect()->route('barber.schedules.index')->with('success', 'Horarios (bloques de tiempo) guardados correctamente.');
    }
}
