<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/'.$en->logo) }}">
    <title>Receta Médica - {{ $en->nombre_comercial }}</title>
    <link rel="stylesheet" href="{{ asset('css/pdf-a5.rp.css') }}">
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1>{{ $en->nombre_comercial }}</h1>
            <p class="slogan">{{ $en->slogan }}</p>
            <div class="legal-info">
                <p><b>{{ $en->representante_legal }} - Médico Neumólogo</b></p>
                <p>CMP: 60432 | RNE: 39261</p>
                <p>Dirección: {{ $en->direccion }}, {{ $en->ubigeo }}</p>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="patient-info">
            <p>
                <span class="inline-block"><b>DNI:</b> {{ $hc[0]->dni }}</span>
                <span class="inline-block"><b>Nombres:</b> {{ $hc[0]->nombres }}</span>
                <span class="inline-block"><b>Edad:</b> {{ $hc[0]->age }} años</span>
                <span class="inline-block"><b>Fecha:</b> {{ \Carbon\Carbon::parse($ex->created_at)->format('d/m/Y H:i') }}</span>
            </p>
        </div>
        
        <div class="diagnosis-section">
            <p><b>Diagnóstico:</b> {{ implode(' / ', array_map(function($d) { return $d->diagnostic; }, $dx)) }}</p>
        </div>
        
        <div class="prescription-section">
            <h3>Rp.</h3>
            <ol class="prescription-list">
                @foreach($mx as $key => $m)
                    <li>
                        <span class="drug-name">{{ $m->drug }}</span>
                        <div class="drug-details">
                            @if (!empty($m->rp))
                                <span class="drug-line">{{ $m->rp }}</span>
                            @endif
                            @if (!empty($m->quantity))
                                <span class="drug-quantity"><strong>Cant:</strong> {{ $m->quantity }}</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="footer">
        <div class="footer-content">
            <div class="footer-clinic">Clínica Rodriguez y Especialistas II</div>
            <div class="footer-contact">Atención de lunes a sábado | Tel: {{ $en->telefono }}</div>
        </div>
    </div>
</body>
</html>