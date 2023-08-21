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
     * Incorpora directamente, el contenido CSS en código CSS, a menos que, `$external` valga `true`
     *
     * @param string $path
     * @return string
     */
    public static function css(string $path, bool $external = false): string;

    /**
     * Incorpora código JavaScript directamente en código HTML, a menos que se indique
     * lo contrario en `$options`
     *
     * @param string $path Ruta relativa del archivo.
     * @param ?string $options Token de seguridad, en el caso de que aplique.
     * @return string
     */
    public static function js(string $path, array $options): string;

    /**
     * Crea una ruta amigable para los archivos de tipo favicon.
     *
     * @param string $path Ruta del archivo de iconos.
     * @return string
     */
    public static function favicon(string $path): string;

    /**
     * Procesa las imágenes. Esta función permite definir si la imagen se presenta como un archivo
     * externo y se incluye directamente siendo codificada a base 64.
     *
     * @param string $path Ruta de la imagen
     * @param boolean|null $base64 Se indica si se trata como un archivo externo o se incluye directamente como base 64.
     * @return string
     */
    public static function image(string $path): string;

    /**
     * Establece la ruta HTTP de un archivo a partir de una URI
     *
     * @param string $path Ruta del archivo.
     * @return string
     */
    public static function route(string $path): string;
}