@extends('layouts.app')

@section('content')
<header class="container d-flex mb-3 align-items-center">
    <h1 class="h3 text-primary mr-3" id=feed-title>
        {{ "Popular stories in #" . $topic_name }}
    </h1>
    <a href="{{route('new-story-form')}}" class="btn btn-outline-primary mx-3">New Story</a>
    <span class="small text-right flex-grow-1 ml-3">Sort by: </span>
    <div class="dropdown">
        <a class="dropdown-toggle mx-2 text-primary" data-toggle="dropdown">Hot</a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="">Hot</a>
            <a class="dropdown-item" href="">New</a>
            <a class="dropdown-item" href="">Top (day)</a>
            <a class="dropdown-item" href="">Top (all time)</a>
        </div>
    </div>
</header>

@foreach ($stories as $story)
    @include('partials.card', ['story' => $story, 'comments' => $comments, 'topics' => $topics])
@endforeach
@endsection