@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Nuevo examen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('hcl.exams.home') }}">Exámenes</a></li>
                        <li class="breadcrumb-item active">Nuevo examen</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    NUEVO EXAMEN DE {{ $hc[0]['dni'].' :: '.$hc[0]['nombres'] }}
                </div>
                <form id="examForm" method="post" enctype="multipart/form-data">
                    @csrf
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
                                    <input type="hidden" name="dni" value="{{ $hc[0]['dni'] }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="id_tipo">Tipo consulta</label>
                                    <select class="form-control" name="id_tipo" id="id_tipo" required>
                                        <option value="">-- Seleccione --</option>
                                        @foreach($te as $t)
                                            <option value="{{ $t->id }}">{{ $t->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @role('administrador|especialista')
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h5 class="card-title">Examen físico</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="ta">TA</label>
                                                        <input type="text" class="form-control form-control-sm" id="ta" name="ta" value="{{ old('ta') }}">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="fc">FC</label>
                                                        <input type="text" class="form-control form-control-sm" id="fc" name="fc" value="{{ old('fc') }}">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="rf">RF</label>
                                                        <input type="text" class="form-control form-control-sm" id="rf" name="rf" value="{{ old('rf') }}">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="so2">SO2</label>
                                                        <input type="text" class="form-control form-control-sm" id="so2" name="so2" value="{{ old('so2') }}">
                                                    </div>
                                                </div>
                                                <div class="col-1">
                                                    <div class="form-group">
                                                        <label for="peso">Peso (kg)</label>
                                                        <input type="text" class="form-control form-control-sm" id="peso" name="peso">
                                                    </div>
                                                </div>
                                                <div class="col-1">
                                                    <div class="form-group">
                                                        <label for="talla">Talla (m)</label>
                                                        <input type="text" class="form-control form-control-sm" id="talla" name="talla">
                                                    </div>
                                                </div>
                                                <div class="col-1">
                                                    <div class="form-group">
                                                        <label for="imc">IMC: </label>
                                                        <input type="text" class="form-control form-control-sm" id="imc" name="imc" step="0.01" onkeypress="return imc(event);" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="pym">PYM: </label>
                                                        <input type="text" class="form-control form-control-sm" id="pym" name="pym" value="{{ old('pym') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="typ">TYP: </label>
                                                        <input type="text" class="form-control form-control-sm" id="typ" name="typ" value="{{ old('typ') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="cv">CV: </label>
                                                        <input type="text" class="form-control form-control-sm" id="cv" name="cv" value="{{ old('cv') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="abdomen">Abdomen: </label>
                                                        <input type="text" class="form-control form-control-sm" id="abdomen" name="abdomen" value="{{ old('abdomen') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="hemolinfopoyetico">Hemolinfopoyetico: </label>
                                                        <input type="text" class="form-control form-control-sm" id="hemolinfopoyetico" name="hemolinfopoyetico" value="{{ old('hemolinfopoyetico') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="tcs">TCS: </label>
                                                        <input type="text" class="form-control form-control-sm" id="tcs" name="tcs" value="{{ old('tcs') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="neurologico">Neurológico: </label>
                                                        <input type="text" class="form-control form-control-sm" id="neurologico" name="neurologico" value="{{ old('neurologico') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-12">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h5 class="card-title">Examen auxiliar</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="hemograma">Hemograma:</label>
                                                        <input type="text" class="form-control form-control-sm" id="hemograma" name="hemograma" value="{{ old('hemograma') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="bioquimico">Bioquímico:</label>
                                                        <input type="text" class="form-control form-control-sm" id="bioquimico" name="bioquimico" value="{{ old('bioquimico') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="perfilhepatico">Perfil hepático:</label>
                                                        <input type="text" class="form-control form-control-sm" id="perfilhepatico" name="perfilhepatico" value="{{ old('perfilhepatico') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="perfilcoagulacion">Perfil coagulación</label>
                                                        <input type="text" class="form-control form-control-sm" id="perfilcoagulacion" name="perfilcoagulacion" value="{{ old('perfilcoagulacion') }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="perfilreumatologico">Perfil reumatológico</label>
                                                        <input type="text" class="form-control form-control-sm" id="perfilreumatologico" name="perfilreumatologico" value="{{ old('perfilreumatologico') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h5 class="card-title">Microbiología</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="orina">Orina:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="orina" name="orina" rows="1">{{ old('orina') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="sangre">Sangre:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="sangre" name="sangre" rows="1">{{ old('sangre') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="esputo">Esputo:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="esputo" name="esputo" rows="1">{{ old('esputo') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="heces">Heces:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="heces" name="heces" rows="1">{{ old('heces') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="lcr">LCR:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="lcr" name="lcr" rows="1">{{ old('lcr') }}</textarea>
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
                                            <h5 class="card-title">Líquido pleural</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="citoquimico">Citoquímico</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="citoquimico" name="citoquimico" rows="1">{{ old('citoquimico') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="adalp">ADA:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="adalp" name="adalp" rows="1">{{ old('adalp') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="paplp">PAP:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="paplp" name="paplp" rows="1">{{ old('paplp') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="bclp">Block Cell:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="bclp" name="bclp" rows="1">{{ old('bclp') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="cgchlp">Cultivo GCH:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="cgchlp" name="cgchlp" rows="1">{{ old('cgchlp') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="cbklp">Cultivo BK:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="cbklp" name="cbklp" rows="1">{{ old('cbklp') }}</textarea>
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
                                            <h5 class="card-title">Aspirado bronquial</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="bkdab">BK Directo</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="bkdab" name="bkdab" rows="1">{{ old('bkdab') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="bkcab">BK Cultivo:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="bkcab" name="bkcab" rows="1">{{ old('bkcab') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="cgchab">Cultivo GCH:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="cgchab" name="cgchab" rows="1">{{ old('cgchab') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="papab">PAP:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="papab" name="papab" rows="1">{{ old('papab') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="bcab">Block Cell:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="bcab" name="bcab" rows="1">{{ old('bcab') }}</textarea>
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
                                            <h5 class="card-title">Biopsia pleuro pulmonar</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="pulmon">Pulmón:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="pulmon" name="pulmon" rows="1">{{ old('pulmon') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="pleurabpp">Pleura:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="pleurabpp" name="pleurabpp" rows="1">{{ old('pleurabpp') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card card-info">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="funcionpulmonar">Funcion pulmonar:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="funcionpulmonar" name="funcionpulmonar" rows="1">{{ old('funcionpulmonar') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="medicinanuclear">Medicina nuclear:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="medicinanuclear" name="medicinanuclear" rows="1">{{ old('medicinanuclear') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label" for="medicinanuclear">Otros:</label>
                                                        <div class="col-sm-10">
                                                            <textarea class="form-control form-control-sm" id="otros" name="otros" rows="1">{{ old('otros') }}</textarea>
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
                                <div class="col-12">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h5 class="card-title">Exámenes radiológicos</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group row">
                                                        <button id="btn-add-img" type="button" class="btn btn-primary btn-block"><i class="bi bi-plus-circle"></i> Agregar campo</button>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <table id="tableImg" class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Imagen</th>
                                                                <th>Fecha de la radiografía</th>
                                                                <th></th>
                                                            </tr>
                                                        <thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endrole
                        @role('asistente')
                            <p class="alert alert-danger">
                                No tienes permisos para crear exámenes, consulte con el administrador del sistema.
                            </p>
                        @endrole
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
<script src="{{ asset('js/forms/exams.js') }}"></script>
@endsection