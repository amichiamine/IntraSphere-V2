<?php
/**
 * Script de test de connexion à la base de données
 * À supprimer après installation
 */

// Charger la configuration
if (file_exists('.env')) {
    $envVars = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envVars as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$driver = $_ENV['DB_DRIVER'] ?? 'mysql';
$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbname = $_ENV['DB_NAME'] ?? 'intrasphere';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Connexion DB - IntraSphere</title>
    <style>
        body { 
            font-family: monospace; background: #1a1a1a; color: #00ff00; 
            padding: 20px; margin: 0; 
        }
        .result { 
            background: #000; padding: 20px; border-radius: 5px; 
            border: 1px solid #333; margin: 10px 0; 
        }
        .success { color: #00ff00; }
        .error { color: #ff4444; }
        .info { color: #4444ff; }
    </style>
</head>
<body>
    <h1>🔧 Test de Connexion Base de Données</h1>
    
    <div class="result">
        <h2>Configuration Détectée :</h2>
        <p class="info">Driver: <?= htmlspecialchars($driver) ?></p>
        <p class="info">Host: <?= htmlspecialchars($host) ?>:<?= htmlspecialchars($port) ?></p>
        <p class="info">Database: <?= htmlspecialchars($dbname) ?></p>
        <p class="info">User: <?= htmlspecialchars($username) ?></p>
        <p class="info">Password: <?= empty($password) ? '[VIDE]' : '[***]' ?></p>
    </div>
    
    <div class="result">
        <h2>Test de Connexion :</h2>
        <?php
        try {
            $dsn = $driver === 'mysql' ? 
                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4" :
                "pgsql:host={$host};port={$port};dbname={$dbname}";
            
            echo "<p class='info'>DSN: {$dsn}</p>";
            
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            echo "<p class='success'>✅ CONNEXION RÉUSSIE !</p>";
            
            // Test de requête simple
            $version = $pdo->query("SELECT VERSION()")->fetchColumn();
            echo "<p class='success'>Version DB: {$version}</p>";
            
            // Vérifier les tables IntraSphere
            $tables = [];
            if ($driver === 'mysql') {
                $stmt = $pdo->query("SHOW TABLES");
                while ($row = $stmt->fetch()) {
                    $tables[] = array_values($row)[0];
                }
            } else {
                $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
                while ($row = $stmt->fetch()) {
                    $tables[] = $row['table_name'];
                }
            }
            
            if (empty($tables)) {
                echo "<p class='error'>❌ Aucune table trouvée - Installation requise</p>";
            } else {
                echo "<p class='success'>✅ Tables détectées: " . implode(', ', $tables) . "</p>";
                
                // Test données utilisateurs
                if (in_array('users', $tables)) {
                    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                    echo "<p class='success'>👥 Utilisateurs: {$userCount}</p>";
                }
            }
            
        } catch (PDOException $e) {
            echo "<p class='error'>❌ ERREUR DE CONNEXION !</p>";
            echo "<p class='error'>Message: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p class='error'>Code: " . $e->getCode() . "</p>";
            
            // Suggestions de résolution
            echo "<h3>💡 Suggestions :</h3>";
            echo "<ul>";
            echo "<li>Vérifiez que la base de données '{$dbname}' existe</li>";
            echo "<li>Vérifiez les identifiants utilisateur/mot de passe</li>";
            echo "<li>Vérifiez que le serveur DB est démarré</li>";
            if ($driver === 'mysql') {
                echo "<li>Vérifiez que l'extension PDO_MySQL est installée</li>";
            } else {
                echo "<li>Vérifiez que l'extension PDO_PostgreSQL est installée</li>";
            }
            echo "</ul>";
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ ERREUR GÉNÉRALE: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
    
    <div class="result">
        <h2>Extensions PHP :</h2>
        <?php
        $extensions = ['pdo', 'pdo_mysql', 'pdo_pgsql'];
        foreach ($extensions as $ext) {
            $loaded = extension_loaded($ext);
            $status = $loaded ? '✅' : '❌';
            $class = $loaded ? 'success' : 'error';
            echo "<p class='{$class}'>{$status} {$ext}</p>";
        }
        ?>
    </div>
    
    <div class="result">
        <h2>Actions :</h2>
        <p><a href="install.php" style="color: #00ff00;">🚀 Lancer l'installation</a></p>
        <p><a href="/" style="color: #4444ff;">🏠 Accueil IntraSphere</a></p>
        <p style="color: #ff4444;">⚠️ Supprimez ce fichier après vérification</p>
    </div>
</body>
</html>