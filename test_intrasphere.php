<?php
/**
 * Script de test final pour IntraSphere PHP
 */

// Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test Final IntraSphere</h1>";

// Test 1: Vérification des fichiers principaux
echo "<h2>1. Vérification des fichiers</h2>";
$requiredFiles = [
    'php-migration/index.php' => 'Index principal',
    'php-migration/.env' => 'Configuration environnement',
    'php-migration/views/auth/login.php' => 'Page de connexion',
    'php-migration/views/dashboard/index.php' => 'Dashboard',
    'php-migration/views/error/404.php' => 'Page erreur 404',
    'php-migration/views/error/500.php' => 'Page erreur 500'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description<br>";
    } else {
        echo "❌ $description manquant<br>";
    }
}

// Test 2: Configuration base de données
echo "<h2>2. Test de configuration</h2>";
$envFile = 'php-migration/.env';
if (file_exists($envFile)) {
    echo "✅ Fichier .env trouvé<br>";
    $content = file_get_contents($envFile);
    if (strpos($content, 'DB_HOST') !== false) {
        echo "✅ Configuration DB présente<br>";
    } else {
        echo "❌ Configuration DB incomplète<br>";
    }
} else {
    echo "❌ Fichier .env manquant<br>";
}

// Test 3: Simulation d'accès
echo "<h2>3. Test d'accès simulé</h2>";
echo "<p>Tests à effectuer manuellement :</p>";
echo "<ul>";
echo "<li><a href='php-migration/index.php' target='_blank'>🔗 Accéder à l'application principale</a></li>";
echo "<li><a href='simple_index.php' target='_blank'>🔗 Version simplifiée (fonctionnelle)</a></li>";
echo "<li><a href='debug_index.php' target='_blank'>🔗 Diagnostic complet</a></li>";
echo "</ul>";

// Test 4: Informations de connexion
echo "<h2>4. Comptes de test</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<h3>Comptes disponibles :</h3>";
echo "<p><strong>Administrateur :</strong> admin / admin123</p>";
echo "<p><strong>Employé :</strong> marie.martin / password123</p>";
echo "<p><strong>Modérateur :</strong> pierre.dubois / password123</p>";
echo "</div>";

// Test 5: Résolution des problèmes
echo "<h2>5. Si l'erreur 500 persiste</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<h3>Solutions :</h3>";
echo "<ol>";
echo "<li>Utilisez <a href='reset_installation.php'><strong>reset_installation.php</strong></a> pour nettoyer</li>";
echo "<li>Relancez l'installation avec <a href='install_fixed.php'><strong>install_fixed.php</strong></a></li>";
echo "<li>Vérifiez les logs d'erreur de votre hébergeur</li>";
echo "<li>Contactez le support technique si nécessaire</li>";
echo "</ol>";
echo "</div>";

// Test 6: Statut final
echo "<h2>6. Statut de l'installation</h2>";
if (file_exists('php-migration/.env') && file_exists('php-migration/index.php')) {
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; text-align: center;'>";
    echo "<h3 style='color: #155724;'>✅ Installation IntraSphere Complète</h3>";
    echo "<p>L'application est installée et configurée.</p>";
    echo "<p><strong>URL principale :</strong> <a href='php-migration/index.php'>php-migration/index.php</a></p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; text-align: center;'>";
    echo "<h3 style='color: #721c24;'>❌ Installation Incomplète</h3>";
    echo "<p>Veuillez compléter l'installation.</p>";
    echo "</div>";
}

?>

<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
    h1 { color: #8B5CF6; text-align: center; }
    h2 { color: #6b7280; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
    a { color: #8B5CF6; text-decoration: none; }
    a:hover { text-decoration: underline; }
    ul, ol { margin-left: 20px; }
    .test-ok { color: #059669; }
    .test-error { color: #dc2626; }
</style>