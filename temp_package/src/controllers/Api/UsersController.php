<?php
namespace Api;

/**
 * Contrôleur API des utilisateurs
 * Équivalent aux routes /api/users/* TypeScript
 */

class UsersController extends \BaseController {
    private \User $userModel;
    private \Permission $permissionModel;
    
    public function __construct() {
        $this->userModel = new \User();
        $this->permissionModel = new \Permission();
    }
    
    /**
     * GET /api/users
     */
    public function index(): void {
        $this->requireRole('moderator');
        
        $search = $this->getQueryParam('search');
        $role = $this->getQueryParam('role');
        $status = $this->getQueryParam('status');
        
        if ($search) {
            $users = $this->userModel->search($search);
        } else {
            $users = $this->userModel->getDirectory();
        }
        
        // Filtrer par rôle si spécifié
        if ($role) {
            $users = array_filter($users, fn($user) => $user['role'] === $role);
        }
        
        // Filtrer par statut si spécifié
        if ($status !== null) {
            $isActive = $status === 'active';
            $users = array_filter($users, fn($user) => $user['is_active'] == $isActive);
        }
        
        $this->json($users);
    }
    
    /**
     * GET /api/users/:id
     */
    public function show(string $id): void {
        $currentUser = $this->requireAuth();
        
        // Un utilisateur peut voir son propre profil, ou les admins/mods peuvent voir tous
        if ($currentUser['id'] !== $id && !in_array($currentUser['role'], ['admin', 'moderator'])) {
            $this->error('Accès refusé', 403);
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->error('Utilisateur introuvable', 404);
        }
        
        // Supprimer le mot de passe
        unset($user['password']);
        
        // Ajouter les permissions si admin/mod
        if (in_array($currentUser['role'], ['admin', 'moderator'])) {
            $user['permissions'] = $this->permissionModel->getUserPermissions($id);
        }
        
        $this->json($user);
    }
    
    /**
     * POST /api/users
     */
    public function create(): void {
        $this->requireRole('admin');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['username', 'password', 'name']);
        
        // Vérifier l'unicité du nom d'utilisateur
        if ($this->userModel->findByUsername($data['username'])) {
            $this->error('Nom d\'utilisateur déjà utilisé');
        }
        
        // Vérifier l'unicité de l'employee_id si fourni
        if (!empty($data['employee_id']) && $this->userModel->findByEmployeeId($data['employee_id'])) {
            $this->error('ID employé déjà utilisé');
        }
        
        $userData = $this->sanitizeInput([
            'username' => $data['username'],
            'password' => $data['password'],
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'department' => $data['department'] ?? null,
            'position' => $data['position'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'employee',
            'is_active' => $data['is_active'] ?? true
        ]);
        
        try {
            $user = $this->userModel->create($userData);
            unset($user['password']);
            
            $this->logActivity('user_created', ['new_user_id' => $user['id']]);
            
            $this->json($user, 201);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la création de l\'utilisateur');
        }
    }
    
    /**
     * PATCH /api/users/:id
     */
    public function update(string $id): void {
        $currentUser = $this->requireAuth();
        $data = $this->getJsonInput();
        
        // Un utilisateur peut modifier son profil, ou les admins peuvent modifier tous
        $canEdit = ($currentUser['id'] === $id) || ($currentUser['role'] === 'admin');
        
        if (!$canEdit) {
            $this->error('Accès refusé', 403);
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->error('Utilisateur introuvable', 404);
        }
        
        // Limiter les champs modifiables selon le rôle
        $allowedFields = ['name', 'email', 'phone', 'avatar'];
        
        if ($currentUser['role'] === 'admin') {
            $allowedFields = array_merge($allowedFields, [
                'username', 'employee_id', 'department', 'position', 'role', 'is_active'
            ]);
        }
        
        $updateData = [];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        // Validations spéciales
        if (isset($updateData['username']) && $updateData['username'] !== $user['username']) {
            if ($this->userModel->findByUsername($updateData['username'])) {
                $this->error('Nom d\'utilisateur déjà utilisé');
            }
        }
        
        if (isset($updateData['employee_id']) && $updateData['employee_id'] !== $user['employee_id']) {
            if ($this->userModel->findByEmployeeId($updateData['employee_id'])) {
                $this->error('ID employé déjà utilisé');
            }
        }
        
        try {
            $updatedUser = $this->userModel->update($id, $this->sanitizeInput($updateData));
            unset($updatedUser['password']);
            
            $this->logActivity('user_updated', ['user_id' => $id, 'fields' => array_keys($updateData)]);
            
            $this->json($updatedUser);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour');
        }
    }
    
    /**
     * DELETE /api/users/:id
     */
    public function delete(string $id): void {
        $this->requireRole('admin');
        
        if (!$this->userModel->find($id)) {
            $this->error('Utilisateur introuvable', 404);
        }
        
        // Empêcher l'auto-suppression
        $currentUser = $this->requireAuth();
        if ($currentUser['id'] === $id) {
            $this->error('Impossible de supprimer son propre compte');
        }
        
        try {
            $this->userModel->delete($id);
            
            $this->logActivity('user_deleted', ['deleted_user_id' => $id]);
            
            $this->json(['message' => 'Utilisateur supprimé']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression');
        }
    }
    
    /**
     * PATCH /api/users/:id/role
     */
    public function updateRole(string $id): void {
        $this->requireRole('admin');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['role']);
        
        if (!in_array($data['role'], array_keys(USER_ROLES))) {
            $this->error('Rôle invalide');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->error('Utilisateur introuvable', 404);
        }
        
        $success = $this->userModel->updateRole($id, $data['role']);
        
        if ($success) {
            $this->logActivity('user_role_changed', [
                'user_id' => $id,
                'old_role' => $user['role'],
                'new_role' => $data['role']
            ]);
            
            $this->json(['message' => 'Rôle mis à jour']);
        } else {
            $this->error('Erreur lors de la mise à jour du rôle');
        }
    }
    
    /**
     * PATCH /api/users/:id/status
     */
    public function updateStatus(string $id): void {
        $this->requireRole('admin');
        
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->error('Utilisateur introuvable', 404);
        }
        
        // Empêcher l'auto-désactivation
        $currentUser = $this->requireAuth();
        if ($currentUser['id'] === $id) {
            $this->error('Impossible de modifier son propre statut');
        }
        
        $success = $this->userModel->toggleStatus($id);
        
        if ($success) {
            $newStatus = !$user['is_active'];
            
            $this->logActivity('user_status_changed', [
                'user_id' => $id,
                'new_status' => $newStatus ? 'active' : 'inactive'
            ]);
            
            $this->json([
                'message' => 'Statut mis à jour',
                'is_active' => $newStatus
            ]);
        } else {
            $this->error('Erreur lors de la mise à jour du statut');
        }
    }
    
    /**
     * GET /api/users/:id/permissions
     */
    public function permissions(string $id): void {
        $this->requireRole('moderator');
        
        if (!$this->userModel->find($id)) {
            $this->error('Utilisateur introuvable', 404);
        }
        
        $permissions = $this->permissionModel->getUserPermissions($id);
        
        $this->json($permissions);
    }
    
    /**
     * GET /api/users/search
     */
    public function search(): void {
        $this->requireAuth();
        
        $query = $this->getQueryParam('q');
        if (!$query) {
            $this->error('Paramètre de recherche requis');
        }
        
        $users = $this->userModel->search($query);
        
        // Limiter les informations retournées pour la recherche
        $results = array_map(function($user) {
            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'username' => $user['username'],
                'employee_id' => $user['employee_id'],
                'department' => $user['department'],
                'position' => $user['position'],
                'avatar' => $user['avatar']
            ];
        }, $users);
        
        $this->json($results);
    }
    
    /**
     * GET /api/users/directory
     */
    public function directory(): void {
        $this->requireAuth();
        
        $department = $this->getQueryParam('department');
        $position = $this->getQueryParam('position');
        
        $users = $this->userModel->getDirectory();
        
        // Filtrer par département si spécifié
        if ($department) {
            $users = array_filter($users, fn($user) => $user['department'] === $department);
        }
        
        // Filtrer par poste si spécifié
        if ($position) {
            $users = array_filter($users, fn($user) => $user['position'] === $position);
        }
        
        $this->json($users);
    }
    
    /**
     * POST /api/users/bulk-update
     */
    public function bulkUpdate(): void {
        $this->requireRole('admin');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['user_ids', 'updates']);
        
        if (!is_array($data['user_ids']) || empty($data['user_ids'])) {
            $this->error('Liste d\'utilisateurs invalide');
        }
        
        $allowedUpdates = ['role', 'is_active', 'department'];
        $updates = array_intersect_key($data['updates'], array_flip($allowedUpdates));
        
        if (empty($updates)) {
            $this->error('Aucune mise à jour valide');
        }
        
        $updated = 0;
        $errors = [];
        
        foreach ($data['user_ids'] as $userId) {
            try {
                $this->userModel->update($userId, $updates);
                $updated++;
            } catch (Exception $e) {
                $errors[] = "Erreur pour l'utilisateur {$userId}: " . $e->getMessage();
            }
        }
        
        $this->logActivity('users_bulk_updated', [
            'count' => $updated,
            'updates' => $updates
        ]);
        
        $this->json([
            'message' => "{$updated} utilisateur(s) mis à jour",
            'updated' => $updated,
            'errors' => $errors
        ]);
    }
    
    /**
     * POST /api/users/bulk-delete
     */
    public function bulkDelete(): void {
        $this->requireRole('admin');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['user_ids']);
        
        if (!is_array($data['user_ids']) || empty($data['user_ids'])) {
            $this->error('Liste d\'utilisateurs invalide');
        }
        
        // Empêcher l'auto-suppression
        $currentUser = $this->requireAuth();
        if (in_array($currentUser['id'], $data['user_ids'])) {
            $this->error('Impossible de supprimer son propre compte');
        }
        
        $deleted = 0;
        $errors = [];
        
        foreach ($data['user_ids'] as $userId) {
            try {
                if ($this->userModel->delete($userId)) {
                    $deleted++;
                }
            } catch (Exception $e) {
                $errors[] = "Erreur pour l'utilisateur {$userId}: " . $e->getMessage();
            }
        }
        
        $this->logActivity('users_bulk_deleted', ['count' => $deleted]);
        
        $this->json([
            'message' => "{$deleted} utilisateur(s) supprimé(s)",
            'deleted' => $deleted,
            'errors' => $errors
        ]);
    }
}
?>