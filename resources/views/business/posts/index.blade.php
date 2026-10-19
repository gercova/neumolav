@extends('layouts.app')
@section('title', config('global.site_name') . ' - Publicaciones')
@php
    $publicationsState = [
        'permissions' => [
            'create' => auth()->user()?->can('post_crear') ?? false,
            'update' => auth()->user()?->can('post_actualizar') ?? false,
            'delete' => auth()->user()?->can('post_borrar') ?? false,
        ],
        'postTypes' => $postTypes,
    ];
@endphp
@section('content')
    <div id="vue-publications"></div>
@endsection
@push('scripts')
    <script>
        window.__PUBLICATIONS_STATE__ = @json($publicationsState);
    </script>
    @vite(['resources/js/modules/publications.js'])
@endpush