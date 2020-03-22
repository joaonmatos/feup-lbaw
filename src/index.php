<?php
include_once("components/common.php");
include_once("components/navbar.php");
include_once("components/feed.php");

function Welcome()
{ ?>
    <aside class="jumbotron text-center bg-white border-bottom border-primary" id="about">
        <p class="display-3 display-md-2">Welcome to news.ly</h1>
            <p class="lead">news.ly is the next-generation content aggregation platform.</p>
            <div class="container">
                <!-- <a href="/about.php"><button class="btn btn-outline-primary mx-2">About Us</button></a> -->
                <a href="/signup.php"><button class="btn btn-outline-primary mx-2">Sign Up</button></a>
                <a href="/signin.php"><button class="btn btn-primary mx-2">Sign In</button></a>
            </div>
    </aside>
<?php
}
Head("Home - news.ly", [], []);
Navbar(null);
Welcome();
Feed("Recommended Stories", 1, true);
Foot();
?>