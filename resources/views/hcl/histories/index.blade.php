@extends('layouts.app')
@section('title', config('global.site_name') . ' - Historias')
@php
    $historiesState = [
        'addUrl' => route('hcl.histories.add'),
        'editUrl' => url('/admin/histories/edit'),
    ];
@endphp
@section('content')
    <div id="vue-histories"></div>
@endsection
@push('scripts')
    <script>
        window.__HISTORIES_STATE__ = @json($historiesState);
    </script>
    @vite(['resources/js/modules/histories.js'])
@endpush
