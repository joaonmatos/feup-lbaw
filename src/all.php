<?php
include_once("components/common.php");
include_once("components/navbar.php");
include_once("components/feed.php");
Head("All Stories - news.ly", [], []);
Navbar("ambrosio");
Feed("Popular Stories on news.ly", 1, false);
Foot();
?>