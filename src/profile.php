<?php 
include_once("components/common.php");
include_once("components/navbar.php");
include_once("components/card.php");

function DrawProfile($session_user, $profile_username, $n_followers, $n_following, $isFollowing, $posts, $comments) { ?> 

    <section id="userProfile" class="container-fluid px-5 mt-4 mb-4 ">
        <div class="d-flex justify-content-between">
            <div class="d-inline-block mr-5">
                <div class="container-fluid">
                    <h3 class="display-3"><?= $profile_username ?></h3>
                </div>
            </div>

            <div class="col-5 d-inline-block">
                
                    <div class="d-flex">
                        <div class="mr-4"><?= $n_followers ?> followers</div>
                        <div><?= $n_following ?> following</div>
                    </div>
                <?php if(strcmp($session_user, $profile_username) !== 0) { ?>
                    <div class="container mt-1">
                        <div class="row">
                            <?php if($isFollowing) { ?>
                                <button type="button" class="btn btn-primary text-light">Unfollow</button>
                            <?php } else { ?>
                                <button type="button" class="btn btn-primary text-light">Follow</button>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <ul class="nav nav-pills my-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-posts" data-toggle="pill" href="#profilePosts" role="tab"
                    aria-controls="profilePosts" aria-selected="true">Posts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-comments" data-toggle="pill" href="#profileComments" role="tab"
                    aria-controls="profileComments" aria-selected="false">Comments</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="profilePosts" role="tabpanel" aria-labelledby="profile-posts">
                <?php for ($i=0; $i < count($posts); $i++) {
                    Card($posts[$i][0], $posts[$i][1], $posts[$i][2], $posts[$i][3], $posts[$i][4], $posts[$i][5], $posts[$i][6], $posts[$i][7], $posts[$i][8]);
                } ?>
            

            </div>

            <div class="tab-pane fade" id="profileComments" role="tabpanel" aria-labelledby="profile-comments">
                <?php for ($i = 0; $i < count($comments); $i++) {
                    DrawComment($comments[$i]);
                } ?>

            </div>
        </div>


    </section>

<?php } 

function DrawComment($comment)
{ ?>
    <div class="card card-body">
        <div class="d-inline-block small mb-2">
            <a class="card-subtitle mb-2" href="#"><?= $comment[0] ?></a> <i class="far fa-clock"> <?= $comment[1] ?></i>
        </div>
        <?= $comment[2] ?>
        <div class="d-flex justify-content-end">
            <a href="#">Reply</a>
        </div>
    </div>
<?php }



Head("Profile");
Navbar("ambrosio");
DrawProfile("ambrosio", "Ringo", "100", "3", false, 
[
    ["Ringo", "How to drum", "Want to learn how to play drums like a Beatle?", "10 minutes", "10 minutes", "", "500", ["Music", "The Beatles"], "9999"],
    ["Ringo", "Global warming", "We are all going to die!", "2 days", "2 days", "", "10", ["Earth", "Science"], "666"]
],
[
    ["Ringo", "3 hours ago", "ayy lmao"]
]);
Foot();
?>