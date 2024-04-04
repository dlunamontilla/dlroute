<?php

ini_set('display_errors', 1);

use DLRoute\Requests\DLRoute;
use DLRoute\Test\TestController;

include dirname(__DIR__) . "/vendor/autoload.php";

/**
 * Este archivo se incorpora como ejemplo de uso del sistema de rutas. Sea el controlador
 * o las funciones que se pasen como argumento deben devolver datos.
 * 
 * Los datos devueltos por la función serán analizados de forma automática para determinar
 * su tipo y devolver al cliente una respuesta con su tipo MIME correspondiente a la 
 * salida.
 * 
 * Lo que sigue más abajo son rutas de ejemplos recién creadas.
 */

DLRoute::get('/', function() {

    return [
        'status' => true,
        'success' => "Mensaje de prueba"
    ];
});

DLRoute::post('/test', [TestController::class,'file']);

DLRoute::execute();