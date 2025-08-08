<?php
namespace Api;

/**
 * Contrôleur API des réclamations
 * Équivalent aux routes /api/complaints/* TypeScript
 */

class ComplaintsController extends \BaseController {
    private \Complaint $complaintModel;
    private \User $userModel;
    
    public function __construct() {
        $this->complaintModel = new \Complaint();
        $this->userModel = new \User();
    }
    
    /**
     * GET /api/complaints
     */
    public function index(): void {
        $user = $this->requireAuth();
        
        $status = $this->getQueryParam('status');
        $priority = $this->getQueryParam('priority');
        $assignee = $this->getQueryParam('assignee');
        $search = $this->getQueryParam('search');
        $page = $this->getQueryParam('page', 1);
        $limit = $this->getQueryParam('limit', 20);
        
        try {
            if ($search) {
                $complaints = $this->complaintModel->search($search);
            } elseif ($status) {
                $complaints = $this->complaintModel->getByStatus($status);
            } elseif ($priority) {
                $complaints = $this->complaintModel->getByPriority($priority);
            } elseif ($assignee) {
                $complaints = $this->complaintModel->getAssignedTo($assignee);
            } elseif ($user['role'] === 'employee') {
                // Les employés ne voient que leurs propres réclamations
                $complaints = $this->complaintModel->getBySubmitter($user['id']);
            } else {
                // Les modérateurs/admins voient tout avec pagination
                $filters = array_filter([
                    'status' => $status,
                    'priority' => $priority,
                    'assignee_id' => $assignee
                ]);
                $complaints = $this->complaintModel->getPaginated($page, $limit, $filters);
            }
            
            $this->json($complaints);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération des réclamations');
        }
    }
    
    /**
     * GET /api/complaints/:id
     */
    public function show(string $id): void {
        $user = $this->requireAuth();
        
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            $this->error('Réclamation introuvable', 404);
        }
        
        // Vérifier les permissions de lecture
        if ($user['role'] === 'employee' && $complaint['submitter_id'] !== $user['id']) {
            $this->error('Vous ne pouvez consulter que vos propres réclamations', 403);
        }
        
        // Enrichir avec les informations des utilisateurs
        $complaintWithUsers = $this->complaintModel->findWithUsers($id);
        
        $this->json($complaintWithUsers);
    }
    
    /**
     * POST /api/complaints
     */
    public function create(): void {
        $user = $this->requireAuth();
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['title', 'description']);
        
        $complaintData = $this->sanitizeInput([
            'submitter_id' => $user['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'] ?? null,
            'priority' => $data['priority'] ?? 'medium'
        ]);
        
        try {
            $complaint = $this->complaintModel->create($complaintData);
            
            // Enrichir avec les informations utilisateur
            $complaintWithUsers = $this->complaintModel->findWithUsers($complaint['id']);
            
            $this->logActivity('complaint_created', [
                'complaint_id' => $complaint['id'],
                'title' => $complaint['title'],
                'priority' => $complaint['priority']
            ]);
            
            $this->json($complaintWithUsers, 201);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la création de la réclamation');
        }
    }
    
    /**
     * PUT /api/complaints/:id
     */
    public function update(string $id): void {
        $user = $this->requireAuth();
        
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            $this->error('Réclamation introuvable', 404);
        }
        
        // Vérifier les permissions de modification
        $canEdit = ($user['role'] === 'admin') || 
                   ($user['role'] === 'moderator') ||
                   ($complaint['submitter_id'] === $user['id'] && $complaint['status'] === 'open');
        
        if (!$canEdit) {
            $this->error('Vous ne pouvez modifier cette réclamation', 403);
        }
        
        $data = $this->getJsonInput();
        
        $updateData = [];
        
        // Les employés ne peuvent modifier que le titre et la description
        if ($user['role'] === 'employee') {
            if (isset($data['title'])) $updateData['title'] = $data['title'];
            if (isset($data['description'])) $updateData['description'] = $data['description'];
        } else {
            // Les modérateurs/admins peuvent tout modifier
            if (isset($data['title'])) $updateData['title'] = $data['title'];
            if (isset($data['description'])) $updateData['description'] = $data['description'];
            if (isset($data['category'])) $updateData['category'] = $data['category'];
            if (isset($data['priority'])) $updateData['priority'] = $data['priority'];
            if (isset($data['status'])) $updateData['status'] = $data['status'];
            if (isset($data['assigned_to_id'])) $updateData['assigned_to_id'] = $data['assigned_to_id'];
        }
        
        if (empty($updateData)) {
            $this->error('Aucune donnée à mettre à jour');
        }
        
        try {
            $updatedComplaint = $this->complaintModel->update($id, $this->sanitizeInput($updateData));
            
            // Enrichir avec les informations utilisateur
            $complaintWithUsers = $this->complaintModel->findWithUsers($id);
            
            $this->logActivity('complaint_updated', [
                'complaint_id' => $id,
                'title' => $updatedComplaint['title']
            ]);
            
            $this->json($complaintWithUsers);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour de la réclamation');
        }
    }
    
    /**
     * DELETE /api/complaints/:id
     */
    public function delete(string $id): void {
        $user = $this->requirePermission('manage_complaints');
        
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            $this->error('Réclamation introuvable', 404);
        }
        
        try {
            $this->complaintModel->delete($id);
            
            $this->logActivity('complaint_deleted', [
                'complaint_id' => $id,
                'title' => $complaint['title']
            ]);
            
            $this->json(['message' => 'Réclamation supprimée avec succès']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression de la réclamation');
        }
    }
    
    /**
     * PATCH /api/complaints/:id/assign
     */
    public function assign(string $id): void {
        $user = $this->requireRole('moderator');
        
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            $this->error('Réclamation introuvable', 404);
        }
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['assignee_id']);
        
        // Vérifier que l'assignee existe
        $assignee = $this->userModel->find($data['assignee_id']);
        if (!$assignee || !in_array($assignee['role'], ['moderator', 'admin'])) {
            $this->error('L\'assigné doit être un modérateur ou administrateur');
        }
        
        try {
            $updatedComplaint = $this->complaintModel->assignTo($id, $data['assignee_id']);
            
            $this->logActivity('complaint_assigned', [
                'complaint_id' => $id,
                'assignee_id' => $data['assignee_id'],
                'assignee_name' => $assignee['name']
            ]);
            
            // Enrichir avec les informations utilisateur
            $complaintWithUsers = $this->complaintModel->findWithUsers($id);
            
            $this->json($complaintWithUsers);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de l\'assignation de la réclamation');
        }
    }
    
    /**
     * PATCH /api/complaints/:id/status
     */
    public function changeStatus(string $id): void {
        $user = $this->requireRole('moderator');
        
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            $this->error('Réclamation introuvable', 404);
        }
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['status']);
        
        try {
            $updatedComplaint = $this->complaintModel->changeStatus($id, $data['status']);
            
            $this->logActivity('complaint_status_changed', [
                'complaint_id' => $id,
                'old_status' => $complaint['status'],
                'new_status' => $data['status']
            ]);
            
            // Enrichir avec les informations utilisateur
            $complaintWithUsers = $this->complaintModel->findWithUsers($id);
            
            $this->json($complaintWithUsers);
            
        } catch (Exception $e) {
            $this->error('Erreur lors du changement de statut');
        }
    }
    
    /**
     * PATCH /api/complaints/:id/priority
     */
    public function changePriority(string $id): void {
        $user = $this->requireRole('moderator');
        
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            $this->error('Réclamation introuvable', 404);
        }
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['priority']);
        
        try {
            $updatedComplaint = $this->complaintModel->changePriority($id, $data['priority']);
            
            $this->logActivity('complaint_priority_changed', [
                'complaint_id' => $id,
                'old_priority' => $complaint['priority'],
                'new_priority' => $data['priority']
            ]);
            
            // Enrichir avec les informations utilisateur
            $complaintWithUsers = $this->complaintModel->findWithUsers($id);
            
            $this->json($complaintWithUsers);
            
        } catch (Exception $e) {
            $this->error('Erreur lors du changement de priorité');
        }
    }
    
    /**
     * GET /api/complaints/my-complaints
     */
    public function myComplaints(): void {
        $user = $this->requireAuth();
        
        $complaints = $this->complaintModel->getBySubmitter($user['id']);
        
        $this->json($complaints);
    }
    
    /**
     * GET /api/complaints/assigned-to-me
     */
    public function assignedToMe(): void {
        $user = $this->requireRole('moderator');
        
        $complaints = $this->complaintModel->getAssignedTo($user['id']);
        
        $this->json($complaints);
    }
    
    /**
     * GET /api/complaints/stats
     */
    public function stats(): void {
        $this->requireRole('moderator');
        
        $stats = $this->complaintModel->getStats();
        
        $this->json($stats);
    }
    
    /**
     * POST /api/complaints/bulk-delete
     */
    public function bulkDelete(): void {
        $user = $this->requireRole('admin');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['complaint_ids']);
        
        $complaintIds = $data['complaint_ids'];
        if (!is_array($complaintIds) || empty($complaintIds)) {
            $this->error('Liste d\'IDs de réclamations invalide');
        }
        
        try {
            $deletedCount = $this->complaintModel->bulkDelete($complaintIds);
            
            $this->logActivity('complaints_bulk_deleted', [
                'count' => $deletedCount,
                'complaint_ids' => $complaintIds
            ]);
            
            $this->json([
                'message' => "$deletedCount réclamation(s) supprimée(s) avec succès",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression en masse des réclamations');
        }
    }
}