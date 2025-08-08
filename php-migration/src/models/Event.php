<?php
/**
 * Modèle Événement
 * Équivalent à la table 'events' TypeScript
 */

class Event extends BaseModel {
    protected string $table = 'events';
    
    /**
     * Créer un événement
     */
    public function create(array $data): array {
        $data['type'] = $data['type'] ?? 'meeting';
        
        return parent::create($this->sanitize($data));
    }
    
    /**
     * Obtenir les événements avec informations organisateur
     */
    public function findAllWithOrganizer(): array {
        $sql = "SELECT e.*, u.name as organizer_name, u.avatar as organizer_avatar
                FROM {$this->table} e
                LEFT JOIN users u ON e.organizer_id = u.id
                ORDER BY e.date ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les événements à venir
     */
    public function getUpcoming(): array {
        $sql = "SELECT e.*, u.name as organizer_name, u.avatar as organizer_avatar
                FROM {$this->table} e
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.date >= NOW()
                ORDER BY e.date ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les événements par type
     */
    public function getByType(string $type): array {
        $sql = "SELECT e.*, u.name as organizer_name, u.avatar as organizer_avatar
                FROM {$this->table} e
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.type = ?
                ORDER BY e.date ASC";
        
        return $this->db->fetchAll($sql, [$type]);
    }
    
    /**
     * Obtenir les événements d'un utilisateur
     */
    public function getMyEvents(string $userId): array {
        $sql = "SELECT e.*, u.name as organizer_name, u.avatar as organizer_avatar
                FROM {$this->table} e
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.organizer_id = ?
                ORDER BY e.date ASC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    /**
     * Obtenir les événements pour le calendrier
     */
    public function getCalendarEvents(string $month = null, string $year = null): array {
        $currentMonth = $month ?? date('m');
        $currentYear = $year ?? date('Y');
        
        $sql = "SELECT e.*, u.name as organizer_name
                FROM {$this->table} e
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE MONTH(e.date) = ? AND YEAR(e.date) = ?
                ORDER BY e.date ASC";
        
        return $this->db->fetchAll($sql, [$currentMonth, $currentYear]);
    }
    
    /**
     * Rechercher des événements
     */
    public function search(string $query): array {
        $searchTerm = "%{$query}%";
        $sql = "SELECT e.*, u.name as organizer_name, u.avatar as organizer_avatar
                FROM {$this->table} e
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.title LIKE ? OR e.description LIKE ? OR e.location LIKE ?
                ORDER BY e.date ASC";
        
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Obtenir les statistiques des événements
     */
    public function getStats(): array {
        $total = $this->count();
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE date >= NOW()";
        $upcoming = $this->db->fetchOne($sql)['count'];
        
        $sql = "SELECT type, COUNT(*) as count FROM {$this->table} GROUP BY type";
        $byType = $this->db->fetchAll($sql);
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE MONTH(date) = MONTH(NOW()) AND YEAR(date) = YEAR(NOW())";
        $thisMonth = $this->db->fetchOne($sql)['count'];
        
        return [
            'total' => $total,
            'upcoming' => $upcoming,
            'this_month' => $thisMonth,
            'by_type' => $byType
        ];
    }
    
    /**
     * Validation des données
     */
    protected function validate(array $data): array {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = "Le titre est requis";
        }
        
        if (empty($data['date'])) {
            $errors[] = "La date est requise";
        } elseif (!strtotime($data['date'])) {
            $errors[] = "Format de date invalide";
        }
        
        if (empty($data['organizer_id'])) {
            $errors[] = "L'organisateur est requis";
        }
        
        if (isset($data['type']) && !in_array($data['type'], ['meeting', 'training', 'social', 'other'])) {
            $errors[] = "Type d'événement invalide";
        }
        
        return $errors;
    }
}
?>