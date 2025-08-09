<?php
/**
 * Assistant d'installation IntraSphere PHP
 * Interface web pour configurer la base de données
 */

// Empêcher l'accès direct en production
if (file_exists('.env') && getenv('APP_ENV') === 'production') {
    die('Installation disabled in production mode');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

if ($_POST) {
    switch ($step) {
        case 1: // Test connexion DB
            try {
                $driver = $_POST['db_driver'];
                $host = $_POST['db_host'];
                $port = $_POST['db_port'];
                $dbname = $_POST['db_name'];
                $username = $_POST['db_user'];
                $password = $_POST['db_password'];
                
                $dsn = $driver === 'mysql' ? 
                    "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4" :
                    "pgsql:host={$host};port={$port};dbname={$dbname}";
                
                $pdo = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                
                // Sauvegarder config
                $envContent = "DB_DRIVER={$driver}\n";
                $envContent .= "DB_HOST={$host}\n";
                $envContent .= "DB_PORT={$port}\n";
                $envContent .= "DB_NAME={$dbname}\n";
                $envContent .= "DB_USER={$username}\n";
                $envContent .= "DB_PASSWORD={$password}\n";
                $envContent .= "APP_ENV=production\n";
                $envContent .= "SESSION_SECRET=" . bin2hex(random_bytes(32)) . "\n";
                
                file_put_contents('.env', $envContent);
                
                $success = 'Connexion réussie ! Configuration sauvegardée.';
                $step = 2;
                
            } catch (Exception $e) {
                $error = 'Erreur de connexion : ' . $e->getMessage();
            }
            break;
            
        case 2: // Installation des tables
            try {
                require_once 'config/database.php';
                $db = Database::getInstance();
                
                // Lire et exécuter le script SQL
                $sql = file_get_contents('sql/create_tables.sql');
                $statements = explode(';', $sql);
                
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        $db->getConnection()->exec($statement);
                    }
                }
                
                // Données de démonstration
                if (file_exists('sql/insert_demo_data.sql')) {
                    $demoSql = file_get_contents('sql/insert_demo_data.sql');
                    $demoStatements = explode(';', $demoSql);
                    
                    foreach ($demoStatements as $statement) {
                        $statement = trim($statement);
                        if (!empty($statement)) {
                            $db->getConnection()->exec($statement);
                        }
                    }
                }
                
                $success = 'Installation terminée avec succès !';
                $step = 3;
                
            } catch (Exception $e) {
                $error = 'Erreur d\'installation : ' . $e->getMessage();
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation IntraSphere</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0; padding: 20px; min-height: 100vh;
        }
        .container { 
            max-width: 600px; margin: 0 auto; 
            background: rgba(255,255,255,0.95); 
            border-radius: 20px; padding: 40px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        h1 { color: #4c1d95; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
        input, select { 
            width: 100%; padding: 12px; border: 2px solid #e5e7eb; 
            border-radius: 8px; font-size: 14px; box-sizing: border-box;
        }
        input:focus, select:focus { outline: none; border-color: #8b5cf6; }
        .btn { 
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white; padding: 12px 30px; border: none; 
            border-radius: 8px; cursor: pointer; font-size: 16px; width: 100%;
        }
        .btn:hover { transform: translateY(-2px); }
        .error { background: #fef2f2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .success { background: #f0fdf4; color: #16a34a; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .step { display: flex; justify-content: center; margin-bottom: 30px; }
        .step-item { 
            width: 30px; height: 30px; border-radius: 50%; 
            background: #e5e7eb; color: #6b7280; display: flex; 
            align-items: center; justify-content: center; margin: 0 10px;
        }
        .step-item.active { background: #8b5cf6; color: white; }
        .credentials { background: #f8fafc; padding: 20px; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Installation IntraSphere</h1>
        
        <div class="step">
            <div class="step-item <?= $step >= 1 ? 'active' : '' ?>">1</div>
            <div class="step-item <?= $step >= 2 ? 'active' : '' ?>">2</div>
            <div class="step-item <?= $step >= 3 ? 'active' : '' ?>">3</div>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <h2>Configuration Base de Données</h2>
            <form method="POST">
                <input type="hidden" name="step" value="1">
                
                <div class="form-group">
                    <label>Type de Base de Données</label>
                    <select name="db_driver" required>
                        <option value="mysql">MySQL</option>
                        <option value="pgsql">PostgreSQL</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Serveur</label>
                    <input type="text" name="db_host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label>Port</label>
                    <input type="text" name="db_port" value="3306" required>
                </div>
                
                <div class="form-group">
                    <label>Nom de la Base</label>
                    <input type="text" name="db_name" value="intrasphere" required>
                </div>
                
                <div class="form-group">
                    <label>Utilisateur</label>
                    <input type="text" name="db_user" required>
                </div>
                
                <div class="form-group">
                    <label>Mot de Passe</label>
                    <input type="password" name="db_password">
                </div>
                
                <button type="submit" class="btn">Tester la Connexion</button>
            </form>
            
        <?php elseif ($step == 2): ?>
            <h2>Installation des Tables</h2>
            <p>La connexion à la base de données fonctionne. Cliquez pour installer les tables :</p>
            
            <form method="POST">
                <input type="hidden" name="step" value="2">
                <button type="submit" class="btn">Installer les Tables</button>
            </form>
            
        <?php elseif ($step == 3): ?>
            <h2>✅ Installation Terminée</h2>
            <p>IntraSphere a été installé avec succès !</p>
            
            <div class="credentials">
                <h3>Identifiants par Défaut :</h3>
                <p><strong>Administrateur :</strong> admin / admin123</p>
                <p><strong>Employé :</strong> marie.martin / password123</p>
            </div>
            
            <div style="margin-top: 30px; text-align: center;">
                <a href="/" class="btn" style="display: inline-block; text-decoration: none;">
                    Accéder à IntraSphere
                </a>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: #fef3c7; border-radius: 8px;">
                <strong>⚠️ Sécurité :</strong> Supprimez le fichier <code>install.php</code> après installation.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>