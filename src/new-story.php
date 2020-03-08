<?php
include_once("components/common.php");
include_once("components/navbar.php");
Head("New Story - news.ly", [], []);
Navbar("ambrosio", "#russia");
?>
<main class="container mt-4 text-center mb-auto">
    <form>
        <h1 class="text-primary">New Story</h1>
        <div class="my-2">
            <label for="title" class="h3">Title</label>
            </br>
            <input type="text" name="title" id="title">
        </div>
        <div class="my-2">
            <label for="link" class="h3">Link</label>
            </br>
            <input type="url" name="link" id="link">
        </div>
        <div class="my-2">
            <span class="h3">Topics</span>
            </br>
            <input type="text" name="topic1" id="topic1" class="my-1" value="russia">
            </br>
            <input type="text" name="topic2" id="topic2" class="my-1">
            </br>
            <input type="text" name="topic3" id="topic3" class="my-1">
        </div>
        <button type="submit" class="btn btn-primary text-light my-2">Post Story</button>
    </form>
</main>
<?php
Foot();
