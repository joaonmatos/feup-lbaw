<?php
include_once("components/common.php");

function Signin()
{ ?>
    <main class="text-center flex-grow-1 d-flex flex-column justify-content-center">
        <form class="form-signin text-center">
            <h1 class="display-3 mb-3">
                <a href="/" class="text-dark text-decoration-none">news.ly</a>
            </h1>
            <label for="inputEmail" class="sr-only">Email address</label>
            <input type="email" name="inputEmail" id="inputEmail" class="form-control firstInputForm" placeholder="Email address" required="" autofocus="">
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" name="inputPassword" id="inputPassword" class="form-control lastInputForm" placeholder="Password" required="">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>
        <div class="text-center d-flex flex-column">
            <!-- Change these hrefs after creating the subpages -->
            <a href="/signup.php">Don't have an account?</a>
            <a href="/reset-password.php">Forgot your password?</a>
        </div>
    </main>
<?php }

Head("Sign In - news.ly", ["/styles/signin.css"], []);
Signin();
Foot();

?>