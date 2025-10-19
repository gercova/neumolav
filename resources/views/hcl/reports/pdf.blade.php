<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Neumológico - {{ $en->nombre_comercial }}</title>
    <link rel="stylesheet" href="{{ asset('css/pdf.report.css') }}">
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <h1 class="clinic-name">{{ $en->nombre_comercial }}</h1>
                <p class="clinic-slogan">{{ $en->slogan }}</p>
                <p class="doctor-info"><strong>{{ $en->representante_legal }} - Médico Neumólogo</strong></p>
                <p class="doctor-credentials">CMP: 60432 | RNE: 39261</p>
                <p class="clinic-address">Dirección: {{ $en->direccion }}, {{ $en->ubigeo }}</p>
            </div>
            <div class="patient-info-section">
                <h2 class="document-title">INFORME MÉDICO</h2>
                <p><strong>Fecha:</strong> {{ date('d/m/Y', strtotime($rk->created_at)) }}</p>
            </div>
        </div>
    </div>

    <!-- Información del paciente -->
    <div class="patient-info">
        <div class="patient-details">
            <div class="patient-field"><strong>DNI:</strong> {{ $hc[0]->dni }}</div>
            <div class="patient-field"><strong>Paciente:</strong> {{ $hc[0]->nombres }}</div>
            <div class="patient-field"><strong>Edad:</strong> {{ $hc[0]->age }} años</div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <!-- Antecedentes -->
        <div class="content-section">
            <h3 class="section-title">ANTECEDENTES</h3>
            <div class="section-content">
                <p>{!! $rk->antecedentes !!}</p>
            </div>
        </div>

        <!-- Historial de enfermedad -->
        <div class="content-section">
            <h3 class="section-title">HISTORIAL DE ENFERMEDAD</h3>
            <div class="section-content">
                <p>{!! $rk->historial_enfermedad !!}</p>
            </div>
        </div>

        <!-- Examen físico -->
        <div class="content-section">
            <h3 class="section-title">EXAMEN FÍSICO</h3>
            <div class="section-content">
                <p>{!! $rk->examen_fisico !!}</p>
            </div>
        </div>

        <!-- Exámenes complementarios -->
        <div class="content-section">
            <h3 class="section-title">EXÁMENES COMPLEMENTARIOS</h3>
            <div class="section-content">
                <p>{!! $rk->examen_complementario !!}</p>
            </div>
        </div>

        <!-- Diagnóstico -->
        <div class="content-section diagnosis-section no-break">
            <h3 class="section-title">DIAGNÓSTICO</h3>
            <div class="section-content">
                <p><strong>{{ implode(' / ', array_map(function($d) { return $d->diagnostic; }, $dx)) }}</strong></p>
            </div>
        </div>

        <!-- Tratamiento -->
        <div class="content-section treatment-section no-break">
            <h3 class="section-title">TRATAMIENTO</h3>
            <div class="section-content">
                <p>{!! $rk->tratamiento !!}</p>
            </div>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-clinic">Clínica Rodriguez y Especialistas II</div>
            <div class="footer-contact">Atención de lunes a sábado | Tel: {{ $en->telefono }}</div>
        </div>
    </div>
</body>
</html>