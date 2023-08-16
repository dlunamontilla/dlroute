<?php

namespace DLRoute\Interfaces;

/**
 * Procesa los datos de `$_SERVER`
 * 
 * @package DLRoute\Interfaces;
 * 
 * @version 0.0.0
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2023 David E Luna M
 * @license MIT
 */
interface ServerInterface {

    /**
     * Devuelve la URI de la aplicación.
     *
     * @return string
     */
    public static function get_uri(): string;

    /**
     * Devuelve el nombre de host.
     *
     * @return string
     */
    public static function get_hostname(): string;

    /**
     * Devuelve el método HTTP.
     *
     * @return string
     */
    public static function get_method(): string;

    /**
     * Devuelve la dirección IP del cliente.
     *
     * @return string
     */
    public static function get_ipaddress(): string;
}