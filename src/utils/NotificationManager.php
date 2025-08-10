<?php
/**
 * Gestionnaire unifié des notifications
 * Compatible avec le système frontend 5 canaux
 */

class NotificationManager {
    private static Database $db;
    private static array $channels = ['browser', 'email', 'sms', 'in_app', 'digest'];
    
    public static function init(): void {
        self::$db = Database::getInstance();
        self::createNotificationsTable();
    }
    
    /**
     * Envoyer une notification multi-canal
     */
    public static function send(array $data): bool {
        $notification = [
            'id' => uniqid('notif_', true),
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'] ?? 'info',
            'channels' => json_encode($data['channels'] ?? ['in_app']),
            'data' => json_encode($data['data'] ?? []),
            'is_read' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            // Insérer en base
            $sql = "INSERT INTO notifications (" . implode(', ', array_keys($notification)) . ") 
                    VALUES (" . str_repeat('?,', count($notification) - 1) . "?)";
            self::$db->execute($sql, array_values($notification));
            
            // Envoyer selon les canaux
            foreach ($data['channels'] ?? ['in_app'] as $channel) {
                self::sendToChannel($channel, $notification, $data);
            }
            
            return true;
        } catch (Exception $e) {
            Logger::error('Erreur envoi notification', ['error' => $e->getMessage(), 'data' => $data]);
            return false;
        }
    }
    
    /**
     * Récupérer les notifications d'un utilisateur
     */
    public static function getUserNotifications(string $userId, bool $unreadOnly = false): array {
        $sql = "SELECT * FROM notifications WHERE user_id = ?";
        $params = [$userId];
        
        if ($unreadOnly) {
            $sql .= " AND is_read = FALSE";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT 50";
        
        $notifications = self::$db->fetchAll($sql, $params);
        
        // Décoder les JSON
        foreach ($notifications as &$notif) {
            $notif['channels'] = json_decode($notif['channels'], true);
            $notif['data'] = json_decode($notif['data'], true);
        }
        
        return $notifications;
    }
    
    /**
     * Marquer comme lue
     */
    public static function markAsRead(string $notificationId, string $userId): bool {
        $sql = "UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?";
        return self::$db->execute($sql, [$notificationId, $userId]);
    }
    
    /**
     * Marquer toutes comme lues
     */
    public static function markAllAsRead(string $userId): bool {
        $sql = "UPDATE notifications SET is_read = TRUE WHERE user_id = ? AND is_read = FALSE";
        return self::$db->execute($sql, [$userId]);
    }
    
    /**
     * Compter les non lues
     */
    public static function getUnreadCount(string $userId): int {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE";
        $result = self::$db->fetchOne($sql, [$userId]);
        return (int) $result['count'];
    }
    
    /**
     * Envoyer vers un canal spécifique
     */
    private static function sendToChannel(string $channel, array $notification, array $data): void {
        switch ($channel) {
            case 'browser':
                self::sendBrowserNotification($notification, $data);
                break;
            case 'email':
                self::sendEmailNotification($notification, $data);
                break;
            case 'in_app':
                // Déjà en base, rien à faire
                break;
            case 'digest':
                self::addToDigest($notification, $data);
                break;
        }
    }
    
    /**
     * Notification browser (Web Push)
     */
    private static function sendBrowserNotification(array $notification, array $data): void {
        // Stocker pour Server-Sent Events
        $sse_file = ROOT_PATH . '/tmp/sse_' . $notification['user_id'] . '.json';
        $sse_data = [
            'type' => 'notification',
            'data' => $notification,
            'timestamp' => time()
        ];
        
        if (!file_exists(dirname($sse_file))) {
            mkdir(dirname($sse_file), 0755, true);
        }
        
        file_put_contents($sse_file, json_encode($sse_data));
    }
    
    /**
     * Notification email
     */
    private static function sendEmailNotification(array $notification, array $data): void {
        if (empty($data['user_email'])) return;
        
        // Configuration email simple
        $subject = "[IntraSphere] " . $notification['title'];
        $message = $notification['message'];
        $headers = [
            'From: ' . MAIL_FROM,
            'Reply-To: ' . MAIL_FROM,
            'Content-Type: text/html; charset=UTF-8'
        ];
        
        mail($data['user_email'], $subject, $message, implode("\r\n", $headers));
    }
    
    /**
     * Ajouter au digest quotidien
     */
    private static function addToDigest(array $notification, array $data): void {
        $digest_file = ROOT_PATH . '/tmp/digest_' . date('Y-m-d') . '.json';
        $digest_data = [];
        
        if (file_exists($digest_file)) {
            $digest_data = json_decode(file_get_contents($digest_file), true) ?? [];
        }
        
        $digest_data[] = [
            'user_id' => $notification['user_id'],
            'notification' => $notification,
            'timestamp' => time()
        ];
        
        if (!file_exists(dirname($digest_file))) {
            mkdir(dirname($digest_file), 0755, true);
        }
        
        file_put_contents($digest_file, json_encode($digest_data));
    }
    
    /**
     * Créer la table des notifications
     */
    private static function createNotificationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS notifications (
            id VARCHAR(50) PRIMARY KEY,
            user_id VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
            channels JSON,
            data JSON,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_is_read (is_read),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        
        try {
            self::$db->execute($sql);
        } catch (Exception $e) {
            Logger::error('Erreur création table notifications', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * API Server-Sent Events pour temps réel
     */
    public static function streamEvents(string $userId): void {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        
        $sse_file = ROOT_PATH . '/tmp/sse_' . $userId . '.json';
        
        while (true) {
            if (file_exists($sse_file)) {
                $data = json_decode(file_get_contents($sse_file), true);
                if ($data && $data['timestamp'] > (time() - 30)) {
                    echo "data: " . json_encode($data) . "\n\n";
                    unlink($sse_file); // Consommer le message
                    flush();
                }
            }
            
            // Heartbeat
            echo "data: " . json_encode(['type' => 'heartbeat', 'timestamp' => time()]) . "\n\n";
            flush();
            
            sleep(5);
            
            // Vérifier si la connexion est fermée
            if (connection_aborted()) break;
        }
    }
    
    /**
     * Notifications prédéfinies du système
     */
    public static function sendSystemNotification(string $type, string $userId, array $data = []): bool {
        $templates = [
            'new_announcement' => [
                'title' => 'Nouvelle annonce',
                'message' => 'Une nouvelle annonce a été publiée: ' . ($data['title'] ?? ''),
                'channels' => ['in_app', 'browser']
            ],
            'new_message' => [
                'title' => 'Nouveau message',
                'message' => 'Vous avez reçu un nouveau message de ' . ($data['sender_name'] ?? 'un utilisateur'),
                'channels' => ['in_app', 'browser', 'email']
            ],
            'training_reminder' => [
                'title' => 'Rappel formation',
                'message' => 'N\'oubliez pas votre formation: ' . ($data['training_title'] ?? ''),
                'channels' => ['in_app', 'email']
            ],
            'document_updated' => [
                'title' => 'Document mis à jour',
                'message' => 'Le document ' . ($data['document_title'] ?? '') . ' a été mis à jour',
                'channels' => ['in_app']
            ],
            'complaint_assigned' => [
                'title' => 'Réclamation assignée',
                'message' => 'Une réclamation vous a été assignée: ' . ($data['complaint_title'] ?? ''),
                'channels' => ['in_app', 'email']
            ]
        ];
        
        if (!isset($templates[$type])) {
            return false;
        }
        
        $notification = array_merge($templates[$type], [
            'user_id' => $userId,
            'type' => $type,
            'data' => $data
        ]);
        
        return self::send($notification);
    }
}

// Initialiser automatiquement
NotificationManager::init();
?>