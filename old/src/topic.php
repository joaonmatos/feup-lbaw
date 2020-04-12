<?php
include_once("components/common.php");
include_once("components/navbar.php");
include_once("components/feed.php");
Head("#russia - news.ly", [], []);
Navbar("ambrosio");
Feed("Popular stories on #russia", 1, false);
Foot();
?>
