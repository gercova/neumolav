<input type="hidden" name="reportId" id="reportId" value="{{ $rp->id ?? '' }}">
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
                <input type="text" class="form-control" value="{{ $hc->dni.' :: '.$hc->nombres }}" readonly>
                <input type="hidden" name="id_historia" id="id_historia" value="{{ $hc->id }}">
                <input type="hidden" name="dni" id="dni" value="{{ $hc->dni }}">
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
                                        <textarea class="form-control form-control-sm summernote" id="antecedentes" name="antecedentes" rows="2">{{ old('antecedentes', $rp->antecedentes ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="col-form-label" for="historial_enfermedad">Historial de enfermedad:</label>
                                        <textarea class="form-control form-control-sm summernote" id="historial_enfermedad" name="historial_enfermedad" rows="2">{{ old('historial_enfermedad', $rp->historial_enfermedad ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="col-form-label" for="examen_fisico">Examen físico:</label>
                                        <textarea class="form-control form-control-sm summernote" id="examen_fisico" name="examen_fisico" rows="2">{{ old('examen_fisico', $rp->examen_fisico ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="col-form-label" for="examen_complementario">Examen complementario:</label>
                                        <textarea class="form-control form-control-sm summernote" id="examen_complementario" name="examen_complementario" rows="2">{{ old('examen_complementario', $rp->examen_complementario ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="diagnostics">Buscar:</label>
                                                <div class="col-sm-9">
                                                    <select type="text" class="form-control form-control-sm searchDiagnostics" id="diagnostics" placeholder="Buscar por código o nombre"></select>
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
                                        </div>
                                    </div>
                                    @if(isset($rp) && $rp->diagnostics)
                                        <div class="col-12">
                                            <table id="diagnostic_data" class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="width:10%;">#</th>
                                                        <th style="width:75%;">Diagnóstico</th>
                                                        <th style="width:15%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <hr>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <label class="col-form-label" for="tratamiento">Tratamiento:</label>
                                        <textarea class="form-control form-control-sm summernote" id="tratamiento" name="tratamiento" rows="2">{{ old('tratamiento', $rp->tratamiento ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="col-form-label" for="sugerencia">Sugerencia:</label>
                                        <textarea class="form-control form-control-sm summernote" id="sugerencia" name="sugerencia" rows="2">{{ old('sugerencia', $rp->sugerencia ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endrole
            @role('asistente')
                <p class="alert alert-danger">
                    No tienes permisos para editar informes, consulte con el administrador del sistema.
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
        <button type="submit" class="btn btn-primary"> Guardar</button>
    </div>
</div>
