<?php
/**
 * Script de test pour valider l'installation IntraSphere
 * Vérifie la base de données et les fonctionnalités de base
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$testResults = [];
$errors = [];

// Test de connexion base de données
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    if ($env) {
        try {
            $pdo = new PDO(
                "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']}", 
                $env['DB_USER'], 
                $env['DB_PASSWORD']
            );
            $testResults['database'] = 'OK - Connexion réussie';
            
            // Test des tables principales
            $tables = ['users', 'announcements', 'documents', 'system_settings'];
            foreach ($tables as $table) {
                $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
                if ($stmt) {
                    $count = $stmt->fetchColumn();
                    $testResults["table_{$table}"] = "OK - {$count} enregistrements";
                } else {
                    $errors[] = "Table {$table} introuvable";
                }
            }
            
            // Test utilisateur admin
            $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch();
            if ($admin) {
                $testResults['admin_user'] = "OK - Utilisateur admin: {$admin['username']}";
            } else {
                $errors[] = "Aucun utilisateur admin trouvé";
            }
            
        } catch (Exception $e) {
            $errors[] = "Erreur base de données: " . $e->getMessage();
        }
    } else {
        $errors[] = "Fichier .env non valide";
    }
} else {
    $errors[] = "Fichier .env manquant";
}

// Test des fichiers requis
$requiredFiles = [
    'index.php' => 'Point d\'entrée principal',
    'config/database.php' => 'Configuration base de données',
    'src/Router.php' => 'Système de routage',
    '.htaccess' => 'Configuration Apache (optionnel)'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $testResults["file_{$file}"] = "OK - {$description}";
    } else {
        if ($file === '.htaccess') {
            $testResults["file_{$file}"] = "MANQUANT - {$description} (optionnel)";
        } else {
            $errors[] = "Fichier manquant: {$file} - {$description}";
        }
    }
}

// Test des permissions
$testResults['permissions_write'] = is_writable(__DIR__) ? 'OK - Écriture autorisée' : 'ERREUR - Pas d\'écriture';
$testResults['permissions_read'] = is_readable(__DIR__) ? 'OK - Lecture autorisée' : 'ERREUR - Pas de lecture';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Installation IntraSphere</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding: 20px; background: linear-gradient(45deg, #8B5CF6, #A78BFA); color: white; border-radius: 8px; }
        .test-section { margin: 20px 0; }
        .test-section h3 { border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
        .test-item { display: flex; justify-content: space-between; padding: 10px; margin: 5px 0; border-radius: 4px; }
        .test-item.ok { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-item.warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .test-item.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .summary { padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .summary.success { background: #d4edda; border: 2px solid #28a745; color: #155724; }
        .summary.error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; }
        .btn { padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 5px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #8B5CF6; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #8B5CF6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Test d'Installation IntraSphere</h1>
            <p>Validation des composants et de la configuration</p>
        </div>

        <?php 
        $totalTests = count($testResults);
        $errorCount = count($errors);
        $successCount = $totalTests - $errorCount;
        ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $totalTests ?></div>
                <div>Tests effectués</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #28a745;"><?= $successCount ?></div>
                <div>Réussis</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #dc3545;"><?= $errorCount ?></div>
                <div>Erreurs</div>
            </div>
        </div>

        <?php if (empty($errors)): ?>
            <div class="summary success">
                <h2>✅ Installation Validée avec Succès</h2>
                <p>Tous les composants d'IntraSphere sont correctement installés et configurés.</p>
                <a href="index.php" class="btn btn-primary">Accéder à IntraSphere</a>
            </div>
        <?php else: ?>
            <div class="summary error">
                <h2>⚠️ Problèmes Détectés</h2>
                <p><?= $errorCount ?> erreur(s) doivent être corrigées avant utilisation.</p>
                <a href="clean-install.php" class="btn btn-secondary">Nettoyer et Réinstaller</a>
            </div>
        <?php endif; ?>

        <div class="test-section">
            <h3>🗄️ Base de Données</h3>
            <?php foreach ($testResults as $key => $result): ?>
                <?php if (strpos($key, 'database') === 0 || strpos($key, 'table_') === 0 || strpos($key, 'admin_') === 0): ?>
                    <div class="test-item ok">
                        <span><?= ucfirst(str_replace(['database', 'table_', 'admin_', '_'], ['Base de données', 'Table ', 'Admin ', ' '], $key)) ?></span>
                        <span><?= htmlspecialchars($result) ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="test-section">
            <h3>📁 Fichiers Système</h3>
            <?php foreach ($testResults as $key => $result): ?>
                <?php if (strpos($key, 'file_') === 0): ?>
                    <div class="test-item <?= strpos($result, 'MANQUANT') !== false ? 'warning' : 'ok' ?>">
                        <span><?= str_replace(['file_', '_'], ['', '/'], $key) ?></span>
                        <span><?= htmlspecialchars($result) ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="test-section">
            <h3>🔒 Permissions</h3>
            <?php foreach ($testResults as $key => $result): ?>
                <?php if (strpos($key, 'permissions_') === 0): ?>
                    <div class="test-item <?= strpos($result, 'ERREUR') !== false ? 'error' : 'ok' ?>">
                        <span><?= str_replace(['permissions_', '_'], ['Permissions ', ' '], $key) ?></span>
                        <span><?= htmlspecialchars($result) ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="test-section">
                <h3>❌ Erreurs à Corriger</h3>
                <?php foreach ($errors as $error): ?>
                    <div class="test-item error">
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 30px; text-align: center; color: #666; font-size: 14px;">
            <p>Test effectué le <?= date('d/m/Y à H:i:s') ?></p>
            <p>Version IntraSphere PHP - Installation <?= empty($errors) ? 'Validée' : 'à Corriger' ?></p>
        </div>
    </div>
</body>
</html>