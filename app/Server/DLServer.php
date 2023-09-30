<?php

namespace DLRoute\Server;

use DLRoute\Config\DLRealPath;
use DLRoute\Interfaces\ServerInterface;
use DLRoute\Routes\RouteDebugger;

class DLServer implements ServerInterface {

    public static function get_uri(): string {
        /**
         * URI de la aplicación
         * 
         * @var string $uri
         */
        $uri = "";

        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        return trim($uri);
    }

    public static function get_hostname(): string {
        /**
         * Host en el que corre la aplicación
         * 
         * @var string $hostname
         */
        $hostname = "";

        if (array_key_exists('SERVER_NAME', $_SERVER)) {
            $hostname = $_SERVER['SERVER_NAME'];
        }

        return trim($hostname);
    }

    public static function get_method(): string {
        /**
         * Método de HTTP de envío.
         * 
         * @var string $method
         */
        $method = "";

        if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        return trim($method);
    }

    public static function get_script_filename(): string {
        /**
         * Nombre del script de ejecución
         * 
         * @var string $script_filename
         */
        $script_filename = "";

        if (array_key_exists('SCRIPT_FILENAME', $_SERVER)) {
            $script_filename = $_SERVER['SCRIPT_FILENAME'];
        }

        return trim($script_filename);
    }

    public static function get_ipaddress(): string {
        /**
         * Dirección IP del cliente
         * 
         * @var string $ip
         */
        $ip = "";

        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return trim($ip);
    }

    public static function get_user_agent(): string {
        /**
         * Agente de usuario del cliente
         * 
         * @var string $user_agent
         */
        $user_agent = "";

        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }

        return $user_agent;
    }

    public static function get_document_root(): string {
        /**
         * Ruta real de la aplicación
         * 
         * @var DLRealPath $realpath
         */
        $realpath = DLRealPath::get_instance();
        return trim($realpath->get_document_root());
    }

    public static function is_post(): bool {
        return self::get_method() === "POST";
    }

    public static function is_get(): bool {
        return self::get_method() === "GET";
    }

    public static function is_put(): bool {
        return self::get_method() === "PUT";
    }

    public static function is_patch(): bool {
        return self::get_method() === "PATCH";
    }

    public static function is_delete(): bool {
        return self::get_method() === "DELETE";
    }

    public static function get_http_host(): string {
        /**
         * Nombre de host en el que corre la aplicación
         * 
         * @var string $http_host;
         */
        $http_host = "";

        /**
         * Devuelve el protocolo HTTP o HTTPs
         * 
         * @var string $protocol
         */
        $protocol = self::get_protocol();

        if (array_key_exists('HTTP_HOST', $_SERVER)) {
            $http_host = $_SERVER['HTTP_HOST'];
        }

        return "{$protocol}{$http_host}";
    }

    public static function get_route(): string {
        /**
         * URI de la aplicación.
         * 
         * @var string
         */
        $uri = self::get_uri();
        $uri = urldecode($uri);

        self::remove_query($uri);

        /**
         * Nombre del script
         * 
         * @var string
         */
        $script_name = self::get_script_name();

        /**
         * Ruta relativa de ejecución de la aplicación.
         * 
         * @var string
         */
        $relative_route = dirname($script_name);
        $relative_route = trim($relative_route);
        $relative_route = urldecode($relative_route);

        if ($relative_route === "/") {
            $relative_route = "";
        }
        
        /**
         * Ruta virtual.
         * 
         * @var string
         */
        $virtual_route = str_replace($relative_route, '', $uri);
        $virtual_route = trim($virtual_route);
        $virtual_route = "/{$virtual_route}";
        $virtual_route = preg_replace("/\/+/", '/', $virtual_route);

        if (empty($virtual_route)) {
            $virtual_route .= "/";
        }
        
        return $virtual_route;
    }

    public static function get_script_name(): string {
        /**
         * Nombre del script de ejecución de la aplicación
         * 
         * @var string $script_name
         */
        $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
        return urldecode($script_name);
    }

    /**
     * Devuelve el directorio principal de ejecución
     *
     * @return string
     */
    public static function get_script_dir(): string {
        /**
         * Archivo principal de ejecución de la aplicación.
         * 
         * @var string
         */
        $file = self::get_script_name();

        /**
         * Directorio principal de ejecución.
         * 
         * @var string
         */
        $script_dir = dirname($file);
        $script_dir = RouteDebugger::trim_slash($script_dir);

        return $script_dir;
    }

    /**
     * Devuelve la URL base de la aplicación.
     *
     * @return string
     */
    public static function get_base_url(): string {
        /**
         * Ruta del host de ejecución de la aplicación.
         * 
         * @var string
         */
        $host = self::get_http_host();

        /**
         * Directorio base de ejecución de la aplicación.
         * 
         * @var string
         */
        $basedir = self::get_script_dir();

        return "{$host}/{$basedir}";
    }

    /**
     * Devuelve el subdirectorio en función de la URL base de la aplicación
     *
     * @param string $subdir Subdirectorio
     * @return string
     */
    public static function get_subdir(string $subdir): string {
        /**
         * URL base de la aplicación
         * 
         * @var string
         */
        $base_url = self::get_base_url();
        
        $base_url = rtrim($base_url, "\/");
        $base_url = trim($base_url);

        /**
         * Subdirectorio de la aplicación.
         * 
         * @var string $subdir
         */
        $subdir = RouteDebugger::dot_to_slash($subdir);
        $subdir = RouteDebugger::trim_slash($subdir);
        $subdir = "{$base_url}/{$subdir}";

        return $subdir;
    }

    /**
     * Devuelve el protocolo HTTP que se está usando, es decir: `http` o `https`.
     *
     * @return string
     */
    private static function get_protocol(): string {
        /**
         * Es HTTPS
         * 
         * @var boolean $is_https
         */
        $is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

        /**
         * Protocolo
         * 
         * @var string $protocol
         */
        $protocol = "http://";

        if ($is_https) {
            $protocol = "https://";
        }

        return $protocol;
    }

    /**
     * Remueve las query de las URI.
     *
     * @param string $input Entrada a ser procesada
     * @return void
     */
    private static function remove_query(string &$input): void {
        /**
         * Patrón de búsqueda de las query a ser removida.
         * 
         * @var string
         */
        $pattern = '\?(.*)$';

        $input = trim($input);
        $input = preg_replace("/{$pattern}/", '', $input);
    }
}
