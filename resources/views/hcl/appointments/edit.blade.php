@extends('layouts.app')
@section('title', config('global.site_name') . ' - Actualizar control')
@php
    $existingDiagnostics = $ap->diagnostics
        ->map(
            fn($d) => [
                'id'            => $d->id,
                'descripcion'   => $d->diagnostic->descripcion ?? '--',
            ],
        )
        ->values()
        ->all();

    $existingMedications = $ap->medication
        ->map(
            fn($m) => [
                'id'            => $m->id,
                'descripcion'   => $m->drug->descripcion ?? '--',
                'rp'            => $m->descripcion ?? '',
            ],
        )
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
            'createdAt' => $ap->created_at?->format('d/m/Y H:i') ?? '',
        ],
        'form' => array_merge($ap->toArray(), [
            'appointmentId' => $ap->id,
            'id_historia'   => $hc->id,
        ]),
        'diagnostics' => $existingDiagnostics,
        'medications' => $existingMedications,
    ];
@endphp
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Actualizar control</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hcl.appointments.home') }}">Controles</a></li>
                            <li class="breadcrumb-item active">Actualizar control</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        ACTUALIZAR CONTROL DE {{ $hc->dni . ' :: ' . $hc->nombres }}
                    </div>
                    <div id="vue-appointment-form"></div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        window.__APPOINTMENT_FORM_STATE__ = @json($formState);
    </script>
    @vite(['resources/js/modules/appointment-form.js'])
    <script src="{{ asset('js/forms/modalDetails.js') }}"></script>
@endpush
