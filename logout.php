<?php

require_once "./vendor/autoload.php";

use Auth\Auth;
$user = new Auth;

$user->logout();

?>