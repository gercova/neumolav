@method('POST')
@csrf
<input type="hidden" name="id" id="id" value="{{ $history->id ?? '' }}">
<div class="card-body">
    <!-- FILIACIÓN PRINCIPAL -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="id_td">Tipo Documento <span class="text-danger">*</span></label>
                <select class="form-control" id="id_td" name="id_td" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($dt as $t)
                        <option value="{{ $t->id }}" {{ (old('id_td', $history->id_td ?? '') == $t->id) ? 'selected' : '' }}>{{ $t->descripcion }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="dni" id="label_dni">Documento / DNI <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" class="form-control" id="dni" name="dni" value="{{ old('dni', $history->dni ?? '') }}" placeholder="Ingrese DNI" maxlength="8" autocomplete="off" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="button" id="btnSearchDni" title="Buscar en RENIEC">
                            <i class="fas fa-search" id="iconSearchDni"></i>
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerSearchDni" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted" id="dniHelperText">Para DNI presione buscar o Enter (8 dígitos).</small>
                <div class="invalid-feedback d-block" id="dniFeedback"></div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label for="nombres">Nombres y Apellidos <span class="text-danger">*</span></label>
                <input type="text" class="form-control text-uppercase" id="nombres" name="nombres" value="{{ old('nombres', $history->nombres ?? '') }}" placeholder="NOMBRES COMPLETOS" required autocomplete="off">
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento <span class="text-danger">*</span></label>
                {{-- Hidden ISO field sent to the backend (yyyy-mm-dd) --}}
                <input type="hidden" id="fecha_nacimiento_iso" name="fecha_nacimiento"
                    value="{{ old('fecha_nacimiento', isset($history->fecha_nacimiento) ? $history->fecha_nacimiento->format('Y-m-d') : '') }}">
                <div class="input-group">
                    <input type="text"
                        class="form-control"
                        id="fecha_nacimiento"
                        placeholder="DD-MM-AAAA"
                        maxlength="10"
                        autocomplete="off"
                        inputmode="numeric"
                        value="{{ old('fecha_nacimiento') ? '' : (isset($history->fecha_nacimiento) ? $history->fecha_nacimiento->format('d-m-Y') : '') }}"
                        spellcheck="false">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btnDatepickerToggle" title="Seleccionar fecha">
                            <i class="far fa-calendar-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="invalid-feedback" id="fechaNacimientoFeedback"></div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="form-group">
                <label for="age">Edad</label>
                <div class="input-group">
                    <input type="text" class="form-control bg-light text-center font-weight-bold" id="age" name="age" value="{{ old('age') }}" readonly placeholder="0">
                    <div class="input-group-append">
                        <span class="input-group-text">años</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="form-group">
                <label for="id_sexo">Sexo <span class="text-danger">*</span></label>
                <select class="form-control" id="id_sexo" name="id_sexo" required>
                    <option value="">-- Seleccione --</option>
                    @foreach ($sx as $s)
                        <option value="{{ $s->id }}" {{ (old('id_sexo', $history->id_sexo ?? '') == $s->id) ? 'selected' : '' }}>{{ $s->descripcion }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="form-group">
                <label for="telefono">Celular / Teléfono <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $history->telefono ?? '') }}" maxlength="11" placeholder="Ej: 987654321" required>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="id_gs">Grupo sanguíneo <span class="text-danger">*</span></label>
                <select class="form-control" id="id_gs" name="id_gs" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($bg as $b)
                        <option value="{{ $b->id }}" {{ (old('id_gs', $history->id_gs ?? '') == $b->id) ? 'selected' : '' }}>{{ $b->descripcion }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <hr>

    <!-- UBICACIÓN Y PROCEDENCIA -->
    <div class="row align-items-center">
        <div class="col-md-2 col-sm-12">
            @php
                $isForeign = isset($history) && !empty($history->ubigeo_extranjero);
            @endphp
            <div class="form-group">
                <label class="d-none d-md-block">&nbsp;</label>
                <button type="button" class="btn btn-outline-warning btn-block extra {{ $isForeign ? 'd-none' : '' }}" title="Haga clic si el paciente nació en el extranjero">
                    <i class="fa fa-globe"></i> Extranjero
                </button>
                <button type="button" class="btn btn-outline-success btn-block pe {{ $isForeign ? '' : 'd-none' }}" title="Haga clic si el paciente nació en Perú">
                    <i class="fa fa-map-marker-alt"></i> Nacional (Perú)
                </button>
            </div>
        </div>
        <div class="col-md-5 col-sm-12">
            <div class="form-group nacional {{ $isForeign ? 'd-none' : '' }}">
                <label for="ubigeo_nacimiento">Lugar de Nacimiento (Perú): </label>
                <select class="form-control buscarUbigeo" id="ubigeo_nacimiento" name="ubigeo_nacimiento" style="width: 100%;">
                    @if (isset($history) && !empty($unacimiento[0]['nacimiento']))
                        <option value="{{ $unacimiento[0]['nacimiento'] }}" selected>{{ $unacimiento[0]['nacimiento'] }}</option>
                    @endif
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group foreign {{ $isForeign ? '' : 'd-none' }}">
                <label for="extranjero">Lugar de Nacimiento (Extranjero): </label>
                <input class="form-control" id="extranjero" name="extranjero" placeholder="PAÍS, REGIÓN, CIUDAD" value="{{ old('extranjero', $history->ubigeo_extranjero ?? '') }}">
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-5 col-sm-12">
            <div class="form-group">
                <label for="ubigeo_residencia">Lugar de Residencia: <span class="text-danger">*</span></label>
                <select class="form-control buscarUbigeoR" id="ubigeo_residencia" name="ubigeo_residencia" style="width: 100%;" required>
                    @if (isset($history) && !empty($uresidencia[0]['residencia']))
                        <option value="{{ $uresidencia[0]['residencia'] }}" selected>{{ $uresidencia[0]['residencia'] }}</option>
                    @endif
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <hr>

    <!-- INSTRUCCIÓN, OCUPACIÓN, ESTADO CIVIL -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="id_gi">Grado instrucción <span class="text-danger">*</span></label>
                <select class="form-control" id="id_gi" name="id_gi" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($di as $g)
                        <option value="{{ $g->id }}" {{ (old('id_gi', $history->id_gi ?? '') == $g->id) ? 'selected' : '' }}>{{ $g->descripcion }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label for="id_ocupacion">Ocupación <span class="text-danger">*</span></label>
                <select class="form-control buscarOcupacion" id="id_ocupacion" name="id_ocupacion" style="width: 100%;" required>
                    @if (isset($history) && !empty($occupation[0]['occupation']))
                        <option value="{{ $occupation[0]['occupation'] }}" selected>{{ $occupation[0]['occupation'] }}</option>
                    @endif
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="id_estado">Estado civil <span class="text-danger">*</span></label>
                <select class="form-control" id="id_estado" name="id_estado" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($ms as $m)
                        <option value="{{ $m->id }}" {{ (old('id_estado', $history->id_estado ?? '') == $m->id ) ? 'selected' : '' }}>{{ $m->descripcion }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <hr>

    <!-- ANTECEDENTES CLÍNICOS -->
    @role('administrador|especialista')
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="cirugias">Cirugías</label>
                    <input type="text" class="form-control" name="cirugias" id="cirugias" value="{{ old('cirugias', $history->cirugias ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="transfusiones">Transfusiones sanguíneas</label>
                    <input type="text" class="form-control" name="transfusiones" id="transfusiones" value="{{ old('transfusiones', $history->transfusiones ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="traumatismos">Traumatismos</label>
                    <input type="text" class="form-control" name="traumatismos" id="traumatismos" value="{{ old('traumatismos', $history->traumatismos ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="hospitalizaciones">Hospitalizaciones previas</label>
                    <input type="text" class="form-control" name="hospitalizaciones" id="hospitalizaciones" value="{{ old('hospitalizaciones', $history->hospitalizaciones ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="drogas">Consumo de drogas sociales</label>
                    <input type="text" class="form-control" name="drogas" id="drogas" value="{{ old('drogas', $history->drogas ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="antecedentes">Antecedentes familiares</label>
                    <input type="text" class="form-control" name="antecedentes" id="antecedentes" value="{{ old('antecedentes', $history->antecedentes ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="estadobasal">Estado basal</label>
                    <input type="text" class="form-control" name="estadobasal" id="estadobasal" value="{{ old('estadobasal', $history->estadobasal ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="medicacion">RAMS / Medicación</label>
                    <input type="text" class="form-control" name="medicacion" id="medicacion" value="{{ old('medicacion', $history->medicacion ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="animales">Animales en casa</label>
                    <input type="text" class="form-control" name="animales" id="animales" value="{{ old('animales', $history->animales ?? '') }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="otros">Otros antecedentes</label>
                    <textarea class="form-control" rows="2" name="otros" id="otros">{{ old('otros', $history->otros ?? '') }}</textarea>
                </div>
            </div>
        </div>
        <hr>

        <!-- ENFERMEDADES RESPIRATORIAS Y TABAQUISMO -->
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="asmabronquial">Asma bronquial</label>
                    <input type="text" class="form-control" name="asmabronquial" id="asmabronquial" value="{{ old('asmabronquial', $history->asmabronquial ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="epoc">EPOC</label>
                    <input type="text" class="form-control" name="epoc" id="epoc" value="{{ old('epoc', $history->epoc ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="epid">EPID</label>
                    <input type="text" class="form-control" name="epid" id="epid" value="{{ old('epid', $history->epid ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="tuberculosis">Tuberculosis</label>
                    <input type="text" class="form-control" name="tuberculosis" id="tuberculosis" value="{{ old('tuberculosis', $history->tuberculosis ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="cancerpulmon">Cáncer al pulmón</label>
                    <input type="text" class="form-control" name="cancerpulmon" id="cancerpulmon" value="{{ old('cancerpulmon', $history->cancerpulmon ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="efusionpleural">Efusión pleural</label>
                    <input type="text" class="form-control" name="efusionpleural" id="efusionpleural" value="{{ old('efusionpleural', $history->efusionpleural ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="neumonias">Neumonías</label>
                    <input type="text" class="form-control" name="neumonias" id="neumonias" value="{{ old('neumonias', $history->neumonias ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="tabaquismo">Tabaquismo</label>
                    <input type="text" class="form-control" name="tabaquismo" id="tabaquismo" value="{{ old('tabaquismo', $history->tabaquismo ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="id_ct">Tipo de consumo de tabaco</label>
                    <select class="form-control" name="id_ct" id="id_ct">
                        <option value="">-- Seleccione --</option>
                        @foreach($tb as $t)
                            <option value="{{ $t->id }}" {{ (old('id_ct', $history->id_ct ?? '') == $t->id) ? 'selected' : '' }}>{{ $t->consumo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="form-group">
                    <label for="cig">Cigarros x día</label>
                    <input type="number" class="form-control" name="cig" min="0" step="1" id="cig" value="{{ old('cig', $history->cig ?? 0) }}">
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="form-group">
                    <label for="af">Años fumando</label>
                    <input type="number" class="form-control" name="aniosfum" min="0" step="1" id="af" value="{{ old('aniosfum', $history->aniosfum ?? 0) }}">
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="form-group">
                    <label for="r">Resultado (IPA)</label>
                    <input type="number" class="form-control bg-light font-weight-bold" name="result" min="0" step="0.01" id="r" value="{{ old('result', $history->result ?? 0) }}" readonly>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="contactotbc">Contacto TBC / COVID</label>
                    <input type="text" class="form-control" name="contactotbc" id="contactotbc" value="{{ old('contactotbc', $history->contactotbc ?? '') }}">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="exposicionbiomasa">Exposición a biomasa</label>
                    <input type="text" class="form-control" name="exposicionbiomasa" id="exposicionbiomasa" value="{{ old('exposicionbiomasa', $history->exposicionbiomasa ?? '') }}">
                </div>
            </div>
        </div>
        <hr>

        <!-- MOTIVO Y RELATO CLÍNICO -->
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="motivoconsulta">Motivo consulta</label>
                    <input type="text" class="form-control" name="motivoconsulta" id="motivoconsulta" value="{{ old('motivoconsulta', $history->motivoconsulta ?? '') }}">
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="sintomascardinales">Síntomas cardinales</label>
                    <input type="text" class="form-control" name="sintomascardinales" id="sintomascardinales" value="{{ old('sintomascardinales', $history->sintomascardinales ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="te">TE (Tiempo de Enfermedad)</label>
                    <input type="text" class="form-control" name="te" id="te" value="{{ old('te', $history->te ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="fi">FI (Forma de Inicio)</label>
                    <input type="text" class="form-control" name="fi" id="fi" value="{{ old('fi', $history->fi ?? '') }}">
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="c">C (Curso)</label>
                    <input type="text" class="form-control" name="c" id="c" value="{{ old('c', $history->c ?? '') }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="relatocronologico">Relato cronológico</label>
                    <textarea class="form-control" rows="3" name="relatocronologico" id="relatocronologico">{{ old('relatocronologico', $history->relatocronologico ?? '') }}</textarea>
                </div>
            </div>
        </div>
    @endrole
</div>
<div class="card-footer bg-white border-top">
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('hcl.histories.home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Cancelar / Volver
        </a>
        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSubmitHC">
            <i class="fas fa-save mr-1"></i> Guardar Historia Clínica
        </button>
    </div>
</div>
