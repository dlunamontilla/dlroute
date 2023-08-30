<?php

namespace DLRoute\Test;
use DLRoute\Config\Controller;

final class TestController extends Controller {

    public function index(array | object $data): array {

        return [
            "request" => $this->request->get_values(),
            "key" => false,
            "json" => $this->get_json([]),
            "ip" => $this->get_ip()
        ];
    }
}