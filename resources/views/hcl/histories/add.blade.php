@extends('layouts.app')
@section('title', config('global.site_name').' - Nueva historia') <!-- Título dinámico -->
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Nueva historia</h1>
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
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Filiación</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <form method="post" id="formHC">
                    @method('POST')
                    @csrf
                    @include('hcl.histories.partials.form')
                </form>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('js/forms/histories.js') }}"></script>
@endsection
