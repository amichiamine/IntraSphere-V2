<?php
/**
 * Modèle des réclamations/plaintes
 */

class Complaint extends BaseModel {
    protected string $table = 'complaints';
    
    /**
     * Créer une nouvelle réclamation
     */
    public function create(array $data): array {
        // Validation des données
        $this->validate($data);
        
        $data['status'] = $data['status'] ?? 'open';
        $data['priority'] = $data['priority'] ?? 'medium';
        
        return parent::create($data);
    }
    
    /**
     * Validation des données de réclamation
     */
    public function validate(array $data): void {
        $allowedStatuses = ['open', 'in_progress', 'resolved', 'closed'];
        $allowedPriorities = ['low', 'medium', 'high', 'urgent'];
        
        if (isset($data['status']) && !in_array($data['status'], $allowedStatuses)) {
            throw new Exception('Statut de réclamation invalide');
        }
        
        if (isset($data['priority']) && !in_array($data['priority'], $allowedPriorities)) {
            throw new Exception('Priorité de réclamation invalide');
        }
    }
    
    /**
     * Récupérer les réclamations avec informations des utilisateurs
     */
    public function findAllWithUsers(): array {
        $sql = "
            SELECT c.*, 
                   submitter.name as submitter_name,
                   submitter.email as submitter_email,
                   assignee.name as assignee_name,
                   assignee.email as assignee_email
            FROM {$this->table} c
            LEFT JOIN users submitter ON c.submitter_id = submitter.id
            LEFT JOIN users assignee ON c.assigned_to_id = assignee.id
            ORDER BY 
                CASE c.priority 
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    ELSE 4
                END,
                c.created_at DESC
        ";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Récupérer les réclamations par statut
     */
    public function getByStatus(string $status): array {
        $sql = "
            SELECT c.*, 
                   submitter.name as submitter_name,
                   assignee.name as assignee_name
            FROM {$this->table} c
            LEFT JOIN users submitter ON c.submitter_id = submitter.id
            LEFT JOIN users assignee ON c.assigned_to_id = assignee.id
            WHERE c.status = ?
            ORDER BY 
                CASE c.priority 
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    ELSE 4
                END,
                c.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$status]);
    }
    
    /**
     * Récupérer les réclamations par priorité
     */
    public function getByPriority(string $priority): array {
        $sql = "
            SELECT c.*, 
                   submitter.name as submitter_name,
                   assignee.name as assignee_name
            FROM {$this->table} c
            LEFT JOIN users submitter ON c.submitter_id = submitter.id
            LEFT JOIN users assignee ON c.assigned_to_id = assignee.id
            WHERE c.priority = ?
            ORDER BY c.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$priority]);
    }
    
    /**
     * Récupérer les réclamations d'un utilisateur
     */
    public function getBySubmitter(string $submitterId): array {
        $sql = "
            SELECT c.*, 
                   assignee.name as assignee_name
            FROM {$this->table} c
            LEFT JOIN users assignee ON c.assigned_to_id = assignee.id
            WHERE c.submitter_id = ?
            ORDER BY c.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$submitterId]);
    }
    
    /**
     * Récupérer les réclamations assignées à un utilisateur
     */
    public function getAssignedTo(string $assigneeId): array {
        $sql = "
            SELECT c.*, 
                   submitter.name as submitter_name,
                   submitter.email as submitter_email
            FROM {$this->table} c
            LEFT JOIN users submitter ON c.submitter_id = submitter.id
            WHERE c.assigned_to_id = ?
            ORDER BY 
                CASE c.priority 
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    ELSE 4
                END,
                c.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$assigneeId]);
    }
    
    /**
     * Assigner une réclamation à un utilisateur
     */
    public function assignTo(string $complaintId, string $assigneeId): array {
        $updateData = [
            'assigned_to_id' => $assigneeId,
            'status' => 'in_progress'
        ];
        
        return $this->update($complaintId, $updateData);
    }
    
    /**
     * Changer le statut d'une réclamation
     */
    public function changeStatus(string $complaintId, string $newStatus): array {
        $this->validate(['status' => $newStatus]);
        
        return $this->update($complaintId, ['status' => $newStatus]);
    }
    
    /**
     * Changer la priorité d'une réclamation
     */
    public function changePriority(string $complaintId, string $newPriority): array {
        $this->validate(['priority' => $newPriority]);
        
        return $this->update($complaintId, ['priority' => $newPriority]);
    }
    
    /**
     * Rechercher des réclamations
     */
    public function search(string $query): array {
        $searchTerm = '%' . $query . '%';
        
        $sql = "
            SELECT c.*, 
                   submitter.name as submitter_name,
                   assignee.name as assignee_name
            FROM {$this->table} c
            LEFT JOIN users submitter ON c.submitter_id = submitter.id
            LEFT JOIN users assignee ON c.assigned_to_id = assignee.id
            WHERE c.title LIKE ? 
               OR c.description LIKE ?
               OR c.category LIKE ?
               OR submitter.name LIKE ?
            ORDER BY c.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Obtenir les statistiques des réclamations
     */
    public function getStats(): array {
        // Compter par statut
        $statusStats = $this->db->fetchAll("
            SELECT status, COUNT(*) as count 
            FROM {$this->table} 
            GROUP BY status
        ");
        
        // Compter par priorité
        $priorityStats = $this->db->fetchAll("
            SELECT priority, COUNT(*) as count 
            FROM {$this->table} 
            GROUP BY priority
        ");
        
        // Compter par catégorie
        $categoryStats = $this->db->fetchAll("
            SELECT category, COUNT(*) as count 
            FROM {$this->table} 
            WHERE category IS NOT NULL AND category != ''
            GROUP BY category
            ORDER BY count DESC
        ");
        
        // Réclamations récentes (7 derniers jours)
        $recentCount = $this->db->fetchOne("
            SELECT COUNT(*) as count 
            FROM {$this->table} 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ")['count'];
        
        // Temps de résolution moyen
        $avgResolutionTime = $this->db->fetchOne("
            SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours
            FROM {$this->table} 
            WHERE status = 'resolved'
        ");
        
        return [
            'by_status' => $statusStats,
            'by_priority' => $priorityStats,
            'by_category' => $categoryStats,
            'recent_count' => $recentCount,
            'avg_resolution_time_hours' => round($avgResolutionTime['avg_hours'] ?? 0, 2)
        ];
    }
    
    /**
     * Récupérer les réclamations récentes
     */
    public function getRecent(int $days = 7, int $limit = 10): array {
        $sql = "
            SELECT c.*, 
                   submitter.name as submitter_name,
                   assignee.name as assignee_name
            FROM {$this->table} c
            LEFT JOIN users submitter ON c.submitter_id = submitter.id
            LEFT JOIN users assignee ON c.assigned_to_id = assignee.id
            WHERE c.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ORDER BY c.created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$days, $limit]);
    }
    
    /**
     * Compter par statut
     */
    public function countByStatus(string $status): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = ?";
        $result = $this->db->fetchOne($sql, [$status]);
        return (int)$result['count'];
    }
    
    /**
     * Compter les réclamations urgentes
     */
    public function countUrgent(): int {
        return $this->countByPriority('urgent') + $this->countByPriority('high');
    }
    
    /**
     * Compter par priorité
     */
    public function countByPriority(string $priority): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE priority = ?";
        $result = $this->db->fetchOne($sql, [$priority]);
        return (int)$result['count'];
    }
    
    /**
     * Récupérer les réclamations avec pagination
     */
    public function getPaginated(int $page = 1, int $limit = 20, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $whereClause = "WHERE 1=1";
        $params = [];
        
        // Appliquer les filtres
        if (!empty($filters['status'])) {
            $whereClause .= " AND c.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['priority'])) {
            $whereClause .= " AND c.priority = ?";
            $params[] = $filters['priority'];
        }
        
        if (!empty($filters['category'])) {
            $whereClause .= " AND c.category = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['assignee_id'])) {
            $whereClause .= " AND c.assigned_to_id = ?";
            $params[] = $filters['assignee_id'];
        }
        
        $sql = "
            SELECT c.*, 
                   submitter.name as submitter_name,
                   assignee.name as assignee_name
            FROM {$this->table} c
            LEFT JOIN users submitter ON c.submitter_id = submitter.id
            LEFT JOIN users assignee ON c.assigned_to_id = assignee.id
            {$whereClause}
            ORDER BY 
                CASE c.priority 
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    ELSE 4
                END,
                c.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Suppression en masse
     */
    public function bulkDelete(array $complaintIds): int {
        if (empty($complaintIds)) {
            return 0;
        }
        
        $placeholders = str_repeat('?,', count($complaintIds) - 1) . '?';
        $sql = "DELETE FROM {$this->table} WHERE id IN ($placeholders)";
        
        $this->db->execute($sql, $complaintIds);
        
        return count($complaintIds);
    }
}