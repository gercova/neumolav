<!DOCTYPE html>
<html lang="es_PE">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/'.$enterprise[0]->logo) }}">
    <title>Login {{ $enterprise[0]->nombre_comercial }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <script>
        const API_BASE_URL = "{{ url('/') }}";
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
    <!-- SweetAlert 2 -->
    <script src="{{ asset('sweetalert2/sweetalert2@11.js') }}"></script>
    <!-- Axios -->
    <script src="{{ asset('js/axios.min.js') }}"></script>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div>
                <picture>
                    <source srcset="{{ asset('storage/homepage/isologo-neumotar-25.png') }}" type="image/png">
                    <img src="{{ asset('storage/homepage/isologo-neumotar-25.png') }}" alt="Logo" class="img-fluid">
                </picture>
            </div>
            <div class="card-header text-center">
                <!--<a href="#" class="h1"><b>{{ $enterprise[0]->nombre_comercial }}</b></a>-->
                <p class="login-box-msg">Especialista en enfermedades respiratorias</p>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Bienvenido, ingrese sus credenciales</p>
                <div id="message"></div>
                <form method="post" id="loginForm">
                    @csrf
                    <div class="form-group row">
                        <input type="email" id="email" name="email" class="form-control" placeholder="E-mail">
                    </div>
                    <div class="form-group row">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Clave y/o Contraseña">
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" id="loginButton" class="btn btn-primary float-right">Iniciar sesión</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
    <script src="{{ asset('js/forms/login.js') }}"></script>
</body>
</html>