<?php
namespace Api;

/**
 * Contrôleur API des annonces
 * Équivalent aux routes /api/announcements/* TypeScript
 */

class AnnouncementsController extends \BaseController {
    private \Announcement $announcementModel;
    
    public function __construct() {
        $this->announcementModel = new \Announcement();
    }
    
    /**
     * GET /api/announcements
     */
    public function index(): void {
        $this->requireAuth();
        
        $type = $this->getQueryParam('type');
        $important = $this->getQueryParam('important');
        $search = $this->getQueryParam('search');
        $limit = $this->getQueryParam('limit', 20);
        
        if ($search) {
            $announcements = $this->announcementModel->search($search);
        } elseif ($type) {
            $announcements = $this->announcementModel->getByType($type);
        } elseif ($important === 'true') {
            $announcements = $this->announcementModel->getImportant();
        } else {
            $announcements = $this->announcementModel->findAllWithAuthor();
        }
        
        // Limiter le nombre de résultats
        if ($limit) {
            $announcements = array_slice($announcements, 0, (int)$limit);
        }
        
        $this->json($announcements);
    }
    
    /**
     * GET /api/announcements/:id
     */
    public function show(string $id): void {
        $this->requireAuth();
        
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            $this->error('Annonce introuvable', 404);
        }
        
        $this->json($announcement);
    }
    
    /**
     * POST /api/announcements
     */
    public function create(): void {
        $user = $this->requirePermission('manage_announcements');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['title', 'content']);
        
        $announcementData = $this->sanitizeInput([
            'title' => $data['title'],
            'content' => $data['content'],
            'type' => $data['type'] ?? 'info',
            'author_id' => $user['id'],
            'author_name' => $user['name'],
            'image_url' => $data['image_url'] ?? null,
            'icon' => $data['icon'] ?? '📢',
            'is_important' => $data['is_important'] ?? false
        ]);
        
        try {
            $announcement = $this->announcementModel->create($announcementData);
            
            $this->logActivity('announcement_created', ['announcement_id' => $announcement['id']]);
            
            $this->json($announcement, 201);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la création de l\'annonce');
        }
    }
    
    /**
     * PUT /api/announcements/:id
     */
    public function update(string $id): void {
        $user = $this->requirePermission('manage_announcements');
        
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            $this->error('Annonce introuvable', 404);
        }
        
        // Vérifier si l'utilisateur peut modifier cette annonce
        if ($user['role'] !== 'admin' && $announcement['author_id'] !== $user['id']) {
            $this->error('Vous ne pouvez modifier que vos propres annonces', 403);
        }
        
        $data = $this->getJsonInput();
        
        $updateData = [];
        $allowedFields = ['title', 'content', 'type', 'image_url', 'icon', 'is_important'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (empty($updateData)) {
            $this->error('Aucune donnée à mettre à jour');
        }
        
        try {
            $updatedAnnouncement = $this->announcementModel->update($id, $this->sanitizeInput($updateData));
            
            $this->logActivity('announcement_updated', [
                'announcement_id' => $id,
                'fields' => array_keys($updateData)
            ]);
            
            $this->json($updatedAnnouncement);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour');
        }
    }
    
    /**
     * DELETE /api/announcements/:id
     */
    public function delete(string $id): void {
        $user = $this->requirePermission('manage_announcements');
        
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            $this->error('Annonce introuvable', 404);
        }
        
        // Vérifier si l'utilisateur peut supprimer cette annonce
        if ($user['role'] !== 'admin' && $announcement['author_id'] !== $user['id']) {
            $this->error('Vous ne pouvez supprimer que vos propres annonces', 403);
        }
        
        try {
            $this->announcementModel->delete($id);
            
            $this->logActivity('announcement_deleted', ['announcement_id' => $id]);
            
            $this->json(['message' => 'Annonce supprimée']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression');
        }
    }
    
    /**
     * GET /api/announcements/important
     */
    public function important(): void {
        $this->requireAuth();
        
        $announcements = $this->announcementModel->getImportant();
        
        $this->json($announcements);
    }
    
    /**
     * PATCH /api/announcements/:id/importance
     */
    public function toggleImportance(string $id): void {
        $this->requireRole('moderator');
        
        if (!$this->announcementModel->find($id)) {
            $this->error('Annonce introuvable', 404);
        }
        
        $success = $this->announcementModel->toggleImportance($id);
        
        if ($success) {
            $this->logActivity('announcement_importance_toggled', ['announcement_id' => $id]);
            
            $this->json(['message' => 'Importance modifiée']);
        } else {
            $this->error('Erreur lors de la modification');
        }
    }
    
    /**
     * GET /api/announcements/by-type/:type
     */
    public function byType(string $type): void {
        $this->requireAuth();
        
        if (!in_array($type, array_keys(ANNOUNCEMENT_TYPES))) {
            $this->error('Type d\'annonce invalide');
        }
        
        $announcements = $this->announcementModel->getByType($type);
        
        $this->json($announcements);
    }
    
    /**
     * GET /api/announcements/recent
     */
    public function recent(): void {
        $this->requireAuth();
        
        $announcements = $this->announcementModel->getRecent();
        
        $this->json($announcements);
    }
    
    /**
     * POST /api/announcements/bulk-delete
     */
    public function bulkDelete(): void {
        $user = $this->requirePermission('manage_announcements');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['announcement_ids']);
        
        if (!is_array($data['announcement_ids']) || empty($data['announcement_ids'])) {
            $this->error('Liste d\'annonces invalide');
        }
        
        // Vérifier les permissions pour chaque annonce
        $allowedIds = [];
        
        foreach ($data['announcement_ids'] as $announcementId) {
            $announcement = $this->announcementModel->find($announcementId);
            
            if ($announcement) {
                // Admin peut tout supprimer, sinon seulement ses propres annonces
                if ($user['role'] === 'admin' || $announcement['author_id'] === $user['id']) {
                    $allowedIds[] = $announcementId;
                }
            }
        }
        
        if (empty($allowedIds)) {
            $this->error('Aucune annonce à supprimer');
        }
        
        try {
            $deleted = $this->announcementModel->bulkDelete($allowedIds);
            
            $this->logActivity('announcements_bulk_deleted', [
                'count' => $deleted,
                'ids' => $allowedIds
            ]);
            
            $this->json([
                'message' => "{$deleted} annonce(s) supprimée(s)",
                'deleted' => $deleted
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression en masse');
        }
    }
    
    /**
     * GET /api/announcements/stats
     */
    public function stats(): void {
        $this->requireRole('moderator');
        
        $stats = $this->announcementModel->getStats();
        
        $this->json($stats);
    }
    
    /**
     * POST /api/announcements/:id/pin
     */
    public function pin(string $id): void {
        $this->requireRole('moderator');
        
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            $this->error('Annonce introuvable', 404);
        }
        
        // Marquer comme importante (équivalent du pinning)
        $success = $this->announcementModel->update($id, ['is_important' => true]);
        
        if ($success) {
            $this->logActivity('announcement_pinned', ['announcement_id' => $id]);
            
            $this->json(['message' => 'Annonce épinglée']);
        } else {
            $this->error('Erreur lors de l\'épinglage');
        }
    }
    
    /**
     * DELETE /api/announcements/:id/pin
     */
    public function unpin(string $id): void {
        $this->requireRole('moderator');
        
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            $this->error('Annonce introuvable', 404);
        }
        
        $success = $this->announcementModel->update($id, ['is_important' => false]);
        
        if ($success) {
            $this->logActivity('announcement_unpinned', ['announcement_id' => $id]);
            
            $this->json(['message' => 'Annonce désépinglée']);
        } else {
            $this->error('Erreur lors du désépinglage');
        }
    }
}
?>