<?php
/**
 * Modèle Document
 * Équivalent à la table 'documents' TypeScript
 */

class Document extends BaseModel {
    protected string $table = 'documents';
    
    /**
     * Créer un document
     */
    public function create(array $data): array {
        // Valeurs par défaut
        $data['version'] = $data['version'] ?? '1.0';
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return parent::create($this->sanitize($data));
    }
    
    /**
     * Obtenir les documents par catégorie
     */
    public function getByCategory(string $category): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category = ? 
                ORDER BY updated_at DESC";
        
        return $this->db->fetchAll($sql, [$category]);
    }
    
    /**
     * Obtenir les documents récents (derniers 30 jours)
     */
    public function getRecent(): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY updated_at DESC
                LIMIT 10";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Rechercher dans les documents
     */
    public function search(string $query): array {
        $searchTerm = "%{$query}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE title LIKE ? OR description LIKE ? OR file_name LIKE ?
                ORDER BY updated_at DESC";
        
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Obtenir les documents avec pagination
     */
    public function getPaginated(int $page = 1, int $limit = 20): array {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY updated_at DESC 
                LIMIT ? OFFSET ?";
        
        $documents = $this->db->fetchAll($sql, [$limit, $offset]);
        
        $totalSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $total = $this->db->fetchOne($totalSql)['total'];
        
        return [
            'data' => $documents,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];
    }
    
    /**
     * Mettre à jour la version du document
     */
    public function updateVersion(string $id, string $newVersion, array $data = []): array {
        $data['version'] = $newVersion;
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->update($id, $data);
    }
    
    /**
     * Obtenir l'historique des versions (simulation)
     */
    public function getVersionHistory(string $id): array {
        // En version simple, on retourne juste la version actuelle
        // Dans une version avancée, on aurait une table séparée pour l'historique
        $document = $this->find($id);
        if (!$document) return [];
        
        return [
            [
                'version' => $document['version'],
                'updated_at' => $document['updated_at'],
                'changes' => 'Version actuelle'
            ]
        ];
    }
    
    /**
     * Obtenir les statistiques des documents
     */
    public function getStats(): array {
        $total = $this->count();
        
        $sql = "SELECT category, COUNT(*) as count FROM {$this->table} GROUP BY category";
        $byCategory = $this->db->fetchAll($sql);
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $thisMonth = $this->db->fetchOne($sql)['count'];
        
        return [
            'total' => $total,
            'this_month' => $thisMonth,
            'by_category' => $byCategory
        ];
    }
    
    /**
     * Vérifier si un fichier existe
     */
    public function fileExists(string $fileName): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE file_name = ?";
        $result = $this->db->fetchOne($sql, [$fileName]);
        return $result['count'] > 0;
    }
    
    /**
     * Supprimer le fichier physique lors de la suppression
     */
    public function delete($id): bool {
        $document = $this->find($id);
        if (!$document) return false;
        
        // Supprimer le fichier physique
        $filePath = UPLOADS_PATH . '/documents/' . $document['file_name'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        return parent::delete($id);
    }
    
    /**
     * Suppression en masse
     */
    public function bulkDelete(array $ids): int {
        if (empty($ids)) return 0;
        
        // Récupérer les documents pour supprimer les fichiers
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT file_name FROM {$this->table} WHERE id IN ({$placeholders})";
        $documents = $this->db->fetchAll($sql, $ids);
        
        // Supprimer les fichiers physiques
        foreach ($documents as $doc) {
            $filePath = UPLOADS_PATH . '/documents/' . $doc['file_name'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        // Supprimer de la base
        $deleteSql = "DELETE FROM {$this->table} WHERE id IN ({$placeholders})";
        $stmt = $this->db->query($deleteSql, $ids);
        return $stmt->rowCount();
    }
    
    /**
     * Validation des données
     */
    protected function validate(array $data): array {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = "Le titre est requis";
        }
        
        if (empty($data['file_name'])) {
            $errors[] = "Le nom de fichier est requis";
        }
        
        if (empty($data['file_url'])) {
            $errors[] = "L'URL du fichier est requise";
        }
        
        if (empty($data['category'])) {
            $errors[] = "La catégorie est requise";
        }
        
        return $errors;
    }
}
?>