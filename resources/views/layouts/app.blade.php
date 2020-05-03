<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (Route::currentRouteName() == 'stories')
    @yield('title')
    @else
    <title>{{ config('app.name', 'Laravel') }}</title>
    @endif

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    @if (Route::currentRouteName() == 'signin' || Route::currentRouteName() == 'signup')
    <link href="{{ asset('css/signin.css') }}" rel="stylesheet">
    @endif

    <script type="text/javascript">
        // Fix for Firefox autofocus CSS bug (This bug was fixed in Firefox 60, 2 years ago, is it really necessary?)
        // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
    </script>
    <script type="text/javascript" src={{ asset('js/app.js') }} defer></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/0d5b08d93c.js" crossorigin="anonymous"></script>
    <script defer>
        $(function() {
            $('[data-toggle="popover"]').popover()
        })
    </script>

</head>

<body>
    @if (Route::currentRouteName() != 'signin' && Route::currentRouteName() != 'signup')
    <header>
        @include('partials.navbar')
    </header>
    @if (Route::currentRouteName() == 'stories')
    <main class="container-lg my-4">
        @else
        <main class="container container-md my-4" id="feed">
            @endif
            @else
            <main class="text-center flex-grow-1 d-flex flex-column justify-content-center">
                @endif

                <section id="content">
                    @yield('content')
                </section>
            </main>

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