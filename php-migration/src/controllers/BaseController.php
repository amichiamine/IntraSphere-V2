<?php
/**
 * Contrôleur de base pour tous les contrôleurs
 */

abstract class BaseController {
    
    /**
     * Retourner une réponse JSON
     */
    protected function json($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Retourner une erreur JSON
     */
    protected function error(string $message, int $statusCode = 400): void {
        $this->json(['error' => $message], $statusCode);
    }
    
    /**
     * Vérifier l'authentification
     */
    protected function requireAuth(): array {
        if (!isset($_SESSION['user'])) {
            $this->error('Non authentifié', 401);
        }
        
        return $_SESSION['user'];
    }
    
    /**
     * Vérifier le rôle utilisateur
     */
    protected function requireRole(string $role): array {
        $user = $this->requireAuth();
        
        $roleHierarchy = [
            'employee' => 1,
            'moderator' => 2,
            'admin' => 3
        ];
        
        $userLevel = $roleHierarchy[$user['role']] ?? 0;
        $requiredLevel = $roleHierarchy[$role] ?? 999;
        
        if ($userLevel < $requiredLevel) {
            $this->error('Permissions insuffisantes', 403);
        }
        
        return $user;
    }
    
    /**
     * Vérifier une permission spécifique
     */
    protected function requirePermission(string $permission): array {
        $user = $this->requireAuth();
        
        // Admin a toutes les permissions
        if ($user['role'] === 'admin') {
            return $user;
        }
        
        $permissionModel = new Permission();
        if (!$permissionModel->hasPermission($user['id'], $permission)) {
            $this->error('Permission refusée', 403);
        }
        
        return $user;
    }
    
    /**
     * Obtenir les données POST/PUT en JSON
     */
    protected function getJsonInput(): array {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Données JSON invalides');
        }
        
        return $data ?? [];
    }
    
    /**
     * Obtenir les paramètres GET avec validation
     */
    protected function getQueryParam(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Valider les données requises
     */
    protected function validateRequired(array $data, array $requiredFields): void {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            $this->error('Champs requis manquants: ' . implode(', ', $missing));
        }
    }
    
    /**
     * Nettoyer et valider les données d'entrée
     */
    protected function sanitizeInput(array $data): array {
        $clean = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $clean[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } elseif (is_array($value)) {
                $clean[$key] = $this->sanitizeInput($value);
            } else {
                $clean[$key] = $value;
            }
        }
        
        return $clean;
    }
    
    /**
     * Pagination helper
     */
    protected function getPaginationParams(): array {
        $page = max(1, (int) $this->getQueryParam('page', 1));
        $limit = min(MAX_PAGE_SIZE, max(1, (int) $this->getQueryParam('limit', DEFAULT_PAGE_SIZE)));
        
        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => ($page - 1) * $limit
        ];
    }
    
    /**
     * Réponse paginée
     */
    protected function paginatedResponse(array $data, int $total, int $page, int $limit): void {
        $this->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    /**
     * Render une vue PHP
     */
    protected function view(string $viewName, array $data = []): void {
        $viewPath = VIEWS_PATH . '/' . $viewName . '.php';
        
        if (!file_exists($viewPath)) {
            $this->error('Vue introuvable: ' . $viewName, 404);
        }
        
        // Extraire les variables pour la vue
        extract($data);
        
        // Démarrer la capture de sortie
        ob_start();
        
        // Inclure la vue
        include $viewPath;
        
        // Récupérer le contenu
        $content = ob_get_clean();
        
        // Afficher le contenu
        echo $content;
    }
    
    /**
     * Redirection
     */
    protected function redirect(string $url, int $statusCode = 302): void {
        header("Location: {$url}", true, $statusCode);
        exit;
    }
    
    /**
     * Vérifier le token CSRF (optionnel)
     */
    protected function validateCsrfToken(): void {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = $_SESSION['_token'] ?? '';
        
        if (!hash_equals($sessionToken, $token)) {
            $this->error('Token CSRF invalide', 403);
        }
    }
    
    /**
     * Générer un token CSRF
     */
    protected function generateCsrfToken(): string {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['_token'];
    }
    
    /**
     * Rate limiting simple
     */
    protected function checkRateLimit(string $key, int $maxRequests = 60, int $timeWindow = 60): void {
        $cacheKey = "rate_limit:{$key}";
        
        if (!isset($_SESSION[$cacheKey])) {
            $_SESSION[$cacheKey] = [
                'count' => 1,
                'reset_time' => time() + $timeWindow
            ];
            return;
        }
        
        $rateLimit = $_SESSION[$cacheKey];
        
        if (time() > $rateLimit['reset_time']) {
            $_SESSION[$cacheKey] = [
                'count' => 1,
                'reset_time' => time() + $timeWindow
            ];
            return;
        }
        
        if ($rateLimit['count'] >= $maxRequests) {
            $this->error('Trop de requêtes', 429);
        }
        
        $_SESSION[$cacheKey]['count']++;
    }
    
    /**
     * Log d'activité
     */
    protected function logActivity(string $action, array $data = []): void {
        if (!LOG_ENABLED) return;
        
        $user = $_SESSION['user'] ?? null;
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'user_id' => $user['id'] ?? 'anonymous',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'data' => $data
        ];
        
        error_log('ACTIVITY: ' . json_encode($logEntry));
    }
}
?>