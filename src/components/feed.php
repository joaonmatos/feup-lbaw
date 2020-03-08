<?php
include_once("card.php");
function Feed($title, $page_no, $next_page_exists)
{
?>
    <main class="container container-md my-4" id="feed">
        <header class="container d-flex mb-3 align-items-center">
            <h1 class="h3 text-primary d-inline-block flex-grow-1" id=feed-title>
                <?= $title ?>
            </h1>
            <span class="ml-auto small">Sort by: </span>
            <div class="dropdown">
                <a class="dropdown-toggle mx-2 text-primary" data-toggle="dropdown">Hot</a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#">Hot</a>
                    <a class="dropdown-item" href="#">New</a>
                    <a class="dropdown-item" href="#">Top (day)</a>
                    <a class="dropdown-item" href="#">Top (all time)</a>
                </div>
            </div>
        </header>
        <?php
        ExampleCard();
        ExampleCard();
        ExampleCard();
        ExampleCard();
        ExampleCard();
        ?>
        <footer class="container text-center">
            <a class=<?= "btn btn-link" . $page_no != 1 ? "" : "disabled" ?> href="#">
                <i class="fas fa-angle-left"></i>
            </a>
            <span class="mx-2">
                <?= $page_no ?>
            </span>
            <a class=<?= "btn btn-link" . $next_page_exists ? "" : "disabled" ?> href="#">
                <i class="fas fa-angle-right"></i>
            </a>
        </footer>
    </main>
<?php
}
?>