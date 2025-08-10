<?php
namespace Api;

/**
 * Contrôleur API des notifications
 * Compatible avec le système frontend 5 canaux
 */

class NotificationsController extends \BaseController {
    
    /**
     * GET /api/notifications
     */
    public function index(): void {
        $user = $this->requireAuth();
        
        $unreadOnly = $this->getQueryParam('unread_only', false);
        $page = (int) $this->getQueryParam('page', 1);
        $limit = min((int) $this->getQueryParam('limit', 20), 50);
        
        try {
            $notifications = NotificationManager::getUserNotifications($user['id'], $unreadOnly);
            $unreadCount = NotificationManager::getUnreadCount($user['id']);
            
            $this->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => count($notifications)
                ]
            ]);
            
        } catch (Exception $e) {
            Logger::error('Erreur récupération notifications', [
                'user_id' => $user['id'],
                'error' => $e->getMessage()
            ]);
            $this->error('Erreur lors de la récupération des notifications');
        }
    }
    
    /**
     * GET /api/notifications/unread-count
     */
    public function unreadCount(): void {
        $user = $this->requireAuth();
        
        try {
            $count = NotificationManager::getUnreadCount($user['id']);
            $this->json(['unread_count' => $count]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors du comptage');
        }
    }
    
    /**
     * PATCH /api/notifications/:id/read
     */
    public function markAsRead(string $id): void {
        $user = $this->requireAuth();
        
        try {
            $success = NotificationManager::markAsRead($id, $user['id']);
            
            if ($success) {
                $this->json(['message' => 'Notification marquée comme lue']);
            } else {
                $this->error('Notification introuvable', 404);
            }
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour');
        }
    }
    
    /**
     * POST /api/notifications/mark-all-read
     */
    public function markAllAsRead(): void {
        $user = $this->requireAuth();
        
        try {
            $success = NotificationManager::markAllAsRead($user['id']);
            $this->json(['message' => 'Toutes les notifications marquées comme lues']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour');
        }
    }
    
    /**
     * GET /api/notifications/stream (Server-Sent Events)
     */
    public function stream(): void {
        $user = $this->requireAuth();
        
        // Configuration pour Server-Sent Events
        set_time_limit(0);
        ignore_user_abort(false);
        
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        
        // Stream des événements
        NotificationManager::streamEvents($user['id']);
    }
    
    /**
     * POST /api/notifications/test (pour développement)
     */
    public function test(): void {
        if (APP_ENV !== 'development') {
            $this->error('Endpoint disponible uniquement en développement', 403);
        }
        
        $user = $this->requireAuth();
        $data = $this->getJsonInput();
        
        $success = NotificationManager::sendSystemNotification(
            $data['type'] ?? 'test',
            $user['id'],
            $data['data'] ?? ['message' => 'Test notification']
        );
        
        if ($success) {
            $this->json(['message' => 'Notification de test envoyée']);
        } else {
            $this->error('Erreur lors de l\'envoi de la notification');
        }
    }
}
?>