<?php

namespace DLRoute\Routes;
use DLRoute\Interfaces\DebuggerInterface;

/**
 * Depura las rutas introducidas por el usuario.
 * 
 * @package DLRoute\RouteDebugger
 * 
 * @version 0.0.0
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2023 David E Luna M
 * @license MIT
 */
class RouteDebugger implements DebuggerInterface {

    public static function clear_route(string $route): string {
        $route = preg_replace("/\+/", DIRECTORY_SEPARATOR, $route);
        $route = preg_replace("/\+$/", '', $route);
        return trim($route);
    }
}