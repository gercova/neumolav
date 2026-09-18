@extends('layouts.app')
@section('title', config('global.site_name') . ' - Módulos inicio')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/roles.css') }}">
@endsection
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
                            <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
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
                                <button type="button" class="btn btn-outline btn-primary" id="btn-add-module">
                                    <i class="bi bi-plus-circle"></i> Agregar módulo
                                </button>
                                <button type="button" class="btn btn-outline btn-primary" id="btn-add-submodule">
                                    <i class="bi bi-plus-circle"></i> Agregar Submódulo
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="module_data">
                                        <thead>
                                            <tr>
                                                <th style="width:10px">#</th>
                                                <th>Descripción</th>
                                                <th>Ícono</th>
                                                <th>Submódulos</th>
                                                <th>Permisos</th>
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
    {{-- MODAL 1: Crear / Editar Módulo --}}
    <div class="modal fade" id="modalModule" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static"
        aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
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
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL 2: Crear / Editar Submódulo --}}
    <div class="modal fade" id="modalSubmodule" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static"
        aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
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
                            <label for="sm_nombre">Nombre: </label>
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
                        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 3: Permisos del Módulo (dual-panel) --}}
    <div class="modal fade" id="modalModulePermissions" tabindex="-1" aria-modal="true" role="dialog"
        data-backdrop="static" aria-labelledby="modalModulePermissionsLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalModulePermissionsLabel">
                        <i class="bi bi-key-fill mr-1"></i>
                        Permisos del Módulo: <strong id="modulePermName"></strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modulePermId">
                    <div class="row align-items-start">
                        {{-- Panel izquierdo: disponibles --}}
                        <div class="col-md-5">
                            <div class="card mb-0">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0">
                                        Disponibles
                                        <span class="badge badge-secondary counter-badge" id="modAvailableCount">0</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="input-group input-group-sm search-box">
                                        <input type="text" id="modAvailableSearch" class="form-control"
                                            placeholder="Buscar permiso...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="clearModAvailableSearch">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="permission-panel">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>Nombre</th>
                                                    <th width="20%">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modAvailableBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Columna de botones de transferencia --}}
                        <div class="col-md-2">
                            <div class="transfer-col">
                                <button type="button" id="modAddAll" class="btn btn-sm btn-outline-primary w-100"
                                    title="Agregar todos">
                                    <i class="fas fa-angle-double-right"></i> Todos
                                </button>
                                <button type="button" id="modAddSelected" class="btn btn-sm btn-primary w-100"
                                    title="Agregar seleccionados" style="display:none;">
                                    <i class="fas fa-arrow-right"></i> Selec.
                                </button>
                                <button type="button" id="modRemoveSelected" class="btn btn-sm btn-warning w-100"
                                    title="Quitar seleccionados" style="display:none;">
                                    <i class="fas fa-arrow-left"></i> Selec.
                                </button>
                                <button type="button" id="modRemoveAll" class="btn btn-sm btn-outline-warning w-100"
                                    title="Quitar todos">
                                    <i class="fas fa-angle-double-left"></i> Ninguno
                                </button>
                            </div>
                        </div>
                        {{-- Panel derecho: asignados --}}
                        <div class="col-md-5">
                            <div class="card mb-0">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0">
                                        Asignados
                                        <span class="badge badge-success counter-badge" id="modAssignedCount">0</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="input-group input-group-sm search-box">
                                        <input type="text" id="modAssignedSearch" class="form-control"
                                            placeholder="Buscar permiso...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="clearModAssignedSearch">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="permission-panel">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>Nombre</th>
                                                    <th width="20%">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modAssignedBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn-save-module-permissions">
                        <i class="bi bi-save"></i> Guardar Permisos
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL 4: Permisos del Submódulo (dual-panel) --}}
    <div class="modal fade" id="modalSubmodulePermissions" tabindex="-1" aria-modal="true" role="dialog"
        data-backdrop="static" aria-labelledby="modalSubmodulePermissionsLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalSubmodulePermissionsLabel">
                        <i class="bi bi-key-fill mr-1"></i>
                        Permisos del Submódulo: <strong id="submodulePermName"></strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="submodulePermId">
                    <p class="text-muted small mb-2">
                        <i class="bi bi-info-circle"></i>
                        Solo se muestran los permisos relacionados con este submódulo.
                        Los cambios se aplican al módulo padre.
                        Módulo padre: <strong id="submoduleParentName"></strong>
                    </p>
                    <div class="row align-items-start">
                        {{-- Panel izquierdo: disponibles --}}
                        <div class="col-md-5">
                            <div class="card mb-0">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0">
                                        Disponibles
                                        <span class="badge badge-secondary counter-badge" id="smAvailableCount">0</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="permission-panel">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>Nombre</th>
                                                    <th width="20%">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="smAvailableBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Columna de botones de transferencia --}}
                        <div class="col-md-2">
                            <div class="transfer-col">
                                <button type="button" id="smAddAll" class="btn btn-sm btn-outline-primary w-100"
                                    title="Agregar todos">
                                    <i class="fas fa-angle-double-right"></i> Todos
                                </button>
                                <button type="button" id="smAddSelected" class="btn btn-sm btn-primary w-100"
                                    title="Agregar seleccionados" style="display:none;">
                                    <i class="fas fa-arrow-right"></i> Selec.
                                </button>
                                <button type="button" id="smRemoveSelected" class="btn btn-sm btn-warning w-100"
                                    title="Quitar seleccionados" style="display:none;">
                                    <i class="fas fa-arrow-left"></i> Selec.
                                </button>
                                <button type="button" id="smRemoveAll" class="btn btn-sm btn-outline-warning w-100"
                                    title="Quitar todos">
                                    <i class="fas fa-angle-double-left"></i> Ninguno
                                </button>
                            </div>
                        </div>
                        {{-- Panel derecho: asignados --}}
                        <div class="col-md-5">
                            <div class="card mb-0">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0">
                                        Asignados
                                        <span class="badge badge-success counter-badge" id="smAssignedCount">0</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="permission-panel">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>Nombre</th>
                                                    <th width="20%">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="smAssignedBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn-save-submodule-permissions">
                        <i class="bi bi-save"></i> Guardar Permisos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/forms/modules.js') }}"></script>
@endsection
