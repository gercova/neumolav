@extends('layouts.app')
@section('title', config('global.site_name') . ' - Controles')
@section('content')
    <div id="vue-appointments"></div>
@endsection
@push('scripts')
    @vite(['resources/js/modules/appointments.js'])
@endpush
