@extends('layouts.app')
@section('title', config('global.site_name').' - Actualizar control') <!-- Título dinámico -->
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
                    ACTUALIZAR CONTROL DE {{ $hc->dni.' :: '.$hc->nombres }}
                </div>
                <form id="appointmentForm" method="post">
                    @method('POST')
                    @csrf
                    @include('hcl.appointments.partials.form')
                </form>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('js/forms/appointments.js') }}"></script>
<script src="{{ asset('js/forms/modalDetails.js') }}"></script>
@endsection
