<?php
namespace Api;

/**
 * Contrôleur API des formations
 * Équivalent aux routes /api/trainings/* TypeScript
 */

class TrainingsController extends \BaseController {
    private \Training $trainingModel;
    private \User $userModel;
    
    public function __construct() {
        $this->trainingModel = new \Training();
        $this->userModel = new \User();
    }
    
    /**
     * GET /api/trainings
     */
    public function index(): void {
        $this->requireAuth();
        
        $category = $this->getQueryParam('category');
        $upcoming = $this->getQueryParam('upcoming');
        $mandatory = $this->getQueryParam('mandatory');
        $search = $this->getQueryParam('search');
        $page = $this->getQueryParam('page', 1);
        $limit = $this->getQueryParam('limit', 20);
        
        try {
            if ($search) {
                $trainings = $this->trainingModel->search($search);
            } elseif ($upcoming === 'true') {
                $trainings = $this->trainingModel->getUpcoming($limit);
            } elseif ($mandatory === 'true') {
                $trainings = $this->trainingModel->getMandatory();
            } elseif ($category) {
                $trainings = $this->trainingModel->getByCategory($category);
            } else {
                $trainings = $this->trainingModel->getPaginated($page, $limit);
            }
            
            $this->json($trainings);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération des formations');
        }
    }
    
    /**
     * GET /api/trainings/:id
     */
    public function show(string $id): void {
        $this->requireAuth();
        
        $training = $this->trainingModel->findWithInstructor($id);
        if (!$training) {
            $this->error('Formation introuvable', 404);
        }
        
        $this->json($training);
    }
    
    /**
     * POST /api/trainings
     */
    public function create(): void {
        $user = $this->requirePermission('manage_trainings');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['title', 'description']);
        
        // Validation des données
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = strtotime($data['start_date']);
            $endDate = strtotime($data['end_date']);
            
            if ($startDate === false || $endDate === false) {
                $this->error('Format de date invalide');
            }
            
            if ($startDate >= $endDate) {
                $this->error('La date de fin doit être postérieure à la date de début');
            }
            
            $data['start_date'] = date('Y-m-d H:i:s', $startDate);
            $data['end_date'] = date('Y-m-d H:i:s', $endDate);
        }
        
        $trainingData = $this->sanitizeInput([
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'] ?? 'general',
            'instructor_id' => $data['instructor_id'] ?? $user['id'],
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'max_participants' => $data['max_participants'] ?? null,
            'location' => $data['location'] ?? null,
            'is_mandatory' => $data['is_mandatory'] ?? false,
            'is_active' => $data['is_active'] ?? true
        ]);
        
        try {
            $training = $this->trainingModel->create($trainingData);
            
            // Enrichir avec les informations instructeur
            $trainingWithInstructor = $this->trainingModel->findWithInstructor($training['id']);
            
            $this->logActivity('training_created', [
                'training_id' => $training['id'],
                'title' => $training['title']
            ]);
            
            $this->json($trainingWithInstructor, 201);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la création de la formation');
        }
    }
    
    /**
     * PUT /api/trainings/:id
     */
    public function update(string $id): void {
        $user = $this->requirePermission('manage_trainings');
        
        $training = $this->trainingModel->find($id);
        if (!$training) {
            $this->error('Formation introuvable', 404);
        }
        
        // Vérifier les permissions
        if ($user['role'] !== 'admin' && $training['instructor_id'] !== $user['id']) {
            $this->error('Vous ne pouvez modifier que vos propres formations', 403);
        }
        
        $data = $this->getJsonInput();
        
        // Validation des dates si fournies
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = strtotime($data['start_date']);
            $endDate = strtotime($data['end_date']);
            
            if ($startDate === false || $endDate === false) {
                $this->error('Format de date invalide');
            }
            
            if ($startDate >= $endDate) {
                $this->error('La date de fin doit être postérieure à la date de début');
            }
            
            $data['start_date'] = date('Y-m-d H:i:s', $startDate);
            $data['end_date'] = date('Y-m-d H:i:s', $endDate);
        }
        
        $updateData = $this->sanitizeInput(array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'instructor_id' => $data['instructor_id'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'max_participants' => $data['max_participants'] ?? null,
            'location' => $data['location'] ?? null,
            'is_mandatory' => isset($data['is_mandatory']) ? $data['is_mandatory'] : null,
            'is_active' => isset($data['is_active']) ? $data['is_active'] : null
        ], fn($value) => $value !== null));
        
        try {
            $updatedTraining = $this->trainingModel->update($id, $updateData);
            
            // Enrichir avec les informations instructeur
            $trainingWithInstructor = $this->trainingModel->findWithInstructor($id);
            
            $this->logActivity('training_updated', [
                'training_id' => $id,
                'title' => $updatedTraining['title']
            ]);
            
            $this->json($trainingWithInstructor);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour de la formation');
        }
    }
    
    /**
     * DELETE /api/trainings/:id
     */
    public function delete(string $id): void {
        $user = $this->requirePermission('manage_trainings');
        
        $training = $this->trainingModel->find($id);
        if (!$training) {
            $this->error('Formation introuvable', 404);
        }
        
        // Vérifier les permissions
        if ($user['role'] !== 'admin' && $training['instructor_id'] !== $user['id']) {
            $this->error('Vous ne pouvez supprimer que vos propres formations', 403);
        }
        
        try {
            $this->trainingModel->delete($id);
            
            $this->logActivity('training_deleted', [
                'training_id' => $id,
                'title' => $training['title']
            ]);
            
            $this->json(['message' => 'Formation supprimée avec succès']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression de la formation');
        }
    }
    
    /**
     * POST /api/trainings/:id/register
     */
    public function register(string $id): void {
        $user = $this->requireAuth();
        
        $training = $this->trainingModel->find($id);
        if (!$training) {
            $this->error('Formation introuvable', 404);
        }
        
        if (!$training['is_active']) {
            $this->error('Cette formation n\'est plus active');
        }
        
        try {
            $canRegister = $this->trainingModel->canRegister($id, $user['id']);
            if (!$canRegister['allowed']) {
                $this->error($canRegister['reason']);
            }
            
            $this->trainingModel->registerUser($id, $user['id']);
            
            $this->logActivity('training_registration', [
                'training_id' => $id,
                'user_id' => $user['id'],
                'training_title' => $training['title']
            ]);
            
            $this->json(['message' => 'Inscription à la formation réussie']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de l\'inscription à la formation');
        }
    }
    
    /**
     * DELETE /api/trainings/:id/register
     */
    public function unregister(string $id): void {
        $user = $this->requireAuth();
        
        $training = $this->trainingModel->find($id);
        if (!$training) {
            $this->error('Formation introuvable', 404);
        }
        
        try {
            $this->trainingModel->unregisterUser($id, $user['id']);
            
            $this->logActivity('training_unregistration', [
                'training_id' => $id,
                'user_id' => $user['id'],
                'training_title' => $training['title']
            ]);
            
            $this->json(['message' => 'Désinscription de la formation réussie']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la désinscription de la formation');
        }
    }
    
    /**
     * GET /api/trainings/:id/participants
     */
    public function participants(string $id): void {
        $user = $this->requireAuth();
        
        $training = $this->trainingModel->find($id);
        if (!$training) {
            $this->error('Formation introuvable', 404);
        }
        
        // Seuls les instructeurs et admins peuvent voir les participants
        if ($user['role'] !== 'admin' && $training['instructor_id'] !== $user['id']) {
            $this->error('Accès refusé aux informations des participants', 403);
        }
        
        try {
            $participants = $this->trainingModel->getParticipants($id);
            
            $this->json([
                'training' => [
                    'id' => $training['id'],
                    'title' => $training['title'],
                    'max_participants' => $training['max_participants']
                ],
                'participants' => $participants,
                'total_registered' => count($participants)
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération des participants');
        }
    }
    
    /**
     * GET /api/trainings/my-trainings
     */
    public function myTrainings(): void {
        $user = $this->requireAuth();
        
        $type = $this->getQueryParam('type', 'registered'); // registered, instructing
        
        try {
            if ($type === 'instructing') {
                $trainings = $this->trainingModel->getInstructorTrainings($user['id']);
            } else {
                $trainings = $this->trainingModel->getUserRegistrations($user['id']);
            }
            
            $this->json($trainings);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération de vos formations');
        }
    }
    
    /**
     * GET /api/trainings/categories
     */
    public function categories(): void {
        $this->requireAuth();
        
        $categories = $this->trainingModel->getCategories();
        
        $this->json($categories);
    }
    
    /**
     * GET /api/trainings/stats
     */
    public function stats(): void {
        $this->requireRole('moderator');
        
        $stats = [
            'total' => $this->trainingModel->count(),
            'active' => $this->trainingModel->countActive(),
            'upcoming' => $this->trainingModel->countUpcoming(),
            'mandatory' => $this->trainingModel->countMandatory(),
            'by_category' => $this->trainingModel->countByCategory(),
            'total_participants' => $this->trainingModel->countTotalParticipants(),
            'most_popular' => $this->trainingModel->getMostPopular(5)
        ];
        
        $this->json($stats);
    }
}