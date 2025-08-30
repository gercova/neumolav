@extends('layouts.app')
@section('content')
<style>
    /* En la sección @css */
    .badge-heredado {
        background-color: #ffc107;
        color: #212529;
        font-size: 0.75em;
        margin-left: 5px;
    }
</style>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Usuarios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item">Seguridad</li>
                        <li class="breadcrumb-item active">Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Asignar permisos a: <b>{{ $user->name }}</b></h3>
                </div>
                <div class="card-body">
                    <form id="assignPermissionsForm" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Permisos Disponibles</h3>
                                    </div>
                                    <div class="card-body">
                                        <table id="availablePermissions" class="table table-striped table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($availablePermissions as $permission)
                                                    <tr data-id="{{ $permission->id }}">
                                                        <td>{{ $permission->name }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary add-permission btn-xs">
                                                                <i class="fas fa-plus"></i> Agregar
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <button type="button" id="addAllPermissions" class="btn btn-info mb-2">
                                        <i class="fas fa-angle-double-right"></i> Todos
                                    </button>
                                    <button type="button" id="removeAllPermissions" class="btn btn-info mb-2">
                                        <i class="fas fa-angle-double-left"></i> Ninguno
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Permisos Asignados</h3>
                                    </div>
                                    <div class="card-body">
                                        <table id="assignedPermissions" class="table table-striped table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->getAllPermissions() as $permission)
                                                    <tr data-id="{{ $permission->id }}">
                                                        <td>{{ $permission->name }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger remove-permission btn-xs">
                                                                <i class="bi bi-trash"></i> Quitar
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="userId" name="userId" value="{{ $user->id }}">
                        <input type="hidden" name="permissions" id="selectedPermissions" value="{{ $user->getAllPermissions()->pluck('id')->implode(',') }}">
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Guardar Cambios</button>
                            <a href="{{ route('security.users.home') }}" class="btn btn-danger"><i class="bi bi-box-arrow-left"></i> Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Permisos heredados del rol: <b>{{ $user->getRoleNames()->implode(', ') }}</b></h3>
                </div>
                <div class="card-body">
                    <table id="permissionsRoleTable" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Nombre del Permiso</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rolePermissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td>{{ $permission->description ?? 'Sin descripción' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">El rol no tiene permisos asignados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('js/forms/users.js') }}"></script>
@endsection