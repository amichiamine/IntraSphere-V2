<?php
/**
 * Script de test complet pour IntraSphere
 * Diagnostic avancé et vérification de l'installation
 */

// Configuration stricte des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Headers de sécurité de base
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$startTime = microtime(true);
$tests = [];
$errors = [];
$warnings = [];

// Fonction de test standardisée
function runTest($name, $callback, $critical = false) {
    global $tests, $errors, $warnings;
    
    try {
        $result = $callback();
        $tests[$name] = [
            'status' => 'success',
            'message' => $result,
            'critical' => $critical
        ];
    } catch (Exception $e) {
        $tests[$name] = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'critical' => $critical
        ];
        
        if ($critical) {
            $errors[] = "{$name}: {$e->getMessage()}";
        } else {
            $warnings[] = "{$name}: {$e->getMessage()}";
        }
    }
}

// Tests de compatibilité PHP
runTest('Version PHP', function() {
    $version = PHP_VERSION;
    if (version_compare($version, '7.4.0', '<')) {
        throw new Exception("Version {$version} incompatible (7.4+ requis)");
    }
    return "Version {$version} - Compatible";
}, true);

runTest('Extension PDO', function() {
    if (!extension_loaded('pdo')) {
        throw new Exception('Extension PDO manquante');
    }
    return 'PDO disponible';
}, true);

runTest('Extension PDO MySQL', function() {
    if (!extension_loaded('pdo_mysql')) {
        throw new Exception('Extension PDO MySQL manquante');
    }
    return 'PDO MySQL disponible';
}, true);

runTest('Extension OpenSSL', function() {
    if (!extension_loaded('openssl')) {
        throw new Exception('Extension OpenSSL manquante - Requis pour la sécurité');
    }
    return 'OpenSSL disponible';
}, true);

runTest('Extension mbstring', function() {
    if (!extension_loaded('mbstring')) {
        throw new Exception('Extension mbstring manquante - Requis pour les caractères UTF-8');
    }
    return 'mbstring disponible';
}, true);

runTest('Extension JSON', function() {
    if (!extension_loaded('json')) {
        throw new Exception('Extension JSON manquante');
    }
    return 'JSON disponible';
}, true);

// Tests optionnels mais recommandés
runTest('Extension fileinfo', function() {
    if (!extension_loaded('fileinfo')) {
        throw new Exception('Extension fileinfo manquante - Recommandée pour la validation de fichiers');
    }
    return 'fileinfo disponible';
}, false);

runTest('Extension GD', function() {
    if (!extension_loaded('gd')) {
        throw new Exception('Extension GD manquante - Optionnelle pour le traitement d\'images');
    }
    return 'GD disponible pour les images';
}, false);

// Tests de configuration PHP
runTest('Memory Limit', function() {
    $limit = ini_get('memory_limit');
    $limitBytes = return_bytes($limit);
    if ($limitBytes > 0 && $limitBytes < 64 * 1024 * 1024) { // 64MB minimum
        throw new Exception("Memory limit trop bas: {$limit} (64MB minimum recommandé)");
    }
    return "Memory limit: {$limit}";
}, false);

runTest('Max Execution Time', function() {
    $time = ini_get('max_execution_time');
    if ($time > 0 && $time < 30) {
        throw new Exception("Max execution time trop bas: {$time}s (30s minimum recommandé)");
    }
    return "Max execution time: {$time}s";
}, false);

// Test de permissions
runTest('Permissions d\'écriture', function() {
    if (!is_writable(__DIR__)) {
        throw new Exception('Le dossier n\'est pas inscriptible - Vérifiez les permissions (755 recommandé)');
    }
    
    // Test d'écriture effective
    $testFile = __DIR__ . '/test_write_' . uniqid() . '.txt';
    if (file_put_contents($testFile, 'test') === false) {
        throw new Exception('Impossible d\'écrire un fichier de test');
    }
    
    if (!unlink($testFile)) {
        throw new Exception('Impossible de supprimer le fichier de test');
    }
    
    return 'Permissions d\'écriture correctes';
}, true);

// Test de sessions
runTest('Sessions PHP', function() {
    if (session_status() === PHP_SESSION_NONE) {
        if (!session_start()) {
            throw new Exception('Impossible de démarrer une session PHP');
        }
    }
    
    $_SESSION['test_key'] = 'test_value';
    if (!isset($_SESSION['test_key']) || $_SESSION['test_key'] !== 'test_value') {
        throw new Exception('Les variables de session ne fonctionnent pas correctement');
    }
    
    return 'Sessions PHP fonctionnelles';
}, true);

// Test de mod_rewrite (approximatif)
runTest('mod_rewrite Apache', function() {
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (!in_array('mod_rewrite', $modules)) {
            throw new Exception('mod_rewrite n\'est pas activé - Requis pour le routing URL');
        }
        return 'mod_rewrite activé';
    }
    
    // Test alternatif avec .htaccess
    $htaccessFile = __DIR__ . '/.htaccess';
    if (!file_exists($htaccessFile)) {
        throw new Exception('Fichier .htaccess manquant - mod_rewrite impossible à vérifier');
    }
    
    return 'mod_rewrite probablement disponible (impossible à vérifier précisément)';
}, false);

// Test de structure de fichiers
runTest('Structure de fichiers', function() {
    $requiredFiles = [
        'install.php' => 'Assistant d\'installation',
        '.env.example' => 'Configuration d\'environnement',
        '.htaccess' => 'Configuration Apache'
    ];
    
    $missing = [];
    foreach ($requiredFiles as $file => $description) {
        if (!file_exists(__DIR__ . '/' . $file)) {
            $missing[] = "{$file} ({$description})";
        }
    }
    
    if (!empty($missing)) {
        throw new Exception('Fichiers manquants: ' . implode(', ', $missing));
    }
    
    return 'Tous les fichiers requis sont présents';
}, true);

// Test de connexion base de données (si .env existe)
runTest('Configuration base de données', function() {
    $envFile = __DIR__ . '/.env';
    if (!file_exists($envFile)) {
        throw new Exception('Fichier .env non trouvé - Configuration requise');
    }
    
    // Charger les variables d'environnement
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $config = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value);
        }
    }
    
    // Test de connexion
    $host = $config['DB_HOST'] ?? '';
    $port = $config['DB_PORT'] ?? '3306';
    $dbname = $config['DB_NAME'] ?? '';
    $username = $config['DB_USER'] ?? '';
    $password = $config['DB_PASSWORD'] ?? '';
    
    if (empty($host) || empty($dbname) || empty($username)) {
        throw new Exception('Configuration base de données incomplète dans .env');
    }
    
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        
        // Test simple
        $pdo->query('SELECT 1');
        
        return "Connexion base de données réussie ({$host}:{$port}/{$dbname})";
        
    } catch (PDOException $e) {
        throw new Exception("Erreur de connexion DB: " . $e->getMessage());
    }
}, false);

// Fonction utilitaire pour convertir memory_limit
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $num = (int) $val;
    switch($last) {
        case 'g': $num *= 1024;
        case 'm': $num *= 1024;
        case 'k': $num *= 1024;
    }
    return $num;
}

$executionTime = round((microtime(true) - $startTime) * 1000, 2);
$criticalErrors = array_filter($tests, function($test) { return $test['status'] === 'error' && $test['critical']; });
$totalTests = count($tests);
$successTests = count(array_filter($tests, function($test) { return $test['status'] === 'success'; }));

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Complet IntraSphere</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
            margin: 0; padding: 20px; min-height: 100vh;
        }
        .container { 
            max-width: 900px; margin: 0 auto; 
            background: rgba(255, 255, 255, 0.95); 
            padding: 40px; border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 2.5rem; color: #8B5CF6; margin-bottom: 10px; }
        .summary { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px; margin: 30px 0;
        }
        .stat-card { 
            text-align: center; padding: 20px; border-radius: 12px;
            background: #f8fafc; border: 1px solid #e2e8f0;
        }
        .stat-number { font-size: 2rem; font-weight: 700; margin-bottom: 5px; }
        .stat-label { color: #64748b; font-size: 0.9rem; }
        .success-number { color: #059669; }
        .error-number { color: #dc2626; }
        .warning-number { color: #d97706; }
        
        .test-results { margin: 30px 0; }
        .test-item { 
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px; border-bottom: 1px solid #e5e7eb;
            background: #fafafa; margin: 5px 0; border-radius: 8px;
        }
        .test-name { font-weight: 600; }
        .test-result { padding: 6px 12px; border-radius: 6px; font-size: 0.9rem; }
        .test-success { background: #d1fae5; color: #065f46; }
        .test-error { background: #fee2e2; color: #991b1b; }
        .test-warning { background: #fef3c7; color: #92400e; }
        
        .alert { padding: 20px; border-radius: 12px; margin: 20px 0; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
        
        .btn { 
            display: inline-block; padding: 14px 28px; background: #8B5CF6; 
            color: white; text-decoration: none; border-radius: 8px; 
            font-weight: 600; margin: 10px 5px; text-align: center;
        }
        .btn:hover { background: #7C3AED; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
        
        .details { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .system-info { font-family: monospace; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🧪 Test IntraSphere</div>
            <p>Diagnostic complet de compatibilité et configuration</p>
        </div>

        <div class="summary">
            <div class="stat-card">
                <div class="stat-number success-number"><?= $successTests ?></div>
                <div class="stat-label">Tests Réussis</div>
            </div>
            <div class="stat-card">
                <div class="stat-number error-number"><?= count($errors) ?></div>
                <div class="stat-label">Erreurs Critiques</div>
            </div>
            <div class="stat-card">
                <div class="stat-number warning-number"><?= count($warnings) ?></div>
                <div class="stat-label">Avertissements</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #8B5CF6;"><?= $executionTime ?>ms</div>
                <div class="stat-label">Temps d'Exécution</div>
            </div>
        </div>

        <?php if (empty($criticalErrors)): ?>
            <div class="alert alert-success">
                <h3>✅ Système Compatible !</h3>
                <p>Votre serveur répond à tous les prérequis critiques pour IntraSphere. Vous pouvez procéder à l'installation.</p>
            </div>
        <?php else: ?>
            <div class="alert alert-error">
                <h3>❌ Problèmes Critiques Détectés</h3>
                <p>Votre serveur présente des problèmes qui empêchent l'installation d'IntraSphere:</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Contactez votre hébergeur pour résoudre ces problèmes.</strong></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($warnings)): ?>
            <div class="alert alert-warning">
                <h3>⚠️ Avertissements</h3>
                <p>Points d'attention (non bloquants mais recommandés):</p>
                <ul>
                    <?php foreach ($warnings as $warning): ?>
                        <li><?= htmlspecialchars($warning) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="test-results">
            <h3>Résultats Détaillés</h3>
            <?php foreach ($tests as $name => $test): ?>
                <div class="test-item">
                    <span class="test-name"><?= htmlspecialchars($name) ?></span>
                    <span class="test-result test-<?= $test['status'] ?>">
                        <?= $test['status'] === 'success' ? '✅' : '❌' ?> 
                        <?= htmlspecialchars($test['message']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="details">
            <h3>Informations Système</h3>
            <div class="system-info">
                <strong>PHP Version:</strong> <?= PHP_VERSION ?><br>
                <strong>Serveur:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Non défini' ?><br>
                <strong>OS:</strong> <?= PHP_OS ?><br>
                <strong>SAPI:</strong> <?= php_sapi_name() ?><br>
                <strong>Document Root:</strong> <?= $_SERVER['DOCUMENT_ROOT'] ?? 'Non défini' ?><br>
                <strong>Script Path:</strong> <?= __FILE__ ?><br>
                <strong>Memory Usage:</strong> <?= round(memory_get_usage(true) / 1024 / 1024, 2) ?>MB<br>
                <strong>Peak Memory:</strong> <?= round(memory_get_peak_usage(true) / 1024 / 1024, 2) ?>MB
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <?php if (empty($criticalErrors)): ?>
                <a href="install.php" class="btn">🚀 Procéder à l'Installation</a>
            <?php else: ?>
                <span style="color: #6b7280;">Installation bloquée - Résolvez les erreurs critiques</span>
            <?php endif; ?>
            
            <a href="simple_index.php" class="btn btn-secondary">🔍 Test Simple</a>
            <a href="debug_index.php" class="btn btn-secondary">📊 Diagnostic Détaillé</a>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 0.9rem;">
            IntraSphere Diagnostic Suite v1.0 - Tests effectués en <?= $executionTime ?>ms
        </div>
    </div>
</body>
</html>