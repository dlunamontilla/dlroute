<?php

namespace DLRoute\Routes;


class ResourceManager {

    public function __construct() {}

    /**
     * Formatea código CSS a código compacto y remueve los comentarios.
     *
     * @param string $content
     * @return string
     */
    private static function format_css(string $content): string {
        # Agregar espacios en blanco:
        $content = preg_replace("/(?<=\{)(.*?)(?=\})/", " $1 ", $content);

        # Agregar saltos de línea:
        $content = preg_replace("/(.*?)\{[\s\S]+?\}/", "\n$0", $content);

        # Agregar espacios por la izquierda a cualquier carácter que venga precedida por llave de cierre (}):
        $content = preg_replace("/(?<=\})(.*)/", " $0", $content);

        # Agregar espacio por la izquierda a cualquier carácter precedido por dos puntos (:)
        $content = preg_replace("/(?<=\:)(.*)/m", " $1", $content);
        
        # Agregar puntos y comas en cada valor:
        $content = preg_replace("/(?<=:)(.*?)(?=\n|\})/m", "$0;", $content);

        # Remover comentarios en el código:
        $content = preg_replace("/\/\*[\s\S]+?\*\//", "", $content);

        # Remover espacios que estén seguidos por punto y coma (;):
        $content = preg_replace("/\s(?=;)/", "", $content);

        # Remueve los puntos y comas duplicados:
        $content = preg_replace("/\;+/m", ";", $content);

        # Remover espacios en blanco adicionales, incluyendo, saltos de línea:
        // $content = preg_replace("/\s+/", ' ', $content);

        return trim($content);
    }
}