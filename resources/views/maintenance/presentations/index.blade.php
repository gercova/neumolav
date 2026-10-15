@extends('layouts.app')
@section('title', config('global.site_name') . ' - Presentación Fármaco')
@php
    $presentationsState = [
        'permissions' => [
            'create' => auth()->user()->can('presentacion_crear'),
            'update' => auth()->user()->can('presentacion_actualizar'),
            'delete' => auth()->user()->can('presentacion_borrar'),
        ],
    ];
@endphp
@section('content')
    <div id="vue-presentations"></div>
@endsection
@push('scripts')
    <script>
        window.__PRESENTATIONS_STATE__ = @json($presentationsState);
    </script>
    @vite(['resources/js/modules/presentations.js'])
@endpush