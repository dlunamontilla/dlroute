<?php

namespace DLRoute\Interfaces;

interface IRoute {

    /**
     * Define una ruta para manejar solicitudes HTTP GET.
     *
     * Este método te permite definir una ruta para manejar solicitudes HTTP GET.
     * El callback o controlador proporcionado se ejecutará cuando la URI definida sea accedida
     * utilizando el método HTTP GET.
     *
     * @param string $uri El patrón de URI que se comparará con las solicitudes entrantes.
     * @param callable|array|string $controller El callback o controlador encargado de manejar la solicitud.
     * @return void
     *
     * @example
     * ```
     * # Apunta a un controlador usando un array:
     * Route::get('/user/{id}', [ControladorUsuario::class, 'mostrar']);
     * 
     * # Apunta al controlador utilizando una cadena de texto:
     * Route::get('/user/{id}', "Ruta\Al\Controlador@metodo);
     * 
     * # O directamente, ejecuta la función:
     * Route::get('/user/{id}', function(object $data) {
     *  // Lógica para el usuario.
     * });
     * ```
     *
     * En el ejemplo anterior, cuando se realiza una solicitud GET a '/usuario/123', se invocará el método 'mostrar'
     * de la clase 'ControladorUsuario' para manejar la solicitud.
     */
    public static function get(string $uri, callable|array|string $controller): void;

    
    /**
     * Define una ruta para manejar solicitudes HTTP POST.
     *
     * Este método te permite definir una ruta para manejar solicitudes HTTP POST.
     * El callback o controlador proporcionado se ejecutará cuando la URI definida sea accedida
     * utilizando el método HTTP POST.
     *
     * @param string $uri El patrón de URI que se comparará con las solicitudes entrantes.
     * @param callable|array|string $controller El callback o controlador encargado de manejar la solicitud.
     * @return void
     *
     * @example
     * ```
     * # Apunta a un controlador usando un array:
     * Route::post('/user/create', [ControladorUsuario::class, 'mostrar']);
     * 
     * # Apunta al controlador utilizando una cadena de texto:
     * Route::post('/user/create', "Ruta\Al\Controlador@metodo);
     * 
     * # O directamente, ejecuta la función:
     * Route::post('/user/create', function(object $data) {
     *  // Lógica para el usuario.
     * });
     * ```
     *
     * En el ejemplo anterior, cuando se realiza una solicitud GET a '/usuario/123', se invocará el método 'mostrar'
     * de la clase 'ControladorUsuario' para manejar la solicitud.
     */
    public static function post(string $uri, callable|array|string $controller): void;
}
