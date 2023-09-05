<?php
namespace DLRoute\Requests;
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

    public static function get(string $uri, callable|array|string $controller, array|object $data = [], ?string $mime_type = null): void {
        
        if (!DLServer::is_get()) {
            return;
        }

        self::request($uri, $controller, 'GET', $data, $mime_type);
    }

    public static function post(string $uri, callable|array|string $controller, array|object $data = [], ?string $mime_type = null): void {
        if (!DLServer::is_post()) {
            return;
        }

        self::request($uri, $controller, 'POST', $data, $mime_type);
    }

    public static function put(string $uri, callable|array|string $controller, array|object $data = [], ?string $mime_type = null): void {

        if (!DLServer::is_put()) {
            return;
        }

        self::request($uri, $controller, 'PUT', $data, $mime_type);
    }

    public static function delete(string $uri, callable|array|string $controller, array|object $data = [], ?string $mime_type = null): void {

        if (!DLServer::is_delete()) {
            return;
        }

        self::request($uri, $controller, 'DELETE', $data, $mime_type);
    }

    private static function register_uri(string $uri): void {
        $method = DLServer::get_method();
    }
}