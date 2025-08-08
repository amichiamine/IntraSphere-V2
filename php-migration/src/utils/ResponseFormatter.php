<?php
/**
 * Formateur de réponses API standardisées
 * Compatible avec le format frontend attendu
 */

class ResponseFormatter {
    
    /**
     * Format de réponse API standard
     */
    public static function success($data = null, string $message = '', int $code = 200, array $meta = []): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c'),
            'meta' => $meta
        ];
        
        // Ajouter pagination si présente
        if (isset($meta['pagination'])) {
            $response['pagination'] = $meta['pagination'];
            unset($response['meta']['pagination']);
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Format d'erreur API standard
     */
    public static function error(string $message, int $code = 400, array $details = [], string $type = 'error'): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'status' => 'error',
            'error' => [
                'message' => $message,
                'code' => $code,
                'type' => $type,
                'details' => $details
            ],
            'timestamp' => date('c')
        ];
        
        // Log des erreurs pour debugging
        if ($code >= 500) {
            Logger::error('API Error', [
                'message' => $message,
                'code' => $code,
                'details' => $details,
                'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Format de liste paginée
     */
    public static function paginated(array $items, int $page, int $limit, int $total, array $meta = []): void {
        $totalPages = ceil($total / $limit);
        
        $pagination = [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
            'next_page' => $page < $totalPages ? $page + 1 : null,
            'prev_page' => $page > 1 ? $page - 1 : null
        ];
        
        self::success($items, 'Liste récupérée avec succès', 200, array_merge($meta, [
            'pagination' => $pagination
        ]));
    }
    
    /**
     * Format de validation d'erreur
     */
    public static function validationError(array $errors, string $message = 'Données de validation invalides'): void {
        self::error($message, 422, [
            'validation_errors' => $errors
        ], 'validation_error');
    }
    
    /**
     * Format d'erreur d'authentification
     */
    public static function authError(string $message = 'Authentification requise'): void {
        self::error($message, 401, [], 'auth_error');
    }
    
    /**
     * Format d'erreur de permission
     */
    public static function permissionError(string $message = 'Permissions insuffisantes'): void {
        self::error($message, 403, [], 'permission_error');
    }
    
    /**
     * Format de ressource non trouvée
     */
    public static function notFound(string $resource = 'Resource', string $message = ''): void {
        $msg = $message ?: "{$resource} non trouvé";
        self::error($msg, 404, [], 'not_found');
    }
    
    /**
     * Format d'erreur de rate limiting
     */
    public static function rateLimitError(int $retryAfter = 60): void {
        header("Retry-After: {$retryAfter}");
        self::error(
            "Trop de requêtes. Réessayez dans {$retryAfter} secondes.",
            429,
            ['retry_after' => $retryAfter],
            'rate_limit'
        );
    }
    
    /**
     * Format de redirection
     */
    public static function redirect(string $url, int $code = 302): void {
        header("Location: {$url}", true, $code);
        self::success(['redirect_url' => $url], 'Redirection', $code);
    }
    
    /**
     * Format de fichier/téléchargement
     */
    public static function file(string $filePath, string $filename = '', string $mimeType = ''): void {
        if (!file_exists($filePath)) {
            self::notFound('Fichier');
        }
        
        $filename = $filename ?: basename($filePath);
        $mimeType = $mimeType ?: mime_content_type($filePath);
        
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        exit;
    }
    
    /**
     * Format de cache avec ETag
     */
    public static function cached($data, string $etag, int $maxAge = 300): void {
        $clientEtag = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        
        if ($clientEtag === $etag) {
            http_response_code(304);
            header('ETag: ' . $etag);
            header('Cache-Control: max-age=' . $maxAge);
            exit;
        }
        
        header('ETag: ' . $etag);
        header('Cache-Control: max-age=' . $maxAge);
        
        self::success($data);
    }
}
?>