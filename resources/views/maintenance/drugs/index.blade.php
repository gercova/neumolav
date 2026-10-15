@extends('layouts.app')
@section('title', config('global.site_name') . ' - Fármacos Inicio')
@php
    $drugsState = [
        'permissions' => [
            'create' => auth()->user()?->can('farmaco_crear') ?? false,
            'update' => auth()->user()?->can('farmaco_actualizar') ?? false,
            'delete' => auth()->user()?->can('farmaco_borrar') ?? false,
        ],
        'categories' => $categories,
        'presentations' => $presentations,
    ];
@endphp
@section('content')
    <div id="vue-drugs"></div>
@endsection
@push('scripts')
    <script>
        window.__DRUGS_STATE__ = @json($drugsState);
    </script>
    @vite(['resources/js/modules/drugs.js'])
@endpush
