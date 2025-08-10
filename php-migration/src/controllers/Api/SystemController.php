<?php
namespace Api;

/**
 * Contrôleur API système - Cache, performance, monitoring
 * Pour diagnostics et optimisation
 */

class SystemController extends \BaseController {
    
    /**
     * GET /api/system/cache/stats
     */
    public function cacheStats(): void {
        $user = $this->requireRole('admin');
        
        try {
            $stats = CacheManagerOptimized::stats();
            $this->json($stats, 'Statistiques du cache récupérées');
            
        } catch (Exception $e) {
            Logger::error('Erreur stats cache', ['error' => $e->getMessage()]);
            $this->error('Erreur lors de la récupération des statistiques');
        }
    }
    
    /**
     * POST /api/system/cache/clear
     */
    public function clearCache(): void {
        $user = $this->requireRole('admin');
        
        try {
            $cleared = CacheManagerOptimized::clear();
            Logger::info('Cache vidé', ['user_id' => $user['id']]);
            
            $this->json([
                'cleared' => $cleared,
                'message' => 'Cache vidé avec succès'
            ]);
            
        } catch (Exception $e) {
            Logger::error('Erreur vidage cache', ['error' => $e->getMessage()]);
            $this->error('Erreur lors du vidage du cache');
        }
    }
    
    /**
     * POST /api/system/cache/cleanup
     */
    public function cleanupCache(): void {
        $user = $this->requireRole('admin');
        
        try {
            $cleaned = CacheManagerOptimized::cleanup();
            
            $this->json([
                'cleaned_entries' => $cleaned,
                'message' => 'Nettoyage du cache terminé'
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors du nettoyage');
        }
    }
    
    /**
     * GET /api/system/health
     */
    public function health(): void {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'filesystem' => $this->checkFilesystem(),
            'sessions' => $this->checkSessions(),
            'notifications' => $this->checkNotifications()
        ];
        
        $allHealthy = array_reduce($checks, fn($carry, $check) => $carry && $check['status'] === 'ok', true);
        
        $this->json([
            'status' => $allHealthy ? 'healthy' : 'degraded',
            'checks' => $checks,
            'timestamp' => date('c')
        ], 'Vérification système terminée', $allHealthy ? 200 : 503);
    }
    
    /**
     * GET /api/system/performance
     */
    public function performance(): void {
        $user = $this->requireRole('moderator');
        
        $metrics = [
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit')
            ],
            'php_version' => PHP_VERSION,
            'server_load' => function_exists('sys_getloadavg') ? sys_getloadavg() : null,
            'opcache' => function_exists('opcache_get_status') ? opcache_get_status() : null,
            'cache_stats' => CacheManagerOptimized::stats()
        ];
        
        $this->json($metrics, 'Métriques de performance récupérées');
    }
    
    /**
     * Vérifier la base de données
     */
    private function checkDatabase(): array {
        try {
            $db = Database::getInstance();
            $result = $db->fetchOne("SELECT 1 as test");
            
            return [
                'status' => 'ok',
                'message' => 'Base de données opérationnelle',
                'response_time_ms' => 0 // Simplified
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur base de données: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Vérifier le système de cache
     */
    private function checkCache(): array {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'test_data';
            
            CacheManagerOptimized::set($testKey, $testValue, 60);
            $retrieved = CacheManagerOptimized::get($testKey);
            CacheManagerOptimized::delete($testKey);
            
            return [
                'status' => $retrieved === $testValue ? 'ok' : 'error',
                'message' => 'Cache opérationnel',
                'stats' => CacheManagerOptimized::stats()
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur cache: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Vérifier le système de fichiers
     */
    private function checkFilesystem(): array {
        $uploadDir = ROOT_PATH . '/public/uploads';
        $cacheDir = ROOT_PATH . '/tmp/cache';
        
        $checks = [
            'upload_writable' => is_writable($uploadDir),
            'cache_writable' => is_writable($cacheDir),
            'tmp_space' => disk_free_space(sys_get_temp_dir())
        ];
        
        $allOk = array_reduce($checks, fn($carry, $check) => $carry && $check, true);
        
        return [
            'status' => $allOk ? 'ok' : 'warning',
            'message' => $allOk ? 'Système de fichiers OK' : 'Problèmes de permissions',
            'details' => $checks
        ];
    }
    
    /**
     * Vérifier les sessions
     */
    private function checkSessions(): array {
        $sessionStatus = session_status();
        
        return [
            'status' => $sessionStatus === PHP_SESSION_ACTIVE ? 'ok' : 'warning',
            'message' => 'Sessions PHP: ' . [
                PHP_SESSION_DISABLED => 'désactivées',
                PHP_SESSION_NONE => 'non démarrées', 
                PHP_SESSION_ACTIVE => 'actives'
            ][$sessionStatus],
            'session_name' => session_name(),
            'session_id' => session_id()
        ];
    }
    
    /**
     * Vérifier le système de notifications
     */
    private function checkNotifications(): array {
        try {
            // Test de création temporaire
            $tmpDir = ROOT_PATH . '/tmp';
            $canWrite = is_writable($tmpDir);
            
            return [
                'status' => $canWrite ? 'ok' : 'error',
                'message' => $canWrite ? 'Système de notifications OK' : 'Impossible d\'écrire les notifications',
                'sse_support' => true // Server-Sent Events supportés
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur notifications: ' . $e->getMessage()
            ];
        }
    }
}
?>