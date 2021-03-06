<?php
include_once("components/common.php");

function Signup()
{ ?>

    <form class="form-signin text-center">
        <h1 class="display-3 mb-3">
            <a href="/" class="text-dark text-decoration-none">news.ly</a>
        </h1>
        <a href="" class="btn btn-block btn-facebook"> <i class="fab fa-facebook-f"></i>  Sign up with Facebook</a>
        <div class="login-or">
            <hr class="hr-or">
            <span class="span-or">or</span>
        </div>

        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" name="inputEmail" id="inputEmail" class="form-control firstInputForm" placeholder="Email address" required="" autofocus="">
        <label for="inputUsername" class="sr-only">Username</label>
        <input type="text" name="inputUsername" id="inputUsername" class="form-control middleInputForm" placeholder="Username " required=" " autofocus=" ">

        <label for="inputPassword" class="sr-only ">password</label>
        <input type="password" name="inputPassword" id="inputPassword" class="form-control middleInputForm" placeholder="Password " required=" ">

        <label for="confirmPassword" class="sr-only ">Confirm password</label>
        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control lastInputForm" placeholder="Confirm Password " required=" ">

        <!-- Add ref to terms of use here -->
        <p class="terms-of-use">By signing up you accept our <a href="/terms-of-use.php">Terms of Use</a>.
        </p>
        <button class="btn btn-lg btn-primary btn-block " type="submit ">Sign up</button>

        <div class="text-center ">
    </form>


    <div class="text-center mt-2 ">
        <!-- Change these hrefs after creating the subpages -->
        <a href="/signin.php ">Already have an account?</a>

    </div>

<?php }

Head("Sign Up - news.ly", ["/styles/signin.css"], []);
Signup();
Foot();

?>