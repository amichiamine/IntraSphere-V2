<?php
/**
 * IntraSphere - PHP Pure Migration - Version Corrigée
 * Point d'entrée principal de l'application
 * Version simplifiée qui évite l'erreur 500
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
define('APP_ROOT', __DIR__ . '/php-migration');
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

// Fonctions utilitaires
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    if (ob_get_level()) ob_end_clean();
    header('Location: ' . $url);
    exit;
}

function jsonResponse($data, $status = 200) {
    if (ob_get_level()) ob_end_clean();
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function hasRole($role) {
    $user = currentUser();
    return $user && $user['role'] === $role;
}

function showError($message) {
    ob_end_clean();
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erreur - IntraSphere</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
            .error-container { max-width: 600px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
            .error-title { color: #dc2626; font-size: 24px; margin-bottom: 20px; }
            .error-message { color: #666; margin-bottom: 30px; }
            .btn { background: #667eea; color: white; padding: 12px 24px; border: none; border-radius: 6px; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1 class="error-title">Erreur</h1>
            <p class="error-message"><?= h($message) ?></p>
            <a href="simple_index.php" class="btn">Version simplifiée</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Chargement de l'environnement
loadEnvironment();

// Récupération de la base de données
$db = getDatabase();

// Router simple
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Nettoyage de l'URI (supprimer le préfixe de dossier si nécessaire)
$uri = str_replace('/intrasphere', '', $uri);
if (empty($uri)) $uri = '/';

try {
    // Routes d'authentification
    if ($method === 'POST' && $uri === '/login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            jsonResponse(['success' => true, 'redirect' => '/dashboard']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Identifiants incorrects'], 401);
        }
    }
    
    if ($method === 'POST' && $uri === '/logout') {
        session_destroy();
        jsonResponse(['success' => true, 'redirect' => '/']);
    }
    
    // Routes API simples
    if (strpos($uri, '/api/') === 0) {
        if (!isLoggedIn() && $uri !== '/api/auth/me') {
            jsonResponse(['error' => 'Non authentifié'], 401);
        }
        
        switch ($uri) {
            case '/api/auth/me':
                if (isLoggedIn()) {
                    jsonResponse(['user' => currentUser()]);
                } else {
                    jsonResponse(['message' => 'Not authenticated'], 401);
                }
                break;
                
            case '/api/stats':
                $stats = [
                    'totalUsers' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
                    'totalAnnouncements' => $db->query("SELECT COUNT(*) FROM announcements")->fetchColumn(),
                    'totalDocuments' => $db->query("SELECT COUNT(*) FROM documents")->fetchColumn(),
                    'totalMessages' => $db->query("SELECT COUNT(*) FROM messages")->fetchColumn(),
                    'totalTrainings' => $db->query("SELECT COUNT(*) FROM trainings")->fetchColumn(),
                    'totalComplaints' => $db->query("SELECT COUNT(*) FROM complaints")->fetchColumn()
                ];
                jsonResponse($stats);
                break;
                
            case '/api/announcements':
                $announcements = $db->query("
                    SELECT * FROM announcements 
                    ORDER BY is_important DESC, created_at DESC 
                    LIMIT 10
                ")->fetchAll();
                jsonResponse($announcements);
                break;
                
            default:
                jsonResponse(['error' => 'Route API non trouvée'], 404);
        }
    }
    
    // Middleware d'authentification pour les pages
    if (!isLoggedIn() && $uri !== '/' && $uri !== '/login') {
        redirect('/');
    }
    
    // Routes des pages
    switch ($uri) {
        case '/':
        case '/login':
            if (isLoggedIn()) {
                redirect('/dashboard');
            }
            include VIEWS_PATH . '/auth/login.php';
            break;
            
        case '/dashboard':
            include VIEWS_PATH . '/dashboard/index.php';
            break;
            
        case '/announcements':
            include VIEWS_PATH . '/announcements/index.php';
            break;
            
        case '/documents':
            include VIEWS_PATH . '/documents/index.php';
            break;
            
        case '/messages':
            include VIEWS_PATH . '/messages/index.php';
            break;
            
        case '/trainings':
            include VIEWS_PATH . '/trainings/index.php';
            break;
            
        case '/admin':
            if (!hasRole('admin')) {
                redirect('/dashboard');
            }
            include VIEWS_PATH . '/admin/index.php';
            break;
            
        default:
            http_response_code(404);
            include VIEWS_PATH . '/error/404.php';
    }
    
} catch (Exception $e) {
    error_log("Erreur application: " . $e->getMessage());
    showError("Une erreur est survenue. Veuillez réessayer.");
}

// Vider le buffer si encore actif
if (ob_get_level()) {
    ob_end_flush();
}
?>