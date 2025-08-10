<?php
/**
 * Gestionnaire de cache unifié optimisé
 * Compatible avec la stratégie frontend (LocalStorage, SessionStorage)
 */

class CacheManagerOptimized {
    private static array $memoryCache = [];
    private static bool $apcu_available = false;
    
    /**
     * Initialiser le gestionnaire de cache
     */
    public static function init(): void {
        self::$apcu_available = function_exists('apcu_enabled') && apcu_enabled();
        
        // Créer le dossier de cache si nécessaire
        if (!is_dir(ROOT_PATH . '/tmp/cache')) {
            mkdir(ROOT_PATH . '/tmp/cache', 0755, true);
        }
    }
    
    /**
     * Mettre en cache avec TTL
     */
    public static function set(string $key, $data, int $ttl = null): bool {
        $ttl = $ttl ?? CACHE_TTL;
        $expiry = time() + $ttl;
        
        $cacheData = [
            'data' => $data,
            'expiry' => $expiry,
            'created' => time()
        ];
        
        // Cache mémoire (priorité haute)
        self::$memoryCache[$key] = $cacheData;
        
        // Cache APCu si disponible
        if (self::$apcu_available) {
            return apcu_store($key, serialize($cacheData), $ttl);
        }
        
        // Cache fichier (fallback)
        $filename = self::getCacheFilename($key);
        return file_put_contents($filename, serialize($cacheData)) !== false;
    }
    
    /**
     * Récupérer du cache
     */
    public static function get(string $key) {
        // Vérifier cache mémoire d'abord
        if (isset(self::$memoryCache[$key])) {
            $cacheData = self::$memoryCache[$key];
            if (time() < $cacheData['expiry']) {
                return $cacheData['data'];
            }
            unset(self::$memoryCache[$key]);
        }
        
        // Vérifier APCu
        if (self::$apcu_available) {
            $serialized = apcu_fetch($key);
            if ($serialized !== false) {
                $cacheData = unserialize($serialized);
                if (time() < $cacheData['expiry']) {
                    self::$memoryCache[$key] = $cacheData; // Populator cache mémoire
                    return $cacheData['data'];
                }
                apcu_delete($key);
            }
        }
        
        // Vérifier cache fichier
        $filename = self::getCacheFilename($key);
        if (file_exists($filename)) {
            $cacheData = unserialize(file_get_contents($filename));
            if (time() < $cacheData['expiry']) {
                self::$memoryCache[$key] = $cacheData;
                return $cacheData['data'];
            }
            unlink($filename);
        }
        
        return null;
    }
    
    /**
     * Supprimer du cache
     */
    public static function delete(string $key): bool {
        unset(self::$memoryCache[$key]);
        
        if (self::$apcu_available) {
            apcu_delete($key);
        }
        
        $filename = self::getCacheFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
    
    /**
     * Vider tout le cache
     */
    public static function clear(): bool {
        self::$memoryCache = [];
        
        if (self::$apcu_available) {
            apcu_clear_cache();
        }
        
        $cacheDir = ROOT_PATH . '/tmp/cache/';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*.cache');
            foreach ($files as $file) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    /**
     * Vérifier si une clé existe et est valide
     */
    public static function has(string $key): bool {
        return self::get($key) !== null;
    }
    
    /**
     * Cache avec callback (pattern remember)
     */
    public static function remember(string $key, callable $callback, int $ttl = null) {
        $data = self::get($key);
        if ($data !== null) {
            return $data;
        }
        
        $data = $callback();
        self::set($key, $data, $ttl);
        return $data;
    }
    
    /**
     * Invalider par pattern
     */
    public static function invalidatePattern(string $pattern): int {
        $deleted = 0;
        
        // Cache mémoire
        foreach (array_keys(self::$memoryCache) as $key) {
            if (fnmatch($pattern, $key)) {
                unset(self::$memoryCache[$key]);
                $deleted++;
            }
        }
        
        // Cache APCu
        if (self::$apcu_available) {
            $info = apcu_cache_info();
            foreach ($info['cache_list'] as $entry) {
                if (fnmatch($pattern, $entry['info'])) {
                    apcu_delete($entry['info']);
                    $deleted++;
                }
            }
        }
        
        // Cache fichier
        $cacheDir = ROOT_PATH . '/tmp/cache/';
        $files = glob($cacheDir . '*.cache');
        foreach ($files as $file) {
            $key = self::getKeyFromFilename($file);
            if (fnmatch($pattern, $key)) {
                unlink($file);
                $deleted++;
            }
        }
        
        return $deleted;
    }
    
    /**
     * Statistiques du cache
     */
    public static function stats(): array {
        $stats = [
            'memory_cache_size' => count(self::$memoryCache),
            'apcu_available' => self::$apcu_available,
            'file_cache_size' => 0,
            'total_size_bytes' => 0
        ];
        
        // Compter les fichiers de cache
        $cacheDir = ROOT_PATH . '/tmp/cache/';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*.cache');
            $stats['file_cache_size'] = count($files);
            
            foreach ($files as $file) {
                $stats['total_size_bytes'] += filesize($file);
            }
        }
        
        // Stats APCu
        if (self::$apcu_available) {
            $info = apcu_cache_info();
            $stats['apcu_cache_size'] = count($info['cache_list']);
            $stats['apcu_memory_size'] = $info['mem_size'];
        }
        
        return $stats;
    }
    
    /**
     * Nettoyage automatique des caches expirés
     */
    public static function cleanup(): int {
        $cleaned = 0;
        
        // Nettoyer cache mémoire
        foreach (self::$memoryCache as $key => $cacheData) {
            if (time() >= $cacheData['expiry']) {
                unset(self::$memoryCache[$key]);
                $cleaned++;
            }
        }
        
        // Nettoyer cache fichier
        $cacheDir = ROOT_PATH . '/tmp/cache/';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*.cache');
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $cacheData = unserialize(file_get_contents($file));
                    if (time() >= $cacheData['expiry']) {
                        unlink($file);
                        $cleaned++;
                    }
                }
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Cache spécialisé pour les requêtes BDD
     */
    public static function queryCache(string $sql, array $params, callable $callback, int $ttl = 300) {
        $key = 'query:' . md5($sql . serialize($params));
        return self::remember($key, $callback, $ttl);
    }
    
    /**
     * Cache spécialisé pour les sessions utilisateur
     */
    public static function userCache(string $userId, string $key, $data = null, int $ttl = 1800) {
        $cacheKey = "user:{$userId}:{$key}";
        
        if ($data !== null) {
            return self::set($cacheKey, $data, $ttl);
        }
        
        return self::get($cacheKey);
    }
    
    /**
     * Nom de fichier pour une clé de cache
     */
    private static function getCacheFilename(string $key): string {
        $hash = md5($key);
        return ROOT_PATH . "/tmp/cache/{$hash}.cache";
    }
    
    /**
     * Extraire la clé depuis un nom de fichier
     */
    private static function getKeyFromFilename(string $filename): string {
        // Cette fonction est simplifiée, en réalité il faudrait une table de mapping
        return basename($filename, '.cache');
    }
    
    /**
     * Synchroniser avec le cache frontend via headers HTTP
     */
    public static function setCacheHeaders(string $key, int $maxAge = 300): void {
        $etag = md5($key . filemtime(__FILE__));
        $lastModified = date('D, d M Y H:i:s \G\M\T', filemtime(__FILE__));
        
        header("Cache-Control: public, max-age={$maxAge}");
        header("ETag: \"{$etag}\"");
        header("Last-Modified: {$lastModified}");
        
        // Vérifier If-None-Match
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
            $_SERVER['HTTP_IF_NONE_MATCH'] === "\"{$etag}\"") {
            http_response_code(304);
            exit;
        }
    }
}

// Initialiser automatiquement
CacheManagerOptimized::init();
?>