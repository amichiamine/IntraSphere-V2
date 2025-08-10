<?php
/**
 * Contrôleur de gestion des erreurs
 */

require_once __DIR__ . '/BaseController.php';

class ErrorController extends BaseController {
    
    public function notFound() {
        http_response_code(404);
        include __DIR__ . '/../../views/error/404.php';
    }
    
    public function serverError($message = null) {
        http_response_code(500);
        $error_message = $message;
        include __DIR__ . '/../../views/error/500.php';
    }
    
    public function unauthorized() {
        http_response_code(401);
        include __DIR__ . '/../../views/error/401.php';
    }
    
    public function forbidden() {
        http_response_code(403);
        include __DIR__ . '/../../views/error/403.php';
    }
}
?>