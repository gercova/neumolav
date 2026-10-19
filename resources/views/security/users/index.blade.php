@extends('layouts.app')
@section('title', config('global.site_name') . ' - Usuarios')
@php
    $usersState = [
        'permissions' => [
            'create'       => auth()->user()?->can('usuario_crear') ?? false,
            'update'       => auth()->user()?->can('usuario_actualizar') ?? false,
            'delete'       => auth()->user()?->can('usuario_borrar') ?? false,
            'assign_roles' => auth()->user()?->hasRole('administrador') ?? false,
        ],
        'current_user_id' => auth()->id(),
        'roles'           => $roles,
        'specialties'     => $specialties,
        'routes'          => [
            'add'         => route('security.users.add'),
            'role_prefix' => url('/admin/users/role'),
            'edit_prefix' => url('/admin/users/edit'),
        ],
    ];
@endphp
@section('content')
    <div id="vue-users"></div>
@endsection
@push('scripts')
    <script>
        window.__USERS_STATE__ = @json($usersState);
    </script>
    @vite(['resources/js/modules/users.js'])
@endpush
