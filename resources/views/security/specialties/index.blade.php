@extends('layouts.app')
@section('title', config('global.site_name') . ' - Especialidades')
@php
    $specialtiesState = [
        'permissions' => [
            'create' => auth()->user()?->can('especialidad_crear') ?? false,
            'update' => auth()->user()?->can('especialidad_actualizar') ?? false,
            'delete' => auth()->user()?->can('especialidad_borrar') ?? false,
        ],
        'occupations' => $occupations,
    ];
@endphp
@section('content')
    <div id="vue-specialties"></div>
@endsection
@push('scripts')
    <script>
        window.__SPECIALTIES_STATE__ = @json($specialtiesState);
    </script>
    @vite(['resources/js/modules/specialties.js'])
@endpush
