@extends('layouts.app')
@section('title', config('global.site_name') . ' - Informes')
@section('content')
    <div id="vue-reports"></div>
@endsection
@push('scripts')
    @vite(['resources/js/modules/reports.js'])
@endpush
