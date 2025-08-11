<?php
/**
 * Script de diagnostic IntraSphere
 * Version simplifiée pour identifier les problèmes de configuration
 */

// Activer l'affichage des erreurs pour diagnostic
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Fonction de test sécurisée
function safeTest($label, $callback) {
    echo "<tr>";
    echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$label}</td>";
    try {
        $result = $callback();
        echo "<td style='padding: 8px; border: 1px solid #ddd; color: green;'>✓ {$result}</td>";
    } catch (Exception $e) {
        echo "<td style='padding: 8px; border: 1px solid #ddd; color: red;'>✗ " . htmlspecialchars($e->getMessage()) . "</td>";
    }
    echo "</tr>";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic IntraSphere</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f5f5f5; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic IntraSphere</h1>
        <p>Script de diagnostic pour identifier les problèmes de configuration.</p>
        
        <div class="info">
            <strong>Informations serveur :</strong><br>
            <strong>Date/Heure :</strong> <?= date('Y-m-d H:i:s') ?><br>
            <strong>Serveur :</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Non défini' ?><br>
            <strong>Host :</strong> <?= $_SERVER['HTTP_HOST'] ?? 'Non défini' ?><br>
            <strong>Script :</strong> <?= $_SERVER['SCRIPT_NAME'] ?? 'Non défini' ?>
        </div>

        <h2>Tests de Compatibilité</h2>
        <table>
            <thead>
                <tr>
                    <th>Test</th>
                    <th>Résultat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                safeTest('Version PHP', function() {
                    return PHP_VERSION . (version_compare(PHP_VERSION, '7.4.0', '>=') ? ' (Compatible)' : ' (Incompatible - 7.4+ requis)');
                });
                
                safeTest('Extension PDO', function() {
                    return extension_loaded('pdo') ? 'Disponible' : 'Manquante';
                });
                
                safeTest('Extension PDO MySQL', function() {
                    return extension_loaded('pdo_mysql') ? 'Disponible' : 'Manquante';
                });
                
                safeTest('Extension OpenSSL', function() {
                    return extension_loaded('openssl') ? 'Disponible' : 'Manquante';
                });
                
                safeTest('Extension mbstring', function() {
                    return extension_loaded('mbstring') ? 'Disponible' : 'Manquante';
                });
                
                safeTest('Sessions PHP', function() {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    return 'Fonctionnelles';
                });
                
                safeTest('Dossier inscriptible', function() {
                    return is_writable(__DIR__) ? 'Oui' : 'Non - Permissions insuffisantes';
                });
                
                safeTest('memory_limit', function() {
                    return ini_get('memory_limit') ?: 'Non défini';
                });
                
                safeTest('max_execution_time', function() {
                    return ini_get('max_execution_time') ?: 'Non défini';
                });
                
                safeTest('upload_max_filesize', function() {
                    return ini_get('upload_max_filesize') ?: 'Non défini';
                });
                
                safeTest('Fonction mail()', function() {
                    return function_exists('mail') ? 'Disponible' : 'Indisponible';
                });
                
                safeTest('mod_rewrite (approx)', function() {
                    if (function_exists('apache_get_modules')) {
                        return in_array('mod_rewrite', apache_get_modules()) ? 'Actif' : 'Inactif';
                    }
                    return 'Impossible à détecter - Test .htaccess requis';
                });
                ?>
            </tbody>
        </table>

        <h2>Tests de Fichiers</h2>
        <table>
            <thead>
                <tr>
                    <th>Fichier</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $files = [
                    'install.php' => 'Assistant d\'installation',
                    'index.php' => 'Point d\'entrée principal',
                    '.htaccess' => 'Configuration Apache',
                    '.env.example' => 'Configuration d\'environnement',
                    'config/app.php' => 'Configuration application',
                    'src/Router.php' => 'Router principal'
                ];
                
                foreach ($files as $file => $description) {
                    safeTest("{$description} ({$file})", function() use ($file) {
                        if (file_exists(__DIR__ . '/' . $file)) {
                            $size = filesize(__DIR__ . '/' . $file);
                            return "Présent ({$size} octets)";
                        }
                        return 'Manquant';
                    });
                }
                ?>
            </tbody>
        </table>

        <h2>Configuration PHP Détaillée</h2>
        <table>
            <thead>
                <tr>
                    <th>Directive</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $directives = [
                    'display_errors',
                    'log_errors',
                    'error_log',
                    'max_input_time',
                    'post_max_size',
                    'allow_url_fopen',
                    'safe_mode',
                    'open_basedir'
                ];
                
                foreach ($directives as $directive) {
                    safeTest($directive, function() use ($directive) {
                        $value = ini_get($directive);
                        if ($value === false) return 'Non défini';
                        if ($value === '') return 'Vide';
                        return $value;
                    });
                }
                ?>
            </tbody>
        </table>

        <h2>Test de Création de Fichier</h2>
        <?php
        try {
            $testFile = __DIR__ . '/test_write.txt';
            $testContent = 'Test d\'écriture - ' . date('Y-m-d H:i:s');
            
            if (file_put_contents($testFile, $testContent) !== false) {
                echo "<p class='success'>✓ Écriture de fichier réussie</p>";
                if (file_exists($testFile)) {
                    unlink($testFile);
                    echo "<p class='success'>✓ Suppression de fichier réussie</p>";
                }
            } else {
                echo "<p class='error'>✗ Échec de l'écriture de fichier</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Erreur lors du test d'écriture: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>

        <h2>Variables d'Environnement Serveur</h2>
        <table>
            <thead>
                <tr>
                    <th>Variable</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serverVars = [
                    'DOCUMENT_ROOT',
                    'SERVER_NAME',
                    'REQUEST_METHOD',
                    'QUERY_STRING',
                    'REQUEST_URI',
                    'SCRIPT_FILENAME',
                    'PATH_INFO'
                ];
                
                foreach ($serverVars as $var) {
                    safeTest($var, function() use ($var) {
                        return $_SERVER[$var] ?? 'Non défini';
                    });
                }
                ?>
            </tbody>
        </table>

        <h2>Extensions PHP Chargées</h2>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <?php
            $extensions = get_loaded_extensions();
            sort($extensions);
            echo implode(', ', $extensions);
            ?>
        </div>

        <div class="info">
            <strong>Instructions de débogage :</strong><br>
            1. Si des extensions PHP manquent, contactez votre hébergeur<br>
            2. Si les permissions sont insuffisantes, définissez chmod 755 pour les dossiers et 644 pour les fichiers<br>
            3. Si mod_rewrite est inactif, activez-le dans votre panneau de contrôle<br>
            4. Vérifiez les logs d'erreur de votre serveur pour plus de détails
        </div>

        <p><a href="simple_index.php" style="background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">→ Tester avec l'index simplifié</a></p>
    </div>
</body>
</html>