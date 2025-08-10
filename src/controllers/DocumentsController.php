<?php
/**
 * Contrôleur des pages de documents
 */

class DocumentsController extends BaseController {
    
    /**
     * GET /documents
     */
    public function index(): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'title' => 'Documents - IntraSphere',
            'description' => 'Gestionnaire de documents d\'entreprise'
        ];
        
        $this->view('documents/index', $data);
    }
    
    /**
     * GET /documents/upload
     */
    public function upload(): void {
        $user = $this->requirePermission('manage_documents');
        
        $data = [
            'user' => $user,
            'title' => 'Upload de document - IntraSphere',
            'description' => 'Télécharger un nouveau document'
        ];
        
        $this->view('documents/upload', $data);
    }
    
    /**
     * GET /documents/:id
     */
    public function show(string $id): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'document_id' => $id,
            'title' => 'Document - IntraSphere',
            'description' => 'Détail du document'
        ];
        
        $this->view('documents/show', $data);
    }
}