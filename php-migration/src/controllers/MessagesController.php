<?php
/**
 * Contrôleur des pages de messagerie
 */

class MessagesController extends BaseController {
    
    /**
     * GET /messages
     */
    public function index(): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'title' => 'Messagerie - IntraSphere',
            'description' => 'Votre messagerie interne'
        ];
        
        $this->view('messages/index', $data);
    }
    
    /**
     * GET /messages/compose
     */
    public function compose(): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'title' => 'Nouveau message - IntraSphere',
            'description' => 'Composer un nouveau message'
        ];
        
        $this->view('messages/compose', $data);
    }
    
    /**
     * GET /messages/:id
     */
    public function show(string $id): void {
        $user = $this->requireAuth();
        
        $data = [
            'user' => $user,
            'message_id' => $id,
            'title' => 'Message - IntraSphere',
            'description' => 'Détail du message'
        ];
        
        $this->view('messages/show', $data);
    }
}