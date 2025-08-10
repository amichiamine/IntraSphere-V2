<?php
namespace Api;

/**
 * Contrôleur API d'administration
 * Équivalent aux routes /api/admin/* TypeScript
 */

class AdminController extends \BaseController {
    private \User $userModel;
    private \Announcement $announcementModel;
    private \Document $documentModel;
    private \Message $messageModel;
    private \Training $trainingModel;
    
    public function __construct() {
        $this->userModel = new \User();
        $this->announcementModel = new \Announcement();
        $this->documentModel = new \Document();
        $this->messageModel = new \Message();
        $this->trainingModel = new \Training();
    }
    
    /**
     * GET /api/stats
     */
    public function stats(): void {
        $user = $this->requireRole('moderator');
        
        try {
            // Statistiques générales
            $stats = [
                'totalUsers' => $this->userModel->count(),
                'totalAnnouncements' => $this->announcementModel->count(),
                'totalDocuments' => $this->documentModel->count(),
                'totalMessages' => $this->messageModel->count(),
                'totalTrainings' => $this->trainingModel->count(),
                
                // Statistiques détaillées
                'activeUsers' => $this->userModel->countActive(),
                'importantAnnouncements' => $this->announcementModel->countImportant(),
                'recentDocuments' => $this->documentModel->countRecent(30),
                'unreadMessages' => $this->messageModel->countUnread(),
                'upcomingTrainings' => $this->trainingModel->countUpcoming(),
                
                // Tendances (7 derniers jours)
                'trends' => [
                    'newUsers' => $this->userModel->countCreatedSince(7),
                    'newAnnouncements' => $this->announcementModel->countCreatedSince(7),
                    'newDocuments' => $this->documentModel->countCreatedSince(7),
                    'newMessages' => $this->messageModel->countCreatedSince(7)
                ]
            ];
            
            $this->json($stats);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération des statistiques');
        }
    }
    
    /**
     * GET /api/permissions
     */
    public function permissions(): void {
        $this->requireRole('admin');
        
        $permissionModel = new \Permission();
        $permissions = $permissionModel->getAllWithUsers();
        
        $this->json($permissions);
    }
    
    /**
     * GET /api/admin/users-overview
     */
    public function usersOverview(): void {
        $this->requireRole('admin');
        
        $overview = [
            'total' => $this->userModel->count(),
            'active' => $this->userModel->countActive(),
            'inactive' => $this->userModel->countInactive(),
            'byRole' => $this->userModel->countByRole(),
            'recentlyCreated' => $this->userModel->getRecentlyCreated(10),
            'recentlyActive' => $this->userModel->getRecentlyActive(10)
        ];
        
        $this->json($overview);
    }
    
    /**
     * GET /api/admin/content-overview
     */
    public function contentOverview(): void {
        $this->requireRole('moderator');
        
        $overview = [
            'announcements' => [
                'total' => $this->announcementModel->count(),
                'important' => $this->announcementModel->countImportant(),
                'byType' => $this->announcementModel->countByType(),
                'recent' => $this->announcementModel->getRecent(5)
            ],
            'documents' => [
                'total' => $this->documentModel->count(),
                'byCategory' => $this->documentModel->countByCategory(),
                'recent' => $this->documentModel->getRecent(5)
            ],
            'messages' => [
                'total' => $this->messageModel->count(),
                'unread' => $this->messageModel->countUnread(),
                'recent' => $this->messageModel->getRecent(5)
            ]
        ];
        
        $this->json($overview);
    }
    
    /**
     * GET /api/admin/system-info
     */
    public function systemInfo(): void {
        $this->requireRole('admin');
        
        $info = [
            'version' => APP_VERSION,
            'environment' => APP_ENV,
            'php_version' => PHP_VERSION,
            'database' => [
                'type' => DB_TYPE ?? 'mysql',
                'version' => $this->getDatabaseVersion()
            ],
            'server_info' => [
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'php_sapi' => PHP_SAPI,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time')
            ],
            'disk_usage' => $this->getDiskUsage(),
            'uptime' => $this->getUptime()
        ];
        
        $this->json($info);
    }
    
    /**
     * POST /api/admin/maintenance-mode
     */
    public function toggleMaintenanceMode(): void {
        $this->requireRole('admin');
        
        $data = $this->getJsonInput();
        $enabled = $data['enabled'] ?? false;
        
        // Sauvegarder l'état en fichier
        $maintenanceFile = ROOT_PATH . '/.maintenance';
        
        if ($enabled) {
            file_put_contents($maintenanceFile, json_encode([
                'enabled' => true,
                'message' => $data['message'] ?? 'Site en maintenance',
                'enabled_by' => $this->currentUser['id'],
                'enabled_at' => date('Y-m-d H:i:s')
            ]));
        } else {
            if (file_exists($maintenanceFile)) {
                unlink($maintenanceFile);
            }
        }
        
        $this->logActivity('maintenance_mode_' . ($enabled ? 'enabled' : 'disabled'));
        
        $this->json([
            'success' => true,
            'maintenance_mode' => $enabled,
            'message' => $enabled ? 'Mode maintenance activé' : 'Mode maintenance désactivé'
        ]);
    }
    
    // Méthodes privées pour les informations système
    
    private function getDatabaseVersion(): string {
        try {
            $db = \Database::getInstance();
            $result = $db->query('SELECT VERSION() as version');
            return $result[0]['version'] ?? 'Unknown';
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
    
    private function getDiskUsage(): array {
        $bytes = disk_total_space('.');
        $free = disk_free_space('.');
        $used = $bytes - $free;
        
        return [
            'total' => $this->formatBytes($bytes),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percent_used' => round(($used / $bytes) * 100, 2)
        ];
    }
    
    private function getUptime(): string {
        if (function_exists('sys_getloadavg')) {
            $uptime_file = '/proc/uptime';
            if (file_exists($uptime_file)) {
                $uptime = file_get_contents($uptime_file);
                $uptime = floatval($uptime);
                return $this->formatUptime($uptime);
            }
        }
        return 'Unknown';
    }
    
    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    private function formatUptime(float $seconds): string {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return sprintf('%d jours, %d heures, %d minutes', $days, $hours, $minutes);
    }
}