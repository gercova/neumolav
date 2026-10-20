@extends('layouts.app')
@section('title', config('global.site_name') . ' - Actualizar historia')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vanilla-datepicker.css') }}">
@endsection
@php
    $formState      = [
        'isEdit'    => true,
        'canSeeClinical' => auth()
            ->user()
            ->hasAnyRole(['administrador', 'especialista']),
        'form'      => array_merge($history->toArray(), [
            'fecha_nacimiento'  => $history->fecha_nacimiento?->format('Y-m-d'),
            'ubigeo_nacimiento' => $unacimiento[0]['nacimiento'] ?? '',
            'ubigeo_residencia' => $uresidencia[0]['residencia'] ?? '',
            'id_ocupacion'      => $occupation[0]['occupation'] ?? '',
        ]),
        'catalog'   => [
            'documentTypes'     => $dt,
            'sexes'             => $sx,
            'bloodGroups'       => $bg,
            'degrees'           => $di,
            'maritalStatuses'   => $ms,
            'smokingTypes'      => $tb,
        ],
    ];
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Actualizar historia clínica</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('hcl.histories.home') }}">Historias</a></li>
                            <li class="breadcrumb-item active">Actualizar historia</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-warning card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-edit mr-1"></i> Filiación y Datos del Paciente</h3>
                    </div>
                    <div id="vue-history-form"></div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/vanilla-datepicker.js') }}"></script>
    <script>
        window.__HISTORY_FORM_STATE__ = @json($formState);
    </script>
    @vite(['resources/js/modules/history-form.js'])
@endpush
