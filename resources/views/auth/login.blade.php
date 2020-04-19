@extends('layouts.app')

@section('content')
<form class="form-signin text-center" method="POST" action="{{ route('signin') }}">
    {{ csrf_field() }}
    <h1 class="display-3 mb-3">
        <a href="/" class="text-dark text-decoration-none">news.ly</a>
    </h1>
    <label for="email" class="sr-only">Email address</label>
    <input type="email" name="email" id="email" class="form-control firstInputForm" placeholder="Email address" value="{{ old('email') }}" required autofocus>
    <label for="password" class="sr-only">Password</label>
    <input type="password" name="password" id="password" class="form-control lastInputForm" placeholder="Password" required="">
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>
<div class="text-center d-flex flex-column">
    <!-- Change these hrefs after creating the subpages -->
    <a href="/{{ route('signup') }}">Don't have an account?</a>
    <!-- <a href="/reset-password.php">Forgot your password?</a> -->
</div>
@endsection