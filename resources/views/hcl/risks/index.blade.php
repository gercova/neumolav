@extends('layouts.app')
@section('title', config('global.site_name') . ' - Informes de Riesgo')
@section('content')
    <div id="vue-risks"></div>
@endsection
@push('scripts')
    @vite(['resources/js/modules/risks.js'])
@endpush

