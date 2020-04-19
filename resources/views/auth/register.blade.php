@extends('layouts.app')

@section('content')
<form class="form-signin text-center" method="POST" action="{{ route('signup') }}">
  {{ csrf_field() }}
  <h1 class="display-3 mb-3">
    <a href="/" class="text-dark text-decoration-none">news.ly</a>
  </h1>
  <a href="" class="btn btn-block btn-facebook"> <i class="fab fa-facebook-f"></i>  Sign up with Facebook</a>
  <div class="login-or">
    <hr class="hr-or">
    <span class="span-or">or</span>
  </div>

  <label for="email" class="sr-only">Email address</label>
  <input type="email" name="email" id="email" class="form-control firstInputForm" placeholder="Email address" value="{{old('email')}}" required autofocus>
  <label for="username" class="sr-only">Username</label>
  <input type="text" name="username" id="username" class="form-control middleInputForm" placeholder="Username" value="{{old('username')}}" required>

  <label for="password" class="sr-only ">password</label>
  <input type="password" name="password" id="password" class="form-control middleInputForm" placeholder="Password" required>

  <label for="password_confirmation" class="sr-only ">Confirm password</label>
  <input type="password" name="password_confirmation" id="password_confirmation" class="form-control lastInputForm" placeholder="Confirm Password" required>

  <!-- Add ref to terms of use here -->
  <p class="terms-of-use">By signing up you accept our <a href="">Terms of Use</a>.
  </p>
  <button class="btn btn-lg btn-primary btn-block " type="submit">Sign up</button>

  <div class="text-center ">
</form>


<div class="text-center mt-2 ">
  <!-- Change these hrefs after creating the subpages -->
  <a href="/{{ route('signin') }}">Already have an account?</a>

</div>
@endsection