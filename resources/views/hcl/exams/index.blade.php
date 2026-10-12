@extends('layouts.app')
@section('title', config('global.site_name') . ' - Exámenes')
@section('content')
    <div id="vue-exams"></div>
@endsection
@push('scripts')
    @vite(['resources/js/modules/exams.js'])
@endpush
