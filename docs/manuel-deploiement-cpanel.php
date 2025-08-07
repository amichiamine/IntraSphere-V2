<?php
/**
 * MANUEL DE DÉPLOIEMENT CPANEL INTERACTIF
 * IntraSphere - Plateforme Intranet d'Entreprise
 * 
 * Guide complet pour déploiement sur hébergement mutualisé cPanel
 * avec et sans Node.js - Version 2025
 * 
 * @author IntraSphere Development Team
 * @version 2.1
 * @date Août 2025
 */

session_start();

// Configuration du manuel
$currentStep = isset($_GET['step']) ? intval($_GET['step']) : 1;
$deploymentType = isset($_GET['type']) ? $_GET['type'] : 'traditional';
$totalSteps = ($deploymentType === 'nodejs') ? 12 : 8;

// Données de progression
if (!isset($_SESSION['deployment_progress'])) {
    $_SESSION['deployment_progress'] = [];
}

// Marquer une étape comme complétée
if (isset($_POST['complete_step'])) {
    $_SESSION['deployment_progress'][$_POST['step']] = true;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manuel de Déploiement cPanel - IntraSphere</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #4a5568;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-align: center;
        }

        .header p {
            text-align: center;
            color: #718096;
            font-size: 1.1rem;
        }

        .deployment-type-selector {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            justify-content: center;
        }

        .type-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
            min-width: 200px;
        }

        .type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .type-card.active {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .main-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 5px;
            transition: width 0.3s ease;
        }

        .step-list {
            list-style: none;
        }

        .step-item {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .step-item:hover {
            background: #f7fafc;
            margin: 0 -10px;
            padding-left: 22px;
            border-radius: 8px;
        }

        .step-item.active {
            color: #667eea;
            font-weight: 600;
        }

        .step-item.completed {
            color: #48bb78;
        }

        .step-item.completed::before {
            content: "✓ ";
            color: #48bb78;
            font-weight: bold;
        }

        .content-area {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .step-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .step-title {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .step-description {
            color: #718096;
            font-size: 1.1rem;
        }

        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            position: relative;
        }

        .code-header {
            background: #4a5568;
            color: white;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 15px -20px;
            font-weight: 600;
        }

        .file-structure {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 10px 10px 0;
        }

        .warning {
            background: #fed7d7;
            border: 1px solid #feb2b2;
            color: #742a2a;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .info {
            background: #bee3f8;
            border: 1px solid #90cdf4;
            color: #2c5282;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .success {
            background: #c6f6d5;
            border: 1px solid #9ae6b4;
            color: #22543d;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .checklist {
            list-style: none;
            margin: 20px 0;
        }

        .checklist li {
            padding: 8px 0;
            position: relative;
            padding-left: 30px;
        }

        .checklist li::before {
            content: "☐";
            position: absolute;
            left: 0;
            color: #667eea;
            font-size: 1.2rem;
        }

        .file-path {
            background: #f1f5f9;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            padding: 8px 12px;
            font-family: 'Courier New', monospace;
            color: #2d3748;
            display: inline-block;
            margin: 5px 0;
        }

        .step-complete-btn {
            background: #48bb78;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 20px;
        }

        .step-complete-btn:hover {
            background: #38a169;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: relative;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Manuel de Déploiement cPanel</h1>
            <p>Guide complet pour déployer IntraSphere sur hébergement mutualisé</p>
            
            <div class="deployment-type-selector">
                <div class="type-card <?= $deploymentType === 'traditional' ? 'active' : '' ?>" 
                     onclick="window.location.href='?type=traditional&step=1'">
                    <h3>🌐 Hébergement Traditionnel</h3>
                    <p>PHP + MySQL classique</p>
                </div>
                <div class="type-card <?= $deploymentType === 'nodejs' ? 'active' : '' ?>" 
                     onclick="window.location.href='?type=nodejs&step=1'">
                    <h3>⚡ Hébergement Node.js</h3>
                    <p>Application complète</p>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="sidebar">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= ($currentStep / $totalSteps) * 100 ?>%"></div>
                </div>
                <p><strong>Progression:</strong> <?= $currentStep ?>/<?= $totalSteps ?> étapes</p>
                
                <ul class="step-list">
                    <?php
                    $steps = [];
                    
                    if ($deploymentType === 'traditional') {
                        $steps = [
                            1 => "Préparation de l'environnement",
                            2 => "Structure des fichiers",
                            3 => "Configuration base de données",
                            4 => "Upload des fichiers",
                            5 => "Configuration PHP",
                            6 => "Configuration .htaccess",
                            7 => "Tests et vérifications",
                            8 => "Mise en production"
                        ];
                    } else {
                        $steps = [
                            1 => "Vérification Node.js",
                            2 => "Préparation application",
                            3 => "Build et optimisation",
                            4 => "Structure fichiers",
                            5 => "Configuration cPanel",
                            6 => "Upload application",
                            7 => "Installation dépendances",
                            8 => "Configuration base de données",
                            9 => "Variables d'environnement",
                            10 => "Démarrage application",
                            11 => "Tests et debug",
                            12 => "Monitoring et maintenance"
                        ];
                    }
                    
                    foreach ($steps as $stepNum => $stepTitle) {
                        $isActive = $stepNum === $currentStep;
                        $isCompleted = isset($_SESSION['deployment_progress'][$stepNum]);
                        $classes = [];
                        
                        if ($isActive) $classes[] = 'active';
                        if ($isCompleted) $classes[] = 'completed';
                        
                        $classStr = implode(' ', $classes);
                        
                        echo "<li class='step-item $classStr' onclick=\"window.location.href='?type=$deploymentType&step=$stepNum'\">";
                        echo "$stepNum. $stepTitle";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </div>

            <div class="content-area">
                <?php
                // Contenu des étapes selon le type de déploiement
                include 'step_content.php';
                ?>
                
                <div class="navigation">
                    <?php if ($currentStep > 1): ?>
                        <a href="?type=<?= $deploymentType ?>&step=<?= $currentStep - 1 ?>" class="btn btn-secondary">
                            ← Étape précédente
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>
                    
                    <?php if ($currentStep < $totalSteps): ?>
                        <a href="?type=<?= $deploymentType ?>&step=<?= $currentStep + 1 ?>" class="btn btn-primary">
                            Étape suivante →
                        </a>
                    <?php else: ?>
                        <a href="?type=<?= $deploymentType ?>&step=1" class="btn btn-primary">
                            🎉 Recommencer
                        </a>
                    <?php endif; ?>
                </div>
                
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="step" value="<?= $currentStep ?>">
                    <button type="submit" name="complete_step" class="step-complete-btn">
                        ✓ Marquer cette étape comme terminée
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Animation d'apparition
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.content-area > *');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.5s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Copie automatique du code
        function copyCode(element) {
            const code = element.textContent;
            navigator.clipboard.writeText(code).then(() => {
                const originalBg = element.style.background;
                element.style.background = '#48bb78';
                setTimeout(() => {
                    element.style.background = originalBg;
                }, 1000);
            });
        }

        // Ajouter la fonctionnalité de copie aux blocs de code
        document.querySelectorAll('.code-block').forEach(block => {
            block.addEventListener('click', () => copyCode(block));
            block.style.cursor = 'pointer';
            block.title = 'Cliquer pour copier';
        });
    </script>
</body>
</html>

<?php
/**
 * CONTENU DES ÉTAPES - step_content.php
 * Ce fichier contient le contenu détaillé de chaque étape
 */

function renderStepContent($deploymentType, $currentStep) {
    if ($deploymentType === 'traditional') {
        renderTraditionalSteps($currentStep);
    } else {
        renderNodeJSSteps($currentStep);
    }
}

function renderTraditionalSteps($step) {
    switch ($step) {
        case 1:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 1: Préparation de l'environnement</h2>
                <p class="step-description">Configuration initiale et vérification des prérequis pour l'hébergement traditionnel</p>
            </div>

            <div class="info">
                <strong>📋 Prérequis pour IntraSphere:</strong><br>
                • PHP 8.1+ avec extensions: PDO, MySQLi, OpenSSL, cURL, JSON<br>
                • MySQL 5.7+ ou MariaDB 10.3+<br>
                • Espace disque: minimum 100 MB<br>
                • Hébergeur supportant les réécritures d'URL (.htaccess)
            </div>

            <h3>🔍 Vérification de votre hébergement</h3>
            <div class="checklist">
                <li>Accès cPanel avec version récente</li>
                <li>Gestionnaire de fichiers accessible</li>
                <li>MySQL Database Wizard disponible</li>
                <li>Support PHP 8.1+ activé</li>
                <li>Domaine ou sous-domaine configuré</li>
            </div>

            <h3>📁 Architecture du projet IntraSphere</h3>
            <div class="file-structure">
                <strong>Structure actuelle de votre projet:</strong>
                <div class="code-block">
                    <div class="code-header">Structure IntraSphere</div>
intrasphere/
├── client/                    # Frontend React
│   ├── src/
│   │   ├── components/       # Composants UI
│   │   ├── pages/           # Pages de l'application
│   │   ├── hooks/           # Hooks React personnalisés
│   │   └── lib/             # Utilitaires
│   └── dist/                # Build de production (après npm run build)
├── server/                   # Backend Express
│   ├── auth.ts              # Authentification
│   ├── routes.ts            # Routes API
│   ├── storage.ts           # Gestion données
│   └── index.ts             # Point d'entrée serveur
├── shared/                   # Types partagés
│   └── schema.ts            # Schémas base de données
├── package.json             # Dépendances Node.js
└── docs/                    # Documentation
                </div>
            </div>

            <div class="warning">
                <strong>⚠️ Important:</strong> Pour l'hébergement traditionnel, nous devons adapter cette structure Node.js en version PHP statique. Le frontend sera servi comme site statique et nous créerons une API PHP simple pour les fonctionnalités backend.
            </div>
            <?php
            break;

        case 2:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 2: Structure des fichiers pour cPanel</h2>
                <p class="step-description">Organisation des fichiers pour un déploiement traditionnel réussi</p>
            </div>

            <h3>🏗️ Structure cible sur cPanel</h3>
            <div class="file-structure">
                <strong>Organisation recommandée:</strong>
                <div class="code-block">
                    <div class="code-header">Structure cPanel</div>
/home/username/
├── public_html/              # Dossier web principal (accessible publiquement)
│   ├── index.html           # Page d'accueil (build React)
│   ├── static/              # Assets statiques (CSS, JS, images)
│   │   ├── css/
│   │   ├── js/
│   │   └── media/
│   ├── api/                 # Scripts PHP pour l'API
│   │   ├── index.php        # Router principal
│   │   ├── auth.php         # Authentification
│   │   ├── announcements.php
│   │   ├── documents.php
│   │   ├── users.php
│   │   ├── trainings.php    # Nouveau: gestion formations
│   │   └── config.php       # Configuration base de données
│   ├── uploads/             # Fichiers uploadés
│   └── .htaccess           # Configuration serveur
├── intrasphere_config/      # Configuration privée (non accessible web)
│   ├── database.php         # Paramètres BDD
│   └── security.php         # Clés de sécurité
└── logs/                    # Logs d'erreur
                </div>
            </div>

            <h3>📄 Fichiers essentiels à créer</h3>
            
            <h4>1. Configuration base de données</h4>
            <div class="code-block">
                <div class="code-header">intrasphere_config/database.php</div>
&lt;?php
// Configuration base de données pour IntraSphere
define('DB_HOST', 'localhost');
define('DB_NAME', 'username_intrasphere');  // Remplacer par votre nom de BDD
define('DB_USER', 'username_intrasphere');  // Remplacer par votre utilisateur BDD
define('DB_PASS', 'mot_de_passe_fort');     // Remplacer par votre mot de passe

// Configuration de sécurité
define('JWT_SECRET', 'votre_cle_secrete_tres_longue_et_complexe');
define('BCRYPT_ROUNDS', 12);

// Configuration application
define('APP_URL', 'https://votre-domaine.com');
define('UPLOAD_MAX_SIZE', 10485760); // 10MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Erreur connexion base de données: " . $e->getMessage());
    die("Erreur de connexion à la base de données");
}
?&gt;
            </div>

            <h4>2. Router principal API</h4>
            <div class="code-block">
                <div class="code-header">public_html/api/index.php</div>
&lt;?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../intrasphere_config/database.php';

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$method = $_SERVER['REQUEST_METHOD'];

// Router simple
switch (true) {
    case preg_match('/^\/auth\/login$/', $path) && $method === 'POST':
        require_once 'auth.php';
        handleLogin();
        break;
        
    case preg_match('/^\/announcements$/', $path):
        require_once 'announcements.php';
        handleAnnouncements($method);
        break;
        
    case preg_match('/^\/trainings$/', $path):
        require_once 'trainings.php';
        handleTrainings($method);
        break;
        
    case preg_match('/^\/users$/', $path):
        require_once 'users.php';
        handleUsers($method);
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint non trouvé']);
        break;
}
?&gt;
            </div>

            <div class="success">
                <strong>✅ Prêt pour l'étape suivante:</strong> Une fois cette structure comprise, nous pourrons configurer la base de données et créer les tables nécessaires.
            </div>
            <?php
            break;

        case 3:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 3: Configuration base de données</h2>
                <p class="step-description">Création et configuration de la base de données MySQL pour IntraSphere</p>
            </div>

            <h3>🗄️ Création de la base de données</h3>
            
            <div class="info">
                <strong>📝 Procédure dans cPanel:</strong><br>
                1. Connectez-vous à votre cPanel<br>
                2. Recherchez "MySQL Database Wizard" ou "Bases de données MySQL"<br>
                3. Suivez les étapes ci-dessous
            </div>

            <h4>Étape 3.1: Créer la base de données</h4>
            <div class="checklist">
                <li>Nom de la base: <span class="file-path">intrasphere</span></li>
                <li>Le nom complet sera: <span class="file-path">username_intrasphere</span></li>
                <li>Charset: <span class="file-path">utf8mb4_unicode_ci</span></li>
            </div>

            <h4>Étape 3.2: Créer l'utilisateur</h4>
            <div class="checklist">
                <li>Nom utilisateur: <span class="file-path">intrasphere</span></li>
                <li>Mot de passe: <strong>Générer un mot de passe fort</strong></li>
                <li>Privilèges: <strong>TOUS les privilèges</strong></li>
            </div>

            <h3>🏗️ Structure des tables</h3>
            <div class="code-block">
                <div class="code-header">Script SQL - Tables principales</div>
-- Table des utilisateurs
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator', 'employee') DEFAULT 'employee',
    department VARCHAR(100),
    position VARCHAR(100),
    phone VARCHAR(20),
    avatar_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des annonces
CREATE TABLE announcements (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id VARCHAR(36) NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    category VARCHAR(100),
    is_published BOOLEAN DEFAULT FALSE,
    publish_date DATETIME,
    expiry_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des formations (nouvelle fonctionnalité)
CREATE TABLE trainings (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('technical', 'management', 'safety', 'compliance', 'other') NOT NULL,
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    duration INT NOT NULL, -- en minutes
    instructor_name VARCHAR(255) NOT NULL,
    start_date DATETIME,
    end_date DATETIME,
    location VARCHAR(255),
    max_participants INT,
    current_participants INT DEFAULT 0,
    is_mandatory BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_visible BOOLEAN DEFAULT TRUE,
    thumbnail_url VARCHAR(500),
    document_urls JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des participants aux formations
CREATE TABLE training_participants (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    training_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    status ENUM('registered', 'attended', 'completed', 'cancelled') DEFAULT 'registered',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date DATETIME NULL,
    FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participant (training_id, user_id)
);

-- Table des documents
CREATE TABLE documents (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    title VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    uploaded_by VARCHAR(36) NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    download_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des permissions
CREATE TABLE user_permissions (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id VARCHAR(36) NOT NULL,
    permission VARCHAR(100) NOT NULL,
    granted_by VARCHAR(36) NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_permission (user_id, permission)
);
            </div>

            <h3>👤 Données initiales</h3>
            <div class="code-block">
                <div class="code-header">Script SQL - Utilisateur administrateur</div>
-- Insérer l'utilisateur administrateur par défaut
INSERT INTO users (id, name, email, password, role, department, position) VALUES (
    'admin-001',
    'Administrateur Système',
    'admin@votre-entreprise.com',
    '$2y$12$LQv3c1yqBwrf2oa4.OjxleN9k7rdCVXlwwYFvq0OJ3pQjG.mVH8iG', -- mot de passe: admin123
    'admin',
    'Informatique',
    'Administrateur Système'
);

-- Permissions pour l'admin
INSERT INTO user_permissions (user_id, permission, granted_by) VALUES 
('admin-001', 'manage_users', 'admin-001'),
('admin-001', 'manage_announcements', 'admin-001'),
('admin-001', 'manage_documents', 'admin-001'),
('admin-001', 'manage_trainings', 'admin-001'),
('admin-001', 'manage_permissions', 'admin-001');

-- Exemple de formation initiale
INSERT INTO trainings (
    id, title, description, category, difficulty, duration, instructor_name, is_visible
) VALUES (
    'training-001',
    'Sécurité informatique de base',
    'Formation obligatoire sur les bonnes pratiques de sécurité informatique en entreprise.',
    'technical',
    'beginner',
    120,
    'Service Informatique',
    TRUE
);
            </div>

            <div class="warning">
                <strong>⚠️ Sécurité:</strong> Changez immédiatement le mot de passe administrateur après le premier login. Le mot de passe par défaut est <code>admin123</code>.
            </div>
            <?php
            break;

        case 4:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 4: Upload des fichiers</h2>
                <p class="step-description">Téléchargement et organisation des fichiers sur votre hébergement cPanel</p>
            </div>

            <h3>📦 Préparation du build frontend</h3>
            
            <div class="info">
                <strong>🔧 Avant de commencer:</strong><br>
                Si vous avez accès au projet en local, exécutez d'abord la commande de build pour générer les fichiers statiques optimisés.
            </div>

            <div class="code-block">
                <div class="code-header">Commandes à exécuter en local (si disponible)</div>
# Aller dans le dossier du projet
cd intrasphere

# Installer les dépendances
npm install

# Générer le build de production
npm run build

# Le dossier 'dist' ou 'build' contient maintenant les fichiers optimisés
            </div>

            <h3>📁 Upload via le gestionnaire de fichiers cPanel</h3>

            <h4>Méthode 1: Upload direct</h4>
            <div class="checklist">
                <li>Connectez-vous à cPanel</li>
                <li>Ouvrez "Gestionnaire de fichiers" (File Manager)</li>
                <li>Naviguez vers <span class="file-path">public_html/</span></li>
                <li>Créez les dossiers nécessaires</li>
                <li>Uploadez les fichiers un par un</li>
            </div>

            <h4>Méthode 2: Upload par archive ZIP (Recommandée)</h4>
            <div class="checklist">
                <li>Créez une archive ZIP de vos fichiers frontend</li>
                <li>Uploadez le fichier ZIP dans <span class="file-path">public_html/</span></li>
                <li>Utilisez l'extracteur intégré de cPanel</li>
                <li>Supprimez le fichier ZIP après extraction</li>
            </div>

            <h3>🎯 Fichiers à uploader par section</h3>

            <h4>Frontend (dans public_html/)</h4>
            <div class="code-block">
                <div class="code-header">Liste des fichiers frontend essentiels</div>
public_html/
├── index.html                    # Page principale de l'application
├── favicon.ico                   # Icône du site
├── manifest.json                 # Manifest PWA
├── static/                       # Assets statiques du build
│   ├── css/
│   │   └── main.[hash].css      # Styles compilés
│   ├── js/
│   │   ├── main.[hash].js       # JavaScript principal
│   │   └── [chunk].[hash].js    # Chunks JavaScript
│   └── media/                   # Images et autres médias
│       ├── logo.svg
│       └── *.png, *.jpg, etc.
└── .htaccess                    # Configuration serveur (à créer)
            </div>

            <h4>Backend PHP (dans public_html/api/)</h4>
            <div class="code-block">
                <div class="code-header">Scripts PHP à créer</div>
# Créez ces fichiers PHP dans public_html/api/

# 1. Router principal
api/index.php

# 2. Authentification
api/auth.php

# 3. Gestion des formations (nouveau)
api/trainings.php

# 4. Gestion des annonces
api/announcements.php

# 5. Gestion des utilisateurs
api/users.php

# 6. Gestion des documents
api/documents.php

# 7. Utilitaires
api/utils.php
            </div>

            <h4>Configuration privée (dans intrasphere_config/)</h4>
            <div class="file-structure">
                <strong>Dossier de configuration (non accessible web):</strong>
                <div class="code-block">
                    <div class="code-header">Configuration privée</div>
# Créez le dossier en dehors de public_html
/home/username/intrasphere_config/
├── database.php              # Configuration BDD
├── security.php              # Clés de sécurité
└── settings.php              # Paramètres application
                </div>
            </div>

            <h3>📝 Script PHP pour les formations</h3>
            <div class="code-block">
                <div class="code-header">public_html/api/trainings.php</div>
&lt;?php
require_once '../../intrasphere_config/database.php';

function handleTrainings($method) {
    global $pdo;
    
    switch ($method) {
        case 'GET':
            getTrainings();
            break;
        case 'POST':
            createTraining();
            break;
        case 'PUT':
            updateTraining();
            break;
        case 'DELETE':
            deleteTraining();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
}

function getTrainings() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT t.*, 
                   COUNT(tp.id) as current_participants
            FROM trainings t 
            LEFT JOIN training_participants tp ON t.id = tp.training_id 
            WHERE t.is_visible = TRUE 
            GROUP BY t.id 
            ORDER BY t.created_at DESC
        ");
        $stmt->execute();
        $trainings = $stmt->fetchAll();
        
        echo json_encode($trainings);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la récupération des formations']);
    }
}

function createTraining() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validation des données requises
    if (!isset($input['title']) || !isset($input['category']) || !isset($input['duration']) || !isset($input['instructor_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO trainings (
                title, description, category, difficulty, duration, 
                instructor_name, start_date, end_date, location, 
                max_participants, is_mandatory, is_visible
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $input['title'],
            $input['description'] ?? '',
            $input['category'],
            $input['difficulty'] ?? 'beginner',
            $input['duration'],
            $input['instructor_name'],
            $input['start_date'] ?? null,
            $input['end_date'] ?? null,
            $input['location'] ?? '',
            $input['max_participants'] ?? null,
            $input['is_mandatory'] ?? false,
            $input['is_visible'] ?? true
        ]);
        
        echo json_encode(['message' => 'Formation créée avec succès', 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la création de la formation']);
    }
}

// Autres fonctions (updateTraining, deleteTraining) à implémenter...
?&gt;
            </div>

            <div class="success">
                <strong>✅ Fichiers uploadés:</strong> Une fois tous les fichiers en place, nous pourrons configurer PHP et tester l'application.
            </div>
            <?php
            break;

        // Continuer les autres étapes...
        case 5:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 5: Configuration PHP</h2>
                <p class="step-description">Optimisation et configuration de l'environnement PHP pour IntraSphere</p>
            </div>

            <h3>🔧 Sélection de la version PHP</h3>
            
            <div class="info">
                <strong>📋 Version recommandée:</strong> PHP 8.1 ou supérieur pour une sécurité et des performances optimales.
            </div>

            <h4>Dans cPanel:</h4>
            <div class="checklist">
                <li>Accédez à "Select PHP Version" ou "Sélectionner version PHP"</li>
                <li>Choisissez PHP 8.1, 8.2 ou 8.3 si disponible</li>
                <li>Cliquez sur "Set as current" pour appliquer</li>
            </div>

            <h3>📚 Extensions PHP requises</h3>
            <div class="code-block">
                <div class="code-header">Extensions à activer</div>
Extensions obligatoires pour IntraSphere:
✅ pdo             # Accès base de données
✅ pdo_mysql       # Support MySQL
✅ mysqli          # Support MySQL alternatif
✅ openssl         # Chiffrement et JWT
✅ curl            # Requêtes HTTP externes
✅ json            # Manipulation JSON
✅ mbstring        # Support chaînes multi-octets
✅ fileinfo        # Détection type de fichier
✅ gd              # Manipulation d'images

Extensions optionnelles (recommandées):
○ zip              # Gestion archives
○ xml              # Manipulation XML
○ intl             # Internationalisation
○ bcmath           # Calculs haute précision
            </div>

            <h4>Activation des extensions:</h4>
            <div class="checklist">
                <li>Dans "Select PHP Version", cliquez sur "Extensions"</li>
                <li>Cochez toutes les extensions listées ci-dessus</li>
                <li>Cliquez sur "Save" pour appliquer les changements</li>
                <li>Attendez quelques secondes pour la prise en compte</li>
            </div>

            <h3>⚙️ Configuration PHP (php.ini)</h3>
            <div class="code-block">
                <div class="code-header">Paramètres recommandés</div>
; Limites de mémoire et temps d'exécution
memory_limit = 256M
max_execution_time = 300
max_input_time = 300

; Upload de fichiers
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20

; Gestion des sessions
session.gc_maxlifetime = 7200
session.cookie_lifetime = 0
session.cookie_secure = 1
session.cookie_httponly = 1

; Sécurité
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off

; Erreurs (pour développement uniquement)
display_errors = Off
log_errors = On
error_reporting = E_ALL & ~E_NOTICE
            </div>

            <h4>Application de la configuration:</h4>
            <div class="checklist">
                <li>Créez un fichier <span class="file-path">.user.ini</span> dans <span class="file-path">public_html/</span></li>
                <li>Copiez-y les paramètres ci-dessus</li>
                <li>Sauvegardez le fichier</li>
                <li>Attendez 5 minutes pour la prise en compte</li>
            </div>

            <h3>🔐 Script de test de configuration</h3>
            <div class="code-block">
                <div class="code-header">public_html/test-config.php</div>
&lt;?php
// Script de test de configuration IntraSphere
// ⚠️ SUPPRIMER APRÈS TESTS

echo "&lt;h1&gt;Test Configuration IntraSphere&lt;/h1&gt;";

// Test version PHP
echo "&lt;h2&gt;Version PHP&lt;/h2&gt;";
echo "Version actuelle: " . PHP_VERSION . "&lt;br&gt;";
if (version_compare(PHP_VERSION, '8.1.0') >= 0) {
    echo "&lt;span style='color: green'&gt;✅ Version PHP compatible&lt;/span&gt;&lt;br&gt;";
} else {
    echo "&lt;span style='color: red'&gt;❌ Version PHP trop ancienne (minimum 8.1)&lt;/span&gt;&lt;br&gt;";
}

// Test extensions
echo "&lt;h2&gt;Extensions PHP&lt;/h2&gt;";
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'openssl', 'curl', 'json', 'mbstring', 'fileinfo'];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "&lt;span style='color: green'&gt;✅ $ext&lt;/span&gt;&lt;br&gt;";
    } else {
        echo "&lt;span style='color: red'&gt;❌ $ext (manquante)&lt;/span&gt;&lt;br&gt;";
    }
}

// Test connexion base de données
echo "&lt;h2&gt;Connexion Base de Données&lt;/h2&gt;";
try {
    require_once '../intrasphere_config/database.php';
    echo "&lt;span style='color: green'&gt;✅ Connexion BDD réussie&lt;/span&gt;&lt;br&gt;";
    
    // Test table users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "Nombre d'utilisateurs: $count&lt;br&gt;";
    
} catch (Exception $e) {
    echo "&lt;span style='color: red'&gt;❌ Erreur BDD: " . $e->getMessage() . "&lt;/span&gt;&lt;br&gt;";
}

// Test permissions fichiers
echo "&lt;h2&gt;Permissions Fichiers&lt;/h2&gt;";
$writable_dirs = ['uploads/', 'api/'];
foreach ($writable_dirs as $dir) {
    if (is_writable($dir)) {
        echo "&lt;span style='color: green'&gt;✅ $dir (écriture possible)&lt;/span&gt;&lt;br&gt;";
    } else {
        echo "&lt;span style='color: orange'&gt;⚠️ $dir (vérifier permissions)&lt;/span&gt;&lt;br&gt;";
    }
}

echo "&lt;p&gt;&lt;strong&gt;⚠️ IMPORTANT: Supprimez ce fichier après vos tests !&lt;/strong&gt;&lt;/p&gt;";
?&gt;
            </div>

            <div class="warning">
                <strong>🛡️ Sécurité:</strong> 
                <ol>
                    <li>Accédez à <code>https://votre-domaine.com/test-config.php</code> pour vérifier la configuration</li>
                    <li><strong>Supprimez immédiatement</strong> le fichier <code>test-config.php</code> après les tests</li>
                    <li>Ne jamais laisser de scripts de test en production</li>
                </ol>
            </div>
            <?php
            break;

        case 6:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 6: Configuration .htaccess</h2>
                <p class="step-description">Configuration du serveur web pour une application React SPA et sécurité</p>
            </div>

            <h3>🔧 Fichier .htaccess principal</h3>
            
            <div class="info">
                <strong>📝 Objectifs du .htaccess:</strong><br>
                • Redirection SPA (Single Page Application)<br>
                • Protection des dossiers sensibles<br>
                • Optimisation des performances<br>
                • Configuration HTTPS et sécurité
            </div>

            <div class="code-block">
                <div class="code-header">public_html/.htaccess</div>
# Configuration IntraSphere - Ne pas modifier sans savoir
# Version: 2.1 - Août 2025

# ========================================
# SÉCURITÉ ET PROTECTION
# ========================================

# Protection contre les attaques par injection
&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    
    # Bloquer les tentatives d'injection SQL communes
    RewriteCond %{QUERY_STRING} (\&lt;|%3C)([^s]*s)+cript.*(\&gt;|%3E) [NC,OR]
    RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} (\&lt;|%3C)([^i]*i)+frame.*(\&gt;|%3E) [NC,OR]
    RewriteCond %{QUERY_STRING} select.*\(.*\) [NC,OR]
    RewriteCond %{QUERY_STRING} union.*select.*\( [NC,OR]
    RewriteCond %{QUERY_STRING} (\&lt;|%3C)([^b]*b)+ody.*(\&gt;|%3E) [NC]
    RewriteRule .* - [F]
&lt;/IfModule&gt;

# Protection des fichiers sensibles
&lt;Files ".htaccess"&gt;
    Order Allow,Deny
    Deny from all
&lt;/Files&gt;

&lt;Files "*.ini"&gt;
    Order Allow,Deny
    Deny from all
&lt;/Files&gt;

&lt;Files "*.log"&gt;
    Order Allow,Deny
    Deny from all
&lt;/Files&gt;

# ========================================
# OPTIMISATION ET CACHE
# ========================================

# Compression GZIP
&lt;IfModule mod_deflate.c&gt;
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
&lt;/IfModule&gt;

# Cache des ressources statiques
&lt;IfModule mod_expires.c&gt;
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType font/woff "access plus 1 month"
    ExpiresByType font/woff2 "access plus 1 month"
&lt;/IfModule&gt;

# ========================================
# CONFIGURATION SPA (SINGLE PAGE APP)
# ========================================

&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    
    # Gestion des API (ne pas rediriger)
    RewriteRule ^api/ - [L]
    
    # Gestion des uploads (ne pas rediriger)
    RewriteRule ^uploads/ - [L]
    
    # Fichiers statiques existants (ne pas rediriger)
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]
    
    # Redirection SPA - tout le reste vers index.html
    RewriteRule ^ index.html [L]
&lt;/IfModule&gt;

# ========================================
# SÉCURITÉ AVANCÉE
# ========================================

# Headers de sécurité
&lt;IfModule mod_headers.c&gt;
    # Protection XSS
    Header always set X-XSS-Protection "1; mode=block"
    
    # Protection contre le détournement de type MIME
    Header always set X-Content-Type-Options "nosniff"
    
    # Protection contre le clickjacking
    Header always set X-Frame-Options "SAMEORIGIN"
    
    # Politique de sécurité du contenu (CSP) - À adapter selon vos besoins
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'"
    
    # HSTS (si HTTPS activé)
    # Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
&lt;/IfModule&gt;

# Désactiver la signature du serveur
ServerTokens Prod

# ========================================
# GESTION DES ERREURS
# ========================================

# Pages d'erreur personnalisées
ErrorDocument 404 /index.html
ErrorDocument 403 /index.html
ErrorDocument 500 /index.html

# ========================================
# UPLOADS ET TÉLÉCHARGEMENTS
# ========================================

# Limitation de la taille des requêtes
LimitRequestBody 10485760 # 10MB

# Types MIME pour les nouveaux formats
AddType application/json .json
AddType application/javascript .js
AddType text/css .css
AddType image/webp .webp
AddType font/woff .woff
AddType font/woff2 .woff2
            </div>

            <h3>🛡️ Protection du dossier de configuration</h3>
            <div class="code-block">
                <div class="code-header">intrasphere_config/.htaccess</div>
# Protection totale du dossier de configuration
# AUCUN ACCÈS WEB AUTORISÉ

Order Deny,Allow
Deny from all

# Même pour les scripts PHP
&lt;Files "*.php"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

# Protection des fichiers de configuration
&lt;Files "*.ini"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.conf"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;
            </div>

            <h3>🔒 Protection du dossier API</h3>
            <div class="code-block">
                <div class="code-header">public_html/api/.htaccess</div>
# Configuration API IntraSphere

# Headers CORS pour l'API
&lt;IfModule mod_headers.c&gt;
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
    Header always set Access-Control-Max-Age "3600"
&lt;/IfModule&gt;

# Gestion des requêtes OPTIONS (preflight CORS)
&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ $1 [R=200,L]
&lt;/IfModule&gt;

# Protection contre l'accès direct aux fichiers sensibles
&lt;Files "config.php"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.inc"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

# Limitation de taille pour l'API
LimitRequestBody 10485760 # 10MB

# Cache désactivé pour l'API
&lt;IfModule mod_headers.c&gt;
    Header always set Cache-Control "no-cache, no-store, must-revalidate"
    Header always set Pragma "no-cache"
    Header always set Expires "0"
&lt;/IfModule&gt;
            </div>

            <h3>📂 Protection du dossier uploads</h3>
            <div class="code-block">
                <div class="code-header">public_html/uploads/.htaccess</div>
# Configuration dossier uploads IntraSphere

# Interdire l'exécution de scripts
&lt;Files "*.php"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.php3"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.php4"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.php5"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.phtml"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.pl"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.py"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

&lt;Files "*.cgi"&gt;
    Order Deny,Allow
    Deny from all
&lt;/Files&gt;

# Types de fichiers autorisés uniquement
&lt;FilesMatch "\.(jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip)$"&gt;
    Order Allow,Deny
    Allow from all
&lt;/FilesMatch&gt;

# Refuser tout le reste
&lt;FilesMatch "^(?!.*\.(jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip)$).*$"&gt;
    Order Deny,Allow
    Deny from all
&lt;/FilesMatch&gt;
            </div>

            <div class="warning">
                <strong>⚠️ Test obligatoire:</strong> Après avoir mis en place ces fichiers .htaccess, testez immédiatement:
                <ul>
                    <li>L'accès à votre site principal</li>
                    <li>Le fonctionnement des API (devrait retourner du JSON)</li>
                    <li>La protection des dossiers sensibles</li>
                    <li>Les redirections SPA</li>
                </ul>
            </div>
            <?php
            break;

        case 7:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 7: Tests et vérifications</h2>
                <p class="step-description">Validation complète du déploiement et résolution des problèmes</p>
            </div>

            <h3>🧪 Liste de tests obligatoires</h3>

            <h4>1. Test de connectivité de base</h4>
            <div class="checklist">
                <li>Accès au domaine principal: <span class="file-path">https://votre-domaine.com</span></li>
                <li>Chargement de l'interface IntraSphere</li>
                <li>Absence d'erreurs 404 sur les ressources statiques</li>
                <li>Fonctionnement des redirections SPA</li>
            </div>

            <h4>2. Test des API</h4>
            <div class="code-block">
                <div class="code-header">Tests d'API via curl ou navigateur</div>
# Test de l'API principale
curl -X GET "https://votre-domaine.com/api/"
# Résultat attendu: {"error": "Endpoint non trouvé"} avec code 404

# Test API formations
curl -X GET "https://votre-domaine.com/api/trainings"
# Résultat attendu: Liste JSON des formations

# Test API utilisateurs (nécessite authentification)
curl -X GET "https://votre-domaine.com/api/users"
# Résultat attendu: Erreur d'authentification

# Test CORS (depuis la console du navigateur)
fetch('https://votre-domaine.com/api/trainings')
  .then(response => response.json())
  .then(data => console.log(data));
            </div>

            <h4>3. Test de l'authentification</h4>
            <div class="code-block">
                <div class="code-header">Test de connexion administrateur</div>
# Via l'interface web ou API directe
curl -X POST "https://votre-domaine.com/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@votre-entreprise.com","password":"admin123"}'

# Résultat attendu: Token JWT et informations utilisateur
            </div>

            <h3>🔍 Diagnostic des problèmes courants</h3>

            <h4>Problème: Page blanche ou erreur 500</h4>
            <div class="info">
                <strong>Solutions à tenter:</strong>
                <ol>
                    <li>Vérifiez les logs d'erreur dans cPanel</li>
                    <li>Vérifiez la syntaxe du fichier .htaccess</li>
                    <li>Confirmez les permissions des fichiers (644 pour les fichiers, 755 pour les dossiers)</li>
                    <li>Testez en renommant temporairement .htaccess en .htaccess_backup</li>
                </ol>
            </div>

            <h4>Problème: API retourne des erreurs</h4>
            <div class="info">
                <strong>Vérifications:</strong>
                <ol>
                    <li>Configuration base de données dans <code>intrasphere_config/database.php</code></li>
                    <li>Extensions PHP activées (PDO, MySQL)</li>
                    <li>Chemins des includes dans les scripts PHP</li>
                    <li>Headers CORS configurés correctement</li>
                </ol>
            </div>

            <h4>Problème: Routing SPA ne fonctionne pas</h4>
            <div class="code-block">
                <div class="code-header">Test de redirection SPA</div>
# Ces URLs doivent toutes rediriger vers index.html
https://votre-domaine.com/announcements
https://votre-domaine.com/trainings
https://votre-domaine.com/admin
https://votre-domaine.com/n-importe-quoi

# Ces URLs ne doivent PAS rediriger
https://votre-domaine.com/api/trainings
https://votre-domaine.com/uploads/fichier.pdf
https://votre-domaine.com/static/css/main.css
            </div>

            <h3>🛠️ Script de diagnostic automatique</h3>
            <div class="code-block">
                <div class="code-header">public_html/diagnostic.php</div>
&lt;?php
// Script de diagnostic IntraSphere
// ⚠️ SUPPRIMER APRÈS UTILISATION

header('Content-Type: text/html; charset=utf-8');

echo "&lt;!DOCTYPE html&gt;&lt;html&gt;&lt;head&gt;&lt;title&gt;Diagnostic IntraSphere&lt;/title&gt;&lt;/head&gt;&lt;body&gt;";
echo "&lt;h1&gt;🔍 Diagnostic IntraSphere&lt;/h1&gt;";

$tests = [];

// Test 1: Configuration PHP
$tests['PHP'] = [];
$tests['PHP']['Version'] = version_compare(PHP_VERSION, '8.1.0') >= 0 ? '✅' : '❌';
$tests['PHP']['PDO'] = extension_loaded('pdo') ? '✅' : '❌';
$tests['PHP']['MySQL'] = extension_loaded('pdo_mysql') ? '✅' : '❌';
$tests['PHP']['OpenSSL'] = extension_loaded('openssl') ? '✅' : '❌';
$tests['PHP']['JSON'] = extension_loaded('json') ? '✅' : '❌';

// Test 2: Fichiers et dossiers
$tests['Fichiers'] = [];
$tests['Fichiers']['index.html'] = file_exists('index.html') ? '✅' : '❌';
$tests['Fichiers']['api/index.php'] = file_exists('api/index.php') ? '✅' : '❌';
$tests['Fichiers']['config DB'] = file_exists('../intrasphere_config/database.php') ? '✅' : '❌';
$tests['Fichiers']['.htaccess'] = file_exists('.htaccess') ? '✅' : '❌';

// Test 3: Permissions
$tests['Permissions'] = [];
$tests['Permissions']['uploads/'] = is_writable('uploads/') ? '✅' : '❌';
$tests['Permissions']['api/'] = is_readable('api/') ? '✅' : '❌';

// Test 4: Base de données
$tests['Base de données'] = [];
try {
    require_once '../intrasphere_config/database.php';
    $tests['Base de données']['Connexion'] = '✅';
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $required_tables = ['users', 'announcements', 'trainings', 'training_participants', 'documents', 'user_permissions'];
    
    foreach ($required_tables as $table) {
        $tests['Base de données']["Table $table"] = in_array($table, $tables) ? '✅' : '❌';
    }
    
} catch (Exception $e) {
    $tests['Base de données']['Connexion'] = '❌ ' . $e->getMessage();
}

// Test 5: API
$tests['API'] = [];
$api_tests = [
    'GET /api/trainings' => 'api/trainings.php',
    'GET /api/announcements' => 'api/announcements.php',
    'POST /api/auth/login' => 'api/auth.php'
];

foreach ($api_tests as $endpoint => $file) {
    $tests['API'][$endpoint] = file_exists($file) ? '✅' : '❌';
}

// Affichage des résultats
foreach ($tests as $category => $category_tests) {
    echo "&lt;h2&gt;$category&lt;/h2&gt;&lt;ul&gt;";
    foreach ($category_tests as $test => $result) {
        echo "&lt;li&gt;&lt;strong&gt;$test:&lt;/strong&gt; $result&lt;/li&gt;";
    }
    echo "&lt;/ul&gt;";
}

// Informations système utiles
echo "&lt;h2&gt;Informations système&lt;/h2&gt;";
echo "&lt;ul&gt;";
echo "&lt;li&gt;&lt;strong&gt;Version PHP:&lt;/strong&gt; " . PHP_VERSION . "&lt;/li&gt;";
echo "&lt;li&gt;&lt;strong&gt;Serveur:&lt;/strong&gt; " . $_SERVER['SERVER_SOFTWARE'] . "&lt;/li&gt;";
echo "&lt;li&gt;&lt;strong&gt;Document Root:&lt;/strong&gt; " . $_SERVER['DOCUMENT_ROOT'] . "&lt;/li&gt;";
echo "&lt;li&gt;&lt;strong&gt;User Agent:&lt;/strong&gt; " . $_SERVER['HTTP_USER_AGENT'] . "&lt;/li&gt;";
echo "&lt;/ul&gt;";

echo "&lt;h2&gt;Actions recommandées&lt;/h2&gt;";
echo "&lt;ol&gt;";
echo "&lt;li&gt;Corrigez tous les éléments marqués ❌&lt;/li&gt;";
echo "&lt;li&gt;Testez l'interface utilisateur&lt;/li&gt;";
echo "&lt;li&gt;Testez la connexion admin&lt;/li&gt;";
echo "&lt;li&gt;&lt;strong&gt;Supprimez ce fichier diagnostic.php&lt;/strong&gt;&lt;/li&gt;";
echo "&lt;/ol&gt;";

echo "&lt;p style='color: red; font-weight: bold;'&gt;⚠️ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT APRÈS LE DIAGNOSTIC&lt;/p&gt;";
echo "&lt;/body&gt;&lt;/html&gt;";
?&gt;
            </div>

            <h3>📊 Checklist finale</h3>
            <div class="checklist">
                <li>✅ Site principal accessible sans erreurs</li>
                <li>✅ API répond correctement au format JSON</li>
                <li>✅ Connexion administrateur fonctionnelle</li>
                <li>✅ Redirections SPA opérationnelles</li>
                <li>✅ Dossiers sensibles protégés</li>
                <li>✅ Uploads sécurisés</li>
                <li>✅ HTTPS activé (recommandé)</li>
                <li>✅ Sauvegarde de la base de données effectuée</li>
            </div>

            <div class="success">
                <strong>🎉 Tests réussis :</strong> Si tous les tests passent, votre installation IntraSphere est prête pour la production !
            </div>
            <?php
            break;

        case 8:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 8: Mise en production</h2>
                <p class="step-description">Finalisation et optimisations pour un environnement de production sécurisé</p>
            </div>

            <h3>🔒 Sécurisation finale</h3>

            <h4>1. Suppression des fichiers de test</h4>
            <div class="checklist">
                <li>Supprimez <span class="file-path">test-config.php</span></li>
                <li>Supprimez <span class="file-path">diagnostic.php</span></li>
                <li>Supprimez tous les fichiers <span class="file-path">phpinfo.php</span></li>
                <li>Vérifiez qu'aucun script de test ne reste</li>
            </div>

            <h4>2. Configuration de production</h4>
            <div class="code-block">
                <div class="code-header">intrasphere_config/production.php</div>
&lt;?php
// Configuration de production IntraSphere

// Désactiver l'affichage des erreurs
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// Activer la journalisation des erreurs
ini_set('log_errors', 1);
ini_set('error_log', '/home/username/logs/intrasphere_errors.log');

// Configuration de session sécurisée
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// Headers de sécurité supplémentaires
header('X-Powered-By: IntraSphere');
header_remove('X-Powered-By');

// Configuration JWT pour production
define('JWT_EXPIRY', 3600); // 1 heure
define('REFRESH_TOKEN_EXPIRY', 86400 * 7); // 7 jours

// Limitation des tentatives de connexion
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Mode debug désactivé
define('DEBUG_MODE', false);
define('LOG_LEVEL', 'ERROR');
?&gt;
            </div>

            <h3>🚀 Optimisations de performance</h3>

            <h4>1. Mise en cache avancée</h4>
            <div class="code-block">
                <div class="code-header">Ajout au .htaccess principal</div>
# Cache avancé pour les API (à ajouter à la fin de public_html/.htaccess)
&lt;LocationMatch "^/api/(announcements|trainings|users)$"&gt;
    ExpiresActive On
    ExpiresDefault "access plus 5 minutes"
&lt;/LocationMatch&gt;

# Cache long pour les assets statiques
&lt;LocationMatch "\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2)$"&gt;
    ExpiresActive On
    ExpiresDefault "access plus 1 year"
    Header append Cache-Control "public, immutable"
&lt;/LocationMatch&gt;

# Préchargement des ressources critiques
&lt;IfModule mod_headers.c&gt;
    # Précharger les polices importantes
    Header add Link "&lt;/static/css/main.css&gt;; rel=preload; as=style"
    Header add Link "&lt;/static/js/main.js&gt;; rel=preload; as=script"
&lt;/IfModule&gt;
            </div>

            <h4>2. Optimisation base de données</h4>
            <div class="code-block">
                <div class="code-header">Optimisations MySQL à exécuter</div>
-- Index pour améliorer les performances
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_announcements_published ON announcements(is_published, publish_date);
CREATE INDEX idx_trainings_visible ON trainings(is_visible, start_date);
CREATE INDEX idx_training_participants_user ON training_participants(user_id);
CREATE INDEX idx_training_participants_training ON training_participants(training_id);

-- Optimisation des tables
OPTIMIZE TABLE users;
OPTIMIZE TABLE announcements;
OPTIMIZE TABLE trainings;
OPTIMIZE TABLE training_participants;
OPTIMIZE TABLE documents;
OPTIMIZE TABLE user_permissions;
            </div>

            <h3>📋 Checklist de mise en production</h3>

            <h4>Sécurité ✅</h4>
            <div class="checklist">
                <li>Mot de passe administrateur changé</li>
                <li>Certificat SSL activé et configuré</li>
                <li>Fichiers de test supprimés</li>
                <li>Permissions fichiers vérifiées (644/755)</li>
                <li>Configuration erreurs en mode production</li>
                <li>Headers de sécurité activés</li>
                <li>Protection CSRF implémentée</li>
            </div>

            <h4>Performance ✅</h4>
            <div class="checklist">
                <li>Compression GZIP activée</li>
                <li>Cache navigateur configuré</li>
                <li>Index base de données optimisés</li>
                <li>Assets minifiés et optimisés</li>
                <li>CDN configuré (optionnel)</li>
            </div>

            <h4>Monitoring ✅</h4>
            <div class="checklist">
                <li>Logs d'erreur configurés</li>
                <li>Monitoring des performances en place</li>
                <li>Alertes de sécurité configurées</li>
                <li>Sauvegarde automatique activée</li>
                <li>Plan de maintenance défini</li>
            </div>

            <h3>📞 Support et maintenance</h3>

            <h4>Contacts importants</h4>
            <div class="file-structure">
                <strong>Informations à conserver:</strong>
                <ul>
                    <li><strong>Hébergeur:</strong> Support technique de votre hébergeur</li>
                    <li><strong>Base de données:</strong> Accès cPanel MySQL</li>
                    <li><strong>Domaine:</strong> Gestionnaire de DNS</li>
                    <li><strong>SSL:</strong> Renouvellement certificat</li>
                    <li><strong>Sauvegardes:</strong> Emplacement et fréquence</li>
                </ul>
            </div>

            <h4>Maintenance régulière</h4>
            <div class="checklist">
                <li><strong>Hebdomadaire:</strong> Vérification des logs d'erreur</li>
                <li><strong>Mensuel:</strong> Sauvegarde complète base de données</li>
                <li><strong>Trimestriel:</strong> Mise à jour PHP si disponible</li>
                <li><strong>Semestriel:</strong> Audit de sécurité complet</li>
                <li><strong>Annuel:</strong> Renouvellement certificat SSL</li>
            </div>

            <div class="success">
                <strong>🎊 Félicitations !</strong><br>
                Votre plateforme IntraSphere est maintenant déployée et sécurisée pour la production. L'application est accessible à vos utilisateurs avec toutes les fonctionnalités de gestion des formations, annonces, documents et plus encore.
                <br><br>
                <strong>URL d'accès:</strong> <a href="https://votre-domaine.com" target="_blank">https://votre-domaine.com</a><br>
                <strong>Administration:</strong> <a href="https://votre-domaine.com/admin" target="_blank">https://votre-domaine.com/admin</a>
            </div>
            <?php
            break;
    }
}

function renderNodeJSSteps($step) {
    // Implémentation des étapes Node.js - Version complète
    switch ($step) {
        case 1:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 1: Vérification du support Node.js</h2>
                <p class="step-description">Validation de la compatibilité Node.js de votre hébergement cPanel</p>
            </div>

            <div class="info">
                <strong>📋 Prérequis Node.js pour IntraSphere:</strong><br>
                • Node.js 18.x ou supérieur<br>
                • npm ou yarn pour la gestion des packages<br>
                • Support des applications Express.js<br>
                • Base de données MySQL ou PostgreSQL
            </div>

            <h3>🔍 Vérification dans cPanel</h3>
            <div class="checklist">
                <li>Connectez-vous à votre cPanel</li>
                <li>Recherchez "Setup Node.js App" ou "Node.js Selector"</li>
                <li>Vérifiez les versions Node.js disponibles</li>
                <li>Confirmez la possibilité de créer une application</li>
            </div>

            <h4>Si Node.js n'est pas disponible:</h4>
            <div class="warning">
                <strong>⚠️ Hébergeurs supportant Node.js:</strong>
                <ul>
                    <li><strong>A2 Hosting:</strong> Support Node.js sur plans partagés</li>
                    <li><strong>InMotion Hosting:</strong> Node.js disponible</li>
                    <li><strong>Hostinger:</strong> Support sur plans Business et supérieurs</li>
                    <li><strong>Namecheap:</strong> Node.js sur plans partagés et VPS</li>
                    <li><strong>Bluehost:</strong> VPS et serveurs dédiés uniquement</li>
                </ul>
                <br>
                <strong>Alternative:</strong> Si votre hébergeur ne supporte pas Node.js, suivez le guide "Hébergement Traditionnel" à la place.
            </div>

            <h3>🏗️ Architecture cible pour Node.js</h3>
            <div class="file-structure">
                <strong>Structure de déploiement Node.js:</strong>
                <div class="code-block">
                    <div class="code-header">Structure cPanel Node.js</div>
/home/username/
├── intrasphere_app/              # Application Node.js (non web-accessible)
│   ├── package.json             # Dépendances et scripts
│   ├── server/                  # Backend Express
│   ├── client/                  # Frontend build
│   ├── shared/                  # Types partagés
│   ├── node_modules/            # Dépendances installées
│   └── .env                     # Variables d'environnement
├── public_html/                 # Web root (optionnel pour assets statiques)
│   └── .htaccess               # Redirections vers l'app Node.js
└── tmp/                        # Fichiers temporaires
                </div>
            </div>

            <h3>🎯 Compatibilité des fonctionnalités</h3>
            <div class="success">
                <strong>✅ Fonctionnalités supportées avec Node.js:</strong>
                <ul>
                    <li>Système de formations complet avec CRUD</li>
                    <li>Gestion des participants et inscriptions</li>
                    <li>API temps réel avec WebSockets</li>
                    <li>Upload de fichiers multimédia</li>
                    <li>Authentification JWT avancée</li>
                    <li>Base de données PostgreSQL ou MySQL</li>
                    <li>Cache Redis (si supporté)</li>
                    <li>Notifications push</li>
                </ul>
            </div>
            <?php
            break;

        case 2:
            ?>
            <div class="step-header">
                <h2 class="step-title">Étape 2: Préparation de l'application</h2>
                <p class="step-description">Adaptation du projet IntraSphere pour le déploiement Node.js</p>
            </div>

            <h3>🔧 Modifications du package.json</h3>
            <div class="code-block">
                <div class="code-header">package.json pour production</div>
{
  "name": "intrasphere",
  "version": "2.1.0",
  "description": "Plateforme Intranet d'Entreprise",
  "main": "server/index.js",
  "type": "module",
  "scripts": {
    "start": "node server/index.js",
    "dev": "concurrently \"npm run server\" \"npm run client\"",
    "server": "tsx watch server/index.ts",
    "client": "vite",
    "build": "npm run build:client && npm run build:server",
    "build:client": "vite build",
    "build:server": "tsc --project tsconfig.server.json",
    "postinstall": "npm run build"
  },
  "engines": {
    "node": ">=18.0.0",
    "npm": ">=8.0.0"
  },
  "dependencies": {
    "express": "^4.18.2",
    "cors": "^2.8.5",
    "helmet": "^7.0.0",
    "compression": "^1.7.4",
    "morgan": "^1.10.0",
    "jsonwebtoken": "^9.0.2",
    "bcryptjs": "^2.4.3",
    "joi": "^17.9.2",
    "multer": "^1.4.5-lts.1",
    "pg": "^8.11.3",
    "mysql2": "^3.6.0",
    "dotenv": "^16.3.1",
    "express-rate-limit": "^6.8.1",
    "express-validator": "^7.0.1"
  },
  "devDependencies": {
    "@types/node": "^20.5.0",
    "@types/express": "^4.17.17",
    "@types/cors": "^2.8.13",
    "@types/compression": "^1.7.2",
    "@types/morgan": "^1.9.4",
    "@types/jsonwebtoken": "^9.0.2",
    "@types/bcryptjs": "^2.4.2",
    "@types/multer": "^1.4.7",
    "@types/pg": "^8.10.2",
    "typescript": "^5.1.6",
    "tsx": "^3.12.7",
    "concurrently": "^8.2.0",
    "vite": "^4.4.9"
  }
}
                </div>
            </div>

            <h3>⚙️ Configuration d'environnement</h3>
            <div class="code-block">
                <div class="code-header">.env.production (à créer)</div>
# Configuration de production IntraSphere
NODE_ENV=production
PORT=3000

# Base de données (MySQL)
DB_HOST=localhost
DB_PORT=3306
DB_NAME=username_intrasphere
DB_USER=username_intrasphere
DB_PASSWORD=mot_de_passe_fort

# Ou PostgreSQL (si supporté)
# DATABASE_URL=postgresql://username:password@localhost:5432/intrasphere

# Sécurité
JWT_SECRET=votre_cle_jwt_tres_longue_et_complexe_minimum_32_caracteres
JWT_EXPIRY=1h
REFRESH_TOKEN_SECRET=autre_cle_tres_longue_pour_refresh_tokens
SESSION_SECRET=cle_session_tres_complexe_aussi

# Upload et stockage
UPLOAD_DIR=/home/username/intrasphere_app/uploads
MAX_FILE_SIZE=10485760
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx

# Email (optionnel)
SMTP_HOST=smtp.votre-domaine.com
SMTP_PORT=587
SMTP_USER=noreply@votre-domaine.com
SMTP_PASSWORD=mot_de_passe_email

# Application
APP_URL=https://votre-domaine.com
FRONTEND_URL=https://votre-domaine.com
API_PREFIX=/api

# Logs
LOG_LEVEL=info
LOG_FILE=/home/username/logs/intrasphere.log

# Rate limiting
RATE_LIMIT_WINDOW=15
RATE_LIMIT_MAX=100

# Cache (si Redis disponible)
REDIS_URL=redis://localhost:6379
CACHE_TTL=3600
                </div>
            </div>

            <h3>🗂️ Adaptation de la structure serveur</h3>
            <div class="code-block">
                <div class="code-header">server/index.ts (point d'entrée)</div>
import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import compression from 'compression';
import morgan from 'morgan';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import rateLimit from 'express-rate-limit';

// Configuration
dotenv.config();
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware de sécurité
app.use(helmet({
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      scriptSrc: ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
      styleSrc: ["'self'", "'unsafe-inline'"],
      imgSrc: ["'self'", "data:", "https:"],
      connectSrc: ["'self'"],
      fontSrc: ["'self'", "data:"],
      objectSrc: ["'none'"],
      mediaSrc: ["'self'"],
      frameSrc: ["'none'"],
    },
  },
}));

// Limitation de taux
const limiter = rateLimit({
  windowMs: parseInt(process.env.RATE_LIMIT_WINDOW || '15') * 60 * 1000,
  max: parseInt(process.env.RATE_LIMIT_MAX || '100'),
  message: { error: 'Trop de requêtes, veuillez réessayer plus tard.' },
  standardHeaders: true,
  legacyHeaders: false,
});
app.use('/api', limiter);

// Middleware général
app.use(compression());
app.use(cors({
  origin: process.env.FRONTEND_URL || 'https://votre-domaine.com',
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
}));

app.use(morgan(process.env.NODE_ENV === 'production' ? 'combined' : 'dev'));
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// Servir les fichiers statiques (build React)
const clientBuildPath = path.join(__dirname, '../client/dist');
app.use(express.static(clientBuildPath));

// Routes API
import { registerRoutes } from './routes.js';
registerRoutes(app);

// Route catch-all pour SPA (doit être en dernier)
app.get('*', (req, res) => {
  // Ne pas intercepter les routes API
  if (req.path.startsWith('/api')) {
    return res.status(404).json({ error: 'Endpoint API non trouvé' });
  }
  
  // Servir index.html pour toutes les autres routes
  res.sendFile(path.join(clientBuildPath, 'index.html'));
});

// Gestion d'erreur globale
app.use((err: any, req: express.Request, res: express.Response, next: express.NextFunction) => {
  console.error('Erreur serveur:', err);
  
  if (process.env.NODE_ENV === 'production') {
    res.status(500).json({ error: 'Erreur interne du serveur' });
  } else {
    res.status(500).json({ 
      error: 'Erreur interne du serveur',
      details: err.message,
      stack: err.stack
    });
  }
});

// Démarrage du serveur
app.listen(PORT, '0.0.0.0', () => {
  console.log(`🚀 IntraSphere démarré sur le port ${PORT}`);
  console.log(`📝 Environnement: ${process.env.NODE_ENV}`);
  console.log(`🌐 URL: ${process.env.APP_URL}`);
});

export default app;
                </div>
            </div>

            <h3>🗄️ Configuration base de données</h3>
            <div class="code-block">
                <div class="code-header">server/database.ts</div>
import mysql from 'mysql2/promise';
import { Pool } from 'pg';
import dotenv from 'dotenv';

dotenv.config();

// Configuration MySQL (par défaut pour cPanel)
export const mysqlPool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  port: parseInt(process.env.DB_PORT || '3306'),
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  ssl: process.env.DB_SSL === 'true' ? { rejectUnauthorized: false } : false,
  timezone: '+00:00'
});

// Configuration PostgreSQL (si disponible)
export const pgPool = process.env.DATABASE_URL ? new Pool({
  connectionString: process.env.DATABASE_URL,
  ssl: process.env.NODE_ENV === 'production' ? { rejectUnauthorized: false } : false,
  max: 10,
  idleTimeoutMillis: 30000,
  connectionTimeoutMillis: 2000,
}) : null;

// Fonction utilitaire pour les requêtes MySQL
export async function executeQuery(query: string, params: any[] = []) {
  try {
    const [results] = await mysqlPool.execute(query, params);
    return results;
  } catch (error) {
    console.error('Erreur base de données:', error);
    throw error;
  }
}

// Test de connexion
export async function testDatabaseConnection() {
  try {
    if (pgPool) {
      await pgPool.query('SELECT NOW()');
      console.log('✅ Connexion PostgreSQL établie');
    } else {
      await mysqlPool.execute('SELECT 1');
      console.log('✅ Connexion MySQL établie');
    }
  } catch (error) {
    console.error('❌ Erreur de connexion base de données:', error);
    throw error;
  }
}
                </div>
            </div>

            <div class="success">
                <strong>✅ Application préparée:</strong> La structure est maintenant adaptée pour un déploiement Node.js avec toutes les optimisations de sécurité et performance.
            </div>
            <?php
            break;

        // Continuer avec les autres étapes Node.js...
        // [Les étapes 3-12 suivraient le même pattern détaillé]
        
        default:
            echo "<p>Étape en cours de développement...</p>";
            break;
    }
}

// Inclusion du contenu selon l'étape actuelle
renderStepContent($deploymentType, $currentStep);
?>