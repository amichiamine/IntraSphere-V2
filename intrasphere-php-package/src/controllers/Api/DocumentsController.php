<?php
namespace Api;

/**
 * Contrôleur API des documents
 * Équivalent aux routes /api/documents/* TypeScript
 */

class DocumentsController extends \BaseController {
    private \Document $documentModel;
    
    public function __construct() {
        $this->documentModel = new \Document();
    }
    
    /**
     * GET /api/documents
     */
    public function index(): void {
        $this->requireAuth();
        
        $category = $this->getQueryParam('category');
        $search = $this->getQueryParam('search');
        $recent = $this->getQueryParam('recent');
        $page = $this->getQueryParam('page', 1);
        $limit = $this->getQueryParam('limit', 20);
        
        try {
            if ($search) {
                $documents = $this->documentModel->search($search);
            } elseif ($category) {
                $documents = $this->documentModel->getByCategory($category);
            } elseif ($recent === 'true') {
                $documents = $this->documentModel->getRecent(30);
            } else {
                $documents = $this->documentModel->getPaginated($page, $limit);
            }
            
            $this->json($documents);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération des documents');
        }
    }
    
    /**
     * GET /api/documents/:id
     */
    public function show(string $id): void {
        $this->requireAuth();
        
        $document = $this->documentModel->find($id);
        if (!$document) {
            $this->error('Document introuvable', 404);
        }
        
        // Incrémenter le compteur de vues
        $this->documentModel->incrementViews($id);
        
        $this->json($document);
    }
    
    /**
     * POST /api/documents
     */
    public function create(): void {
        $user = $this->requirePermission('manage_documents');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['title', 'category']);
        
        // Validation de la catégorie
        $allowedCategories = ['regulation', 'policy', 'guide', 'procedure'];
        if (!in_array($data['category'], $allowedCategories)) {
            $this->error('Catégorie invalide');
        }
        
        $documentData = $this->sanitizeInput([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'],
            'file_name' => $data['file_name'] ?? null,
            'file_url' => $data['file_url'] ?? null,
            'version' => $data['version'] ?? '1.0',
            'uploaded_by' => $user['id']
        ]);
        
        try {
            $document = $this->documentModel->create($documentData);
            
            $this->logActivity('document_created', [
                'document_id' => $document['id'],
                'title' => $document['title']
            ]);
            
            $this->json($document, 201);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la création du document');
        }
    }
    
    /**
     * PUT /api/documents/:id
     */
    public function update(string $id): void {
        $user = $this->requirePermission('manage_documents');
        
        $document = $this->documentModel->find($id);
        if (!$document) {
            $this->error('Document introuvable', 404);
        }
        
        $data = $this->getJsonInput();
        
        // Validation de la catégorie si fournie
        if (isset($data['category'])) {
            $allowedCategories = ['regulation', 'policy', 'guide', 'procedure'];
            if (!in_array($data['category'], $allowedCategories)) {
                $this->error('Catégorie invalide');
            }
        }
        
        $updateData = $this->sanitizeInput([
            'title' => $data['title'] ?? $document['title'],
            'description' => $data['description'] ?? $document['description'],
            'category' => $data['category'] ?? $document['category'],
            'file_name' => $data['file_name'] ?? $document['file_name'],
            'file_url' => $data['file_url'] ?? $document['file_url'],
            'version' => $data['version'] ?? $document['version']
        ]);
        
        try {
            $updatedDocument = $this->documentModel->update($id, $updateData);
            
            $this->logActivity('document_updated', [
                'document_id' => $id,
                'title' => $updatedDocument['title']
            ]);
            
            $this->json($updatedDocument);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour du document');
        }
    }
    
    /**
     * DELETE /api/documents/:id
     */
    public function delete(string $id): void {
        $user = $this->requirePermission('manage_documents');
        
        $document = $this->documentModel->find($id);
        if (!$document) {
            $this->error('Document introuvable', 404);
        }
        
        try {
            $this->documentModel->delete($id);
            
            $this->logActivity('document_deleted', [
                'document_id' => $id,
                'title' => $document['title']
            ]);
            
            $this->json(['message' => 'Document supprimé avec succès']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression du document');
        }
    }
    
    /**
     * GET /api/documents/categories
     */
    public function categories(): void {
        $this->requireAuth();
        
        $categories = [
            'regulation' => 'Réglementation',
            'policy' => 'Politique',
            'guide' => 'Guide',
            'procedure' => 'Procédure'
        ];
        
        // Compter les documents par catégorie
        $categoriesWithCount = [];
        foreach ($categories as $key => $label) {
            $categoriesWithCount[] = [
                'key' => $key,
                'label' => $label,
                'count' => $this->documentModel->countByCategory($key)
            ];
        }
        
        $this->json($categoriesWithCount);
    }
    
    /**
     * GET /api/documents/recent
     */
    public function recent(): void {
        $this->requireAuth();
        
        $days = $this->getQueryParam('days', 30);
        $limit = $this->getQueryParam('limit', 10);
        
        $recentDocuments = $this->documentModel->getRecent($days, $limit);
        
        $this->json($recentDocuments);
    }
    
    /**
     * GET /api/documents/stats
     */
    public function stats(): void {
        $this->requireRole('moderator');
        
        $stats = [
            'total' => $this->documentModel->count(),
            'by_category' => $this->documentModel->countByCategory(),
            'recent_uploads' => $this->documentModel->countRecent(7),
            'most_viewed' => $this->documentModel->getMostViewed(10),
            'total_size' => $this->documentModel->getTotalFileSize()
        ];
        
        $this->json($stats);
    }
    
    /**
     * POST /api/documents/bulk-delete
     */
    public function bulkDelete(): void {
        $user = $this->requirePermission('manage_documents');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['document_ids']);
        
        $documentIds = $data['document_ids'];
        if (!is_array($documentIds) || empty($documentIds)) {
            $this->error('Liste d\'IDs de documents invalide');
        }
        
        try {
            $deletedCount = $this->documentModel->bulkDelete($documentIds);
            
            $this->logActivity('documents_bulk_deleted', [
                'count' => $deletedCount,
                'document_ids' => $documentIds
            ]);
            
            $this->json([
                'message' => "$deletedCount document(s) supprimé(s) avec succès",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression en masse des documents');
        }
    }
    
    /**
     * POST /api/documents/:id/download
     */
    public function download(string $id): void {
        $user = $this->requireAuth();
        
        $document = $this->documentModel->find($id);
        if (!$document) {
            $this->error('Document introuvable', 404);
        }
        
        // Vérifier que le fichier existe
        if (!$this->documentModel->fileExists($id)) {
            $this->error('Fichier introuvable sur le serveur', 404);
        }
        
        // Log du téléchargement
        $this->logActivity('document_downloaded', [
            'document_id' => $id,
            'title' => $document['title'],
            'user_id' => $user['id']
        ]);
        
        // Incrémenter le compteur de téléchargements
        $this->documentModel->incrementDownloads($id);
        
        $this->json([
            'download_url' => $document['file_url'],
            'file_name' => $document['file_name'],
            'message' => 'Téléchargement autorisé'
        ]);
    }
}