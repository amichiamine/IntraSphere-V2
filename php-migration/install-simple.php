<?php
/**
 * IntraSphere PHP - Installation Simplifiée
 * Version compatible hébergement mutualisé
 */

// Configuration d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Empêcher la réexécution
if (file_exists(__DIR__ . '/.installed')) {
    die('IntraSphere est déjà installé.');
}

// Variables
$step = $_GET['step'] ?? 'check';
$errors = [];
$success = [];

// Fonctions utilitaires
function checkRequirements() {
    $checks = [
        'php' => version_compare(PHP_VERSION, '7.4', '>='),
        'pdo' => extension_loaded('pdo'),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'writable' => is_writable(__DIR__)
    ];
    return $checks;
}

function testDatabase($host, $dbname, $user, $pass) {
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ['success' => true, 'pdo' => $pdo];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function createTables($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id VARCHAR(50) PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password TEXT NOT NULL,
        name VARCHAR(255) NOT NULL,
        role ENUM('admin', 'moderator', 'employee') DEFAULT 'employee',
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS announcements (
        id VARCHAR(50) PRIMARY KEY,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        type ENUM('info', 'important', 'event') DEFAULT 'info',
        author_id VARCHAR(50),
        author_name VARCHAR(255) NOT NULL,
        is_important BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS documents (
        id VARCHAR(50) PRIMARY KEY,
        title TEXT NOT NULL,
        description TEXT,
        category ENUM('regulation', 'policy', 'guide', 'procedure') NOT NULL,
        file_name TEXT NOT NULL,
        file_url TEXT NOT NULL,
        version VARCHAR(20) DEFAULT '1.0',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    ";
    
    $statements = array_filter(explode(';', $sql));
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
}

// Traitement des étapes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 'install') {
        try {
            // Validation
            $required = ['db_host', 'db_name', 'db_user', 'admin_username', 'admin_password'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Le champ {$field} est requis");
                }
            }
            
            // Test BDD
            $dbTest = testDatabase($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_password'] ?? '');
            if (!$dbTest['success']) {
                throw new Exception("Connexion BDD échouée: " . $dbTest['error']);
            }
            
            // Création tables
            createTables($dbTest['pdo']);
            
            // Création admin
            $adminId = uniqid('admin_');
            $hashedPassword = password_hash($_POST['admin_password'], PASSWORD_BCRYPT);
            $stmt = $dbTest['pdo']->prepare("INSERT INTO users (id, username, password, name, role) VALUES (?, ?, ?, ?, 'admin')");
            $stmt->execute([$adminId, $_POST['admin_username'], $hashedPassword, $_POST['admin_name'] ?? 'Administrateur']);
            
            // Création fichier .env
            $env = "DB_HOST=" . $_POST['db_host'] . "\n";
            $env .= "DB_NAME=" . $_POST['db_name'] . "\n";
            $env .= "DB_USER=" . $_POST['db_user'] . "\n";
            $env .= "DB_PASSWORD=" . $_POST['db_password'] . "\n";
            $env .= "SESSION_SECRET=" . bin2hex(random_bytes(16)) . "\n";
            file_put_contents(__DIR__ . '/.env', $env);
            
            // Création .htaccess basique
            $htaccess = "RewriteEngine On\nRewriteRule ^(.*)$ index.php [QSA,L]\n";
            file_put_contents(__DIR__ . '/.htaccess', $htaccess);
            
            // Marquer comme installé
            file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
            
            $step = 'success';
            $success[] = 'Installation réussie !';
            
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $step = 'form';
        }
    }
}

$checks = checkRequirements();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation IntraSphere PHP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding: 20px; background: linear-gradient(45deg, #8B5CF6, #A78BFA); color: white; border-radius: 8px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { background: #8B5CF6; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #7C3AED; }
        .error { background: #fee; border: 1px solid #fcc; color: #c33; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .success { background: #efe; border: 1px solid #cfc; color: #363; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .check { margin: 10px 0; }
        .check.ok { color: green; }
        .check.error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($step === 'check' || $step === 'form'): ?>
            <div class="header">
                <h1>Installation IntraSphere PHP</h1>
                <p>Configuration de votre plateforme d'apprentissage</p>
            </div>
            
            <h2>Vérification des prérequis</h2>
            <div class="check <?= $checks['php'] ? 'ok' : 'error' ?>">
                PHP 7.4+ : <?= $checks['php'] ? '✓ OK (' . PHP_VERSION . ')' : '✗ KO (' . PHP_VERSION . ')' ?>
            </div>
            <div class="check <?= $checks['pdo'] ? 'ok' : 'error' ?>">
                Extension PDO : <?= $checks['pdo'] ? '✓ OK' : '✗ KO' ?>
            </div>
            <div class="check <?= $checks['pdo_mysql'] ? 'ok' : 'error' ?>">
                Extension PDO MySQL : <?= $checks['pdo_mysql'] ? '✓ OK' : '✗ KO' ?>
            </div>
            <div class="check <?= $checks['writable'] ? 'ok' : 'error' ?>">
                Permissions écriture : <?= $checks['writable'] ? '✓ OK' : '✗ KO' ?>
            </div>
            
            <?php if (!$checks['php'] || !$checks['pdo'] || !$checks['pdo_mysql']): ?>
                <div class="error">
                    <strong>Erreurs critiques détectées.</strong><br>
                    Contactez votre hébergeur pour corriger ces problèmes.
                </div>
            <?php else: ?>
                
                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <div class="error"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <form method="POST" action="?step=install">
                    <h2>Configuration Base de Données</h2>
                    
                    <div class="form-group">
                        <label>Serveur de base de données</label>
                        <input type="text" name="db_host" value="localhost" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom de la base de données</label>
                        <input type="text" name="db_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Utilisateur</label>
                        <input type="text" name="db_user" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="db_password">
                    </div>
                    
                    <h2>Compte Administrateur</h2>
                    
                    <div class="form-group">
                        <label>Nom d'utilisateur admin</label>
                        <input type="text" name="admin_username" value="admin" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" name="admin_name" value="Administrateur">
                    </div>
                    
                    <div class="form-group">
                        <label>Mot de passe admin</label>
                        <input type="password" name="admin_password" required>
                    </div>
                    
                    <button type="submit" class="btn">Installer IntraSphere</button>
                </form>
                
            <?php endif; ?>
            
        <?php elseif ($step === 'success'): ?>
            <div class="header">
                <h1>🎉 Installation Réussie !</h1>
                <p>IntraSphere PHP est maintenant opérationnel</p>
            </div>
            
            <?php foreach ($success as $msg): ?>
                <div class="success"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; ?>
            
            <h2>Informations de connexion</h2>
            <p><strong>URL :</strong> <a href="index.php">Accéder à IntraSphere</a></p>
            <p><strong>Utilisateur :</strong> <?= htmlspecialchars($_POST['admin_username'] ?? 'admin') ?></p>
            <p><strong>Mot de passe :</strong> [Celui que vous avez saisi]</p>
            
            <a href="index.php" class="btn">Accéder à l'application</a>
            
        <?php endif; ?>
    </div>
</body>
</html>