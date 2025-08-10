<?php
/**
 * Contrôleur d'upload de fichiers
 */

class UploadController extends BaseController {
    
    /**
     * POST /upload
     */
    public function handle(): void {
        $user = $this->requireAuth();
        
        // Vérifier qu'un fichier a été envoyé
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->error('Aucun fichier valide reçu', 400);
        }
        
        $file = $_FILES['file'];
        $uploadType = $_POST['type'] ?? 'general'; // document, avatar, announcement, etc.
        
        try {
            // Valider le fichier
            $this->validateFile($file, $uploadType);
            
            // Générer un nom unique
            $fileName = $this->generateFileName($file['name']);
            
            // Déterminer le répertoire de destination
            $uploadDir = $this->getUploadDirectory($uploadType);
            $uploadPath = $uploadDir . '/' . $fileName;
            
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Déplacer le fichier
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $this->error('Erreur lors du déplacement du fichier');
            }
            
            // Créer une miniature si c'est une image
            $thumbnailUrl = null;
            if ($this->isImage($file['type'])) {
                $thumbnailUrl = $this->createThumbnail($uploadPath, $uploadType);
            }
            
            // Sauvegarder les informations du fichier
            $fileInfo = [
                'original_name' => $file['name'],
                'file_name' => $fileName,
                'file_path' => $uploadPath,
                'file_url' => $this->getFileUrl($fileName, $uploadType),
                'file_size' => $file['size'],
                'mime_type' => $file['type'],
                'upload_type' => $uploadType,
                'uploaded_by' => $user['id'],
                'thumbnail_url' => $thumbnailUrl
            ];
            
            // Log de l'activité
            $this->logActivity('file_uploaded', [
                'file_name' => $file['name'],
                'file_size' => $file['size'],
                'upload_type' => $uploadType
            ]);
            
            $this->json($fileInfo, 201);
            
        } catch (Exception $e) {
            // Nettoyer en cas d'erreur
            if (isset($uploadPath) && file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            
            $this->error($e->getMessage());
        }
    }
    
    /**
     * DELETE /upload/:filename
     */
    public function delete(string $filename): void {
        $user = $this->requireAuth();
        
        $uploadType = $this->getQueryParam('type', 'general');
        
        // Construire le chemin du fichier
        $uploadDir = $this->getUploadDirectory($uploadType);
        $filePath = $uploadDir . '/' . $filename;
        
        // Vérifier que le fichier existe
        if (!file_exists($filePath)) {
            $this->error('Fichier introuvable', 404);
        }
        
        // Vérifier les permissions (admin ou propriétaire du fichier)
        if ($user['role'] !== 'admin') {
            // Ici on pourrait vérifier en base qui a uploadé le fichier
            // Pour simplifier, on permet seulement aux admins
            $this->error('Seuls les administrateurs peuvent supprimer des fichiers', 403);
        }
        
        try {
            // Supprimer le fichier principal
            unlink($filePath);
            
            // Supprimer la miniature si elle existe
            $thumbnailPath = $this->getThumbnailPath($filename, $uploadType);
            if (file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
            }
            
            $this->logActivity('file_deleted', [
                'file_name' => $filename,
                'upload_type' => $uploadType
            ]);
            
            $this->json(['message' => 'Fichier supprimé avec succès']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression du fichier');
        }
    }
    
    /**
     * Valider un fichier uploadé
     */
    private function validateFile(array $file, string $uploadType): void {
        // Vérifier la taille
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('Fichier trop volumineux (max ' . $this->formatFileSize(MAX_FILE_SIZE) . ')');
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = $this->getAllowedExtensions($uploadType);
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Type de fichier non autorisé. Extensions autorisées : ' . implode(', ', $allowedExtensions));
        }
        
        // Vérifier le type MIME
        $allowedMimes = $this->getAllowedMimeTypes($uploadType);
        if (!in_array($file['type'], $allowedMimes)) {
            throw new Exception('Type MIME non autorisé : ' . $file['type']);
        }
        
        // Vérifications de sécurité supplémentaires
        if ($this->isExecutable($file['name'])) {
            throw new Exception('Les fichiers exécutables ne sont pas autorisés');
        }
    }
    
    /**
     * Générer un nom de fichier unique
     */
    private function generateFileName(string $originalName): string {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Nettoyer le nom de base
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $baseName = substr($baseName, 0, 50); // Limiter la longueur
        
        // Ajouter un timestamp et un hash unique
        $timestamp = time();
        $hash = substr(md5(uniqid()), 0, 8);
        
        return "{$baseName}_{$timestamp}_{$hash}.{$extension}";
    }
    
    /**
     * Obtenir le répertoire d'upload selon le type
     */
    private function getUploadDirectory(string $uploadType): string {
        $baseDir = __DIR__ . '/../../public/uploads';
        
        switch ($uploadType) {
            case 'document':
                return $baseDir . '/documents';
            case 'avatar':
                return $baseDir . '/avatars';
            case 'announcement':
                return $baseDir . '/announcements';
            case 'training':
                return $baseDir . '/trainings';
            default:
                return $baseDir . '/general';
        }
    }
    
    /**
     * Obtenir l'URL publique d'un fichier
     */
    private function getFileUrl(string $fileName, string $uploadType): string {
        $baseUrl = rtrim(BASE_URL, '/') . '/uploads';
        
        switch ($uploadType) {
            case 'document':
                return $baseUrl . '/documents/' . $fileName;
            case 'avatar':
                return $baseUrl . '/avatars/' . $fileName;
            case 'announcement':
                return $baseUrl . '/announcements/' . $fileName;
            case 'training':
                return $baseUrl . '/trainings/' . $fileName;
            default:
                return $baseUrl . '/general/' . $fileName;
        }
    }
    
    /**
     * Extensions autorisées par type d'upload
     */
    private function getAllowedExtensions(string $uploadType): array {
        switch ($uploadType) {
            case 'document':
                return ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
            case 'avatar':
                return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            case 'announcement':
            case 'training':
                return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'];
            default:
                return ALLOWED_FILE_TYPES;
        }
    }
    
    /**
     * Types MIME autorisés par type d'upload
     */
    private function getAllowedMimeTypes(string $uploadType): array {
        switch ($uploadType) {
            case 'document':
                return [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain',
                    'text/rtf'
                ];
            case 'avatar':
            case 'announcement':
            case 'training':
                return [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'application/pdf'
                ];
            default:
                return [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'application/pdf', 'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain'
                ];
        }
    }
    
    /**
     * Vérifier si un fichier est une image
     */
    private function isImage(string $mimeType): bool {
        return strpos($mimeType, 'image/') === 0;
    }
    
    /**
     * Vérifier si un fichier est potentiellement exécutable
     */
    private function isExecutable(string $fileName): bool {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $executableExts = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'php', 'pl', 'py', 'sh'];
        
        return in_array($extension, $executableExts);
    }
    
    /**
     * Créer une miniature pour une image
     */
    private function createThumbnail(string $imagePath, string $uploadType): ?string {
        try {
            $thumbnailDir = $this->getUploadDirectory($uploadType) . '/thumbnails';
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }
            
            $fileName = basename($imagePath);
            $thumbnailPath = $thumbnailDir . '/' . $fileName;
            
            // Utiliser la fonction helper si elle existe
            if (function_exists('createThumbnail')) {
                $success = createThumbnail($imagePath, $thumbnailPath, 300, 300);
                if ($success) {
                    return str_replace($this->getUploadDirectory($uploadType), 
                                     $this->getFileUrl('', $uploadType), 
                                     $thumbnailPath);
                }
            }
            
            return null;
            
        } catch (Exception $e) {
            // En cas d'erreur, on continue sans miniature
            return null;
        }
    }
    
    /**
     * Obtenir le chemin d'une miniature
     */
    private function getThumbnailPath(string $fileName, string $uploadType): string {
        return $this->getUploadDirectory($uploadType) . '/thumbnails/' . $fileName;
    }
    
    /**
     * Formater une taille de fichier
     */
    private function formatFileSize(int $size): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        
        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}