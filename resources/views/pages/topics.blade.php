@extends('layouts.app')

@section('title')
<title>Manage Your Topics - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')

<p>Topics you are following</p>
<table>
    @foreach ($followed as $topic)
    <tr>
        <td><a href="/topics/{{ $topic }}">#{{ $topic }}</a></td>
        <td><form class="form-signin" method="POST" action="/topics/{{ $topic }}/unfollow">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-primary mt-1">Unfollow</button>
        </form></td>
    <tr>
    @endforeach
</table>

<br/>

<p>Other Topics</p>
<table>
    @foreach ($other_topics as $topic)
    <tr>
        <td><a href="/topics/{{ $topic }}">#{{ $topic }}</a></td>
        <td><form class="form-signin" method="POST" action="/topics/{{ $topic }}/follow">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-primary mt-1">Follow</button>
        </form></td>
    <tr>
    @endforeach
</table>

@endsection