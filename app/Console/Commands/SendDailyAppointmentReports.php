<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BarberDailyReportMail;
use App\Mail\AdminDailyReportMail;

class SendDailyAppointmentReports extends Command
{
    /**
     * El nombre y la firma del comando en la consola (cómo se llamará desde la terminal).
     *
     * @var string
     */
    protected $signature = 'reports:send-daily';

    /**
     * La descripción breve del comando.
     *
     * @var string
     */
    protected $description = 'Envía el reporte diario de citas a los barberos y el resumen general a los administradores.';

    /**
     * Lógica de ejecución del comando.
     */
    public function handle()
    {
        // 1. Obtenemos la fecha de hoy
        $today = Carbon::today()->toDateString();
        $this->info("Iniciando envío de reportes diarios para la fecha: {$today}");

        // 2. Obtenemos TODAS las citas programadas para hoy, cargando sus relaciones.
        // Solo incluimos citas pendientes o confirmadas.
        $appointments = Appointment::with(['client', 'barber', 'service'])
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->orderBy('start_time', 'asc')
            ->get();

        // Si no hay citas hoy, terminamos el comando y avisamos
        if ($appointments->isEmpty()) {
            $this->info("No hay citas programadas para hoy. No se enviarán correos.");
            return;
        }

        // 3. Agrupamos las citas por el ID del barbero para enviar correos individuales
        $appointmentsByBarber = $appointments->groupBy('barber_id');

        // 4. Enviar correos a cada Barbero
        foreach ($appointmentsByBarber as $barberId => $barberAppointments) {
            // Buscamos la información del barbero
            $barber = User::find($barberId);

            if ($barber && $barber->email) {
                try {
                    // Enviamos el Mailable enviando al barbero y a sus respectivas citas
                    Mail::to($barber->email)->send(new BarberDailyReportMail($barber, $barberAppointments));
                    $this->info("Reporte enviado al barbero: {$barber->name}");
                } catch (\Exception $e) {
                    // Registramos si hubo error al enviar el correo
                    Log::error("Fallo al enviar reporte al barbero {$barber->name}: " . $e->getMessage());
                    $this->error("Error enviando a {$barber->name}");
                }
            }
        }

        // 5. Enviar correo resumen a todos los Administradores
        // Buscamos usuarios que tengan el rol 'admin' (usando Spatie HasRoles)
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            if ($admin->email) {
                try {
                    // Enviamos el reporte general, pasándole TODAS las citas agrupadas o el listado completo
                    Mail::to($admin->email)->send(new AdminDailyReportMail($admin, $appointmentsByBarber));
                    $this->info("Resumen general enviado al administrador: {$admin->name}");
                } catch (\Exception $e) {
                    Log::error("Fallo al enviar reporte al admin {$admin->name}: " . $e->getMessage());
                    $this->error("Error enviando a administrador {$admin->name}");
                }
            }
        }

        $this->info("Finalizó el envío de reportes diarios con éxito.");
    }
}
