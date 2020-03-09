<?php
include_once("components/common.php");

function Signin()
{ ?>
    <main class="text-center">
        <form class="form-signin text-center">
            <h1 class="display-3 mb-3">news.ly</h1>
            <label for="inputEmail" class="sr-only">Email address</label>
            <input type="email" name="inputEmail" id="inputEmail" class="form-control firstInputForm" placeholder="Email address" required="" autofocus="">
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" name="inputPassword" id="inputPassword" class="form-control lastInputForm" placeholder="Password" required="">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>
        <div class="text-center mt-2 d-flex flex-column">
            <!-- Change these hrefs after creating the subpages -->
            <a href="#">Don't have an account?</a>
            <a href="#">Forgot your password?</a>
        </div>
    </main>
<?php }

Head("Sign In - news.ly", ["/styles/signin.css"], []);
Signin();
Foot();

?>