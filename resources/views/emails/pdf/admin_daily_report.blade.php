<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resumen General de Citas</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1f2937; font-size: 13px; line-height: 1.5; margin: 0; padding: 0; }
        .header { background-color: #111827; color: #ffffff; padding: 25px; text-align: center; border-bottom: 4px solid #d97706; }
        .header h1 { margin: 0; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; color: #9ca3af; }
        .title-section { text-align: center; margin: 20px 0; }
        .title-section h2 { font-size: 18px; text-transform: uppercase; color: #111827; border-bottom: 2px solid #f3f4f6; display: inline-block; padding-bottom: 8px; width: 80%; }
        .admin-info { text-align: center; margin-bottom: 25px; font-size: 14px; font-weight: bold; color: #4b5563; }
        
        /* Contenedor por barbero */
        .barber-section { width: 90%; margin: 0 auto 30px auto; }
        .barber-header { background-color: #d97706; color: #ffffff; padding: 8px 15px; font-weight: bold; font-size: 14px; border-radius: 4px 4px 0 0; }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #e5e7eb; font-size: 11px; text-transform: uppercase; }
        .data-table td { padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        .time-col { font-weight: bold; color: #111827; width: 100px; }
        
        .footer { font-size: 11px; color: #6b7280; text-align: center; margin-top: 50px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mutant Barber</h1>
        <p>Resumen Administrativo de Agenda</p>
    </div>

    <div class="title-section">
        <h2>Citas Globales Programadas</h2>
    </div>

    <div class="admin-info">
        Fecha del Reporte: {{ \Carbon\Carbon::today()->format('d/m/Y') }}
    </div>

    @foreach($appointmentsGrouped as $barberId => $appointments)
        @php
            // El nombre del barbero se obtiene de la primera cita de la agrupación
            $barberName = $appointments->first()->barber->name;
        @endphp
        <div class="barber-section">
            <div class="barber-header">
                Barbero: {{ $barberName }} ({{ $appointments->count() }} citas)
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Horario</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th style="text-align: right;">Ingreso Estimado</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalIngresos = 0; @endphp
                    @foreach($appointments as $appt)
                        @php $totalIngresos += $appt->service->price; @endphp
                        <tr>
                            <td class="time-col">
                                {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}
                            </td>
                            <td>{{ $appt->client->name }}</td>
                            <td>{{ $appt->service->name }}</td>
                            <td style="text-align: right;">${{ number_format($appt->service->price, 2) }}</td>
                        </tr>
                    @endforeach
                    <!-- Fila de totales del barbero -->
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Subtotal Proyectado:</td>
                        <td style="text-align: right; font-weight: bold; color: #d97706;">
                            ${{ number_format($totalIngresos, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer">
        <p>Documento autogenerado por Mutant Barber para uso administrativo el {{ now()->format('d/m/Y H:i:s') }}.</p>
    </div>
</body>
</html>
