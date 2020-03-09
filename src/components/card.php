<?php
function Card($user_name, $title, $description, $posted_time, $url_source, $comments, $tags, $points, $thumbnail)
{ ?>
    <!-- Card begin -->
    <article class="card my-3">
        <div class="row no-gutters" style="overflow:hidden;">
            <div class="col-md-4 text-hide" style="overflow:hidden; background-image: url(https://picsum.photos/500); background-size: cover;">
                <!-- Image column -->
                Thumbnail
            </div>
            <div class="col-md-8">
                <div class="card-body row no-gutters">
                    <div class="col-sm-11">
                        <div class="d-flex flex-column">
                            <div class="container text-right my-2">
                                <small class="mx-2">
                                    <a href="/profile.php" class="card-link">
                                        <i class="fas fa-user mx-1"></i>
                                        <?= $user_name ?>
                                    </a>
                                </small>
                                <small class="text-muted mx-2">
                                    <i class="fas fa-clock mx-1"></i>
                                    <?= $posted_time ?> ago
                                </small>
                            </div>
                            <!-- Main content column -->
                            <div class="container">
                                <h2 class="card-title">
                                    <a href="https://example.com/" class="text-dark"><?= $title ?></a>
                                </h2>
                                <p class="card-text"><?= $description ?></p>
                                <small class="text-muted">
                                    <a href="https://example.com/" class="card-link">
                                        <i class="fas fa-external-link-alt mx-1"></i>
                                        <?= $url_source ?>
                                    </a>
                                    <a href="/story.php#comments" class="card-link ml-2">
                                        <i class="fas fa-comments mx-1"></i>
                                        <?= $comments ?>
                                        <?= $comments == 1 ? "comment" : "comments" ?>
                                    </a>
                                </small>
                                <div class="absolute-bottom">
                                    <small class="text-muted">
                                        <?php for ($i = 0; $i < count($tags); $i++) :
                                            if ($i == 0) : ?>
                                                <a href="/topic.php" class="card-link">#<?= $tags[$i] ?></a>
                                            <?php else : ?>
                                                <a href="/topic.php" class="card-link ml-2">#<?= $tags[$i] ?></a>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <!-- Votes interaction column -->
                        <div class="d-flex flex-lg-column align-items-center justify-content-center">
                            <button class="btn btn-link text-dark"><i class="fas fa-arrow-up"></i></a>
                            <?= $points ?>
                            <button class="btn btn-link text-dark"><i class="fas fa-arrow-down"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>

<?php
}
function ExampleCard() {
    Card(
        "danny687",
        "Russian wine collection surfaces with chewed corks",
        "O rato roeu a rolha da garrafa roxa do rei Rodolfo da Rússia",
        "4 hours",
        "example.com",
        42,
        ["russia", "wine"],
        69,
        "https://picsum.photos/500"
    );
}
?>