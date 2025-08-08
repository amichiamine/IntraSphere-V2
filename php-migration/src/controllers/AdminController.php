<?php
/**
 * Contrôleur des pages d'administration
 */

class AdminController extends BaseController {
    
    /**
     * GET /admin
     */
    public function index(): void {
        $user = $this->requireRole('admin');
        
        $data = [
            'user' => $user,
            'title' => 'Administration - IntraSphere',
            'description' => 'Panneau d\'administration général'
        ];
        
        $this->view('admin/index', $data);
    }
    
    /**
     * GET /admin/users
     */
    public function users(): void {
        $user = $this->requireRole('admin');
        
        $data = [
            'user' => $user,
            'title' => 'Gestion des utilisateurs - IntraSphere',
            'description' => 'Administration des comptes utilisateurs'
        ];
        
        $this->view('admin/users', $data);
    }
    
    /**
     * GET /admin/permissions
     */
    public function permissions(): void {
        $user = $this->requireRole('admin');
        
        $data = [
            'user' => $user,
            'title' => 'Gestion des permissions - IntraSphere',
            'description' => 'Administration des rôles et permissions'
        ];
        
        $this->view('admin/permissions', $data);
    }
    
    /**
     * GET /admin/system
     */
    public function system(): void {
        $user = $this->requireRole('admin');
        
        $data = [
            'user' => $user,
            'title' => 'Configuration système - IntraSphere',
            'description' => 'Paramètres et informations système'
        ];
        
        $this->view('admin/system', $data);
    }
    
    /**
     * GET /admin/logs
     */
    public function logs(): void {
        $user = $this->requireRole('admin');
        
        $data = [
            'user' => $user,
            'title' => 'Journaux système - IntraSphere',
            'description' => 'Logs d\'activité et d\'erreurs'
        ];
        
        $this->view('admin/logs', $data);
    }
}