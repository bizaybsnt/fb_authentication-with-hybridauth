<?php


use Auth\Auth;

$user = new Auth;

try {
    $fb = new Hybridauth\Provider\Facebook($GLOBALS['config']);
    $fb->authenticate();
    $accessToken = $fb->getAccessToken();
    $userProfile = $fb->getUserProfile();

    if (isset($_SESSION['authUser'])) {
        if ($user->connectFb($userProfile) == false) {
            $_SESSION['systemMessage'] = "Cannot Connect to Facebook";
        }
            $user->redirectTo('/home');
    } else {
        $user->fbLogin($userProfile);
        $user->redirectTo('/home');
    }

    $fb->disconnect();
} catch (\Exception $e) {
    echo 'Oops, we ran into an issue! ' . $e->getMessage();
}
