@extends('layouts.app')

@section('title')
<title>Manage Your Topics - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')

<p>Topics you are following</p>
<table>

    @foreach ($followed as $topic)
    <div class="row">
        <div class="col-sm"><a href="/topics/{{ $topic }}">#{{ $topic }}</a></div>
        <form class="form-signin" method="POST" action="/topics/{{ $topic }}/unfollow">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-primary mt-1">Unfollow</button>
        </form>
    </div>
    @endforeach
</table>

<br/>

<p>Other Topics</p>
<table>
    @foreach ($other_topics as $topic)
    <div class="row">
        <div class="col-sm"><a href="/topics/{{ $topic }}">#{{ $topic }}</a></div>
        <form class="form-signin" method="POST" action="/topics/{{ $topic }}/follow">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-primary mt-1">Follow</button>
        </form>
    </div>
    @endforeach
</table>

@endsection