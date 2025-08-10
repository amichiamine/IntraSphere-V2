<?php
/**
 * Script d'installation automatisé IntraSphere PHP
 * Version : 1.0.0
 * Compatibilité : Hébergement web mutualisé (cPanel, OVH, Ionos, etc.)
 */

// Configuration des erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de la session pour suivre l'installation
session_start();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation IntraSphere - Plateforme Intranet d'Entreprise</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #667eea;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .step {
            margin-bottom: 30px;
            padding: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .step.active {
            border-color: #667eea;
            background: #f8faff;
        }
        
        .step.completed {
            border-color: #10b981;
            background: #f0fdf4;
        }
        
        .step.error {
            border-color: #ef4444;
            background: #fef2f2;
        }
        
        .step-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .step.completed .step-number {
            background: #10b981;
        }
        
        .step.error .step-number {
            background: #ef4444;
        }
        
        .step-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
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
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: #10b981;
            color: white;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin: 20px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.5s ease;
        }
        
        .config-preview {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 14px;
            white-space: pre-line;
        }
        
        .hosting-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .hosting-option {
            padding: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .hosting-option:hover,
        .hosting-option.selected {
            border-color: #667eea;
            background: #f8faff;
        }
        
        .hosting-option h3 {
            margin-bottom: 8px;
            color: #374151;
        }
        
        .hosting-option p {
            color: #6b7280;
            font-size: 14px;
        }
        
        .log-output {
            background: #1f2937;
            color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
            max-height: 300px;
            overflow-y: auto;
            margin: 20px 0;
        }
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>🚀 IntraSphere</h1>
                <p>Installation automatisée de votre plateforme intranet d'entreprise</p>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            
            <!-- Navigation Steps -->
            <div style="display: flex; justify-content: center; margin-bottom: 30px;">
                <span id="stepIndicator">Étape 1 sur 5 - Configuration</span>
            </div>
            
            <?php
            
            class IntraSphereInstaller {
                private $steps = [
                    1 => 'Configuration initiale',
                    2 => 'Test de connexion base de données', 
                    3 => 'Installation des fichiers',
                    4 => 'Création de la base de données',
                    5 => 'Finalisation'
                ];
                
                private $currentStep = 1;
                private $hostingTypes = [
                    'cpanel' => [
                        'name' => 'cPanel (Hébergement mutualisé)',
                        'template' => [
                            'host' => 'localhost',
                            'port' => '3306',
                            'driver' => 'mysql',
                            'dbname_format' => '{user}_{dbname}',
                            'username_format' => '{user}_{dbname}'
                        ]
                    ],
                    'ovh' => [
                        'name' => 'OVH Mutualisé',
                        'template' => [
                            'host' => 'mysql-{dbname}.hosting.ovh.net',
                            'port' => '3306',
                            'driver' => 'mysql',
                            'dbname_format' => '{dbname}',
                            'username_format' => '{dbname}'
                        ]
                    ],
                    'ionos' => [
                        'name' => '1&1 / Ionos',
                        'template' => [
                            'host' => 'db{id}.hosting.1and1.com',
                            'port' => '3306',
                            'driver' => 'mysql',
                            'dbname_format' => 'db{id}',
                            'username_format' => 'dbo{id}'
                        ]
                    ],
                    'local' => [
                        'name' => 'Développement Local (XAMPP/WAMP)',
                        'template' => [
                            'host' => 'localhost',
                            'port' => '3306',
                            'driver' => 'mysql',
                            'dbname_format' => '{dbname}',
                            'username_format' => 'root',
                            'password' => ''
                        ]
                    ],
                    'custom' => [
                        'name' => 'Configuration personnalisée',
                        'template' => [
                            'host' => '',
                            'port' => '3306',
                            'driver' => 'mysql',
                            'dbname_format' => '{dbname}',
                            'username_format' => '{username}'
                        ]
                    ]
                ];
                
                public function __construct() {
                    $this->currentStep = $_SESSION['install_step'] ?? 1;
                }
                
                public function getCurrentStep() {
                    return $this->currentStep;
                }
                
                public function getStepTitle($step) {
                    return $this->steps[$step] ?? 'Inconnu';
                }
                
                public function processStep() {
                    switch ($this->currentStep) {
                        case 1:
                            return $this->showConfigurationForm();
                        case 2:
                            return $this->testDatabaseConnection();
                        case 3:
                            return $this->installFiles();
                        case 4:
                            return $this->createDatabase();
                        case 5:
                            return $this->finalize();
                        default:
                            return $this->showError('Étape inconnue');
                    }
                }
                
                private function showConfigurationForm() {
                    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'configure') {
                        return $this->saveConfiguration();
                    }
                    
                    $selectedHosting = $_SESSION['hosting_type'] ?? 'cpanel';
                    $config = $_SESSION['db_config'] ?? [];
                    
                    ?>
                    <div class="step active">
                        <div class="step-header">
                            <div class="step-number">1</div>
                            <div class="step-title">Configuration de la base de données</div>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="configure">
                            
                            <!-- Sélection du type d'hébergement -->
                            <div class="form-group">
                                <label>Type d'hébergement :</label>
                                <div class="hosting-options">
                                    <?php foreach ($this->hostingTypes as $key => $hosting): ?>
                                        <div class="hosting-option <?= $selectedHosting === $key ? 'selected' : '' ?>" 
                                             onclick="selectHosting('<?= $key ?>')">
                                            <h3><?= htmlspecialchars($hosting['name']) ?></h3>
                                            <?php if ($key === 'cpanel'): ?>
                                                <p>cPanel, DirectAdmin, Plesk</p>
                                            <?php elseif ($key === 'ovh'): ?>
                                                <p>Mutualisé OVH</p>
                                            <?php elseif ($key === 'ionos'): ?>
                                                <p>1&1, Ionos</p>
                                            <?php elseif ($key === 'local'): ?>
                                                <p>XAMPP, WAMP, MAMP</p>
                                            <?php else: ?>
                                                <p>Configuration manuelle</p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <input type="hidden" name="hosting_type" id="hostingType" value="<?= $selectedHosting ?>">
                            </div>
                            
                            <!-- Configuration base de données -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="db_host">Serveur de base de données :</label>
                                    <input type="text" id="db_host" name="db_host" 
                                           value="<?= htmlspecialchars($config['host'] ?? 'localhost') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="db_port">Port :</label>
                                    <input type="text" id="db_port" name="db_port" 
                                           value="<?= htmlspecialchars($config['port'] ?? '3306') ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="db_name">Nom de la base de données :</label>
                                    <input type="text" id="db_name" name="db_name" 
                                           value="<?= htmlspecialchars($config['dbname'] ?? 'intrasphere') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="db_user">Utilisateur :</label>
                                    <input type="text" id="db_user" name="db_user" 
                                           value="<?= htmlspecialchars($config['username'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="db_password">Mot de passe :</label>
                                <input type="password" id="db_password" name="db_password" 
                                       value="<?= htmlspecialchars($config['password'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_password">Mot de passe administrateur IntraSphere :</label>
                                <input type="password" id="admin_password" name="admin_password" 
                                       value="<?= htmlspecialchars($config['admin_password'] ?? '') ?>" required>
                                <small style="color: #6b7280; font-size: 14px;">
                                    Ce sera le mot de passe du compte admin pour accéder à IntraSphere
                                </small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                Suivant - Tester la connexion
                            </button>
                        </form>
                    </div>
                    
                    <script>
                        function selectHosting(type) {
                            // Supprimer la sélection précédente
                            document.querySelectorAll('.hosting-option').forEach(el => {
                                el.classList.remove('selected');
                            });
                            
                            // Sélectionner le nouveau
                            event.target.classList.add('selected');
                            document.getElementById('hostingType').value = type;
                            
                            // Pré-remplir les champs selon le type
                            const templates = <?= json_encode($this->hostingTypes) ?>;
                            const template = templates[type].template;
                            
                            if (template.host && template.host !== '') {
                                document.getElementById('db_host').value = template.host.replace('{dbname}', 'intrasphere');
                            }
                            
                            if (template.port) {
                                document.getElementById('db_port').value = template.port;
                            }
                            
                            if (template.username_format && type === 'local') {
                                document.getElementById('db_user').value = 'root';
                            }
                        }
                    </script>
                    <?php
                }
                
                private function saveConfiguration() {
                    $config = [
                        'host' => $_POST['db_host'],
                        'port' => $_POST['db_port'],
                        'dbname' => $_POST['db_name'],
                        'username' => $_POST['db_user'],
                        'password' => $_POST['db_password'],
                        'driver' => 'mysql',
                        'admin_password' => $_POST['admin_password']
                    ];
                    
                    $_SESSION['hosting_type'] = $_POST['hosting_type'];
                    $_SESSION['db_config'] = $config;
                    $_SESSION['install_step'] = 2;
                    
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                }
                
                private function testDatabaseConnection() {
                    $config = $_SESSION['db_config'] ?? [];
                    
                    if (empty($config)) {
                        $_SESSION['install_step'] = 1;
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    }
                    
                    if ($_POST && isset($_POST['action'])) {
                        if ($_POST['action'] === 'test_connection') {
                            return $this->performConnectionTest();
                        } elseif ($_POST['action'] === 'continue') {
                            $_SESSION['install_step'] = 3;
                            header('Location: ' . $_SERVER['PHP_SELF']);
                            exit;
                        }
                    }
                    
                    ?>
                    <div class="step completed">
                        <div class="step-header">
                            <div class="step-number">✓</div>
                            <div class="step-title">Configuration sauvegardée</div>
                        </div>
                    </div>
                    
                    <div class="step active">
                        <div class="step-header">
                            <div class="step-number">2</div>
                            <div class="step-title">Test de la connexion base de données</div>
                        </div>
                        
                        <div class="config-preview">
                            <strong>Configuration actuelle :</strong>
                            Serveur: <?= htmlspecialchars($config['host']) ?>:<?= htmlspecialchars($config['port']) ?>
                            Base de données: <?= htmlspecialchars($config['dbname']) ?>
                            Utilisateur: <?= htmlspecialchars($config['username']) ?>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="test_connection">
                            <button type="submit" class="btn btn-primary">
                                Tester la connexion
                            </button>
                        </form>
                        
                        <div id="connectionResult"></div>
                    </div>
                    <?php
                }
                
                private function performConnectionTest() {
                    $config = $_SESSION['db_config'];
                    
                    try {
                        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
                        $pdo = new PDO($dsn, $config['username'], $config['password'], [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_TIMEOUT => 5
                        ]);
                        
                        // Tester si la base de données existe
                        $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
                        $stmt->execute([$config['dbname']]);
                        $dbExists = $stmt->fetch() !== false;
                        
                        ?>
                        <div class="alert alert-success">
                            <strong>✅ Connexion réussie !</strong><br>
                            <?php if ($dbExists): ?>
                                La base de données "<?= htmlspecialchars($config['dbname']) ?>" existe déjà.
                            <?php else: ?>
                                La base de données "<?= htmlspecialchars($config['dbname']) ?>" sera créée automatiquement.
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="continue">
                            <button type="submit" class="btn btn-success">
                                Continuer l'installation
                            </button>
                        </form>
                        <?php
                        
                    } catch (PDOException $e) {
                        ?>
                        <div class="alert alert-error">
                            <strong>❌ Erreur de connexion :</strong><br>
                            <?= htmlspecialchars($e->getMessage()) ?>
                        </div>
                        
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?step=1" class="btn btn-danger">
                            Retour à la configuration
                        </a>
                        <?php
                    }
                }
                
                private function installFiles() {
                    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'install_files') {
                        return $this->performFileInstallation();
                    }
                    
                    ?>
                    <div class="step completed">
                        <div class="step-header">
                            <div class="step-number">✓</div>
                            <div class="step-title">Connexion base de données validée</div>
                        </div>
                    </div>
                    
                    <div class="step active">
                        <div class="step-header">
                            <div class="step-number">3</div>
                            <div class="step-title">Installation des fichiers</div>
                        </div>
                        
                        <p>Cette étape va :</p>
                        <ul style="margin: 20px 0; padding-left: 40px;">
                            <li>Extraire tous les fichiers IntraSphere</li>
                            <li>Configurer les permissions</li>
                            <li>Générer le fichier de configuration .env</li>
                            <li>Créer les dossiers nécessaires</li>
                        </ul>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="install_files">
                            <button type="submit" class="btn btn-primary">
                                Installer les fichiers
                            </button>
                        </form>
                    </div>
                    <?php
                }
                
                private function performFileInstallation() {
                    $errors = [];
                    $success = [];
                    
                    // Créer les dossiers nécessaires
                    $directories = [
                        'config',
                        'src',
                        'src/controllers',
                        'src/controllers/Api',
                        'src/models',
                        'src/utils',
                        'views',
                        'views/admin',
                        'views/announcements',
                        'views/auth',
                        'views/dashboard',
                        'views/documents',
                        'views/error',
                        'views/layout',
                        'views/messages',
                        'views/trainings',
                        'public',
                        'public/uploads',
                        'logs',
                        'sql'
                    ];
                    
                    foreach ($directories as $dir) {
                        if (!is_dir($dir)) {
                            if (mkdir($dir, 0755, true)) {
                                $success[] = "Dossier créé : $dir";
                            } else {
                                $errors[] = "Impossible de créer le dossier : $dir";
                            }
                        }
                    }
                    
                    // Générer le fichier .env
                    $config = $_SESSION['db_config'];
                    $envContent = $this->generateEnvFile($config);
                    
                    if (file_put_contents('.env', $envContent)) {
                        $success[] = "Fichier .env créé avec succès";
                    } else {
                        $errors[] = "Impossible de créer le fichier .env";
                    }
                    
                    // Créer le fichier .htaccess pour la sécurité
                    $htaccessContent = $this->generateHtaccessFile();
                    if (file_put_contents('.htaccess', $htaccessContent)) {
                        $success[] = "Fichier .htaccess créé pour la sécurité";
                    }
                    
                    ?>
                    <div class="log-output" id="installLog">
                        <?php if (!empty($success)): ?>
                            <?php foreach ($success as $msg): ?>
                                <div style="color: #10b981;">✅ <?= htmlspecialchars($msg) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <?php foreach ($errors as $msg): ?>
                                <div style="color: #ef4444;">❌ <?= htmlspecialchars($msg) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (empty($errors)): ?>
                        <div class="alert alert-success">
                            <strong>✅ Installation des fichiers réussie !</strong>
                        </div>
                        
                        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                            <input type="hidden" name="step" value="4">
                            <button type="submit" class="btn btn-success">
                                Suivant - Créer la base de données
                            </button>
                        </form>
                        
                        <script>
                            setTimeout(function() {
                                // Redirection automatique
                                window.location.href = '<?= $_SERVER['PHP_SELF'] ?>?auto_next=1';
                            }, 3000);
                        </script>
                    <?php else: ?>
                        <div class="alert alert-error">
                            <strong>❌ Erreurs lors de l'installation</strong><br>
                            Veuillez corriger les erreurs ci-dessus et réessayer.
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    
                    if (empty($errors)) {
                        $_SESSION['install_step'] = 4;
                    }
                }
                
                private function createDatabase() {
                    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'create_db') {
                        return $this->performDatabaseCreation();
                    }
                    
                    ?>
                    <div class="step completed">
                        <div class="step-header">
                            <div class="step-number">✓</div>
                            <div class="step-title">Fichiers installés</div>
                        </div>
                    </div>
                    
                    <div class="step active">
                        <div class="step-header">
                            <div class="step-number">4</div>
                            <div class="step-title">Création de la base de données</div>
                        </div>
                        
                        <p>Cette étape va :</p>
                        <ul style="margin: 20px 0; padding-left: 40px;">
                            <li>Créer la base de données (si nécessaire)</li>
                            <li>Créer toutes les tables nécessaires</li>
                            <li>Insérer les données de démonstration</li>
                            <li>Créer le compte administrateur</li>
                        </ul>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="create_db">
                            <button type="submit" class="btn btn-primary">
                                Créer la base de données
                            </button>
                        </form>
                    </div>
                    <?php
                }
                
                private function performDatabaseCreation() {
                    $config = $_SESSION['db_config'];
                    $errors = [];
                    $success = [];
                    
                    try {
                        // Connexion sans spécifier la base de données
                        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
                        $pdo = new PDO($dsn, $config['username'], $config['password'], [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                        ]);
                        
                        // Créer la base de données si elle n'existe pas
                        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        $success[] = "Base de données '{$config['dbname']}' créée/vérifiée";
                        
                        // Se connecter à la base de données
                        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
                        $pdo = new PDO($dsn, $config['username'], $config['password'], [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                        ]);
                        
                        // Créer les tables
                        $sqlFile = $this->getCreateTablesSql();
                        $statements = explode(';', $sqlFile);
                        
                        foreach ($statements as $statement) {
                            $statement = trim($statement);
                            if (!empty($statement)) {
                                $pdo->exec($statement);
                            }
                        }
                        $success[] = "Tables créées avec succès";
                        
                        // Insérer les données de base
                        $this->insertDemoData($pdo, $config);
                        $success[] = "Données de démonstration insérées";
                        
                        ?>
                        <div class="log-output">
                            <?php foreach ($success as $msg): ?>
                                <div style="color: #10b981;">✅ <?= htmlspecialchars($msg) ?></div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="alert alert-success">
                            <strong>✅ Base de données créée avec succès !</strong>
                        </div>
                        
                        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                            <input type="hidden" name="step" value="5">
                            <button type="submit" class="btn btn-success">
                                Finaliser l'installation
                            </button>
                        </form>
                        
                        <script>
                            setTimeout(function() {
                                window.location.href = '<?= $_SERVER['PHP_SELF'] ?>?auto_final=1';
                            }, 2000);
                        </script>
                        <?php
                        
                        $_SESSION['install_step'] = 5;
                        
                    } catch (PDOException $e) {
                        ?>
                        <div class="alert alert-error">
                            <strong>❌ Erreur lors de la création de la base de données :</strong><br>
                            <?= htmlspecialchars($e->getMessage()) ?>
                        </div>
                        <?php
                    }
                }
                
                private function finalize() {
                    ?>
                    <div class="step completed">
                        <div class="step-header">
                            <div class="step-number">✓</div>
                            <div class="step-title">Base de données créée</div>
                        </div>
                    </div>
                    
                    <div class="step completed">
                        <div class="step-header">
                            <div class="step-number">✓</div>
                            <div class="step-title">Installation terminée !</div>
                        </div>
                        
                        <div class="alert alert-success">
                            <h3 style="margin-bottom: 15px;">🎉 IntraSphere est maintenant installé !</h3>
                            
                            <p><strong>Informations de connexion :</strong></p>
                            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin: 15px 0;">
                                <strong>Administrateur :</strong><br>
                                Nom d'utilisateur : <code>admin</code><br>
                                Mot de passe : <code><?= htmlspecialchars($_SESSION['db_config']['admin_password']) ?></code>
                            </div>
                            
                            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin: 15px 0;">
                                <strong>Comptes de test :</strong><br>
                                Employé : <code>marie.martin</code> / <code>password123</code><br>
                                Modérateur : <code>pierre.dubois</code> / <code>password123</code>
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="index.php" class="btn btn-success" style="margin-right: 10px;">
                                🚀 Accéder à IntraSphere
                            </a>
                            <button onclick="cleanInstaller()" class="btn btn-primary">
                                🧹 Nettoyer l'installateur
                            </button>
                        </div>
                        
                        <div class="alert alert-info">
                            <h4>Étapes suivantes recommandées :</h4>
                            <ol style="margin: 10px 0; padding-left: 20px;">
                                <li>Connectez-vous avec le compte admin</li>
                                <li>Changez le mot de passe administrateur</li>
                                <li>Supprimez ce fichier install.php pour la sécurité</li>
                                <li>Configurez les paramètres de votre entreprise</li>
                                <li>Créez vos utilisateurs</li>
                            </ol>
                        </div>
                    </div>
                    
                    <script>
                        function cleanInstaller() {
                            if (confirm('Voulez-vous supprimer le fichier d\'installation pour des raisons de sécurité ?')) {
                                fetch('<?= $_SERVER['PHP_SELF'] ?>?action=cleanup', {method: 'POST'})
                                .then(() => {
                                    alert('Installateur supprimé avec succès !');
                                    window.location.href = 'index.php';
                                })
                                .catch(() => {
                                    alert('Veuillez supprimer manuellement le fichier install.php');
                                });
                            }
                        }
                    </script>
                    <?php
                    
                    // Nettoyer la session
                    if (isset($_GET['action']) && $_GET['action'] === 'cleanup') {
                        unlink(__FILE__);
                        exit('OK');
                    }
                }
                
                private function generateEnvFile($config) {
                    return "# Configuration IntraSphere - Générée automatiquement
# " . date('Y-m-d H:i:s') . "

DB_DRIVER=mysql
DB_HOST={$config['host']}
DB_PORT={$config['port']}
DB_NAME={$config['dbname']}
DB_USER={$config['username']}
DB_PASSWORD={$config['password']}

APP_ENV=production
SESSION_SECRET=" . bin2hex(random_bytes(32)) . "
PASSWORD_HASH_ALGO=PASSWORD_DEFAULT

# Sécurité
ALLOWED_ORIGINS=*
RATE_LIMIT_ENABLED=true
RATE_LIMIT_MAX_REQUESTS=100
RATE_LIMIT_WINDOW=3600

# Upload
MAX_FILE_SIZE=10485760
ALLOWED_EXTENSIONS=pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif

# Email (à configurer si besoin)
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@intrasphere.local
MAIL_FROM_NAME=IntraSphere
";
                }
                
                private function generateHtaccessFile() {
                    return "# IntraSphere - Configuration Apache
RewriteEngine On

# Redirection des erreurs
ErrorDocument 404 /views/error/404.php
ErrorDocument 500 /views/error/500.php

# Sécurité - Masquer les fichiers sensibles
<Files \".env\">
    Order allow,deny
    Deny from all
</Files>

<Files \"*.log\">
    Order allow,deny
    Deny from all
</Files>

# Protection contre les injections
<IfModule mod_rewrite.c>
    RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
    RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} proc/self/environ [OR]
    RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|\%3D) [OR]
    RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [OR]
    RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC]
    RewriteRule .* - [F]
</IfModule>

# Headers de sécurité
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains\"
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
</IfModule>

# Compression
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

# Cache des ressources statiques
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType image/png \"access plus 1 month\"
    ExpiresByType image/jpg \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 month\"
    ExpiresByType image/gif \"access plus 1 month\"
    ExpiresByType image/ico \"access plus 1 month\"
</IfModule>
";
                }
                
                private function getCreateTablesSql() {
                    return "-- Tables IntraSphere PHP
-- Compatible MySQL

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(50) PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    role ENUM('employee', 'moderator', 'admin') DEFAULT 'employee',
    avatar TEXT,
    employee_id VARCHAR(50) UNIQUE,
    department VARCHAR(255),
    position VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    phone VARCHAR(50),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des annonces
CREATE TABLE IF NOT EXISTS announcements (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('info', 'important', 'event', 'formation') DEFAULT 'info',
    author_id VARCHAR(50),
    author_name VARCHAR(255) NOT NULL,
    image_url TEXT,
    icon VARCHAR(10) DEFAULT 'info',
    is_important BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des documents
CREATE TABLE IF NOT EXISTS documents (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('regulation', 'policy', 'guide', 'procedure') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_url TEXT NOT NULL,
    version VARCHAR(20) DEFAULT '1.0',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des événements
CREATE TABLE IF NOT EXISTS events (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    date DATETIME NOT NULL,
    location VARCHAR(255),
    type ENUM('meeting', 'training', 'social', 'other') DEFAULT 'meeting',
    organizer_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des messages
CREATE TABLE IF NOT EXISTS messages (
    id VARCHAR(50) PRIMARY KEY,
    sender_id VARCHAR(50),
    recipient_id VARCHAR(50),
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des réclamations
CREATE TABLE IF NOT EXISTS complaints (
    id VARCHAR(50) PRIMARY KEY,
    submitter_id VARCHAR(50),
    assigned_to_id VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des formations
CREATE TABLE IF NOT EXISTS trainings (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    duration INT,
    instructor_id VARCHAR(50),
    instructor_name VARCHAR(255) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    location VARCHAR(255),
    max_participants INT DEFAULT 20,
    current_participants INT DEFAULT 0,
    is_mandatory BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_visible BOOLEAN DEFAULT TRUE,
    thumbnail_url TEXT,
    document_urls JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des permissions
CREATE TABLE IF NOT EXISTS permissions (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    permission VARCHAR(100) NOT NULL,
    granted_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_permission (user_id, permission)
);

-- Index pour optimiser les performances
CREATE INDEX idx_announcements_type ON announcements(type);
CREATE INDEX idx_announcements_important ON announcements(is_important);
CREATE INDEX idx_announcements_created ON announcements(created_at);
CREATE INDEX idx_documents_category ON documents(category);
CREATE INDEX idx_events_date ON events(date);
CREATE INDEX idx_events_organizer ON events(organizer_id);
CREATE INDEX idx_messages_recipient ON messages(recipient_id);
CREATE INDEX idx_messages_sender ON messages(sender_id);
CREATE INDEX idx_messages_read ON messages(is_read);
CREATE INDEX idx_complaints_status ON complaints(status);
CREATE INDEX idx_complaints_submitter ON complaints(submitter_id);
CREATE INDEX idx_trainings_active ON trainings(is_active);
CREATE INDEX idx_trainings_start_date ON trainings(start_date)";
                }
                
                private function insertDemoData($pdo, $config) {
                    // Hasher le mot de passe admin
                    $adminPassword = password_hash($config['admin_password'], PASSWORD_DEFAULT);
                    $demoPassword = password_hash('password123', PASSWORD_DEFAULT);
                    
                    // Insérer les utilisateurs
                    $stmt = $pdo->prepare("INSERT INTO users (id, username, password, name, role, employee_id, department, position, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    $users = [
                        ['admin', 'admin', $adminPassword, 'Administrateur Système', 'admin', 'EMP-001', 'IT', 'Administrateur Système', 'admin@intrasphere.com', '+33 1 23 45 67 89'],
                        ['user-2', 'marie.martin', $demoPassword, 'Marie Martin', 'employee', 'EMP-002', 'Marketing', 'Chargée de Communication', 'marie.martin@intrasphere.com', '+33 1 23 45 67 90'],
                        ['user-3', 'pierre.dubois', $demoPassword, 'Pierre Dubois', 'moderator', 'EMP-003', 'RH', 'Responsable RH', 'pierre.dubois@intrasphere.com', '+33 1 23 45 67 91']
                    ];
                    
                    foreach ($users as $user) {
                        $stmt->execute($user);
                    }
                    
                    // Insérer des annonces de démonstration
                    $stmt = $pdo->prepare("INSERT INTO announcements (id, title, content, type, author_id, author_name, icon, is_important) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    $announcements = [
                        ['ann-1', 'Bienvenue sur IntraSphere', 'Bienvenue sur votre nouvelle plateforme intranet ! Découvrez toutes les fonctionnalités disponibles.', 'important', 'admin', 'Administrateur Système', '🎉', true],
                        ['ann-2', 'Formation cybersécurité', 'Formation obligatoire en cybersécurité prévue pour tous les employés.', 'formation', 'user-3', 'Pierre Dubois', '🔒', false]
                    ];
                    
                    foreach ($announcements as $announcement) {
                        $stmt->execute($announcement);
                    }
                }
                
                private function showError($message) {
                    ?>
                    <div class="alert alert-error">
                        <strong>Erreur :</strong> <?= htmlspecialchars($message) ?>
                    </div>
                    <?php
                }
            }
            
            // Gestion des étapes
            if (isset($_GET['step'])) {
                $_SESSION['install_step'] = (int)$_GET['step'];
            }
            
            if (isset($_GET['auto_next'])) {
                $_SESSION['install_step'] = 4;
            }
            
            if (isset($_GET['auto_final'])) {
                $_SESSION['install_step'] = 5;
            }
            
            // Instancier l'installateur
            $installer = new IntraSphereInstaller();
            $currentStep = $installer->getCurrentStep();
            
            // Mettre à jour la barre de progression
            $progress = ($currentStep / 5) * 100;
            ?>
            
            <script>
                document.getElementById('progressFill').style.width = '<?= $progress ?>%';
                document.getElementById('stepIndicator').textContent = 'Étape <?= $currentStep ?> sur 5 - <?= $installer->getStepTitle($currentStep) ?>';
            </script>
            
            <?php
            // Traiter l'étape actuelle
            $installer->processStep();
            ?>
        </div>
    </div>
</body>
</html>