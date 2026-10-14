@extends('layouts.app')
@section('title', config('global.site_name') . ' - Ver informes de riesgo')
@php
    $seeState = [
        'historyId' => $hc->id,
        'dni'       => $hc->dni,
        'nombres'   => $hc->nombres,
        'addUrl'    => route('hcl.risks.add', ['hc' => $hc->id]),
        'homeUrl'   => route('home'),
        'canCreate' => auth()->user()->can('riesgo_crear'),
    ];
@endphp
@section('content')
    <div id="vue-risks-see"></div>
@endsection
@push('scripts')
    <script>
        window.__RISKS_SEE_STATE__ = @json($seeState);
    </script>
    @vite(['resources/js/modules/risks-see.js'])
@endpush
