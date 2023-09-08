<?php

ini_set('display_errors', 1);

use DLRoute\Requests\DLRoute;
use DLRoute\Test\TestController;

include dirname(__DIR__) . "/vendor/autoload.php";

/**
 * @test Lo que sigue es una prueba de uso del sistema de
 * enrutamiento.
 */

DLRoute::get('/', function (object $params, array $data) {
    return [
        "data" => $data,
        "params" => $params
    ];
});


// DLRoute::get('/product/{id}', [TestController::class, 'index'])
//     ->filter_by_type([
//         "id" => "numeric"
//     ]);

// DLRoute::get('/product/{id}/{name}', [TestController::class, 'index'])
//     ->filter_by_type([
//         "id" => "numeric",
//         "name" => "email"
//     ]);

DLRoute::get('/test/{parametro}', function (object $params) {
    return $params;
})->filter_by_type([
    "parametro" => "string"
]);

DLRoute::execute();
