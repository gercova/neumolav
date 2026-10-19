@extends('layouts.app')
@section('title', config('global.site_name') . ' - Permisos Inicio')
@php
    $permissionsState = [
        'permissions' => [
            'create' => auth()->user()?->can('permiso_crear') ?? false,
            'update' => auth()->user()?->can('permiso_actualizar') ?? false,
            'delete' => auth()->user()?->can('permiso_borrar') ?? false,
        ],
    ];
@endphp
@section('content')
    <div id="vue-permissions"></div>
@endsection
@push('scripts')
    <script>
        window.__PERMISSIONS_STATE__ = @json($permissionsState);
    </script>
    @vite(['resources/js/modules/permissions.js'])
@endpush
