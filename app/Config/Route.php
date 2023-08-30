<?php

namespace DLRoute\Config;

use DLRoute\Requests\DLOutput;
use DLRoute\Server\DLServer;

abstract class Route {

    /**
     * Almacenamiento de rutas
     *
     * @var array
     */
    protected static array $routes = [];

    /**
     * Procesa la solicitud del usuario
     *
     * @param string $uri Ruta a registrar.
     * @param callable|array|string $controller
     * @param string $method Método de envío HTTP.
     * @param array|object $vars Datos que pueden ser usados como parámetros del método del controlador.
     * @return void
     */
    protected static function request(string $uri, callable|array|string $controller, string $method, array|object $vars): void {
        # Por ahora, lo dejamos así para revisar la salida.

        self::register_routes($method, $uri);

        if (is_string($controller)) {
            self::string_controller($controller, $vars);
        }

        /**
         * @var mixed
         */
        $data = null;

        if (is_array($controller)) {
            $object = new $controller[0];
            $data = $object->{$controller[1]};
        }

        if (is_callable($controller)) {
            $data = $controller();
        }

        print_r(self::$routes);
    }

    /**
     * Registra nuevas rutas
     *
     * @param string $route
     * @return void
     */
    protected static function register_routes(string $method, string $route): void {
        /**
         * Método HTTP.
         * 
         * @var string
         */
        $method = DLServer::get_method();
    }

    /**
     * Ejecuta la función que se pase como argumento y devuelve su salida.
     *
     * @param callable $callback Función a ejecutar como controlador.
     * @param array|object $data Datos que serán usados como un parámetro en el controlador.
     * @return mixed
     */
    protected static function callable_controller(callable $callback, array|object $data): mixed {
        $content = $callback($data);

        if (is_string($content)) {
            $content = trim($content);
        }

        return $content;
    }

    /**
     * Devuelve la salida del método a ejecutar del controlador al que se apunta.
     *
     * @param array $controller Controlador al que se apunta.
     * @param array|object $data Datos que serán usados como un parámetro en el controlador.
     * @return mixed
     */
    protected static function array_controller(array $controller, array|object $data): mixed {
        /**
         * Contenido del método del controlador.
         * 
         * @var mixed
         */
        $content = null;

        $controller_name = $controller[0] ?? null;
        $controller_method = $controller[1] ?? null;

        /**
         * Información de errores del sistema en formato JSON.
         * 
         * @var string
         */
        $error = "";

        if (!is_string($controller_name)) {
            self::response_code(500);

            $error = DLOutput::get_json([
                "status" => false,
                "error" => 'Controlador inválido'
            ]);

            if (self::is_producction()) {
                self::set_error($error);
                $error = self::get_generic_error();
            }

            echo $error;
            exit;
        }

        if (!is_string($controller_method)) {
            self::response_code(500);

            $error = DLOutput::get_json([
                "status" => false,
                "error" => "Método del controlador inválido"
            ]);

            if (self::is_producction()) {
                self::set_error($error);
                $error = self::get_generic_error();
            }

            echo $error;
            exit;
        }

        self::validate_classname($controller_name);

        if (!class_exists($controller_name)) {
            self::response_code(404);

            $error = DLOutput::get_json([
                "status" => false,
                "error" => "El controlador «{$controller_name}» no está definido."
            ]);

            if (self::is_producction()) {
                self::set_error($error);
                $error = self::get_generic_error();
            }

            echo $error;
            exit;
        }

        self::validate_method($controller_method);

        if (!method_exists($controller_name, $controller_method)) {
            self::response_code(404);

            $error = DLOutput::get_json([
                "status" => false,
                "error" => "El método «{$controller_method}» del controlador «{$controller_name}» no está definido"
            ]);

            if (self::is_producction()) {
                self::set_error($error);
                $error = self::get_generic_error();
            }

            echo $error;
            exit;
        }

        $instance = new $controller_name;
        $content = $instance->{$controller_method}($data);

        if (is_string($content)) {
            $content = trim($content);
        }

        return $content;
    }

    /**
     * Devuelve la salida del método del controlador al que se apunta.
     *
     * @param string $controller Controlador al que se apunta.
     * @param array|object $data Datos que serán usados como un parámetro en el controlador.
     * @return mixed
     */
    protected static function string_controller(string $controller, array|object $data): mixed {
        $pattern = "/@/";

        preg_match_all($pattern, $controller, $matches);

        /**
         * Cantidad de arrobas (@) encontradas.
         * 
         * @var int
         */
        $quantity = count($matches[0]);

        /**
         * Información de errores del sistema en formato JSON.
         * 
         * @var string
         */
        $error = "";

        if ($quantity !== 1) {
            self::response_code(500);

            $error = DLOutput::get_json([
                "status" => false,
                "error" => 'Fomato de nombre de controlador inválido'
            ], true);

            if (self::is_producction()) {
                self::set_error($error);
                $error = self::get_generic_error();
            }

            exit;
        }

        $parts_controller = explode('@', $controller);

        /**
         * Salida del controlador.
         * 
         * @var mixed
         */
        $content = null;

        if (is_array($parts_controller)) {
            $content = self::array_controller($parts_controller, $data);
        }

        return $content;
    }

    /**
     * Establece el código de respuesta en y establece la cabecera a formato JSON.
     *
     * @param integer $code
     * @return void
     */
    private static function response_code(int $code): void {
        header("Content-Type: application/json; charset=utf-8", true, $code);
    }

    /**
     * Valida si el nombre de la clase es correcto.
     *
     * @param string $classname
     * @return void
     */
    private static function validate_classname(string $classname): void {
        /**
         * Patrón de nombre en formato PascalCase
         * 
         * @var string
         */
        $pascal_case_pattern = "/^[A-Z][a-zA-Z]+/";

        /**
         * Patrón de nombre de clase.
         * 
         * @var string
         */
        $classname_pattern = "/^[a-z_][a-z0-9_]+$/i";

        /**
         * Partes de un nombre de clase.
         * 
         * @var array
         */
        $parts = preg_split('/\\\+/', $classname);

        /**
         * Índice indicadora del nombre de clase.
         * 
         * @var int
         */
        $index = count($parts) - 1;

        /**
         * Nombre del controlador.
         * 
         * @var string
         */
        $controller_name = $parts[$index] ?? '';

        /**
         * Mensaje de error.
         * 
         * @var string
         */
        $error = "";

        if (!(preg_match($classname_pattern, $controller_name))) {
            self::response_code(500);

            $error = DLOutput::get_json([
                "status" => false,
                "error" => "Caracteres Inválidos"
            ], true);

            if (self::is_producction()) {
                $_SESSION['error'] = $error;

                $error = DLOutput::get_json([
                    "error" => "Error del sistema"
                ]);
            }

            echo $error;
            exit;
        }

        if (!(preg_match($pascal_case_pattern, $controller_name))) {
            self::response_code(500);

            $error = DLOutput::get_json([
                "status" => false,
                "error" => "El nombre de clase debe tener el formato PascalCase"
            ]);

            if (self::is_producction()) {
                self::set_error($error);
                $error = self::get_generic_error();
            }

            echo $error;
            exit;
        }
    }

    /**
     * Valida si se ha escrito correctamente el nombre del método del controlador.
     *
     * @param string $method_name Nombre del método del controlador.
     * @return void
     */
    private static function validate_method(string $method_name): void {
        $found =  preg_match('/^[a-z_][a-z0-9_]+$/i', $method_name);

        /**
         * Mensaje de error del sistema.
         * 
         * @var string
         */
        $error = "";

        if (!$found) {
            self::response_code(500);

            $error = DLOutput::get_json([
                "status" => false,
                "error" => "El nombre del método «{$method_name}» es inválido"
            ]);

            if (self::is_producction()) {
                self::set_error($error);
                $error = self::get_generic_error();
            }

            echo $error;
            exit;
        }
    }

    /**
     * Indica si el sistema está en modo producción o no.
     *
     * @return boolean
     */
    private static function is_producction(): bool {
        if (defined('DL_PRODUCTION')) {
            return constant('DL_PRODUCTION');
        }

        return false;
    }

    /**
     * Almacena información de error del sistema en una variable de sessión
     *
     * @param string $error
     * @return void
     */
    private static function set_error(string $error): void {
        $_SESSION['error'] = trim($error);
    }

    /**
     * Devuelve errores genéricos.
     *
     * @return string
     */
    private static function get_generic_error(): string {
        return DLOutput::get_json([
            "status" => false,
            "error" => "Error del sistema"
        ]);
    }
}
