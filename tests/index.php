<?php

ini_set('display_errors', 1);

use DLRoute\DLRoute;
use DLRoute\Server\DLServer;

include dirname(__DIR__) . "/vendor/autoload.php";

$method = DLServer::get_method();

if (DLServer::is_post()) {
    echo "Petición hecha al método POST";
}

if (DLServer::is_get()) {
    echo "Petición hecha al método GET";
}

if (DLServer::is_put()) {
    echo "Petición de actualización";
}

if (DLServer::is_delete()) {
    echo "Petición de eliminación";
}


DLRoute::get("/usr/science", function() {
    echo "Esta es prueba usando el sistema de enrutamiento";
});

DLRoute::post("/test/content", function() {

    return "Esta es una prueba";
});

echo "\n\n";
print_r($_REQUEST);
$data = file_get_contents("php://input");

echo "\n\n\n";
print_r(json_decode($data));