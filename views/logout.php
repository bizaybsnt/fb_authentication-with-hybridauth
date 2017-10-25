<?php

//require_once "./vendor/autoload.php";

use Auth\Auth;

$user = new Auth;

if ($user->logout()) {
    $user->redirectTo('/home');
}
