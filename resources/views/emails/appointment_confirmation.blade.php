<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmación de Cita</title>
    <style>
        /* Estilos generales responsivos y limpios para clientes de correo */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #111827; /* Fondo oscuro elegante */
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #d97706; /* Acento dorado/ámbar de barbería */
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-text {
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .details-box {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
            border-bottom: 1px dashed #e5e7eb;
            padding-bottom: 8px;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .label {
            font-weight: 600;
            color: #4b5563;
        }
        .value {
            color: #111827;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
        .attachment-note {
            font-size: 14px;
            color: #6b7280;
            margin-top: 20px;
            text-align: center;
            font-style: italic;
        }
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
            <p>&copy; {{ date('Y') }} Barber System. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
