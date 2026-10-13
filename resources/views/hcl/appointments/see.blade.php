@extends('layouts.app')
@section('title', config('global.site_name') . ' - Ver controles')
@php
    $seeState = [
        'historyId' => $hc->id,
        'dni'       => $hc->dni,
        'nombres'   => $hc->nombres,
        'addUrl'    => route('hcl.appointments.add', $hc->id),
        'homeUrl'   => route('home'),
        'canCreate' => auth()->user()->can('control_crear'),
    ];
@endphp
@section('content')
    <div id="vue-appointments-see"></div>
@endsection
@push('scripts')
    <script>
        window.__APPOINTMENTS_SEE_STATE__ = @json($seeState);
    </script>
    @vite(['resources/js/modules/appointments-see.js'])
@endpush
