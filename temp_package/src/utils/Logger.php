<?php
/**
 * Système de logging unifié
 * Compatible avec les besoins de monitoring TypeScript
 */

class Logger {
    
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_CRITICAL = 5;
    
    private static array $levelNames = [
        self::LEVEL_DEBUG => 'DEBUG',
        self::LEVEL_INFO => 'INFO',
        self::LEVEL_WARNING => 'WARNING',
        self::LEVEL_ERROR => 'ERROR',
        self::LEVEL_CRITICAL => 'CRITICAL'
    ];
    
    private static int $currentLevel = self::LEVEL_INFO;
    
    /**
     * Initialiser le logger avec le niveau configuré
     */
    public static function init(): void {
        if (LOG_LEVEL === 'DEBUG') {
            self::$currentLevel = self::LEVEL_DEBUG;
        } elseif (LOG_LEVEL === 'ERROR') {
            self::$currentLevel = self::LEVEL_ERROR;
        } else {
            self::$currentLevel = self::LEVEL_INFO;
        }
    }
    
    /**
     * Logger un message avec niveau
     */
    public static function log(int $level, string $message, array $context = []): void {
        if (!LOG_ENABLED || $level < self::$currentLevel) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $levelName = self::$levelNames[$level] ?? 'UNKNOWN';
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        
        $logMessage = "[{$timestamp}] {$levelName}: {$message}{$contextStr}";
        
        // Écrire dans le log système PHP
        error_log($logMessage);
        
        // En développement, afficher aussi dans la console
        if (APP_DEBUG && $level >= self::LEVEL_WARNING) {
            echo $logMessage . PHP_EOL;
        }
    }
    
    /**
     * Logger des messages de debug
     */
    public static function debug(string $message, array $context = []): void {
        self::log(self::LEVEL_DEBUG, $message, $context);
    }
    
    /**
     * Logger des messages d'information
     */
    public static function info(string $message, array $context = []): void {
        self::log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * Logger des avertissements
     */
    public static function warning(string $message, array $context = []): void {
        self::log(self::LEVEL_WARNING, $message, $context);
    }
    
    /**
     * Logger des erreurs
     */
    public static function error(string $message, array $context = []): void {
        self::log(self::LEVEL_ERROR, $message, $context);
    }
    
    /**
     * Logger des erreurs critiques
     */
    public static function critical(string $message, array $context = []): void {
        self::log(self::LEVEL_CRITICAL, $message, $context);
    }
    
    /**
     * Logger les activités utilisateur (conforme à BaseController)
     */
    public static function activity(string $action, array $data = [], string $userId = null): void {
        $context = [
            'action' => $action,
            'user_id' => $userId ?? ($_SESSION['user']['id'] ?? 'anonymous'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'timestamp' => time(),
            'data' => $data
        ];
        
        self::info("ACTIVITY: {$action}", $context);
    }
    
    /**
     * Logger les erreurs API avec contexte
     */
    public static function apiError(string $endpoint, string $method, string $error, array $context = []): void {
        $context = array_merge($context, [
            'endpoint' => $endpoint,
            'method' => $method,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        self::error("API_ERROR: {$error}", $context);
    }
    
    /**
     * Logger les requêtes API (performance monitoring)
     */
    public static function apiRequest(string $endpoint, string $method, int $statusCode, float $duration, array $context = []): void {
        $context = array_merge($context, [
            'endpoint' => $endpoint,
            'method' => $method,
            'status_code' => $statusCode,
            'duration_ms' => round($duration * 1000, 2),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        if ($statusCode >= 400) {
            self::warning("API_REQUEST: {$method} {$endpoint} - {$statusCode}", $context);
        } else {
            self::info("API_REQUEST: {$method} {$endpoint} - {$statusCode}", $context);
        }
    }
    
    /**
     * Logger les tentatives de sécurité suspectes
     */
    public static function security(string $event, array $context = []): void {
        $context = array_merge($context, [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'none'
        ]);
        
        self::warning("SECURITY: {$event}", $context);
    }
    
    /**
     * Logger les erreurs de base de données
     */
    public static function database(string $query, string $error, array $params = []): void {
        self::error("DATABASE_ERROR: {$error}", [
            'query' => $query,
            'params' => $params
        ]);
    }
    
    /**
     * Logger les performances (requêtes lentes, etc.)
     */
    public static function performance(string $operation, float $duration, array $context = []): void {
        $durationMs = round($duration * 1000, 2);
        
        if ($durationMs > 1000) { // Plus d'1 seconde
            self::warning("SLOW_OPERATION: {$operation} took {$durationMs}ms", $context);
        } elseif ($durationMs > 500) { // Plus de 500ms
            self::info("PERFORMANCE: {$operation} took {$durationMs}ms", $context);
        }
    }
    
    /**
     * Obtenir les statistiques de logging
     */
    public static function getStats(): array {
        return [
            'enabled' => LOG_ENABLED,
            'level' => self::$levelNames[self::$currentLevel],
            'level_numeric' => self::$currentLevel
        ];
    }
}

// Initialiser le logger
Logger::init();
?>