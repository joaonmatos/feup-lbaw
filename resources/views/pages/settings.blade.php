@extends('layouts.app')

@section('content')

<section id="settings" class="container mt-4 mb-4">
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    <h2>Settings</h2>
    <hr>
    
    <h3>Account Manegement</h3>
    @if (Auth::check())
    <div class="row">
        <div class="col-2"><p>Username</p></div>
        <div class="col-8"><p>{{ Auth::getUser()->username }}</p></div>
    </div>
    <div class="row">
        <div class="col-2"><p>Email</p></div>
        <div class="col-8"><p>{{ Auth::getUser()->email }}</p></div>
    </div>
    <div class="row">
        <div class="col-10"><p>Password</p></div>
        <div class="col-auto"><a href="{{route('change-password')}}">Edit</a></div>
    </div>

    <div class="row">
        <div class="col-10"><a href="" data-toggle="modal" data-target="#exampleModal">Delete account</a></div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>This action will permanently delete your account!</h6> 
                    <h6>Insert your password if you wish to continue.</h6>
                    <form class="form-signin" method="POST" action="{{route('delete-account')}}">
                        {{ csrf_field() }}
                        <label for="password" class="sr-only">New Password</label>
                        <input type="password" name="password" id="password" class="form-control firstInputForm" placeholder="Password" required autofocus>
                    
                        <button type="submit" class="btn btn-primary mt-1">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

</section>
@endsection