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
                        <a href="/profile.php" class="card-link">
                            <i class="fas fa-user mx-1"></i>
                            {{ $story["username"]}}
                        </a>
                    </small>
                    <small class="text-muted mx-2">
                        <i class="fas fa-clock mx-1"></i>
                        {{ $story["published_date"] }}
                        <!--ago-->
                    </small>
                    <div class="flex-grow-1 text-right voting-section" data-story-id="{{$story['story_id']}}">
                        <div id="card-voting-display">
                            <a class="btn btn-link text-muted upvote">
                                <i class="fas fa-arrow-up"></i></a>
                            </a>
                            <span>{{ $story["rating"] }}</span>
                            <a class="btn btn-link text-muted downvote">
                                <i class="fas fa-arrow-down"></i></a>
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