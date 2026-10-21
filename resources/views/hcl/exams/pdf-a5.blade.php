<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta Médica - {{ $en->nombre_comercial ?? 'NeumoLav' }}</title>
    <style>
        @page {
            size: a5 portrait;
            margin: 6mm 8mm 6mm 8mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5px;
            color: #111827;
            line-height: 1.25;
            background: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            margin-bottom: 4px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .clinic-brand {
            width: 66%;
            padding-right: 8px;
        }

        .clinic-name {
            font-size: 13px;
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0 0 1px 0;
        }

        .clinic-slogan {
            font-size: 7.5px;
            color: #1E6091;
            font-style: italic;
            margin-bottom: 2px;
        }

        .clinic-doctor {
            font-size: 8px;
            font-weight: bold;
            color: #0F2C59;
            margin-bottom: 1px;
        }

        .clinic-credentials {
            font-size: 7.5px;
            color: #334155;
            margin-bottom: 1px;
        }

        .clinic-contact {
            font-size: 7px;
            color: #64748B;
            line-height: 1.15;
        }

        /* DOCUMENT BADGE CARD */
        .doc-badge-col {
            width: 34%;
            text-align: right;
        }

        .doc-badge-box {
            display: inline-block;
            width: 100%;
            border: 1px solid #0284C7;
            background-color: #F0F7FF;
            text-align: center;
        }

        .doc-badge-header {
            background-color: #0F2C59;
            color: #FFFFFF;
            padding: 3px 5px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .doc-badge-body {
            padding: 4px 5px;
        }

        .doc-badge-subtitle {
            font-size: 7px;
            font-weight: bold;
            color: #0284C7;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .doc-badge-folio {
            font-size: 8.5px;
            color: #0F2C59;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .doc-badge-date {
            font-size: 7.5px;
            color: #334155;
        }

        /* DIVIDER ACCENT */
        .accent-bar {
            height: 2px;
            background-color: #0F2C59;
            margin-bottom: 1px;
            width: 100%;
        }

        .accent-subbar {
            height: 1px;
            background-color: #0284C7;
            margin-bottom: 5px;
            width: 100%;
        }

        /* PATIENT CARD */
        .patient-card {
            background-color: #F4F9FD;
            border: 1px solid #D0E3F3;
            border-left: 3px solid #0F2C59;
            margin-bottom: 5px;
            padding: 4px 6px;
        }

        .patient-table td {
            padding: 1px 3px;
            font-size: 8px;
        }

        .patient-label {
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            font-size: 7px;
        }

        .patient-value {
            color: #111827;
            font-weight: bold;
        }

        /* DIAGNOSIS SECTION */
        .diagnosis-card {
            background-color: #EBF5FB;
            border: 1px solid #BAE6FD;
            border-left: 3px solid #0284C7;
            padding: 3px 6px;
            margin-bottom: 5px;
            font-size: 8px;
        }

        .diagnosis-title {
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            margin-right: 4px;
        }

        .diagnosis-text {
            color: #111827;
            font-weight: bold;
        }

        /* PRESCRIPTION SECTION */
        .section-header {
            margin-bottom: 3px;
            padding-bottom: 2px;
            border-bottom: 1px solid #0F2C59;
        }

        .section-title {
            font-size: 9px;
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .section-tag {
            font-size: 7px;
            color: #0284C7;
            font-weight: bold;
            margin-left: 4px;
            text-transform: uppercase;
        }

        /* PRESCRIPTION TABLE */
        .prescription-table {
            width: 100%;
            margin-bottom: 6px;
            border: 1px solid #D0E3F3;
        }

        .prescription-table th {
            background-color: #0F2C59;
            color: #FFFFFF;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 4px;
            letter-spacing: 0.2px;
            border: 1px solid #0F2C59;
        }

        .prescription-table td {
            padding: 4px 5px;
            font-size: 8px;
            vertical-align: top;
            border-bottom: 1px solid #E2E8F0;
        }

        .prescription-table tr.alt-row {
            background-color: #F8FAFC;
        }

        .prescription-table tr:last-child td {
            border-bottom: none;
        }

        .item-num {
            width: 5%;
            text-align: center;
            font-weight: bold;
            color: #0F2C59;
            font-size: 8px;
        }

        .drug-col {
            width: 38%;
        }

        .drug-name {
            font-weight: bold;
            color: #0F2C59;
            font-size: 8.5px;
            display: block;
            margin-bottom: 1px;
        }

        .instructions-col {
            width: 44%;
            color: #334155;
            font-size: 8px;
            line-height: 1.25;
        }

        .qty-col {
            width: 13%;
            text-align: center;
        }

        .qty-badge {
            display: inline-block;
            background-color: #E0F2FE;
            color: #0369A1;
            font-weight: bold;
            font-size: 7.5px;
            padding: 1px 5px;
            border: 1px solid #BAE6FD;
        }

        .empty-rx {
            text-align: center;
            padding: 10px;
            color: #64748B;
            font-style: italic;
            font-size: 8px;
        }

        /* GENERAL INSTRUCTIONS CARD */
        .instructions-card {
            background-color: #F0F7FF;
            border: 1px solid #BAE6FD;
            border-left: 2.5px solid #0284C7;
            padding: 4px 6px;
            margin-bottom: 8px;
        }

        .instructions-heading {
            font-size: 7.5px;
            font-weight: bold;
            color: #0F2C59;
            text-transform: uppercase;
            margin-bottom: 2px;
            letter-spacing: 0.2px;
        }

        .instructions-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .instructions-list li {
            font-size: 7px;
            color: #334155;
            margin-bottom: 1px;
            line-height: 1.2;
        }

        .instructions-list li:before {
            content: "• ";
            color: #0284C7;
            font-weight: bold;
        }

        /* SIGNATURE SECTION */
        .signature-table {
            width: 100%;
            margin-top: 6px;
            page-break-inside: avoid;
        }

        .signature-left {
            width: 55%;
            vertical-align: bottom;
            padding-left: 3px;
        }

        .signature-right {
            width: 45%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-img-container {
            height: 40px;
            margin-bottom: 1px;
            text-align: center;
        }

        .signature-img {
            max-height: 38px;
            max-width: 140px;
        }

        .signature-spacer {
            height: 32px;
        }

        .signature-line {
            width: 150px;
            border-top: 1px solid #0F2C59;
            margin: 0 auto 3px auto;
        }

        .doctor-signature-name {
            font-size: 8.5px;
            font-weight: bold;
            color: #0F2C59;
            line-height: 1.15;
        }

        .doctor-signature-spec {
            font-size: 7.5px;
            color: #0284C7;
            font-weight: bold;
            line-height: 1.15;
        }

        .doctor-signature-reg {
            font-size: 7px;
            color: #475569;
            line-height: 1.15;
        }

        .security-stamp {
            border: 1px dashed #CBD5E1;
            background-color: #F8FAFC;
            padding: 3px 5px;
            font-size: 6.5px;
            color: #64748B;
            display: inline-block;
            max-width: 180px;
        }

        /* FOOTER */
        .footer {
            margin-top: 10px;
            padding-top: 4px;
            border-top: 1px solid #0F2C59;
        }

        .footer-subline {
            height: 1px;
            background-color: #0284C7;
            margin-bottom: 3px;
        }

        .footer-table td {
            font-size: 6.5px;
            color: #64748B;
            vertical-align: middle;
        }

        .footer-left {
            text-align: left;
            width: 60%;
        }

        .footer-right {
            text-align: right;
            width: 40%;
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
                            <td style="width: 55px; vertical-align: top; padding-right: 6px;">
                                <img src="{{ $logoBase64 }}" style="max-height: 40px; max-width: 55px;" alt="Logo">
                            </td>
                        @else
                            <td style="width: 34px; vertical-align: top; padding-right: 6px;">
                                <div style="width: 30px; height: 30px; background-color: #0F2C59; border: 1.5px solid #0284C7; text-align: center; line-height: 28px; color: #FFFFFF; font-weight: bold; font-size: 15px;">
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
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="doc-badge-col">
                <div class="doc-badge-box">
                    <div class="doc-badge-header">RECETA MÉDICA</div>
                    <div class="doc-badge-body">
                        <div class="doc-badge-subtitle">EXAMEN ESPECIALIZADO</div>
                        <div class="doc-badge-folio">N° EX-{{ str_pad($ex->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="doc-badge-date">
                            {{ \Carbon\Carbon::parse($ex->created_at)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($ex->created_at)->format('H:i') }}
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
                    <span class="patient-label">DNI / Doc:</span><br>
                    <span class="patient-value">{{ $hc[0]->dni ?? '---' }}</span>
                </td>
                <td style="width: 16%;">
                    <span class="patient-label">Edad:</span><br>
                    <span class="patient-value">{{ $hc[0]->age ?? '---' }} años</span>
                </td>
                <td style="width: 22%;">
                    <span class="patient-label">N° Historia:</span><br>
                    <span class="patient-value">{{ !empty($hc[0]->history) ? str_pad($hc[0]->history, 5, '0', STR_PAD_LEFT) : '---' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- DIAGNÓSTICO -->
    <div class="diagnosis-card no-break">
        <span class="diagnosis-title">Diagnóstico(s):</span>
        <span class="diagnosis-text">
            @if(!empty($dx) && count($dx) > 0)
                {{ implode('  •  ', array_map(function($d) { return $d->diagnostic; }, $dx)) }}
            @else
                Evaluación médica general
            @endif
        </span>
    </div>

    <!-- SECCIÓN DE PRESCRIPCIÓN MÉDICA -->
    <div class="section-header no-break">
        <span class="section-title">Prescripción Médica (Rp.)</span>
        <span class="section-tag">&bull; Tratamiento Farmacológico</span>
    </div>

    <table class="prescription-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 38%;">Medicamento / Presentación</th>
                <th style="width: 44%;">Indicaciones / Dosificación y Posología</th>
                <th style="width: 13%;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mx as $key => $m)
                <tr class="{{ $key % 2 == 1 ? 'alt-row' : '' }} no-break">
                    <td class="item-num">{{ $key + 1 }}</td>
                    <td class="drug-col">
                        <span class="drug-name">{{ $m->drug }}</span>
                    </td>
                    <td class="instructions-col">
                        @if(!empty($m->rp))
                            {{ $m->rp }}
                        @else
                            <span style="color: #94A3B8; font-style: italic;">Según indicación médica en consulta</span>
                        @endif
                    </td>
                    <td class="qty-col">
                        @if(!empty($m->quantity))
                            <span class="qty-badge">{{ $m->quantity }}</span>
                        @else
                            <span style="color: #94A3B8;">---</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty-rx">
                        No se registraron medicamentos para este examen médico.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- RECOMENDACIONES GENERALES -->
    <div class="instructions-card no-break">
        <div class="instructions-heading">Instrucciones y Cuidados Generales para el Paciente:</div>
        <ul class="instructions-list">
            <li>Cumplir estrictamente con los horarios, dosis y duración indicados para garantizar la efectividad del tratamiento.</li>
            <li>No suspender la medicación ni automedicarse sin consultar previamente con su médico especialista.</li>
            <li>Acudir a su consulta de control en la fecha programada o acudir oportunamente si presenta alguna reacción adversa.</li>
        </ul>
    </div>

    <!-- SECCIÓN DE FIRMA -->
    <table class="signature-table no-break">
        <tr>
            <td class="signature-left">
                <div class="security-stamp">
                    <strong>Documento Médico Oficial</strong><br>
                    Válido para dispensación farmacéutica.
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
                    <strong>{{ $en->nombre_comercial ?: ($en->razon_social ?: 'Centro Neumológico') }}</strong>
                    @if(!empty($en->telefono)) &bull; Tel: {{ $en->telefono }} @endif
                </td>
                <td class="footer-right">
                    Confidencial &bull; Emisión: {{ date('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>