@extends('layouts.app')
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
                    INFORME DE {{ $hc->dni.' :: '.$hc->nombres }}
                </div>
                <form id="reportForm" method="post">
                    @method('POST')
                    @csrf
                    @include('hcl.reports.partials.form')
                </form>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('js/forms/reports.js') }}"></script>
@endsection
