<?php

namespace DLRoute\Requests;
use DLRoute\Interfaces\InstanceInterface;
use DLRoute\Interfaces\ParamTypeInterface;

final class DLParamValueType implements InstanceInterface, ParamTypeInterface {
    /**
     * Instancia de clase.
     *
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * Tipos de datos.
     *
     * @var array
     */
    private array $types = [];

    /**
     * Nombre del campo al que se le establecerá el tipo de datos
     *
     * @var string
     */
    private string $field = "";

    private function __construct() {}

    public function field(string $field): self {
        $this->field = trim($field);
        return $this;
    }
    public function filter_value(string $regex = '(.*?)', string $flag = ""): void {
        /**
         * Expresión regultar de búsqueda
         * 
         * @var string
         */
        $pattern = "/{$regex}/{$flag}";

        $pattern = preg_replace('/\/+/', '/', $pattern);

        $this->types[$this->field] = $pattern;
    }

    public static function get_instance(): self {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}