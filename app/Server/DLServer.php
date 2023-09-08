<?php

namespace DLRoute\Server;

use DLRoute\Config\DLRealPath;
use DLRoute\Interfaces\ServerInterface;

class DLServer implements ServerInterface {

    public static function get_uri(): string {
        $uri = "";

        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        return trim($uri);
    }

    public static function get_hostname(): string {
        $hostname = "";

        if (array_key_exists('SERVER_NAME', $_SERVER)) {
            $hostname = $_SERVER['SERVER_NAME'];
        }

        return trim($hostname);
    }

    public static function get_method(): string {
        $method = "";

        if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        return trim($method);
    }

    public static function get_script_filename(): string {
        $script_filename = "";

        if (array_key_exists('SCRIPT_FILENAME', $_SERVER)) {
            $script_filename = $_SERVER['SCRIPT_FILENAME'];
        }

        return trim($script_filename);
    }

    public static function get_ipaddress(): string {
        $ip = "";

        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return trim($ip);
    }

    public static function get_user_agent(): string {
        $user_agent = "";

        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }

        print_r($_SERVER);

        return $user_agent;
    }

    public static function get_document_root(): string {
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
        $http_host = "";
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
        $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
        return urldecode($script_name);
    }

    /**
     * Devuelve el protocolo HTTP que se está usando, es decir: `http` o `https`.
     *
     * @return string
     */
    private static function get_protocol(): string {
        $is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

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
