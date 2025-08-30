<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/'.$enterprise[0]->logo) }}">
    <title>@yield('title', $enterprise[0]->nombre_comercial . ' - Especialista en enfermedades respiratorias')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Especialista en enfermedades respiratorias')">
    <meta name="keywords" content="@yield('keywords', 'enfermedades respiratorias, neumotar, neumotar.com')">
    <meta name="author" content="Tamayiyo">
    <!-- Optimización en motores de búsqueda por Rank Math PRO -  https://rankmath.com/ -->    
    <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large">
    <link rel="canonical" href="{{ request()->url() }}">
    <meta property="og:locale" content="{{ app()->getLocale() }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="@yield('title', $enterprise[0]->nombre_comercial . ' - Especialista en enfermedades respiratorias')">
    <meta property="og:description" content="@yield('description', 'Especialista en enfermedades respiratorias')">
    <meta property="og:image" content="{{ asset('storage/'.$enterprise[0]->logo) }}">
    <meta property="og:description" content="@yield('description', 'Especialista en enfermedades respiratorias')">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="{{ $enterprise[0]->nombre_comercial }}">
    <!--<meta property="article:section" content="Tutoriales">-->
    <meta property="og:updated_time" content="{{ $enterprise[0]->updated_at }}">
    <!--<meta property="og:image" content="https://laravelole.es/wp-content/uploads/2024/12/integracion-ia-aplicacion-laravel.jpg">-->
    <!--<meta property="og:image:secure_url" content="https://laravelole.es/wp-content/uploads/2024/12/integracion-ia-aplicacion-laravel.jpg">-->
    <!--<meta property="og:image:width" content="1080">-->
    <!--<meta property="og:image:height" content="480">-->
    <!--<meta property="og:image:alt" content="integracion-ia-aplicacion-laravel">-->
    <meta property="og:image:type" content="image/jpeg">
    <meta property="article:published_time" content="{!! $enterprise[0]->created_at !!}">
    <meta property="article:modified_time" content="{!! $enterprise[0]->updated_at !!}">
    <!--<meta name="twitter:card" content="summary_large_image">-->
    <meta name="twitter:title" content="@yield('title', $enterprise[0]->nombre_comercial . ' - Especialista en enfermedades respiratorias')">
    <meta name="twitter:description" content="@yield('description', 'Especialista en enfermedades respiratorias')">
    <!--<meta name="twitter:image" content="https://laravelole.es/wp-content/uploads/2024/12/integracion-ia-aplicacion-laravel.jpg">-->
    <!--<meta name="twitter:label1" content="Escrito por">-->
    <!--<meta name="twitter:data1" content="osgarcia@10code.es">-->
    <!--<meta name="twitter:label2" content="Tiempo de lectura">-->
    <!--<meta name="twitter:data2" content="3 minutos">-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="{{ asset('homepage/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('homepage/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('homepage/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('homepage/css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('homepage/css/owl.theme.default.min.css') }}">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('homepage/css/tooplate-style.css') }}">
    <script>
        const API_BASE_URL = "{{ url('/') }}";
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2@11.js') }}"></script>
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <!-- PRE LOADER -->
    <section class="preloader">
        <div class="spinner">
            <span class="spinner-rotate"></span> 
        </div>
    </section>
    <!-- HEADER -->
    <header>
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-5">
                    <p>Bienvenido a {{ $enterprise[0]->nombre_comercial }}</p>
                </div>
                <div class="col-md-8 col-sm-7 text-align-right">
                    <span class="phone-icon"><i class="fa fa-phone"></i>{{ $enterprise[0]->codigo_pais.' '.$enterprise[0]->telefono }}</span>

                    <span class="date-icon"><i class="fa fa-calendar-plus-o"></i> 2:00 PM - 7:00 PM (Lunes - Viernes)</span>
                    <span class="email-icon"><i class="fa fa-envelope-o"></i> <a href="#">{{ $enterprise[0]->email }}</a></span>
                </div>
            </div>
        </div>
    </header>
    <!-- MENU -->
    <section class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                </button>
                <!-- lOGO TEXT HERE -->
                <a href="{{ url('/') }}" class="navbar-brand">{{ $enterprise[0]->nombre_comercial }}</a>
            </div>
            <!-- MENU LINKS -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{ url('/') }}" class="smoothScroll">Inicio</a></li>
                    <li><a href="{{ route('nosotros') }}" class="smoothScroll">Nosotros</a></li>
                    <!--<li><a href="#team" class="smoothScroll">Nuestros Servicios</a></li>-->
                    <li><a href="{{ route('posts', null) }}" class="smoothScroll">Publicaciones</a></li>
                    <li><a href="{{ url('/') }}#map" class="smoothScroll">Contacto</a></li>
                    <li class="appointment-btn"><a href="{{ url('/') }}#appointment">Realice su cita</a></li>   
                </ul>
            </div>
        </div>
    </section>
    @yield('content')
    <!-- FOOTER -->
    <footer data-stellar-background-ratio="5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4">
                    <div class="footer-thumb"> 
                        <h4 class="wow fadeInUp" data-wow-delay="0.4s">Información de contacto</h4>
                        <p>{{ $enterprise[0]->descripcion }}</p>
                        <div class="contact-info">
                            <p><i class="fa fa-phone"></i> {{ $enterprise[0]->telefono }}</p>
                            <p><i class="fa fa-envelope-o"></i> <a href="#">{{ $enterprise[0]->email }}</a></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4"> 
                    <div class="footer-thumb"> 
                        <h4 class="wow fadeInUp" data-wow-delay="0.4s">Lo último</h4>
                        @foreach ($recentPost as $post)
                            <div class="latest-stories">
                                <div class="stories-image">
                                    <a href="{{ route('post.show', $post->url) }}"><img src="{{ asset('storage/'.$post->img) }}" class="img-responsive" alt="{{ $post->alt_img }}"></a>
                                </div>
                                <div class="stories-info">
                                    <a href="{{ route('post.show', $post->url) }}"><h5>{{ $post->titulo_corto }}</h5></a>
                                    <span>{{ $post->created_at->format('d-m-Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4 col-sm-4"> 
                    <div class="footer-thumb">
                        <div class="opening-hours">
                            <h4 class="wow fadeInUp" data-wow-delay="0.4s">Horario de atención</h4>
                            <p>Lunes - Viernes <span>2:00 PM - 7:00 PM</span></p>
                            <p>Sábado <span>3:00 PM - 5:00 PM</span></p>
                            <p>Domingo <span>Cerrado</span></p>
                        </div> 
                        <ul class="social-icon">
                            <li><a href="https://www.facebook.com/neumologotarapoto" _blank class="fa fa-facebook-square" attr="facebook icon"></a></li>
                            <!--<li><a href="#" class="fa fa-twitter"></a></li>-->
                            <!--<li><a href="#" class="fa fa-instagram"></a></li>-->
                        </ul>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 border-top">
                    <div class="col-md-4 col-sm-6">
                        <div class="copyright-text"> 
                            <p>{{ $enterprise[0]->nombre_comercial }} &copy; {{ date('Y') }} | Diseñado por Tamayiyo</p>
                        </div>
                    </div>
                    <!--<div class="col-md-6 col-sm-6">
                        <div class="footer-link"> 
                            <a href="#">Laboratory Tests</a>
                            <a href="#">Departments</a>
                            <a href="#">Insurance Policy</a>
                            <a href="#">Careers</a>
                        </div>
                    </div>-->
                    <div class="col-md-2 col-sm-2 text-align-center">
                        <div class="angle-up-btn"> 
                            <a href="#top" class="smoothScroll wow fadeInUp" data-wow-delay="1.2s"><i class="fa fa-angle-up"></i></a>
                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </footer>
    <!-- SCRIPTS -->
    <script src="{{ asset('homepage/js/jquery.js') }}"></script>
    <script src="{{ asset('homepage/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('homepage/js/jquery.sticky.js') }}"></script>
    <script src="{{ asset('homepage/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('homepage/js/wow.min.js') }}"></script>
    <script src="{{ asset('homepage/js/smoothscroll.js') }}"></script>
    <script src="{{ asset('homepage/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('homepage/js/custom.js') }}"></script>
    <script src="{{ asset('js/forms/sendmail.js') }}"></script>

</body>
</html>