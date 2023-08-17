<?php

namespace DLRoute\RouteDebugger;

class RouteDebugger {

    /**
     * Depura y limpia las rutas.
     *
     * @param string $route
     * @return string
     */
    public static function clear_route(string $route): string {
        $route = preg_replace("/\+/", DIRECTORY_SEPARATOR, $route);
        $route = preg_replace("/\+$/", '', $route);
        return trim($route);
    }
}