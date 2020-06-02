@extends('layouts.app')

@section('title')
<title>About Us - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')
<section id="about" class="container mt-4 mb-4 ">
    <h1>About Us</h1>
    <hr>
    <section id="description" class="mb-4 row">
            <div class="col-sm-6">
                <p>News.ly is your hub for all information.</p>
                <p>Follow your favourite topic, stay up to date with the latest news, curate your own feed, post new stories to the platform, rate stories and interact with the community on the comment section.</p>
                <p>Leveraging the community and the power of the internet, news.ly is the ultimate news platform on the web.</p>
            </div>

            <div class="col-sm-6">
                <img class="img-fluid" style="max-height: 250px;" src="{{url('/images/news.jpg')}}" alt="news_symbol">
            </div>
    </section>


    <section id="team">
        <h2 class="mb-3">Our team</h2>

        <div class="row text-center pb-3">
            <div class="col-md-4 d-flex justify-content-center">
                <div class="card text-center" style="width: 14rem;">
                    <img class="card-img-top img-fluid" src="{{url('/images/gonc.jpg')}}" alt="go">
                    <div class="card-body">
                        <h5>Gonçalo Oliveira</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex justify-content-center">
                <div class="card text-center" style="width: 14rem;">
                    <img class="card-img-top img-fluid" src="{{url('/images/joana.jpg')}}" alt="jf">
                    <div class="card-body">
                        <h5>Joana Ferreira</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex justify-content-center">
                <div class="card text-center" style="width: 14rem;">
                    <img class="card-img-top img-fluid" src="{{url('/images/joao.jpg')}}" alt="jm">
                    <div class="card-body">
                        <h5>João Monteiro</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row text-center">
            <div class="col-md-12 d-flex justify-content-center">
                <div class="card" style="width: 14rem;">
                    <img class="card-img-top img-fluid" src="{{url('/images/matos.jpg')}}" alt="jnm">
                    <div class="card-body">
                        <h5>João Nuno Matos</h5>
                    </div>
                </div>
            </div>
        </div>

    </section>
</section>
@endsection