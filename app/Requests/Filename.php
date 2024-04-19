<?php

namespace DLRoute\Requests;

use DLRoute\Server\DLServer;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

/**
 * MIT License
 * 
 * Copyright (c) 2024 David E Luna M
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
 * @version 1.0.0 (release)
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2024 David E Luna M
 * @license MIT
 */

final class Filename {

    /**
     * Nombre del archivo
     *
     * @var string $name
     */
    public readonly string $name;

    /**
     * Archivo de destino
     *
     * @var string $target_file
     */
    public string $target_file;

    /**
     * Nombre temporal del archivo al momento de subir
     *
     * @var string $tmp_name
     */
    public readonly string $tmp_name;

    /**
     * Ruta completa del archivo proporcionada por $_SERVER
     *
     * @var string $full_path;
     */
    public readonly string $full_path;

    /**
     * Tipo MIME del archivo enviado al servidor
     *
     * @var string $type
     */
    public readonly string $type;

    /**
     * Formato de archivo
     *
     * @var string $file_format
     */
    public readonly string $file_format;

    /**
     * Tamaño en bytes del archivo
     *
     * @var int $size;
     */
    public readonly int $size;

    /**
     * Tamaño del archivo en formato legible
     *
     * @var string $readable_size
     */
    public readonly string $readable_size;

    /**
     * Indica si se produjo un error en el archivo durante la subida
     *
     * @var integer $error
     */
    public readonly int $error;

    /**
     * Directorio base del archivo
     *
     * @var string $basedir
     */
    public readonly string $basedir;

    /**
     * Ruta absoluta del directorio base
     *
     * @var string $absolute_basedir
     */
    public readonly string $absolute_basedir;

    /**
     * Ruta relativa del directorio donde se enviará el archivo
     *
     * @var string $relative_path
     */
    public readonly string $relative_path;

    /**
     * Ruta absoluta del directorio de archivo
     *
     * @var string $absolute_path
     */
    public readonly string $absolute_path;

    /**
     * Ruta relativa del directorio de vista previa de imagen (en el caso de que aplique).
     *
     * @var string $relative_path_thumbnail
     */
    public readonly string $relative_path_thumbnail;

    /**
     * Vista previa de la imagen
     *
     * @var string $thumbnail
     */
    public readonly string $thumbnail;

    /**
     * Anchura predeterminada
     *
     * @var integer $preview_width
     */
    public int $preview_width = 500;

    /**
     * Ruta absoluta de la vista previa
     *
     * @var string $absolute_path_thumbnail
     */
    public readonly string $absolute_path_thumbnail;

    public function __construct(array $attributes = []) {
        $this->name = $attributes['name'] ?? '';
        $this->tmp_name = $attributes['tmp_name'] ?? '';
        $this->full_path = $this->get_path($attributes['full_path'] ?? '');
        $this->type = $attributes['type'] ?? '';
        $this->file_format = $attributes['file_format'] ?? '';
        $this->size = $attributes['size'] ?? 0;
        $this->readable_size = $attributes['readable_size'] ?? '';
        $this->error = $attributes['error'] ?? 0;
        $this->basedir = $this->get_path($attributes['basedir'] ?? '');
        $this->relative_path = $this->get_path($attributes['relative_path'] ?? '');
        $this->relative_path_thumbnail = $this->get_path($attributes['relative_path_thumbnail'] ?? '');

        ## ÁREA DE ARCHIVOS DE DESTINO
        $this->target_file = $this->get_path("{$this->relative_path}/{$this->name}");

        ## ÁREA DE RUTAS ABSOLUTAS
        $this->absolute_path = $this->get_absolute_path($this->relative_path);
        $this->absolute_path_thumbnail = $this->get_absolute_path($this->relative_path_thumbnail);
        $this->absolute_basedir = $this->get_absolute_path($this->basedir);

        ## VISTA PREVIA DE IMAGEN EN EL CASO DE QUE APLIQUE
        $this->thumbnail = $this->to_webp($this->target_file, $this->preview_width);
    }

    /**
     * Devuelve la ruta en función del sistema operativo de ejecucion
     *
     * @param ?string $path Ruta relativa o absoluta
     * @return string
     */
    private function get_path(?string $path): string {

        if (is_null($path)) {
            $path = "";
        }

        /**
         * Patrón de búsqueda de letras de unidad
         * 
         * @var string $pattern_unit
         */
        $pattern_unit = "/[a-z]+:/i";

        $path = preg_replace($pattern_unit, '', $path);

        /**
         * Patrón de búsqueda de barras diagonales
         * 
         * @var string $pattern_path
         */
        $pattern_path = "/[\/\\\]+/";

        $path = preg_replace($pattern_path, DIRECTORY_SEPARATOR, $path);

        return $path;
    }

    /**
     * Devuelve una ruta absoluta a partir de una ruta relativa.
     * **Importante:** Cualquier ruta que se pase como argumento se considerará ruta relativa.
     *
     * @param string|null $path Ruta relativa
     * @return string
     */
    public function get_absolute_path(?string $path): string {

        if (is_null($path)) {
            $path = "";
        }

        /**
         * Directorio raíz del sistema
         * 
         * @var string $root
         */
        $root = DLServer::get_document_root();

        /**
         * Ruta absoluta del archivo enviado al servidor
         * 
         * @var string $absolute_path
         */
        $absolute_path = "{$root}/{$path}";

        return $this->get_path($absolute_path);
    }

    /**
     * Convierte cualquier imagena formato WebP
     *
     * @param string $filename Archivo a ser procesado
     * @param integer|null $preview_width Vista previa del archivo
     * @return string
     */
    protected function to_webp(string $filename, int $preview_width = null): string {

        $filename = $this->get_path($filename);

        if (!($this->is_image($filename))) {
            return "";
        }

        /**
         * Patrón de búsqueda del directorio de la imagen
         * 
         * @var string $dir_pattern
         */
        $dir_pattern = "(.*)\/[0-9]{4}\/[0-9]{2}\/+";

        /**
         * Patrón de directorio para Windows
         * 
         * @var string $dir_pattern_win
         */
        $dir_pattern_win = "(.*)\\\[0-9]{4}\\\[0-9]{2}\\\+";

        /**
         * Directorio de la vista previa
         * 
         * @var string $thumbnail
         */
        $thumbnail = "";

        if (!file_exists($this->absolute_path_thumbnail)) {
            mkdir($this->absolute_path_thumbnail, 0775, true);
        }

        /**
         * Capturar el directorio base
         * 
         * @var string $basedir
         */
        $basedir = "";

        if (!is_null($filename)) {

            /**
             * Indica si se ha encontrado un patrón o no
             * 
             * @var int|false $found
             */
            $found = preg_match("/{$dir_pattern}|{$dir_pattern_win}/", $filename, $matches);

            if ($found) {
                $basedir = $matches[0];
                $thumbnail = "{$basedir}thumbnail/";
            }
        }


        /**
         * Nueva ruta en el caso de que aplique
         * 
         * @var string $new_filename
         */
        $new_filename = $filename;

        if (!file_exists($filename)) {
            $new_filename = $this->get_absolute_path($filename);
        }

        $new_filename = $this->get_path($new_filename);

        if (!file_exists($new_filename)) {
            header("content-type: application/json; charset=utf-8", 404);

            echo DLOutput::get_json([
                "status" => false,
                "error" => "El archivo no existe {$new_filename}"
            ]);

            exit;
        }

        /**
         * Devuelve las dimensiones de la imagen
         * 
         * @var array|false $size
         */
        $size = getimagesize($new_filename);

        if (!((bool) $size)) {
            header("content-type: application/json; charset=utf-8", 400);

            DLOutput::get_json([
                "status" => false,
                "error" => "formato de imagen inválido"
            ]);

            exit;
        }

        /**
         * Anchura de la imagen
         * 
         * @var integer $width
         */
        $width = (int) ($size[0] ?? 0);

        /**
         * Altura de la imagen
         * 
         * @var integer $height
         */
        $height = (int) ($size[1] ?? 0);

        /**
         * Relación de aspecto
         * 
         * @var double $aspect_radio
         */
        $aspect_radio = $height / $width;

        if ($width > 1920) {
            $width = 1920;
        }

        /**
         * Nueva anchura
         * 
         * @var integer $new_width
         */
        $new_width = is_null($preview_width) ? $width : $preview_width;

        /**
         * @var integer|float $new_height
         */
        $new_height = $new_width * $aspect_radio;

        /**
         * Encuentra la extensión del archivo
         * 
         * @var string $pattern
         */
        $pattern = "/\.[^.]+$/";

        /**
         * Ruta donde se desea guardar la imagen convertida en WebP
         * 
         * @var string $output
         */
        $output = trim($new_filename);
        $output = preg_replace($pattern, '', $output);
        $output = "{$output}.webp";

        if (!file_exists($output)) {
            header("content-type: application/json; charset=utf-8", 404);

            echo DLOutput::get_json([
                "status" => false,
                "error" => "El archivo no existe {$output}"
            ]);

            exit;
        }

        /**
         * @var Imagine $imagine
         */
        $imagine = new Imagine();

        $image = $imagine->open($new_filename);

        $image->resize(new Box($new_width, $new_height));

        if (!is_null($preview_width)) {
            $output = str_replace($basedir, $thumbnail, $output);
        }

        $image->save($output, [
            'format' => 'webp',
            'quality' => 100,
        ]);

        return $output;
    }

    /**
     * Establece la anchura de la imagen que se usará de vista previa
     *
     * @param integer $width
     * @return void
     */
    public function set_preview_width(int $width): void {
        $this->preview_width = $width;
    }

    /**
     * Indica si es una imagen o no
     *
     * @param string $path
     * @return boolean
     */
    private function is_image(string $path): bool {

        /**
         * Patrón de búsqueda de imagen
         * 
         * @var string $pattern
         */
        $pattern = "/image\/(.*?)/";

        return boolval(preg_match($pattern, $this->get_absolute_path($path)));
    }
}
