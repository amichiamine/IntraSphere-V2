<?php
/**
 * Gestionnaire de permissions unifié
 * Compatible avec le système TypeScript shared/schema.ts
 */

class PermissionManager {
    
    /**
     * Permissions système définies (harmonisées avec TypeScript)
     */
    private static array $systemPermissions = [
        'manage_announcements' => 'Gérer les annonces',
        'manage_documents' => 'Gérer les documents',
        'manage_events' => 'Gérer les événements',
        'manage_users' => 'Gérer les utilisateurs',
        'manage_trainings' => 'Gérer les formations',
        'validate_topics' => 'Valider les sujets',
        'validate_posts' => 'Valider les posts',
        'manage_employee_categories' => 'Gérer les catégories d\'employés',
        'manage_content' => 'Gérer le contenu multimédia',
        'manage_complaints' => 'Gérer les réclamations',
        'view_admin_stats' => 'Voir les statistiques d\'administration',
        'manage_permissions' => 'Gérer les permissions utilisateur',
        'system_configuration' => 'Configuration système'
    ];
    
    /**
     * Hiérarchie des rôles (harmonisée avec BaseController)
     */
    private static array $roleHierarchy = [
        'employee' => 1,
        'moderator' => 2,
        'admin' => 3
    ];
    
    /**
     * Permissions par défaut par rôle
     */
    private static array $defaultRolePermissions = [
        'employee' => [],
        'moderator' => [
            'manage_announcements',
            'manage_documents',
            'manage_events',
            'manage_trainings',
            'validate_topics',
            'validate_posts',
            'manage_complaints'
        ],
        'admin' => 'ALL' // Admin a toutes les permissions
    ];
    
    /**
     * Vérifier si un utilisateur a une permission spécifique
     */
    public static function hasPermission(array $user, string $permission): bool {
        // Admin a toutes les permissions
        if ($user['role'] === 'admin') {
            return true;
        }
        
        // Vérifier les permissions par défaut du rôle
        $rolePermissions = self::$defaultRolePermissions[$user['role']] ?? [];
        if (is_array($rolePermissions) && in_array($permission, $rolePermissions)) {
            return true;
        }
        
        // Vérifier les permissions individuelles (base de données)
        $permissionModel = new Permission();
        return $permissionModel->hasPermission($user['id'], $permission);
    }
    
    /**
     * Vérifier si un utilisateur a le rôle minimum requis
     */
    public static function hasRole(array $user, string $requiredRole): bool {
        $userLevel = self::$roleHierarchy[$user['role']] ?? 0;
        $requiredLevel = self::$roleHierarchy[$requiredRole] ?? 999;
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * Obtenir toutes les permissions disponibles
     */
    public static function getAllPermissions(): array {
        return self::$systemPermissions;
    }
    
    /**
     * Obtenir les permissions d'un utilisateur
     */
    public static function getUserPermissions(array $user): array {
        if ($user['role'] === 'admin') {
            return array_keys(self::$systemPermissions);
        }
        
        $permissions = self::$defaultRolePermissions[$user['role']] ?? [];
        
        // Ajouter les permissions individuelles
        $permissionModel = new Permission();
        $individualPermissions = $permissionModel->getUserPermissions($user['id']);
        
        return array_unique(array_merge($permissions, $individualPermissions));
    }
    
    /**
     * Accorder une permission à un utilisateur
     */
    public static function grantPermission(string $userId, string $permission, string $grantedBy): bool {
        if (!isset(self::$systemPermissions[$permission])) {
            return false;
        }
        
        $permissionModel = new Permission();
        return $permissionModel->grant($userId, $permission, $grantedBy);
    }
    
    /**
     * Révoquer une permission d'un utilisateur
     */
    public static function revokePermission(string $userId, string $permission): bool {
        $permissionModel = new Permission();
        return $permissionModel->revoke($userId, $permission);
    }
    
    /**
     * Vérifier si une permission existe
     */
    public static function permissionExists(string $permission): bool {
        return isset(self::$systemPermissions[$permission]);
    }
    
    /**
     * Obtenir la description d'une permission
     */
    public static function getPermissionDescription(string $permission): string {
        return self::$systemPermissions[$permission] ?? 'Permission inconnue';
    }
    
    /**
     * Synchroniser les permissions d'un rôle
     */
    public static function syncRolePermissions(string $userId, string $role): bool {
        $permissionModel = new Permission();
        $currentPermissions = $permissionModel->getUserPermissions($userId);
        $defaultPermissions = self::$defaultRolePermissions[$role] ?? [];
        
        if ($role === 'admin') {
            // Admin n'a pas besoin de permissions individuelles
            return $permissionModel->clearUserPermissions($userId);
        }
        
        // Supprimer les permissions qui ne sont plus nécessaires
        foreach ($currentPermissions as $permission) {
            if (!in_array($permission, $defaultPermissions)) {
                $permissionModel->revoke($userId, $permission);
            }
        }
        
        return true;
    }
    
    /**
     * Valider une demande de permission (middleware)
     */
    public static function validatePermissionRequest(array $user, string $permission): array {
        if (!self::permissionExists($permission)) {
            return [
                'valid' => false,
                'error' => 'Permission inconnue',
                'code' => 400
            ];
        }
        
        if (!self::hasPermission($user, $permission)) {
            return [
                'valid' => false,
                'error' => 'Permission refusée',
                'code' => 403
            ];
        }
        
        return ['valid' => true];
    }
}
?>