@extends('layouts.app')

@section('title')
<title>Feed for #{{ $topic_name }} - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')
<header class="container d-flex mb-3 align-items-center">
    <h1 class="h3 text-primary mr-3" id=feed-title>
        @if ($search)
            {{ "Search Results" }}
            {{-- <a class="btn btn-outline-primary mx-3 print-hide" onClick="openForm()">Advanced search</a> --}}
            
        @else
            {{ "Popular stories in #" . $topic_name }}
        @endif
    </h1>
    <a href="{{route('new-story-form')}}" class="btn btn-outline-primary mx-3 print-hide">New Story</a>
    
    @if ($search)
        <form action="/action_page.php" class="form-container">
            <div class="dropdown">
                <a class="dropdown-toggle mx-2 text-primary" data-toggle="dropdown">Today</a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="advanced-search?query={{app('request')->input('query')}}&time_filter=today">Today</a>
                    <a class="dropdown-item" href="advanced-search?query={{app('request')->input('query')}}&time_filter=last-week">Last Week</a>
                    <a class="dropdown-item" href="advanced-search?query={{app('request')->input('query')}}&time_filter=last-month">Last month</a>
                    <a class="dropdown-item" href="advanced-search?query={{app('request')->input('query')}}&time_filter=all-time">All time</a>
                </div>
            </div>
        </form>
    @endif
    {{-- Uncomment to add sort --}}
    {{-- <span class="small text-right flex-grow-1 ml-3">Sort by: </span>
    <div class="dropdown">
        <a class="dropdown-toggle mx-2 text-primary" data-toggle="dropdown">Hot</a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="">Hot</a>
            <a class="dropdown-item" href="">New</a>
            <a class="dropdown-item" href="">Top (day)</a>
            <a class="dropdown-item" href="">Top (all time)</a>
        </div>
    </div> --}}
</header>

    

  {{-- <form class="form-popup col-6" id="myForm" method="POST" action="{{route('new-story-action')}}">
    {{ csrf_field() }}
    @if(count($errors))
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="my-2">
        <label data-toggle="tooltip" data-placement="right" title="Insert a title for the story" for="title" class="h3">Title</label>
        <br>
        <input type="text" name="title" id="title" value="{{ old('title') }}">
    </div>
    <div class="my-2">
        <label data-toggle="tooltip" data-placement="right" title="Insert a link to the original story" for="link" class="h3">Link</label>
        <br>
        <input type="url" name="link" id="link" value="{{ old('link') }}">
    </div>
    <div class="my-2">
        <span data-toggle="tooltip" data-placement="right" title="Insert at most 3 topics for this story" class="h3">Topics</span>
        <br>
        <input type="text" name="topic1" id="topic1" class="my-1" value="{{ old('topic1') }}">
        <br>
        <input type="text" name="topic2" id="topic2" class="my-1" value="{{ old('topic2') }}">
        <br>
        <input type="text" name="topic3" id="topic3" class="my-1" value="{{ old('topic3') }}">
    </div>
    <button type="submit" class="btn btn-primary my-2">Post Story</button>
</form> --}}

@foreach ($stories as $story)
    @include('partials.card', ['story' => $story, 'comments' => $comments, 'topics' => $topics])
@endforeach
@endsection