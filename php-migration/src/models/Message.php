<?php
/**
 * Modèle Message
 * Équivalent à la table 'messages' TypeScript
 */

class Message extends BaseModel {
    protected string $table = 'messages';
    
    /**
     * Créer un message
     */
    public function create(array $data): array {
        $data['is_read'] = false; // Nouveau message non lu
        
        return parent::create($this->sanitize($data));
    }
    
    /**
     * Obtenir les messages d'un utilisateur (boîte de réception)
     */
    public function getInbox(string $userId): array {
        $sql = "SELECT m.*, 
                       us.name as sender_name, us.avatar as sender_avatar,
                       ur.name as recipient_name, ur.avatar as recipient_avatar
                FROM {$this->table} m
                LEFT JOIN users us ON m.sender_id = us.id
                LEFT JOIN users ur ON m.recipient_id = ur.id
                WHERE m.recipient_id = ?
                ORDER BY m.created_at DESC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    /**
     * Obtenir les messages envoyés par un utilisateur
     */
    public function getSent(string $userId): array {
        $sql = "SELECT m.*, 
                       us.name as sender_name, us.avatar as sender_avatar,
                       ur.name as recipient_name, ur.avatar as recipient_avatar
                FROM {$this->table} m
                LEFT JOIN users us ON m.sender_id = us.id
                LEFT JOIN users ur ON m.recipient_id = ur.id
                WHERE m.sender_id = ?
                ORDER BY m.created_at DESC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    /**
     * Obtenir un message avec les informations complètes
     */
    public function findWithUsers(string $messageId): ?array {
        $sql = "SELECT m.*, 
                       us.name as sender_name, us.avatar as sender_avatar,
                       ur.name as recipient_name, ur.avatar as recipient_avatar
                FROM {$this->table} m
                LEFT JOIN users us ON m.sender_id = us.id
                LEFT JOIN users ur ON m.recipient_id = ur.id
                WHERE m.id = ?";
        
        $message = $this->db->fetchOne($sql, [$messageId]);
        return $message ?: null;
    }
    
    /**
     * Marquer un message comme lu
     */
    public function markAsRead(string $messageId): bool {
        return $this->update($messageId, ['is_read' => true]);
    }
    
    /**
     * Marquer plusieurs messages comme lus
     */
    public function markMultipleAsRead(array $messageIds): int {
        if (empty($messageIds)) return 0;
        
        $placeholders = implode(',', array_fill(0, count($messageIds), '?'));
        $sql = "UPDATE {$this->table} SET is_read = true WHERE id IN ({$placeholders})";
        
        $stmt = $this->db->query($sql, $messageIds);
        return $stmt->rowCount();
    }
    
    /**
     * Obtenir le nombre de messages non lus
     */
    public function getUnreadCount(string $userId): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE recipient_id = ? AND is_read = false";
        
        $result = $this->db->fetchOne($sql, [$userId]);
        return (int) $result['count'];
    }
    
    /**
     * Obtenir la conversation entre deux utilisateurs
     */
    public function getConversation(string $user1Id, string $user2Id): array {
        $sql = "SELECT m.*, 
                       us.name as sender_name, us.avatar as sender_avatar,
                       ur.name as recipient_name, ur.avatar as recipient_avatar
                FROM {$this->table} m
                LEFT JOIN users us ON m.sender_id = us.id
                LEFT JOIN users ur ON m.recipient_id = ur.id
                WHERE (m.sender_id = ? AND m.recipient_id = ?) 
                   OR (m.sender_id = ? AND m.recipient_id = ?)
                ORDER BY m.created_at ASC";
        
        return $this->db->fetchAll($sql, [$user1Id, $user2Id, $user2Id, $user1Id]);
    }
    
    /**
     * Rechercher dans les messages
     */
    public function search(string $userId, string $query): array {
        $searchTerm = "%{$query}%";
        $sql = "SELECT m.*, 
                       us.name as sender_name, us.avatar as sender_avatar,
                       ur.name as recipient_name, ur.avatar as recipient_avatar
                FROM {$this->table} m
                LEFT JOIN users us ON m.sender_id = us.id
                LEFT JOIN users ur ON m.recipient_id = ur.id
                WHERE (m.sender_id = ? OR m.recipient_id = ?)
                AND (m.subject LIKE ? OR m.content LIKE ?)
                ORDER BY m.created_at DESC";
        
        return $this->db->fetchAll($sql, [$userId, $userId, $searchTerm, $searchTerm]);
    }
    
    /**
     * Obtenir les conversations récentes d'un utilisateur
     */
    public function getRecentConversations(string $userId, int $limit = 10): array {
        // Récupérer les derniers messages de chaque conversation
        $sql = "SELECT DISTINCT
                    CASE 
                        WHEN m.sender_id = ? THEN m.recipient_id 
                        ELSE m.sender_id 
                    END as contact_id,
                    u.name as contact_name,
                    u.avatar as contact_avatar,
                    (SELECT m2.subject FROM {$this->table} m2 
                     WHERE (m2.sender_id = ? AND m2.recipient_id = contact_id) 
                        OR (m2.sender_id = contact_id AND m2.recipient_id = ?)
                     ORDER BY m2.created_at DESC LIMIT 1) as last_subject,
                    (SELECT m2.created_at FROM {$this->table} m2 
                     WHERE (m2.sender_id = ? AND m2.recipient_id = contact_id) 
                        OR (m2.sender_id = contact_id AND m2.recipient_id = ?)
                     ORDER BY m2.created_at DESC LIMIT 1) as last_message_date
                FROM {$this->table} m
                LEFT JOIN users u ON (CASE WHEN m.sender_id = ? THEN m.recipient_id ELSE m.sender_id END) = u.id
                WHERE m.sender_id = ? OR m.recipient_id = ?
                ORDER BY last_message_date DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [
            $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId, $limit
        ]);
    }
    
    /**
     * Supprimer les messages d'une conversation
     */
    public function deleteConversation(string $user1Id, string $user2Id): int {
        $sql = "DELETE FROM {$this->table} 
                WHERE (sender_id = ? AND recipient_id = ?) 
                   OR (sender_id = ? AND recipient_id = ?)";
        
        $stmt = $this->db->query($sql, [$user1Id, $user2Id, $user2Id, $user1Id]);
        return $stmt->rowCount();
    }
    
    /**
     * Obtenir les statistiques des messages
     */
    public function getStats(): array {
        $total = $this->count();
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_read = false";
        $unread = $this->db->fetchOne($sql)['count'];
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $today = $this->db->fetchOne($sql)['count'];
        
        return [
            'total' => $total,
            'unread' => $unread,
            'today' => $today
        ];
    }
    
    /**
     * Validation des données
     */
    protected function validate(array $data): array {
        $errors = [];
        
        if (empty($data['sender_id'])) {
            $errors[] = "L'expéditeur est requis";
        }
        
        if (empty($data['recipient_id'])) {
            $errors[] = "Le destinataire est requis";
        }
        
        if (empty($data['subject'])) {
            $errors[] = "Le sujet est requis";
        }
        
        if (empty($data['content'])) {
            $errors[] = "Le contenu est requis";
        }
        
        if (isset($data['sender_id'], $data['recipient_id']) && 
            $data['sender_id'] === $data['recipient_id']) {
            $errors[] = "Impossible d'envoyer un message à soi-même";
        }
        
        return $errors;
    }
}
?>