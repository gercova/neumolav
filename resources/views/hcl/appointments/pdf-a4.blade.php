<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/'.$en->logo) }}">
    <title>Receta Médica - {{ $en->nombre_comercial }}</title>
    <link rel="stylesheet" href="{{ asset('css/pdf-a4.rp.css') }}">
</head>
<body>
    <div class="a4-container">
        <div class="header no-break">
            <div class="company-info">
                <h1>{{ $en->nombre_comercial }}</h1>
                <p class="slogan">{{ $en->slogan }}</p>
                <div class="legal-info">
                    <p><b>{{ $en->representante_legal }} - Médico Neumólogo</b></p>
                    <p>CMP: 60432 | RNE: 39261</p>
                    <p class="compact-text">Dirección: {{ $en->direccion }}, {{ $en->ubigeo }}</p>
                </div>
            </div>
        </div>

        <div class="content">
            <!-- Información del paciente más compacta -->
            <div class="compact-grid no-break">
                <div class="grid-item">
                    <b>DNI:</b> {{ $hc[0]->dni }}
                </div>
                <div class="grid-item">
                    <b>Paciente:</b> {{ $hc[0]->nombres }}
                </div>
                <div class="grid-item">
                    <b>Edad:</b> {{ $hc[0]->age }} años
                </div>
                <div class="grid-item">
                    <b>Fecha:</b> {{ \Carbon\Carbon::parse($ap->created_at)->format('d/m/Y H:i') }}
                </div>
            </div>
            
            <div class="diagnosis-section no-break">
                <p><b>Diagnóstico:</b> {{ implode(' / ', array_map(function($d) { return $d->diagnostic; }, $dx)) }}</p>
            </div>
            
            <div class="prescription-section">
                <h3>RECETA MÉDICA</h3>
                <ol class="prescription-list">
                    @foreach($mx as $key => $m)
                        <li class="no-break">
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

            <!-- Sección de instrucciones opcional -->
            <div class="instructions-section no-break">
                <h4>Instrucciones Generales:</h4>
                <p class="compact-text">• Cumplir con el tratamiento completo • No automedicarse • Asistir a controles programados • Consultar en caso de efectos adversos</p>
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <div class="footer-clinic">Clínica Rodriguez y Especialistas II</div>
                <div class="footer-contact">Atención: Lunes - Sábado | Tel: {{ $en->telefono }}</div>
            </div>
        </div>
    </div>
</body>
</html>