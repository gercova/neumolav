@extends('layouts.app')
@section('title', config('global.site_name') . ' - Nuevo informe')
@section('styles')
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
@endsection
@php
    $formState = [
        'meta' => [
            'isEdit'    => false,
            'canEdit'   => auth()->user()->hasAnyRole(['administrador', 'especialista']),
            'patient'   => ['id' => $hc->id, 'dni' => $hc->dni, 'nombres' => $hc->nombres],
            'userName'  => Auth::user()->name,
        ],
        'form' => [
            'id_historia'   => $hc->id,
            'dni'           => $hc->dni,
        ],
    ];
@endphp
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Nuevo informe</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('hcl.reports.home') }}">Informes</a></li>
                            <li class="breadcrumb-item active">Nuevo informe</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        NUEVO INFORME DE {{ $hc->dni . ' :: ' . $hc->nombres }}
                    </div>
                    <div id="vue-report-form"></div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        window.__REPORT_FORM_STATE__ = @json($formState);
    </script>
    @vite(['resources/js/modules/report-form.js'])
    <script src="{{ asset('js/forms/modalDetails.js') }}"></script>
@endpush
