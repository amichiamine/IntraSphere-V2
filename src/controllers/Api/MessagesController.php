<?php
namespace Api;

require_once __DIR__ . '/../utils/ArrayGuard.php';

/**
 * Contrôleur API des messages
 * Équivalent aux routes /api/messages/* TypeScript
 */

class MessagesController extends \BaseController {
    private \Message $messageModel;
    private \User $userModel;
    
    public function __construct() {
        $this->messageModel = new \Message();
        $this->userModel = new \User();
    }
    
    /**
     * GET /api/messages
     */
    public function index(): void {
        $user = $this->requireAuth();
        
        $type = $this->getQueryParam('type', 'inbox'); // inbox, sent, all
        $search = $this->getQueryParam('search');
        $page = $this->getQueryParam('page', 1);
        $limit = $this->getQueryParam('limit', 20);
        
        try {
            if ($search) {
                $messages = $this->messageModel->search($user['id'], $search);
            } elseif ($type === 'sent') {
                $messages = $this->messageModel->getSent($user['id'], $page, $limit);
            } elseif ($type === 'all') {
                $messages = $this->messageModel->getAllUserMessages($user['id'], $page, $limit);
            } else {
                $messages = $this->messageModel->getInbox($user['id'], $page, $limit);
            }
            
            // Ensure safe JSON response (equivalent to React array protection)
            $safeMessages = ArrayGuard::safeJsonResponse($messages);
            $this->json($safeMessages);
            
        } catch (Exception $e) {
            // Return empty array on error to prevent frontend crashes
            $this->json([]);
        }
    }
    
    /**
     * GET /api/messages/:id
     */
    public function show(string $id): void {
        $user = $this->requireAuth();
        
        $message = $this->messageModel->findWithUsers($id);
        if (!$message) {
            $this->error('Message introuvable', 404);
        }
        
        // Vérifier que l'utilisateur peut voir ce message
        if ($message['sender_id'] !== $user['id'] && $message['recipient_id'] !== $user['id']) {
            $this->error('Accès refusé à ce message', 403);
        }
        
        // Marquer comme lu si c'est le destinataire
        if ($message['recipient_id'] === $user['id'] && !$message['is_read']) {
            $this->messageModel->markAsRead($id);
            $message['is_read'] = true;
        }
        
        $this->json($message);
    }
    
    /**
     * POST /api/messages
     */
    public function create(): void {
        $user = $this->requireAuth();
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['recipient_id', 'subject', 'content']);
        
        // Vérifier que le destinataire existe
        $recipient = $this->userModel->find($data['recipient_id']);
        if (!$recipient || !$recipient['is_active']) {
            $this->error('Destinataire introuvable ou inactif');
        }
        
        // Empêcher l'envoi à soi-même
        if ($data['recipient_id'] === $user['id']) {
            $this->error('Vous ne pouvez pas vous envoyer un message à vous-même');
        }
        
        $messageData = $this->sanitizeInput([
            'sender_id' => $user['id'],
            'recipient_id' => $data['recipient_id'],
            'subject' => $data['subject'],
            'content' => $data['content']
        ]);
        
        try {
            $message = $this->messageModel->create($messageData);
            
            // Enrichir avec les informations utilisateur
            $messageWithUsers = $this->messageModel->findWithUsers($message['id']);
            
            $this->logActivity('message_sent', [
                'message_id' => $message['id'],
                'recipient_id' => $data['recipient_id'],
                'subject' => $data['subject']
            ]);
            
            $this->json($messageWithUsers, 201);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de l\'envoi du message');
        }
    }
    
    /**
     * DELETE /api/messages/:id
     */
    public function delete(string $id): void {
        $user = $this->requireAuth();
        
        $message = $this->messageModel->find($id);
        if (!$message) {
            $this->error('Message introuvable', 404);
        }
        
        // Vérifier que l'utilisateur peut supprimer ce message
        if ($message['sender_id'] !== $user['id'] && $message['recipient_id'] !== $user['id']) {
            $this->error('Vous ne pouvez supprimer que vos propres messages', 403);
        }
        
        try {
            $this->messageModel->delete($id);
            
            $this->logActivity('message_deleted', [
                'message_id' => $id,
                'subject' => $message['subject']
            ]);
            
            $this->json(['message' => 'Message supprimé avec succès']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression du message');
        }
    }
    
    /**
     * PATCH /api/messages/:id/read
     */
    public function markAsRead(string $id): void {
        $user = $this->requireAuth();
        
        $message = $this->messageModel->find($id);
        if (!$message) {
            $this->error('Message introuvable', 404);
        }
        
        // Seul le destinataire peut marquer comme lu
        if ($message['recipient_id'] !== $user['id']) {
            $this->error('Vous ne pouvez marquer comme lu que vos messages reçus', 403);
        }
        
        try {
            $this->messageModel->markAsRead($id);
            
            $this->json(['message' => 'Message marqué comme lu']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour du statut de lecture');
        }
    }
    
    /**
     * GET /api/messages/unread-count
     */
    public function unreadCount(): void {
        $user = $this->requireAuth();
        
        $count = $this->messageModel->getUnreadCount($user['id']);
        
        $this->json(['unread_count' => $count]);
    }
    
    /**
     * GET /api/messages/conversations
     */
    public function conversations(): void {
        $user = $this->requireAuth();
        
        $limit = $this->getQueryParam('limit', 10);
        
        $conversations = $this->messageModel->getRecentConversations($user['id'], $limit);
        
        $this->json($conversations);
    }
    
    /**
     * GET /api/messages/conversation/:user_id
     */
    public function conversation(string $otherUserId): void {
        $user = $this->requireAuth();
        
        // Vérifier que l'autre utilisateur existe
        $otherUser = $this->userModel->find($otherUserId);
        if (!$otherUser) {
            $this->error('Utilisateur introuvable', 404);
        }
        
        $page = $this->getQueryParam('page', 1);
        $limit = $this->getQueryParam('limit', 50);
        
        try {
            $messages = $this->messageModel->getConversation($user['id'], $otherUserId, $page, $limit);
            
            $this->json([
                'conversation_with' => [
                    'id' => $otherUser['id'],
                    'name' => $otherUser['name'],
                    'avatar' => $otherUser['avatar']
                ],
                'messages' => $messages
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération de la conversation');
        }
    }
    
    /**
     * POST /api/messages/bulk-read
     */
    public function bulkRead(): void {
        $user = $this->requireAuth();
        
        $data = $this->getJsonInput();
        $messageIds = $data['message_ids'] ?? [];
        
        // Use ArrayGuard for safe array operations (equivalent to React fix)
        try {
            $messageIds = ArrayGuard::validateMessageIds($messageIds);
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage(), ['provided' => $data['message_ids'] ?? null]);
        }
        
        try {
            $readCount = $this->messageModel->markMultipleAsRead($user['id'], $messageIds);
            
            $this->logActivity('messages_bulk_read', [
                'count' => $readCount,
                'message_ids' => $messageIds
            ]);
            
            $this->json([
                'message' => "$readCount message(s) marqué(s) comme lu(s)",
                'read_count' => $readCount
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la lecture en masse des messages');
        }
    }
    
    /**
     * DELETE /api/messages/conversation/:user_id
     */
    public function deleteConversation(string $otherUserId): void {
        $user = $this->requireAuth();
        
        // Vérifier que l'autre utilisateur existe
        $otherUser = $this->userModel->find($otherUserId);
        if (!$otherUser) {
            $this->error('Utilisateur introuvable', 404);
        }
        
        try {
            $deletedCount = $this->messageModel->deleteConversation($user['id'], $otherUserId);
            
            $this->logActivity('conversation_deleted', [
                'other_user_id' => $otherUserId,
                'deleted_count' => $deletedCount
            ]);
            
            $this->json([
                'message' => "Conversation supprimée ($deletedCount messages)",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression de la conversation');
        }
    }
    
    /**
     * GET /api/messages/stats
     */
    public function stats(): void {
        $user = $this->requireAuth();
        
        $stats = [
            'total_sent' => $this->messageModel->countSent($user['id']),
            'total_received' => $this->messageModel->countReceived($user['id']),
            'unread_count' => $this->messageModel->getUnreadCount($user['id']),
            'conversations_count' => $this->messageModel->countConversations($user['id']),
            'recent_activity' => $this->messageModel->getRecentActivity($user['id'], 7)
        ];
        
        $this->json($stats);
    }
}