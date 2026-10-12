@extends('layouts.app')
@section('title', config('global.site_name') . ' - Actualizar examen')
@php
    // Imágenes existentes del examen
    $existingImages = $ex->images
        ->map(
            fn($img)        => [
                'id'        => $img->id,
                'preview'   => \Storage::disk('public')->url($img->imagen),
                'date'      => $img->fecha_examen,
                'isNew'     => false,
            ],
        )
        ->values()
        ->all();

    // Diagnósticos existentes
    $existingDiagnostics = $ex->diagnostics
        ->map(
            fn($d) => [
                'id'            => $d->diagnostic->id ?? null,
                'descripcion'   => $d->diagnostic->descripcion ?? '',
            ],
        )
        ->filter(fn($d) => $d['id'])
        ->values()
        ->all();

    // Medicación existente
    $existingDrugs = $ex->medication
        ->map(
            fn($m)  => [
                'id'            => $m->drug->id ?? null,
                'descripcion'   => $m->drug->descripcion ?? '',
                'rp'            => $m->descripcion ?? '',
            ],
        )
        ->filter(fn($d) => $d['id'])
        ->values()
        ->all();

    $formState = [
        'meta' => [
            'isEdit'    => true,
            'canEdit'   => auth()
                ->user()
                ->hasAnyRole(['administrador', 'especialista']),
            'patient'   => ['id' => $hc->id, 'dni' => $hc->dni, 'nombres' => $hc->nombres],
            'userName'  => Auth::user()->name,
            'createdAt' => $ex->created_at->format('Y-m-d H:i:s'),
        ],
        'form' => array_merge($ex->toArray(), [
            'examId'        => $ex->id,
            'id_historia'   => $hc->id,
        ]),
        'catalog'       => ['examTypes' => $te],
        'diagnostics'   => $existingDiagnostics,
        'drugs'         => $existingDrugs,
        'images'        => $existingImages,
    ];
@endphp
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Actualizar examen</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hcl.exams.home') }}">Exámenes</a></li>
                            <li class="breadcrumb-item active">Actualizar examen</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        EXAMEN DE {{ $hc->dni . ' :: ' . $hc->nombres }}
                    </div>
                    <div id="vue-exam-form"></div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        window.__EXAM_FORM_STATE__ = @json($formState);
    </script>
    @vite(['resources/js/modules/exam-form.js'])
@endpush
