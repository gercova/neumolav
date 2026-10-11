@extends('layouts.app')
@section('title', config('global.site_name', 'NeumoTar') . ' - Dashboard & Citas')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/citas.css') }}">
@endsection
@php
    $homeState = [
        'initialDate'   => $selectedDate,
        'globalStats'   => [
            'historias' => $historias,
            'examenes'  => $examenes,
            'controles' => $controles,
            'users'     => $users,
        ],
        'statuses'      => $statuses,
    ];
@endphp
@section('content')
    <div id="vue-home">
        {{-- Vue monta aquí --}}
    </div>
    @include('home.partials.modals')
@endsection
@push('scripts')
    <script>
        window.__HOME_STATE__ = @json($homeState);
    </script>
    @vite(['resources/js/modules/home.js'])
    <script src="{{ asset('js/forms/citas.js') }}"></script>
@endpush
