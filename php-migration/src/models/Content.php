<?php
/**
 * Modèle de contenu multimédia
 */

class Content extends BaseModel {
    protected string $table = 'contents';
    
    /**
     * Créer un nouveau contenu
     */
    public function create(array $data): array {
        // Validation des données
        $this->validate($data);
        
        $data['view_count'] = 0;
        $data['rating'] = 0.0;
        $data['is_popular'] = false;
        $data['is_featured'] = false;
        
        return parent::create($data);
    }
    
    /**
     * Validation des données de contenu
     */
    public function validate(array $data): void {
        $allowedTypes = ['video', 'image', 'document', 'audio'];
        
        if (isset($data['type']) && !in_array($data['type'], $allowedTypes)) {
            throw new Exception('Type de contenu invalide');
        }
        
        if (isset($data['rating']) && ($data['rating'] < 0 || $data['rating'] > 5)) {
            throw new Exception('La note doit être entre 0 et 5');
        }
    }
    
    /**
     * Récupérer le contenu par type
     */
    public function getByType(string $type): array {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE type = ? 
            ORDER BY created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$type]);
    }
    
    /**
     * Récupérer le contenu par catégorie
     */
    public function getByCategory(string $category): array {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE category = ? 
            ORDER BY created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$category]);
    }
    
    /**
     * Récupérer le contenu populaire
     */
    public function getPopular(int $limit = 10): array {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE is_popular = TRUE
            ORDER BY view_count DESC, rating DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Récupérer le contenu à la une
     */
    public function getFeatured(int $limit = 5): array {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE is_featured = TRUE
            ORDER BY created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Récupérer le contenu récent
     */
    public function getRecent(int $days = 30, int $limit = 10): array {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ORDER BY created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$days, $limit]);
    }
    
    /**
     * Rechercher du contenu
     */
    public function search(string $query): array {
        $searchTerm = '%' . $query . '%';
        
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE title LIKE ? 
               OR description LIKE ?
               OR category LIKE ?
               OR JSON_SEARCH(tags, 'one', ?) IS NOT NULL
            ORDER BY 
                CASE 
                    WHEN title LIKE ? THEN 1
                    WHEN description LIKE ? THEN 2
                    ELSE 3
                END,
                view_count DESC
        ";
        
        return $this->db->fetchAll($sql, [
            $searchTerm, $searchTerm, $searchTerm, $query,
            $searchTerm, $searchTerm
        ]);
    }
    
    /**
     * Incrémenter le nombre de vues
     */
    public function incrementViews(string $contentId): void {
        $sql = "UPDATE {$this->table} SET view_count = view_count + 1 WHERE id = ?";
        $this->db->execute($sql, [$contentId]);
    }
    
    /**
     * Noter un contenu
     */
    public function rateContent(string $contentId, float $rating): array {
        if ($rating < 0 || $rating > 5) {
            throw new Exception('La note doit être entre 0 et 5');
        }
        
        // Ici on pourrait implémenter un système de notes multiples
        // Pour simplifier, on met à jour la note moyenne directement
        $updateData = ['rating' => $rating];
        
        return $this->update($contentId, $updateData);
    }
    
    /**
     * Marquer comme populaire
     */
    public function markAsPopular(string $contentId): array {
        return $this->update($contentId, ['is_popular' => true]);
    }
    
    /**
     * Retirer de la popularité
     */
    public function unmarkAsPopular(string $contentId): array {
        return $this->update($contentId, ['is_popular' => false]);
    }
    
    /**
     * Marquer comme à la une
     */
    public function markAsFeatured(string $contentId): array {
        return $this->update($contentId, ['is_featured' => true]);
    }
    
    /**
     * Retirer de la une
     */
    public function unmarkAsFeatured(string $contentId): array {
        return $this->update($contentId, ['is_featured' => false]);
    }
    
    /**
     * Ajouter des tags à un contenu
     */
    public function addTags(string $contentId, array $tags): array {
        $content = $this->find($contentId);
        if (!$content) {
            throw new Exception('Contenu introuvable');
        }
        
        $existingTags = json_decode($content['tags'] ?? '[]', true);
        $newTags = array_unique(array_merge($existingTags, $tags));
        
        return $this->update($contentId, ['tags' => json_encode($newTags)]);
    }
    
    /**
     * Récupérer tous les tags uniques
     */
    public function getAllTags(): array {
        $sql = "SELECT DISTINCT tags FROM {$this->table} WHERE tags IS NOT NULL";
        $results = $this->db->fetchAll($sql);
        
        $allTags = [];
        foreach ($results as $result) {
            $tags = json_decode($result['tags'], true);
            if (is_array($tags)) {
                $allTags = array_merge($allTags, $tags);
            }
        }
        
        return array_unique($allTags);
    }
    
    /**
     * Récupérer le contenu par tags
     */
    public function getByTags(array $tags): array {
        if (empty($tags)) {
            return [];
        }
        
        $conditions = [];
        $params = [];
        
        foreach ($tags as $tag) {
            $conditions[] = "JSON_SEARCH(tags, 'one', ?) IS NOT NULL";
            $params[] = $tag;
        }
        
        $whereClause = implode(' OR ', $conditions);
        
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE {$whereClause}
            ORDER BY view_count DESC, created_at DESC
        ";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Obtenir les statistiques du contenu
     */
    public function getStats(): array {
        // Compter par type
        $typeStats = $this->db->fetchAll("
            SELECT type, COUNT(*) as count 
            FROM {$this->table} 
            GROUP BY type
        ");
        
        // Compter par catégorie
        $categoryStats = $this->db->fetchAll("
            SELECT category, COUNT(*) as count 
            FROM {$this->table} 
            WHERE category IS NOT NULL AND category != ''
            GROUP BY category
            ORDER BY count DESC
        ");
        
        // Contenu le plus vu
        $mostViewed = $this->db->fetchAll("
            SELECT title, view_count 
            FROM {$this->table} 
            ORDER BY view_count DESC 
            LIMIT 5
        ");
        
        // Contenu le mieux noté
        $topRated = $this->db->fetchAll("
            SELECT title, rating 
            FROM {$this->table} 
            WHERE rating > 0
            ORDER BY rating DESC 
            LIMIT 5
        ");
        
        // Total des vues
        $totalViews = $this->db->fetchOne("
            SELECT SUM(view_count) as total 
            FROM {$this->table}
        ")['total'];
        
        return [
            'by_type' => $typeStats,
            'by_category' => $categoryStats,
            'most_viewed' => $mostViewed,
            'top_rated' => $topRated,
            'total_views' => $totalViews ?? 0
        ];
    }
    
    /**
     * Récupérer avec pagination
     */
    public function getPaginated(int $page = 1, int $limit = 20, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $whereClause = "WHERE 1=1";
        $params = [];
        
        // Appliquer les filtres
        if (!empty($filters['type'])) {
            $whereClause .= " AND type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['category'])) {
            $whereClause .= " AND category = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['is_popular'])) {
            $whereClause .= " AND is_popular = ?";
            $params[] = $filters['is_popular'];
        }
        
        if (!empty($filters['is_featured'])) {
            $whereClause .= " AND is_featured = ?";
            $params[] = $filters['is_featured'];
        }
        
        $orderBy = "ORDER BY created_at DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'views':
                    $orderBy = "ORDER BY view_count DESC";
                    break;
                case 'rating':
                    $orderBy = "ORDER BY rating DESC";
                    break;
                case 'title':
                    $orderBy = "ORDER BY title ASC";
                    break;
            }
        }
        
        $sql = "
            SELECT * FROM {$this->table} 
            {$whereClause}
            {$orderBy}
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Compter les contenus par type
     */
    public function countByType(string $type): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE type = ?";
        $result = $this->db->fetchOne($sql, [$type]);
        return (int)$result['count'];
    }
    
    /**
     * Suppression en masse
     */
    public function bulkDelete(array $contentIds): int {
        if (empty($contentIds)) {
            return 0;
        }
        
        $placeholders = str_repeat('?,', count($contentIds) - 1) . '?';
        $sql = "DELETE FROM {$this->table} WHERE id IN ($placeholders)";
        
        $this->db->execute($sql, $contentIds);
        
        return count($contentIds);
    }
}