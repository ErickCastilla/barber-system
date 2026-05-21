<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tus Citas de Hoy</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1f2937; font-size: 13px; line-height: 1.5; margin: 0; padding: 0; }
        .header { background-color: #111827; color: #ffffff; padding: 25px; text-align: center; border-bottom: 4px solid #d97706; }
        .header h1 { margin: 0; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; color: #9ca3af; }
        .title-section { text-align: center; margin: 20px 0; }
        .title-section h2 { font-size: 18px; text-transform: uppercase; color: #111827; border-bottom: 2px solid #f3f4f6; display: inline-block; padding-bottom: 8px; width: 80%; }
        .barber-info { text-align: center; margin-bottom: 25px; font-size: 15px; font-weight: bold; color: #d97706; }
        .data-table { width: 90%; margin: 0 auto; border-collapse: collapse; }
        .data-table th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 10px; border-bottom: 2px solid #e5e7eb; font-size: 12px; text-transform: uppercase; }
        .data-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        .time-col { font-weight: bold; color: #111827; width: 120px; }
        .footer { font-size: 11px; color: #6b7280; text-align: center; margin-top: 50px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mutant Barber</h1>
        <p>Reporte Diario de Agenda</p>
    </div>

    <div class="title-section">
        <h2>Citas Programadas</h2>
    </div>

    <div class="barber-info">
        Barbero: {{ $barber->name }} | Fecha: {{ \Carbon\Carbon::today()->format('d/m/Y') }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Horario</th>
                <th>Cliente</th>
                <th>Servicio</th>
                <th style="text-align: right;">Duración</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appt)
            <tr>
                <td class="time-col">
                    {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }} - 
                    {{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}
                </td>
                <td>{{ $appt->client->name }}</td>
                <td>
                    <strong>{{ $appt->service->name }}</strong><br>
                    <span style="font-size: 11px; color: #6b7280;">${{ number_format($appt->service->price, 2) }}</span>
                </td>
                <td style="text-align: right;">{{ $appt->service->duration_minutes }} min</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Documento autogenerado por Mutant Barber el {{ now()->format('d/m/Y H:i:s') }}.</p>
    </div>
</body>
</html>
