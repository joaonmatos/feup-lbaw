@extends('layouts.app')

@section('content')

<h3 class="mb-4">
    <a href="/" class="text-dark text-decoration-none">Change Password</a>
</h3>

<form class="form-signin text-center" method="POST" action="{{route('change-password')}}">
    {{ csrf_field() }}
    @if(count($errors))

	<div class="alert alert-danger">
		<strong>Whoops!</strong> There were some problems with your input.
		<br/>
		<ul>
			@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
    @endif

    <label for="password" class="sr-only">New Password</label>
    <input type="password" name="password" id="password" class="form-control firstInputForm" placeholder="New Password" required autofocus>
    
    <label for="password_confirmation" class="sr-only">Re-Type Password</label>
    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control lastInputForm" placeholder="Re-type New Password" required>

    <button class="btn btn-lg btn-primary btn-block mt-1" type="submit">Save Changes</button>
</form>
<div class="text-center d-flex flex-column mt-2">
    <a href="{{route('settings-page')}}">Cancel</a>
</div>
@endsection