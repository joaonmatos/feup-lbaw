@extends('layouts.app')

@section('title')
<title>Submit new story - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')
<h1 class="text-primary">New Story</h1>
<div class="row">
    <form class="col-6" method="POST" action="{{route('new-story-action')}}">
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
    </form>
</div>
@endsection