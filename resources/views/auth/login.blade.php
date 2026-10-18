<!DOCTYPE html>
<html lang="es_PE">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ !empty($enterprise[0]->logo) ? asset('storage/'.$enterprise[0]->logo) : asset('favicon.ico') }}">
    <title>Login - {{ $enterprise[0]->nombre_comercial ?? config('global.site_name', 'Neumotar') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css') }}">
    <!-- iCheck Bootstrap -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- AdminLTE Theme -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <!-- Vite CSS & Core App JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        const API_BASE_URL     = "{{ url('/') }}";
        const API_URL          = "{{ url('/') }}";
        const NAME_ENTERPRISE  = "{{ $enterprise[0]->nombre_comercial ?? config('global.site_name', 'Neumotar') }}";
        const token            = "{{ csrf_token() }}";
        window.API_BASE_URL    = API_BASE_URL;
        window.API_URL         = API_URL;
        window.NAME_ENTERPRISE = NAME_ENTERPRISE;
        window.token           = token;
    </script>
</head>
<body class="hold-transition login-page">
    {{-- Montaje SPA Vue 3 --}}
    <div id="vue-login"></div>

    {{-- Estado inicial provisto por AuthController --}}
    <script>
        window.__LOGIN_STATE__ = @json($loginState);
    </script>

    {{-- Módulo Vue SPA para Auth / Login --}}
    @vite(['resources/js/modules/auth.js'])

    {{-- Scripts de compatibilidad y dependencias legacy --}}
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2@11.js') }}"></script>
    <script src="{{ asset('js/forms/login.js') }}"></script>
</body>
</html>