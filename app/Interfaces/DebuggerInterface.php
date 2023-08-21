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

    /**
     * Procesa las rutas que apuntan directo al recurso.
     *
     * @param string $path
     * @return string
     */
    public static function process_route(string $path): string;

    /**
     * Remueve la última o últimas diagonales en la ruta. Por ejemplo, si la ruta está definida
     * como se muestra a continuación:
     * 
     * ```
     * /ruta/con/un/slash/
     * /ruta/index.php/
     * ```
     * 
     * Devolverá lo siguiente:
     * 
     * ```
     * /ruta/con/un/slash
     * /ruta/index.php
     * ```
     *
     * @param string $path
     * @return string
     */
    public static function remove_trailing_slash(string $path): string;
}