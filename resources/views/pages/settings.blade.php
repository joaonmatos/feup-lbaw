@extends('layouts.app')

@section('content')

<section id="settings" class="container mt-4 mb-4">
    <h2>Settings</h2>
    <hr>
    
    <h3>Account Manegement</h3>
    @if (Auth::check())
    <div class="row">
        <div class="col-2"><p>Name</p></div>
        <div class="col-8"><p>{{ Auth::getUser()->name }}</p></div>
    </div>
    <div class="row">
        <div class="col-2"><p>Username</p></div>
        <div class="col-8"><p>{{ Auth::getUser()->username }}</p></div>
    </div>
    <div class="row">
        <div class="col-2"><p>Email</p></div>
        <div class="col-8"><p>{{ Auth::getUser()->email }}</p></div>
        <div class="col-auto"><a href="">Edit</a></div>
    </div>
    <div class="row">
        <div class="col-10"><p>Password</p></div>
        <div class="col-auto"><a href="{{route('change-password')}}">Edit</a></div>
    </div>
    @endif
    

</section>
@endsection