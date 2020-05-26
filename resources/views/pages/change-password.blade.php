@extends('layouts.app')

@section('content')

<h3 class="mb-4">
    <a href="/" class="text-dark text-decoration-none">Change Password</a>
</h3>

<form class="form-signin text-center" method="POST" action="">
    {{ csrf_field() }}
    
    <label for="inputPassword" class="sr-only">New Password</label>
    <input type="password" name="inputPassword" id="inputPassword" class="form-control firstInputForm" placeholder="New Password" required autofocus>
    <label for="password" class="sr-only">Password</label>
    <input type="password" name="password" id="password" class="form-control lastInputForm" placeholder="Re-type New Password" required="">
<!--  @if(count($errors))
    <ul id="form-errors">
        @foreach($errors->all() as $error)
        <li class="text-danger text-italic">{{$error}}</li>
        @endforeach
    </ul>
    @endif-->
    <button class="btn btn-lg btn-primary btn-block mt-1" type="submit">Save Changes</button>
</form>
<div class="text-center d-flex flex-column mt-2">
    <a href="{{route('settings')}}">Cancel</a>
</div>
@endsection