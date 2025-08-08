<?php
/**
 * Contrôleur du tableau de bord principal
 */

class DashboardController extends BaseController {
    
    public function index(): void {
        $user = $this->requireAuth();
        
        // Passer les données à la vue
        $data = [
            'user' => $user,
            'title' => 'Tableau de bord - IntraSphere',
            'description' => 'Vue d\'ensemble de votre espace de travail'
        ];
        
        $this->view('dashboard/index', $data);
    }
}