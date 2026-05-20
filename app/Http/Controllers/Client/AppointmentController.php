<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // Para el envío de correos en Laravel
use App\Mail\AppointmentConfirmationMail; // Mailable personalizado de confirmación

/**
 * Controlador de Citas para el Cliente
 * Permite al cliente agendar, buscar horarios disponibles y ver sus próximas citas.
 */
class AppointmentController extends Controller
{
    /**
     * Muestra las citas agendadas hacia el futuro.
     */
    public function index()
    {
        $client = Auth::user();
        $today = Carbon::today()->toDateString();
        
        // Obtenemos solo las citas de hoy en adelante, ordenadas por fecha y hora
        $appointments = Appointment::with(['barber', 'service'])
            ->where('client_id', $client->id)
            ->whereDate('appointment_date', '>=', $today)
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('client.appointments.index', compact('appointments'));
    }

    /**
     * Muestra el formulario para agendar una nueva cita.
     */
    public function create()
    {
        // Obtenemos todos los barberos (usuarios con rol 'barbero')
        $barbers = User::role('barbero')->get();
        
        // Obtenemos todos los servicios disponibles
        $services = Service::orderBy('name')->get();

        return view('client.appointments.create', compact('barbers', 'services'));
    }

    /**
     * Endpoint API para calcular las horas disponibles.
     * Recibe: barber_id, service_id, date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $barberId = $request->barber_id;
        $service = Service::find($request->service_id);
        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek; // 0 (Domingo) a 6 (Sábado)

        // 1. Obtener los bloques de trabajo del barbero para ese día
        $schedules = Schedule::where('user_id', $barberId)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return response()->json(['slots' => []]); // El barbero no trabaja ese día
        }

        // 2. Obtener las citas que ya tiene el barbero en esa fecha
        $existingAppointments = Appointment::where('barber_id', $barberId)
            ->whereDate('appointment_date', $date->toDateString())
            ->whereIn('status', ['pendiente', 'confirmada']) // Ignorar canceladas
            ->get();

        $availableSlots = [];
        $serviceDuration = $service->duration_minutes;

        // Por simplicidad, iteraremos sobre cada bloque de 1 hora del barbero
        // y verificaremos si el servicio "cabe" y no choca con otra cita.
        foreach ($schedules as $schedule) {
            $slotStart = Carbon::parse($date->toDateString() . ' ' . $schedule->start_time);
            $slotEnd = $slotStart->copy()->addMinutes($serviceDuration);

            // Verificamos si este bloque propuesto choca con el fin de su horario de trabajo
            $scheduleEnd = Carbon::parse($date->toDateString() . ' ' . $schedule->end_time);
            if ($slotEnd->gt($scheduleEnd)) {
                // Si el servicio dura 2 horas pero el bloque es de 1 hora y el barbero sale,
                // no podemos agendar aquí si no tiene el siguiente bloque continuo.
                // *Nota: Para simplificar la Fase 3, asumimos bloques independientes. 
                // Pero como es un sistema robusto, debemos verificar la continuidad.*
                
                // Si la hora de fin sobrepasa la hora de salida de este bloque, verificamos
                // si el barbero también trabaja en el siguiente bloque.
                $hasNextBlock = Schedule::where('user_id', $barberId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('start_time', $schedule->end_time)
                    ->exists();

                if (!$hasNextBlock) {
                    continue; // No tiene tiempo suficiente
                }
            }

            // 3. Verificar choque con citas existentes
            $conflict = false;
            foreach ($existingAppointments as $appt) {
                $apptStart = Carbon::parse($date->toDateString() . ' ' . $appt->start_time);
                $apptEnd = Carbon::parse($date->toDateString() . ' ' . $appt->end_time);

                // Lógica de traslape: (StartA < EndB) y (EndA > StartB)
                if ($slotStart->lt($apptEnd) && $slotEnd->gt($apptStart)) {
                    $conflict = true;
                    break;
                }
            }

            if (!$conflict) {
                // Formateamos la hora para enviarla a la vista (Ej: "08:00")
                $availableSlots[] = $slotStart->format('H:i');
            }
        }

        // Si la fecha elegida es hoy, filtramos las horas que ya pasaron
        if ($date->isToday()) {
            $now = Carbon::now();
            $availableSlots = array_filter($availableSlots, function($time) use ($now) {
                return Carbon::parse($time)->gt($now);
            });
        }

        // Reindexar el array después del filtro
        return response()->json(['slots' => array_values($availableSlots)]);
    }

    /**
     * Guarda la cita en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ]);

        $service = Service::find($request->service_id);
        
        // Calculamos a qué hora terminará el servicio
        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        // Opcional: Podríamos re-verificar colisiones en el backend antes de guardar
        // por si dos personas dieron clic al mismo tiempo.

        // 1. Guardamos la cita creada en una variable local
        $appointment = Appointment::create([
            'client_id' => Auth::id(),
            'barber_id' => $request->barber_id,
            'service_id' => $request->service_id,
            'appointment_date' => $request->appointment_date,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'status' => 'pendiente', // Estado inicial
        ]);

        // 2. Cargamos las relaciones para poder acceder a los datos completos en el correo y PDF
        $appointment->load(['client', 'barber', 'service']);

        // 3. Enviamos el correo de confirmación a ambos participantes dentro de un bloque try-catch
        // Esto previene que si hay un error en el servidor de correos, la reserva de la cita no falle.
        try {
            // Enviamos el correo con el PDF adjunto al cliente que agendó
            Mail::to($appointment->client->email)->send(new AppointmentConfirmationMail($appointment));
            
            // Enviamos el mismo correo con el PDF adjunto al barbero asignado
            Mail::to($appointment->barber->email)->send(new AppointmentConfirmationMail($appointment));
        } catch (\Exception $e) {
            // Registramos el error en los logs del sistema para auditoría y depuración posterior
            \Illuminate\Support\Facades\Log::error('Error enviando correos de confirmación de cita: ' . $e->getMessage());
        }

        return redirect()->route('client.appointments.index')
            ->with('success', '¡Tu cita ha sido agendada con éxito!');
    }

    /**
     * Cancela una cita, validando que falte al menos 1 hora para su inicio.
     */
    public function cancel(Appointment $appointment)
    {
        // Validamos que la cita pertenezca al usuario logueado
        if ($appointment->client_id !== Auth::id()) {
            abort(403, 'Acceso denegado.');
        }

        // Combinamos la fecha y la hora de la cita en un objeto Carbon
        $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->start_time);
        
        // Comparamos con la hora actual
        $now = Carbon::now();
        $diffInMinutes = $now->diffInMinutes($appointmentDateTime, false); // false para que de negativo si ya pasó

        // Si faltan menos de 60 minutos (1 hora) o ya pasó, no se puede cancelar por sistema
        if ($diffInMinutes < 60) {
            return redirect()->route('client.appointments.index')
                ->with('error', 'No puedes cancelar con menos de 1 hora de antelación. Por favor, comunícate directamente con la barbería.');
        }

        // Actualizamos el estado de la cita en lugar de borrarla de la base de datos
        // Esto permite mantener un historial de cancelaciones
        $appointment->update(['status' => 'cancelada']);

        return redirect()->route('client.appointments.index')
            ->with('success', 'Tu cita ha sido cancelada exitosamente.');
    }
}
