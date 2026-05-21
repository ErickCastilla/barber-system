<?php

namespace App\Mail;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable encargado de enviar el reporte diario al barbero.
 */
class BarberDailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $barber;
    public Collection $appointments;

    /**
     * Constructor del correo.
     * Recibe al barbero y la colección de sus citas de hoy.
     */
    public function __construct(User $barber, Collection $appointments)
    {
        $this->barber = $barber;
        $this->appointments = $appointments;
    }

    /**
     * Define el sobre (envelope) con el asunto del correo.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu Reporte de Citas para Hoy - Barber System',
        );
    }

    /**
     * Define el contenido visual (cuerpo) del correo.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.barber_daily_report',
        );
    }

    /**
     * Genera el PDF en memoria y lo adjunta al correo.
     */
    public function attachments(): array
    {
        // Generamos el PDF usando DomPDF con la vista correspondiente
        $pdf = Pdf::loadView('emails.pdf.barber_daily_report', [
            'barber' => $this->barber,
            'appointments' => $this->appointments,
        ]);

        return [
            // Adjuntamos desde memoria
            Attachment::fromData(fn () => $pdf->output(), 'mis_citas_hoy.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
