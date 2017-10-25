<?php
$router = new AltoRouter();

$router->map('GET', '/', function () {
    require  '../views/index.php';
});

$router->map('GET', '/home', function () {
    require  '../views/index.php';
});

$router->map('POST', '/home', function () {
    require  '../views/index.php';
});

$router->map('GET', '/logout', function () {
    require  '../views/logout.php';
});

$router->map('GET', '/login', function () {
    require  '../views/login.php';
});


$router->map('POST', '/login', function () {
    require  '../views/login.php';
});

$router->map('GET', '/register', function () {
    require  '../views/register.php';
});

$router->map('POST', '/register', function () {
    require  '../views/register.php';
});

$router->map('GET', '/call', function () {
    require  '../views/call.php';
});




/* Match the current request */
$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
// no route was matched
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    require  '../views/404.php';
}
