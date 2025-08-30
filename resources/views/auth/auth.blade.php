<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/'.$enterprise[0]->logo) }}">
    <title>Login {{ $enterprise[0]->nombre_comercial }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('auth2/fonts/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ asset('auth2/css/owl.carousel.min.css') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('auth2/css/bootstrap.min.css') }}">
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('auth2/css/style.css') }}">
    <script>
        const API_BASE_URL = "{{ url('/') }}";
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    <script src="{{ asset('sweetalert2/sweetalert2@11.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
</head>
<body>
    <div class="d-lg-flex half">
        <div class="bg order-1 order-md-2" style="background-image: url('{{ asset('storage/homepage/neumotar_25.png') }}');"></div>

        <div class="contents order-2 order-md-1">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-7">
                        <h3>Login {{ $enterprise[0]->nombre_comercial }}</h3>
                        <p class="mb-4">Especialista en enfermedades respiratorias</p>
                        <form id="loginForm" method="post">
                            @csrf
                            <div class="form-group first">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" placeholder="your-email@neumotar.com" id="username">
                            </div>
                            <div class="form-group last mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" placeholder="Your Password" id="password">
                            </div>
                    
                            <div class="d-flex mb-5 align-items-center">
                                <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                                    <input type="checkbox" checked="checked"/>
                                    <div class="control__indicator"></div>
                                </label>
                            </div>
                            <input type="submit" value="Log In" class="btn btn-block btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('auth2/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('auth2/js/popper.min.js') }}"></script>
    <script src="{{ asset('auth2/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('auth2/js/main.js') }}"></script>
</body>
</html>