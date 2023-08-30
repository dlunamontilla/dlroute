<?php

ini_set('display_errors', 1);

use DLRoute\Config\DLRealPath;
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

// DLRoute::get('/home', function() use ($method) {
//     $test = ResourceManager::js('tests.test.js', [
//         "external" => true,
//         "behavior_attributes" => "defer",
//         "type" => "module",
//         "token" => "Contenido del token"
//     ]);

//     print_r($test);

//     echo "\n\n";

//     $test = ResourceManager::css("tests.test", true);

//     print_r($test);

//     echo "\n\n";

//     $test = ResourceManager::asset('tests/test.css');

//     print_r($test);

//     echo "\n\n";

//     $test = ResourceManager::image('tests/test.jpg', [
//         "base64" => false,
//         "title" => "Imagen de prueba"
//     ]);

//     print_r($test);

//     echo "\n\n";

//     $test = ResourceManager::asset("tests/test.jpg");

//     print_r($test);

//     echo "\n\n";

//     $test = ResourceManager::css('otro.estilos', true);

//     print_r($test);
// });

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

// $test = ResourceManager::asset('tests.test');
// $test = ResourceManager::css('tests.test', true);
$test = ResourceManager::js('tests.test', [
    "external" => true,
    "type" => "module",
    "token" => hash('sha256', 'Contenido del token'),
    "behavior_attributes" => 'defer'
]);

// $test = ResourceManager::image('tests.test', [
//     "html" => false,
//     "title" => "Título de la imagen",
// ]);

// $test
echo "{$test}";