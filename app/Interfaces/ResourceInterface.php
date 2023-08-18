<?php

namespace DLRoute\Interfaces;

/**
 * Establece el sistema de ruta hacia los recursos críticos del sistema.
 * 
 * @package DLRoute\Interface
 * 
 * @version 0.0.0
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2023 David E Luna M
 * @license MIT
 */
interface ResourceInterface {

    /**
     * Establece una ruta amigable para el archivo que se está apuntando y la utiliza para enlazarlo
     * externamente.
     * 
     * Además, se crea un `hash` para indicar si el recurso ha cambiado o no. Si el recurso cambia
     * el `hash` se actualizará, evitando que se cargue la caché del navegador.
     * 
     * El contenido que llega al navegador será `text/css`.
     *
     * @param string $path
     * @return string
     */
    public static function css(string $path): string;

    /**
     * Incorpora contenido CSS directamente en el código HTML generado.
     *
     * @param string $path
     * @return string
     */
    public static function css_inline(string $path): string;

    /**
     * Crea una ruta amigable para le script que se está apuntando y al mismo tiempo
     * envía el tipo MIME `application/javascript` al navegador.
     *
     * @param string $path
     * @return string
     */
    public static function js(string $path): string;

    /**
     * Incorpora código JavaScript directamente en el código HTML.
     *
     * @param string $path
     * @return string
     */
    public static function js_inline(string $path): string;

    /**
     * Crea una ruta amigable para los archivos de tipo favicon.
     *
     * @param string $path
     * @return string
     */
    public static function favicon(string $path): string;

    public static function image(string $path, ?bool $base64 = false): string;
}