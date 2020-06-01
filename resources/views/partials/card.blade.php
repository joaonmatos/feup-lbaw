<!-- Card begin -->
<article class="card my-3">
    <div class="row no-gutters">
        <aside class="col-md-4 text-hide" style="overflow:hidden; background-image: url(https://picsum.photos/500); background-size: cover;">
            <!-- Image column -->
            Thumbnail
        </aside>
        <div class="col-md-8 card-body px-2">
            <div class="d-flex flex-column">
                <div class="container d-flex my-2 align-items-center">
                    <small class="mx-2">
                        <a href="/users/{{ $story['username'] }}" class="card-link">
                            <i class="fas fa-user mx-1"></i>
                            <span data-toggle="tooltip" title="View user profile">{{ $story["username"]}}</span>
                        </a>
                    </small>
                    <small class="text-muted mx-2">
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
                                    $diff = $diff / $length[$i];
                                }

                                $diff = round($diff);
                                if ($diff == 1) {
                                    echo $diff . " " . $str_time[$i] . "ago";
                                } else {
                                    echo $diff . " " . $str_time[$i] . "s ago";
                                }
                            }
                        @endphp
                        </span>
                    </small>
                    <div class="flex-grow-1 text-right voting-section" data-story-id="{{$story['story_id']}}">
                        <div id="card-voting-display">
                            <a class="btn btn-link text-muted upvote">
                                <span data-toggle="tooltip" title="Upvote"><i class="fas fa-arrow-up"></i></span></a>
                            </a>
                            <span data-toggle="tooltip" title="Story rating" class="rating">{{ $story["rating"] }}</span>
                            <a class="btn btn-link text-muted downvote">
                                <span data-toggle="tooltip" title="Downvote &nbsp; Do not use this button just because you don't like the post, use it only if the content is irrelevant to the topic."><i class="fas fa-arrow-down"></i></span></a>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main content column -->
                <div class="container">
                    <h2 class="card-title">
                        <a href="/stories/{{ $story['story_id'] }}" class="text-dark">{{ $story["title"]}}</a>
                    </h2>
                    {{-- <p class="card-text">{{ $description }}</p> --}}
                    <div class="text-muted d-flex">
                        <a href="https://example.com/" class="card-link">
                            <i class="fas fa-external-link-alt mx-1"></i>
                            {{ $story['url'] }}
                        </a>

                        <a href="/stories/{{ $story['story_id'] }}#comments" class="card-link ml-2">
                            <i class="fas fa-comments mx-1"></i>
                            {{ $comments[$story['story_id']] }}
                            {{ $comments == 1 ? "comment" : "comments" }}
                        </a>

                        <span class="text-right flex-grow-1">
                            <?php for ($i = 0; $i < count($topics[$story['story_id']]); $i++) : ?>
                                <a href="/topics/{{ $topics[$story['story_id']][$i]['name'] }}" class={{"card-link" . ($i == 0 ? "" : "ml-2") }}>#{{ $topics[$story['story_id']][$i]['name'] }}</a>
                            <?php endfor; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>