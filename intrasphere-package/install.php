<?php
/**
 * Script d'installation automatisé pour IntraSphere PHP
 * Version plug & play pour hébergement mutualisé
 * Version: 1.0.0
 */

// Démarrage du buffer de sortie
ob_start();

// Configuration des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Constantes d'installation
define('INSTALL_ROOT', __DIR__);
define('LOG_FILE', INSTALL_ROOT . '/installation.log');
define('VERSION', '1.0.0');

class IntraSphereInstaller {
    private $steps = [
        1 => 'Vérification de l\'environnement',
        2 => 'Configuration de la base de données',
        3 => 'Création des tables',
        4 => 'Configuration de l\'application',
        5 => 'Finalisation'
    ];
    
    private $hostingTemplates = [
        'cpanel' => [
            'name' => 'cPanel (Hébergement mutualisé)',
            'host' => 'localhost',
            'port' => '3306',
            'driver' => 'mysql',
            'dbname_pattern' => '{user}_{dbname}',
            'username_pattern' => '{user}_{dbname}'
        ],
        'ovh' => [
            'name' => 'OVH Hébergement Mutualisé',
            'host' => 'mysql-{dbname}.hosting.ovh.net',
            'port' => '3306',
            'driver' => 'mysql',
            'dbname_pattern' => '{dbname}',
            'username_pattern' => '{dbname}'
        ],
        'ionos' => [
            'name' => '1&1 / Ionos',
            'host' => 'db{id}.hosting.1and1.com',
            'port' => '3306',
            'driver' => 'mysql',
            'dbname_pattern' => 'db{id}',
            'username_pattern' => 'dbo{id}'
        ],
        'local' => [
            'name' => 'Développement Local (XAMPP/WAMP)',
            'host' => 'localhost',
            'port' => '3306',
            'driver' => 'mysql',
            'dbname_pattern' => '{dbname}',
            'username_pattern' => 'root'
        ],
        'custom' => [
            'name' => 'Configuration personnalisée',
            'host' => '',
            'port' => '3306',
            'driver' => 'mysql',
            'dbname_pattern' => '{dbname}',
            'username_pattern' => '{username}'
        ]
    ];
    
    public function run() {
        $currentStep = $_SESSION['install_step'] ?? 1;
        
        if (isset($_POST['action'])) {
            $this->handleAction($_POST['action']);
            return;
        }
        
        $this->showHeader();
        
        switch ($currentStep) {
            case 1:
                $this->showEnvironmentCheck();
                break;
            case 2:
                $this->showDatabaseConfig();
                break;
            case 3:
                $this->showTableCreation();
                break;
            case 4:
                $this->showAppConfig();
                break;
            case 5:
                $this->showCompletion();
                break;
            default:
                $this->showEnvironmentCheck();
        }
        
        $this->showFooter();
    }
    
    private function handleAction($action) {
        switch ($action) {
            case 'check_environment':
                $this->checkEnvironment();
                break;
            case 'test_database':
                $this->testDatabase();
                break;
            case 'configure_database':
                $this->configureDatabase();
                break;
            case 'create_tables':
                $this->createTables();
                break;
            case 'configure_app':
                $this->configureApp();
                break;
            case 'finalize':
                $this->finalizeInstallation();
                break;
        }
    }
    
    private function showHeader() {
        ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation IntraSphere <?= VERSION ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: white;
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 1.2rem;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            margin-bottom: 30px;
        }
        
        .progress-bar {
            background: #e5e7eb;
            border-radius: 8px;
            height: 8px;
            margin: 20px 0;
            overflow: hidden;
        }
        
        .progress-fill {
            background: linear-gradient(90deg, #8B5CF6, #7C3AED);
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            margin: 5px;
            min-width: 120px;
        }
        
        .step.active {
            background: #8B5CF6;
            color: white;
        }
        
        .step.completed {
            background: #059669;
            color: white;
        }
        
        .step.pending {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        .form-group {
            margin: 20px 0;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8B5CF6;
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }
        
        .btn-primary {
            background: #8B5CF6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #7C3AED;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-success {
            background: #059669;
            color: white;
        }
        
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        
        .check-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
        }
        
        .check-item.success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .check-item.error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .check-item.warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .check-icon {
            margin-right: 10px;
            font-weight: bold;
        }
        
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
            
            .steps {
                flex-direction: column;
            }
            
            .container {
                padding: 10px;
            }
            
            .card {
                padding: 20px;
            }
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(139, 92, 246, 0.3);
            border-radius: 50%;
            border-top-color: #8B5CF6;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .code-block {
            background: #1f2937;
            color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 14px;
            overflow-x: auto;
            margin: 15px 0;
        }
        
        .template-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .template-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .template-card:hover {
            border-color: #8B5CF6;
            background: #f8fafc;
        }
        
        .template-card.selected {
            border-color: #8B5CF6;
            background: #ede9fe;
        }
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ IntraSphere</h1>
            <p>Installation automatisée - Version <?= VERSION ?></p>
        </div>
        
        <div class="card">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= (($_SESSION['install_step'] ?? 1) / 5) * 100 ?>%"></div>
            </div>
            
            <div class="steps">
                <?php foreach ($this->steps as $num => $title): ?>
                    <div class="step <?= $this->getStepClass($num) ?>">
                        <div><?= $num ?></div>
                        <div><?= $title ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
    }
    
    private function getStepClass($stepNum) {
        $currentStep = $_SESSION['install_step'] ?? 1;
        if ($stepNum < $currentStep) return 'completed';
        if ($stepNum == $currentStep) return 'active';
        return 'pending';
    }
    
    private function showEnvironmentCheck() {
        ?>
        <h2>🔍 Vérification de l'environnement</h2>
        <p>Vérification des prérequis pour l'installation d'IntraSphere.</p>
        
        <div id="environment-checks">
            <div class="alert alert-info">
                <strong>Vérification en cours...</strong> Veuillez patienter pendant la vérification de votre environnement.
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <button type="button" class="btn btn-primary" onclick="checkEnvironment()">
                <span class="loading" style="display: none;"></span>
                Vérifier l'environnement
            </button>
        </div>
        
        <script>
        function checkEnvironment() {
            const btn = event.target;
            const loading = btn.querySelector('.loading');
            loading.style.display = 'inline-block';
            btn.disabled = true;
            
            fetch('install.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=check_environment'
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('environment-checks').innerHTML = data.html;
                if (data.success) {
                    setTimeout(() => {
                        window.location.href = 'install.php';
                    }, 2000);
                }
            })
            .finally(() => {
                loading.style.display = 'none';
                btn.disabled = false;
            });
        }
        </script>
        <?php
    }
    
    private function checkEnvironment() {
        $checks = [];
        $allPassed = true;
        
        // Vérification PHP
        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '7.4.0', '>=');
        $checks[] = [
            'name' => 'Version PHP',
            'status' => $phpOk ? 'success' : 'error',
            'message' => $phpOk ? "PHP $phpVersion ✓" : "PHP $phpVersion (7.4+ requis)",
            'required' => true
        ];
        if (!$phpOk) $allPassed = false;
        
        // Extensions PHP requises
        $extensions = ['pdo', 'pdo_mysql', 'json', 'session', 'mbstring'];
        foreach ($extensions as $ext) {
            $loaded = extension_loaded($ext);
            $checks[] = [
                'name' => "Extension $ext",
                'status' => $loaded ? 'success' : 'error',
                'message' => $loaded ? "Extension $ext activée ✓" : "Extension $ext manquante",
                'required' => true
            ];
            if (!$loaded) $allPassed = false;
        }
        
        // Permissions de fichiers
        $paths = [
            '.' => 'Dossier racine',
            './config' => 'Dossier config',
            './logs' => 'Dossier logs',
            './public/uploads' => 'Dossier uploads'
        ];
        
        foreach ($paths as $path => $name) {
            if (!file_exists($path)) {
                @mkdir($path, 0755, true);
            }
            $writable = is_writable($path);
            $checks[] = [
                'name' => $name,
                'status' => $writable ? 'success' : 'warning',
                'message' => $writable ? "Écriture autorisée ✓" : "Permissions limitées",
                'required' => false
            ];
        }
        
        // Générer le HTML
        $html = '';
        foreach ($checks as $check) {
            $html .= "<div class='check-item {$check['status']}'>";
            $html .= "<span class='check-icon'>" . ($check['status'] === 'success' ? '✅' : ($check['status'] === 'error' ? '❌' : '⚠️')) . "</span>";
            $html .= "<div>";
            $html .= "<strong>{$check['name']}</strong><br>";
            $html .= "{$check['message']}";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        if ($allPassed) {
            $html .= "<div class='alert alert-success'>";
            $html .= "<strong>✅ Environnement compatible !</strong> Vous pouvez continuer l'installation.";
            $html .= "</div>";
            $_SESSION['install_step'] = 2;
        } else {
            $html .= "<div class='alert alert-error'>";
            $html .= "<strong>❌ Problèmes détectés</strong> Veuillez corriger les erreurs avant de continuer.";
            $html .= "</div>";
        }
        
        $this->jsonResponse(['success' => $allPassed, 'html' => $html]);
    }
    
    private function showDatabaseConfig() {
        ?>
        <h2>🗄️ Configuration de la base de données</h2>
        <p>Configurez la connexion à votre base de données selon votre hébergeur.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="configure_database">
            
            <div class="form-group">
                <label>Type d'hébergement :</label>
                <div class="template-selector">
                    <?php foreach ($this->hostingTemplates as $key => $template): ?>
                        <div class="template-card" onclick="selectTemplate('<?= $key ?>')">
                            <h4><?= htmlspecialchars($template['name']) ?></h4>
                            <small><?= $key === 'local' ? 'XAMPP, WAMP, MAMP' : ($key === 'custom' ? 'Autre hébergeur' : 'Hébergement mutualisé') ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="hosting_type" id="hosting_type" required>
            </div>
            
            <div id="db-config" class="hidden">
                <div class="grid">
                    <div class="form-group">
                        <label>Serveur de base de données :</label>
                        <input type="text" name="db_host" id="db_host" placeholder="localhost" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Port :</label>
                        <input type="number" name="db_port" id="db_port" value="3306" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom de la base de données :</label>
                        <input type="text" name="db_name" id="db_name" placeholder="intrasphere" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom d'utilisateur :</label>
                        <input type="text" name="db_user" id="db_user" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mot de passe :</label>
                    <input type="password" name="db_password" id="db_password">
                </div>
                
                <div id="template-help" class="alert alert-info" style="display: none;">
                    <!-- Aide contextuelle selon le template -->
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="button" class="btn btn-secondary" onclick="testConnection()">
                        Tester la connexion
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Configurer la base de données
                    </button>
                </div>
            </div>
        </form>
        
        <div id="test-result"></div>
        
        <script>
        function selectTemplate(type) {
            document.querySelectorAll('.template-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            document.getElementById('hosting_type').value = type;
            document.getElementById('db-config').classList.remove('hidden');
            
            const templates = <?= json_encode($this->hostingTemplates) ?>;
            const template = templates[type];
            
            document.getElementById('db_host').value = template.host;
            document.getElementById('db_port').value = template.port;
            
            // Aide contextuelle
            const helpDiv = document.getElementById('template-help');
            let helpText = '';
            
            switch(type) {
                case 'cpanel':
                    helpText = '<strong>cPanel :</strong> Utilisez les informations de votre panneau cPanel. Le nom de base et utilisateur suivent généralement le format cpanel_user_nombase.';
                    break;
                case 'ovh':
                    helpText = '<strong>OVH :</strong> Consultez votre espace client OVH pour obtenir l\'hostname MySQL et les identifiants.';
                    break;
                case 'ionos':
                    helpText = '<strong>1&1/Ionos :</strong> Utilisez les informations fournies dans votre contrat. Format db12345 pour la base et dbo12345 pour l\'utilisateur.';
                    break;
                case 'local':
                    helpText = '<strong>Développement local :</strong> Configuration par défaut pour XAMPP/WAMP. Mot de passe généralement vide.';
                    break;
            }
            
            if (helpText) {
                helpDiv.innerHTML = helpText;
                helpDiv.style.display = 'block';
            }
        }
        
        function testConnection() {
            const formData = new FormData();
            formData.append('action', 'test_database');
            formData.append('db_host', document.getElementById('db_host').value);
            formData.append('db_port', document.getElementById('db_port').value);
            formData.append('db_name', document.getElementById('db_name').value);
            formData.append('db_user', document.getElementById('db_user').value);
            formData.append('db_password', document.getElementById('db_password').value);
            
            fetch('install.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('test-result');
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><strong>✅ Connexion réussie !</strong> ' + data.message + '</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-error"><strong>❌ Erreur de connexion :</strong> ' + data.message + '</div>';
                }
            });
        }
        </script>
        <?php
    }
    
    private function testDatabase() {
        try {
            $host = $_POST['db_host'];
            $port = $_POST['db_port'];
            $dbname = $_POST['db_name'];
            $username = $_POST['db_user'];
            $password = $_POST['db_password'];
            
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Connexion établie avec succès à la base de données.'
            ]);
            
        } catch (PDOException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function configureDatabase() {
        $config = [
            'DB_DRIVER' => 'mysql',
            'DB_HOST' => $_POST['db_host'],
            'DB_PORT' => $_POST['db_port'],
            'DB_NAME' => $_POST['db_name'],
            'DB_USER' => $_POST['db_user'],
            'DB_PASSWORD' => $_POST['db_password'],
            'APP_ENV' => 'production',
            'SESSION_SECRET' => bin2hex(random_bytes(32))
        ];
        
        // Créer le fichier .env
        $envContent = "# Configuration IntraSphere\n# Généré le " . date('Y-m-d H:i:s') . "\n\n";
        foreach ($config as $key => $value) {
            $envContent .= "{$key}={$value}\n";
        }
        
        if (file_put_contents('.env', $envContent)) {
            $_SESSION['db_config'] = $config;
            $_SESSION['install_step'] = 3;
            header('Location: install.php');
            exit;
        } else {
            $this->showError('Impossible de créer le fichier de configuration.');
        }
    }
    
    private function showTableCreation() {
        ?>
        <h2>🏗️ Création des tables</h2>
        <p>Création de la structure de base de données pour IntraSphere.</p>
        
        <div id="table-creation-log">
            <div class="alert alert-info">
                <strong>Prêt à créer les tables</strong> Cliquez sur le bouton pour démarrer la création.
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <button type="button" class="btn btn-primary" onclick="createTables()">
                <span class="loading" style="display: none;"></span>
                Créer les tables
            </button>
        </div>
        
        <script>
        function createTables() {
            const btn = event.target;
            const loading = btn.querySelector('.loading');
            loading.style.display = 'inline-block';
            btn.disabled = true;
            
            fetch('install.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=create_tables'
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('table-creation-log').innerHTML = data.html;
                if (data.success) {
                    setTimeout(() => {
                        window.location.href = 'install.php';
                    }, 3000);
                }
            })
            .finally(() => {
                loading.style.display = 'none';
                btn.disabled = false;
            });
        }
        </script>
        <?php
    }
    
    private function createTables() {
        $html = '';
        $success = true;
        
        try {
            $config = $_SESSION['db_config'];
            $dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Lire le script SQL
            $sqlContent = file_get_contents('./sql/create_tables.sql');
            if (!$sqlContent) {
                throw new Exception('Fichier de création des tables introuvable');
            }
            
            // Exécuter les requêtes SQL
            $statements = explode(';', $sqlContent);
            $executed = 0;
            
            $html .= '<div class="code-block">';
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (empty($statement) || strpos($statement, '--') === 0) continue;
                
                try {
                    $pdo->exec($statement);
                    $executed++;
                    
                    // Extraire le nom de la table
                    if (preg_match('/CREATE TABLE.*?`?(\w+)`?\s*\(/i', $statement, $matches)) {
                        $tableName = $matches[1];
                        $html .= "✅ Table '{$tableName}' créée avec succès\n";
                    }
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'already exists') !== false) {
                        $html .= "ℹ️  Table déjà existante (ignorée)\n";
                    } else {
                        $html .= "❌ Erreur : " . $e->getMessage() . "\n";
                        $success = false;
                    }
                }
            }
            
            $html .= '</div>';
            
            if ($success) {
                // Insérer les données de démonstration
                $demoData = file_get_contents('./sql/insert_demo_data.sql');
                if ($demoData) {
                    $statements = explode(';', $demoData);
                    foreach ($statements as $statement) {
                        $statement = trim($statement);
                        if (empty($statement) || strpos($statement, '--') === 0) continue;
                        
                        try {
                            $pdo->exec($statement);
                        } catch (PDOException $e) {
                            // Ignorer les erreurs de données existantes
                        }
                    }
                }
                
                $html .= '<div class="alert alert-success">';
                $html .= "<strong>✅ Base de données créée avec succès !</strong> {$executed} tables créées.";
                $html .= '</div>';
                
                $_SESSION['install_step'] = 4;
            } else {
                $html .= '<div class="alert alert-error">';
                $html .= '<strong>❌ Erreurs détectées</strong> Veuillez vérifier la configuration.';
                $html .= '</div>';
            }
            
        } catch (Exception $e) {
            $html = '<div class="alert alert-error">';
            $html .= '<strong>❌ Erreur de connexion :</strong> ' . htmlspecialchars($e->getMessage());
            $html .= '</div>';
            $success = false;
        }
        
        $this->jsonResponse(['success' => $success, 'html' => $html]);
    }
    
    private function showAppConfig() {
        ?>
        <h2>⚙️ Configuration de l'application</h2>
        <p>Configuration finale et création du compte administrateur.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="configure_app">
            
            <h3>Compte Administrateur</h3>
            <div class="grid">
                <div class="form-group">
                    <label>Nom d'utilisateur :</label>
                    <input type="text" name="admin_username" value="admin" required>
                </div>
                
                <div class="form-group">
                    <label>Nom complet :</label>
                    <input type="text" name="admin_name" value="Administrateur Système" required>
                </div>
                
                <div class="form-group">
                    <label>Mot de passe :</label>
                    <input type="password" name="admin_password" required minlength="8">
                </div>
                
                <div class="form-group">
                    <label>Confirmer le mot de passe :</label>
                    <input type="password" name="admin_password_confirm" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Email :</label>
                <input type="email" name="admin_email" placeholder="admin@exemple.com">
            </div>
            
            <h3>Configuration Générale</h3>
            <div class="form-group">
                <label>Nom de l'organisation :</label>
                <input type="text" name="org_name" value="Mon Entreprise" required>
            </div>
            
            <div class="form-group">
                <label>URL du site (optionnel) :</label>
                <input type="url" name="site_url" placeholder="https://www.exemple.com">
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    Finaliser l'installation
                </button>
            </div>
        </form>
        
        <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.querySelector('[name="admin_password"]').value;
            const confirm = document.querySelector('[name="admin_password_confirm"]').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères.');
                return;
            }
        });
        </script>
        <?php
    }
    
    private function configureApp() {
        try {
            $config = $_SESSION['db_config'];
            $dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Vérifier la correspondance des mots de passe
            if ($_POST['admin_password'] !== $_POST['admin_password_confirm']) {
                throw new Exception('Les mots de passe ne correspondent pas.');
            }
            
            // Créer l'utilisateur administrateur
            $adminId = uniqid('admin-', true);
            $hashedPassword = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (id, username, password, name, role, email, is_active, created_at) 
                VALUES (?, ?, ?, ?, 'admin', ?, 1, NOW())
                ON DUPLICATE KEY UPDATE
                password = VALUES(password), name = VALUES(name), email = VALUES(email)
            ");
            
            $stmt->execute([
                $adminId,
                $_POST['admin_username'],
                $hashedPassword,
                $_POST['admin_name'],
                $_POST['admin_email']
            ]);
            
            // Créer le fichier .htaccess pour la sécurité
            $htaccessContent = "# IntraSphere Security Rules\n";
            $htaccessContent .= "RewriteEngine On\n";
            $htaccessContent .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
            $htaccessContent .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
            $htaccessContent .= "RewriteRule ^(.*)$ index.php [QSA,L]\n\n";
            $htaccessContent .= "# Sécurité\n";
            $htaccessContent .= "<Files \".env\">\n";
            $htaccessContent .= "    Order allow,deny\n";
            $htaccessContent .= "    Deny from all\n";
            $htaccessContent .= "</Files>\n\n";
            $htaccessContent .= "<Files \"install.php\">\n";
            $htaccessContent .= "    Order allow,deny\n";
            $htaccessContent .= "    Deny from all\n";
            $htaccessContent .= "</Files>\n";
            
            file_put_contents('.htaccess', $htaccessContent);
            
            $_SESSION['install_step'] = 5;
            $_SESSION['admin_created'] = true;
            
            header('Location: install.php');
            exit;
            
        } catch (Exception $e) {
            $this->showError('Erreur lors de la configuration : ' . $e->getMessage());
        }
    }
    
    private function showCompletion() {
        ?>
        <h2>🎉 Installation terminée !</h2>
        <p>IntraSphere a été installé avec succès sur votre serveur.</p>
        
        <div class="alert alert-success">
            <strong>✅ Installation réussie !</strong> Votre plateforme IntraSphere est maintenant opérationnelle.
        </div>
        
        <div class="alert alert-info">
            <h4>Informations importantes :</h4>
            <ul>
                <li><strong>URL d'accès :</strong> <a href="index.php" target="_blank"><?= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) ?>/index.php</a></li>
                <li><strong>Nom d'utilisateur admin :</strong> <?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></li>
                <li><strong>Panneau d'administration :</strong> Accessible via le menu utilisateur</li>
            </ul>
        </div>
        
        <div class="alert alert-warning">
            <h4>Recommandations de sécurité :</h4>
            <ul>
                <li>Supprimez le fichier <code>install.php</code> de votre serveur</li>
                <li>Vérifiez que le fichier <code>.env</code> n'est pas accessible publiquement</li>
                <li>Configurez des sauvegardes régulières de votre base de données</li>
                <li>Mettez à jour régulièrement vos mots de passe</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn btn-success">
                🚀 Accéder à IntraSphere
            </a>
            <button type="button" class="btn btn-danger" onclick="deleteInstaller()">
                🗑️ Supprimer l'installateur
            </button>
        </div>
        
        <script>
        function deleteInstaller() {
            if (confirm('Êtes-vous sûr de vouloir supprimer le fichier d\'installation ?\n\nCette action est irréversible.')) {
                fetch('install.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=finalize'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Installateur supprimé avec succès !');
                        window.location.href = 'index.php';
                    } else {
                        alert('Erreur lors de la suppression : ' + data.message);
                    }
                });
            }
        }
        </script>
        <?php
    }
    
    private function finalizeInstallation() {
        try {
            // Supprimer le fichier d'installation
            if (file_exists('install.php')) {
                unlink('install.php');
            }
            
            // Nettoyer la session d'installation
            unset($_SESSION['install_step']);
            unset($_SESSION['db_config']);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Installation finalisée avec succès.'
            ]);
            
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function showFooter() {
        ?>
        </div>
    </div>
    
    <div style="text-align: center; padding: 20px; color: rgba(255,255,255,0.8);">
        <p>IntraSphere <?= VERSION ?> - Installation automatisée</p>
        <p>Plateforme intranet moderne pour entreprises</p>
    </div>
</body>
</html>
        <?php
    }
    
    private function showError($message) {
        echo "<div class='alert alert-error'><strong>Erreur :</strong> " . htmlspecialchars($message) . "</div>";
    }
    
    private function jsonResponse($data) {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logLine = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents(LOG_FILE, $logLine, FILE_APPEND | LOCK_EX);
    }
}

// Vérifier si l'installation n'est pas déjà terminée
if (file_exists('.env') && file_exists('index.php') && !isset($_GET['force'])) {
    if (!isset($_SESSION['install_step']) || $_SESSION['install_step'] >= 5) {
        header('Location: index.php');
        exit;
    }
}

// Démarrer l'installation
$installer = new IntraSphereInstaller();
$installer->run();

// Vider le buffer si encore actif
if (ob_get_level()) {
    ob_end_flush();
}
?>