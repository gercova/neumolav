@extends('layouts.app')
@section('title', config('global.site_name') . ' - Ocupaciones Inicio')
@php
    $occupationsState = [
        'permissions' => [
            'create' => auth()->user()?->can('ocupacion_crear') ?? false,
            'update' => auth()->user()?->can('ocupacion_actualizar') ?? false,
            'delete' => auth()->user()?->can('ocupacion_borrar') ?? false,
        ],
    ];
@endphp
@section('content')
    <div id="vue-occupations"></div>
@endsection
@push('scripts')
    <script>
        window.__OCCUPATIONS_STATE__ = @json($occupationsState);
    </script>
    @vite(['resources/js/modules/occupations.js'])
@endpush
