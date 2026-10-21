<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Neumológico - {{ $en->nombre_comercial ?? 'NeumoLav' }}</title>
    <style>
        @page {
            margin: 10mm 12mm 12mm 12mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10.5px;
            color: #111827;
            line-height: 1.35;
            background: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            margin-bottom: 8px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .clinic-brand {
            width: 65%;
            padding-right: 12px;
        }

        .clinic-name {
            font-size: 17px;
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
        }

        .clinic-slogan {
            font-size: 9.5px;
            color: #1E6091;
            font-style: italic;
            margin-bottom: 4px;
        }

        .clinic-doctor {
            font-size: 10px;
            font-weight: bold;
            color: #0F2C59;
            margin-bottom: 2px;
        }

        .clinic-credentials {
            font-size: 9px;
            color: #334155;
            margin-bottom: 2px;
        }

        .clinic-contact {
            font-size: 8.5px;
            color: #64748B;
            line-height: 1.25;
        }

        /* DOCUMENT BADGE CARD */
        .doc-badge-col {
            width: 35%;
            text-align: right;
        }

        .doc-badge-box {
            display: inline-block;
            width: 100%;
            border: 1.5px solid #0284C7;
            background-color: #F0F7FF;
            text-align: center;
        }

        .doc-badge-header {
            background-color: #0F2C59;
            color: #FFFFFF;
            padding: 5px 8px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .doc-badge-body {
            padding: 6px 8px;
        }

        .doc-badge-subtitle {
            font-size: 8.5px;
            font-weight: bold;
            color: #0284C7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .doc-badge-folio {
            font-size: 10px;
            color: #0F2C59;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .doc-badge-date {
            font-size: 9px;
            color: #334155;
        }

        /* DIVIDER ACCENT */
        .accent-bar {
            height: 3px;
            background-color: #0F2C59;
            margin-bottom: 2px;
            width: 100%;
        }

        .accent-subbar {
            height: 1.5px;
            background-color: #0284C7;
            margin-bottom: 10px;
            width: 100%;
        }

        /* PATIENT CARD */
        .patient-card {
            background-color: #F4F9FD;
            border: 1px solid #D0E3F3;
            border-left: 4px solid #0F2C59;
            margin-bottom: 10px;
            padding: 7px 10px;
        }

        .patient-table td {
            padding: 2px 4px;
            font-size: 10px;
        }

        .patient-label {
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            font-size: 9px;
        }

        .patient-value {
            color: #111827;
            font-weight: bold;
        }

        /* CLINICAL REPORT SECTIONS */
        .report-section {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .section-bar {
            background-color: #F0F7FF;
            border: 1px solid #BAE6FD;
            border-left: 3.5px solid #0F2C59;
            padding: 4px 8px;
        }

        .section-bar-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .section-body {
            padding: 5px 8px;
            border: 1px solid #E2E8F0;
            border-top: none;
            background-color: #FFFFFF;
            text-align: justify;
            font-size: 10px;
            color: #1E293B;
            line-height: 1.35;
        }

        .section-body p {
            margin-bottom: 3px;
        }

        .section-body p:last-child {
            margin-bottom: 0;
        }

        /* SPECIAL DIAGNOSIS BLOCK */
        .diagnosis-section {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .diagnosis-bar {
            background-color: #EBF5FB;
            border: 1px solid #BAE6FD;
            border-left: 4px solid #0284C7;
            padding: 5px 8px;
        }

        .diagnosis-bar-title {
            font-size: 10px;
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .diagnosis-body {
            padding: 6px 8px;
            border: 1px solid #BAE6FD;
            border-top: none;
            background-color: #F8FAFC;
            font-size: 10.5px;
            font-weight: bold;
            color: #0F2C59;
            line-height: 1.35;
        }

        /* TREATMENT & SUGGESTIONS BLOCKS */
        .treatment-section {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .treatment-bar {
            background-color: #F0F7FF;
            border: 1px solid #BAE6FD;
            border-left: 4px solid #0F2C59;
            padding: 4px 8px;
        }

        .treatment-body {
            padding: 5px 8px;
            border: 1px solid #BAE6FD;
            border-top: none;
            background-color: #FFFFFF;
            text-align: justify;
            font-size: 10px;
            color: #1E293B;
            line-height: 1.35;
        }

        .suggestions-bar {
            background-color: #F0F7FF;
            border: 1px solid #BAE6FD;
            border-left: 4px solid #0284C7;
            padding: 4px 8px;
        }

        .empty-field {
            color: #94A3B8;
            font-style: italic;
        }

        /* SIGNATURE SECTION */
        .signature-table {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
        }

        .signature-left {
            width: 58%;
            vertical-align: bottom;
            padding-left: 5px;
        }

        .signature-right {
            width: 42%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-img-container {
            height: 62px;
            margin-bottom: 2px;
            text-align: center;
        }

        .signature-img {
            max-height: 60px;
            max-width: 180px;
        }

        .signature-spacer {
            height: 50px;
        }

        .signature-line {
            width: 220px;
            border-top: 1.5px solid #0F2C59;
            margin: 0 auto 4px auto;
        }

        .doctor-signature-name {
            font-size: 10.5px;
            font-weight: bold;
            color: #0F2C59;
            line-height: 1.2;
        }

        .doctor-signature-spec {
            font-size: 9px;
            color: #0284C7;
            font-weight: bold;
            line-height: 1.2;
        }

        .doctor-signature-reg {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.2;
        }

        .security-stamp {
            border: 1px dashed #CBD5E1;
            background-color: #F8FAFC;
            padding: 6px 9px;
            font-size: 8px;
            color: #64748B;
            display: inline-block;
            max-width: 300px;
            line-height: 1.3;
        }

        /* FOOTER */
        .footer {
            margin-top: 20px;
            padding-top: 6px;
            border-top: 1.5px solid #0F2C59;
            page-break-inside: avoid;
        }

        .footer-subline {
            height: 1px;
            background-color: #0284C7;
            margin-bottom: 5px;
        }

        .footer-table td {
            font-size: 8px;
            color: #64748B;
            vertical-align: middle;
        }

        .footer-left {
            text-align: left;
            width: 55%;
        }

        .footer-right {
            text-align: right;
            width: 45%;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    @php
        $logoPath = null;
        if (!empty($en->logo_receta) && file_exists(public_path('storage/' . $en->logo_receta))) {
            $logoPath = public_path('storage/' . $en->logo_receta);
        } elseif (!empty($en->logo) && file_exists(public_path('storage/' . $en->logo))) {
            $logoPath = public_path('storage/' . $en->logo);
        } elseif (!empty($en->logo_mini) && file_exists(public_path('storage/' . $en->logo_mini))) {
            $logoPath = public_path('storage/' . $en->logo_mini);
        }
        $logoBase64 = null;
        if ($logoPath && is_readable($logoPath)) {
            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = @file_get_contents($logoPath);
            if ($logoData) {
                $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
            }
        }

        $doctorName = !empty($us->name) ? $us->name : (!empty($en->representante_legal) ? $en->representante_legal : 'Médico Neumólogo');
        $specialtyName = !empty($us->specialty_name) ? $us->specialty_name : 'Médico Neumólogo';
        $cmp = !empty($us->cmp) ? $us->cmp : '60432';
        $rne = !empty($us->rne) ? $us->rne : '39261';

        $sigImg = null;
        if (!empty($signatureBase64)) {
            $sigImg = $signatureBase64;
        } elseif (!empty($us->firma_digital) && \Illuminate\Support\Facades\Storage::disk('public')->exists($us->firma_digital)) {
            $mime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($us->firma_digital) ?: 'image/png';
            $sigData = \Illuminate\Support\Facades\Storage::disk('public')->get($us->firma_digital);
            $sigImg = 'data:' . $mime . ';base64,' . base64_encode($sigData);
        }
    @endphp

    <!-- ENCABEZADO -->
    <table class="header-table no-break">
        <tr>
            <td class="clinic-brand">
                <table style="width: 100%;">
                    <tr>
                        @if($logoBase64)
                            <td style="width: 75px; vertical-align: top; padding-right: 10px;">
                                <img src="{{ $logoBase64 }}" style="max-height: 52px; max-width: 75px;" alt="Logo">
                            </td>
                        @else
                            <td style="width: 46px; vertical-align: top; padding-right: 8px;">
                                <div style="width: 42px; height: 42px; background-color: #0F2C59; border: 2px solid #0284C7; text-align: center; line-height: 40px; color: #FFFFFF; font-weight: bold; font-size: 20px;">
                                    +
                                </div>
                            </td>
                        @endif
                        <td style="vertical-align: top;">
                            <h1 class="clinic-name">{{ $en->nombre_comercial ?: ($en->razon_social ?: 'CENTRO NEUMOLÓGICO') }}</h1>
                            @if(!empty($en->slogan) || !empty($en->frase))
                                <div class="clinic-slogan">{{ $en->slogan ?: $en->frase }}</div>
                            @endif
                            <div class="clinic-doctor">Dr(a). {{ $doctorName }} &bull; {{ $specialtyName }}</div>
                            <div class="clinic-credentials">CMP: {{ $cmp }} &nbsp;|&nbsp; RNE: {{ $rne }}</div>
                            <div class="clinic-contact">
                                {{ $en->direccion }}{{ !empty($en->ubigeo) ? ' - ' . $en->ubigeo : '' }}
                                @if(!empty($en->telefono)) &bull; Tel: {{ $en->telefono }} @endif
                                @if(!empty($en->ruc)) &bull; RUC: {{ $en->ruc }} @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="doc-badge-col">
                <div class="doc-badge-box">
                    <div class="doc-badge-header">INFORME MÉDICO</div>
                    <div class="doc-badge-body">
                        <div class="doc-badge-subtitle">ESPECIALIDAD EN NEUMOLOGÍA</div>
                        <div class="doc-badge-folio">N° INF-{{ str_pad($rp->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="doc-badge-date">
                            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($rp->created_at)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="accent-bar"></div>
    <div class="accent-subbar"></div>

    <!-- DATOS DEL PACIENTE -->
    <div class="patient-card no-break">
        <table class="patient-table">
            <tr>
                <td style="width: 42%;">
                    <span class="patient-label">Paciente:</span><br>
                    <span class="patient-value">{{ $hc[0]->nombres ?? '---' }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="patient-label">DNI / Documento:</span><br>
                    <span class="patient-value">{{ $hc[0]->dni ?? '---' }}</span>
                </td>
                <td style="width: 16%;">
                    <span class="patient-label">Edad:</span><br>
                    <span class="patient-value">{{ $hc[0]->age ?? '---' }} años</span>
                </td>
                <td style="width: 22%;">
                    <span class="patient-label">N° Historia Clínica:</span><br>
                    <span class="patient-value">{{ !empty($hc[0]->history) ? str_pad($hc[0]->history, 5, '0', STR_PAD_LEFT) : '---' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- SECCIONES DEL INFORME -->

    <!-- I. ANTECEDENTES -->
    <div class="report-section no-break">
        <div class="section-bar">
            <span class="section-bar-title">I. Antecedentes Clínicos Relevantes</span>
        </div>
        <div class="section-body">
            @if(!empty(trim(strip_tags($rp->antecedentes))))
                {!! $rp->antecedentes !!}
            @else
                <span class="empty-field">Sin antecedentes patológicos consignados.</span>
            @endif
        </div>
    </div>

    <!-- II. HISTORIAL DE ENFERMEDAD -->
    <div class="report-section no-break">
        <div class="section-bar">
            <span class="section-bar-title">II. Historial de la Enfermedad Actual</span>
        </div>
        <div class="section-body">
            @if(!empty(trim(strip_tags($rp->historial_enfermedad))))
                {!! $rp->historial_enfermedad !!}
            @else
                <span class="empty-field">No refiere información adicional de inicio o evolución.</span>
            @endif
        </div>
    </div>

    <!-- III. EXAMEN FÍSICO -->
    <div class="report-section no-break">
        <div class="section-bar">
            <span class="section-bar-title">III. Examen Físico y Hallazgos Clínicos</span>
        </div>
        <div class="section-body">
            @if(!empty(trim(strip_tags($rp->examen_fisico))))
                {!! $rp->examen_fisico !!}
            @else
                <span class="empty-field">Sin hallazgos clínicos de significancia patológica.</span>
            @endif
        </div>
    </div>

    <!-- IV. EXÁMENES COMPLEMENTARIOS -->
    <div class="report-section no-break">
        <div class="section-bar">
            <span class="section-bar-title">IV. Exámenes Complementarios y Estudios Auxiliares</span>
        </div>
        <div class="section-body">
            @if(!empty(trim(strip_tags($rp->examen_complementario))))
                {!! $rp->examen_complementario !!}
            @else
                <span class="empty-field">No se registran exámenes complementarios asociados.</span>
            @endif
        </div>
    </div>

    <!-- V. DIAGNÓSTICO -->
    <div class="diagnosis-section no-break">
        <div class="diagnosis-bar">
            <span class="diagnosis-bar-title">V. Conclusión Diagnóstica</span>
        </div>
        <div class="diagnosis-body">
            @if(!empty($dx) && count($dx) > 0)
                {{ implode('  •  ', array_map(function($d) { return $d->diagnostic; }, $dx)) }}
            @else
                <span class="empty-field">Evaluación médica general</span>
            @endif
        </div>
    </div>

    <!-- VI. PLAN DE TRATAMIENTO -->
    <div class="treatment-section no-break">
        <div class="treatment-bar">
            <span class="section-bar-title">VI. Plan de Tratamiento Médico</span>
        </div>
        <div class="treatment-body">
            @if(!empty(trim(strip_tags($rp->tratamiento))))
                {!! $rp->tratamiento !!}
            @else
                <span class="empty-field">Sin indicaciones terapéuticas registradas.</span>
            @endif
        </div>
    </div>

    <!-- VII. SUGERENCIAS Y RECOMENDACIONES -->
    <div class="treatment-section no-break">
        <div class="section-bar suggestions-bar">
            <span class="section-bar-title">VII. Sugerencias y Recomendaciones Médicas</span>
        </div>
        <div class="treatment-body">
            @if(!empty(trim(strip_tags($rp->sugerencia))))
                {!! $rp->sugerencia !!}
            @else
                <span class="empty-field">Continuar con controles periódicos según evolución clínica.</span>
            @endif
        </div>
    </div>

    <!-- SECCIÓN DE FIRMA -->
    <table class="signature-table no-break">
        <tr>
            <td class="signature-left">
                <div class="security-stamp">
                    <strong>Certificación Médica Profesional</strong><br>
                    El presente informe refleja la evaluación clínica del paciente a la fecha indicada.
                </div>
            </td>
            <td class="signature-right">
                <div class="signature-img-container">
                    @if($sigImg)
                        <img src="{{ $sigImg }}" class="signature-img" alt="Firma Digital">
                    @else
                        <div class="signature-spacer"></div>
                    @endif
                </div>
                <div class="signature-line"></div>
                <div class="doctor-signature-name">Dr(a). {{ $doctorName }}</div>
                <div class="doctor-signature-spec">{{ $specialtyName }}</div>
                <div class="doctor-signature-reg">CMP: {{ $cmp }} &nbsp;|&nbsp; RNE: {{ $rne }}</div>
            </td>
        </tr>
    </table>

    <!-- PIE DE PÁGINA -->
    <div class="footer no-break">
        <div class="footer-subline"></div>
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    <strong>{{ $en->nombre_comercial ?: ($en->razon_social ?: 'Centro Neumológico') }}</strong> &bull;
                    Atención Médica Especializada
                    @if(!empty($en->telefono)) &bull; Tel: {{ $en->telefono }} @endif
                </td>
                <td class="footer-right">
                    Documento de carácter médico confidencial &bull; Fecha de emisión: {{ date('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
