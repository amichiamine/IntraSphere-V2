<?php
/**
 * Configuration générale de l'application
 */

// Configuration générale
define('APP_NAME', 'IntraSphere');
define('APP_VERSION', '2.0.0-PHP');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', APP_ENV === 'development');

// URLs et chemins
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost');
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Sécurité
define('SECRET_KEY', $_ENV['SECRET_KEY'] ?? 'changeme-in-production');
define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);

// Pagination
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);

// Upload de fichiers
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_FILE_TYPES', [
    'jpg', 'jpeg', 'png', 'gif', 'webp', // Images
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', // Documents
    'mp4', 'avi', 'mov', 'wmv', // Vidéos
    'mp3', 'wav', 'ogg', // Audio
    'zip', 'rar', '7z', 'tar', 'gz' // Archives
]);

// Emails
define('MAIL_FROM', $_ENV['MAIL_FROM'] ?? 'noreply@intrasphere.local');
define('MAIL_FROM_NAME', $_ENV['MAIL_FROM_NAME'] ?? APP_NAME);

// Sessions
define('SESSION_LIFETIME', 3600); // 1 heure
define('SESSION_NAME', 'INTRASPHERE_SESSION');

// Cache
define('CACHE_ENABLED', true);
define('CACHE_TTL', 300); // 5 minutes

// Logs
define('LOG_ENABLED', true);
define('LOG_LEVEL', APP_DEBUG ? 'DEBUG' : 'ERROR');

// Rôles utilisateurs
define('USER_ROLES', [
    'employee' => 'Employé',
    'moderator' => 'Modérateur', 
    'admin' => 'Administrateur'
]);

// Permissions système
define('PERMISSIONS', [
    'manage_announcements' => 'Gérer les annonces',
    'manage_documents' => 'Gérer les documents',
    'manage_events' => 'Gérer les événements',
    'manage_users' => 'Gérer les utilisateurs',
    'manage_trainings' => 'Gérer les formations',
    'validate_topics' => 'Valider les sujets',
    'validate_posts' => 'Valider les posts',
    'manage_employee_categories' => 'Gérer les catégories d\'employés'
]);

// Types de contenu
define('CONTENT_TYPES', [
    'video' => 'Vidéo',
    'image' => 'Image',
    'document' => 'Document',
    'audio' => 'Audio'
]);

// Catégories d'annonces
define('ANNOUNCEMENT_TYPES', [
    'info' => 'Information',
    'important' => 'Important',
    'event' => 'Événement',
    'formation' => 'Formation'
]);

// Priorités des réclamations
define('COMPLAINT_PRIORITIES', [
    'low' => 'Faible',
    'medium' => 'Moyenne',
    'high' => 'Élevée',
    'urgent' => 'Urgente'
]);

// Statuts des réclamations
define('COMPLAINT_STATUSES', [
    'open' => 'Ouverte',
    'in_progress' => 'En cours',
    'resolved' => 'Résolue',
    'closed' => 'Fermée'
]);
?>