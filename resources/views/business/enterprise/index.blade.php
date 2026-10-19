@extends('layouts.app')
@section('title', config('global.site_name') . ' - Empresa')
@php
    $enterpriseState = [
        'enterprise'  => $etp->toArray(),
        'images'      => [
            'foto_representante' => $etp->foto_representante ? asset('storage/' . $etp->foto_representante) : null,
            'logo'               => $etp->logo ? asset('storage/' . $etp->logo) : null,
            'logo_mini'          => $etp->logo_mini ? asset('storage/' . $etp->logo_mini) : null,
            'logo_receta'        => $etp->logo_receta ? asset('storage/' . $etp->logo_receta) : null,
        ],
        'permissions'   => [
            'update'    => auth()->user()?->can('empresa_actualizar') ?? false,
        ],
    ];
@endphp
@section('content')
    <div id="vue-enterprise"></div>
@endsection
@push('scripts')
    <script>
        window.__ENTERPRISE_STATE__ = @json($enterpriseState);
    </script>
    @vite(['resources/js/modules/enterprise.js'])
@endpush