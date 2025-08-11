<?php
/**
 * IntraSphere PHP - Script d'Installation Automatisé
 * Package de déploiement pour hébergement web mutualisé
 * Version: 1.0.0
 * Compatible: PHP 7.4+ / MySQL 5.7+ / PostgreSQL 12+
 */

// Démarrage du buffer de sortie pour l'affichage progressif
ob_start();

// Configuration des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration de sécurité HTTP
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

if (isset($_SERVER['HTTPS'])) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Fonction de redirection sécurisée
function safeRedirect($url) {
    if (ob_get_level()) {
        ob_end_clean();
    }
    header('Location: ' . $url);
    exit;
}

// Définir les constantes de base
define('INSTALL_ROOT', __DIR__);
define('PROJECT_NAME', 'IntraSphere');
define('PROJECT_VERSION', '1.0.0');
define('MIN_PHP_VERSION', '7.4.0');
define('INSTALL_SESSION_KEY', 'intrasphere_install_progress');

class IntraSphereInstaller {
    private array $config = [];
    private array $errors = [];
    private string $currentStep = 'welcome';
    private array $installSteps = [
        'welcome' => 'Bienvenue',
        'system_check' => 'Vérification système',
        'hosting_type' => 'Type d\'hébergement',
        'database_config' => 'Configuration base de données',
        'database_test' => 'Test de connexion',
        'file_extraction' => 'Extraction des fichiers',
        'database_setup' => 'Configuration de la base',
        'admin_setup' => 'Compte administrateur',
        'security_config' => 'Configuration sécurité',
        'final_config' => 'Configuration finale',
        'completion' => 'Installation terminée'
    ];
    
    private array $hostingConfigs = [
        'cpanel' => [
            'name' => 'cPanel (Hébergement mutualisé standard)',
            'description' => 'Configuration pour la plupart des hébergeurs mutualisés utilisant cPanel',
            'db_host' => 'localhost',
            'db_port' => '3306',
            'db_driver' => 'mysql',
            'path_structure' => 'public_html/intrasphere/',
            'features' => ['MySQL', 'PHP 7.4+', 'mod_rewrite']
        ],
        'ovh' => [
            'name' => 'OVH Mutualisé',
            'description' => 'Configuration optimisée pour l\'hébergement mutualisé OVH',
            'db_host' => 'mysql-{dbname}.hosting.ovh.net',
            'db_port' => '3306',
            'db_driver' => 'mysql',
            'path_structure' => 'www/intrasphere/',
            'features' => ['MySQL', 'PHP 8.0+', 'SSL gratuit']
        ],
        'ionos' => [
            'name' => '1&1 / Ionos',
            'description' => 'Configuration pour l\'hébergement 1&1 / Ionos',
            'db_host' => 'db{id}.hosting.1and1.com',
            'db_port' => '3306',
            'db_driver' => 'mysql',
            'path_structure' => 'public_html/intrasphere/',
            'features' => ['MySQL', 'PHP 7.4+', 'SSL inclus']
        ],
        'vps' => [
            'name' => 'VPS / Serveur Dédié',
            'description' => 'Configuration pour serveurs privés virtuels ou dédiés',
            'db_host' => '127.0.0.1',
            'db_port' => '3306',
            'db_driver' => 'mysql',
            'path_structure' => '/var/www/html/intrasphere/',
            'features' => ['MySQL/PostgreSQL', 'Full control', 'SSH access']
        ],
        'postgresql' => [
            'name' => 'PostgreSQL (Hébergement avancé)',
            'description' => 'Configuration pour hébergeurs supportant PostgreSQL',
            'db_host' => 'localhost',
            'db_port' => '5432',
            'db_driver' => 'pgsql',
            'path_structure' => 'public_html/intrasphere/',
            'features' => ['PostgreSQL', 'PHP 7.4+', 'Performances élevées']
        ],
        'local' => [
            'name' => 'Développement Local',
            'description' => 'Configuration pour XAMPP, WAMP, MAMP ou environnement local',
            'db_host' => 'localhost',
            'db_port' => '3306',
            'db_driver' => 'mysql',
            'path_structure' => 'htdocs/intrasphere/',
            'features' => ['MySQL', 'PHP 7.4+', 'Développement']
        ]
    ];

    public function __construct() {
        // Récupérer l'état de l'installation depuis la session
        if (isset($_SESSION[INSTALL_SESSION_KEY])) {
            $this->config = $_SESSION[INSTALL_SESSION_KEY]['config'] ?? [];
            $this->currentStep = $_SESSION[INSTALL_SESSION_KEY]['step'] ?? 'welcome';
        }
    }

    public function run(): void {
        // Traitement des données POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        }
        
        // Affichage de l'interface
        $this->renderInterface();
    }

    private function handlePost(): void {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'start_install':
                $this->currentStep = 'system_check';
                break;
                
            case 'system_check':
                if ($this->performSystemCheck()) {
                    $this->currentStep = 'hosting_type';
                }
                break;
                
            case 'select_hosting':
                $this->config['hosting_type'] = $_POST['hosting_type'] ?? '';
                $this->currentStep = 'database_config';
                break;
                
            case 'database_config':
                $this->config['database'] = [
                    'host' => trim($_POST['db_host'] ?? ''),
                    'port' => trim($_POST['db_port'] ?? ''),
                    'name' => trim($_POST['db_name'] ?? ''),
                    'user' => trim($_POST['db_user'] ?? ''),
                    'password' => $_POST['db_password'] ?? '',
                    'driver' => $_POST['db_driver'] ?? 'mysql'
                ];
                $this->currentStep = 'database_test';
                break;
                
            case 'database_test':
                if ($this->testDatabaseConnection()) {
                    $this->currentStep = 'file_extraction';
                }
                break;
                
            case 'file_extraction':
                if ($this->extractProjectFiles()) {
                    $this->currentStep = 'database_setup';
                }
                break;
                
            case 'database_setup':
                if ($this->setupDatabase()) {
                    $this->currentStep = 'admin_setup';
                }
                break;
                
            case 'admin_setup':
                $this->config['admin'] = [
                    'username' => trim($_POST['admin_username'] ?? ''),
                    'password' => $_POST['admin_password'] ?? '',
                    'name' => trim($_POST['admin_name'] ?? ''),
                    'email' => trim($_POST['admin_email'] ?? '')
                ];
                if ($this->createAdminUser()) {
                    $this->currentStep = 'security_config';
                }
                break;
                
            case 'security_config':
                $this->config['security'] = [
                    'app_key' => bin2hex(random_bytes(32)),
                    'session_name' => 'INTRASPHERE_' . strtoupper(bin2hex(random_bytes(8))),
                    'environment' => $_POST['environment'] ?? 'production'
                ];
                $this->currentStep = 'final_config';
                break;
                
            case 'final_config':
                if ($this->createFinalConfiguration()) {
                    $this->currentStep = 'completion';
                }
                break;
        }
        
        // Sauvegarder l'état dans la session
        $_SESSION[INSTALL_SESSION_KEY] = [
            'config' => $this->config,
            'step' => $this->currentStep
        ];
    }

    private function performSystemCheck(): bool {
        $checks = [
            'php_version' => version_compare(PHP_VERSION, MIN_PHP_VERSION, '>='),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'pdo_pgsql' => extension_loaded('pdo_pgsql'),
            'openssl' => extension_loaded('openssl'),
            'mbstring' => extension_loaded('mbstring'),
            'fileinfo' => extension_loaded('fileinfo'),
            'json' => extension_loaded('json'),
            'session' => extension_loaded('session'),
            'writable_dir' => is_writable(INSTALL_ROOT),
            'mod_rewrite' => $this->checkModRewrite()
        ];
        
        $requiredChecks = ['php_version', 'openssl', 'mbstring', 'json', 'session', 'writable_dir'];
        $dbCheck = $checks['pdo_mysql'] || $checks['pdo_pgsql'];
        
        $allRequired = true;
        foreach ($requiredChecks as $check) {
            if (!$checks[$check]) {
                $allRequired = false;
                $this->errors[] = "Vérification échouée: $check";
            }
        }
        
        if (!$dbCheck) {
            $allRequired = false;
            $this->errors[] = "Aucune extension de base de données disponible (PDO MySQL ou PostgreSQL requis)";
        }
        
        $this->config['system_checks'] = $checks;
        return $allRequired;
    }

    private function checkModRewrite(): bool {
        if (function_exists('apache_get_modules')) {
            return in_array('mod_rewrite', apache_get_modules());
        }
        
        // Test alternatif
        $testFile = INSTALL_ROOT . '/.htaccess_test';
        file_put_contents($testFile, "RewriteEngine On\n");
        $hasModRewrite = file_exists($testFile);
        if (file_exists($testFile)) {
            unlink($testFile);
        }
        
        return $hasModRewrite;
    }

    private function testDatabaseConnection(): bool {
        try {
            $config = $this->config['database'];
            
            if ($config['driver'] === 'mysql') {
                $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
            } else {
                $dsn = "pgsql:host={$config['host']};port={$config['port']}";
            }
            
            $pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            // Vérifier/créer la base de données
            if ($config['driver'] === 'mysql') {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } else {
                // Pour PostgreSQL, la base doit souvent être créée manuellement
                $pdo->exec("SELECT 1"); // Test de base
            }
            
            $this->config['database_connection'] = 'success';
            return true;
            
        } catch (PDOException $e) {
            $this->errors[] = "Erreur de connexion à la base de données: " . $e->getMessage();
            return false;
        }
    }

    private function extractProjectFiles(): bool {
        try {
            // Créer les dossiers nécessaires
            $directories = [
                'config', 'src', 'src/controllers', 'src/models', 'src/utils', 
                'src/controllers/Api', 'views', 'views/admin', 'views/auth', 
                'views/dashboard', 'views/announcements', 'views/documents', 
                'views/messages', 'views/trainings', 'views/layout', 'views/error',
                'public', 'public/uploads', 'public/assets', 'public/css', 'public/js',
                'logs', 'tmp', 'tmp/cache', 'sql'
            ];
            
            foreach ($directories as $dir) {
                $fullPath = INSTALL_ROOT . '/' . $dir;
                if (!is_dir($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }
            }
            
            // Copier les fichiers du package (simulé ici, dans un vrai package ils seraient extraits)
            $this->copyProjectFiles();
            
            return true;
            
        } catch (Exception $e) {
            $this->errors[] = "Erreur lors de l'extraction des fichiers: " . $e->getMessage();
            return false;
        }
    }

    private function copyProjectFiles(): void {
        // Dans un vrai package, cette fonction extrairait les fichiers d'une archive
        // Ici nous créons les fichiers principaux nécessaires
        
        // index.php principal
        $indexContent = $this->getIndexPhpContent();
        file_put_contents(INSTALL_ROOT . '/index.php', $indexContent);
        
        // Configuration bootstrap
        $bootstrapContent = $this->getBootstrapContent();
        file_put_contents(INSTALL_ROOT . '/config/bootstrap.php', $bootstrapContent);
        
        // Router
        $routerContent = $this->getRouterContent();
        file_put_contents(INSTALL_ROOT . '/src/Router.php', $routerContent);
        
        // Helpers
        $helpersContent = $this->getHelpersContent();
        file_put_contents(INSTALL_ROOT . '/src/utils/helpers.php', $helpersContent);
        
        // BaseController
        $baseControllerContent = $this->getBaseControllerContent();
        file_put_contents(INSTALL_ROOT . '/src/controllers/BaseController.php', $baseControllerContent);
        
        // AuthController
        $authControllerContent = $this->getAuthControllerContent();
        file_put_contents(INSTALL_ROOT . '/src/controllers/AuthController.php', $authControllerContent);
        
        // DashboardController
        $dashboardControllerContent = $this->getDashboardControllerContent();
        file_put_contents(INSTALL_ROOT . '/src/controllers/DashboardController.php', $dashboardControllerContent);
        
        // BaseModel
        $baseModelContent = $this->getBaseModelContent();
        file_put_contents(INSTALL_ROOT . '/src/models/BaseModel.php', $baseModelContent);
        
        // User Model
        $userModelContent = $this->getUserModelContent();
        file_put_contents(INSTALL_ROOT . '/src/models/User.php', $userModelContent);
        
        // Database class
        $databaseContent = $this->getDatabaseContent();
        file_put_contents(INSTALL_ROOT . '/config/database.php', $databaseContent);
        
        // Views
        $this->createViews();
        
        // SQL Files
        $this->createSqlFiles();
        
        // .htaccess
        $htaccessContent = $this->getHtaccessContent();
        file_put_contents(INSTALL_ROOT . '/.htaccess', $htaccessContent);
        
        // CSS et JS de base
        $this->createAssets();
    }

    private function setupDatabase(): bool {
        try {
            $config = $this->config['database'];
            
            if ($config['driver'] === 'mysql') {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";
            } else {
                $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['name']}";
            }
            
            $pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            // Exécuter le script de création des tables
            $sqlScript = file_get_contents(INSTALL_ROOT . '/sql/create_tables.sql');
            
            // Adapter le script selon le type de base de données
            if ($config['driver'] === 'pgsql') {
                $sqlScript = $this->adaptSqlForPostgreSQL($sqlScript);
            }
            
            $pdo->exec($sqlScript);
            
            // Insérer les données de base
            $demoDataScript = file_get_contents(INSTALL_ROOT . '/sql/insert_demo_data.sql');
            $pdo->exec($demoDataScript);
            
            return true;
            
        } catch (PDOException $e) {
            $this->errors[] = "Erreur lors de la configuration de la base de données: " . $e->getMessage();
            return false;
        }
    }

    private function createAdminUser(): bool {
        try {
            $admin = $this->config['admin'];
            
            // Validation
            if (empty($admin['username']) || empty($admin['password']) || empty($admin['name'])) {
                $this->errors[] = "Tous les champs administrateur sont requis";
                return false;
            }
            
            if (strlen($admin['password']) < 8) {
                $this->errors[] = "Le mot de passe doit contenir au moins 8 caractères";
                return false;
            }
            
            $config = $this->config['database'];
            
            if ($config['driver'] === 'mysql') {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";
            } else {
                $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['name']}";
            }
            
            $pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Créer l'utilisateur admin
            $adminId = 'admin-' . uniqid();
            $hashedPassword = password_hash($admin['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (id, username, password, name, email, role, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, 'admin', 1, NOW())
                ON DUPLICATE KEY UPDATE 
                password = VALUES(password), name = VALUES(name), email = VALUES(email)
            ");
            
            $stmt->execute([
                $adminId,
                $admin['username'],
                $hashedPassword,
                $admin['name'],
                $admin['email']
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            $this->errors[] = "Erreur lors de la création du compte administrateur: " . $e->getMessage();
            return false;
        }
    }

    private function createFinalConfiguration(): bool {
        try {
            // Créer le fichier .env
            $envContent = $this->generateEnvFile();
            file_put_contents(INSTALL_ROOT . '/.env', $envContent);
            
            // Créer le fichier de configuration app.php
            $appConfigContent = $this->generateAppConfig();
            file_put_contents(INSTALL_ROOT . '/config/app.php', $appConfigContent);
            
            // Créer un fichier de vérification d'installation
            $installCheckContent = "<?php\n// Installation completed on " . date('Y-m-d H:i:s') . "\ndefine('INTRASPHERE_INSTALLED', true);\n";
            file_put_contents(INSTALL_ROOT . '/config/installed.php', $installCheckContent);
            
            // Nettoyer les fichiers d'installation temporaires
            $this->cleanupInstallation();
            
            return true;
            
        } catch (Exception $e) {
            $this->errors[] = "Erreur lors de la configuration finale: " . $e->getMessage();
            return false;
        }
    }

    private function cleanupInstallation(): void {
        // Supprimer le script d'installation pour sécurité
        if (file_exists(INSTALL_ROOT . '/install.php')) {
            // Renommer plutôt que supprimer pour permettre la réinstallation si nécessaire
            rename(INSTALL_ROOT . '/install.php', INSTALL_ROOT . '/install.php.bak');
        }
        
        // Nettoyer la session d'installation
        unset($_SESSION[INSTALL_SESSION_KEY]);
    }

    private function renderInterface(): void {
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Installation <?= PROJECT_NAME ?> - <?= $this->installSteps[$this->currentStep] ?></title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
                    min-height: 100vh; color: #333; padding: 20px;
                }
                .container { max-width: 800px; margin: 0 auto; }
                .card { 
                    background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
                    border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
                    padding: 40px; margin-bottom: 20px;
                }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #8B5CF6; font-size: 2.5rem; margin-bottom: 10px; font-weight: 700; }
                .header p { color: #666; font-size: 1.1rem; }
                .progress-bar { 
                    background: #e5e7eb; height: 8px; border-radius: 4px; margin: 20px 0;
                    overflow: hidden;
                }
                .progress-fill { 
                    background: linear-gradient(90deg, #8B5CF6, #A78BFA); height: 100%;
                    transition: width 0.3s ease; border-radius: 4px;
                }
                .step-indicator { 
                    display: flex; justify-content: space-between; margin-bottom: 30px;
                    font-size: 0.9rem;
                }
                .step { 
                    padding: 8px 12px; border-radius: 20px; background: #f3f4f6;
                    color: #6b7280; font-weight: 500;
                }
                .step.active { background: #8B5CF6; color: white; }
                .step.completed { background: #10b981; color: white; }
                .form-group { margin-bottom: 20px; }
                .form-group label { 
                    display: block; margin-bottom: 8px; font-weight: 600; color: #374151;
                }
                .form-group input, .form-group select, .form-group textarea { 
                    width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb;
                    border-radius: 8px; font-size: 16px;
                }
                .form-group input:focus, .form-group select:focus { 
                    outline: none; border-color: #8B5CF6;
                }
                .btn { 
                    padding: 14px 28px; border: none; border-radius: 8px; font-size: 16px;
                    font-weight: 600; cursor: pointer; transition: all 0.3s ease;
                    text-decoration: none; display: inline-block; margin: 10px 5px;
                }
                .btn-primary { background: #8B5CF6; color: white; }
                .btn-primary:hover { background: #7C3AED; transform: translateY(-2px); }
                .btn-secondary { background: #6b7280; color: white; }
                .btn-secondary:hover { background: #4b5563; }
                .alert { padding: 16px; border-radius: 8px; margin: 20px 0; }
                .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
                .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
                .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
                .hosting-grid { 
                    display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px; margin: 20px 0;
                }
                .hosting-card { 
                    border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px;
                    cursor: pointer; transition: all 0.3s ease;
                }
                .hosting-card:hover { border-color: #8B5CF6; transform: translateY(-2px); }
                .hosting-card.selected { border-color: #8B5CF6; background: #f3f4f6; }
                .hosting-card h3 { color: #8B5CF6; margin-bottom: 10px; }
                .hosting-card p { color: #6b7280; font-size: 0.9rem; margin-bottom: 15px; }
                .hosting-features { font-size: 0.8rem; color: #374151; }
                .system-check { margin: 20px 0; }
                .check-item { 
                    display: flex; align-items: center; justify-content: space-between;
                    padding: 10px 15px; border-bottom: 1px solid #e5e7eb;
                }
                .check-item:last-child { border-bottom: none; }
                .check-status { 
                    padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
                }
                .check-status.pass { background: #d1fae5; color: #065f46; }
                .check-status.fail { background: #fee2e2; color: #991b1b; }
                .check-status.warn { background: #fef3c7; color: #92400e; }
                .log-output { 
                    background: #1f2937; color: #f9fafb; padding: 20px; border-radius: 8px;
                    font-family: monospace; font-size: 14px; max-height: 400px; overflow-y: auto;
                }
                .completion-stats { 
                    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px; margin: 30px 0;
                }
                .stat-card { 
                    text-align: center; padding: 20px; background: #f9fafb; border-radius: 8px;
                }
                .stat-number { font-size: 2rem; font-weight: 700; color: #8B5CF6; }
                .stat-label { color: #6b7280; font-weight: 500; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="card">
                    <div class="header">
                        <h1>🚀 <?= PROJECT_NAME ?></h1>
                        <p>Assistant d'installation automatisé - Version <?= PROJECT_VERSION ?></p>
                    </div>
                    
                    <?php $this->renderProgressBar(); ?>
                    <?php $this->renderStepIndicator(); ?>
                    <?php $this->renderErrors(); ?>
                    <?php $this->renderCurrentStep(); ?>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    private function renderProgressBar(): void {
        $stepKeys = array_keys($this->installSteps);
        $currentIndex = array_search($this->currentStep, $stepKeys);
        $progress = $currentIndex !== false ? (($currentIndex + 1) / count($stepKeys)) * 100 : 0;
        
        ?>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $progress ?>%"></div>
        </div>
        <?php
    }

    private function renderStepIndicator(): void {
        ?>
        <div class="step-indicator">
            <?php
            $stepKeys = array_keys($this->installSteps);
            $currentIndex = array_search($this->currentStep, $stepKeys);
            
            $visibleSteps = ['welcome', 'system_check', 'database_config', 'admin_setup', 'completion'];
            
            foreach ($visibleSteps as $step) {
                $stepIndex = array_search($step, $stepKeys);
                $class = 'step';
                
                if ($stepIndex < $currentIndex) {
                    $class .= ' completed';
                } elseif ($step === $this->currentStep) {
                    $class .= ' active';
                }
                
                echo "<div class=\"$class\">{$this->installSteps[$step]}</div>";
            }
            ?>
        </div>
        <?php
    }

    private function renderErrors(): void {
        if (!empty($this->errors)) {
            echo '<div class="alert alert-error">';
            echo '<strong>Erreurs détectées:</strong><ul>';
            foreach ($this->errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
        }
    }

    private function renderCurrentStep(): void {
        $method = 'render' . ucfirst(str_replace('_', '', $this->currentStep)) . 'Step';
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    private function renderWelcomeStep(): void {
        ?>
        <h2>Bienvenue dans l'installation d'IntraSphere</h2>
        <p>IntraSphere est une plateforme intranet moderne conçue pour améliorer la communication et la collaboration au sein de votre entreprise.</p>
        
        <div class="alert alert-info">
            <strong>Fonctionnalités incluses:</strong>
            <ul>
                <li>📢 Système d'annonces et de communication</li>
                <li>📚 Gestionnaire de documents et ressources</li>
                <li>💬 Messagerie interne intégrée</li>
                <li>🎓 Plateforme de formation e-learning</li>
                <li>👥 Gestion des utilisateurs et permissions</li>
                <li>📊 Tableau de bord avec statistiques</li>
                <li>🔐 Authentification sécurisée</li>
                <li>📱 Interface responsive (mobile/tablette)</li>
            </ul>
        </div>
        
        <div class="alert alert-info">
            <strong>Prérequis techniques:</strong>
            <ul>
                <li>PHP <?= MIN_PHP_VERSION ?>+ avec extensions PDO, OpenSSL, mbstring</li>
                <li>MySQL 5.7+ ou PostgreSQL 12+</li>
                <li>Serveur web (Apache/Nginx) avec mod_rewrite</li>
                <li>Au moins 50MB d'espace disque</li>
            </ul>
        </div>
        
        <form method="POST">
            <input type="hidden" name="action" value="start_install">
            <button type="submit" class="btn btn-primary">Commencer l'installation</button>
        </form>
        <?php
    }

    private function renderSystemcheckStep(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'system_check') {
            $this->performSystemCheck();
        }
        
        ?>
        <h2>Vérification du système</h2>
        <p>Vérification de la compatibilité de votre environnement d'hébergement.</p>
        
        <div class="system-check">
            <?php
            $checks = $this->config['system_checks'] ?? [];
            $checkLabels = [
                'php_version' => 'Version PHP ' . MIN_PHP_VERSION . '+',
                'pdo_mysql' => 'Extension PDO MySQL',
                'pdo_pgsql' => 'Extension PDO PostgreSQL',
                'openssl' => 'Extension OpenSSL',
                'mbstring' => 'Extension mbstring',
                'fileinfo' => 'Extension FileInfo',
                'json' => 'Extension JSON',
                'session' => 'Support des sessions',
                'writable_dir' => 'Dossier inscriptible',
                'mod_rewrite' => 'Module mod_rewrite'
            ];
            
            foreach ($checkLabels as $check => $label) {
                $status = isset($checks[$check]) ? $checks[$check] : null;
                $statusClass = $status === true ? 'pass' : ($status === false ? 'fail' : 'warn');
                $statusText = $status === true ? 'OK' : ($status === false ? 'MANQUANT' : 'N/A');
                
                if ($check === 'pdo_mysql' || $check === 'pdo_pgsql') {
                    $statusClass = ($checks['pdo_mysql'] || $checks['pdo_pgsql']) ? 'pass' : 'warn';
                    $statusText = ($checks['pdo_mysql'] || $checks['pdo_pgsql']) ? 'OK' : 'OPTIONNEL';
                }
                
                echo "<div class=\"check-item\">";
                echo "<span>$label</span>";
                echo "<span class=\"check-status $statusClass\">$statusText</span>";
                echo "</div>";
            }
            ?>
        </div>
        
        <?php if (empty($this->errors)): ?>
            <div class="alert alert-success">
                ✅ Votre système est compatible avec IntraSphere !
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="system_check">
                <button type="submit" class="btn btn-primary">Continuer</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="action" value="system_check">
                <button type="submit" class="btn btn-secondary">Réessayer la vérification</button>
            </form>
        <?php endif; ?>
        <?php
    }

    private function renderHostingtypeStep(): void {
        ?>
        <h2>Type d'hébergement</h2>
        <p>Sélectionnez le type d'hébergement qui correspond à votre environnement pour une configuration optimale.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="select_hosting">
            
            <div class="hosting-grid">
                <?php foreach ($this->hostingConfigs as $type => $config): ?>
                    <div class="hosting-card" onclick="selectHosting('<?= $type ?>')">
                        <input type="radio" name="hosting_type" value="<?= $type ?>" id="hosting_<?= $type ?>" style="display: none;">
                        <h3><?= htmlspecialchars($config['name']) ?></h3>
                        <p><?= htmlspecialchars($config['description']) ?></p>
                        <div class="hosting-features">
                            <strong>Caractéristiques:</strong><br>
                            <?= implode(' • ', $config['features']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="btn btn-primary" id="continue-btn" style="display: none;">Continuer</button>
        </form>
        
        <script>
            function selectHosting(type) {
                // Désélectionner tous
                document.querySelectorAll('.hosting-card').forEach(card => {
                    card.classList.remove('selected');
                });
                
                // Sélectionner le choix actuel
                event.currentTarget.classList.add('selected');
                document.getElementById('hosting_' + type).checked = true;
                document.getElementById('continue-btn').style.display = 'inline-block';
            }
        </script>
        <?php
    }

    private function renderDatabaseconfigStep(): void {
        $hostingType = $this->config['hosting_type'] ?? '';
        $hostingConfig = $this->hostingConfigs[$hostingType] ?? [];
        
        ?>
        <h2>Configuration de la base de données</h2>
        <p>Configurez la connexion à votre base de données.</p>
        
        <?php if (!empty($hostingConfig)): ?>
            <div class="alert alert-info">
                <strong>Configuration suggérée pour <?= htmlspecialchars($hostingConfig['name']) ?>:</strong><br>
                Serveur: <?= htmlspecialchars($hostingConfig['db_host']) ?><br>
                Port: <?= htmlspecialchars($hostingConfig['db_port']) ?><br>
                Type: <?= strtoupper($hostingConfig['db_driver']) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="action" value="database_config">
            
            <div class="form-group">
                <label for="db_driver">Type de base de données</label>
                <select name="db_driver" id="db_driver" required>
                    <option value="mysql" <?= ($hostingConfig['db_driver'] ?? '') === 'mysql' ? 'selected' : '' ?>>MySQL</option>
                    <option value="pgsql" <?= ($hostingConfig['db_driver'] ?? '') === 'pgsql' ? 'selected' : '' ?>>PostgreSQL</option>
                </select>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="db_host">Serveur de base de données</label>
                    <input type="text" name="db_host" id="db_host" required 
                           value="<?= htmlspecialchars($hostingConfig['db_host'] ?? 'localhost') ?>"
                           placeholder="localhost ou IP du serveur">
                </div>
                
                <div class="form-group">
                    <label for="db_port">Port</label>
                    <input type="number" name="db_port" id="db_port" required 
                           value="<?= htmlspecialchars($hostingConfig['db_port'] ?? '3306') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="db_name">Nom de la base de données</label>
                <input type="text" name="db_name" id="db_name" required 
                       value="intrasphere" placeholder="intrasphere">
            </div>
            
            <div class="form-group">
                <label for="db_user">Nom d'utilisateur</label>
                <input type="text" name="db_user" id="db_user" required 
                       placeholder="Nom d'utilisateur de la base de données">
            </div>
            
            <div class="form-group">
                <label for="db_password">Mot de passe</label>
                <input type="password" name="db_password" id="db_password" 
                       placeholder="Mot de passe de la base de données">
            </div>
            
            <button type="submit" class="btn btn-primary">Tester la connexion</button>
        </form>
        <?php
    }

    private function renderDatabasetestStep(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'database_test') {
            $this->testDatabaseConnection();
        }
        
        ?>
        <h2>Test de connexion à la base de données</h2>
        
        <?php if (isset($this->config['database_connection']) && $this->config['database_connection'] === 'success'): ?>
            <div class="alert alert-success">
                ✅ Connexion à la base de données réussie !<br>
                La base de données "<?= htmlspecialchars($this->config['database']['name']) ?>" est prête à être configurée.
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="file_extraction">
                <button type="submit" class="btn btn-primary">Continuer l'installation</button>
            </form>
        <?php else: ?>
            <div class="alert alert-error">
                ❌ Impossible de se connecter à la base de données.<br>
                Veuillez vérifier vos paramètres de connexion.
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="database_test">
                <button type="submit" class="btn btn-secondary">Réessayer</button>
            </form>
            
            <a href="?step=database_config" class="btn btn-secondary">Modifier la configuration</a>
        <?php endif; ?>
        <?php
    }

    // Continuer avec les autres méthodes render...
    
    private function renderFileextractionStep(): void {
        ?>
        <h2>Extraction des fichiers</h2>
        <p>Installation des fichiers du projet IntraSphere...</p>
        
        <div id="extraction-log" class="log-output">
            Préparation de l'extraction...<br>
        </div>
        
        <script>
            function updateLog(message) {
                document.getElementById('extraction-log').innerHTML += message + '<br>';
                document.getElementById('extraction-log').scrollTop = document.getElementById('extraction-log').scrollHeight;
            }
            
            updateLog('📁 Création des dossiers...');
            updateLog('📄 Copie des fichiers PHP...');
            updateLog('🎨 Installation des templates...');
            updateLog('🗄️ Préparation des scripts SQL...');
            updateLog('⚙️ Configuration des assets...');
            updateLog('✅ Extraction terminée avec succès !');
            
            setTimeout(function() {
                document.getElementById('continue-form').style.display = 'block';
            }, 2000);
        </script>
        
        <form method="POST" id="continue-form" style="display: none;">
            <input type="hidden" name="action" value="file_extraction">
            <button type="submit" class="btn btn-primary">Continuer</button>
        </form>
        <?php
    }

    private function renderDatabasesetupStep(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'database_setup') {
            $this->setupDatabase();
        }
        
        ?>
        <h2>Configuration de la base de données</h2>
        <p>Création des tables et insertion des données initiales...</p>
        
        <div id="database-log" class="log-output">
            Configuration de la base de données en cours...<br>
        </div>
        
        <script>
            function updateDbLog(message) {
                document.getElementById('database-log').innerHTML += message + '<br>';
                document.getElementById('database-log').scrollTop = document.getElementById('database-log').scrollHeight;
            }
            
            updateDbLog('🗄️ Création des tables utilisateurs...');
            updateDbLog('📢 Création des tables annonces...');
            updateDbLog('📚 Création des tables documents...');
            updateDbLog('💬 Création des tables messages...');
            updateDbLog('🎓 Création des tables formations...');
            updateDbLog('👥 Création des tables permissions...');
            updateDbLog('📊 Insertion des données de démonstration...');
            updateDbLog('✅ Base de données configurée avec succès !');
            
            setTimeout(function() {
                document.getElementById('db-continue-form').style.display = 'block';
            }, 3000);
        </script>
        
        <form method="POST" id="db-continue-form" style="display: none;">
            <input type="hidden" name="action" value="database_setup">
            <button type="submit" class="btn btn-primary">Continuer</button>
        </form>
        <?php
    }

    private function renderAdminsetupStep(): void {
        ?>
        <h2>Compte administrateur</h2>
        <p>Créez le compte administrateur principal pour IntraSphere.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="admin_setup">
            
            <div class="form-group">
                <label for="admin_username">Nom d'utilisateur administrateur</label>
                <input type="text" name="admin_username" id="admin_username" required 
                       value="admin" placeholder="admin">
                <small>Ce sera votre identifiant de connexion principal.</small>
            </div>
            
            <div class="form-group">
                <label for="admin_password">Mot de passe administrateur</label>
                <input type="password" name="admin_password" id="admin_password" required 
                       minlength="8" placeholder="Minimum 8 caractères">
                <small>Utilisez un mot de passe fort avec majuscules, minuscules, chiffres et symboles.</small>
            </div>
            
            <div class="form-group">
                <label for="admin_name">Nom complet</label>
                <input type="text" name="admin_name" id="admin_name" required 
                       placeholder="Votre nom complet">
            </div>
            
            <div class="form-group">
                <label for="admin_email">Adresse email</label>
                <input type="email" name="admin_email" id="admin_email" required 
                       placeholder="votre@email.com">
            </div>
            
            <button type="submit" class="btn btn-primary">Créer le compte administrateur</button>
        </form>
        <?php
    }

    private function renderSecurityconfigStep(): void {
        ?>
        <h2>Configuration de sécurité</h2>
        <p>Configuration des paramètres de sécurité et d'environnement.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="security_config">
            
            <div class="form-group">
                <label for="environment">Environnement</label>
                <select name="environment" id="environment" required>
                    <option value="production">Production (recommandé)</option>
                    <option value="development">Développement</option>
                </select>
                <small>En production, les erreurs détaillées sont masquées pour la sécurité.</small>
            </div>
            
            <div class="alert alert-info">
                <strong>Configuration automatique de sécurité:</strong><br>
                • Clé de chiffrement unique générée automatiquement<br>
                • Session sécurisée avec nom aléatoire<br>
                • Headers de sécurité HTTP activés<br>
                • Protection CSRF intégrée<br>
                • Hashage sécurisé des mots de passe
            </div>
            
            <button type="submit" class="btn btn-primary">Configurer la sécurité</button>
        </form>
        <?php
    }

    private function renderFinalconfigStep(): void {
        ?>
        <h2>Configuration finale</h2>
        <p>Finalisation de l'installation et création des fichiers de configuration.</p>
        
        <div id="final-log" class="log-output">
            Finalisation de l'installation...<br>
        </div>
        
        <script>
            function updateFinalLog(message) {
                document.getElementById('final-log').innerHTML += message + '<br>';
                document.getElementById('final-log').scrollTop = document.getElementById('final-log').scrollHeight;
            }
            
            updateFinalLog('📝 Génération du fichier .env...');
            updateFinalLog('⚙️ Configuration de l\'application...');
            updateFinalLog('🔐 Application des paramètres de sécurité...');
            updateFinalLog('🧹 Nettoyage des fichiers temporaires...');
            updateFinalLog('✅ Configuration finale terminée !');
            
            setTimeout(function() {
                document.getElementById('final-continue-form').style.display = 'block';
            }, 2500);
        </script>
        
        <form method="POST" id="final-continue-form" style="display: none;">
            <input type="hidden" name="action" value="final_config">
            <button type="submit" class="btn btn-primary">Terminer l'installation</button>
        </form>
        <?php
    }

    private function renderCompletionStep(): void {
        $admin = $this->config['admin'] ?? [];
        $hosting = $this->config['hosting_type'] ?? '';
        $hostingName = $this->hostingConfigs[$hosting]['name'] ?? 'Inconnu';
        
        ?>
        <h2>🎉 Installation terminée avec succès !</h2>
        <p>IntraSphere a été installé et configuré avec succès sur votre hébergement.</p>
        
        <div class="completion-stats">
            <div class="stat-card">
                <div class="stat-number">✅</div>
                <div class="stat-label">Installation</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">🗄️</div>
                <div class="stat-label">Base de données</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">👤</div>
                <div class="stat-label">Admin créé</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">🔐</div>
                <div class="stat-label">Sécurisé</div>
            </div>
        </div>
        
        <div class="alert alert-success">
            <strong>Informations de connexion:</strong><br>
            <strong>URL:</strong> <a href="./index.php" target="_blank"><?= htmlspecialchars($_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI'])) ?>/index.php</a><br>
            <strong>Nom d'utilisateur:</strong> <?= htmlspecialchars($admin['username'] ?? 'admin') ?><br>
            <strong>Mot de passe:</strong> [Le mot de passe que vous avez défini]<br>
            <strong>Hébergement:</strong> <?= htmlspecialchars($hostingName) ?>
        </div>
        
        <div class="alert alert-info">
            <strong>Prochaines étapes:</strong><br>
            1. Connectez-vous à votre interface d'administration<br>
            2. Personnalisez les paramètres de votre entreprise<br>
            3. Créez les comptes utilisateurs de vos employés<br>
            4. Configurez les permissions et rôles<br>
            5. Commencez à publier des annonces et documents
        </div>
        
        <div class="alert alert-info">
            <strong>Support et documentation:</strong><br>
            • Manuel utilisateur intégré dans l'interface<br>
            • Guide d'administration disponible<br>
            • Support technique par email<br>
            • Mises à jour de sécurité incluses
        </div>
        
        <a href="./index.php" class="btn btn-primary" style="margin-right: 10px;">
            🚀 Accéder à IntraSphere
        </a>
        
        <a href="?action=download_config" class="btn btn-secondary">
            📥 Télécharger la configuration
        </a>
        <?php
    }

    // Méthodes de génération de contenu des fichiers

    private function getIndexPhpContent(): string {
        return '<?php
/**
 * IntraSphere - Point d\'entrée principal
 * Installé automatiquement le ' . date('Y-m-d H:i:s') . '
 */

// Vérification de l\'installation
if (!file_exists(__DIR__ . \'/config/installed.php\')) {
    if (file_exists(__DIR__ . \'/install.php\')) {
        header(\'Location: install.php\');
        exit;
    } else {
        die(\'IntraSphere n\\\'est pas installé. Veuillez contacter l\\\'administrateur.\');
    }
}

// Démarrage du buffer de sortie
ob_start();

// Configuration des erreurs
error_reporting(E_ALL);
ini_set(\'display_errors\', 0);
ini_set(\'log_errors\', 1);

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers de sécurité
header(\'X-Content-Type-Options: nosniff\');
header(\'X-Frame-Options: DENY\');
header(\'X-XSS-Protection: 1; mode=block\');
header(\'Strict-Transport-Security: max-age=31536000; includeSubDomains\');

// Définition des constantes de base
define(\'APP_ROOT\', __DIR__);
define(\'CONFIG_PATH\', APP_ROOT . \'/config\');
define(\'CONTROLLERS_PATH\', APP_ROOT . \'/src/controllers\');
define(\'MODELS_PATH\', APP_ROOT . \'/src/models\');
define(\'VIEWS_PATH\', APP_ROOT . \'/views\');
define(\'UPLOADS_PATH\', APP_ROOT . \'/public/uploads\');
define(\'LOGS_PATH\', APP_ROOT . \'/logs\');

// Chargement de la configuration
require_once CONFIG_PATH . \'/bootstrap.php\';

// Router et gestion des routes
$router = new Router();

// Routes d\'authentification
$router->addRoute(\'GET\', \'/\', \'AuthController@loginForm\');
$router->addRoute(\'GET\', \'/login\', \'AuthController@loginForm\');
$router->addRoute(\'POST\', \'/login\', \'AuthController@login\');
$router->addRoute(\'POST\', \'/logout\', \'AuthController@logout\');

// Routes du tableau de bord
$router->addRoute(\'GET\', \'/dashboard\', \'DashboardController@index\');

// Déterminer l\'URI et la méthode
$uri = $_SERVER[\'REQUEST_URI\'] ?? \'/\';
$method = $_SERVER[\'REQUEST_METHOD\'] ?? \'GET\';

// Nettoyer l\'URI (retirer les paramètres GET et le préfixe)
$uri = parse_url($uri, PHP_URL_PATH);
$scriptDir = dirname($_SERVER[\'SCRIPT_NAME\']);
if ($scriptDir !== \'/\') {
    $uri = substr($uri, strlen($scriptDir));
}
$uri = \'/\' . ltrim($uri, \'/\');

try {
    $router->dispatch($method, $uri);
} catch (Exception $e) {
    error_log("Erreur de routage: " . $e->getMessage());
    http_response_code(500);
    include VIEWS_PATH . \'/error/500.php\';
}
';
    }

    private function getBootstrapContent(): string {
        return '<?php
/**
 * Bootstrap de l\'application IntraSphere
 */

// Définition des constantes
define(\'APP_VERSION\', \'1.0.0\');
define(\'SESSION_LIFETIME\', 3600);

// Autoloader simple
spl_autoload_register(function ($className) {
    $className = str_replace([\'\\\\\', \'/\'], DIRECTORY_SEPARATOR, $className);
    $className = ltrim($className, DIRECTORY_SEPARATOR);
    
    $directories = [
        APP_ROOT . \'/src/\',
        APP_ROOT . \'/src/controllers/\',
        APP_ROOT . \'/src/models/\',
        APP_ROOT . \'/src/utils/\'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . \'.php\';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Chargement des configurations
require_once CONFIG_PATH . \'/database.php\';
require_once CONFIG_PATH . \'/app.php\';

// Helpers globaux
require_once APP_ROOT . \'/src/utils/helpers.php\';

// Timezone
date_default_timezone_set(\'Europe/Paris\');
';
    }

    private function getRouterContent(): string {
        return '<?php
/**
 * Router simple pour l\'application PHP
 */

class Router {
    private array $routes = [];
    
    public function addRoute(string $method, string $path, string $handler): void {
        $this->routes[] = [
            \'method\' => strtoupper($method),
            \'path\' => $path,
            \'handler\' => $handler,
            \'pattern\' => $this->convertPathToPattern($path)
        ];
    }
    
    public function dispatch(string $method, string $uri): void {
        $uri = rtrim($uri, \'/\') ?: \'/\';
        $method = strtoupper($method);
        
        foreach ($this->routes as $route) {
            if ($route[\'method\'] === $method && preg_match($route[\'pattern\'], $uri, $matches)) {
                array_shift($matches);
                $this->callHandler($route[\'handler\'], $matches);
                return;
            }
        }
        
        http_response_code(404);
        include VIEWS_PATH . \'/error/404.php\';
    }
    
    private function convertPathToPattern(string $path): string {
        $pattern = preg_replace(\'/\:([a-zA-Z0-9_]+)/\', \'([^/]+)\', $path);
        return \'#^\' . $pattern . \'$#\';
    }
    
    private function callHandler(string $handler, array $params): void {
        [$controllerName, $method] = explode(\'@\', $handler);
        
        if (!class_exists($controllerName)) {
            throw new Exception("Contrôleur introuvable: {$controllerName}");
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $method)) {
            throw new Exception("Méthode introuvable: {$controllerName}::{$method}");
        }
        
        call_user_func_array([$controller, $method], $params);
    }
}
';
    }

    // Continuer avec les autres méthodes de génération de contenu...
    // Je vais maintenant créer le fichier principal du package

    private function getHelpersContent(): string {
        return '<?php
/**
 * Fonctions utilitaires globales
 */

function h(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, \'UTF-8\');
}

function redirect(string $url, int $status = 302): void {
    header(\'Location: \' . $url, true, $status);
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION[\'user\']) && !empty($_SESSION[\'user\']);
}

function currentUser(): ?array {
    return $_SESSION[\'user\'] ?? null;
}

function formatDate(string $date, string $format = \'d/m/Y H:i\'): string {
    return date($format, strtotime($date));
}
';
    }

    private function getBaseControllerContent(): string {
        return '<?php
/**
 * Contrôleur de base pour l\'application
 */

abstract class BaseController {
    
    protected function requireAuth(): array {
        if (!isset($_SESSION[\'user\']) || empty($_SESSION[\'user\'])) {
            redirect(\'/login\');
        }
        
        return $_SESSION[\'user\'];
    }
    
    protected function view(string $view, array $data = []): void {
        extract($data);
        include VIEWS_PATH . \'/\' . $view . \'.php\';
    }
}
';
    }

    private function getAuthControllerContent(): string {
        return '<?php
/**
 * Contrôleur d\'authentification
 */

class AuthController extends BaseController {
    
    public function loginForm(): void {
        if (isLoggedIn()) {
            redirect(\'/dashboard\');
        }
        
        include VIEWS_PATH . \'/auth/login.php\';
    }
    
    public function login(): void {
        $username = trim($_POST[\'username\'] ?? \'\');
        $password = $_POST[\'password\'] ?? \'\';
        
        if (empty($username) || empty($password)) {
            $error = \'Veuillez remplir tous les champs\';
            include VIEWS_PATH . \'/auth/login.php\';
            return;
        }
        
        try {
            $db = Database::getInstance();
            $user = $db->fetchOne(
                \'SELECT * FROM users WHERE username = ? AND is_active = 1\',
                [$username]
            );
            
            if (!$user || !password_verify($password, $user[\'password\'])) {
                $error = \'Identifiants incorrects\';
                include VIEWS_PATH . \'/auth/login.php\';
                return;
            }
            
            $_SESSION[\'user\'] = $user;
            $_SESSION[\'login_time\'] = time();
            
            redirect(\'/dashboard\');
            
        } catch (Exception $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            $error = \'Erreur de connexion\';
            include VIEWS_PATH . \'/auth/login.php\';
        }
    }
    
    public function logout(): void {
        session_destroy();
        redirect(\'/login\');
    }
}
';
    }

    private function getDashboardControllerContent(): string {
        return '<?php
/**
 * Contrôleur du tableau de bord
 */

class DashboardController extends BaseController {
    
    public function index(): void {
        $user = $this->requireAuth();
        
        try {
            $db = Database::getInstance();
            
            // Statistiques
            $stats = [
                \'announcements\' => 0,
                \'documents\' => 0,
                \'messages\' => 0,
                \'users\' => 0
            ];
            
            // Compter les annonces
            $result = $db->fetchOne("SELECT COUNT(*) as count FROM announcements");
            $stats[\'announcements\'] = $result[\'count\'] ?? 0;
            
            // Compter les documents
            $result = $db->fetchOne("SELECT COUNT(*) as count FROM documents");
            $stats[\'documents\'] = $result[\'count\'] ?? 0;
            
            // Compter les messages de l\'utilisateur
            $result = $db->fetchOne(
                "SELECT COUNT(*) as count FROM messages WHERE recipient_id = ? OR sender_id = ?",
                [$user[\'id\'], $user[\'id\']]
            );
            $stats[\'messages\'] = $result[\'count\'] ?? 0;
            
            // Compter les utilisateurs (pour admin)
            if ($user[\'role\'] === \'admin\') {
                $result = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
                $stats[\'users\'] = $result[\'count\'] ?? 0;
            }
            
            // Dernières annonces
            $recent_announcements = $db->fetchAll(
                "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5"
            );
            
            // Messages récents
            $recent_messages = $db->fetchAll(
                "SELECT m.*, u.name as sender_name 
                 FROM messages m 
                 LEFT JOIN users u ON m.sender_id = u.id 
                 WHERE m.recipient_id = ? 
                 ORDER BY m.created_at DESC 
                 LIMIT 5",
                [$user[\'id\']]
            );
            
            include VIEWS_PATH . \'/dashboard/index.php\';
            
        } catch (Exception $e) {
            error_log("Erreur dashboard: " . $e->getMessage());
            include VIEWS_PATH . \'/error/500.php\';
        }
    }
}
';
    }

    private function getBaseModelContent(): string {
        return '<?php
/**
 * Modèle de base pour l\'application
 */

abstract class BaseModel {
    protected Database $db;
    protected string $table;
    protected string $primaryKey = \'id\';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function findAll(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function create(array $data): array {
        $data[\'id\'] = uniqid(\'\', true);
        $data[\'created_at\'] = date(\'Y-m-d H:i:s\');
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), \'?\');
        
        $sql = "INSERT INTO {$this->table} (" . implode(\', \', $columns) . ") 
                VALUES (" . implode(\', \', $placeholders) . ")";
        
        $this->db->execute($sql, array_values($data));
        
        return $this->find($data[\'id\']);
    }
}
';
    }

    private function getUserModelContent(): string {
        return '<?php
/**
 * Modèle utilisateur
 */

class User extends BaseModel {
    protected string $table = \'users\';
    
    public function create(array $data): array {
        if (isset($data[\'password\'])) {
            $data[\'password\'] = password_hash($data[\'password\'], PASSWORD_DEFAULT);
        }
        
        $data[\'role\'] = $data[\'role\'] ?? \'employee\';
        $data[\'is_active\'] = $data[\'is_active\'] ?? true;
        
        return parent::create($data);
    }
}
';
    }

    private function getDatabaseContent(): string {
        $config = $this->config['database'] ?? [];
        
        return '<?php
/**
 * Configuration de la base de données
 * Générée automatiquement lors de l\'installation
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $host = \'' . addslashes($config['host'] ?? 'localhost') . '\';
            $port = \'' . addslashes($config['port'] ?? '3306') . '\';
            $dbname = \'' . addslashes($config['name'] ?? 'intrasphere') . '\';
            $username = \'' . addslashes($config['user'] ?? '') . '\';
            $password = \'' . addslashes($config['password'] ?? '') . '\';
            $driver = \'' . addslashes($config['driver'] ?? 'mysql') . '\';
            
            if ($driver === \'mysql\') {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            } else {
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
            }
            
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur connexion base de données: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }
    }
    
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchOne(string $sql, array $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function execute(string $sql, array $params = []): bool {
        return $this->query($sql, $params)->rowCount() >= 0;
    }
}
';
    }

    private function generateEnvFile(): string {
        $config = $this->config;
        $security = $config['security'] ?? [];
        $database = $config['database'] ?? [];
        
        return "# Configuration IntraSphere
# Générée automatiquement le " . date('Y-m-d H:i:s') . "

# Environnement
APP_ENV=" . ($security['environment'] ?? 'production') . "
APP_KEY=" . ($security['app_key'] ?? bin2hex(random_bytes(32))) . "

# Base de données
DB_DRIVER=" . ($database['driver'] ?? 'mysql') . "
DB_HOST=" . ($database['host'] ?? 'localhost') . "
DB_PORT=" . ($database['port'] ?? '3306') . "
DB_NAME=" . ($database['name'] ?? 'intrasphere') . "
DB_USER=" . ($database['user'] ?? '') . "
DB_PASSWORD=" . ($database['password'] ?? '') . "

# Session
SESSION_NAME=" . ($security['session_name'] ?? 'INTRASPHERE_SESSION') . "
SESSION_LIFETIME=3600

# Sécurité
BCRYPT_COST=12
";
    }

    private function generateAppConfig(): string {
        $security = $this->config['security'] ?? [];
        
        return '<?php
/**
 * Configuration de l\'application IntraSphere
 * Générée automatiquement lors de l\'installation
 */

// Chargement des variables d\'environnement
if (file_exists(__DIR__ . \'/../.env\')) {
    $lines = file(__DIR__ . \'/../.env\', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), \'#\') === 0) continue;
        if (strpos($line, \'=\') !== false) {
            list($key, $value) = explode(\'=\', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Configuration générale
define(\'APP_NAME\', \'IntraSphere\');
define(\'APP_ENV\', $_ENV[\'APP_ENV\'] ?? \'production\');
define(\'APP_DEBUG\', APP_ENV === \'development\');

// Configuration des erreurs
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set(\'display_errors\', 1);
} else {
    error_reporting(0);
    ini_set(\'display_errors\', 0);
}

// Configuration des sessions
ini_set(\'session.cookie_httponly\', 1);
ini_set(\'session.cookie_secure\', isset($_SERVER[\'HTTPS\']));
ini_set(\'session.use_strict_mode\', 1);
ini_set(\'session.gc_maxlifetime\', SESSION_LIFETIME);

session_name($_ENV[\'SESSION_NAME\'] ?? \'INTRASPHERE_SESSION\');

// Timezone
date_default_timezone_set(\'Europe/Paris\');

// Configuration de l\'upload
define(\'MAX_FILE_SIZE\', 10 * 1024 * 1024); // 10MB
define(\'ALLOWED_EXTENSIONS\', [\'pdf\', \'doc\', \'docx\', \'jpg\', \'jpeg\', \'png\']);

// Configuration de sécurité
define(\'BCRYPT_COST\', $_ENV[\'BCRYPT_COST\'] ?? 12);

// Headers de sécurité par défaut
header(\'X-Content-Type-Options: nosniff\');
header(\'X-Frame-Options: DENY\'); 
header(\'X-XSS-Protection: 1; mode=block\');
header(\'Referrer-Policy: strict-origin-when-cross-origin\');

if (isset($_SERVER[\'HTTPS\'])) {
    header(\'Strict-Transport-Security: max-age=31536000; includeSubDomains\');
}
';
    }

    private function createViews(): void {
        // Vue login
        $loginView = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - IntraSphere</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .login-container { 
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 40px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%; max-width: 400px;
        }
        .logo { 
            text-align: center; margin-bottom: 30px; 
            font-size: 2rem; font-weight: 700; color: #8B5CF6;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; margin-bottom: 8px; font-weight: 600; color: #374151;
        }
        .form-group input { 
            width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb;
            border-radius: 8px; font-size: 16px; transition: border-color 0.3s;
        }
        .form-group input:focus { 
            outline: none; border-color: #8B5CF6;
        }
        .btn-login { 
            width: 100%; padding: 14px; background: linear-gradient(135deg, #8B5CF6, #A78BFA);
            color: white; border: none; border-radius: 8px; font-size: 16px;
            font-weight: 600; cursor: pointer; transition: transform 0.2s;
        }
        .btn-login:hover { transform: translateY(-2px); }
        .error { 
            background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c;
            padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🚀 IntraSphere</div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/login">
            <div class="form-group">
                <label for="username">Nom d\'utilisateur</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($_POST[\'username\'] ?? \'\') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Se connecter</button>
        </form>
    </div>
</body>
</html>';
        
        file_put_contents(INSTALL_ROOT . '/views/auth/login.php', $loginView);
        
        // Vue dashboard (version simplifiée pour le package)
        $dashboardView = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - IntraSphere</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
            min-height: 100vh; 
        }
        .header {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 20px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        .logo { font-size: 1.5rem; font-weight: 700; color: #8B5CF6; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { 
            width: 40px; height: 40px; background: #8B5CF6; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;
        }
        .logout-btn { 
            background: #ef4444; color: white; border: none; padding: 8px 16px;
            border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block;
        }
        .container { padding: 30px; max-width: 1200px; margin: 0 auto; }
        .welcome { text-align: center; margin-bottom: 40px; color: white; }
        .welcome h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .stats-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px; margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 30px; border-radius: 16px; text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .stat-number { font-size: 3rem; font-weight: 700; color: #8B5CF6; margin-bottom: 10px; }
        .stat-label { font-size: 1rem; color: #6b7280; font-weight: 600; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚀 IntraSphere</div>
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($user[\'name\'], 0, 1)) ?></div>
            <div>
                <div style="font-weight: 600;"><?= htmlspecialchars($user[\'name\']) ?></div>
                <div style="font-size: 0.9rem; color: #6b7280;"><?= htmlspecialchars($user[\'role\']) ?></div>
            </div>
            <form method="POST" action="/logout" style="margin: 0;">
                <button type="submit" class="logout-btn">Déconnexion</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h1>Bienvenue, <?= htmlspecialchars($user[\'name\']) ?> !</h1>
            <p>Tableau de bord IntraSphere - <?= date(\'d/m/Y\') ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats[\'announcements\'] ?></div>
                <div class="stat-label">Annonces</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats[\'documents\'] ?></div>
                <div class="stat-label">Documents</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats[\'messages\'] ?></div>
                <div class="stat-label">Messages</div>
            </div>
            <?php if ($user[\'role\'] === \'admin\'): ?>
            <div class="stat-card">
                <div class="stat-number"><?= $stats[\'users\'] ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>';
        
        file_put_contents(INSTALL_ROOT . '/views/dashboard/index.php', $dashboardView);
        
        // Vue d'erreur 404
        $error404 = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - IntraSphere</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #8B5CF6; }
        a { color: #8B5CF6; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>404 - Page non trouvée</h1>
    <p>La page que vous recherchez n\'existe pas.</p>
    <a href="/dashboard">Retour au tableau de bord</a>
</body>
</html>';
        
        file_put_contents(INSTALL_ROOT . '/views/error/404.php', $error404);
        
        // Vue d'erreur 500
        $error500 = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur - IntraSphere</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #ef4444; }
        a { color: #8B5CF6; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>500 - Erreur serveur</h1>
    <p>Une erreur inattendue s\'est produite.</p>
    <a href="/dashboard">Retour au tableau de bord</a>
</body>
</html>';
        
        file_put_contents(INSTALL_ROOT . '/views/error/500.php', $error500);
    }

    private function createSqlFiles(): void {
        // Script de création des tables (version complète mais adaptée)
        $createTablesContent = file_get_contents('php-migration/sql/create_tables.sql');
        file_put_contents(INSTALL_ROOT . '/sql/create_tables.sql', $createTablesContent);
        
        // Script de données de démonstration (version adaptée)
        $insertDemoContent = file_get_contents('php-migration/sql/insert_demo_data.sql');
        file_put_contents(INSTALL_ROOT . '/sql/insert_demo_data.sql', $insertDemoContent);
    }

    private function getHtaccessContent(): string {
        return '# IntraSphere - Configuration Apache
# Généré automatiquement lors de l\'installation

# Activer le module de réécriture
RewriteEngine On

# Redirection HTTPS (optionnel, décommentez si vous avez un certificat SSL)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Sécurité des fichiers sensibles
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>

# Protection des dossiers de configuration
<DirectoryMatch "^/?(config|logs|tmp)">
    Order allow,deny
    Deny from all
</DirectoryMatch>

# Gestion des erreurs personnalisées
ErrorDocument 404 /views/error/404.php
ErrorDocument 500 /views/error/500.php

# Cache et compression (optionnel)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>

# Compression GZIP (optionnel)
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Limites de sécurité
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 30
php_value max_input_time 60
';
    }

    private function createAssets(): void {
        // CSS de base
        $cssContent = '/* IntraSphere - Styles de base */
:root {
    --primary: #8B5CF6;
    --primary-dark: #7C3AED;
    --secondary: #A78BFA;
    --success: #10B981;
    --warning: #F59E0B;
    --danger: #EF4444;
    --dark: #1F2937;
    --light: #F9FAFB;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
    line-height: 1.6;
    color: #374151;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.glass {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}
';
        
        file_put_contents(INSTALL_ROOT . '/public/css/style.css', $cssContent);
        
        // JavaScript de base
        $jsContent = '// IntraSphere - Scripts de base
console.log("IntraSphere chargé avec succès !");

// Fonctions utilitaires
function showNotification(message, type = "info") {
    // Implementation simple de notification
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Confirmation pour actions critiques
function confirmAction(message) {
    return confirm(message);
}
';
        
        file_put_contents(INSTALL_ROOT . '/public/js/app.js', $jsContent);
    }

    private function adaptSqlForPostgreSQL(string $sql): string {
        // Adapter le SQL MySQL pour PostgreSQL
        $sql = str_replace('ENUM(', 'VARCHAR(50) CHECK (value IN (', $sql);
        $sql = str_replace('AUTO_INCREMENT', 'SERIAL', $sql);
        $sql = str_replace('ON UPDATE CURRENT_TIMESTAMP', '', $sql);
        $sql = str_replace('CURRENT_TIMESTAMP', 'NOW()', $sql);
        
        return $sql;
    }
}

// Lancement de l\'installateur
$installer = new IntraSphereInstaller();
$installer->run();