
<?php
include_once  "component/page_head.php";
require_once "./vendor/autoload.php";

use Auth\Auth;



$user = new Auth;

if(isset($_SESSION['LoggedIn'])) {
    $user->redirect_to('index.php');
}


if(isset($_POST['login_btn'])) {

    if($user->login($_POST['email'], $_POST['password'])){
        $user->redirect_to('index.php');
    }else{

        $user->redirect_to('login.php');
    }

}

?>

    <h2>Login</h2>
    <hr />

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Email </label>
            <input type="email" class="form-control" name="email" placeholder="Enter Email Address" required  />
        </div>
        <div class="form-group">
            <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter Password" required />
        </div>
        <?php
        if(isset($_SESSION['systemMessage'])) {
            echo "<div class='text-danger'>".$_SESSION['systemMessage']."</div><br />";
            unset($_SESSION['systemMessage']);
        }
        ?>
            <input type="submit" class="btn btn-primary" name="login_btn" value="Login" />
    </form>
    <hr />
    <a class="btn btn-primary" href="call.php">Login With Facebook</a>
    <a class="btn btn-primary" href="register.php">Register</a>

</div>

