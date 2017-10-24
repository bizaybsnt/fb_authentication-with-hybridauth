<?php
include_once  "component/page_head.php";
require_once "./vendor/autoload.php";

use Auth\Auth;

$users =new Auth;


if(isset($_SESSION['authUser'])){
    $users->redirect_to('index.php');
}


if(isset($_POST['register_btn'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $confirm_password=$_POST['confirm_password'];

    $details=array(
      "name"=>$name,
      "email"=>$email,
      "password"=>$password,
      "confirm_password"=>$confirm_password
    );
    if($users->register($details)==true) {
        $users->login($email,$password);
        $users->redirect_to('index.php');
    }
}

?>
<div class="jumbotron">
    <h2>Registration Form</h2>
    <hr />
    <form method="post" action="register.php">
        <div class="form-group">
            <label>Name</label>
            <input class="form-control" type="Text" name="name" placeholder="Enter Full Name" required  />
        </div>
        <div class="form-group">
            <label>Email </label>
            <input class="form-control" type="Text" name="email" placeholder="Enter Email Address" required  />
        </div>
        <div class="form-group">
            <label>Password</label>
            <input class="form-control" type="password" name="password" placeholder="Enter Password" required />
        </div>
        <div class="form-group">
            <label>Confirm Password </label>
            <input class="form-control" type="password" name="confirm_password" placeholder="Confirm Password" required  />
        </div>
        <?php
        if(isset($_SESSION['systemMessage'])){
            echo "<div class='text-danger'>".$_SESSION['systemMessage']."</div><br />";
            unset($_SESSION['systemMessage']);
        }
        ?>
        <input type="submit" class="btn btn-primary" name="register_btn" value="Register" />

    </form>
</div>


