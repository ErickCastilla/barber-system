<?php

namespace App\Mail;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Mailable encargado de enviar el reporte resumen general a los administradores.
 */
class AdminDailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $admin;
    // Las citas agrupadas por el ID del barbero
    public Collection $appointmentsGrouped;

    /**
     * Constructor del correo.
     */
    public function __construct(User $admin, Collection $appointmentsGrouped)
    {
        $this->admin = $admin;
        $this->appointmentsGrouped = $appointmentsGrouped;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Resumen General de Citas de Hoy - Barber System',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_daily_report',
        );
    }

    public function attachments(): array
    {
        // Generamos el PDF con todas las citas agrupadas
        $pdf = Pdf::loadView('emails.pdf.admin_daily_report', [
            'admin' => $this->admin,
            'appointmentsGrouped' => $this->appointmentsGrouped,
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'resumen_general_citas.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
