<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/'.$en->logo) }}">
    <title>Receta Médica - {{ $en->nombre_comercial }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        /* ENCABEZADO */
        .header {
            background-color: #f4f4f4;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

        .header .company-info h1 {
            margin: 0;
            font-size: 14px;
        }

        .header .company-info p {
            margin: 0;
            font-size: 10px;
        }

        /* CONTENIDO - AQUÍ ESTÁ EL CAMBIO */
        .content {
            padding: 5px;
        }

        /* AUMENTAR SOLO LOS PÁRRAFOS DENTRO DE .content */
        .content p {
            font-size: 14px !important; /* Aumentado de 12px a 14px */
            line-height: 1.4;
            margin-bottom: 8px;
        }

        /* Mantener otros tamaños */
        .content h3 {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .content ol li {
            font-size: 15px !important;
            margin-bottom: 3px;
        }

        /* PIE DE PÁGINA */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            background-color: #f4f4f4;
            padding: 3px 0;
            border-top: 1px solid #ddd;
            font-size: 10px;
        }

        .footer p {
            margin: 0;
        }

        /* OTROS ESTILOS */
        .inline-block {
            display: inline-block;
            margin-right: 10px;
        }

        ol {
            margin-top: 0;
            margin-bottom: 0;
            padding-left: 20px;
        }

        ol li {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <div class="company-info">
            <div>
                <h1><center><b>{{ $en->nombre_comercial }}</b></center></h1>
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
        <p><strong>Fecha:</strong> {{ $ap->created_at }}</p>

        <h3>Diagnóstico:</h3>
        <p>{{ implode(' / ', array_map(function($d) { return $d->diagnostic; }, $dx)) }}</p>

        <h3>Rp.</h3>
        <ol>
            @foreach($mx as $key => $m)
                <li>
                    <b>{{ $m->drug }}</b>
                    <br>
                    @if (!empty($m->rp))
                        {{ $m->rp }}
                    @else
                        <br>
                    @endif
                    @if (!empty($m->quantity))
                        <strong>Cant:</strong> {{ $m->quantity }}
                    @endif
                    <br>
                </li>
            @endforeach
        </ol>
    </div>
    <div class="footer">
        <p><center><b>{{ $en->nombre_comercial }}</b></center></p>
        <p><center>Atención de lunes a sábado, cel: {{ $en->telefono }}</center></p>
    </div>
</body>
</html>