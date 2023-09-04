<?php

namespace DLRoute\Test;
use DLRoute\Config\Controller;

final class TestController extends Controller {

    public function index(array | object $data): array {

        return [
            "data" => $data
        ];
    }
}