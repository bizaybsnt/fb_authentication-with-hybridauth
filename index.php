

<?php
include_once  "component/page_head.php";
require_once "./vendor/autoload.php";

use Auth\Auth;
$user = new Auth;



if(isset($_POST['fb'])){

    if($_POST['fb']=='Connect Facebook'){
        $user->redirect_to('call.php');
    }else{
        $user->disconnect_fb();
    }
}


if(isset($_POST['create_password'])){
    $password=$_POST['password'];
    $confirm_password=$_POST['confirm_password'];
    $user->createPassword($password,$confirm_password);
    $user->redirect_to('index.php');
    }


if($user->check()) {
    echo "Welcome ".$_SESSION['authUser']['name']."!!<br /><br />";
    echo "Email: ".$_SESSION['authUser']['email']." <br />";
    if($_SESSION['authUser']['social_email']!=null)
    echo "Secondary Email: ".$_SESSION['authUser']['social_email']."<br /><br />";

    $data=$user->checkFbStatus($_SESSION['authUser']['provider_id']);

    echo "<form method='post' action='index.php'>
          <div class='form-group'>";
    if($data!=null) {
     if($_SESSION['authUser']['password']==null){
         echo "<br />Create New Password <br />";
         echo "<input class='form-control' type='password' name='password' placeholder='New Password' /><br />";
         echo "<input class='form-control' type='password' name='confirm password' placeholder='Confirm Password'/><br />";
         echo "<input class='btn btn-primary' type='submit' name='create_password' value='Change Password'/><hr />";
     }

        echo "Provider: ".$_SESSION['authUser']['provider']." <br />";
        echo "Provider ID: ".$_SESSION['authUser']['provider_id']." <br />";
        echo "Photo: <br /><img src=' ".$_SESSION['authUser']['avatar']."'> <br />";
        echo "<input class='btn btn-warning' type='submit' name='fb' value='Disconnect Facebook'/> </div> ";


    }
    else{
        if(isset($_SESSION['message'])) {
            echo "<hr /><div class='text-danger'>".$_SESSION['message']."</div><br />";
            unset($_SESSION['message']);
        }
        echo "<input class='btn btn-info' type='submit' name='fb' value='Connect Facebook'/>";
    }
    if(isset($_SESSION['systemMessage'])) {
        echo "<div class='text-danger'>".$_SESSION['systemMessage']."</div><br />";
        unset($_SESSION['systemMessage']);
    }

?>

    <hr /><a class="btn btn-danger" href="logout.php">Logout</a>
</div>


<?php
}else {
$user->redirect_to('login.php');
}
?>