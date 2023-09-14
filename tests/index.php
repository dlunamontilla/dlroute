<?php

ini_set('display_errors', 1);

use DLRoute\Requests\DLRoute;
use DLRoute\Test\TestController;

include dirname(__DIR__) . "/vendor/autoload.php";

/**
 * @test Lo que sigue es una prueba de uso del sistema de
 * enrutamiento.
 */

// DLRoute::get('/', function (object $params, array $data) {
//     return [
//         "data" => $data,
//         "params" => $params
//     ];
// });


// DLRoute::post('/product/{id}', [TestController::class, 'index'])
//     ->filter_by_type([
//         "id" => ""
//     ]);

// DLRoute::get('/product/{id}/{name}', [TestController::class, 'index'])
//     ->filter_by_type([
//         "id" => "numeric",
//         "name" => "email"
//     ]);

// DLRoute::patch('/test/{parametro}', function (object $params) {
//     return $params;
// })->filter_by_type([
//     "parametro" => "integer"
// ]);

// DLRoute::put('/user/{id}/{email}', [TestController::class, 'index'])->filter_by_type([
//     "id" => "integer",
//     "email" => "email"
// ]);

DLRoute::get('/user/{parametro}', [TestController::class, 'index'])
    ->filter_by_type([
        "parametro" => 'numeric'
    ]);

DLRoute::post('/test/file', [TestController::class, 'index']);

DLRoute::execute();