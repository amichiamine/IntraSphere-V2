<?php
/**
 * Script de diagnostic IntraSphere
 * Aide à identifier les problèmes d'installation
 */

// Configuration pour afficher toutes les erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>🔍 Diagnostic IntraSphere PHP</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;}</style>";

echo "<h2>1. Informations PHP</h2>";
echo "<p><strong>Version PHP:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>PHP OK:</strong> " . (version_compare(PHP_VERSION, '7.4', '>=') ? '<span class="ok">✓ Oui</span>' : '<span class="error">✗ Non (7.4+ requis)</span>') . "</p>";

echo "<h2>2. Extensions PHP</h2>";
$extensions = ['pdo', 'pdo_mysql', 'json', 'openssl', 'session', 'mbstring'];
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<p><strong>{$ext}:</strong> " . ($loaded ? '<span class="ok">✓ Chargée</span>' : '<span class="error">✗ Manquante</span>') . "</p>";
}

echo "<h2>3. Permissions</h2>";
echo "<p><strong>Dossier accessible en écriture:</strong> " . (is_writable(__DIR__) ? '<span class="ok">✓ Oui</span>' : '<span class="error">✗ Non</span>') . "</p>";
echo "<p><strong>Chemin actuel:</strong> " . __DIR__ . "</p>";

echo "<h2>4. Fichiers requis</h2>";
$requiredFiles = ['sql/create_tables.sql', 'config/database.php'];
foreach ($requiredFiles as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo "<p><strong>{$file}:</strong> " . ($exists ? '<span class="ok">✓ Présent</span>' : '<span class="error">✗ Manquant</span>') . "</p>";
}

echo "<h2>5. Variables serveur</h2>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Non défini') . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Non défini') . "</p>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . "</p>";

echo "<h2>6. Test d'inclusion des fichiers</h2>";
try {
    if (file_exists(__DIR__ . '/config/database.php')) {
        echo "<p><strong>database.php:</strong> <span class='ok'>✓ Inclusion réussie</span></p>";
    } else {
        echo "<p><strong>database.php:</strong> <span class='error'>✗ Fichier manquant</span></p>";
    }
} catch (Exception $e) {
    echo "<p><strong>Erreur inclusion:</strong> <span class='error'>" . $e->getMessage() . "</span></p>";
}

echo "<h2>7. Test de classe simple</h2>";
try {
    class TestClass {
        public function test() {
            return "OK";
        }
    }
    $test = new TestClass();
    echo "<p><strong>Classe PHP:</strong> <span class='ok'>✓ " . $test->test() . "</span></p>";
} catch (Exception $e) {
    echo "<p><strong>Erreur classe:</strong> <span class='error'>" . $e->getMessage() . "</span></p>";
}

echo "<h2>8. Log d'erreurs PHP</h2>";
$errorLog = ini_get('error_log');
echo "<p><strong>Fichier log:</strong> " . ($errorLog ?: 'Par défaut') . "</p>";

// Tenter de lire les dernières erreurs si possible
if ($errorLog && file_exists($errorLog)) {
    $errors = file_get_contents($errorLog);
    $recentErrors = array_slice(explode("\n", $errors), -10);
    echo "<pre>" . implode("\n", $recentErrors) . "</pre>";
} else {
    echo "<p>Aucun log d'erreur spécifique trouvé.</p>";
}

echo "<h2>9. Test de sortie</h2>";
echo "<p>Si vous voyez ce message, PHP fonctionne correctement.</p>";

echo "<hr>";
echo "<p><strong>Prochaines étapes :</strong></p>";
echo "<ul>";
echo "<li>Si tout est OK ci-dessus, le problème vient peut-être de la taille du script install.php</li>";
echo "<li>Vérifiez les logs d'erreur de votre hébergeur</li>";
echo "<li>Contactez votre hébergeur si les extensions manquent</li>";
echo "</ul>";

echo "<p><a href='install-simple.php' style='background:#007cba;color:white;padding:10px;text-decoration:none;border-radius:4px;'>Essayer l'installation simplifiée</a></p>";
?>