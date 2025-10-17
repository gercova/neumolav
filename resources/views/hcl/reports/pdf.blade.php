<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/'.$en->logo) }}">
    <title>Receta Médica - {{ $en->nombre_comercial }}</title>
    <style>
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #2c3e50;
            background: #ffffff;
            line-height: 1.3;
        }

        /* ENCABEZADO PROFESIONAL */
        .header {
            background: linear-gradient(135deg, #1a5276 0%, #2c3e50 100%) !important;
            padding: 15px 20px;
            border-bottom: 4px solid #e74c3c;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .company-info {
            text-align: center;
        }

        .clinic-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 3px;
            color: #ecf0f1;
            letter-spacing: 0.5px;
        }

        .clinic-slogan {
            font-size: 11px;
            color: #bdc3c7;
            margin-bottom: 6px;
            font-style: italic;
        }

        .doctor-info {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .doctor-credentials {
            font-size: 10px;
            color: #ecf0f1;
            margin-bottom: 3px;
        }

        .clinic-address {
            font-size: 10px;
            color: #bdc3c7;
            margin-top: 4px;
        }

        /* CONTENIDO PRINCIPAL MEJORADO */
        .content {
            padding: 15px 20px;
        }

        /* Información del paciente destacada */
        .patient-header {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
            border: 1px solid #e9ecef;
        }

        .patient-data {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .patient-field {
            font-weight: 600;
            color: #2c3e50;
        }

        .patient-field strong {
            color: #1a5276;
        }

        .consultation-date {
            background: #e74c3c;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        /* Secciones médicas mejoradas */
        .medical-section {
            margin-bottom: 12px;
            background: #ffffff;
            border-radius: 5px;
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a5276;
            padding-bottom: 4px;
            border-bottom: 1px solid #3498db;
            margin-bottom: 6px;
        }

        /* Párrafos con tamaño aumentado y mejor legibilidad */
        .section-content p {
            font-size: 13px !important;
            line-height: 1.4;
            margin-bottom: 6px;
            text-align: justify;
            color: #2c3e50;
        }

        /* Diagnóstico destacado */
        .diagnosis-section {
            background: #fffbf0;
            border: 1px solid #ffc107;
            border-left: 4px solid #e74c3c;
        }

        .diagnosis-content {
            font-size: 13px !important;
            font-weight: 600;
            color: #856404;
            padding: 6px;
            background: #fff;
            border-radius: 3px;
        }

        /* Tratamiento destacado */
        .treatment-section {
            background: #f0f9ff;
            border: 1px solid #17a2b8;
            border-left: 4px solid #17a2b8;
        }

        .treatment-content {
            font-size: 13px !important;
            line-height: 1.4;
            margin-bottom: 6px;
        }

        /* PIE DE PÁGINA ELEGANTE */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: linear-gradient(135deg, #2c3e50 0%, #1a5276 100%);
            padding: 6px 0;
            color: white;
            border-top: 3px solid #e74c3c;
            font-size: 10px;
        }

        .footer-content {
            text-align: center;
        }

        .footer-clinic {
            font-weight: 700;
            font-size: 11px;
            color: #ecf0f1;
            margin-bottom: 1px;
        }

        .footer-contact {
            color: #bdc3c7;
        }

        /* Elementos de lista mejorados */
        .section-content ul, .section-content ol {
            margin: 6px 0 6px 18px;
        }

        .section-content li {
            margin-bottom: 3px;
            padding-left: 4px;
            font-size: 12px;
        }

        /* Utilidades */
        .text-accent {
            color: #e74c3c;
        }

        /* Espaciado entre secciones */
        .medical-section + .medical-section {
            margin-top: 3px;
        }

        /* Para impresión/PDF */
        @media print {
            .medical-section {
                page-break-inside: avoid;
            }
            
            .footer {
                position: fixed;
            }
        }
    </style>
</head>
<body>
    <!-- ENCABEZADO -->
    <div class="header">
        <div class="company-info">
            <h1 class="clinic-name">{{ $en->nombre_comercial }}</h1>
            <p class="clinic-slogan">{{ $en->slogan }}</p>
            <p class="doctor-info">{{ $en->representante_legal }} - Médico Neumólogo</p>
            <p class="doctor-credentials">CMP: 60432 | RNE: 39261</p>
            <p class="clinic-address">Dirección: {{ $en->direccion }}, {{ $en->ubigeo }}</p>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        <!-- Información del paciente -->
        <div class="patient-header">
            <div class="patient-data">
                <div class="patient-field"><strong>DNI:</strong> {{ $hc[0]->dni }}</div>
                <div class="patient-field"><strong>Paciente:</strong> {{ $hc[0]->nombres }}</div>
                <div class="patient-field"><strong>Edad:</strong> {{ $hc[0]->age }} años</div>
                <div class="consultation-date">Fecha: {{ date('d/m/Y H:i', strtotime($rk->created_at)) }}</div>
            </div>
        </div>

        <!-- Antecedentes -->
        <div class="medical-section">
            <h3 class="section-title">Antecedentes</h3>
            <div class="section-content">
                <p>{!! $rk->antecedentes !!}</p>
            </div>
        </div>

        <!-- Historial de enfermedad -->
        <div class="medical-section">
            <h3 class="section-title">Historial de Enfermedad</h3>
            <div class="section-content">
                <p>{!! $rk->historial_enfermedad !!}</p>
            </div>
        </div>

        <!-- Examen físico -->
        <div class="medical-section">
            <h3 class="section-title">Examen Físico</h3>
            <div class="section-content">
                <p>{!! $rk->examen_fisico !!}</p>
            </div>
        </div>

        <!-- Exámenes complementarios -->
        <div class="medical-section">
            <h3 class="section-title">Exámenes Complementarios</h3>
            <div class="section-content">
                <p>{!! $rk->examen_complementario !!}</p>
            </div>
        </div>

        <!-- Diagnóstico (sección destacada) -->
        <div class="medical-section diagnosis-section">
            <h3 class="section-title">Diagnóstico</h3>
            <div class="diagnosis-content">
                {{ implode(' / ', array_map(function($d) { return $d->diagnostic; }, $dx)) }}
            </div>
        </div>

        <!-- Tratamiento (sección destacada) -->
        <div class="medical-section treatment-section">
            <h3 class="section-title">Tratamiento</h3>
            <div class="treatment-content">
                <p>{!! $rk->tratamiento !!}</p>
            </div>
        </div>
    </div>

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        <div class="footer-content">
            <p class="footer-clinic">Clínica Rodriguez y Especialistas II</p>
            <p class="footer-contact">Atención de lunes a sábado | Teléfono: {{ $en->telefono }}</p>
        </div>
    </div>
</body>
</html>