<?php

namespace DLRoute;
use DLRoute\Interfaces\IRoute;

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
class DLRoute implements IRoute {
    private static ?self $instance = null;

    public function __construct() {}

    public static function get(string $uri, callable|array|string $controller): void {
        # Pendiente por construir la lógica
    }

    public static function post(string $uri, callable|array|string $controller): void {
        # Pendiente por construir al lógica.
    }
}