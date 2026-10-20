@extends('layouts.app')
@section('title', config('global.site_name') . ' - Nueva historia')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vanilla-datepicker.css') }}">
@endsection
@php
    $formState = [
        'isEdit'    => false,
        'canSeeClinical' => auth()
            ->user()
            ->hasAnyRole(['administrador', 'especialista']),
        'form'      => [
            'id_tipo_atencion' => 1,
        ],
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
                        <h1 class="m-0">Nueva historia clínica</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('hcl.histories.home') }}">Historias</a></li>
                            <li class="breadcrumb-item active">Nueva historia</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-plus mr-1"></i> Filiación y Datos Generales</h3>
                    </div>
                    <div id="vue-history-form">
                        {{-- Vue montará aquí --}}
                    </div>
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
