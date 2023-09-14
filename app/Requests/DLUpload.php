<?php

namespace DLRoute\Requests;

use DLRoute\Routes\RouteDebugger;
use DLRoute\Server\DLServer;
use finfo;

/**
 * MIT License
 * 
 * Copyright (c) 2023 David E Luna M
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * Permite procesar la subida de archivos al servidor.
 * 
 * @package DLRoute\Requests
 * 
 * @version 0.0.0
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2023 David E Luna M
 * @license MIT
 */
trait DLUpload {

    /**
     * Nombres de archivos
     *
     * @var array
     */
    private array $filenames = [];

    /**
     * Directorio base a establecer para la subida de archivos.
     *
     * @var string
     */
    private string $basedir = "";

    /**
     * Sube los archivos al servidor.
     *
     * @param string $field Campo del formulario.
     * @param string $type Indica el tipo de archivo a permitir en el servidor
     * @return array
     */
    protected function upload_file(string $field, string $type = "*/*"): array {
        $field = trim($field);

        $this->load_filenames($field);

        /**
         * Archivos cargados por el usuario.
         * 
         * @var array
         */
        $filenames = $this->get_filenames();
        $filenames = $this->filter_by_type($filenames, $type);

        $this->move_uploaded($filenames);

        return $filenames;
    }

    /**
     * Devuelve los archivos enviados por el usuario
     *
     * @return array
     */
    protected function get_filenames(): array {
        return $this->filenames;
    }
    
    /**
     * Permite establecer un directorio base dónde guardar los archivos.
     *
     * @param string $basedir Directorio base a establecer.
     * @return void
     */
    protected function set_basedir(string $basedir): void {
        $this->basedir = RouteDebugger::clear_route($basedir);
    }

    /**
     * Filtra los archivos por tipo.
     *
     * @param array $filenames
     * @param string $mime_type
     * @return array
     */
    private function filter_by_type(array $filenames, string $mime_type): array {
        /**
         * Partes del tipo de archivos en un array.
         * 
         * @var array
         */
        $parts = explode('/', $mime_type);

        /**
         * Categoría de tipo de archivos.
         * 
         * @var string
         */
        $category = "";

        /**
         * Subcategoría de tipo de arhcivos.
         * 
         * @var string
         */
        $subcategory = "";

        if (array_key_exists(0, $parts)) {
            $category = trim($parts[0]);
        }

        if (array_key_exists(1, $parts)) {
            $subcategory = trim($parts[1]);
        }

        if ($category === "*") {
            $category = "(.*?)";
        }

        if ($subcategory === "*") {
            $subcategory = "(.*?)";
        }

        /**
         * Patrón de búsqueda de tipos de archivos.
         * 
         * @var string
         */
        $pattern = "/^{$category}\/{$subcategory}$/i";

        /**
         * Archivos filtrados por tipo.
         * 
         * @var array
         */
        $filtered_filenames = array_filter($filenames, function (array $filename) use ($pattern) {
            /**
             * Tipo de archivo.
             * 
             * @var string
             */
            $type = (string) $filename['type'] ?? '';

            return preg_match($pattern, $type);
        });

        return array_values($filtered_filenames);
    }

    /**
     * Carga los archivos del usuario en un array de arrays asociativos o de ojetos.
     *
     * @return void
     */
    private function load_filenames(string $field_name): void {

        if (!array_key_exists($field_name, $_FILES)) {
            header("Content-Type: application/json; charset=utf-8", true, 400);

            echo DLOutput::get_json([
                "status" => false,
                "error" => "Revise el nombre de campo del formulario de archivo o tamaño de archivo"
            ], true);

            exit;
        }

        /**
         * Archivos enviados por el usuario.
         * 
         * @var array
         */
        $files = $_FILES[$field_name];

        /**
         * Nombre de archivo o archivos.
         * 
         * @var boolean
         */
        $is_multiple = is_array($files['name']);

        /**
         * Archivos del usuarios cargados en esta variable, pero como array
         * de objetos.
         * 
         * @var array
         */
        $filenames = $this->extract_filenames($files, $is_multiple);

        $this->filenames = $filenames;
    }

    /**
     * Extrae los nombres de archivos si `$_FILES['field']['name']` contienen uno o múltimples archivos.
     *
     * @param array $files Archivos a ser analizado y procesado.
     * @param boolean $is_multiple Indicar si es múltiple.
     * @return array
     */
    private function extract_filenames(array $files, bool $is_multiple = true): array {
        /**
         * Directorio base de archivos.
         * 
         * @var string
         */
        $basedir = $this->get_basedir();

        /**
         * Nombres de archivos.
         * 
         * @var array
         */
        $filenames = [];

        if ($is_multiple) {
            foreach ($files['name'] as $key => $filename) {
                if (!is_string($filename)) {
                    continue;
                }

                /**
                 * Ruta completa del archivo.
                 * 
                 * @var string
                 */
                $full_path = (string) $files['full_path'][$key];

                /**
                 * Nombre temporal del archivo.
                 * 
                 * @var string
                 */
                $tmp_name = (string) $files['tmp_name'][$key];

                /**
                 * Indica si se produce un error durante la subida del archivo. Si
                 * error es `0`, entonces, ha subido exitosamente al directorio temporal `/tmp/`.
                 * 
                 * @var integer
                 */
                $error = (int) $files['error'][$key];

                /**
                 * Tamaño en bytes del archivo.
                 * 
                 * @var integer
                 */
                $size = (int) $files['size'][$key];

                /**
                 * Tipo MIME de archivo.
                 * 
                 * @var string
                 */
                $type = $this->get_mime_type($tmp_name);

                /**
                 * Formato de archivo.
                 * 
                 * @var string
                 */
                $format = $this->get_file_format($tmp_name);

                /**
                 * Tamaño legible del archivo. Se asignan unidades de tamaños.
                 * 
                 * @var string
                 */
                $readable_size = $this->get_readable_size((int) $size);

                $name = $this->slug($filename, $type, ['file' => $tmp_name, 'type' => $files['type'][$key] ?? '']);

                $filenames[] = [
                    "name" => $name,
                    "tmp_name" => $tmp_name,
                    "full_path" => $full_path,
                    "type" => $type,
                    "file_format" => $format,
                    "size" => $size,
                    "readable_size" => $readable_size,
                    "error" => $error,
                    "basedir" =>  $this->basedir,
                    "target" => "{$basedir}/{$name}"
                ];
            }

            return $filenames;
        }

        /**
         * Nombre del archivo.
         * 
         * @var string
         */
        $name = $files['name'];

        /**
         * Nombre temporal del archivo.
         * 
         * @var string
         */
        $tmp_name = (string) $files['tmp_name'];

        /**
         * Ruta completa del archivo.
         * 
         * @var string
         */
        $full_path = (string) $files['full_path'];

        /**
         * Tipo de MIME del archivo.
         * 
         * @var string
         */
        $type = $this->get_mime_type($tmp_name);

        /**
         * Formato de archivo.
         * 
         * @var string
         */
        $file_format = $this->get_file_format($tmp_name);

        /**
         * Tamaño en bytes del archivo.
         * 
         * @var integer
         */
        $size = (int) $files['size'];

        /**
         * Tamaño legible del archivo.
         * 
         * @var string
         */
        $readable_size = $this->get_readable_size($size);

        /**
         * Indicador de errores de archivos. Si vale `0`, entonces, se envió exitosamente
         * al servidor.
         * 
         * @var integer.
         */
        $error = (int) $files['error'];

        $name = $this->slug($name, $type, ['file' => $tmp_name, 'type' => $files['type'] ?? '']);

        $filenames[] = [
            "name" => $name,
            "tmp_name" => $tmp_name,
            "full_path" => $full_path,
            "type" => $type,
            "file_format" => $file_format,
            "size" => $size,
            "readable_size" => $readable_size,
            "error" => $error,
            "basedir" => $this->basedir,
            "target" => "{$basedir}/{$name}"
        ];

        return $filenames;
    }

    /**
     * Devuelve un tamaño legible de datos
     *
     * @param integer $size Tamaño en bytes
     * @return string
     */
    private function get_readable_size(int $size): string {
        /**
         * Tamaño legible de datos.
         * 
         * @var string
         */
        $readable_size = "";

        /**
         * Números formateados.
         * 
         * @var string
         */
        $formatted_number = "";

        if ($size > 1024) {
            $size /= 1024;

            $formatted_number = number_format($size, 2);
            $readable_size = "{$formatted_number} KB";
        }

        if ($size > 1024) {
            $size /= 1024;

            $formatted_number = number_format($size, 2);
            $readable_size = "{$formatted_number} MB";
        }

        if ($size > 1024) {
            $size /= 1024;

            $formatted_number = number_format($size, 2);
            $readable_size = "{$formatted_number} GB";
        }

        if ($size > 1024) {
            $size /= 1024;

            $formatted_number = number_format($size, 2);
            $readable_size = "{$formatted_number} TB";
        }

        if ($size > 1024) {
            $size /= 1024;

            $formatted_number = number_format($size, 2);
            $readable_size = "{$formatted_number} PB";
        }

        if ($size > 1024) {
            $size /= 1024;

            $formatted_number = number_format($size, 2);
            $readable_size = "{$formatted_number} EB";
        }

        if ($size > 1024) {
            $size /= 1024;

            $formatted_number = number_format($size, 2);
            $readable_size = "{$formatted_number} ZB";
        }

        if ($size > 1024) {
            $size /= 1024;

            $formatted_number = number_format($size, 2);
            $readable_size = "{$formatted_number} YB";
        }

        return $readable_size;
    }

    /**
     * Devuelve el tipo MIME del archivo analizado.
     *
     * @param string $filename Archivo a ser analizado.
     * @return string
     */
    private function get_mime_type(string $filename): string {
        /**
         * Tipo MIME del archivo analizado.
         * 
         * @var string
         */
        $mime_type = "";

        if (!file_exists($filename)) {
            return $mime_type;
        }

        $mime_type = mime_content_type($filename);

        return $mime_type;
    }

    /**
     * Devuelve el formato de archivo
     *
     * @param string $filename Archivo a ser analizado
     * @return string
     */
    private function get_file_format(string $filename): string {
        /**
         * Formato de archivos.
         * 
         * @var finfo
         */
        $finfo = new finfo();

        return $finfo->file($filename);
    }

    /**
     * Establece un nombre único para el archivo cargado al servidor en función de su
     * fecha y contenido.
     *
     * @param string $filename Nombre de archivo a ser procesado.
     * @param string $mime_type
     * @param array $options Opciones de archivo
     * @return string
     */
    private function slug(string $filename, string $mime_type, array $options): string {
        /**
         * Nombre de archivos.
         * 
         * @var string
         */
        $file = "";

        /**
         * Tipo de archivos.
         * 
         * @var string
         */
        $type = "";

        if (array_key_exists('file', $options)) {
            $file = $options['file'];
        }

        if (array_key_exists('type', $options)) {
            $type = $options['type'];
        }

        if ($this->is_svg($mime_type)) {
            /**
             * Contenido a ser analizado y depurado.
             * 
             * @var string
             */
            $content = file_get_contents($file);
            $content = $this->sanitize_svg($content);

            file_put_contents($file, $content);
        }

        /**
         * Patrón de búsqueda de la extensión.
         * 
         * @var string
         */
        $extension_pattern = "/((!.*)?[^.*]+)$/";

        $filename = preg_replace("/\s+/", '-', $filename);
        $filename = strtolower($filename);

        /**
         * Indica existe o no alguna extensión.
         * 
         * @var boolean
         */
        $found = preg_match($extension_pattern, $filename, $matches);

        /**
         * Extensión de archivo.
         * 
         * @var string
         */
        $extension = "";

        if ($found) {
            $extension = trim($matches[0] ?? '');
        }

        /**
         * Partes de un tipo MIME de archivo.
         * 
         * @var string[]
         */
        $parts = explode("/", $mime_type);

        /**
         * Subcategoría.
         * 
         * @var string
         */
        $subcategory = "";

        if (array_key_exists(1, $parts)) {
            $subcategory = $parts[1];
        }

        $filename = preg_replace($extension_pattern, '', $filename);
        $filename = rtrim($filename, ".");
        $filename = trim($filename);

        /**
         * Hash como identificador en función de su nombre
         * 
         * @var string
         */
        $hash = "";

        if (file_exists($file)) {
            // $hash .= "-" . hash_file('fnv132', $file);
            $hash .= "-" . hash_file('sha256', $file);
        }

        /**
         * Indica si está o estuvo vacía `$filename`
         * 
         * @var boolean
         */
        $is_empty = empty($filename);

        if ($is_empty) {
            $filename = "{$extension}-{$hash}.{$subcategory}";
        } else {
            $filename = "{$filename}-{$hash}";
        }

        if (!empty($extension) && !$is_empty) {
            $filename .= ".{$extension}";
        }

        if ($type !== $mime_type) {
            $filename = str_replace(".{$extension}", '', $filename);
            $filename .= ".{$subcategory}";
        }

        $filename = preg_replace("/-+/", "-", $filename);

        return $filename;
    }

    /**
     * Devuelve el directorio base de donde se subirán los archivos.
     *
     * @return string
     */
    private function get_basedir(): string {
        /**
         * Directorio raíz de la aplicación.
         * 
         * @var string
         */
        $root = DLServer::get_document_root();

        /**
         * Directorio base para subir archivos.
         * 
         * @var string
         */
        $basedir = "{$root}/{$this->basedir}";
        $basedir = RouteDebugger::clear_route($basedir);

        /**
         * Año actual del servidor.
         * 
         * @var string
         */
        $year = date('Y');

        /**
         * Mes actual del servidor.
         * 
         * @var string
         */
        $month = date('m');

        $basedir = "/{$basedir}/{$year}/{$month}";

        if (!file_exists($basedir)) {
            mkdir($basedir, 0755, true);
        }

        if (!is_dir($basedir)) {
            $this->error('La ruta especificada no es un directorio. Considere otro nombre');
        }

        if (!is_readable($basedir)) {
            $this->error("No tienes permiso de lectura. Contacte con el administrador para cambiar los permisos de lectura");
        }

        if (!is_writable($basedir)) {
            $this->error("No tienes permiso de escritura. Contacte con el administrador del servidor");
        }

        return $basedir;
    }

    /**
     * Ayuda a establecer mensajes de error.
     *
     * @param string $message Mensaje personalizado de error.
     * @return void
     */
    private function error(string $message): void {
        header("Content-Type: application/json; charset=utf-9", true, 500);

        echo DLOutput::get_json([
            "status" => false,
            "error" => trim($message)
        ], true);

        exit;
    }

    /**
     * Mueve los archivos previamente subidos en `/tmp/` al directorio de
     * archivos subidos de la aplicación.
     *
     * @param array $filenames
     * @return void
     */
    private function move_uploaded(array $filenames): void {

        foreach ($filenames as $file) {
            if (!is_array($file)) {
                continue;
            }

            $file = (object) $file;

            move_uploaded_file($file->tmp_name, $file->target);
        }
    }

     /**
     * Sanea el código SVG para evitar la ejecución de código JavaScript no deseada.
     *
     * @param string $content
     * @return string
     */
    private function sanitize_svg(string $content): string {
        /**
         * Patrón de búsqueda de bloque de código JavaScript.
         * 
         * @var string
         */
        $script_block_pattern = '/<script(.*?)>[\s\S]+?<\/script(.*?)>/i';

        /**
         * Patrón de búsqueda de etiquetas sobrantes de JavaScript.
         * 
         * @var string
         */
        $script_pattern = '/<script(.*?)>[\s\S]+/i';

        /**
         * Patrón de búsqueda de eventos de JavaScript.
         * 
         * @var string
         */
        $js_events_pattern = '/(\b((?<!-)on\w+=\"?(.*)\"?)|\b((?<!-)on\w+=\'?(.*)\'?))/i';
        
        /**
         * Patrón de búsqueda de atributos.
         * 
         * @var string
         */
        $attributes_pattern = '/\b(eval\((.*)(\)|\"|\')?|href=[\S]*(\"|\'))/i';

        /**
         * Patrón de búsqueda de atributos `data-*` incompletos.
         * 
         * @var string
         */
        $data_attributes_pattern = '/\b(data-)(?=\s+)/i';

        /**
         * Patrón de búsqueda de bloques de estilos.
         * 
         * @var string
         */
        $style_block_pattern = '/<style(.*?)>[\s\S]+?<\/style(.*?)>/i';

        /**
         * Patrón de búsqueda de etiquetas de estilos sobranes.
         * 
         * @var string
         */
        $style_pattern = '/<style(.*?)>[\s\S]+/i';

        /**
         * Patrón de búsqueda de bloques PHP.
         * 
         * @var string
         */
        $php_block_pattern = '/<\?(php)?[\s\S]*?\?>/i';

        /**
         * Patrón de búsqueda de fragmentos PHP sobrantes.
         * 
         * @var string
         */
        $php_pattern = '/<\?(php)?|\?>/i';
        
        /**
         * Contenido a ser depurado.
         * 
         * @var string
         */
        $content = trim($content);

        /**
         * Patrón de búsqueda de cabeceras XML.
         * 
         * @var string
         */
        $xml_pattern = '/<\?xml version="1.0" encoding="UTF-8" standalone="no"\?>/i';

        /**
         * Indicador de existencia de cabeceras XML en el archivo SVG.
         * 
         * @var boolean
         */
        $xml_exists = preg_match($xml_pattern, $content);
        
        $content = preg_replace($script_block_pattern, '', $content);
        $content = preg_replace($script_pattern, '', $content);
        $content = preg_replace($style_block_pattern, '', $content);
        $content = preg_replace($style_pattern, '', $content);
        $content = preg_replace($php_block_pattern, '', $content);
        $content = preg_replace($php_pattern, '', $content);
        $content = preg_replace($js_events_pattern, '', $content);
        $content = preg_replace($attributes_pattern, '', $content);
        $content = preg_replace($data_attributes_pattern, '', $content);

        $content = trim($content);

        if ($xml_exists) {
            $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
            $content = "{$xml}\n{$content}";
        }

        return $content;
    }

    /**
     * Verifica si el archivo es formato SVG
     *
     * @param string $mime_type Tipo MIME del gráfico vectorial.
     * @return boolean
     */
    private function is_svg(string $mime_type): bool {
        return $mime_type === 'image/svg+xml';
    }

    /**
     * Verifica si el archivo es una foto JPEG.
     *
     * @param string $mime_type Tipo MIME de la foto.
     * @return boolean
     */
    private function is_jpeg(string $mime_type): bool {
        return $mime_type === "image/jpeg";
    }

    /**
     * Verifica si el archivo es una imagen PNG
     *
     * @param string $mime_type Tipo MIME de la imagen PNG.
     * @return boolean
     */
    private function is_png(string $mime_type): bool {
        return $mime_type === "image/png";
    }

    /**
     * Verifica si el archivo es una imagen GIF
     *
     * @param string $mime_type Tipo MIME del archivo
     * @return boolean
     */
    private function is_gif(string $mime_type): bool {
        return $mime_type === "image/gif";
    }

    /**
     * Verifica si el archivo es una imagen `BMP`.
     *
     * @param string $mime_type
     * @return boolean
     */
    private function is_bitmap(string $mime_type): bool {
        return $mime_type === "image/bmp";
    }
}
