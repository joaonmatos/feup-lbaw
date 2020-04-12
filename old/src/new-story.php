<?php
include_once("components/common.php");
include_once("components/navbar.php");
include_once("components/card.php");
Head("New Story - news.ly", [], []);
Navbar("ambrosio");
?>
<main class="container mt-4 text-center mb-auto">
    <h1 class="text-primary">New Story</h1>
    <div class="row">
        <form class="col-6">

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
            <button type="submit" class="btn btn-primary my-2">Post Story</button>
        </form>
        <div class="col-6">
            <?php
            Card(
                "ambrosio",
                "Your title",
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ullamcorper neque lorem, at iaculis turpis sollicitudin a. Phasellus blandit ante eu justo pharetra, in posuere magna maximus.",
                "just now",
                "yoururl.com",
                "42",
                ["russia"],
                "42",
                "https://picsum.photos/500"
            );
            ?>
        </div>
    </div>

</main>
<?php
Foot();
