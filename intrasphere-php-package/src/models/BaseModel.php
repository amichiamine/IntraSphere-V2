<?php
/**
 * Modèle de base pour tous les modèles
 */

abstract class BaseModel {
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Trouve un enregistrement par ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Trouve tous les enregistrements
     */
    public function findAll(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Créer un nouvel enregistrement
     */
    public function create(array $data): array {
        $data['id'] = $this->generateUUID();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        if (isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->execute($sql, array_values($data));
        
        return $this->find($data['id']);
    }
    
    /**
     * Mettre à jour un enregistrement
     */
    public function update($id, array $data): array {
        if (isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $setParts = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . 
               " WHERE {$this->primaryKey} = ?";
        
        $this->db->execute($sql, $values);
        
        return $this->find($id);
    }
    
    /**
     * Supprimer un enregistrement
     */
    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Compter les enregistrements
     */
    public function count(): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return (int) $result['count'];
    }
    
    /**
     * Recherche avec conditions
     */
    public function where(array $conditions): array {
        $whereParts = [];
        $values = [];
        
        foreach ($conditions as $column => $value) {
            $whereParts[] = "{$column} = ?";
            $values[] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $whereParts);
        return $this->db->fetchAll($sql, $values);
    }
    
    /**
     * Générer un UUID simple
     */
    protected function generateUUID(): string {
        return uniqid('', true);
    }
    
    /**
     * Valider les données avant insertion/mise à jour
     */
    protected function validate(array $data): array {
        // À surcharger dans les modèles enfants
        return [];
    }
    
    /**
     * Nettoyer les données avant insertion
     */
    protected function sanitize(array $data): array {
        $clean = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $clean[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } else {
                $clean[$key] = $value;
            }
        }
        return $clean;
    }
}
?>