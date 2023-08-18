<?php

ini_set('display_errors', 1);

use DLRoute\DLRoute;
use DLRoute\Server\DLServer;

include dirname(__DIR__) . "/vendor/autoload.php";

/**
 * Obtención del nombre del método.
 * 
 * @var string
 */
$method = DLServer::get_method();

DLRoute::get('/home', function() use ($method) {
    echo $method;
});

DLRoute::post('/home', function() use ($method) {
    echo $method;
});

DLRoute::put('/home', function() use ($method) {
    echo $method;
});

DLRoute::delete('/home', function() use ($method) {
    echo $method;
});

