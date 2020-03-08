<?php
include_once("components/common.php");
include_once("components/navbar.php");
include_once("components/feed.php");

function Welcome()
{ ?>
    <div class="jumbotron text-center bg-white border-bottom border-primary" id="about">
        <p class="display-3 display-md-2">Welcome to news.ly</h1>
            <p class="lead">news.ly is the next-generation content aggregation platform.</p>
            <div class="container">
                <a href="#"><button class="btn btn-outline-primary mx-2">About Us</button></a>
                <a href="#"><button class="btn btn-outline-primary mx-2">Sign Up</button></a>
                <a href="#"><button class="btn btn-primary mx-2 text-light">Sign In</button></a>
            </div>
    </div>
<?php
}
Head("Home - news.ly", [], []);
Navbar(null, "Home");
Welcome();
Feed("Popular Stories", 1, true);
Foot();
?>