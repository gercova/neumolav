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
                    NUEVO INFORME DE {{ $hc[0]['dni'].' :: '.$hc[0]['nombres'] }}
                </div>
                <form id="riskForm" method="post">
                    <input type="hidden" name="riskId" id="riskId" value="{{ $rk->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Atendido por:</label>
                                    <div class="col-sm-8">
                                        <input type="text" value="{{ Auth::user()->name }}" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>DNI :: NOMBRES</label>
                                    <input type="text" class="form-control" value="{{ $hc[0]['dni'].' :: '.$hc[0]['nombres'] }}" readonly>
                                    <input type="hidden" name="dni" id="dni" value="{{ $hc[0]['dni'] }}">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-7">
                                @role('administrador|especialista')
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h5 class="card-title">Datos del informe</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="motivo">Motivo:</label>
                                                            <textarea class="form-control form-control-sm ckeditor" id="motivo" name="motivo" rows="2">{{ $rk->motivo }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="antecedente">Antecedente:</label>
                                                            <textarea class="form-control form-control-sm ckeditor" id="antecedente" name="antecedente" rows="2">{{ $rk->antecedente }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="sintomas">Síntomas:</label>
                                                            <textarea class="form-control form-control-sm ckeditor" id="sintomas" name="sintomas" rows="2">{{ $rk->sintomas }}</textarea>        
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="examen_fisico">Examen físico:</label>
                                                            <textarea class="form-control form-control-sm ckeditor" id="examen_fisico" name="examen_fisico" rows="2">{{ $rk->examen_fisico }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="examen_complementario">Examen complementario:</label>
                                                            <textarea class="form-control form-control-sm ckeditor" id="examen_complementario" name="examen_complementario" rows="2">{{ $rk->examen_complementario }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="riesgo_neumologico">Riesgo neumológico:</label>
                                                            <textarea class="form-control form-control-sm ckeditor" id="riesgo_neumologico" name="riesgo_neumologico" rows="2">{{ $rk->riesgo_neumologico }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="sugerencia">Sugerencia:</label>
                                                            <textarea class="form-control form-control-sm ckeditor" id="sugerencia" name="sugerencia" rows="2">{{ $rk->sugerencia }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                    </div>
                                @endrole
                                @role('asistente')
                                    <p class="alert alert-danger">
                                        No tienes permisos para editar este registro, consulte con el administrador del sistema.
                                    </p>
                                @endrole
                            </div>
                            <div class="col-5">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h5 class="card-title">Informes anteriores</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <table id="risksByDNI" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 10%">#</th>
                                                            <th style="width: 45%">Fecha</th>
                                                            <th style="width: 45%">Opciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Guardar</button>
                        </div>
                    </div>
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