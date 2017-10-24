<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';
use Auth\Auth;

$user = new Auth;

try {

    $fb = new Hybridauth\Provider\Facebook($config);
    $fb->authenticate();
    $accessToken = $fb->getAccessToken();
    $userProfile = $fb->getUserProfile();

    if(isset($_SESSION['authUser'])){
        if($user->connect_fb($userProfile)==false){
            $_SESSION['systemMessage']="Cannot Connect to Facebook";
            $user->redirect_to('index.php');
        }else {
            $user->redirect_to('index.php');
        }
    }else{
        $user->fb_login($userProfile);
        $user->redirect_to('index.php');
    }

    $fb->disconnect();
}
catch(\Exception $e){
    echo 'Oops, we ran into an issue! ' . $e->getMessage();
}