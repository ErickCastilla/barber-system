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
