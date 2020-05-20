@extends('layouts.app')

@section('title')
<title>{{ $story['title'] }} - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')
<div class="d-flex justify-content-center mb-3">
    <img src="https://image.shutterstock.com/image-vector/example-sign-paper-origami-speech-260nw-1164503347.jpg" class="img-fluid mb-2 w-100" alt="Article thumbnail">
</div>

<h2 class="display-5 my-0"><?= $story["title"] ?></h2>

<div class="d-flex mb-2">
    <div class="flex-grow-1">
        @for ($i = 0; $i < count($topics); $i++) <a href="/topics/{{ $topics[$i]["name"] }}"> #{{ $topics[$i]["name"] }} </a>
            @endfor
    </div>

    <div>
        <small class="mx-1">
            <a href="/users/{{ $story['username'] }}">
                <i class="fas fa-user mx-1"></i>
                <?= $story['username'] ?>
            </a>
        </small>
        <small class="text-muted mx-1">
            <i class="fas fa-clock mx-1"></i>
            <?= $story['published_date'] ?> ago
        </small>
    </div>
</div>

<div id="comments">
    <div class="form-group mt-2 print-hide">
        <textarea class="form-control" id="writeComment" rows="3" placeholder="What's on your mind?"></textarea>
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                {{-- Voting display --}}
            </div>
            <button type="button" class="btn btn-primary my-2 text-light" onClick="commentStory({{$story['id']}})">Comment</button>
        </div>
    </div>

    <div class="d-flex">
        <span class="flex-grow-1">
            {{ count($comments) }}
            {{ count($comments) == 1 ? " Comment" : " Comments" }}
        </span>
        <p class="mr-1">Sort by: </p>
        <div class="dropdown">
            <a href="" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Hot</a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="">Hot</a>
                <a class="dropdown-item" href="">Top</a>
                <a class="dropdown-item" href="">New</a>
            </div>
        </div>
    </div>


    <div>
        @for ($i = 0; $i < count($comments); $i++) 
        <div class="card card-body">
            <div class="d-inline-block mb-2">
                <small class="mx-1">
                    <a href="/users/{{ $comments[$i]['username'] }}">
                        <i class="fas fa-user mr-1"></i>
                        <?= $comments[$i]["username"] ?>
                    </a>
                </small>
                <small class="text-muted">
                    <i class="fas fa-clock mx-1"></i>
                    <?= $comments[$i]["published_date"] ?> ago
                </small>
            </div>
            <?= $comments[$i]["content"] ?>
            <div class="d-flex justify-content-end print-hide">
                <button class="btn btn-outline-primary px-4">Reply</a>
            </div>
        </div>
        @endfor
    </div>
</div>
@endsection