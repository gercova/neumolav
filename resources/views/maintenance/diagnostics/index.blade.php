@extends('layouts.app')
@section('title', config('global.site_name') . ' - Diagnósticos Inicio')
@php
    $diagnosticsState = [
        'permissions' => [
            'create' => auth()->user()?->can('diagnostico_crear') ?? false,
            'update' => auth()->user()?->can('diagnostico_actualizar') ?? false,
            'delete' => auth()->user()?->can('diagnostico_borrar') ?? false,
        ],
    ];
@endphp
@section('content')
    <div id="vue-diagnostics"></div>
@endsection
@push('scripts')
    <script>
        window.__DIAGNOSTICS_STATE__ = @json($diagnosticsState);
    </script>
    @vite(['resources/js/modules/diagnostics.js'])
@endpush
