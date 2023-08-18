<?php
namespace DLRoute;
use DLRoute\Interfaces\RouteInterface;
use DLRoute\Server\DLServer;

/**
 * Define el sistema de enrutamiento del sistema.
 * 
 * @package DLRoute
 * 
 * @version 0.0.0
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2023 David E Luna M
 * @license MIT
 * 
 */
class DLRoute implements RouteInterface {
    private static ?self $instance = null;

    public function __construct() {}

    public static function get(string $uri, callable|array|string $controller): void {
        
        if (!DLServer::is_get()) {
            return;
        }

        self::request($uri, $controller);
    }

    public static function post(string $uri, callable|array|string $controller): void {
        if (!DLServer::is_post()) {
            return;
        }

        self::request($uri, $controller);
    }

    public static function put(string $uri, callable|array|string $controller): void {

        if (!DLServer::is_put()) {
            return;
        }

        self::request($uri, $controller);
    }

    public static function delete(string $uri, callable|array|string $controller): void {

        if (!DLServer::is_delete()) {
            return;
        }

        self::request($uri, $controller);
    }

    /**
     * Procesa la solicitud del usuario
     *
     * @param string $uri
     * @param callable|array|string $controller
     * @return void
     */
    private static function request(string $uri, callable|array|string $controller): void {
        # Realizar una prueba:

        $controller();
    }

    private static function register_uri(string $uri): void {
        $method = DLServer::get_method();
    }
}