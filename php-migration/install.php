<?php
/**
 * 🚀 IntraSphere PHP - Script d'Installation Automatique
 * 
 * Ce script configure automatiquement IntraSphere PHP selon votre hébergement
 * Placez ce fichier dans public_html/intrasphere/ et accédez-y via navigateur
 * 
 * @version 2.0.0
 * @author IntraSphere Team
 */

// Sécurité : empêcher l'exécution après installation
if (file_exists(__DIR__ . '/.installed')) {
    die('⚠️ IntraSphere est déjà installé. Supprimez le fichier .installed pour réinstaller.');
}

// Configuration de base
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300); // 5 minutes max

class IntraSphereInstaller {
    
    private $config = [];
    private $errors = [];
    private $warnings = [];
    private $success = [];
    
    // Types d'hébergement supportés avec templates
    private $hostingTypes = [
        'cpanel' => [
            'name' => 'cPanel (Standard)',
            'db_host' => 'localhost',
            'db_port' => '3306',
            'db_prefix_format' => '{user}_{db}',
            'user_prefix_format' => '{user}_{db}',
            'features' => ['mysql', 'htaccess', 'sessions']
        ],
        'ovh' => [
            'name' => 'OVH Mutualisé',
            'db_host_format' => '{db}.mysql.db',
            'db_port' => '3306',
            'db_prefix_format' => '{db}',
            'user_prefix_format' => '{db}',
            'features' => ['mysql', 'htaccess', 'sessions']
        ],
        'ionos' => [
            'name' => '1&1 / Ionos',
            'db_host_format' => 'db{id}.hosting-data.io',
            'db_port' => '3306',
            'db_prefix_format' => 'db{id}',
            'user_prefix_format' => 'dbo{id}',
            'features' => ['mysql', 'htaccess', 'sessions']
        ],
        'hostinger' => [
            'name' => 'Hostinger',
            'db_host' => 'localhost',
            'db_port' => '3306',
            'db_prefix_format' => 'u{user_id}_{db}',
            'user_prefix_format' => 'u{user_id}_{db}',
            'features' => ['mysql', 'htaccess', 'sessions']
        ],
        'vps' => [
            'name' => 'VPS / Serveur Dédié',
            'db_host' => 'localhost',
            'db_port' => '3306',
            'db_prefix_format' => '{db}',
            'user_prefix_format' => '{user}',
            'features' => ['mysql', 'postgresql', 'htaccess', 'sessions', 'admin']
        ],
        'local' => [
            'name' => 'Développement Local (XAMPP/WAMP)',
            'db_host' => 'localhost',
            'db_port' => '3306',
            'db_prefix_format' => '{db}',
            'user_prefix_format' => 'root',
            'default_password' => '',
            'features' => ['mysql', 'htaccess', 'sessions', 'dev']
        ]
    ];
    
    public function __construct() {
        $this->detectEnvironment();
    }
    
    /**
     * Point d'entrée principal
     */
    public function run() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleInstallation();
        } else {
            return $this->showInstallationForm();
        }
    }
    
    /**
     * Détection automatique de l'environnement
     */
    private function detectEnvironment() {
        $this->config['auto_detected'] = [];
        
        // Détection du type d'hébergement
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = strtolower($_SERVER['HTTP_HOST']);
            if (strpos($host, 'ovh') !== false) {
                $this->config['auto_detected']['hosting'] = 'ovh';
            } elseif (strpos($host, '1and1') !== false || strpos($host, 'ionos') !== false) {
                $this->config['auto_detected']['hosting'] = 'ionos';
            } elseif (strpos($host, 'hostinger') !== false) {
                $this->config['auto_detected']['hosting'] = 'hostinger';
            } elseif ($host === 'localhost' || strpos($host, '127.0.0.1') !== false) {
                $this->config['auto_detected']['hosting'] = 'local';
            } else {
                $this->config['auto_detected']['hosting'] = 'cpanel';
            }
        }
        
        // Détection PHP
        $this->config['php_version'] = PHP_VERSION;
        $this->config['php_ok'] = version_compare(PHP_VERSION, '7.4', '>=');
        
        // Détection des extensions
        $this->config['extensions'] = [
            'pdo' => extension_loaded('pdo'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'json' => extension_loaded('json'),
            'openssl' => extension_loaded('openssl'),
            'session' => extension_loaded('session'),
            'mbstring' => extension_loaded('mbstring')
        ];
        
        // Détection des permissions
        $this->config['permissions'] = [
            'writable' => is_writable(__DIR__),
            'htaccess' => $this->canCreateHtaccess(),
            'sessions' => $this->canUseSessions()
        ];
    }
    
    /**
     * Affichage du formulaire d'installation
     */
    private function showInstallationForm() {
        $detectedHosting = $this->config['auto_detected']['hosting'] ?? 'cpanel';
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Installation IntraSphere PHP</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh; padding: 20px;
                }
                .container { 
                    max-width: 800px; margin: 0 auto; background: white;
                    border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    overflow: hidden;
                }
                .header { 
                    background: linear-gradient(45deg, #8B5CF6, #A78BFA);
                    color: white; padding: 30px; text-align: center;
                }
                .header h1 { font-size: 2rem; margin-bottom: 10px; }
                .header p { opacity: 0.9; }
                .content { padding: 30px; }
                .section { margin-bottom: 30px; }
                .section h2 { 
                    color: #333; margin-bottom: 15px; padding-bottom: 10px;
                    border-bottom: 2px solid #f0f0f0; font-size: 1.3rem;
                }
                .form-group { margin-bottom: 20px; }
                .form-group label { 
                    display: block; margin-bottom: 8px; font-weight: 600; color: #555;
                }
                .form-group input, .form-group select, .form-group textarea {
                    width: 100%; padding: 12px; border: 2px solid #e0e0e0;
                    border-radius: 6px; font-size: 14px; transition: border-color 0.3s;
                }
                .form-group input:focus, .form-group select:focus {
                    outline: none; border-color: #8B5CF6;
                }
                .btn { 
                    background: linear-gradient(45deg, #8B5CF6, #A78BFA);
                    color: white; padding: 15px 30px; border: none;
                    border-radius: 6px; font-size: 16px; font-weight: 600;
                    cursor: pointer; transition: transform 0.2s;
                }
                .btn:hover { transform: translateY(-2px); }
                .btn-full { width: 100%; }
                .status { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
                .status-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
                .status-warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
                .status-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
                .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
                .detected { 
                    background: #e3f2fd; padding: 15px; border-radius: 6px;
                    border-left: 4px solid #2196f3; margin-bottom: 20px;
                }
                .help-text { font-size: 13px; color: #666; margin-top: 5px; }
                .requirements { list-style: none; }
                .requirements li { 
                    padding: 8px 0; display: flex; align-items: center;
                }
                .requirements .ok::before { content: '✅'; margin-right: 10px; }
                .requirements .error::before { content: '❌'; margin-right: 10px; }
                .requirements .warning::before { content: '⚠️'; margin-right: 10px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🚀 Installation IntraSphere PHP</h1>
                    <p>Configuration automatique de votre plateforme d'apprentissage d'entreprise</p>
                </div>
                
                <div class="content">
                    <!-- Détection automatique -->
                    <div class="detected">
                        <h3>🔍 Détection Automatique</h3>
                        <p><strong>Type d'hébergement détecté :</strong> <?= $this->hostingTypes[$detectedHosting]['name'] ?></p>
                        <p><strong>Version PHP :</strong> <?= $this->config['php_version'] ?> 
                        <?= $this->config['php_ok'] ? '✅' : '❌ (PHP 7.4+ requis)' ?></p>
                    </div>
                    
                    <!-- Vérification des prérequis -->
                    <div class="section">
                        <h2>📋 Vérification des Prérequis</h2>
                        <ul class="requirements">
                            <li class="<?= $this->config['php_ok'] ? 'ok' : 'error' ?>">
                                PHP 7.4+ (Version actuelle: <?= $this->config['php_version'] ?>)
                            </li>
                            <?php foreach ($this->config['extensions'] as $ext => $loaded): ?>
                            <li class="<?= $loaded ? 'ok' : 'error' ?>">
                                Extension <?= strtoupper($ext) ?>
                            </li>
                            <?php endforeach; ?>
                            <li class="<?= $this->config['permissions']['writable'] ? 'ok' : 'error' ?>">
                                Permissions d'écriture sur le dossier
                            </li>
                            <li class="<?= $this->config['permissions']['htaccess'] ? 'ok' : 'warning' ?>">
                                Support .htaccess (optionnel)
                            </li>
                        </ul>
                    </div>
                    
                    <?php if ($this->hasBlockingErrors()): ?>
                        <div class="status status-error">
                            <strong>❌ Erreurs critiques détectées</strong><br>
                            Veuillez corriger les problèmes ci-dessus avant de continuer l'installation.
                            Contactez votre hébergeur si nécessaire.
                        </div>
                    <?php else: ?>
                    
                    <!-- Formulaire de configuration -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="install">
                        
                        <div class="section">
                            <h2>🏢 Configuration de l'Hébergement</h2>
                            
                            <div class="form-group">
                                <label for="hosting_type">Type d'hébergement</label>
                                <select name="hosting_type" id="hosting_type" onchange="updateHostingFields()">
                                    <?php foreach ($this->hostingTypes as $key => $type): ?>
                                    <option value="<?= $key ?>" <?= $key === $detectedHosting ? 'selected' : '' ?>>
                                        <?= $type['name'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-text">Sélectionnez votre type d'hébergement pour une configuration optimisée</div>
                            </div>
                        </div>
                        
                        <div class="section">
                            <h2>🗄️ Configuration Base de Données</h2>
                            
                            <div class="grid">
                                <div class="form-group">
                                    <label for="db_host">Serveur de base de données</label>
                                    <input type="text" name="db_host" id="db_host" 
                                           value="<?= $this->hostingTypes[$detectedHosting]['db_host'] ?? 'localhost' ?>" required>
                                    <div class="help-text">Généralement 'localhost' pour l'hébergement mutualisé</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="db_port">Port</label>
                                    <input type="text" name="db_port" id="db_port" 
                                           value="<?= $this->hostingTypes[$detectedHosting]['db_port'] ?? '3306' ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="db_name">Nom de la base de données</label>
                                <input type="text" name="db_name" id="db_name" required>
                                <div class="help-text">Le nom de votre base de données MySQL</div>
                            </div>
                            
                            <div class="grid">
                                <div class="form-group">
                                    <label for="db_user">Utilisateur</label>
                                    <input type="text" name="db_user" id="db_user" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="db_password">Mot de passe</label>
                                    <input type="password" name="db_password" id="db_password">
                                </div>
                            </div>
                        </div>
                        
                        <div class="section">
                            <h2>👤 Compte Administrateur</h2>
                            
                            <div class="grid">
                                <div class="form-group">
                                    <label for="admin_username">Nom d'utilisateur admin</label>
                                    <input type="text" name="admin_username" id="admin_username" 
                                           value="admin" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="admin_name">Nom complet</label>
                                    <input type="text" name="admin_name" id="admin_name" 
                                           value="Administrateur" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_password">Mot de passe admin</label>
                                <input type="password" name="admin_password" id="admin_password" 
                                       minlength="8" required>
                                <div class="help-text">Au moins 8 caractères, avec majuscule, chiffre et caractère spécial</div>
                            </div>
                        </div>
                        
                        <div class="section">
                            <h2>⚙️ Options Avancées</h2>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="create_sample_data" value="1" checked>
                                    Créer des données d'exemple
                                </label>
                                <div class="help-text">Inclut des annonces et utilisateurs de démonstration</div>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="enable_debug" value="1">
                                    Activer le mode debug
                                </label>
                                <div class="help-text">Recommandé uniquement pour le développement</div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-full">
                            🚀 Installer IntraSphere
                        </button>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
            
            <script>
                function updateHostingFields() {
                    const hostingType = document.getElementById('hosting_type').value;
                    const hostingTypes = <?= json_encode($this->hostingTypes) ?>;
                    const config = hostingTypes[hostingType];
                    
                    if (config.db_host) {
                        document.getElementById('db_host').value = config.db_host;
                    }
                    if (config.db_port) {
                        document.getElementById('db_port').value = config.db_port;
                    }
                }
            </script>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Traitement de l'installation
     */
    private function handleInstallation() {
        try {
            $this->validateInput();
            $this->createEnvFile();
            $this->setupDatabase();
            $this->createHtaccessFile();
            $this->createAdminUser();
            $this->createSampleData();
            $this->finalizeInstallation();
            
            return $this->showSuccessPage();
            
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return $this->showErrorPage();
        }
    }
    
    /**
     * Validation des données d'entrée
     */
    private function validateInput() {
        $required = ['db_host', 'db_name', 'db_user', 'admin_username', 'admin_name', 'admin_password'];
        
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Le champ '{$field}' est requis");
            }
        }
        
        // Validation du mot de passe admin
        $password = $_POST['admin_password'];
        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe admin doit faire au moins 8 caractères");
        }
        
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new Exception("Le mot de passe doit contenir au moins une majuscule et un chiffre");
        }
    }
    
    /**
     * Création du fichier .env
     */
    private function createEnvFile() {
        $env = "# Configuration IntraSphere générée automatiquement\n";
        $env .= "# " . date('Y-m-d H:i:s') . "\n\n";
        
        $env .= "# Base de données\n";
        $env .= "DB_DRIVER=mysql\n";
        $env .= "DB_HOST=" . $_POST['db_host'] . "\n";
        $env .= "DB_PORT=" . ($_POST['db_port'] ?: '3306') . "\n";
        $env .= "DB_NAME=" . $_POST['db_name'] . "\n";
        $env .= "DB_USER=" . $_POST['db_user'] . "\n";
        $env .= "DB_PASSWORD=" . $_POST['db_password'] . "\n\n";
        
        $env .= "# Application\n";
        $env .= "APP_ENV=production\n";
        $env .= "APP_DEBUG=" . (isset($_POST['enable_debug']) ? 'true' : 'false') . "\n";
        $env .= "SESSION_SECRET=" . bin2hex(random_bytes(32)) . "\n";
        
        if (!file_put_contents(__DIR__ . '/.env', $env)) {
            throw new Exception("Impossible de créer le fichier .env");
        }
        
        $this->success[] = "Configuration .env créée";
    }
    
    /**
     * Configuration et initialisation de la base de données
     */
    private function setupDatabase() {
        // Test de connexion
        $dsn = "mysql:host={$_POST['db_host']};port=" . ($_POST['db_port'] ?: '3306') . ";dbname={$_POST['db_name']};charset=utf8mb4";
        
        try {
            $pdo = new PDO($dsn, $_POST['db_user'], $_POST['db_password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            throw new Exception("Connexion base de données échouée: " . $e->getMessage());
        }
        
        $this->success[] = "Connexion base de données réussie";
        
        // Exécution du script SQL
        $sqlFile = __DIR__ . '/sql/create_tables.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("Fichier SQL introuvable: {$sqlFile}");
        }
        
        $sql = file_get_contents($sqlFile);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignorer les erreurs "table exists" 
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        throw new Exception("Erreur SQL: " . $e->getMessage());
                    }
                }
            }
        }
        
        $this->success[] = "Tables de base de données créées";
        $this->pdo = $pdo; // Garder la connexion pour la suite
    }
    
    /**
     * Création du fichier .htaccess
     */
    private function createHtaccessFile() {
        $htaccess = "# IntraSphere PHP - Configuration Apache\n";
        $htaccess .= "RewriteEngine On\n\n";
        
        $htaccess .= "# Sécurité - Masquer les fichiers sensibles\n";
        $htaccess .= "<FilesMatch \"\\.(env|sql|log|md)$\">\n";
        $htaccess .= "    Require all denied\n";
        $htaccess .= "</FilesMatch>\n\n";
        
        $htaccess .= "# Redirection vers index.php\n";
        $htaccess .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $htaccess .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $htaccess .= "RewriteRule ^(.*)$ index.php [QSA,L]\n\n";
        
        $htaccess .= "# Headers de sécurité\n";
        $htaccess .= "Header always set X-Content-Type-Options nosniff\n";
        $htaccess .= "Header always set X-Frame-Options DENY\n";
        $htaccess .= "Header always set X-XSS-Protection \"1; mode=block\"\n";
        
        if (file_put_contents(__DIR__ . '/.htaccess', $htaccess)) {
            $this->success[] = "Fichier .htaccess créé";
        } else {
            $this->warnings[] = "Impossible de créer .htaccess (optionnel)";
        }
    }
    
    /**
     * Création du compte administrateur
     */
    private function createAdminUser() {
        $hashedPassword = password_hash($_POST['admin_password'], PASSWORD_BCRYPT);
        $userId = uniqid('admin-', true);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO users (id, username, password, name, role, is_active, created_at) 
            VALUES (?, ?, ?, ?, 'admin', 1, NOW())
            ON DUPLICATE KEY UPDATE 
                password = VALUES(password),
                name = VALUES(name),
                updated_at = NOW()
        ");
        
        $stmt->execute([
            $userId,
            $_POST['admin_username'],
            $hashedPassword,
            $_POST['admin_name']
        ]);
        
        $this->success[] = "Compte administrateur créé";
    }
    
    /**
     * Création des données d'exemple
     */
    private function createSampleData() {
        if (!isset($_POST['create_sample_data'])) {
            return;
        }
        
        // Données d'exemple
        $sampleDataFile = __DIR__ . '/sql/insert_demo_data.sql';
        if (file_exists($sampleDataFile)) {
            $sql = file_get_contents($sampleDataFile);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    try {
                        $this->pdo->exec($statement);
                    } catch (PDOException $e) {
                        // Ignorer les erreurs de doublons
                        if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                            $this->warnings[] = "Erreur données exemple: " . $e->getMessage();
                        }
                    }
                }
            }
            
            $this->success[] = "Données d'exemple créées";
        }
    }
    
    /**
     * Finalisation de l'installation
     */
    private function finalizeInstallation() {
        // Créer le fichier de verrouillage
        file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
        
        // Supprimer ce script d'installation pour la sécurité
        if ($_POST['delete_installer'] ?? false) {
            @unlink(__FILE__);
        }
        
        $this->success[] = "Installation finalisée";
    }
    
    /**
     * Page de succès
     */
    private function showSuccessPage() {
        $baseUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Installation Réussie - IntraSphere</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh; padding: 20px; display: flex; align-items: center;
                }
                .container { 
                    max-width: 600px; margin: 0 auto; background: white;
                    border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    overflow: hidden; text-align: center;
                }
                .header { 
                    background: linear-gradient(45deg, #10B981, #34D399);
                    color: white; padding: 40px;
                }
                .header h1 { font-size: 2.5rem; margin-bottom: 10px; }
                .content { padding: 40px; }
                .success-icon { font-size: 4rem; margin-bottom: 20px; }
                .btn { 
                    background: linear-gradient(45deg, #8B5CF6, #A78BFA);
                    color: white; padding: 15px 30px; border: none;
                    border-radius: 6px; font-size: 16px; font-weight: 600;
                    text-decoration: none; display: inline-block; margin: 10px;
                    transition: transform 0.2s;
                }
                .btn:hover { transform: translateY(-2px); }
                .credentials { 
                    background: #f8f9fa; padding: 20px; border-radius: 8px;
                    margin: 20px 0; text-align: left;
                }
                .success-list { 
                    list-style: none; text-align: left; margin: 20px 0;
                }
                .success-list li { 
                    padding: 8px 0; color: #10B981; font-weight: 500;
                }
                .success-list li::before { content: '✅ '; margin-right: 10px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="success-icon">🎉</div>
                    <h1>Installation Réussie !</h1>
                    <p>IntraSphere PHP est maintenant opérationnel</p>
                </div>
                
                <div class="content">
                    <h2>Configuration terminée avec succès</h2>
                    
                    <ul class="success-list">
                        <?php foreach ($this->success as $message): ?>
                        <li><?= htmlspecialchars($message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <?php if (!empty($this->warnings)): ?>
                    <h3>⚠️ Avertissements</h3>
                    <ul>
                        <?php foreach ($this->warnings as $warning): ?>
                        <li style="color: #F59E0B;"><?= htmlspecialchars($warning) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    
                    <div class="credentials">
                        <h3>🔑 Informations de Connexion</h3>
                        <p><strong>URL d'accès :</strong> <a href="<?= $baseUrl ?>"><?= $baseUrl ?></a></p>
                        <p><strong>Utilisateur :</strong> <?= htmlspecialchars($_POST['admin_username']) ?></p>
                        <p><strong>Mot de passe :</strong> [Le mot de passe que vous avez saisi]</p>
                    </div>
                    
                    <a href="<?= $baseUrl ?>" class="btn">🚀 Accéder à IntraSphere</a>
                    
                    <div style="margin-top: 30px; font-size: 14px; color: #666;">
                        <p>📁 Installation dans : <?= __DIR__ ?></p>
                        <p>🗄️ Base de données : <?= $_POST['db_name'] ?></p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Page d'erreur
     */
    private function showErrorPage() {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Erreur Installation - IntraSphere</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
                .error { background: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 4px; margin: 10px 0; }
                .btn { background: #8B5CF6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>❌ Erreur d'Installation</h1>
                
                <?php foreach ($this->errors as $error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
                
                <p><a href="?" class="btn">↩️ Retour au formulaire</a></p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Vérification des erreurs bloquantes
     */
    private function hasBlockingErrors() {
        return !$this->config['php_ok'] || 
               !$this->config['extensions']['pdo'] || 
               !$this->config['extensions']['pdo_mysql'] ||
               !$this->config['permissions']['writable'];
    }
    
    /**
     * Test de création .htaccess
     */
    private function canCreateHtaccess() {
        $testFile = __DIR__ . '/.htaccess_test';
        $canCreate = @file_put_contents($testFile, 'test');
        if ($canCreate) {
            @unlink($testFile);
            return true;
        }
        return false;
    }
    
    /**
     * Test des sessions
     */
    private function canUseSessions() {
        return extension_loaded('session') && session_status() !== PHP_SESSION_DISABLED;
    }
}

// Exécution du script
try {
    $installer = new IntraSphereInstaller();
    echo $installer->run();
} catch (Exception $e) {
    echo "<h1>Erreur Critique</h1>";
    echo "<p>Une erreur inattendue s'est produite : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Veuillez vérifier les permissions du dossier et réessayer.</p>";
}
?>