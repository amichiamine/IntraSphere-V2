<?php
/**
 * Gestionnaire de réponses API unifié
 * Compatible avec le format TypeScript/Express
 */

class ApiResponse {
    
    /**
     * Réponse de succès
     */
    public static function success($data = null, string $message = null, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        if ($data !== null) {
            if (is_array($data) && isset($data['data']) && isset($data['pagination'])) {
                // Réponse paginée
                $response = array_merge($response, $data);
            } else {
                $response['data'] = $data;
            }
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Réponse d'erreur
     */
    public static function error(string $message, int $statusCode = 400, array $errors = []): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'message' => $message
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        // Ajouter des détails supplémentaires en mode debug
        if (APP_DEBUG && $statusCode >= 500) {
            $response['debug'] = [
                'file' => debug_backtrace()[1]['file'] ?? 'unknown',
                'line' => debug_backtrace()[1]['line'] ?? 'unknown',
                'trace' => array_slice(debug_backtrace(), 1, 5)
            ];
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Réponse de validation
     */
    public static function validationError(array $errors, string $message = "Données invalides"): void {
        self::error($message, 422, $errors);
    }
    
    /**
     * Réponse non autorisé
     */
    public static function unauthorized(string $message = "Non autorisé"): void {
        self::error($message, 401);
    }
    
    /**
     * Réponse interdit
     */
    public static function forbidden(string $message = "Accès refusé"): void {
        self::error($message, 403);
    }
    
    /**
     * Réponse non trouvé
     */
    public static function notFound(string $message = "Ressource non trouvée"): void {
        self::error($message, 404);
    }
    
    /**
     * Réponse rate limit
     */
    public static function rateLimited(string $message = "Trop de requêtes", int $retryAfter = 60): void {
        header("Retry-After: {$retryAfter}");
        self::error($message, 429);
    }
    
    /**
     * Réponse paginée
     */
    public static function paginated(array $data, int $total, int $page, int $limit, string $message = null): void {
        $response = [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
                'has_next' => $page < ceil($total / $limit),
                'has_prev' => $page > 1
            ]
        ];
        
        self::success($response, $message);
    }
    
    /**
     * Réponse de création
     */
    public static function created($data, string $message = "Créé avec succès"): void {
        self::success($data, $message, 201);
    }
    
    /**
     * Réponse de mise à jour
     */
    public static function updated($data = null, string $message = "Mis à jour avec succès"): void {
        self::success($data, $message);
    }
    
    /**
     * Réponse de suppression
     */
    public static function deleted(string $message = "Supprimé avec succès"): void {
        self::success(null, $message);
    }
    
    /**
     * Réponse sans contenu
     */
    public static function noContent(): void {
        http_response_code(204);
        exit;
    }
    
    /**
     * Réponse de redirection
     */
    public static function redirect(string $url, int $statusCode = 302): void {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Formater une erreur d'exception
     */
    public static function fromException(Exception $e, int $statusCode = 500): void {
        $message = APP_DEBUG ? $e->getMessage() : "Erreur interne du serveur";
        
        $errors = [];
        if (APP_DEBUG) {
            $errors = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => array_slice($e->getTrace(), 0, 5)
            ];
        }
        
        self::error($message, $statusCode, $errors);
    }
    
    /**
     * Formater les erreurs de validation
     */
    public static function formatValidationErrors(array $validationResult): array {
        if (isset($validationResult['errors'])) {
            return $validationResult['errors'];
        }
        
        return [];
    }
    
    /**
     * Middleware pour les en-têtes de sécurité
     */
    public static function setSecurityHeaders(): void {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        
        if (HTTPS_ENABLED) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'');
        
        // CORS
        if (API_CORS_ENABLED) {
            $allowedOrigins = API_CORS_ORIGINS;
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            
            if (in_array($origin, $allowedOrigins)) {
                header("Access-Control-Allow-Origin: {$origin}");
            }
            
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Allow-Credentials: true');
        }
    }
}
?>