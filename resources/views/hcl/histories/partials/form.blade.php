@method('POST')
@csrf
<input type="hidden" name="id" id="id" value="{{ $history->id ?? '' }}">
<div class="card-body">
    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label for="id_td">Tipo Documento</label>
                <select class="form-control" id="id_td" name="id_td" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($dt as $t)
                        <option value="{{ $t->id }}" {{ (old('id_td', $history->id_td ?? '') == $t->id) ? 'selected' : '' }}>{{ $t->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" class="form-control" id="dni" name="dni" value="{{ old('dni', $history->dni ?? '') }}" required>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" class="form-control" id="nombres" name="nombres" value="{{ old('nombres', $history->nombres ?? '') }}" required>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', isset($history->fecha_nacimiento) ? $history->fecha_nacimiento->format('Y-m-d') : '') }}" onchange="getAge(this.value);" max="{{ now()->format('Y-m-d') }}" required>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label for="age">Edad</label>
                <input type="text" class="form-control" id="age" name="age" value="{{ old('age') }}" required readonly>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label for="id_sexo">Sexo</label>
                <select class="form-control" id="id_sexo" name="id_sexo" required>
                    <option value="">-- Seleccione --</option>
                    @foreach ($sx as $s)
                        <option value="{{ $s->id }}" {{ (old('id_sexo', $history->id_sexo ?? '') == $s->id) ? 'selected' : '' }}>{{ $s->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label for="telefono">Celular</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $history->telefono ?? '') }}" required>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label for="id_gs">Grupo sanguíneo</label>
                <select class="form-control" id="id_gs" name="id_gs" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($bg as $b)
                        <option value="{{ $b->id }}" {{ (old('id_gs', $history->id_gs) == $b->id) ? 'selected' : '' }}>{{ $b->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <br>
                <button type="button" class="btn btn-warning extra"><i class="fa fa-globe"></i> Paciente extranjero</button>
                <button type="button" class="btn btn-success pe d-none"><i class="fa fa-globe"></i> Paciente nacional</button>
            </div>
        </div>
        <div class="col-5">
            <div class="form-group nacional {{ isset($history) ? (isset($history->ubigeo_extranjero) ? 'd-none' : '') : '' }}">
                <label for="ubigeo_nacimiento">Lugar Nacimiento: </label>
                <select class="form-control buscarUbigeoR" id="ubigeo_nacimiento" name="ubigeo_nacimiento">
                    @if (isset($history))
                        <option value="{{ $unacimiento[0]['nacimiento'] }}"></option>
                    @endif
                </select>
            </div>
            <div class="form-group foreign {{ isset($history) ? (is_null($history->ubigeo_extranjero) ? 'd-none' : '') : 'd-none' }} ">
                <label for="extranjero">Ubigeo extranjero</label>
                <input class="form-control" id="extranjero" name="extranjero" placeholder="PAÍS, REGIÓN, CIUDAD" value="{{ old('extranjero', $history->ubigeo_extranjero ?? '') }}">
            </div>
        </div>
        <div class="col-5">
            <div class="form-group">
                <label for="ubigeo_residencia">Lugar Residencia: </label>
                <select class="form-control buscarUbigeoR" id="ubigeo_residencia" name="ubigeo_residencia" required>
                    @if (isset($history))
                        <option value="{{ $uresidencia[0]['residencia'] }}"></option>
                    @endif
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
                        <option value="{{ $g->id }}" {{ (old('id_gi', $history->id_gi ?? '') == $g->id) ? 'selected' : '' }}>{{ $g->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="id_ocupacion">Ocupación</label>
                <select class="form-control buscarOcupacion" id="id_ocupacion" name="id_ocupacion" required>
                    @if (isset($history))
                        <option value="{{ isset($history) ? $occupation[0]['occupation'] : '' }}"></option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label for="id_estado">Estado civil</label>
                <select class="form-control" id="id_estado" name="id_estado" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($ms as $m)
                        <option value="{{ $m->id }}" {{ (old('id_estado', $history->id_estado ?? '') == $m->id ) ? 'selected' : '' }}>{{ $m->descripcion }}</option>
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
                    <label for="cirugias">Cirugías</label>
                    <input type="text" class="form-control" name="cirugias" id="cirugias" value="{{ old('cirugias', $history->cirugias ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="transfusiones">Transfusiones sanguíneas</label>
                    <input type="text" class="form-control" name="transfusiones" id="transfusiones" value="{{ old('transfusiones', $history->transfusiones ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="traumatismos">Traumatismos</label>
                    <input type="text" class="form-control" name="traumatismos" id="traumatismos" value="{{ old('traumatismos', $history->traumatismos ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="hospitalizaciones">Hospitalizaciones previas</label>
                    <input type="text" class="form-control" name="hospitalizaciones" id="hospitalizaciones" value="{{ old('hospitalizaciones', $history->hospitalizaciones ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="drogas">Consumo de drogas sociales</label>
                    <input type="text" class="form-control" name="drogas" id="drogas" value="{{ old('drogas', $history->drogas ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="antecedentes">Antecedentes familiares</label>
                    <input type="text" class="form-control" name="antecedentes" id="antecedentes" value="{{ old('antecedentes', $history->antecedentes ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="estadobasal">Estado basal</label>
                    <input type="text" class="form-control" name="estadobasal" id="estadobasal" value="{{ old('estadobasal', $history->estadobasal ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="medicacion">RAMS</label>
                    <input type="text" class="form-control" name="medicacion" id="medicacion" value="{{ old('medicacion', $history->medicacion ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="animales">Animales en casa</label>
                    <input type="text" class="form-control" name="animales" id="animales" value="{{ old('animales', $history->animales ?? '') }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="otros">Otros</label>
                    <textarea class="form-control" cols="96" name="otros" id="otros">{{ old('otros', $history->otros ?? '') }}</textarea>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label for="asmabronquial">Asma bronquial</label>
                    <input type="text" class="form-control" name="asmabronquial" id="asmabronquial" value="{{ old('asmabronquial', $history->asmabronquial ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="epoc">EPOC</label>
                    <input type="text" class="form-control" name="epoc" id="epoc" value="{{ old('epoc', $history->epoc ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="epid">EPID</label>
                    <input type="text" class="form-control" name="epid" id="epid" value="{{ old('epid') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="tuberculosis">Tuberculosis</label>
                    <input type="text" class="form-control" name="tuberculosis" id="tuberculosis" value="{{ old('tuberculosis', $history->tuberculosis ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="cancerpulmon">Cáncer al pulmón</label>
                    <input type="text" class="form-control" name="cancerpulmon" id="cancerpulmon" value="{{ old('cancerpulmon', $history->cancerpulmon ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="efusionpleural">Efusión pleural</label>
                    <input type="text" class="form-control" name="efusionpleural" id="efusionpleural" value="{{ old('efusionpleural', $history->efusionpleural ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="neumonias">Neumonías</label>
                    <input type="text" class="form-control" name="neumonias" id="neumonias" value="{{ old('neumonias', $history->neumonias ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="tabaquismo">Tabaquismo</label>
                    <input type="text" class="form-control" name="tabaquismo" id="tabaquismo" value="{{ old('tabaquismo', $history->tabaquismo ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="id_ct">Seleccione tipo de consumo de tabaco</label>
                    <select class="form-control" name="id_ct" id="id_ct">
                        <option value="">-- Seleccione --</option>
                        @foreach($tb as $t)
                            <option value="{{ $t->id }}" {{ (old('id_ct', $history->id_ct ?? '') == $t->id) ? 'selected' : '' }}>{{ $t->consumo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="cig">Cigarros x día</label>
                    <input type="number" class="form-control" name="cig" min="0" value="0" id="cig" value="{{ old('cig', $history->cig ?? '') }}">
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="af">Años fumando</label>
                    <input type="number" class="form-control" name="aniosfum" min="0" value="0" id="af" value="{{ old('aniosfum', $history->aniosfum ?? '') }}">
                </div>
            </div>
            <div class="col-1">
                <div class="form-group">
                    <label for="r">Resultado</label>
                    <input type="number" class="form-control" name="result" min="0" step="any" value="0" id="r" value="{{ old('result', $history->result ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="contactotbc">Contacto TBC / COVID</label>
                    <input type="text" class="form-control" name="contactotbc" id="contactotbc" value="{{ old('contactotbc', $history->contactotbc ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="exposicionbiomasa">Exposición a biomasa</label>
                    <input type="text" class="form-control" name="exposicionbiomasa" id="exposicionbiomasa" value="{{ old('exposicionbiomasa', $history->exposicionbiomasa ?? '') }}">
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="motivoconsulta">Motivo consulta</label>
                    <input type="text" class="form-control" name="motivoconsulta" id="motivoconsulta" value="{{ old('motivoconsulta', $history->motivoconsulta ?? '') }}">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="sintomascardinales">Síntomas cardinales</label>
                    <input type="text" class="form-control" name="sintomascardinales" id="sintomascardinales" value="{{ old('sintomascardinales', $history->sintomascardinales ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="te">TE</label>
                    <input type="text" class="form-control" name="te" id="te" value="{{ old('te', $history->te ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="fi">FI</label>
                    <input type="text" class="form-control" name="fi" id="fi" value="{{ old('fi', $history->fi ?? '') }}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="c">C</label>
                    <input type="text" class="form-control" name="c" id="c" value="{{ old('c', $history->c ?? '') }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="relatocronologico">Relato cronológico</label>
                    <textarea class="form-control" cols="96" name="relatocronologico" id="relatocronologico">{{ old('relatocronologico', $history->relatocronologico ?? '') }}</textarea>
                </div>
            </div>
        </div>
    @endrole
</div>
<div class="card-footer">
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-primary"> Guardar</button>
    </div>
</div>
