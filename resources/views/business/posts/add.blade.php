@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Nueva publicación</h1>
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
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Info de la publicación</h3>
                </div>
                <form method="post" id="postForm" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="titulo">Titulo: </label>
                                            <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo') }}">
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group">
                                            <label for="titulo_corto">Titulo corto: </label>
                                            <input type="text" class="form-control" id="titulo_corto" name="titulo_corto" value="{{ old('titulo_corto') }}">
                                        </div>
                                    </div>

                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="url">URL: </label>
                                            <input type="text" class="form-control" id="url" name="url" value="{{ old('url') }}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="role_id">Tipo de publicación: </label>
                                            <select class="form-control" id="type_id" name="type_id">
                                                <option value="">-- Seleccione --</option>
                                                @foreach ($tipo_post as $tp)
                                                    <option value="{{ $tp->id }}" {{ old('type_id') == $tp->id ? 'selected' : '' }}>{{ $tp->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="contenido">Contenido:</label>
                                            <textarea class="form-control form-control-sm summernote" id="contenido" name="contenido" rows="2">{{ old('contenido') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="resumen">Resumen:</label>
                                            <textarea class="form-control form-control-sm" id="resumen" name="resumen" rows="2">{{ old('resumen') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="img">Imagen del post: </label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="img" name="img" accept="image/*">
                                                <label class="custom-file-label" for="avatar">Elegir archivo</label>
                                            </div>
                                        </div>
                                        <div class="form-group text-center">
                                            <img id="image-preview" src="{{ asset('storage/img/anonymous.png') }}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        </div> 
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="col-form-label" for="descrip_img">Descripción imagen (SEO):</label>
                                            <textarea class="form-control form-control-sm" id="descrip_img" name="descrip_img" rows="2">{{ old('descrip_img') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="col-form-label" for="alt_img">Alt Imagen (SEO):</label>
                                            <textarea class="form-control form-control-sm" id="alt_img" name="alt_img" rows="2">{{ old('alt_img') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="categories">Categorias:</label>
                                            <input type="text" class="form-control form-control-sm" id="categories" name="categories" rows="2" value="{{ old('categories') }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="meta_content">Meta-content (SEO):</label>
                                            <textarea class="form-control form-control-sm" id="meta_content" name="meta_content" rows="2">{{ old('meta_content') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="etiquetas">Etiquetas:</label>
                                            <input type="text" class="form-control form-control-sm" id="etiquetas" name="etiquetas" value="{{ old('etiquetas') }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="key_words">Palabras clave (SEO):</label>
                                            <input type="text" class="form-control form-control-sm" id="key_words" name="key_words" value="{{ old('key_words') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('business.posts') }}" class="btn btn-danger"><i class="bi bi-box-arrow-left"></i> Volver</a>&nbsp;
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('js/forms/publications.js') }}"></script>
@endsection