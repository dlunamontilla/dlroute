<?php

namespace DLRoute\Routes;

use DLRoute\Config\DLRealPath;
use DLRoute\Interfaces\ResourceInterface;
use DLRoute\Server\DLServer;

class ResourceManager implements ResourceInterface {

    public function __construct() {
    }

    public static function css(string $path, ?bool $external = false): string {
        # Elimina la extensión del archivo.
        $path = self::delete_extension($path);

        /**
         * Ruta completa del archivo sin la extensión.
         * 
         * @var string
         */
        $route = RouteDebugger::process_route($path);

        /**
         * Ruta completa del archivo CSS con extensión incluida.
         * 
         * @var string
         */
        $filename = "{$route}.css";

        if (!file_exists($filename)) {
            return "<!-- El archivo {$path}.css no existe -->";
        }

        /**
         * Hash calculado del archivo.
         * 
         * @var string
         */
        $hash = self::calculate_hash($filename);

        /**
         * Contenido CSS.
         * 
         * @var string
         */
        $css_content = "";

        if (!$external) {
            $css_content = file_get_contents($filename);
            $css_content = trim($css_content);
            return "<style>{$css_content}</style>";
        }

        $path = self::exclude_first_part($path);
        $host = DLServer::get_http_host();

        /**
         * URL que apunta al archivo CSS
         * 
         * @var string
         */
        $url = "{$host}/{$path}.css";

        return "<link rel=\"stylesheet\" href=\"{$url}?{$hash}\" />";
    }

    public static function js(string $path, ?array $options = []): string {
        $path = self::delete_extension($path, "js");

        /**
         * Ruta física el archivo sin la extensión.
         * 
         * @var string
         */
        $route = RouteDebugger::process_route($path);

        $filename = "{$route}.js";

        if (!file_exists($filename)) {
            return "<!-- El archivo {$path}.js no existe -->";
        }

        /**
         * Hash calculado del archivo JavaScript.
         * 
         * @var string
         */
        $hash = self::calculate_hash($filename);

        /**
         * @var array|object
         */
        $config = [];

        if (is_array($options)) {

            foreach ($options as $key => $option) {
                $config[$key] = $option;
            }
        }

        $config = (object) $config;

        /**
         * Token de seguridad.
         * 
         * @var string
         */
        $token = $config->token ?? '';

        /**
         * Indicar si el script se tratará como un archivo externo o se incorporará
         * directamente su contenido.
         * 
         * @var boolean
         */
        $external = $config->external ?? false;

        /**
         * Atributos de comportamiento para las etiquetas `<script></script>`
         * 
         * @var string
         */
        $behavior_attributes = trim($config->behavior_attributes ?? '');

        /**
         * Identifica el lenguaje de scripting en el que está escrito el código embebido dentro de
         * la etiqueta `script`
         * 
         * @var string
         */
        $type = trim($config->type ?? 'text/javascript');

        /**
         * Código JavaScript. Aplica solo si es embebido en el código HTML en lugar de tratarse
         * como un recurso externo.
         * 
         * @var string.
         */
        $js_content = "";

        if (!$external) {
            $js_content = file_get_contents($filename);
        }

        $js_content = trim($js_content);

        if ($external) {
            $realpath = DLRealPath::get_instance();

            /**
             * URI del directorio de trabajo.
             * 
             * @var string
             */
            $uri_from_workdir = $realpath->get_uri_from_workdir();

            /**
             * Ruta HTTP.
             * 
             * @var string
             */
            $route = DLServer::get_http_host();

            # Eliminar la primera o primeras barrras diagionales (//) de `$path`
            $path = preg_replace("/^\/+|\/+$/", "", $path);

            $path = RouteDebugger::remove_trailing_slash($path);
            $path = self::exclude_first_part($path);
            $path = "{$uri_from_workdir}/{$path}";
            
            $path = RouteDebugger::clear_route($path);
            
            /**
             * Ruta al archivo JS a través del protocolo HTTP.
             * 
             * @var string
             */
            $js_file = "{$route}/{$path}.js";

            return "<script type=\"{$type}\" src=\"{$js_file}?{$hash}\" nonce=\"{$token}\"{$behavior_attributes} />";
        }

        return "<script nonce=\"{$token}\">{$js_content}</script>";
    }

    public static function favicon(string $path): string {

        return $path;
    }

    public static function image(string $path): string {

        return $path;
    }

    public static function route(string $path): string {
        $host = DLServer::get_http_host();
        return "{$host}/{$path}";
    }

    /**
     * Elimina la extensión seleccionada de la ruta de archivo (si aplica).
     *
     * @param string $path
     * @param string $extension
     * @return string
     */
    private static function delete_extension(string $path, ?string $extension = "css"): string {
        $path = preg_replace("/\.{$extension}$/", '', $path);
        return $path;
    }

    /**
     * Excluye la primera parte de una ruta.
     *
     * @param string $path Ruta a la que se le excluirá la primera parte.
     * @return string
     */
    private static function exclude_first_part(string $path): string {
        return preg_replace("/^(.*?)\//", "", $path);
    }

    /**
     * Calcula el HASH del contenido de los archivos que se quieran analizar con el objeto
     * de que se pueda establecer de que ha cambiado el contenido.
     *
     * @param string $path Ruta del archivo al que se le calculará el hash
     * @return string
     */
    private static function calculate_hash(string $path): string {
        /**
         * Contenido del archivo a ser analizado.
         * 
         * @var string
         */
        $content = "";

        if (!file_exists($path)) {
            return $content;
        }

        $content = file_get_contents($path);

        if ($content === FALSE) {
            $content = "";
        }

        $hash = hash('sha256', $content);
        return $hash;
    }
}
