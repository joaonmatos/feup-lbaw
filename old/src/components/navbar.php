<?php
function Navbar($user)
{
?>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <a href="/" class="navbar-brand lead">news.ly</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-content">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar-content">
            <div class="navbar-nav mr-auto">
                <a class="nav-link active" href="/all.php">All Stories</a>
                <?php NavMenu($user, $page); ?>
            </div>
            <?php
            SearchForm();
            if ($user != null) {
                UserMenu($user);
            } else {
                SessionButtons();
            }
            ?>
        </div>
    </nav>
<?php
}
function SessionButtons()
{ ?>
    <a class="btn btn-outline-light mx-md-2" href="/signup.php">Sign Up</a>
    <a class="btn btn-light mx-md-2" href="/signin.php">Sign In</a>
<?php }
function NavMenu($user)
{ ?>
    <div class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
            <?= $user === null ? "Recommended" : "My" ?> Topics
        </a>
        <div class="dropdown-menu">
            <span class="dropdown-item text-muted mx-2"><?= $user === null ? "Recommended" : "My" ?> Topics</span>
            <a class="dropdown-item" href="/topic.php"><i class="fas fa-hashtag mx-2 text-muted"></i>topic1</a>
            <a class="dropdown-item" href="/topic.php"><i class="fas fa-hashtag mx-2 text-muted"></i>topic2</a>
            <a class="dropdown-item" href="/topic.php"><i class="fas fa-hashtag mx-2 text-muted"></i>topic3</a>
            <a href="/manage-topics.php" class="dropdown-item text-muted"><i class="fas fa-ellipsis-h mx-2"></i>See All</a>
        </div>
    </div>
<?php
}
function UserMenu($name)
{ ?>
    <div class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle text-light px-0 px-md-3" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
            <?= $name ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="/profile.php"><i class="fas fa-user mx-2"></i>Profile</a>
            <a class="dropdown-item" href="/settings.php"><i class="fas fa-cog mx-2"></i>Settings</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href=""><i class="fas fa-sign-out-alt mx-2"></i>Log Out</a>
        </div>
    </div>
<?php
}
function SearchForm()
{ ?>
    <form class="form-inline">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
    </form>
<?php
}
?>