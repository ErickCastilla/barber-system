<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de Cita</title>
    <style>
        /* Estilos optimizados específicamente para DomPDF */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .header {
            background-color: #111827; /* Fondo oscuro elegante */
            color: #ffffff;
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #d97706; /* Acento dorado/ámbar */
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 13px;
            color: #9ca3af;
        }
        .title-section {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 25px;
        }
        .title-section h2 {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: #111827;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
            display: inline-block;
            width: 80%;
        }
        /* Tabla de dos columnas para información de los participantes */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .info-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 15px;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #d97706; /* Acento dorado */
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            min-height: 100px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .label {
            font-weight: bold;
            color: #4b5563;
        }
        .value {
            color: #111827;
        }
        /* Bloque destacado para la fecha y hora de la cita */
        .schedule-box {
            background-color: #111827; /* Fondo oscuro */
            color: #ffffff;
            text-align: center;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #d97706; /* Detalle dorado */
        }
        .schedule-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #d97706;
        }
        .schedule-time {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }
        /* Tabla de detalles del servicio contratado */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .details-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 10px 12px;
            border-bottom: 2px solid #e5e7eb;
            font-size: 11px;
            text-transform: uppercase;
        }
        .details-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        .details-table tr:last-child td {
            border-bottom: 2px solid #111827;
        }
        .price-text {
            font-weight: bold;
            color: #111827;
        }
        /* Términos y pie de página */
        .terms {
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            margin-top: 50px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .terms p {
            margin: 4px 0;
        }
        .stamp {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <!-- Encabezado de la Barbería -->
    <div class="header">
        <h1>Mutant Barber</h1>
        <p>Estilo y Profesionalismo a tu Alcance</p>
    </div>

    <!-- Título del Documento -->
    <div class="title-section">
        <h2>Comprobante de Confirmación de Cita</h2>
    </div>

    <!-- Bloque Destacado de Fecha y Hora -->
    <div class="schedule-box">
        <h3>Programación Confirmada</h3>
        <p class="schedule-time">
            {{ \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('D [de] MMMM, Y') }}
        </p>
        <p style="margin: 5px 0 0 0; font-size: 15px;">
            Horario: {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} a {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }} hs
        </p>
    </div>

    <!-- Información del Cliente y Barbero -->
    <table class="info-table">
        <tr>
            <!-- Columna Cliente -->
            <td>
                <div class="section-title">Información del Cliente</div>
                <div class="info-card">
                    <div class="info-row">
                        <span class="label">Nombre:</span>
                        <div class="value">{{ $appointment->client->name }}</div>
                    </div>
                    <div class="info-row">
                        <span class="label">Correo:</span>
                        <div class="value">{{ $appointment->client->email }}</div>
                    </div>
                </div>
            </td>
            
            <!-- Columna Barbero -->
            <td>
                <div class="section-title">Información del Barbero</div>
                <div class="info-card">
                    <div class="info-row">
                        <span class="label">Nombre:</span>
                        <div class="value">{{ $appointment->barber->name }}</div>
                    </div>
                    <div class="info-row">
                        <span class="label">Correo:</span>
                        <div class="value">{{ $appointment->barber->email }}</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Detalles del Servicio -->
    <div class="section-title" style="margin-left: 15px; margin-right: 15px;">Servicio Agendado</div>
    <table class="details-table" style="margin: 0 15px 40px 15px; width: calc(100% - 30px);">
        <thead>
            <tr>
                <th>Descripción del Servicio</th>
                <th style="width: 100px; text-align: center;">Duración</th>
                <th style="width: 100px; text-align: right;">Costo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $appointment->service->name }}</strong>
                    <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                        {{ $appointment->service->description }}
                    </div>
                </td>
                <td style="text-align: center;">{{ $appointment->service->duration_minutes }} min</td>
                <td style="text-align: right;" class="price-text">${{ number_format($appointment->service->price, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Términos y Recomendaciones -->
    <div class="terms">
        <p><strong>Políticas e indicaciones:</strong></p>
        <p>Le sugerimos presentarse 10 minutos antes del horario programado de su cita.</p>
        <p>Para cancelaciones o reprogramaciones automáticas, estas deberán ser efectuadas con al menos 1 hora de anticipación desde su panel de cliente.</p>
        <p>¡Gracias por depositar su confianza en nuestro equipo de profesionales!</p>
    </div>

    <!-- Sello de Generación -->
    <div class="stamp">
        Documento generado digitalmente por Mutant Barber el {{ now()->format('d/m/Y H:i:s') }}. Estado: {{ ucfirst($appointment->status) }}.
    </div>

</body>
</html>
