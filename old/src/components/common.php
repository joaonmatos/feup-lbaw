<?php
function Head($page_title, $stylesheets, $scripts)
{ ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $page_title ?></title>

        <link rel="stylesheet" href="styles/bootstrap.min.css">
        <?php
        foreach ($stylesheets as $sheet) {
            ?>
            <link rel="stylesheet" href=<?= $sheet ?>>
            <?php
        }
        ?>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://kit.fontawesome.com/0d5b08d93c.js" crossorigin="anonymous"></script>
        <?php
        foreach ($scripts as $script) {
            ?>
            <script src="stylesheet" href=<?= $sheet ?>></script>
            <?php
        }
        ?>
    </head>

    <body>
    <?php
}
    function Foot()
    { ?>
        <footer class="my-5 pt-5 text-muted text-center text-small border-top">
            <p class="mb-1">&copy;2020 news.ly</p>
            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="/privacy.php">Privacy Policy</a>
                </li>
                <li class="list-inline-item">
                    <a href="/terms-of-use.php">Terms of Use</a>
                </li>
                <li class="list-inline-item">
                    <a href="/support.php">Support</a>
                </li>
            </ul>
        </footer>
    </body>

    </html>
<?php } ?>
