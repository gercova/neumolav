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
                    NUEVO INFORME DE {{ $hc[0]['dni'].' :: '.$hc[0]['nombres'] }}
                </div>
                <form id="reportForm" method="post">
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
                                                            <label class="col-form-label" for="antecedentes">Antecedentes:</label>
                                                            <textarea class="form-control form-control-sm summernote" id="antecedentes" name="antecedentes" rows="2">{{ old('antecedentes') }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="historial_enfermedad">Historial de enfermedad:</label>
                                                            <textarea class="form-control form-control-sm summernote" id="historial_enfermedad" name="historial_enfermedad" rows="2">{{ old('historial_enfermedad') }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="examen_fisico">Examen físico:</label>
                                                            <textarea class="form-control form-control-sm summernote" id="examen_fisico" name="examen_fisico" rows="2">{{ old('examen_fisico') }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="examen_complementario">Examen complementario:</label>
                                                            <textarea class="form-control form-control-sm summernote" id="examen_complementario" name="examen_complementario" rows="2">{{ old('examen_complementario') }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-8">
                                                                    <div class="form-group row">
                                                                        <label class="col-sm-3 col-form-label" for="diagnostics">Buscar:</label>
                                                                        <div class="col-sm-9">
                                                                            <input type="text" class="form-control form-control-sm" id="diagnostics" placeholder="Buscar por código o nombre">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="form-group row">
                                                                        <button id="btn-add-diagnostic" type="button" class="btn btn-primary btn-block"><i class="bi bi-plus-circle"></i> Agregar diagnóstico</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <table id="tableDiagnostics" class="table table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:90%;">Diagnóstico</th>
                                                                        <th style="width:10%;"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                            <hr>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="tratamiento">Tratamiento:</label>
                                                            <textarea class="form-control form-control-sm summernote" id="tratamiento" name="tratamiento" rows="2">{{ old('tratamiento') }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="col-form-label" for="sugerencia">Sugerencia:</label>
                                                            <textarea class="form-control form-control-sm summernote" id="sugerencia" name="sugerencia" rows="2">{{ old('sugerencia') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endrole
                                @role('asistente')
                                    <p class="alert alert-danger">
                                        No tienes permisos para crear informes, consulte con el administrador del sistema.
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
                                                <table id="reportsByDNI" class="table table-striped table-hover">
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
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('js/forms/reports.js') }}"></script>
@endsection