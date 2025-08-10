<?php
/**
 * Gestionnaire de cache unifié
 * Compatible avec les besoins de performance TypeScript (TanStack Query)
 */

class CacheManager {
    private static array $cache = [];
    private static array $timestamps = [];
    
    /**
     * Stocker une valeur dans le cache
     */
    public static function set(string $key, $value, int $ttl = null): bool {
        $ttl = $ttl ?? CACHE_TTL;
        
        self::$cache[$key] = $value;
        self::$timestamps[$key] = time() + $ttl;
        
        return true;
    }
    
    /**
     * Récupérer une valeur du cache
     */
    public static function get(string $key) {
        if (!self::has($key)) {
            return null;
        }
        
        return self::$cache[$key];
    }
    
    /**
     * Vérifier si une clé existe et est valide
     */
    public static function has(string $key): bool {
        if (!isset(self::$cache[$key])) {
            return false;
        }
        
        if (time() > self::$timestamps[$key]) {
            self::delete($key);
            return false;
        }
        
        return true;
    }
    
    /**
     * Supprimer une clé du cache
     */
    public static function delete(string $key): bool {
        unset(self::$cache[$key]);
        unset(self::$timestamps[$key]);
        return true;
    }
    
    /**
     * Vider tout le cache
     */
    public static function clear(): bool {
        self::$cache = [];
        self::$timestamps = [];
        return true;
    }
    
    /**
     * Obtenir ou définir une valeur (pattern classique)
     */
    public static function remember(string $key, callable $callback, int $ttl = null) {
        if (self::has($key)) {
            return self::get($key);
        }
        
        $value = $callback();
        self::set($key, $value, $ttl);
        
        return $value;
    }
    
    /**
     * Cache spécialisé pour les requêtes de base de données
     */
    public static function rememberQuery(string $sql, array $params, callable $callback, int $ttl = null) {
        $key = 'query_' . md5($sql . serialize($params));
        return self::remember($key, $callback, $ttl);
    }
    
    /**
     * Cache pour les statistiques
     */
    public static function rememberStats(string $statKey, callable $callback, int $ttl = 600) {
        $key = 'stats_' . $statKey;
        return self::remember($key, $callback, $ttl);
    }
    
    /**
     * Cache pour les permissions utilisateur
     */
    public static function rememberUserPermissions(string $userId, callable $callback, int $ttl = 900) {
        $key = 'user_permissions_' . $userId;
        return self::remember($key, $callback, $ttl);
    }
    
    /**
     * Invalider le cache des permissions d'un utilisateur
     */
    public static function invalidateUserPermissions(string $userId): bool {
        return self::delete('user_permissions_' . $userId);
    }
    
    /**
     * Invalider le cache des statistiques
     */
    public static function invalidateStats(): bool {
        foreach (array_keys(self::$cache) as $key) {
            if (strpos($key, 'stats_') === 0) {
                self::delete($key);
            }
        }
        return true;
    }
    
    /**
     * Nettoyer les entrées expirées
     */
    public static function cleanup(): int {
        $cleaned = 0;
        $now = time();
        
        foreach (self::$timestamps as $key => $expiry) {
            if ($now > $expiry) {
                self::delete($key);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Obtenir les statistiques du cache
     */
    public static function getStats(): array {
        self::cleanup();
        
        return [
            'total_keys' => count(self::$cache),
            'memory_usage' => memory_get_usage(),
            'hit_rate' => 0, // À implémenter avec des compteurs
            'cache_enabled' => CACHE_ENABLED
        ];
    }
    
    /**
     * Générer une clé de cache pour une route API
     */
    public static function generateApiKey(string $endpoint, array $params = [], string $userId = null): string {
        $keyParts = [$endpoint];
        
        if ($userId) {
            $keyParts[] = 'user_' . $userId;
        }
        
        if (!empty($params)) {
            ksort($params);
            $keyParts[] = md5(serialize($params));
        }
        
        return 'api_' . implode('_', $keyParts);
    }
    
    /**
     * Middleware de cache pour les réponses API
     */
    public static function cacheApiResponse(string $endpoint, array $params, callable $callback, int $ttl = 300) {
        if (!CACHE_ENABLED) {
            return $callback();
        }
        
        $key = self::generateApiKey($endpoint, $params);
        return self::remember($key, $callback, $ttl);
    }
}
?>