<?php 
include_once("components/common.php");

function Signup() { ?>

    <form class="form-signin text-center">


        <h1 class="display-3 mb-3">news.ly</h1>
        <a href="" class="btn btn-block btn-facebook"> <i class="fab fa-facebook-f"></i>  Sign up with Facebook</a>
        <div class="login-or">
            <hr class="hr-or">
            <span class="span-or">or</span>
        </div>

        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="firstInputForm" id="inputEmail" class="form-control" placeholder="Email address" required=""
            autofocus="">
        <label for="inputUsername" class="sr-only">Username</label>
        <input type="middleInputForm" id="inputUsername " class="form-control " placeholder="Username " required=" "
            autofocus=" ">

        <label for="inputPassword" class="sr-only ">password</label>
        <input type="middleInputForm" id="inputPassword " class="form-control " placeholder="Password " required=" ">

        <label for="confirmPassword" class="sr-only ">Confirm password</label>
        <input type="lastInputForm" id="confirmPassword " class="form-control " placeholder="Confirm Password "
            required=" ">

        <p class="text-center">
            <!-- Add ref to terms of use here -->
            <p class="terms-of-use">By signing up you accept our <a href="#">Terms of Use</a>.
            </p>
            <button class="btn btn-lg btn-primary btn-block " type="submit ">Sign up</button>

            <div class="text-center ">
    </form>


    <div class="text-center mt-2 ">
        <!-- Change these hrefs after creating the subpages -->
        <a href="/users/sign_in ">Already have an account?</a>

    </div>

<?php }

Head("Sign Up - news.ly", ["/styles/signin.css"], []);
Signup();
Foot();

?>