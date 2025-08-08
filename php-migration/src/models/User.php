<?php
/**
 * Modèle Utilisateur
 * Équivalent à la table 'users' TypeScript
 */

class User extends BaseModel {
    protected string $table = 'users';
    
    /**
     * Créer un utilisateur avec mot de passe hashé
     */
    public function create(array $data): array {
        // Hacher le mot de passe
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_HASH_ALGO);
        }
        
        // Valeurs par défaut
        $data['role'] = $data['role'] ?? 'employee';
        $data['is_active'] = $data['is_active'] ?? true;
        
        return parent::create($this->sanitize($data));
    }
    
    /**
     * Trouver un utilisateur par nom d'utilisateur
     */
    public function findByUsername(string $username): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $user = $this->db->fetchOne($sql, [$username]);
        return $user ?: null;
    }
    
    /**
     * Trouver un utilisateur par ID employé
     */
    public function findByEmployeeId(string $employeeId): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE employee_id = ?";
        $user = $this->db->fetchOne($sql, [$employeeId]);
        return $user ?: null;
    }
    
    /**
     * Vérifier les identifiants de connexion
     */
    public function authenticate(string $username, string $password): ?array {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            return null;
        }
        
        if (!password_verify($password, $user['password'])) {
            return null;
        }
        
        if (!$user['is_active']) {
            return null;
        }
        
        // Retourner l'utilisateur sans le mot de passe
        unset($user['password']);
        return $user;
    }
    
    /**
     * Changer le mot de passe
     */
    public function changePassword(string $userId, string $newPassword): bool {
        $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Obtenir les utilisateurs actifs pour l'annuaire
     */
    public function getDirectory(): array {
        $sql = "SELECT id, username, name, employee_id, department, position, 
                       phone, email, avatar 
                FROM {$this->table} 
                WHERE is_active = true 
                ORDER BY name ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Rechercher des utilisateurs
     */
    public function search(string $query): array {
        $searchTerm = "%{$query}%";
        $sql = "SELECT id, username, name, employee_id, department, position, 
                       phone, email, avatar 
                FROM {$this->table} 
                WHERE is_active = true 
                AND (name LIKE ? OR username LIKE ? OR employee_id LIKE ? 
                     OR department LIKE ? OR position LIKE ?)
                ORDER BY name ASC";
        
        return $this->db->fetchAll($sql, [
            $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm
        ]);
    }
    
    /**
     * Obtenir les statistiques des utilisateurs
     */
    public function getStats(): array {
        $total = $this->count();
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = true";
        $active = $this->db->fetchOne($sql)['count'];
        
        $sql = "SELECT role, COUNT(*) as count FROM {$this->table} 
                WHERE is_active = true GROUP BY role";
        $byRole = $this->db->fetchAll($sql);
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'by_role' => $byRole
        ];
    }
    
    /**
     * Activer/désactiver un utilisateur
     */
    public function toggleStatus(string $userId): bool {
        $user = $this->find($userId);
        if (!$user) return false;
        
        $newStatus = !$user['is_active'];
        return $this->update($userId, ['is_active' => $newStatus]);
    }
    
    /**
     * Mettre à jour le rôle
     */
    public function updateRole(string $userId, string $role): bool {
        if (!in_array($role, array_keys(USER_ROLES))) {
            return false;
        }
        
        return $this->update($userId, ['role' => $role]);
    }
    
    /**
     * Validation des données utilisateur avec standards de sécurité harmonisés
     */
    protected function validate(array $data): array {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis";
        } elseif (strlen($data['username']) < 3) {
            $errors[] = "Le nom d'utilisateur doit faire au moins 3 caractères";
        }
        
        if (empty($data['name'])) {
            $errors[] = "Le nom complet est requis";
        }
        
        if (isset($data['email']) && !empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide";
        }
        
        // Validation renforcée du mot de passe (harmonisée avec TypeScript)
        if (isset($data['password'])) {
            $passwordValidation = PasswordValidator::validatePasswordStrength($data['password']);
            if (!$passwordValidation['isValid']) {
                $errors = array_merge($errors, $passwordValidation['errors']);
            }
        }
        
        return $errors;
    }
}
?>