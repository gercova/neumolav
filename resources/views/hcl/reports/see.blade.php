@extends('layouts.app')
@section('title', config('global.site_name') . ' - Ver informes')
@php
    $seeState = [
        'historyId' => $hc->id,
        'dni'       => $hc->dni,
        'nombres'   => $hc->nombres,
        'addUrl'    => route('hcl.reports.add', ['hc' => $hc->id]),
        'homeUrl'   => route('home'),
        'canCreate' => auth()->user()->can('informe_crear'),
    ];
@endphp
@section('content')
    <div id="vue-reports-see"></div>
@endsection
@push('scripts')
    <script>
        window.__REPORTS_SEE_STATE__ = @json($seeState);
    </script>
    @vite(['resources/js/modules/reports-see.js'])
@endpush
