<?php
/**
 * Système de Rate Limiting unifié
 * Compatible avec les standards de sécurité TypeScript
 */

class RateLimiter {
    private static array $attempts = [];
    
    /**
     * Vérifier et enregistrer une tentative
     * @param string $key Clé unique (IP, username, etc.)
     * @param int $maxAttempts Nombre maximum de tentatives
     * @param int $windowSeconds Fenêtre de temps en secondes
     * @return bool true si autorisé, false si limite atteinte
     */
    public static function checkRateLimit(string $key, int $maxAttempts = 5, int $windowSeconds = 300): bool {
        $now = time();
        $windowStart = $now - $windowSeconds;
        
        // Nettoyer les anciennes tentatives
        if (isset(self::$attempts[$key])) {
            self::$attempts[$key] = array_filter(
                self::$attempts[$key], 
                fn($timestamp) => $timestamp > $windowStart
            );
        } else {
            self::$attempts[$key] = [];
        }
        
        // Vérifier la limite
        if (count(self::$attempts[$key]) >= $maxAttempts) {
            return false;
        }
        
        // Enregistrer la tentative
        self::$attempts[$key][] = $now;
        
        return true;
    }
    
    /**
     * Obtenir le nombre de tentatives restantes
     */
    public static function getRemainingAttempts(string $key, int $maxAttempts = 5, int $windowSeconds = 300): int {
        $now = time();
        $windowStart = $now - $windowSeconds;
        
        if (!isset(self::$attempts[$key])) {
            return $maxAttempts;
        }
        
        $recentAttempts = array_filter(
            self::$attempts[$key], 
            fn($timestamp) => $timestamp > $windowStart
        );
        
        return max(0, $maxAttempts - count($recentAttempts));
    }
    
    /**
     * Obtenir le temps d'attente avant la prochaine tentative
     */
    public static function getRetryAfter(string $key, int $windowSeconds = 300): int {
        if (!isset(self::$attempts[$key]) || empty(self::$attempts[$key])) {
            return 0;
        }
        
        $oldestAttempt = min(self::$attempts[$key]);
        $nextAllowedTime = $oldestAttempt + $windowSeconds;
        
        return max(0, $nextAllowedTime - time());
    }
    
    /**
     * Réinitialiser les tentatives pour une clé
     */
    public static function resetAttempts(string $key): void {
        unset(self::$attempts[$key]);
    }
    
    /**
     * Configurations prédéfinies pour différents endpoints
     */
    public static function getConfig(string $endpoint): array {
        $configs = [
            'login' => ['maxAttempts' => 5, 'windowSeconds' => 300], // 5 tentatives en 5 minutes
            'forgot_password' => ['maxAttempts' => 3, 'windowSeconds' => 3600], // 3 tentatives en 1 heure
            'register' => ['maxAttempts' => 3, 'windowSeconds' => 900], // 3 tentatives en 15 minutes
            'api_general' => ['maxAttempts' => 100, 'windowSeconds' => 3600], // 100 requêtes par heure
            'upload' => ['maxAttempts' => 10, 'windowSeconds' => 600], // 10 uploads en 10 minutes
        ];
        
        return $configs[$endpoint] ?? ['maxAttempts' => 50, 'windowSeconds' => 3600];
    }
    
    /**
     * Middleware pour vérifier automatiquement le rate limiting
     */
    public static function middleware(string $endpoint, string $identifier = null): bool {
        $config = self::getConfig($endpoint);
        $key = $endpoint . '_' . ($identifier ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        return self::checkRateLimit($key, $config['maxAttempts'], $config['windowSeconds']);
    }
}
?>