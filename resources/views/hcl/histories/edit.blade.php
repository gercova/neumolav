@extends('layouts.app')
@section('title', config('global.site_name') . ' - Actualizar historia') <!-- Título dinámico -->
@section('content')
    <link rel="stylesheet" href="{{ asset('css/vanilla-datepicker.css') }}">
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
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card card-warning card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-edit mr-1"></i> Filiación y Datos del Paciente</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form method="post" id="formHC" novalidate>
                        @method('POST')
                        @csrf
                        @include('hcl.histories.partials.form')
                    </form>
                </div>
            </div>
        </section>
    </div>
    @include('hcl.histories.partials.modal_occupation')
    <script src="{{ asset('js/vanilla-datepicker.js') }}"></script>
    <script src="{{ asset('js/forms/histories.js') }}"></script>
@endsection
