<?php

namespace DLRoute\Interfaces;

/**
 * Debe implementarse de forma obligatoria los métodos `get` y `post`
 * en las clases donde se utilicen esta interface.
 * 
 * @package Trading\Interfaces
 * 
 * @version 0.0.0
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2023 David E Luna M
 */
interface RequestInterface {

    /**
     * Valida si las peticiones hechas por el método GET son correctas
     * 
     * Es decir, lo puede hacer de esta forma:
     * 
     * ```
     * $params = [
     *  "campo1" => true,
     *  "campo2" => false
     * ];
     * 
     * if ($request->get($params)) {
     *  # Instrucciones a ejecutar si son válidas.
     * }
     * ```
     * Donde `"campo1" => true` significa que el campo es requerido, y `false`, lo contrario.
     * @param array $params
     * @return boolean
     */
    public function get(array $params): bool;

    /**
     * Valida si las peticiones hechas por el método POST son correctas
     * 
     * Es decir, lo puede hacer de esta forma:
     * 
     * ```
     * $params = [
     *  "campo1" => true,
     *  "campo2" => false
     * ];
     * 
     * if ($request->post($params)) {
     *  # Instrucciones a ejecutar si son válidas.
     * }
     * ```
     * Donde `"campo1" => true` significa que el campo es requerido, y `false`, lo contrario.
     * @param array $params
     * @return boolean
     */
    public function post(array $params): bool;

    /**
     * Ejecuta el controlador asociado al método GET.
     *
     * Esta función ejecuta el controlador proporcionado cuando se recibe una solicitud GET.
     *
     * @param array $params Los parámetros de la solicitud.
     * @param callable|array $controller El controlador que se ejecutará.
     * @param string|null $mime_type (Opcional) El tipo MIME de la respuesta.
     * @return void
     */
    public function execute_get_method(array $params, callable|array $controller, ?string $mime_type = null): void;


    /**
     * Ejecuta el controlador asociado al método POST.
     *
     * Esta función ejecuta el controlador proporcionado cuando se recibe una solicitud POST.
     *
     * @param array $params Los parámetros de la solicitud.
     * @param callable|array $controller El controlador que se ejecutará.
     * @param string|null $mime_type (Opcional) El tipo MIME de la respuesta.
     * @return void
     */
    public function execute_post_method(array $params, callable|array $controller, ?string $mime_type = null): void;
}
