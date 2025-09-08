@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Actualizar historia</h1>
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
                    <input type="hidden" name="id" id="id" value="{{ $history->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="id_td">Tipo Documento</label>
                                    <select class="form-control" id="id_td" name="id_td" required>
                                        <option value="">-- Seleccione --</option>
                                        @foreach($dt as $t)
                                            <option value="{{ $t->id }}" {{ $history->id_td == $t->id ? 'selected' : '' }}>{{ $t->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="dni">DNI</label>
                                    <input type="number" class="form-control" id="dni" name="dni" value="{{ $history->dni }}" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="nombres">Nombres</label>
                                    <input type="text" class="form-control" id="nombres" name="nombres" value="{{ $history->nombres }}" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="fecha_nacimiento">Fecha Nacimiento</label>
                                    <input type="date:yyyy-mm-dd" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ $history->fecha_nacimiento }}" onchange="getAge(this.value);" required><!--onchange="getAge(this.value);"-->
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="age">Edad</label>
                                    <input type="text" class="form-control" id="age" name="age" value="{{ $occupation[0]['age'] }}" required readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="id_sexo">Sexo</label>
                                    <select class="form-control" id="id_sexo" name="id_sexo" required>
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($sx as $s)
                                            <option value="{{ $s->id }}" {{ $history->id_sexo == $s->id ? 'selected' : '' }}>{{ $s->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>  
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="telefono">Celular</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $history->telefono }}" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="id_gs">Grupo sanguíneo</label>
                                    <select class="form-control" id="id_gs" name="id_gs" required>
                                        <option value="">-- Seleccione --</option>
                                        @foreach($bg as $b)
                                            <option value="{{ $b->id }}" {{ $history->id_gs == $b->id ? 'selected' : '' }}>{{ $b->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            @if($history->ubigeo_extranjero !== null)
                                <div class="col-5">
                                    <div class="form-group">
                                        <label for="extranjero">Ubigeo extranjero</label>
                                        <input class="form-control" id="extranjero" name="extranjero" value="{{ $history->ubigeo_extranjero }}" placeholder="PAÍS, REGIÓN, CIUDAD">
                                        <input type="hidden" name="ubigeo_nacimiento" id="ubigeo_nacimiento" value="{{ $unacimiento[0]['nacimiento'] }}">
                                    </div>
                                </div>
                            @else
                                <div class="col-2">
                                    <div class="form-group">
                                        <br>
                                        <button type="button" class="btn btn-warning extra"><i class="fa fa-globe"></i> Paciente extranjero</button>
                                        <button type="button" class="btn btn-success pe d-none"><i class="fa fa-globe"></i> Paciente nacional</button>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group nacional">
                                        <label for="ubigeo_nacimiento">Lugar Nacimiento: </label>
                                        <select class="form-control buscarUbigeoR" id="ubigeo_nacimiento" name="ubigeo_nacimiento">
                                            <option value="{{ $unacimiento[0]['nacimiento'] }}"></option>
                                        </select>
                                    </div>
                                    <div class="form-group foreign d-none">
                                        <label for="extranjero">Ubigeo extranjero</label>
                                        <input class="form-control" id="extranjero" name="extranjero" value="{{ $history->ubigeo_extranjero }}" placeholder="PAÍS, REGIÓN, CIUDAD">
                                    </div>
                                </div>
                            @endif
                            
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="ubigeo_residencia">Lugar Residencia: </label>
                                    <select class="form-control buscarUbigeoR" id="ubigeo_residencia" name="ubigeo_residencia" required>
                                        <option value="{{ $uresidencia[0]['residencia'] }}"></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="id_gi">Grado instrucción</label>
                                    <select class="form-control" id="id_gi" name="id_gi" required>
                                        <option value="">-- Seleccione --</option>
                                        @foreach($di as $g)
                                            <option value="{{ $g->id }}" {{ $history->id_gi == $g->id ? 'selected' : '' }}>{{ $g->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="id_ocupacion">Ocupación</label>
                                    <select class="form-control buscarOcupacion" id="id_ocupacion" name="id_ocupacion" required>
                                        <option value="{{ $occupation[0]['occupation'] }}"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="id_estado">Estado civil</label>
                                    <select class="form-control" id="id_estado" name="id_estado" required>
                                        <option value="">-- Seleccione --</option>
                                        @foreach($ms as $m)
                                            <option value="{{ $m->id }}" {{ $history->id_estado == $m->id ? 'selected' : '' }}>{{ $m->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        @role('administrador|especialista')
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="surgeries">Cirugías</label>
                                        <input type="text" class="form-control" name="surgeries" id="surgeries" value="{{ $history->cirugias }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="transfusions">Transfusiones sanguíneas</label>
                                        <input type="text" class="form-control" name="transfusions" id="transfusions" value="{{ $history->transfusiones }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="trauma">Traumatismos</label>
                                        <input type="text" class="form-control" name="trauma" id="trauma" value="{{ $history->traumatismos }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="hospitalizations">Hospitalizaciones previas</label>
                                        <input type="text" class="form-control" name="hospitalizations" id="hospitalizations" value="{{ $history->hospitalizaciones }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="drugs">Consumo de drogas sociales</label>
                                        <input type="text" class="form-control" name="drugs" id="drugs" value="{{ $history->drogas }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="fh">Antecedentes familiares</label>
                                        <input type="text" class="form-control" name="fh" id="fh" value="{{ $history->antecedentes }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="bs">Estado basal</label>
                                        <input type="text" class="form-control" name="bs" id="bs" value="{{ $history->estadobasal }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="medication">RAMS</label>
                                        <input type="text" class="form-control" name="medication" id="medication" value="{{ $history->medicacion }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="animals">Animales en casa</label>
                                        <input type="text" class="form-control" name="animals" id="animals" value="{{ $history->animales }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="otros">Otros</label>
                                        <textarea class="form-control" cols="96" name="others" id="otros">{{ $history->otros }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="ba">Asma bronquial</label>
                                        <input type="text" class="form-control" name="ba" id="ba" value="{{ $history->asmabronquial }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="epoc">EPOC</label>
                                        <input type="text" class="form-control" name="epoc" id="epoc" value="{{ $history->epoc }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="epid">EPID</label>
                                        <input type="text" class="form-control" name="epid" id="epid" value="{{ $history->epid }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="pt">Tuberculosis</label>
                                        <input type="text" class="form-control" name="pt" id="pt" value="{{ $history->tuberculosis }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="lg">Cáncer al pulmón</label>
                                        <input type="text" class="form-control" name="lg" id="lg" value="{{ $history->cancerpulmon }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="pe">Efusión pleural</label>
                                        <input type="text" class="form-control" name="pe" id="pe" value="{{ $history->efusionpleural }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="pneumonia">Neumonías</label>
                                        <input type="text" class="form-control" name="pneumonia" id="pneumonia" value="{{ $history->neumonias }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="smoking">Tabaquismo</label>
                                        <input type="text" class="form-control" name="smoking" id="smoking" value="{{ $history->tabaquismo }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="tipo">Seleccione tipo de consumo de tabaco</label>
                                        <select class="form-control" name="tipo" id="tipo">
                                            <option value="">-- Seleccione --</option>
                                            @foreach($tb as $t)
                                                <option value="{{ $t->id }}" {{ $history->id_ct == $t->id ? 'selected' : '' }}>{{ $t->consumo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="cig">Cigarros x día</label>
                                        <input type="number" class="form-control" name="cig" min="0" value="0" id="cig" value="{{ $history->cig }}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="af">Años fumando</label>
                                        <input type="number" class="form-control" name="af" min="0" value="0" id="af" value="{{ $history->aniosfum }}">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <label for="r">Resultado</label>
                                        <input type="number" class="form-control" name="r" min="0" step="any" value="0" id="r" value="{{ $history->result }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="tbcc">Contacto TBC / COVID</label>
                                        <input type="text" class="form-control" name="tbcc" id="tbcc" value="{{ $history->contactotbc }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="be">Exposición a biomasa</label>
                                        <input type="text" class="form-control" name="be" id="be" value="{{ $history->exposicionbiomasa }}">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="rfc">Motivo consulta</label>
                                        <input type="text" class="form-control" name="rfc" id="rfc" value="{{ $history->motivoconsulta }}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="cs">Síntomas cardinales</label>
                                        <input type="text" class="form-control" name="cs" id="cs" value="{{ $history->sintomascardinales }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="te">TE</label>
                                        <input type="text" class="form-control" name="te" id="te" value="{{ $history->te }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="fi">FI</label>
                                        <input type="text" class="form-control" name="fi" id="fi" value="{{ $history->fi }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="c">C</label>
                                        <input type="text" class="form-control" name="c" id="c" value="{{ $history->c }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="ca">Relato cronológico</label>
                                        <textarea class="form-control" name="ca" id="ca" cols="96" rows="3">{{ $history->relatocronologico }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endrole
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
<script src="{{ asset('js/forms/histories.js') }}"></script>
@endsection