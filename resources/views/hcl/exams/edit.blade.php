@extends('layouts.app')
@section('title', config('global.site_name').' - Actualizar examen') <!-- Título dinámico -->
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
                    EXAMEN DE {{ $hc->dni.' :: '.$hc->nombres }}
                </div>
                <form id="examForm" method="post" enctype="multipart/form-data">
                    @method('POST')
                    @csrf
                    @include('hcl.exams.partials.form')
                </form>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('js/forms/exams.js') }}"></script>
@endsection
