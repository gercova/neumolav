@extends('layouts.app')
@section('title', config('global.site_name') . ' - Ver exámenes')
@php
    $seeState = [
        'historyId' => $hc->id,
        'dni'       => $hc->dni,
        'nombres'   => $hc->nombres,
        'addUrl'    => route('hcl.exams.add', ['hc' => $hc->id]),
        'homeUrl'   => route('home'),
        'canCreate' => auth()->user()->can('examen_crear'),
    ];
@endphp
@section('content')
    <div id="vue-exams-see"></div>
@endsection
@push('scripts')
    <script>
        window.__EXAMS_SEE_STATE__ = @json($seeState);
    </script>
    @vite(['resources/js/modules/exams-see.js'])
@endpush
