<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta Médica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 14px !important; /* Tamaño de fuente reducido */
        }

        /* Encabezado */
        .header {
            background-color: #f4f4f4;
            padding: 5px; /* Padding reducido */
            border-bottom: 1px solid #ddd;
        }

        .header .company-info {
            display: flex;
            justify-content: space-between;
        }

        .header .company-info h1 {
            margin: 0;
            font-size: 14px; /* Tamaño de fuente reducido */
        }

        .header .company-info p {
            margin: 0;
            font-size: 10px; /* Tamaño de fuente reducido */
        }

        /* Contenido principal */
        .content {
            padding: 5px; /* Padding reducido */
            font-size: 12px; /* Tamaño de fuente reducido */
        }

        /* Pie de página */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            background-color: #f4f4f4;
            padding: 3px 0; /* Padding reducido */
            border-top: 1px solid #ddd;
            font-size: 10px; /* Tamaño de fuente reducido */
        }

        .footer p {
            margin: 0;
        }

        /* Estilos para reducir el espacio entre líneas */
        .inline-block {
            display: inline-block;
            margin-right: 10px; /* Espacio entre elementos en la misma línea */
        }

        /* Reducir el espacio entre líneas en listas */
        ol {
            margin-top: 0;
            margin-bottom: 0;
            padding-left: 20px; /* Padding reducido */
        }

        ol li {
            margin-bottom: 3px; /* Margen inferior reducido */
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div>
                <h1><center><b>{{  $en->nombre_comercial }}</b></center></h1>
                <p><center>{{ $en->slogan }}</center></p>
                <p><center><b>{{ $en->representante_legal }} - Médico Neumólogo</b></center></p>
                <p><center>CMP: 60432 || RNE: 39261</center></p>
                <p><center>Dirección: {{ $en->direccion }}, {{ $en->ubigeo }}</center></p>
            </div>
        </div>
    </div>
    <div class="content">
        <p>
            <span class="inline-block"><strong>DNI:</strong> {{ $hc[0]->dni }}</span>
            <span class="inline-block"><strong>Nombres:</strong> {{ $hc[0]->nombres }}</span>
            <span class="inline-block"><strong>Edad:</strong> {{ $hc[0]->age }} años</span>
        </p>
        <p><strong>Fecha:</strong> {{ $rk->created_at }}</p>

        <h3>Motivo:</h3>
        <p>{{ $rk->motivo }}</p>
        <br>
        <h3>Antecedentes:</h3>
        <p>{!! $rk->antecedente !!}</p>
        <br>
        <h3>Síntomas:</h3>
        <p>{!! $rk->sintomas !!}</p>
        <br>
        <h3>Examen fÍsico:</h3>
        <p>{!! $rk->examen_fisico !!}</p>
        <br>
        <h3>Examen complementario:</h3>
        <p>{!! $rk->examen_complementario !!}</p>
        <br>
        <h3>Riesgo neumológico:</h3>
        <p>{!! $rk->riesgo_neumologico !!}</p>
        <br>
        <h3>Sugerencia:</h3>
        <p>{!! $rk->sugerencia !!}</p>
        <br>
    </div>
    <div class="footer">
        <p><center><b>Clínica Rodriguez y Especialistas II</b></center></p>
        <p><center>Atención de lunes a sábado, cel: {{ $en->telefono }}</center></p>
    </div>
</body>
</html>