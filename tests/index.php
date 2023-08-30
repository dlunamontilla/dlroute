<?php

ini_set('display_errors', 1);

use DLRoute\DLRoute;
use DLRoute\Requests\DLRequest;
use DLRoute\Server\DLServer;
use DLRoute\Test\TestController;

include dirname(__DIR__) . "/vendor/autoload.php";

/**
 * Obtención del nombre del método.
 * 
 * @var string
 */
$method = DLServer::get_method();

// $request = DLRequest::get_instance();
// $request->execute_post_method(["name" => true], [TestController::class, 'index']);


DLRoute::get('/una/ruta', "DLLuna\UnaCiencia@metodo");

