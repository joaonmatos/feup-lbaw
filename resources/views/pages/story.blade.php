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
            <a href="{{$story['url']}}">
                <i class="fas fa-external-link-alt mx-1"></i>
                {{ $story['url'] }}
            </a>
        </small>
        <small class="mx-1">
            <a href="/users/{{ $story['username'] }}">
                <i class="fas fa-user mx-1"></i>
                <?= $story['username'] ?>
            </a>
        </small>
        <small class="text-muted mx-1">
            <i class="fas fa-clock mx-1"></i>
            <span data-toggle="tooltip" title="Published on {{ strftime('%G-%m-%d', strtotime($story['published_date'])) }}">
                @php
                $date = $story['published_date'];
                $timestamp = strtotime($date);

                $str_time = array("second", "minute", "hour", "day", "month", "year");
                $length = array("60", "60", "24", "30", "12", "10");

                $currentTime = time();
                if ($currentTime >= $timestamp) {
                    $diff = time()- $timestamp;
                    for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
                        $diff=$diff / $length[$i];
                    }
                    $diff=round($diff);
                    if ($diff==1) {
                        echo $diff . " " . $str_time[$i] . "ago" ;
                    } else {
                        echo $diff . " " . $str_time[$i] . "s ago";
                    }
                }
                @endphp
            </span>
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
                                @if ($can_delete)
                                <form method="post" action="{{url('/stories/' . $story['id'])}}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link mx-1">Delete Story</button>
                                </form>
                                @endif
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
                            @for ($i = 0; $i < count($comments); $i++) <div class="card card-body">
                                <div class="d-inline-block mb-2">
                                    <small class="mx-1">
                                        <a href="/users/{{ $comments[$i]['username'] }}">
                                            <i class="fas fa-user mr-1"></i>
                                            <?= $comments[$i]["username"] ?>
                                        </a>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-clock mx-1"></i>
                                        <span data-toggle="tooltip" title="Published on {{ strftime('%G-%m-%d', strtotime($comments[$i]['published_date'])) }}">
                                            @php
                                            $date = $comments[$i]['published_date'];
                                            $timestamp = strtotime($date);

                                            $str_time = array("second", "minute", "hour", "day", "month", "year");
                                            $length = array("60", "60", "24", "30", "12", "10");

                                            $currentTime = time();
                                            if ($currentTime >= $timestamp) {
                                            $diff = time()- $timestamp;
                                            for ($j = 0; $diff >= $length[$j] && $j < count($length) - 1; $j++) { $diff=$diff / $length[$j]; } $diff=round($diff); if ($diff==1) { echo $diff . " " . $str_time[$j] . "ago" ; } else { echo $diff . " " . $str_time[$j] . "s ago" ; } } @endphp </span> </small> </div> <?= $comments[$i]["content"] ?> <div class="d-flex justify-content-end print-hide">
                                                <button class="btn btn-outline-primary px-4">Reply</a>
                                </div>
                        </div>
                        @endfor
                    </div>
    </div>
    @endsection