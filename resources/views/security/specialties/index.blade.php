@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Especialidades</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item">Seguridad</li>
                        <li class="breadcrumb-item active">Especialidades</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @can('especialidad_crear')
                            <div class="card-header">
                                <button type="button" class="btn btn-outline btn-primary" id="btn-add-specialty"><i class="bi bi-plus-circle"></i> Agregar especialidad</button>
                            </div>
                        @endcan
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm" id="specialty_data">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Descripción</th>
                                            <th>Fecha</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@can('especialidad_crear')
    <div class="modal fade" id="modalSpecialty" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="specialtyForm" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="id_ocupacion">Cargo: </label>
                            <select class="slim-select" id="id_ocupacion" name="id_ocupacion">
                                <option value="">-- Seleccione --</option>
                                @foreach ($oc as $o)
                                    <option value="{{ $o->id }}">{{ $o->descripcion }}</option>    
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción: </label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion">
                        </div>
                        <div class="form-group">
                            <label for="detalle">Detalle: </label>
                            <input type="text" class="form-control" id="detalle" name="detalle">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <input type="hidden" name="specialtyId" id="specialtyId">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Grabar datos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan
<script src="{{ asset('js/forms/specialties.js') }}"></script>
@endsection