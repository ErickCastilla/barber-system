<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tus Citas de Hoy</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; color: #1f2937; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background-color: #111827; padding: 30px; text-align: center; border-bottom: 4px solid #d97706; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; text-transform: uppercase; }
        .content { padding: 40px 30px; }
        .welcome-text { font-size: 18px; margin-top: 0; margin-bottom: 20px; font-weight: 600; }
        .summary-box { background-color: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 20px; text-align: center; font-size: 16px; margin-bottom: 20px; }
        .footer { background-color: #f9fafb; padding: 20px; text-align: center; font-size: 13px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mutant Barber</h1>
        </div>
        <div class="content">
            <p class="welcome-text">¡Hola, {{ $barber->name }}!</p>
            <p>Este es tu reporte automatizado diario. A continuación, encontrarás un resumen de las citas que tienes programadas para el día de hoy.</p>
            
            <div class="summary-box">
                <strong>Total de citas programadas:</strong> {{ $appointments->count() }}
            </div>

            <p style="text-align: center; font-size: 14px; color: #6b7280; font-style: italic;">
                Se ha adjuntado un documento PDF con los detalles de los clientes, servicios y horarios exactos.
            </p>
        </div>
        <div class="footer">
            <p>Reporte Diario Automatizado</p>
            <p>&copy; {{ date('Y') }} Mutant Barber.</p>
        </div>
    </div>
</body>
</html>
