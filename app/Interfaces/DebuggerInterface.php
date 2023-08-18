<?php

namespace DLRoute\Interfaces;

interface DebuggerInterface {

    /**
     * Depura y limpia las rutas.
     *
     * @param string $route
     * @return string
     */
    public static function clear_route(string $route): string;
}