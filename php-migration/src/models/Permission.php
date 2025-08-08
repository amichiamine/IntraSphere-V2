<?php
/**
 * Modèle Permission
 * Équivalent à la table 'permissions' TypeScript
 */

class Permission extends BaseModel {
    protected string $table = 'permissions';
    
    /**
     * Vérifier si un utilisateur a une permission
     */
    public function hasPermission(string $userId, string $permission): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = ? AND permission = ?";
        
        $result = $this->db->fetchOne($sql, [$userId, $permission]);
        return $result['count'] > 0;
    }
    
    /**
     * Obtenir toutes les permissions d'un utilisateur
     */
    public function getUserPermissions(string $userId): array {
        $sql = "SELECT p.*, u.name as granted_by_name
                FROM {$this->table} p
                LEFT JOIN users u ON p.granted_by = u.id
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    /**
     * Accorder une permission à un utilisateur
     */
    public function grantPermission(string $userId, string $permission, string $grantedBy): array {
        // Vérifier si la permission existe déjà
        if ($this->hasPermission($userId, $permission)) {
            throw new Exception("L'utilisateur a déjà cette permission");
        }
        
        return $this->create([
            'user_id' => $userId,
            'permission' => $permission,
            'granted_by' => $grantedBy
        ]);
    }
    
    /**
     * Révoquer une permission
     */
    public function revokePermission(string $userId, string $permission): bool {
        $sql = "DELETE FROM {$this->table} 
                WHERE user_id = ? AND permission = ?";
        
        return $this->db->execute($sql, [$userId, $permission]);
    }
    
    /**
     * Obtenir toutes les permissions avec les utilisateurs
     */
    public function getAllWithUsers(): array {
        $sql = "SELECT p.*, 
                       u.name as user_name, u.username,
                       ug.name as granted_by_name
                FROM {$this->table} p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN users ug ON p.granted_by = ug.id
                ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les utilisateurs avec leurs permissions
     */
    public function getUsersWithPermissions(): array {
        $sql = "SELECT u.id, u.name, u.username, u.role,
                       GROUP_CONCAT(p.permission) as permissions
                FROM users u
                LEFT JOIN {$this->table} p ON u.id = p.user_id
                WHERE u.is_active = true
                GROUP BY u.id, u.name, u.username, u.role
                ORDER BY u.name ASC";
        
        $users = $this->db->fetchAll($sql);
        
        // Convertir les permissions en tableau
        foreach ($users as &$user) {
            $user['permissions'] = $user['permissions'] ? 
                explode(',', $user['permissions']) : [];
        }
        
        return $users;
    }
    
    /**
     * Synchroniser les permissions d'un utilisateur
     */
    public function syncUserPermissions(string $userId, array $permissions, string $grantedBy): bool {
        try {
            $this->db->getConnection()->beginTransaction();
            
            // Supprimer toutes les permissions existantes
            $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
            $this->db->execute($sql, [$userId]);
            
            // Ajouter les nouvelles permissions
            foreach ($permissions as $permission) {
                $this->create([
                    'user_id' => $userId,
                    'permission' => $permission,
                    'granted_by' => $grantedBy
                ]);
            }
            
            $this->db->getConnection()->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            return false;
        }
    }
    
    /**
     * Obtenir les statistiques des permissions
     */
    public function getStats(): array {
        $sql = "SELECT permission, COUNT(*) as count 
                FROM {$this->table} 
                GROUP BY permission 
                ORDER BY count DESC";
        
        $byPermission = $this->db->fetchAll($sql);
        
        $sql = "SELECT COUNT(DISTINCT user_id) as count FROM {$this->table}";
        $usersWithPermissions = $this->db->fetchOne($sql)['count'];
        
        $total = $this->count();
        
        return [
            'total' => $total,
            'users_with_permissions' => $usersWithPermissions,
            'by_permission' => $byPermission
        ];
    }
    
    /**
     * Vérifier si un utilisateur peut accorder une permission
     */
    public function canGrantPermission(string $userId, string $permission): bool {
        // Récupérer l'utilisateur
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) return false;
        
        // Les admins peuvent tout faire
        if ($user['role'] === 'admin') return true;
        
        // Les modérateurs peuvent accorder certaines permissions
        if ($user['role'] === 'moderator') {
            $moderatorPermissions = [
                'validate_topics',
                'validate_posts'
            ];
            return in_array($permission, $moderatorPermissions);
        }
        
        return false;
    }
    
    /**
     * Obtenir les permissions disponibles pour un rôle
     */
    public function getAvailablePermissions(string $role): array {
        $allPermissions = PERMISSIONS;
        
        switch ($role) {
            case 'admin':
                return $allPermissions;
                
            case 'moderator':
                return array_filter($allPermissions, function($key) {
                    $moderatorAllowed = [
                        'validate_topics',
                        'validate_posts',
                        'manage_announcements'
                    ];
                    return in_array($key, $moderatorAllowed);
                }, ARRAY_FILTER_USE_KEY);
                
            default:
                return [];
        }
    }
    
    /**
     * Validation des données
     */
    protected function validate(array $data): array {
        $errors = [];
        
        if (empty($data['user_id'])) {
            $errors[] = "L'utilisateur est requis";
        }
        
        if (empty($data['permission'])) {
            $errors[] = "La permission est requise";
        } elseif (!array_key_exists($data['permission'], PERMISSIONS)) {
            $errors[] = "Permission invalide";
        }
        
        if (empty($data['granted_by'])) {
            $errors[] = "L'utilisateur qui accorde la permission est requis";
        }
        
        return $errors;
    }
}
?>