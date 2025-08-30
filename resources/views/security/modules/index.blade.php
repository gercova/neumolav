@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Módulos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('home') }} ">Home</a></li>
                        <li class="breadcrumb-item">Seguridad</li>
                        <li class="breadcrumb-item active">Módulos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="btn btn-outline btn-primary" id="btn-add-module"><i class="bi bi-plus-circle"></i> Agregar módulo</button>
                            <button type="button" class="btn btn-outline btn-primary" id="btn-add-submodule"><i class="bi bi-plus-circle"></i> Agregar Submódulo</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="module_data">
                                    <thead>
                                        <tr>
                                            <th style="width:10px">#</th>
                                            <th>Descripción</th>
                                            <th>ícono</th>
                                            <th>Submódulos</th>
                                            <th>Fecha</th>
                                            <th>Opciones</th>
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
    </section>
</div>
<div class="modal fade" id="modalModule" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="moduleForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="descripcion">Descripción: </label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion">
                    </div>
                    <div class="form-group">
                        <label for="detalle">Detalle: </label>
                        <input type="text" class="form-control" id="detalle" name="detalle">
                    </div>
                    <div class="form-group">
                        <label for="icono">Icono: </label>
                        <input type="text" class="form-control" id="icono" name="icono">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <input type="hidden" name="moduleId" id="moduleId">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Grabar datos</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalSubmodule" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="submoduleForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="module_id">Módulo: </label>
                        <select class="form-control" id="module_id" name="module_id">
                            <option value="">-- Seleccione un módulo --</option>
                            @foreach ($md as $m)
                                <option value="{{ $m->id }}">{{ $m->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sm_descripcion">Descripción: </label>
                        <input type="text" class="form-control" id="sm_descripcion" name="descripcion">
                    </div>
                    <div class="form-group">
                        <label for="sm_nombres">Nombre: </label>
                        <input type="text" class="form-control" id="sm_nombre" name="nombre">
                    </div>
                    <div class="form-group">
                        <label for="sm_detalle">Detalle: </label>
                        <input type="text" class="form-control" id="sm_detalle" name="detalle">
                    </div>
                    <div class="form-group">
                        <label for="sm_icono">Icono: </label>
                        <input type="text" class="form-control" id="sm_icono" name="icono">
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <input type="hidden" name="submoduleId" id="submoduleId">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Grabar datos</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('js/forms/modules.js') }}"></script>
@endsection