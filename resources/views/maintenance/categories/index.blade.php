@extends('layouts.app')
@section('title', config('global.site_name') . ' - Categorías Fármacos')
@php
    $categoriesState = [
        'permissions' => [
            'create' => auth()->user()->can('categoria_crear'),
            'update' => auth()->user()->can('categoria_actualizar'),
            'delete' => auth()->user()->can('categoria_borrar'),
        ],
    ];
@endphp
@section('content')
    <div id="vue-categories"></div>
@endsection
@push('scripts')
    <script>
        window.__CATEGORIES_STATE__ = @json($categoriesState);
    </script>
    @vite(['resources/js/modules/categories.js'])
@endpush
