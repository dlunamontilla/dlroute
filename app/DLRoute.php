<?php

namespace DLRoute;
use DLRoute\Interfaces\IRoute;

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