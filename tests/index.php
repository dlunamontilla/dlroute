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

// $request = DLRequest::get_instance();
// $request->execute_post_method(["name" => true], [TestController::class, 'index']);


// DLRoute::get('/una/ruta', [TestController::class, 'index']);
DLRoute::get('/', function(object $params, array $data) {
    return [
        "data" => $data,
        "params" => $params
    ];
});

DLRoute::get('/otra/ruta', function(object $params, array $vars) {
    return [
        "name" => "David Eduardo",
        "lastname" => "Luna Montilla"
    ];
});

DLRoute::get('/otra/parametro/{id}', function(object $params, array $data) {
    return [
        "name" => "David Eduardo",
        "lastname" => "Luna Montilla",
        "params" => $params
    ];
});

DLRoute::get('/product/{user}/{uuid}', function(object $params, array $vars) {
    return [
        "name" => "David Eduardo",
        "lastname" => "Luna Montilla",
        "data" => $vars,
        "params" => $params
    ];
});

DLRoute::init();

