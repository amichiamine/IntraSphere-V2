<?php
/**
 * IntraSphere - PHP Pure Migration - Version Corrigée
 * Point d'entrée principal de l'application
 */

// Démarrage du buffer de sortie
ob_start();

// Configuration des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0); // Masquer en production
ini_set('log_errors', 1);

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration sécurisée
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Définition des constantes de base
define('APP_ROOT', __DIR__);
define('CONFIG_PATH', APP_ROOT . '/config');
define('CONTROLLERS_PATH', APP_ROOT . '/src/controllers');
define('MODELS_PATH', APP_ROOT . '/src/models');
define('VIEWS_PATH', APP_ROOT . '/views');
define('UPLOADS_PATH', APP_ROOT . '/public/uploads');
define('LOGS_PATH', APP_ROOT . '/logs');
define('SESSION_LIFETIME', 3600);

// Chargement de l'environnement
function loadEnvironment() {
    $envFile = APP_ROOT . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}

// Connexion base de données
function getDatabase() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $dbname = $_ENV['DB_NAME'] ?? 'intrasphere';
            $username = $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? '';
            
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log("Erreur connexion DB: " . $e->getMessage());
            showError("Erreur de connexion à la base de données");
        }
    }
    
    return $pdo;
}

// Fonction d'affichage d'erreur
function showError($message) {
    http_response_code(500);
    include VIEWS_PATH . '/error/500.php';
    exit;
}

// Chargement de l'environnement
loadEnvironment();

// Chargement de la configuration
require_once CONFIG_PATH . '/bootstrap.php';

// Router simple
class SimpleRouter {
    private $routes = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Nettoyer l'URI
        $uri = parse_url($uri, PHP_URL_PATH);
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptDir !== '/') {
            $uri = substr($uri, strlen($scriptDir));
        }
        $uri = '/' . ltrim($uri, '/');
        
        if (isset($this->routes[$method][$uri])) {
            $callback = $this->routes[$method][$uri];
            if (is_callable($callback)) {
                $callback();
            } else {
                list($controller, $method) = explode('@', $callback);
                $controllerInstance = new $controller();
                $controllerInstance->$method();
            }
        } else {
            http_response_code(404);
            include VIEWS_PATH . '/error/404.php';
        }
    }
}

// Configuration des routes
$router = new SimpleRouter();

// Routes d'authentification
$router->get('/', 'AuthController@loginForm');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');

// Routes du tableau de bord
$router->get('/dashboard', 'DashboardController@index');

// Routes des annonces
$router->get('/announcements', 'AnnouncementsController@index');
$router->get('/announcements/create', 'AnnouncementsController@create');

// Routes des documents
$router->get('/documents', 'DocumentsController@index');
$router->get('/documents/upload', 'DocumentsController@upload');

// Routes des messages
$router->get('/messages', 'MessagesController@index');
$router->get('/messages/compose', 'MessagesController@compose');

// Routes des formations
$router->get('/trainings', 'TrainingsController@index');
$router->get('/trainings/create', 'TrainingsController@create');

// Routes d'administration
$router->get('/admin', 'AdminController@index');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/permissions', 'AdminController@permissions');

// API Routes
$router->get('/api/stats', 'Api\\AdminController@stats');
$router->get('/api/announcements', 'Api\\AnnouncementsController@index');

// Dispatch du router
try {
    $router->dispatch();
} catch (Exception $e) {
    error_log("Erreur de routage: " . $e->getMessage());
    showError("Erreur interne du serveur");
}