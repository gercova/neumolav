<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/'.$en->logo) }}">
    <title>Informe de Risgos Neumológico - {{ $en->nombre_comercial }}</title>
    <style>
        /* Estilos generales */
        body {
            /*font-family: 'DejaVu Sans', 'Arial', sans-serif;*/
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.2; /* Reducido de 1.4 */
            font-size: 12px;
        }

        /* Encabezado profesional */
        .header {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            padding: 12px 15px; /* Reducido */
            border-bottom: 2px solid #007bff;
            margin-bottom: 10px; /* Reducido */
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            flex: 1;
        }

        .clinic-name {
            font-size: 18px;
            font-weight: bold;
            color: #0056b3;
            margin: 0 0 3px 0; /* Reducido */
        }

        .clinic-slogan {
            font-size: 11px;
            color: #6c757d;
            margin: 0 0 5px 0; /* Reducido */
            font-style: italic;
        }

        .doctor-info {
            font-size: 11px;
            margin: 0;
        }

        .doctor-credentials {
            font-size: 10px;
            margin: 2px 0; /* Reducido */
        }

        .clinic-address {
            font-size: 10px;
            margin: 2px 0 0 0; /* Reducido */
        }

        .patient-info-section {
            flex: 1;
            text-align: right;
            border-left: 1px solid #dee2e6;
            padding-left: 12px; /* Reducido */
        }

        .document-title {
            font-size: 16px;
            font-weight: bold;
            color: #343a40;
            margin: 0 0 8px 0; /* Reducido */
        }

        /* Información del paciente */
        .patient-info {
            background-color: #f8f9fa;
            padding: 10px 12px; /* Reducido */
            border-radius: 5px;
            margin-bottom: 12px; /* Reducido */
            border-left: 4px solid #007bff;
        }

        .patient-details {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .patient-field {
            margin-right: 15px; /* Reducido */
            margin-bottom: 3px; /* Reducido */
        }

        .patient-field strong {
            color: #495057;
        }

        /* Secciones de contenido */
        .content-section {
            margin-bottom: 10px; /* Reducido de 15px */
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0056b3;
            padding-bottom: 3px; /* Reducido */
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 5px; /* Reducido */
        }

        .section-content {
            padding: 0 5px;
            text-align: justify;
            margin-top: 0; /* Asegurar sin margen superior */
        }

        .section-content p {
            margin: 2px 0; /* Reducido significativamente */
        }

        /* Eliminar espacios entre secciones */
        .content-section + .content-section {
            margin-top: -5px; /* Compensar espacio entre secciones */
        }

        /* Pie de página */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            padding: 6px 0; /* Reducido */
            border-top: 2px solid #007bff;
            font-size: 10px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            padding: 0 15px; /* Reducido */
        }

        .footer-clinic {
            font-weight: bold;
            color: #0056b3;
        }

        .footer-contact {
            color: #6c757d;
        }

        /* Utilidades */
        .page-break {
            page-break-before: always;
        }

        .signature-area {
            margin-top: 25px; /* Reducido de 40px */
            text-align: center;
        }

        .signature-line {
            width: 60%;
            border-top: 1px solid #333;
            margin: 0 auto 3px; /* Reducido */
        }

        .doctor-signature {
            font-size: 11px;
            margin-top: 0;
            line-height: 1.1; /* Reducido */
        }

        /* Reducir espacios en elementos específicos */
        h3 {
            margin: 8px 0 5px 0; /* Reducido */
        }

        p {
            margin: 3px 0; /* Reducido significativamente */
        }

        br {
            margin-bottom: 2px; /* Reducir efecto de <br> */
        }
    </style>
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
                <h2 class="document-title">RIESGO NEUMOLÓGICO</h2>
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
        <!-- Motivo de consulta -->
        <div class="content-section">
            <h3 class="section-title">MOTIVO DE CONSULTA</h3>
            <div class="section-content">
                <p>{!! $rk->motivo !!}</p>
            </div>
        </div>

        <!-- Antecedentes -->
        <div class="content-section">
            <h3 class="section-title">ANTECEDENTES</h3>
            <div class="section-content">
                <p>{!! $rk->antecedente !!}</p>
            </div>
        </div>

        <!-- Síntomas -->
        <div class="content-section">
            <h3 class="section-title">SÍNTOMAS</h3>
            <div class="section-content">
                <p>{!! $rk->sintomas !!}</p>
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

        <!-- Riesgo neumológico -->
        <div class="content-section">
            <h3 class="section-title">RIESGO NEUMOLÓGICO</h3>
            <div class="section-content">
                <p>{!! $rk->riesgo_neumologico !!}</p>
            </div>
        </div>

        <!-- Sugerencias -->
        <div class="content-section">
            <h3 class="section-title">SUGERENCIAS Y TRATAMIENTO</h3>
            <div class="section-content">
                <p>{!! $rk->sugerencia !!}</p>
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