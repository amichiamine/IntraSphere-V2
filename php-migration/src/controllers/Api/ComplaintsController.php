<?php
namespace Api;

/**
 * Contrôleur API des réclamations - COMPLET
 * Workflow de traitement des réclamations avec notifications
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
        $assigned_to = $this->getQueryParam('assigned_to');
        $page = (int) $this->getQueryParam('page', 1);
        $limit = min((int) $this->getQueryParam('limit', 20), 50);
        
        try {
            $complaints = $this->complaintModel->findWithFilters([
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => $assigned_to,
                'submitter_id' => $user['role'] === 'employee' ? $user['id'] : null
            ], $page, $limit);
            
            $total = $this->complaintModel->countWithFilters([
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => $assigned_to,
                'submitter_id' => $user['role'] === 'employee' ? $user['id'] : null
            ]);
            
            $this->paginated($complaints, $page, $limit, $total);
            
        } catch (Exception $e) {
            Logger::error('Erreur récupération réclamations', [
                'user_id' => $user['id'],
                'error' => $e->getMessage()
            ]);
            $this->error('Erreur lors de la récupération des réclamations');
        }
    }
    
    /**
     * GET /api/complaints/:id
     */
    public function show(string $id): void {
        $user = $this->requireAuth();
        
        try {
            $complaint = $this->complaintModel->findWithDetails($id);
            
            if (!$complaint) {
                ResponseFormatter::notFound('Réclamation');
            }
            
            // Vérifier les droits d'accès
            if ($user['role'] === 'employee' && $complaint['submitter_id'] !== $user['id']) {
                ResponseFormatter::permissionError('Accès refusé à cette réclamation');
            }
            
            $this->json($complaint, 'Réclamation récupérée');
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération');
        }
    }
    
    /**
     * POST /api/complaints
     */
    public function create(): void {
        $user = $this->requireAuth();
        $data = $this->getJsonInput();
        
        // Validation
        $rules = [
            'title' => 'required|max_length:255',
            'description' => 'required|max_length:5000',
            'category' => 'max_length:100',
            'priority' => 'in:low,medium,high,urgent'
        ];
        
        $errors = ValidationHelper::validate($data, $rules);
        if (!empty($errors)) {
            $this->validationError($errors);
        }
        
        try {
            $complaintData = [
                'id' => uniqid('complaint_', true),
                'submitter_id' => $user['id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'] ?? null,
                'priority' => $data['priority'] ?? 'medium',
                'status' => 'open',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $complaint = $this->complaintModel->create($complaintData);
            
            // Notifier les modérateurs
            $this->notifyModerators($complaint);
            
            // Log de l'action
            Logger::info('Nouvelle réclamation créée', [
                'complaint_id' => $complaint['id'],
                'submitter_id' => $user['id'],
                'title' => $complaint['title']
            ]);
            
            $this->json($complaint, 'Réclamation créée avec succès', 201);
            
        } catch (Exception $e) {
            Logger::error('Erreur création réclamation', [
                'user_id' => $user['id'],
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            $this->error('Erreur lors de la création de la réclamation');
        }
    }
    
    /**
     * PATCH /api/complaints/:id
     */
    public function update(string $id): void {
        $user = $this->requireRole('moderator');
        $data = $this->getJsonInput();
        
        try {
            $complaint = $this->complaintModel->find($id);
            if (!$complaint) {
                ResponseFormatter::notFound('Réclamation');
            }
            
            // Validation des champs modifiables
            $allowedFields = ['status', 'assigned_to_id', 'priority', 'category'];
            $updateData = array_intersect_key($data, array_flip($allowedFields));
            $updateData['updated_at'] = date('Y-m-d H:i:s');
            
            $updated = $this->complaintModel->update($id, $updateData);
            
            // Notifier le demandeur des changements
            if (isset($data['status']) || isset($data['assigned_to_id'])) {
                $this->notifyStatusChange($updated, $user);
            }
            
            // Log
            Logger::info('Réclamation mise à jour', [
                'complaint_id' => $id,
                'updater_id' => $user['id'],
                'changes' => array_keys($updateData)
            ]);
            
            $this->json($updated, 'Réclamation mise à jour');
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour');
        }
    }
    
    /**
     * DELETE /api/complaints/:id
     */
    public function delete(string $id): void {
        $user = $this->requireRole('admin');
        
        try {
            $complaint = $this->complaintModel->find($id);
            if (!$complaint) {
                ResponseFormatter::notFound('Réclamation');
            }
            
            $this->complaintModel->delete($id);
            
            Logger::info('Réclamation supprimée', [
                'complaint_id' => $id,
                'deleter_id' => $user['id']
            ]);
            
            $this->json(null, 'Réclamation supprimée');
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression');
        }
    }
    
    /**
     * GET /api/complaints/stats
     */
    public function stats(): void {
        $user = $this->requireRole('moderator');
        
        try {
            $stats = [
                'by_status' => $this->complaintModel->getStatsByStatus(),
                'by_priority' => $this->complaintModel->getStatsByPriority(),
                'by_category' => $this->complaintModel->getStatsByCategory(),
                'recent_count' => $this->complaintModel->getRecentCount(30),
                'average_resolution_time' => $this->complaintModel->getAverageResolutionTime(),
                'top_assignees' => $this->complaintModel->getTopAssignees(5)
            ];
            
            $this->json($stats, 'Statistiques des réclamations');
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération des statistiques');
        }
    }
    
    /**
     * POST /api/complaints/:id/assign
     */
    public function assign(string $id): void {
        $user = $this->requireRole('moderator');
        $data = $this->getJsonInput();
        
        if (empty($data['assigned_to_id'])) {
            $this->validationError(['assigned_to_id' => ['Utilisateur assigné requis']]);
        }
        
        try {
            // Vérifier que l'utilisateur assigné existe
            $assignee = $this->userModel->find($data['assigned_to_id']);
            if (!$assignee) {
                ResponseFormatter::notFound('Utilisateur assigné');
            }
            
            $updated = $this->complaintModel->update($id, [
                'assigned_to_id' => $data['assigned_to_id'],
                'status' => 'in_progress',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Notifier l'assigné
            NotificationManager::sendSystemNotification(
                'complaint_assigned',
                $assignee['id'],
                [
                    'complaint_id' => $id,
                    'complaint_title' => $updated['title'],
                    'assigner_name' => $user['name']
                ]
            );
            
            Logger::info('Réclamation assignée', [
                'complaint_id' => $id,
                'assigned_to' => $assignee['id'],
                'assigned_by' => $user['id']
            ]);
            
            $this->json($updated, 'Réclamation assignée avec succès');
            
        } catch (Exception $e) {
            $this->error('Erreur lors de l\'assignation');
        }
    }
    
    /**
     * GET /api/complaints/my-complaints
     */
    public function myComplaints(): void {
        $user = $this->requireAuth();
        
        $page = (int) $this->getQueryParam('page', 1);
        $limit = min((int) $this->getQueryParam('limit', 20), 50);
        
        try {
            $complaints = $this->complaintModel->findBySubmitter($user['id'], $page, $limit);
            $total = $this->complaintModel->countBySubmitter($user['id']);
            
            $this->paginated($complaints, $page, $limit, $total, [
                'user_role' => $user['role']
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération');
        }
    }
    
    /**
     * Notifier les modérateurs d'une nouvelle réclamation
     */
    private function notifyModerators(array $complaint): void {
        try {
            $moderators = $this->userModel->getUsersByRole('moderator');
            $admins = $this->userModel->getUsersByRole('admin');
            $recipients = array_merge($moderators, $admins);
            
            foreach ($recipients as $recipient) {
                NotificationManager::sendSystemNotification(
                    'new_complaint',
                    $recipient['id'],
                    [
                        'complaint_id' => $complaint['id'],
                        'complaint_title' => $complaint['title'],
                        'priority' => $complaint['priority'],
                        'submitter_name' => $complaint['submitter_name'] ?? 'Utilisateur'
                    ]
                );
            }
            
        } catch (Exception $e) {
            Logger::error('Erreur notification modérateurs', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Notifier le changement de statut
     */
    private function notifyStatusChange(array $complaint, array $updater): void {
        try {
            if ($complaint['submitter_id']) {
                NotificationManager::sendSystemNotification(
                    'complaint_updated',
                    $complaint['submitter_id'],
                    [
                        'complaint_id' => $complaint['id'],
                        'complaint_title' => $complaint['title'],
                        'new_status' => $complaint['status'],
                        'updater_name' => $updater['name']
                    ]
                );
            }
            
        } catch (Exception $e) {
            Logger::error('Erreur notification changement statut', ['error' => $e->getMessage()]);
        }
    }
}
?>