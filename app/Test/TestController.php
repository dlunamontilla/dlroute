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

    public function index(object $params, array $vars): array {
        $filenames = $this->upload_file('file', '*/*');

        return [
            "data" => $vars,
            "params" => $params,
            "request" => $this->request->get_values(),
            "filenames" => (array) $filenames,
            "files" => $_FILES
        ];
    }
}

// $info = new \finfo;
// $type = $info->file();