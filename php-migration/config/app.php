<?php
/**
 * Configuration de l'application IntraSphere
 */

// Configuration générale
define('APP_NAME', 'IntraSphere');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', APP_ENV === 'development');

// Configuration des erreurs
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configuration des sessions
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Timezone
date_default_timezone_set('Europe/Paris');

// Configuration de l'upload
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif']);

// Configuration de sécurité
define('BCRYPT_COST', 12);
define('SESSION_REGENERATE_INTERVAL', 300); // 5 minutes

// Headers de sécurité par défaut
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY'); 
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

if (isset($_SERVER['HTTPS'])) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
?>