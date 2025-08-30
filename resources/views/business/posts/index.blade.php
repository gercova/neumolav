@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Publicaciones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item">Negocio</li>
                        <li class="breadcrumb-item active">Publicaciones</li>
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
                            <a href="{{ route('business.posts.add') }}" class="btn btn-outline btn-primary" id="btn-add-category"><i class="bi bi-plus-circle"></i> Agregar nuevo post</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm" id="post_data">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Título</th>
                                            <th>Tipo</th>
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
<script src="{{ asset('js/forms/publications.js') }}"></script>
@endsection