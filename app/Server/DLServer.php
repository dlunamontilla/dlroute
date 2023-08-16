<?php

namespace DLRoute\Server;

use DLRoute\Interfaces\ServerInterface;

class DLServer implements ServerInterface {
    /**
     * Instancia de clase
     *
     * @var self|null
     */
    private static ?self $instance = null;

    private function __construct() {
    }

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
        # Pendiente por definir lógica para capturar direcciones IP

        return "";
    }

    public static function get_user_agent(): string {
        $user_agent = "";

        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }

        return $user_agent;
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

    public static function is_delete(): bool {
        return self::get_method() === "DELETE";
    }

    /**
     * Devuelve una instancia de clase
     *
     * @return self
     */
    public static function get_instance(): self {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
