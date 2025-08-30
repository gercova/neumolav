@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Nuevo control</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hcl.appointments.home') }}">Controles</a></li>
                        <li class="breadcrumb-item active">Nuevo control</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    NUEVO CONTROL DE {{ $hc[0]['dni'].' :: '.$hc[0]['nombres'] }}
                </div>
                <form id="appointmentForm" method="post">
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
                                                    <h5 class="card-title">Datos de control</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <label class="col-sm-2 col-form-label" for="sintomas">Síntomas:</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control form-control-sm" id="sintomas" name="sintomas" rows="2">{{ old('sintomas') }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <label class="col-sm-2 col-form-label" for="plan">Plan:</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control form-control-sm" id="plan" name="plan" rows="2">{{ old('plan') }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <label class="col-sm-2 col-form-label" for="tratamiento">Tratamiento:</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control form-control-sm" id="tratamiento" name="tratamiento" rows="2">{{ old('tratamiento') }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                        <div class="col-12">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h5 class="card-title">Diagnósticos</h5>
                                                </div>
                                                <div class="card-body">
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h5 class="card-title">Tratamiento</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label" for="drugs">Buscar:</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control form-control-sm" id="drugs" placeholder="Buscar por descripción">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="form-group row">
                                                                <button id="btn-add-drug" type="button" class="btn btn-primary btn-block"><i class="bi bi-plus-circle"></i> Agregar fármaco</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <table id="tableDrugs" class="table table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:30%;">Fármaco</th>
                                                                        <th style="width:60%;">Receta</th>
                                                                        <th style="width:10%;"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <label class="col-sm-2 col-form-label" for="recomendaciones">Recomendaciones:</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control form-control-sm" id="recomendaciones" name="recomendaciones" rows="2">{{ old('recomendaciones') }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endrole
                                @role('asistente')
                                    <p class="alert alert-danger">
                                        No tienes permisos para crear controles, consulte con el administrador del sistema.
                                    </p>
                                @endrole
                            </div>
                            <div class="col-5">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h5 class="card-title">Ver recetas anteriores</h5>
                                    </div>
                                    <div class="card-body">
                                        <h4 class="text-center">Ver datos</h4>
                                        <hr>
                                        <div class="form-group">
                                            <select class="form-control" id="search-table">
                                                <option value="">Selecciona tabla</option>
                                                <option value="controles">Controles</option>
                                                <option value="examenes">Exámenes</option>
                                            </select>
                                            <div class="table-responsive" id="table_data"></div>
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
<script src="{{ asset('js/forms/appointments.js') }}"></script>
<script src="{{ asset('js/forms/modalDetails.js') }}"></script>
@endsection