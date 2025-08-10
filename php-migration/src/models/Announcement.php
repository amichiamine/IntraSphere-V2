<?php
/**
 * Modèle Annonce
 * Équivalent à la table 'announcements' TypeScript
 */

class Announcement extends BaseModel {
    protected string $table = 'announcements';
    
    /**
     * Créer une annonce
     */
    public function create(array $data): array {
        // Valeurs par défaut
        $data['type'] = $data['type'] ?? 'info';
        $data['icon'] = $data['icon'] ?? '📢';
        $data['is_important'] = $data['is_important'] ?? false;
        
        return parent::create($this->sanitize($data));
    }
    
    /**
     * Obtenir les annonces avec informations auteur
     */
    public function findAllWithAuthor(): array {
        $sql = "SELECT a.*, u.name as author_full_name, u.avatar as author_avatar
                FROM {$this->table} a
                LEFT JOIN users u ON a.author_id = u.id
                ORDER BY a.is_important DESC, a.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les annonces importantes
     */
    public function getImportant(): array {
        $sql = "SELECT a.*, u.name as author_full_name, u.avatar as author_avatar
                FROM {$this->table} a
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.is_important = true
                ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les annonces par type
     */
    public function getByType(string $type): array {
        $sql = "SELECT a.*, u.name as author_full_name, u.avatar as author_avatar
                FROM {$this->table} a
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.type = ?
                ORDER BY a.is_important DESC, a.created_at DESC";
        
        return $this->db->fetchAll($sql, [$type]);
    }
    
    /**
     * Obtenir les annonces récentes (derniers 7 jours)
     */
    public function getRecent(): array {
        $sql = "SELECT a.*, u.name as author_full_name, u.avatar as author_avatar
                FROM {$this->table} a
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY a.is_important DESC, a.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Rechercher dans les annonces
     */
    public function search(string $query): array {
        $searchTerm = "%{$query}%";
        $sql = "SELECT a.*, u.name as author_full_name, u.avatar as author_avatar
                FROM {$this->table} a
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.title LIKE ? OR a.content LIKE ?
                ORDER BY a.is_important DESC, a.created_at DESC";
        
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }
    
    /**
     * Basculer l'importance d'une annonce
     */
    public function toggleImportance(string $id): bool {
        $announcement = $this->find($id);
        if (!$announcement) return false;
        
        $newImportance = !$announcement['is_important'];
        return $this->update($id, ['is_important' => $newImportance]);
    }
    
    /**
     * Obtenir les statistiques des annonces
     */
    public function getStats(): array {
        $total = $this->count();
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_important = true";
        $important = $this->db->fetchOne($sql)['count'];
        
        $sql = "SELECT type, COUNT(*) as count FROM {$this->table} GROUP BY type";
        $byType = $this->db->fetchAll($sql);
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $thisMonth = $this->db->fetchOne($sql)['count'];
        
        return [
            'total' => $total,
            'important' => $important,
            'this_month' => $thisMonth,
            'by_type' => $byType
        ];
    }
    
    /**
     * Suppression en masse
     */
    public function bulkDelete(array $ids): int {
        if (empty($ids)) return 0;
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM {$this->table} WHERE id IN ({$placeholders})";
        
        $stmt = $this->db->query($sql, $ids);
        return $stmt->rowCount();
    }
    
    /**
     * Validation des données
     */
    protected function validate(array $data): array {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = "Le titre est requis";
        } elseif (strlen($data['title']) > 255) {
            $errors[] = "Le titre ne peut pas dépasser 255 caractères";
        }
        
        if (empty($data['content'])) {
            $errors[] = "Le contenu est requis";
        }
        
        if (isset($data['type']) && !in_array($data['type'], array_keys(ANNOUNCEMENT_TYPES))) {
            $errors[] = "Type d'annonce invalide";
        }
        
        if (empty($data['author_name'])) {
            $errors[] = "Le nom de l'auteur est requis";
        }
        
        return $errors;
    }
}
?>