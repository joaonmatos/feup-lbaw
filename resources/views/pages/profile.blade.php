@extends('layouts.app')


@section('title')
<title>{{ $username }}'s Profile - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')
<div>
    <header class="row">
        <div class="col-md-8">
            <h1 class="display-2">{{ $username }}</h3>
        </div>
        <div class="col-md-4 text-right">
            <div class="my-2">
                <span class="mx-2">{{ count($followers) }} @if(count($followers) == 1) follower @else followers @endif</span>
                <span class="mx-2">{{ count($following) }} following</span>
            </div>
            @if(Auth::check())
                @if(strcmp(Auth::getUser()->username, $username) != 0)
                    @if($is_follower)
                    <form class="form-signin" method="POST" action="/users/{{$username}}/unfollow">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary mt-1">Unfollow</button>
                    </form>
                    @else
                    <form class="form-signin" method="POST" action="/users/{{$username}}/follow">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary mt-1">Follow</button>
                    </form>
                    @endif
                @endif
            @endif
        </div>
    </header>

    <nav>
        <ul class="nav nav-pills my-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-posts" data-toggle="pill" href="#profilePosts" role="tab" aria-controls="profilePosts" aria-selected="true">Posts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-comments" data-toggle="pill" href="#profileComments" role="tab" aria-controls="profileComments" aria-selected="false">Comments</a>
            </li>
        </ul>
    </nav>
    <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="profilePosts" role="tabpanel" aria-labelledby="profile-posts">
            @foreach ($stories as $story)
                @include('partials.card', ['story' => $story, 'comments' => $story_comments, 'topics' => $story_topics])
            @endforeach
    </div>
        <div class="tab-pane fade" id="profileComments" role="tabpanel" aria-labelledby="profile-comments">             
            <div>
                @for ($i = 0; $i < count($comments); $i++) 
                <div class="card card-body mb-2">
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
                                        for ($j = 0; $diff >= $length[$j] && $j < count($length) - 1; $j++) {
                                            $diff = $diff / $length[$j];
                                        }
        
                                        $diff = round($diff);
                                        if ($diff == 1) {
                                            echo $diff . " " . $str_time[$j] . "ago";
                                        } else {
                                            echo $diff . " " . $str_time[$j] . "s ago";
                                        }
                                    
                                    }
                                @endphp
                                </span>

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
    </div>
</div>
@endsection