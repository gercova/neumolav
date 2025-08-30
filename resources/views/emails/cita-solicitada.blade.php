<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Solicitud de Cita</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; text-align: center; }
        .content { background: #fff; padding: 20px; border: 1px solid #dee2e6; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Nueva Solicitud de Cita</h2>
        </div>
        
        <div class="content">
            <p><strong>Nombre:</strong> {{ $reservation->name }}</p>
            <p><strong>Email:</strong> {{ $reservation->email }}</p>
            <p><strong>Teléfono:</strong> {{ $reservation->phone }}</p>
            
            @if($reservation->message)
                <p><strong>Mensaje:</strong><br>{{ $reservation->message }}</p>
            @endif
        </div>
        
        <div class="footer">
            <p>Este mensaje fue enviado automáticamente desde el sistema de citas.</p>
            <p>{{ config('app.name') }} © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>