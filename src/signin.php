<?php 
include_once("components/common.php");

function Signin() { ?>


    <form class="form-signin text-center">

        <h1 class="display-3 mb-3">news.ly</h1>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="firstInputForm" id="inputEmail" class="form-control" placeholder="Email address" required=""
            autofocus="">
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="lastInputForm" id="inputPassword" class="form-control" placeholder="Password" required="">
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <div class="text-center">
    </form>


    <div class="text-center mt-2">
        <!-- Change these hrefs after creating the subpages -->
        <a href="#">Don't have an account?</a>
        <div>
            <a href="#">Forgot your password?</a>
        </div>
    </div>


<?php } 

Head("Sign In - news.ly", ["/styles/signin.css"], []);
Signin();
Foot();

?>