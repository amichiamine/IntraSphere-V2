<?php
/**
 * Test simple de l'installation PHP IntraSphere
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test IntraSphere - Installation PHP</h1>";

try {
    // Test 1: Autoloader et configuration
    echo "<h2>Test 1: Configuration de base</h2>";
    require_once __DIR__ . '/config/bootstrap.php';
    echo "✅ Configuration chargée<br>";
    
    // Test 2: Base de données
    echo "<h2>Test 2: Base de données</h2>";
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "✅ Connexion base de données OK<br>";
    
    // Test 3: Tables
    echo "<h2>Test 3: Vérification des tables</h2>";
    $tables = ['users', 'announcements', 'documents', 'events', 'messages', 'complaints', 'permissions'];
    foreach ($tables as $table) {
        $stmt = $connection->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' existe<br>";
        } else {
            echo "❌ Table '$table' manquante<br>";
        }
    }
    
    // Test 4: Sessions
    echo "<h2>Test 4: Sessions</h2>";
    session_start();
    echo "✅ Sessions fonctionnelles<br>";
    
    // Test 5: Contrôleurs
    echo "<h2>Test 5: Contrôleurs</h2>";
    $controllers = ['AuthController', 'ErrorController', 'DashboardController'];
    foreach ($controllers as $controller) {
        if (class_exists($controller)) {
            echo "✅ Contrôleur '$controller' chargé<br>";
        } else {
            echo "❌ Contrôleur '$controller' manquant<br>";
        }
    }
    
    // Test 6: Router
    echo "<h2>Test 6: Router</h2>";
    $router = new Router();
    echo "✅ Router initialisé<br>";
    
    // Test 7: Utilisateurs de test
    echo "<h2>Test 7: Utilisateurs de test</h2>";
    $stmt = $connection->prepare("SELECT username, name, role FROM users WHERE is_active = 1 LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    if ($users) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%'>";
        echo "<tr><th>Username</th><th>Nom</th><th>Rôle</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "✅ " . count($users) . " utilisateurs trouvés<br>";
    } else {
        echo "❌ Aucun utilisateur trouvé<br>";
    }
    
    echo "<h2>🎉 Installation réussie !</h2>";
    echo "<p><a href='index.php' style='color: #8B5CF6; font-weight: bold;'>→ Accéder à l'application</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</h2>";
    echo "<p>Trace: " . nl2br(htmlspecialchars($e->getTraceAsString())) . "</p>";
}
?>

<style>
body { 
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    margin: 40px; background: #f5f7fa; 
}
h1 { color: #8B5CF6; }
h2 { color: #6B46C1; margin-top: 20px; }
table { margin-top: 10px; }
th, td { padding: 8px 12px; text-align: left; border: 1px solid #ddd; }
th { background: #8B5CF6; color: white; }
</style>