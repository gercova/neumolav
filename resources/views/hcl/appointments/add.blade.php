@extends('layouts.app')
@section('title', config('global.site_name') . ' - Nuevo control')
@php
    $formState = [
        'meta' => [
            'isEdit'    => false,
            'canEdit'   => auth()
                ->user()
                ->hasAnyRole(['administrador', 'especialista']),
            'patient'   => ['id' => $hc->id, 'dni' => $hc->dni, 'nombres' => $hc->nombres],
            'userName'  => Auth::user()->name,
            'createdAt' => now()->format('d/m/Y H:i'),
        ],
        'form' => [
            'id_historia'   => $hc->id,
            'dni'           => $hc->dni,
        ],
        'diagnostics' => [],
        'medications' => [],
    ];
@endphp
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Nuevo control</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hcl.appointments.home') }}">Controles</a></li>
                            <li class="breadcrumb-item active">Nuevo control</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        NUEVO CONTROL DE {{ $hc->dni . ' :: ' . $hc->nombres }}
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
