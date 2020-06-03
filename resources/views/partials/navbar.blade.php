<nav class="navbar navbar-expand-md navbar-dark bg-primary print-hide">
    <a href="/" class="navbar-brand lead">news.ly</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-content">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbar-content">
        <div class="navbar-nav mr-auto">
            {{ NavMenu() }}
        </div>
        {{ SearchForm() }}

        @if (Auth::check())
            {{ UserMenu() }}
        @else
            {{ SessionButtons() }}
        @endif
    </div>
</nav>

<?php
function NavMenu() {
?>
    <div class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
            <?= $startText = Auth::check() ? "My" : "Recommended"; $startText ?> Topics
        </a>
        <div class="dropdown-menu">
            <!-- Can this be improved? -->
            <span class="dropdown-item text-muted mx-2"><?= $startText ?> Topics</span>
            
            <a class="dropdown-item" href="/topic.php"><i class="fas fa-hashtag mx-2 text-muted"></i>topic1</a>
            <a class="dropdown-item" href="/topic.php"><i class="fas fa-hashtag mx-2 text-muted"></i>topic2</a>
            <a class="dropdown-item" href="/topic.php"><i class="fas fa-hashtag mx-2 text-muted"></i>topic3</a>
            <a href="/topics" class="dropdown-item text-muted"><i class="fas fa-ellipsis-h mx-2"></i>See All</a>
        </div>
    </div>
<?php
}

function SearchForm() { ?>

<form class="form-inline" method="get" action="{{ route('search') }}">
    <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="query" value="{{ app('request')->input('query') }}">
    <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
</form>

<?php } 

function UserMenu() { ?>
<div class="nav-item dropdown active">
    <a class="nav-link dropdown-toggle text-light px-0 px-md-3" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
        {{ Auth::getUser()->username }}
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="/users/{{ Auth::getUser()->username }}"><i class="fas fa-user mx-2"></i>Profile</a>
        <a class="dropdown-item" href="{{route('settings-page')}}"><i class="fas fa-cog mx-2"></i>Settings</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="{{route('signout')}}"><i class="fas fa-sign-out-alt mx-2"></i>Sign Out</a>
    </div>
</div>

<?php }

function SessionButtons() { ?>

<form class="form-inline">
    <a class="btn btn-outline-light mx-md-2" href="{{route('signup')}}">Sign Up</a>
    <a class="btn btn-light mx-md-2" href="{{route('signin')}}">Sign In</a>
</form>

<?php } ?>
