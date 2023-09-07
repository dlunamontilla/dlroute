<?php

namespace DLRoute\Interfaces;

interface ParamTypeInterface {

    /**
     * Selecciona el campo a ser filtrado por una expresión regular o tipo.
     *
     * @param string $field
     * @return self
     */
    public function field(string $field): self;

    /**
     * Filtra el valor de cada campo en función de la expresión regular indicada
     * en el primer parámetro.
     *
     * @param string $regex Expresión regular a usar para filtrar valor.
     * @param string $flag Banderas de la expresión regular.
     * @return void
     */
    public function filter_value(string $regex = "(.*?)", string $flag = ''): void;
}