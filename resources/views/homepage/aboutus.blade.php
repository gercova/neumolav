@extends('homepage.app')

@section('title', 'Neumotar - '.$post->titulo) <!-- Título dinámico -->
@section('description', $post->meta_content)
@section('keywords', $post->key_words)

@section('content')
<section id="news-detail" data-stellar-background-ratio="0.5">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-7">
                <!-- NEWS THUMB -->
                <div class="news-detail-thumb">
                    <div class="news-image">
                        <img src="{{ asset('storage/'.$post->img) }}" class="img-responsive" alt="{{ $post->alt_img }}">
                    </div>
                    <h3>
                        {{ $post->titulo }}
                    </h3>
                    {!! $post->contenido !!}
                </div>
            </div>

            <div class="col-md-4 col-sm-5">
                <div class="news-sidebar">
                    <div class="news-author">
                        <h4>Nuestro compromiso</h4>
                        <p>Trabajamos cada día para ser ese faro de alivio y cuidado en la vasta selva, asegurando que cada persona, sin importar su origen, sienta que tiene un guardián en su lucha por respirar. Porque en <b>Neumotar</b>, creemos que el bienestar de una comunidad se mide por la salud del aire que comparte. Porque tu aliento, es nuestro motivo.</p>
                    </div>
                    <div class="recent-post">
                        <h4>Publicaciones recientes</h4>
                        @foreach ($recentPost as $rp)
                            <div class="media">
                                <div class="media-object pull-left">
                                    <a href="{{ route('post.show', $rp->url) }}"><img src="{{ asset('storage/'.$rp->img) }}" class="img-responsive" alt="{{ $rp->alt_img }}"></a>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading"><a href="{{ route('post.show', $rp->url) }}">{{ $rp->titulo }}</a></h4>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="news-categories">
                        <h4>Categories</h4>
                        @php
                            $categories = explode(', ', $post->categories);
                        @endphp
                        @foreach($categories as $cat)
                            <li><a href="{{ route('posts', $cat) }}"><i class="fa fa-angle-right"></i> {{ $cat }}</a></li>
                        @endforeach
                    </div>

                    <div class="news-ads sidebar-ads">
                        <h4>Sidebar Banner Ad</h4>
                    </div>
                    <div class="news-tags">
                        <h4>Etiquetas</h4>
                        @php
                            $tags = explode(', ', $post->etiquetas);
                        @endphp
                        <div class="tags">
                            @foreach($tags as $tag)
                                <li><a href="{{ route('posts', $tag) }}">{{ $tag }}</a></li>
                            @endforeach
                        </div>
                    </div>
                    <div class="news-ads sidebar-ads">
                        <h4>Sidebar Banner Ad</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection