<?php
namespace DLRoute;
use DLRoute\Config\Route;
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
class DLRoute extends Route implements RouteInterface {
    private static ?self $instance = null;

    public function __construct() {}

    public static function get(string $uri, callable|array|string $controller, array|object $data = []): void {
        
        if (!DLServer::is_get()) {
            return;
        }

        self::request($uri, $controller, 'GET', $data);
    }

    public static function post(string $uri, callable|array|string $controller, array|object $data = []): void {
        if (!DLServer::is_post()) {
            return;
        }

        self::request($uri, $controller, 'POST', $data);
    }

    public static function put(string $uri, callable|array|string $controller, array|object $data = []): void {

        if (!DLServer::is_put()) {
            return;
        }

        self::request($uri, $controller, 'PUT', $data);
    }

    public static function delete(string $uri, callable|array|string $controller, array|object $data = []): void {

        if (!DLServer::is_delete()) {
            return;
        }

        self::request($uri, $controller, 'DELETE', $data);
    }

    private static function register_uri(string $uri): void {
        $method = DLServer::get_method();
    }
}