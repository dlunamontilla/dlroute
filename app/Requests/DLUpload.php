<?php

namespace DLRoute\Requests;

use DLRoute\Server\DLServer;

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
        $filtered_filenames = array_filter($filenames, function(array $filename) use ($pattern) {
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
     * Carga los archivos del usuario en un array de arrays asociativos o de objetos.
     *
     * @return void
     */
    private function load_filenames(string $field_name): void {
        /**
         * Archivos del usuarios cargados en esta variable, pero como array
         * de objetos.
         * 
         * @var array
         */
        $filenames = [];

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

        $filenames = $is_multiple
            ? $this->extract_filenames($files)
            : [$files];

        if (!$is_multiple) {
            $filenames[0]['readable_size'] = $this->get_readable_size((int) $files['size']);
        }

        $this->filenames = $filenames;
    }

    /**
     * Extrae los nombres de archivos si `$_FILES['field']['name']` contiene múltimples archivos.
     *
     * @param array $files
     * @return array
     */
    private function extract_filenames(array $files): array {
        /**
         * Nombre de archivos.
         * 
         * @var array
         */
        $filenames = [];

        foreach ($files['name'] as $key => $filename) {
            $full_path = $files['full_path'][$key];
            $type = $files['type'][$key];
            $tmp_name = $files['tmp_name'][$key];
            $error = $files['error'][$key];
            $size = $files['size'][$key];

            $filenames[] = [
                "name" => trim($filename),
                "full_path" => trim($full_path),
                "type" => trim($type),
                "tmp_name" => trim($tmp_name),
                "error" => (int) $error,
                "size" => (int) $size,
                "readable_size" => $this->get_readable_size((int) $size)
            ];
        }

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
}