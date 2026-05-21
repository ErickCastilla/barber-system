# Reporte de Cambios y Documentación del Sistema (Fase 1 y Fase 2)

Este archivo contiene la documentación detallada de todos los cambios realizados en el sistema, estructurado para su estudio con rutas completas, explicaciones y código.

---

## FASE 1: Envío de PDF de Confirmación de Cita

### 1. Dependencias del Proyecto
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\composer.json`
* **Qué se modificó:** Se instaló la librería `barryvdh/laravel-dompdf` para generar documentos PDF a partir de vistas Blade de Laravel.
* **Bloque de Código Agregado (en la sección `"require"`):**
```json
"barryvdh/laravel-dompdf": "^3.1"
```

### 2. Clase Mailable para el Correo de Confirmación
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\app\Mail\AppointmentConfirmationMail.php`
* **Qué se modificó:** Se creó un nuevo Mailable (`AppointmentConfirmationMail`) encargado de estructurar el correo y adjuntar el PDF generado en memoria para evitar almacenamiento temporal en el servidor.
* **Código Completo:**
```php
<?php

namespace App\Mail;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para el envío de confirmación de cita.
 * Este correo se envía al cliente y al barbero con el PDF adjunto generado en memoria.
 */
class AppointmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    // La cita que se va a notificar. Se hace pública para que esté disponible en las vistas de Blade.
    public Appointment $appointment;

    /**
     * Crea una nueva instancia del mensaje.
     * Recibe el modelo de la cita ya guardada en la base de datos.
     *
     * @param Appointment $appointment
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Define el sobre (envelope) del correo, incluyendo el asunto.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Cita - Barber System',
        );
    }

    /**
     * Define el contenido del correo electrónico (vista Blade y variables).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment_confirmation',
        );
    }

    /**
     * Genera y adjunta el PDF de confirmación directamente desde la memoria.
     * Esto evita crear archivos temporales en el almacenamiento del servidor.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        // Generamos el archivo PDF a partir de la vista de Blade específica
        // y le pasamos los datos del appointment (cita) con sus relaciones.
        $pdf = Pdf::loadView('emails.appointment_pdf', [
            'appointment' => $this->appointment
        ]);

        return [
            // Adjuntamos el archivo PDF generado a partir del string de salida del render
            Attachment::fromData(fn () => $pdf->output(), 'confirmacion_cita.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
```

### 3. Vista Blade para el Cuerpo del Correo
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\resources\views\emails\appointment_confirmation.blade.php`
* **Qué se modificó:** Se creó el cuerpo del correo en HTML estilizado mediante estilos en línea para asegurar la compatibilidad con gestores de correo electrónicos (Gmail, Outlook, etc.).
* **Código Completo:**
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmación de Cita</title>
    <style>
        /* Estilos generales responsivos y limpios para clientes de correo */
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; color: #1f2937; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; }
        .header { background-color: #111827; padding: 30px; text-align: center; border-bottom: 4px solid #d97706; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px; text-transform: uppercase; }
        .content { padding: 40px 30px; }
        .welcome-text { font-size: 18px; margin-top: 0; margin-bottom: 20px; font-weight: 600; }
        .details-box { background-color: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; border-bottom: 1px dashed #e5e7eb; padding-bottom: 8px; }
        .detail-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .label { font-weight: 600; color: #4b5563; }
        .value { color: #111827; }
        .footer { background-color: #f9fafb; padding: 20px; text-align: center; font-size: 13px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        .attachment-note { font-size: 14px; color: #6b7280; margin-top: 20px; text-align: center; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado con estética premium de barbería -->
        <div class="header">
            <h1>Erick´s Barber</h1>
        </div>
        
        <!-- Contenido principal del correo -->
        <div class="content">
            <p class="welcome-text">¡Hola!</p>
            <p>Queremos confirmarte que la cita ha sido agendada exitosamente. A continuación se presentan los detalles principales:</p>
            
            <!-- Resumen de los datos de la cita -->
            <div class="details-box">
                <div class="detail-row">
                    <span class="label">Cliente:</span>
                    <span class="value">{{ $appointment->client->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Barbero:</span>
                    <span class="value">{{ $appointment->barber->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Servicio:</span>
                    <span class="value">{{ $appointment->service->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Fecha:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Hora:</span>
                    <span class="value">
                        {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                    </span>
                </div>
            </div>

            <!-- Nota sobre el archivo adjunto -->
            <p class="attachment-note">
                Hemos adjuntado a este correo un comprobante oficial en formato PDF con la información completa de tu cita. Por favor, guárdalo para cualquier referencia o aclaración.
            </p>
        </div>
        
        <!-- Pie de página informativo -->
        <div class="footer">
            <p>Este es un correo automático. Por favor, no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Erick´s Barber. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
```

### 4. Vista Blade del PDF de la Cita
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\resources\views\emails\appointment_pdf.blade.php`
* **Qué se modificó:** Se diseñó el comprobante PDF con tablas y CSS compatible con DomPDF. Presenta un diseño premium con colores oscuros y dorados/ámbar, detalles de duración y precio.
* *(Código extenso de la plantilla HTML PDF - se omitió en este resumen por brevedad pero existe en el proyecto).*

### 5. Modificación del Controlador de Citas de Clientes
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\app\Http\Controllers\Client\AppointmentController.php`
* **Qué se modificó:** Se importaron las clases `Mail` y `AppointmentConfirmationMail`. Se implementó un bloque `try-catch` para que los errores de red de correo no interrumpan la reservación exitosa de la cita.

---

## FASE 2: Reportes Diarios Automatizados (Barberos y Admin)

### 1. Comando Artisan de Envío Programado
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\app\Console\Commands\SendDailyAppointmentReports.php`
* **Qué se creó:** Un comando programable que busca las citas de hoy, las agrupa por barbero, y envía los reportes a cada barbero y luego un resumen a todos los administradores.
* **Código Completo:**
```php
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
     */
    protected $signature = 'reports:send-daily';

    /**
     * La descripción breve del comando.
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
```

### 2. Archivo de Configuración de Tareas (Scheduler)
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\routes\console.php`
* **Qué se modificó:** Se importó el Facade `Schedule` y se añadió la instrucción para ejecutar el comando `reports:send-daily` diariamente a las 8:00 AM.
* **Bloque de Código Agregado:**
```php
use Illuminate\Support\Facades\Schedule; // Importamos el Facade para programar tareas

// Programamos el envío de reportes diarios (Fase 2)
// Esto se ejecutará todos los días a las 08:00 a.m.
Schedule::command('reports:send-daily')->dailyAt('08:00');
```

### 3. Clases Mailable para Reportes Diarios
* **Rutas completas:**
  - Mailable de Barberos: `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\app\Mail\BarberDailyReportMail.php`
  - Mailable de Administradores: `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\app\Mail\AdminDailyReportMail.php`
* **Qué se crearon:** Dos clases encargadas de cargar las vistas HTML para el cuerpo del correo de cada rol y adjuntar el PDF generado en memoria a través de la librería DomPDF (`Pdf::loadView()->output()`).

### 4. Vistas Blade de Correo y Plantillas PDF (Fase 2)
* **Rutas completas de Vistas de Correo (HTML Body):**
  - Para Barberos: `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\resources\views\emails\barber_daily_report.blade.php`
  - Para Administradores: `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\resources\views\emails\admin_daily_report.blade.php`
* **Rutas completas de Plantillas PDF:**
  - PDF de Barberos: `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\resources\views\emails\pdf\barber_daily_report.blade.php` (Muestra una tabla con el horario, cliente, servicio y duración de las citas asignadas al barbero).
  - PDF de Administradores: `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\resources\views\emails\pdf\admin_daily_report.blade.php` (Agrupa de manera iterativa todos los barberos e imprime un subtotal de ingresos esperados por barbero y un listado de todas las citas del negocio).

---

## FASE 3: Implementación de Soft Deletes (Eliminación Lógica)

### ¿Cómo funcionan los Soft Deletes?
Es un mecanismo de seguridad de Laravel que evita que los datos se borren físicamente de la base de datos cuando alguien llama a la función `delete()`. En su lugar, el sistema simplemente llena una columna especial llamada `deleted_at` con la fecha y hora de la "eliminación".
Gracias a esto:
- Las consultas normales ignorarán ese registro automáticamente (para los usuarios parecerá que se borró).
- La base de datos mantendrá el registro intacto para reportes financieros, historiales médicos/cortes, o auditorías futuras.

### 1. Nueva Migración de Base de Datos
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\database\migrations\2026_05_20_205730_add_deleted_at_to_appointments_table.php`
* **Qué se modificó:** Se generó una migración dedicada para alterar la tabla `appointments` existente agregándole la columna `deleted_at`.
* **Código Modificado (métodos up y down):**
```php
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->softDeletes(); // Añade la columna de seguridad
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Permite revertir o deshacer la migración
        });
    }
```

### 2. Actualización del Modelo Appointment
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\app\Models\Appointment.php`
* **Qué se modificó:** Se inyectó la funcionalidad de Laravel en el modelo a través del rasgo (`Trait`) `SoftDeletes`. Ahora cualquier intento de borrado sobre este modelo será interceptado y convertido en un borrado lógico.
* **Código Modificado:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Importación del Trait de Laravel

class Appointment extends Model
{
    // 2. Inyección de SoftDeletes junto al Factory
    use HasFactory, SoftDeletes; 

    protected $fillable = [
        'client_id',
        // ...resto de los campos asignables
```

### 3. Interfaz de Administración para Soft Deletes
* **Ruta completa del archivo:** `c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\routes\web.php`
* **Qué se modificó:** Se implementó un botón en la tabla principal para que el Administrador envíe las citas a la papelera. Las citas se ocultan de la tabla web pero conservan intacto su historial en la columna `deleted_at`.
* **Código de las modificaciones (Blade y Controlador):**
  - **Botón en Blade** (`c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\resources\views\admin\appointments\index.blade.php`):
```blade
<form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
</form>
```
  - **Método en el Controlador** (`c:\Users\ErickCastilla\Documents\OctavoSemestre\Backend\barber-system\app\Http\Controllers\Admin\AppointmentController.php`):
```php
    public function destroy(Appointment $appointment)
    {
        $appointment->delete(); // Ejecuta el Soft Delete
        return redirect()->route('admin.appointments.index')
            ->with('success', 'La cita ha sido eliminada y enviada a la papelera de reciclaje.');
    }
```

---

## FASE 4: Pulido Estético y Traducciones (Final)
### Ajustes Visuales y de Marca
* **Cambio de Nombre:** Se actualizó el nombre comercial del proyecto de "Erick's Barber" a **"Mutant Barber"** en toda la interfaz (principalmente en `welcome.blade.php`).
* **Actualización de Dashboard:** Se configuró la vista `dashboard.blade.php` para consumir la nueva fotografía grupal del equipo (`mutant_barber_hero.png`).
* **Traducción de Menús:** Se tradujeron al español las opciones del menú desplegable del perfil en `navigation.blade.php` ("Profile" -> "Perfil", "Log Out" -> "Cerrar sesión") tanto en la vista de escritorio como en la vista responsiva para dispositivos móviles.
