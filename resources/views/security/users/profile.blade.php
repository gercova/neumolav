@extends('layouts.app')
@section('title', config('global.site_name').' - Mi Perfil y Firma Digital')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Mi Perfil y Credenciales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Mi Perfil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-person-badge"></i> Datos del Especialista: <b>{{ $user->name }}</b></h3>
                </div>
                <form id="profileForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill"></i>
                            <strong>Firma Digital y Credenciales:</strong> Configure su número de Colegiatura Médica (CMP), Registro Nacional de Especialista (RNE) y su firma digital en formato PNG con fondo transparente. Estos datos se utilizarán cuando decida incluir la firma digital al imprimir Informes Médicos y de Riesgo.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombres y Apellidos:</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico:</label>
                                    <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="specialty">Especialidad:</label>
                                    <select class="form-control" id="specialty" name="specialty">
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($es as $e)
                                            <option value="{{ $e->id }}" {{ $user->specialty == $e->id ? 'selected' : '' }}>{{ $e->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cmp">Número de Colegiatura Médica (CMP):</label>
                                    <input type="text" class="form-control" id="cmp" name="cmp" value="{{ $user->cmp }}" placeholder="Ej: 60432">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rne">Registro Nacional de Especialista (RNE):</label>
                                    <input type="text" class="form-control" id="rne" name="rne" value="{{ $user->rne }}" placeholder="Ej: 39261">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="avatar">Foto de Perfil (Avatar):</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image-input" name="avatar" accept="image/*">
                                        <label class="custom-file-label" for="avatar">Elegir foto</label>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <img id="image-preview" src="{{ $user->profile_photo_url }}" class="img-thumbnail rounded-circle" style="width: 140px; height: 140px; object-fit: cover;">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firma_digital">Firma Digital (PNG transparente):</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="signature-input" name="firma_digital" accept="image/png,image/jpeg">
                                        <label class="custom-file-label" for="firma_digital">Elegir firma</label>
                                    </div>
                                    <small class="form-text text-muted">Recomendado: PNG fondo transparente (máx. 2MB).</small>
                                </div>
                                <div class="form-group text-center p-3 bg-light border rounded">
                                    <img id="signature-preview" src="{{ $user->digital_signature_url ?: asset('dist/img/default-150x150.png') }}" class="img-fluid" style="max-height: 120px; {{ !$user->digital_signature_url ? 'opacity: 0.25;' : '' }}">
                                    <p class="text-muted small mt-2 mb-0 font-weight-bold">{{ $user->digital_signature_url ? 'Firma digital cargada' : 'Sin firma cargada' }}</p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="biografia">Biografía / Resumen Profesional:</label>
                                    <textarea class="form-control" id="biografia" name="biografia" rows="6" placeholder="Resumen de estudios, acreditaciones y especializaciones...">{{ $user->biografia }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('home') }}" class="btn btn-default"><i class="bi bi-arrow-left"></i> Volver al Inicio</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('js/forms/users.js') }}"></script>
@endsection
