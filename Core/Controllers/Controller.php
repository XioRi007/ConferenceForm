<?php
namespace Core\Controllers;

class Controller {
    public function __construct() {
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * Handles exceptions and sends an appropriate HTTP response with a JSON-encoded error message.
     * @param Exception $e The exception that was thrown.
     * @return void
     */
    public function handleException($e) {
        http_response_code(500);        
        echo json_encode(['error' => $e->getMessage()]);
    }
}
