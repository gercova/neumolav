@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Presentación Fármaco</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item">Mantenimiento</li>
                        <li class="breadcrumb-item active">Presentación Fármaco</li>
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
                        <div class="card-header">
                            <button type="button" class="btn btn-outline btn-primary" id="btn-tpd"><i class="bi bi-plus-circle"></i> Agregar tipo fármaco</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm" id="presentation_data">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Descripción</th>
                                            <th>Alias</th>
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
<div class="modal fade" id="modalTDP" tabindex="-1" aria-modal="true" role="dialog" data-backdrop="static" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="presentationForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="descripcion">Descripción: </label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion">
                    </div>
                    <div class="form-group">
                        <label for="aka">Alias: </label>
                        <input type="text" class="form-control" id="aka" name="aka">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <input type="hidden" name="tdpId" id="tdpId">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('js/forms/presentations.js') }}"></script>
@endsection