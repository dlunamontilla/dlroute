<?php
use DLRoute\Requests\DLOutput;

ini_set('display_errors', 1);

use DLRoute\DLRoute;
use DLRoute\Routes\ResourceManager;
use DLRoute\Server\DLServer;

include dirname(__DIR__) . "/vendor/autoload.php";

/**
 * Obtención del nombre del método.
 * 
 * @var string
 */
$method = DLServer::get_method();

DLRoute::post('/home', function() use ($method) {
    echo $method;
});

DLRoute::put('/home', function() use ($method) {
    $test = ResourceManager::css('tests/test', true);

    print_r($test);
});

DLRoute::delete('/home', function() use ($method) {
    $test = ResourceManager::js('tests/test.js', [
        "external" => true,
        "behavior_attributes" => "defer",
        "type" => "module"
    ]);

    print_r($test);
});

$output = DLOutput::get_instance();

$output->set_content([
    "name" => "ciencia",
    "status" => false
]);

$output->print_response_data();