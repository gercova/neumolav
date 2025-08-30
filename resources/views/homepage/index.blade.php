@extends('homepage.app')
@section('content')
    <!-- HOME -->
    <section id="home" class="slider" data-stellar-background-ratio="0.5">
        <div class="container">
            <div class="row">
                <div class="owl-carousel owl-theme">
                    <div class="item item-first" style="background-image: url('{{ asset('storage/img/homepage/slider1.jpg') }}');">

                        <div class="caption">
                            <div class="col-md-offset-1 col-md-10">
                                <h3>Hagamos tu vida más feliz</h3>
                                <h1>Vida Saludable</h1>
                                <a href="#appointment" class="section-btn btn btn-default smoothScroll">Realice su cita</a>
                            </div>
                        </div>
                    </div>
                    <div class="item item-second" style="background-image: url('{{ asset('storage/img/homepage/slider2.jpg') }}');">
                        <div class="caption">
                            <div class="col-md-offset-1 col-md-10">
                                <h3>Tú salud en manos de expertos</h3>
                                <h1>Cuidamos tu salud</h1>
                                <a href="{{ route('nosotros') }}" class="section-btn btn btn-default btn-gray smoothScroll">Más sobre nosotros</a>
                            </div>
                        </div>
                    </div>
                    <div class="item item-third" style="background-image: url('{{ asset('storage/img/homepage/slider3.jpg') }}');">
                        <div class="caption">
                            <div class="col-md-offset-1 col-md-10">
                                <h3>Realize sus exámenes y controles</h3>
                                <h1>Beneficios para tu salud</h1>
                                <a href="{{ route('posts', null) }}" class="section-btn btn btn-default btn-blue smoothScroll">Ver nuestras publicaciones</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ABOUT -->
    <section id="about">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="about-info">
                        <h2 class="wow fadeInUp" data-wow-delay="0.6s">Somos {{ $enterprise[0]->nombre_comercial }}</h2>
                        <div class="wow fadeInUp" data-wow-delay="0.8s">
                            <p>{{ $enterprise[0]->frase }}</p>
                        </div>
                        <figure class="profile wow fadeInUp" data-wow-delay="1s">
                            <img src="{{ asset('storage/'.$enterprise[0]->foto_representante) }}" class="img-responsive" alt="">
                            <figcaption>
                                <h3>{{ $enterprise[0]->representante_legal }}</h3>
                                <p>Médico Neumólogo</p>
                            </figcaption>
                        </figure>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: center;">
                    <img src="{{ asset('storage/img/homepage/neumotar_doc.jpg') }}" alt="" width="50%">
                </div>
            </div>
        </div>
    </section>
    <!-- NEWS -->
    <section id="news" data-stellar-background-ratio="2.5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="section-title wow fadeInUp" data-wow-delay="0.1s">
                        <h2>Nuestros Servicios</h2>
                    </div>
                </div>
                @foreach ($posts as $p)
                    <div class="col-md-4 col-sm-6">
                        <div class="news-thumb wow fadeInUp" data-wow-delay="0.4s">
                            <a href="{{ route('post.show', $p->url) }}">
                                <div style="height: 300px; overflow: hidden; display: flex; justify-content: center; align-items: center; background: #f0f0f0;">
                                    <img src="{{ asset('storage/'.$p->img) }}" class="img-responsive" style="width: 100%; height: 100%; object-fit: cover; object-position: center;" alt="{{ $p->alt_img }}">
                                </div>
                            </a>
                            <div class="news-info">
                                <span>{{ $p->formated_date }}</span>
                                <h3>
                                    <a href="{{ route('post.show', $p->url) }}">{{ $p->titulo }}</a>
                                    <span class="badge text-bg-secondary">{{ $p->tipo }}</span>
                                </h3>
                                <p>{!! $p->resumen !!}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- MAKE AN APPOINTMENT -->
    <section id="appointment" data-stellar-background-ratio="3">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <img src="{{ asset('storage/img/homepage/appointment-image.png') }}" class="img-responsive" alt="">
                </div>
                <div class="col-md-6 col-sm-6">
                    <!-- CONTACT FORM HERE -->
                    <form id="appointment-form" method="post">
                        @csrf
                        <div class="section-title wow fadeInUp" data-wow-delay="0.4s">
                            <h2>Realice su cita</h2>
                        </div>
                        <div class="wow fadeInUp" data-wow-delay="0.8s">
                            <div class="col-md-12 col-sm-12" id="result"></div>
                            <div class="col-md-6 col-sm-6">
                                <label for="name">Nombres</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nombres completos" required>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Tu Email" required>
                            </div>
                            <div class="col-md-12 col-sm-12">
                                <label for="phone">Número de celular</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="Número de celular" required>
                                <label for="message">Información adicional</label>
                                <textarea class="form-control" rows="5" id="message" name="message" placeholder="Información complementaria..."></textarea>
                                <button type="submit" class="form-control" id="cf-submit" name="submit">Enviar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- GOOGLE MAP -->
    <section id="map">
        {!! $enterprise[0]->iframe_location !!}
    </section>
@endsection