@extends('layouts.app')
@section('title', 'NeumoTar - Dashboard Estadístico')
@php
    $statisticsState = [
        'initialYear'   => $yr->first()->year ?? now()->year,
        'years'         => $yr->map(fn($y) => ['year' => (int) $y->year])->values(),
    ];
@endphp
@section('content')
    <div id="vue-statistics"></div>
@endsection
@push('scripts')
    <script>
        window.__STATISTICS_STATE__ = @json($statisticsState);
    </script>
    @vite(['resources/js/modules/dashboard.js'])
@endpush
