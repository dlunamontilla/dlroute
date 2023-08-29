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
     * Procesa las imágenes. Esta función permite definir si la imagen se presenta como un archivo
     * externo y se incluye directamente siendo codificada a base 64.
     * 
     * Ejemplo de uso:
     * 
     * ```
     * $output = ResourceManager::image('public/image.jpg', [
     *  "title" => "Título de la imagen",
     *  "base64" => true
     * ]);
     *```
     * 
     * Si `base64` es `true`, entonces, el contenido de la imagen se colocará directamente en el código en formato
     * base 64, en lugar de su ruta.
     * 
     * @param string $path Ruta de la imagen
     * @param boolean|null $config Configuración de la imagen.
     * @return string
     */
    public static function image(string $path, object|array|null $config = null): string;

    /**
     * Establece la ruta HTTP de un archivo a partir de una URI
     *
     * @param string $path Ruta del archivo.
     * @return string
     */
    public static function asset(string $path): string;
}
