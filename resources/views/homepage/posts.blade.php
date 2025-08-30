@extends('homepage.app')

@section('title', 'Prueba DLCO en Tarapoto | Diagnóstico de Fibrosis Pulmonar y EPOC | Neumotar - Tarapoto') <!-- Título dinámico -->
@section('description', '🔍 Prueba DLCO en Tarapoto | Diagnóstico de Fibrosis Pulmonar y EPOC | Neumotar - Tarapoto
    💨 Espirometría en Tarapoto | Detección de Asma y EPOC | Resultados Inmediatos ✔️
    🌬️ Test FeNO en Tarapoto | Medición de Inflamación en Asma | Neumotar
    🩺 Procedimientos Pulmonares Ecoguiados | Biopsias y Drenajes | Neumotar - Tarapoto
    👨‍⚕️ Neumólogo en Tarapoto | Especialista en Enfermedades Respiratorias | Neumotar
')
@section('keywords', 'prueba dlco Tarapoto, dlco para fibrosis pulmonar, capacidad de difusión pulmonar, espirometría Tarapoto, prueba de función pulmonar, espirometría para asma, como se hace una espirometría, prueba feno Tarapoto, óxido nítrico exhalado, feno para asma, inflamación vías respiratorias, prueba feno precio, biopsia pulmonar Tarapoto, toracocentesis ecoguiada, drenaje torácico Tarapoto, catéter pig tail pleural, neumólogo intervencionista, neumólogo en Tarapoto, especialista en pulmones, tos crónica diagnóstico, disnea falta de aire, epoc tratamiento Tarapoto
')

@section('content')
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
                        <!-- NEWS THUMB -->
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
                        <br>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection