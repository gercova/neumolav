@extends('layouts.app')
@section('title', config('global.site_name') . ' - Nuevo examen')
@php
    $formState = [
        'meta' => [
            'isEdit'    => false,
            'canEdit'   => auth()
                ->user()
                ->hasAnyRole(['administrador', 'especialista']),
            'patient'   => ['id' => $hc->id, 'dni' => $hc->dni, 'nombres' => $hc->nombres],
            'userName'  => Auth::user()->name,
            'createdAt' => now()->format('Y-m-d'),
        ],
        'form' => [
            'id_historia'   => $hc->id,
            'dni'           => $hc->dni,
        ],
        'catalog' => [
            'examTypes' => $te,
        ],
    ];
@endphp
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Nuevo examen</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('hcl.exams.home') }}">Exámenes</a></li>
                            <li class="breadcrumb-item active">Nuevo examen</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        NUEVO EXAMEN DE {{ $hc->dni . ' :: ' . $hc->nombres }}
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
