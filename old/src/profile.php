<?php
include_once("components/common.php");
include_once("components/navbar.php");
include_once("components/card.php");

function draw_profile($session_user, $profile_username, $n_followers, $n_following, $isFollowing, $posts, $comments)
{ ?>

    <section id="userProfile" class="container mt-4 mb-4 ">

        <header class="row">
            <div class="col-md-8">
                <h1 class="display-2"><?= $profile_username ?></h3>
            </div>

            <div class="col-md-4 text-right">
                <div class="my-2">
                    <span class="mx-2"><?= $n_followers ?> followers</span>
                    <span class="mx-2"><?= $n_following ?> following</span>
                </div>
                <?php if (strcmp($session_user, $profile_username) !== 0) { ?>
                    <div class="my-2">
                        <?php if ($isFollowing) { ?>
                            <button type="button" class="btn btn-primary">Unfollow</button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-primary">Follow</button>
                        <?php } ?>
                    </div>
                <?php } ?>
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
        <main class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="profilePosts" role="tabpanel" aria-labelledby="profile-posts">
                <?php for ($i = 0; $i < count($posts); $i++) {
                    Card($posts[$i][0], $posts[$i][1], $posts[$i][2], $posts[$i][3], $posts[$i][4], $posts[$i][5], $posts[$i][6], $posts[$i][7], $posts[$i][8]);
                } ?>


            </div>

            <div class="tab-pane fade" id="profileComments" role="tabpanel" aria-labelledby="profile-comments">
                <?php for ($i = 0; $i < count($comments); $i++) {
                    draw_comment($comments[$i]);
                } ?>

            </div>
        </main>
    </section>

<?php }

function draw_comment($comment)
{ ?>
    <article class="card card-body">
        <div class="d-inline-block small mb-2">
            <a class="card-subtitle mb-2" href="/profile.php"><?= $comment[0] ?></a> <i class="far fa-clock"> <?= $comment[1] ?></i>
        </div>
        <?= $comment[2] ?>
        <div class="d-flex justify-content-end">
            <button class="btn btn-link">Reply</a>
        </div>
    </article>
<?php }



Head("@ringo - news.ly", [], []);
Navbar("ambrosio");
draw_profile(
    "ambrosio",
    "ringo",
    "100",
    "3",
    false,
    [
        ["ringo", "How to drum", "Want to learn how to play drums like a Beatle?", "10 minutes", "ringo.com", 500, ["music", "thebeatles"], 9999, ""],
        ["ringo", "Global warming", "We are all going to die!", "2 days", "ringo.com", 10, ["earth", "science"], 666, ""]
    ],
    [
        ["ringo", "3 hours ago", "ayy lmao"]
    ]
);
Foot();
?>