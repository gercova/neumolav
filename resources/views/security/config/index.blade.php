@extends('layouts.app')
@section('title', config('global.site_name') . ' - Versiones & Configuración')
@section('content')
    <div id="vue-config"></div>
@endsection
@push('scripts')
    <script>
        window.__CONFIG_STATE__ = @json($configState);
    </script>
    @vite(['resources/js/modules/config.js'])
@endpush
