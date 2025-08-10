<?php
/**
 * Contrôleur des pages de formations
 */

class TrainingsController extends BaseController {
    
    /**
     * GET /trainings
     */
    public function index(): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'title' => 'Formations - IntraSphere',
            'description' => 'Catalogue des formations disponibles'
        ];
        
        $this->view('trainings/index', $data);
    }
    
    /**
     * GET /trainings/create
     */
    public function create(): void {
        $user = $this->requirePermission('manage_trainings');
        
        $data = [
            'user' => $user,
            'title' => 'Nouvelle formation - IntraSphere',
            'description' => 'Créer une nouvelle formation'
        ];
        
        $this->view('trainings/create', $data);
    }
    
    /**
     * GET /trainings/:id
     */
    public function show(string $id): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'training_id' => $id,
            'title' => 'Formation - IntraSphere',
            'description' => 'Détail de la formation'
        ];
        
        $this->view('trainings/show', $data);
    }
    
    /**
     * GET /trainings/my-trainings
     */
    public function myTrainings(): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'title' => 'Mes formations - IntraSphere',
            'description' => 'Vos formations inscrites et dispensées'
        ];
        
        $this->view('trainings/my-trainings', $data);
    }
}