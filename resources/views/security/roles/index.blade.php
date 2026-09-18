@extends('layouts.app')
@section('title', config('global.site_name') . ' - Roles')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/roles.css') }}">
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Roles</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item">Seguridad</li>
                            <li class="breadcrumb-item active">Roles</li>
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
                            @can('rol_crear')
                                <div class="card-header">
                                    <button type="button" class="btn btn-primary" id="btn-add-role">
                                        <i class="bi bi-plus-circle"></i> Agregar Rol
                                    </button>
                                </div>
                            @endcan
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm" id="roles_data">
                                        <thead>
                                            <tr>
                                                <th style="width:40px">#</th>
                                                <th>Nombre</th>
                                                <th>Guard</th>
                                                <th>Permisos</th>
                                                <th>Usuarios</th>
                                                <th>Creado</th>
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

    {{-- ============================================================ --}}
    {{-- MODAL 1: Crear / Editar Rol --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalRole" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static"
        aria-labelledby="modalRoleLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalRoleLabel"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="roleForm" method="post" autocomplete="off">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="role-name">Nombre del rol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="role-name" name="name"
                                placeholder="ej. editor, supervisor..." required>
                        </div>
                        <div class="form-group">
                            <label for="role-guard">Guard Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="role-guard" name="guard_name" value="web"
                                required>
                            <small class="form-text text-muted">Usualmente <code>web</code> o <code>api</code></small>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <input type="hidden" name="roleId" id="roleId">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-role">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL 2: Asignar Permisos al Rol (dual-panel) --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalPermissions" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static"
        aria-labelledby="modalPermissionsLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalPermissionsLabel">
                        <i class="bi bi-key-fill mr-1"></i>
                        Permisos del Rol: <strong id="permRoleName"></strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="permRoleId">
                    <input type="hidden" id="selectedPermissions">
                    <div class="row align-items-start">
                        {{-- Panel izquierdo: disponibles --}}
                        <div class="col-md-5">
                            <div class="card mb-0">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0">
                                        Disponibles
                                        <span class="badge badge-secondary counter-badge" id="availableCount">0</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="input-group input-group-sm search-box">
                                        <input type="text" id="availableSearch" class="form-control"
                                            placeholder="Buscar permiso...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="clearAvailableSearch">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="permission-panel">
                                        <table class="table table-sm table-hover mb-0" id="availablePermissionsTable">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>Nombre</th>
                                                    <th width="20%">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="availablePermissionsBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Columna de botones de transferencia --}}
                        <div class="col-md-2">
                            <div class="transfer-col">
                                <button type="button" id="addAllPermissions"
                                    class="btn btn-sm btn-outline-primary w-100" title="Agregar todos">
                                    <i class="fas fa-angle-double-right"></i> Todos
                                </button>
                                <button type="button" id="addSelectedPermissions" class="btn btn-sm btn-primary w-100"
                                    title="Agregar seleccionados" style="display:none;">
                                    <i class="fas fa-arrow-right"></i> Selec.
                                </button>
                                <button type="button" id="removeSelectedPermissions"
                                    class="btn btn-sm btn-warning w-100" title="Quitar seleccionados"
                                    style="display:none;">
                                    <i class="fas fa-arrow-left"></i> Selec.
                                </button>
                                <button type="button" id="removeAllPermissions"
                                    class="btn btn-sm btn-outline-warning w-100" title="Quitar todos">
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
                                        <span class="badge badge-success counter-badge" id="assignedCount">0</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="input-group input-group-sm search-box">
                                        <input type="text" id="assignedSearch" class="form-control"
                                            placeholder="Buscar permiso...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="clearAssignedSearch">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="permission-panel">
                                        <table class="table table-sm table-hover mb-0" id="assignedPermissionsTable">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>Nombre</th>
                                                    <th width="20%">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="assignedPermissionsBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn-save-permissions">
                        <i class="bi bi-save"></i> Guardar Permisos
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL 3: Usuarios del Rol --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalUsers" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static"
        aria-labelledby="modalUsersLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalUsersLabel">
                        <i class="bi bi-people-fill mr-1"></i>
                        Usuarios del Rol: <strong id="usersRoleName"></strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="usersLoadingSpinner" class="text-center py-3" style="display:none;">
                        <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="usersRoleTable">
                            <thead>
                                <tr>
                                    <th width="40px">#</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody id="usersRoleBody">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Sin usuarios</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/forms/roles.js') }}"></script>
@endsection
