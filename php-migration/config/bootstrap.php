<?php
/**
 * Bootstrap de l'application PHP
 * Configuration générale et autoloader
 */

// Définition des constantes
define('APP_ROOT', dirname(__DIR__));
define('CONFIG_PATH', APP_ROOT . '/config');
define('CONTROLLERS_PATH', APP_ROOT . '/src/controllers');
define('MODELS_PATH', APP_ROOT . '/src/models');
define('VIEWS_PATH', APP_ROOT . '/views');
define('UPLOADS_PATH', APP_ROOT . '/public/uploads');

// Autoloader simple
spl_autoload_register(function ($className) {
    // Conversion du namespace en chemin
    $className = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $className);
    $className = ltrim($className, DIRECTORY_SEPARATOR);
    
    // Tentatives de chargement dans différents dossiers
    $directories = [
        APP_ROOT . '/src/',
        APP_ROOT . '/src/controllers/',
        APP_ROOT . '/src/models/',
        APP_ROOT . '/src/middleware/',
        APP_ROOT . '/src/services/',
        APP_ROOT . '/src/utils/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Configuration de la base de données
require_once CONFIG_PATH . '/database.php';

// Configuration générale
require_once CONFIG_PATH . '/app.php';

// Helpers globaux
require_once APP_ROOT . '/src/utils/helpers.php';

// Timezone
date_default_timezone_set('Europe/Paris');

// Configuration des sessions
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
?>