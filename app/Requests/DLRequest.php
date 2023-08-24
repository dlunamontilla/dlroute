<?php

namespace DLRoute\Requests;

use DLRoute\Interfaces\RequestInterface;
use DLRoute\Server\DLServer;
use Trading\Requests\DLOutput;


/**
 * Procesa las peticiones del usuario.
 * 
 * @package Trading\Requests
 * 
 * @version 0.0.0
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @copyright 2023 David E Luna M
 */
class DLRequest implements RequestInterface {

    /**
     * Instancia de Server
     *
     * @var DLServer|null
     */
    private ?DLServer $server = null;

    /**
     * Instancia de clase.
     *
     * @var self|null
     */
    private static ?self $instance = null;

    private DLOutput $output;
    private function __construct() {
        $this->output = DLOutput::get_instance();
    }

    /**
     * Valida los parámetros de la petición.
     *
     * @param array $params
     * @return boolean
     */
    private function validate(array $params): bool {
        /**
         * Solicitud del usuario.
         * 
         * @var array
         */
        $request = $this->get_request();

        /**
         * ¿Es equivalente?
         * 
         * @var bool
         */
        $is_equal = $this->is_field_equal($request, $params);
        
        if (!$is_equal) {
            return false;
        }
        
        if (!$this->validate_required_fields($request, $params)) {
            return false;
        }
        
        return true;
    }

    /**
     * Verifica si ambos array tienen la misma longitud y campos equivalentes.
     *
     * @param array $arrayA
     * @param array $arrayB
     * @return boolean
     */
    private function is_field_equal(array $arrayA, array $arrayB): bool {
        $lengthA = count($arrayA);
        $lengthB = count($arrayB);

        if ($lengthA !== $lengthB) {
            return false;
        }

        foreach ($arrayA as $key => $value) {

            if (!array_key_exists($key, $arrayB)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida is hay parámetros que sean requeridos. Por lo tanto, devolverá `true` si no hay 
     * campos requeridos en los parámetros o si los hay, pero que estos cumplan la condición
     * de requerido (contengan datos).
     * 
     * Si hay campos requeridos, pero estos no contienen datos, entonces, devolverá `false`, considerando
     * de esta manera que la petición del usuario es inválida.
     *
     * @param array $request
     * @param array $params
     * @return boolean
     */
    private function validate_required_fields(array $request, array $params): bool {

        foreach ($request as $key => $value) {
            $value = trim($value);
            $required = $params[$key];

            if ($required && empty($value)) {
                http_response_code(400);
                return false;
            }
        }

        return true;
    }

    /**
     * Devuelve los parámetros de la petición en un array asociativo.
     *
     * @return array
     */
    private function get_request(): array {
        $request = $_REQUEST;

        if ($this->server->is_post()) {
            $request = $_POST;
        }

        if ($this->server->is_get()) {
            $request = $_GET;
        }

        return $request;
    }
    
    public function get(array $params): bool {
        if (!$this->server->is_get()) {
            return false;
        }

        return $this->validate($params);
    }

    public function post(array $params): bool {
        if (!$this->server->is_post()) {
            return false;
        }

        return $this->validate($params);
    }

    /**
     * Devuelve una instancia de clase
     *
     * @return self
     */
    public static function get_instance(): self {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Ejecuta el controlador en función de los parámetros de la petición.
     *
     * @return void
     */
    private function execute_controller(callable | array $controller, ?string $mime_type = null): void {
        /**
         * Datos de la petición del usuario.
         * 
         * @var array
         */
        $request = $this->get_request();

        /**
         * @var mixed
         */
        $data = null;

        if (is_callable($controller)) {
            $data = $controller($request);
        }

        if (is_array($controller) && count($controller) < 1) {
            http_response_code(500);

            echo DLOutput::get_json([
                "status" => false,
                "error" => "El controlador no está definido"
            ]);

            exit;
        }

        /**
         * Nombre del controlador.
         * 
         * @var string
         */
        $controller_name = $controller[0] ?? '';

        /**
         * Nombre del método a llamar.
         * 
         * @var string
         */
        $method_name = $controller[1] ?? '';

        $controller_name = trim($controller_name);
        $method_name = trim($method_name);

        if (empty($controller_name)) {
            http_response_code(500);

            echo DLOutput::get_json([
                "status" => false,
                "error" => "El controlador no está definido"
            ]);

            exit;
        }

        if (empty($method_name)) {
            http_response_code(500);

            echo DLOutput::get_json([
                "status" => false,
                "error" => "Debe definir un método a ejecutar para el controlador '{$controller_name}'"
            ]);

            exit;
        }

        if (!class_exists($controller_name)) {
            http_response_code(500);

            echo DLOutput::get_json([
                "status" => false,
                "error" => "El controlador '{$controller_name}' debe crearse"
            ]);

            exit;
        }

        if (!method_exists($controller_name, $method_name)) {
            http_response_code(500);

            echo DLOutput::get_json([
                "status" => false,
                "error" => "Debe crear el método '{$method_name}' a ejecutar para el controlador '{$controller_name}'"
            ]);

            exit;
        }

        /**
         * Instancia de clase.
         */
        $instance = new $controller_name;

        $data = $instance->{$method_name}();

        $this->output->set_content($data);
        $this->output->print_response_data($mime_type);
    }

    public function execute_get_method(array $params, callable | array $controller, ?string $mime_type = null): void {
        if (!$this->get($params)) {
            return;
        }

        $this->execute_controller($controller, $mime_type);
    }

    public function execute_post_method(array $params, callable | array $controller, ?string $mime_type = null): void {
        if (!$this->post($params)) {
            return;
        }

        $this->execute_controller($controller, $mime_type);
    }
}