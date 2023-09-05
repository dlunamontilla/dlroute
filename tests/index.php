<?php

ini_set('display_errors', 1);

use DLRoute\Requests\DLRoute;
use DLRoute\Test\TestController;

include dirname(__DIR__) . "/vendor/autoload.php";

/**
 * @test Lo que sigue es una prueba de uso del sistema de
 * enrutamiento.
 */

DLRoute::get('/', function(object $params, array $data) {
    return [
        "data" => $data,
        "params" => $params
    ];
});


DLRoute::post('/product/{id}', [TestController::class, 'index']);
DLRoute::get('/product/{id}/{name}', [TestController::class, 'index']);


DLRoute::init();

