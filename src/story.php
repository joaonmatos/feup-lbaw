<?php
include_once("components/common.php");
include_once("components/navbar.php");
include_once("components/card.php");

function NewsPage($img_url, $title, $user_name, $posted_time, $tags, $comments)
{ ?>

    <main class="container-lg my-4">
        <div class="d-flex justify-content-center mb-3">
            <img src=<?= $img_url ?> class="img-fluid mb-2 w-100" alt="Article thumbnail">
        </div>

        <h2 class="display-5 my-0"><?= $title ?></h2>

        <div class="d-flex mb-2">
            <div class="flex-grow-1">
                <?php for ($i = 0; $i < count($tags); $i++) { ?>
                    <a href="/topic.php"> #<?= $tags[$i] ?> </a>
                <?php } ?>
            </div>

            <div>
                <small class="mx-1">
                    <a href="/profile.php">
                        <i class="fas fa-user mx-1"></i>
                        <?= $user_name ?>
                    </a>
                </small>
                <small class="text-muted mx-1">
                    <i class="fas fa-clock mx-1"></i>
                    <?= $posted_time ?> ago
                </small>
            </div>
        </div>

        <div id="comments">
            <div class="form-group mt-2">
                <textarea class="form-control" id="writeComment" rows="3" placeholder="What's on your mind?"></textarea>
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <?php
                        VotingDisplay([
                            "state" => 1,
                            "score" => 42
                        ])
                        ?>
                    </div>
                    <button type="button" class="btn btn-primary my-2 text-light">Comment</button>
                </div>
            </div>

            <div class="d-flex">
                <span class="flex-grow-1">
                    <?= count($comments) ?>
                    <?= count($comments) == 1 ? " Comment" : " Comments" ?>
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
                <?php for ($i = 0; $i < count($comments); $i++) {
                    draw_comment($comments[$i]);
                } ?>

            </div>
        </div>
    </main>
<?php }

function draw_comment($comment)
{ ?>
    <div class="card card-body">
        <div class="d-inline-block mb-2">
            <small class="mx-1">
                <a href="/profile.php">
                    <i class="fas fa-user mr-1"></i>
                    <?= $comment[0] ?>
                </a>
            </small>
            <small class="text-muted">
                <i class="fas fa-clock mx-1"></i>
                <?= $comment[1] ?> ago
            </small>
        </div>
        <?= $comment[2] ?>
        <div class="d-flex justify-content-end">
            <button class="btn btn-outline-primary px-4">Reply</a>
        </div>
    </div>
<?php }
Head("'Pumpkin spice latte bad for you': Gordon Ramsey", [], []);
Navbar("ambrosio");
NewsPage(
    "data:image/webp;base64,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",
    "'Pumpkin spice latte bad for you': Gordon Ramsey",
    "miguel",
    "4 hours",
    ["starbucks", "cooking"],
    [
        ["joana", "3 hours", "ayy lmao"]
    ]
);
Foot();
?>