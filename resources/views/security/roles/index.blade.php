@extends('layouts.app')
@section('title', config('global.site_name') . ' - Roles')
@php
    $rolesState = [
        'permissions' => [
            'create' => auth()->user()?->can('rol_crear') ?? false,
            'update' => auth()->user()?->can('rol_actualizar') ?? false,
            'delete' => auth()->user()?->can('rol_borrar') ?? false,
        ],
    ];
@endphp
@section('content')
    <div id="vue-roles"></div>
@endsection
@push('scripts')
    <script>
        window.__ROLES_STATE__ = @json($rolesState);
    </script>
    @vite(['resources/js/modules/roles.js'])
@endpush
