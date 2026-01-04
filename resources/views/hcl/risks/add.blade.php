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
                        <li class="breadcrumb-item"><a href="{{ route('hcl.risks.home') }}">Informes</a></li>
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
                    NUEVO INFORME DE {{ $hc->dni.' :: '.$hc->nombres }}
                </div>
                <form id="riskForm" method="post">
                    @method('POST')
                    @csrf
                    @include('hcl.risks.partials.form')
                </form>
            </div>
        </div>
    </section>
</div>
<script>
    // Inicializar todos los editores con clase "ckeditor"
    document.querySelectorAll('.ckeditor').forEach(textarea => {
        ClassicEditor
            .create(textarea)
            .catch(error => {
                console.error('Error en editor:', error);
            });
    });
</script>
<script src="{{ asset('js/forms/risks.js') }}"></script>
@endsection
