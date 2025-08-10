<?php
/**
 * Contrôleur des pages d'annonces
 */

class AnnouncementsController extends BaseController {
    
    /**
     * GET /announcements
     */
    public function index(): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'title' => 'Annonces - IntraSphere',
            'description' => 'Consultez toutes les annonces de l\'entreprise'
        ];
        
        $this->view('announcements/index', $data);
    }
    
    /**
     * GET /announcements/create
     */
    public function create(): void {
        $user = $this->requirePermission('manage_announcements');
        
        $data = [
            'user' => $user,
            'title' => 'Nouvelle annonce - IntraSphere',
            'description' => 'Créer une nouvelle annonce'
        ];
        
        $this->view('announcements/create', $data);
    }
    
    /**
     * GET /announcements/:id
     */
    public function show(string $id): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'announcement_id' => $id,
            'title' => 'Annonce - IntraSphere',
            'description' => 'Détail de l\'annonce'
        ];
        
        $this->view('announcements/show', $data);
    }
    
    /**
     * GET /announcements/:id/edit
     */
    public function edit(string $id): void {
        $user = $this->requirePermission('manage_announcements');
        
        $data = [
            'user' => $user,
            'announcement_id' => $id,
            'title' => 'Modifier l\'annonce - IntraSphere',
            'description' => 'Modifier une annonce existante'
        ];
        
        $this->view('announcements/edit', $data);
    }
}