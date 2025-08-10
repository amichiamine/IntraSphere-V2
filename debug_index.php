<?php
/**
 * Script de diagnostic pour IntraSphere PHP
 * Permet d'identifier la cause de l'erreur 500
 */

// Affichage des erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Démarrage du buffer de sortie
ob_start();

echo "<h1>Diagnostic IntraSphere PHP</h1>";
echo "<h2>1. Test de base PHP</h2>";
echo "✅ PHP fonctionne - Version: " . PHP_VERSION . "<br>";

echo "<h2>2. Test des constantes et chemins</h2>";
define('APP_ROOT', __DIR__ . '/php-migration');
define('CONFIG_PATH', APP_ROOT . '/config');
define('CONTROLLERS_PATH', APP_ROOT . '/src/controllers');
define('MODELS_PATH', APP_ROOT . '/src/models');
define('VIEWS_PATH', APP_ROOT . '/views');
define('UPLOADS_PATH', APP_ROOT . '/public/uploads');
define('LOGS_PATH', APP_ROOT . '/logs');
define('SESSION_LIFETIME', 3600);

echo "✅ Constantes définies<br>";
echo "APP_ROOT: " . APP_ROOT . "<br>";

echo "<h2>3. Test d'existence des fichiers critiques</h2>";
$criticalFiles = [
    'config/bootstrap.php' => APP_ROOT . '/config/bootstrap.php',
    'config/database.php' => APP_ROOT . '/config/database.php',
    'config/app.php' => APP_ROOT . '/config/app.php',
    'src/Router.php' => APP_ROOT . '/src/Router.php',
    'src/utils/helpers.php' => APP_ROOT . '/src/utils/helpers.php',
    'src/controllers/AuthController.php' => APP_ROOT . '/src/controllers/AuthController.php',
    '.env' => APP_ROOT . '/.env'
];

foreach ($criticalFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name existe<br>";
    } else {
        echo "❌ $name manquant: $path<br>";
    }
}

echo "<h2>4. Test de chargement du fichier .env</h2>";
$envFile = APP_ROOT . '/.env';
if (file_exists($envFile)) {
    echo "✅ Fichier .env trouvé<br>";
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    echo "Variables trouvées:<br>";
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            echo "- " . trim($key) . "<br>";
        }
    }
} else {
    echo "❌ Fichier .env manquant<br>";
}

echo "<h2>5. Test de connexion base de données</h2>";
try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $dbname = $_ENV['DB_NAME'] ?? 'intrasphere';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    echo "Configuration DB:<br>";
    echo "- Host: $host<br>";
    echo "- Port: $port<br>";
    echo "- Database: $dbname<br>";
    echo "- User: $username<br>";
    
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "✅ Connexion base de données réussie<br>";
    
    // Test de requête simple
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Nombre d'utilisateurs: " . $result['count'] . "<br>";
    
} catch (PDOException $e) {
    echo "❌ Erreur base de données: " . $e->getMessage() . "<br>";
}

echo "<h2>6. Test d'autoloader et classes</h2>";

// Autoloader simple
spl_autoload_register(function ($className) {
    $className = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $className);
    $className = ltrim($className, DIRECTORY_SEPARATOR);
    
    $directories = [
        APP_ROOT . '/src/',
        APP_ROOT . '/src/controllers/',
        APP_ROOT . '/src/models/',
        APP_ROOT . '/src/utils/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            echo "✅ Classe chargée: $className depuis $file<br>";
            return;
        }
    }
    echo "❌ Classe non trouvée: $className<br>";
});

// Test de chargement des classes principales
$classesToTest = [
    'Router',
    'AuthController',
    'DashboardController',
    'BaseController',
    'BaseModel',
    'User'
];

foreach ($classesToTest as $className) {
    try {
        if (class_exists($className)) {
            echo "✅ Classe disponible: $className<br>";
        } else {
            echo "❌ Classe non disponible: $className<br>";
        }
    } catch (Throwable $e) {
        echo "❌ Erreur lors du test de $className: " . $e->getMessage() . "<br>";
    }
}

echo "<h2>7. Test de démarrage de session</h2>";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "✅ Session démarrée<br>";
    echo "Session ID: " . session_id() . "<br>";
} catch (Throwable $e) {
    echo "❌ Erreur session: " . $e->getMessage() . "<br>";
}

echo "<h2>8. Test du Router</h2>";
try {
    if (class_exists('Router')) {
        $router = new Router();
        echo "✅ Router instancié<br>";
        
        // Test d'ajout de route simple
        $router->addRoute('GET', '/test', function() {
            return "Test OK";
        });
        echo "✅ Route de test ajoutée<br>";
    }
} catch (Throwable $e) {
    echo "❌ Erreur Router: " . $e->getMessage() . "<br>";
}

echo "<h2>9. Suggestion de solution</h2>";
echo "Si tous les tests ci-dessus passent, l'erreur 500 vient probablement de:<br>";
echo "- Un contrôleur qui n'existe pas ou a une erreur de syntaxe<br>";
echo "- Un appel à une méthode inexistante<br>";
echo "- Une dépendance manquante<br>";
echo "- Un problème d'autoloader<br>";

echo "<h2>10. Prochaines étapes</h2>";
echo "1. Corrigez les erreurs identifiées ci-dessus<br>";
echo "2. Créez une version simplifiée de index.php<br>";
echo "3. Testez étape par étape<br>";

echo "<p><a href='simple_index.php'>Tester la version simplifiée</a></p>";

// Vider le buffer et afficher le contenu
ob_end_flush();
?>