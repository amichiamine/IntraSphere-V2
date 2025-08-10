<?php
/**
 * Formateur de réponses API standardisées
 */

class ResponseFormatter {
    
    /**
     * Retourner une réponse de succès
     */
    public static function success($data = null, string $message = 'Succès', int $statusCode = 200, array $meta = []): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Retourner une erreur
     */
    public static function error(string $message, int $statusCode = 400, array $details = []): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if (!empty($details)) {
            $response['details'] = $details;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Retourner une liste paginée
     */
    public static function paginated(array $items, int $page, int $limit, int $total, array $meta = []): void {
        $totalPages = ceil($total / $limit);
        
        $pagination = [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'totalPages' => $totalPages,
            'hasNext' => $page < $totalPages,
            'hasPrev' => $page > 1
        ];
        
        $response = [
            'success' => true,
            'data' => $items,
            'pagination' => $pagination
        ];
        
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }
        
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Erreur de validation
     */
    public static function validationError(array $errors, string $message = 'Données invalides'): void {
        self::error($message, 422, $errors);
    }
    
    /**
     * Erreur d'authentification
     */
    public static function authError(string $message = 'Non autorisé'): void {
        self::error($message, 401);
    }
    
    /**
     * Erreur d'autorisation
     */
    public static function forbiddenError(string $message = 'Accès refusé'): void {
        self::error($message, 403);
    }
    
    /**
     * Ressource non trouvée
     */
    public static function notFoundError(string $message = 'Ressource non trouvée'): void {
        self::error($message, 404);
    }
    
    /**
     * Erreur serveur
     */
    public static function serverError(string $message = 'Erreur interne du serveur'): void {
        self::error($message, 500);
    }
    
    /**
     * Retourner une réponse simple
     */
    public static function message(string $message, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => $statusCode < 400,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>