<?php
/**
 * Index PHP Simplifié pour Test de Compatibilité
 * Version minimaliste pour identifier les problèmes
 */

// Configuration des erreurs pour diagnostic
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de session sécurisé
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Information de base
$startTime = microtime(true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntraSphere - Test Simple</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
            margin: 0; padding: 20px; min-height: 100vh;
        }
        .container { 
            max-width: 600px; margin: 0 auto; 
            background: rgba(255, 255, 255, 0.95); 
            padding: 40px; border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        .logo { text-align: center; font-size: 2rem; color: #8B5CF6; margin-bottom: 20px; }
        .status { padding: 15px; border-radius: 8px; margin: 15px 0; }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .info { background: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }
        .details { background: #f9fafb; padding: 15px; border-radius: 8px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px; border: 1px solid #e5e7eb; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; }
        .btn { 
            display: inline-block; padding: 12px 24px; background: #8B5CF6; 
            color: white; text-decoration: none; border-radius: 8px; 
            font-weight: 600; margin: 10px 5px;
        }
        .btn:hover { background: #7C3AED; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🚀 IntraSphere</div>
        <h2>Test de Compatibilité Simplifié</h2>
        
        <div class="status success">
            ✅ PHP fonctionne correctement !<br>
            Version: <?= PHP_VERSION ?><br>
            Serveur: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Non défini' ?>
        </div>

        <div class="details">
            <h3>Informations Système</h3>
            <table>
                <tr><td><strong>Date/Heure</strong></td><td><?= date('Y-m-d H:i:s') ?></td></tr>
                <tr><td><strong>Timezone</strong></td><td><?= date_default_timezone_get() ?></td></tr>
                <tr><td><strong>Memory Limit</strong></td><td><?= ini_get('memory_limit') ?></td></tr>
                <tr><td><strong>Max Execution Time</strong></td><td><?= ini_get('max_execution_time') ?>s</td></tr>
                <tr><td><strong>Upload Max Size</strong></td><td><?= ini_get('upload_max_filesize') ?></td></tr>
                <tr><td><strong>Post Max Size</strong></td><td><?= ini_get('post_max_size') ?></td></tr>
            </table>
        </div>

        <div class="details">
            <h3>Extensions PHP Critiques</h3>
            <table>
                <?php
                $criticalExtensions = [
                    'pdo' => 'Base de données PDO',
                    'pdo_mysql' => 'MySQL via PDO',
                    'openssl' => 'Chiffrement SSL',
                    'mbstring' => 'Chaînes multi-octets',
                    'fileinfo' => 'Informations fichiers',
                    'json' => 'JSON'
                ];
                
                foreach ($criticalExtensions as $ext => $desc) {
                    $loaded = extension_loaded($ext);
                    $status = $loaded ? '✅' : '❌';
                    $class = $loaded ? 'success' : 'error';
                    echo "<tr><td>{$desc}</td><td style='color: " . ($loaded ? 'green' : 'red') . ";'>{$status} " . ($loaded ? 'Disponible' : 'Manquant') . "</td></tr>";
                }
                ?>
            </table>
        </div>

        <?php
        // Test de base de données si les paramètres sont disponibles
        if (file_exists(__DIR__ . '/.env')) {
            echo '<div class="details">';
            echo '<h3>Test de Configuration</h3>';
            echo '<p>Fichier .env détecté - Configuration disponible</p>';
            echo '</div>';
        }
        ?>

        <div class="details">
            <h3>Test de Session</h3>
            <?php
            try {
                $_SESSION['test_time'] = time();
                echo "<p style='color: green;'>✅ Sessions PHP fonctionnelles</p>";
                echo "<p>ID de session: " . session_id() . "</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Erreur de session: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>

        <div class="details">
            <h3>Test d'Écriture de Fichier</h3>
            <?php
            try {
                $testFile = __DIR__ . '/test_permissions.txt';
                $content = "Test d'écriture - " . date('Y-m-d H:i:s');
                
                if (file_put_contents($testFile, $content) !== false) {
                    echo "<p style='color: green;'>✅ Écriture de fichier réussie</p>";
                    if (file_exists($testFile)) {
                        unlink($testFile);
                        echo "<p style='color: green;'>✅ Suppression de fichier réussie</p>";
                    }
                } else {
                    echo "<p style='color: red;'>❌ Impossible d'écrire dans le dossier</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>

        <div class="info">
            <h3>Prochaines Étapes</h3>
            <p>Si tous les tests sont verts, votre serveur est compatible avec IntraSphere.</p>
            <p>Vous pouvez maintenant procéder à l'installation complète.</p>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="install.php" class="btn">🚀 Lancer l'Installation</a>
            <a href="debug_index.php" class="btn" style="background: #6b7280;">🔍 Diagnostic Détaillé</a>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 0.9rem;">
            Temps d'exécution: <?= round((microtime(true) - $startTime) * 1000, 2) ?>ms
        </div>
    </div>
</body>
</html>