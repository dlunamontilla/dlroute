<?php

namespace DLRoute\Test;
use DLRoute\Config\Controller;

/**
 * Es un controlador de prueba para verificar que el sistema
 * de enrutamiento funcione correctamente.
 * 
 * @package DLRoute\Test
 * 
 * @version 0.0.1
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2023 David E Luna M
 * @license MIT
 */
final class TestController extends Controller {

    /**
     * Función principal de ejecución del controlador.
     *
     * @param object $params Parámetros de la ruta parametrizadas.
     * @param array $vars Opcional. Variables a usar en el motor de plantillas.
     * @return array
     */
    public function index(object $params, array $vars = []): array {
        /**
         * Nombre de campo de archivos
         * 
         * @var string
         */
        $file = 'file';

        /**
         * Tipo MIME de archivo a filtrar. Se analiza su contenido, en lugar, de
         * su extensión.
         * 
         * @var string
         */
        $mime_type = '*/*';

        $this->set_thumbnail_width(300);
        $this->set_basedir('/public/uploads');

        /**
         * Nombre de archivos.
         * 
         * @var array
         */
        $filenames = $this->upload_file($file, $mime_type);

        return [
            "vars" => $vars,
            "params" => $params,
            "request" => $this->request->get_values(),
            "filenames" => $filenames
        ];
    }
}