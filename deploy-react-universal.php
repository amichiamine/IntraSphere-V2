<?php
/**
 * 🚀 IntraSphere React - Assistant d'Installation Universel
 * 
 * Script d'installation automatique pour toutes plateformes :
 * - Hébergement web (cPanel, Plesk, DirectAdmin)
 * - VPS/Serveurs dédiés (Linux, Windows)
 * - Cloud platforms (AWS, GCP, Azure)
 * - Développement local (Windows, Mac, Linux)
 * - Conteneurs (Docker, Kubernetes)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 300); // 5 minutes pour installation

// Configuration
$config = [
    'app_name' => 'IntraSphere React',
    'version' => '2.0.0',
    'node_min_version' => '18.0.0',
    'npm_min_version' => '9.0.0',
    'required_extensions' => ['curl', 'json', 'zip'],
    'optional_extensions' => ['openssl', 'mbstring']
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $config['app_name'] ?> - Installation Universelle</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #8B5CF6, #A78BFA);
            color: white;
            padding: 20px;
            font-size: 1.3em;
            font-weight: 600;
        }
        .card-body {
            padding: 25px;
        }
        .step {
            margin-bottom: 25px;
            padding: 15px;
            border-left: 4px solid #8B5CF6;
            background: #f8f9ff;
            border-radius: 0 8px 8px 0;
        }
        .step h3 {
            color: #8B5CF6;
            margin-bottom: 10px;
        }
        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            margin-left: 10px;
        }
        .status.success { background: #10B981; color: white; }
        .status.warning { background: #F59E0B; color: white; }
        .status.error { background: #EF4444; color: white; }
        .status.info { background: #3B82F6; color: white; }
        .btn {
            background: linear-gradient(135deg, #8B5CF6, #A78BFA);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn.secondary {
            background: #6B7280;
        }
        .code-block {
            background: #1a1a1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
        .platform-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .platform-card {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .platform-card:hover, .platform-card.selected {
            border-color: #8B5CF6;
            background: #f8f9ff;
        }
        .platform-card h4 {
            color: #8B5CF6;
            margin-bottom: 8px;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin: 15px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #8B5CF6, #A78BFA);
            width: 0%;
            transition: width 0.3s;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .alert.success { background: #D1FAE5; border-left: 4px solid #10B981; }
        .alert.warning { background: #FEF3C7; border-left: 4px solid #F59E0B; }
        .alert.error { background: #FEE2E2; border-left: 4px solid #EF4444; }
        .alert.info { background: #DBEAFE; border-left: 4px solid #3B82F6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 <?= $config['app_name'] ?></h1>
            <p>Assistant d'Installation Universel - Version <?= $config['version'] ?></p>
        </div>

        <?php
        $step = $_GET['step'] ?? 'detect';
        $platform = $_GET['platform'] ?? '';

        switch ($step) {
            case 'detect':
                showEnvironmentDetection();
                break;
            case 'platform':
                showPlatformSelection();
                break;
            case 'install':
                showInstallationProcess($platform);
                break;
            case 'configure':
                showConfiguration($platform);
                break;
            case 'complete':
                showCompletion();
                break;
            default:
                showEnvironmentDetection();
        }

        function showEnvironmentDetection() {
            global $config;
            ?>
            <div class="card">
                <div class="card-header">
                    🔍 Détection Automatique de l'Environnement
                </div>
                <div class="card-body">
                    <div class="step">
                        <h3>1. Vérification du Système</h3>
                        <?php
                        $checks = performSystemChecks();
                        foreach ($checks as $check) {
                            echo "<p>{$check['name']} <span class='status {$check['status']}'>{$check['message']}</span></p>";
                        }
                        ?>
                    </div>

                    <div class="step">
                        <h3>2. Détection de l'Environnement</h3>
                        <?php
                        $environment = detectEnvironment();
                        echo "<p><strong>Environnement détecté :</strong> <span class='status info'>{$environment['name']}</span></p>";
                        echo "<p><strong>Type :</strong> {$environment['type']}</p>";
                        echo "<p><strong>Recommandation :</strong> {$environment['recommendation']}</p>";
                        ?>
                    </div>

                    <div class="step">
                        <h3>3. Prérequis Node.js</h3>
                        <?php
                        $nodeChecks = checkNodeEnvironment();
                        foreach ($nodeChecks as $check) {
                            echo "<p>{$check['name']} <span class='status {$check['status']}'>{$check['message']}</span></p>";
                        }
                        ?>
                    </div>

                    <?php if (allChecksPass($checks) && allChecksPass($nodeChecks)): ?>
                        <div class="alert success">
                            ✅ Tous les prérequis sont satisfaits ! Vous pouvez procéder à l'installation.
                        </div>
                        <a href="?step=platform" class="btn">Continuer l'Installation →</a>
                    <?php else: ?>
                        <div class="alert warning">
                            ⚠️ Certains prérequis ne sont pas satisfaits. Consultez les instructions ci-dessous.
                        </div>
                        <?php showTroubleshooting($checks, $nodeChecks); ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }

        function showPlatformSelection() {
            ?>
            <div class="card">
                <div class="card-header">
                    🌐 Sélection de la Plateforme de Déploiement
                </div>
                <div class="card-body">
                    <p>Choisissez votre environnement de déploiement pour une configuration optimisée :</p>
                    
                    <div class="platform-grid">
                        <div class="platform-card" onclick="selectPlatform('replit')">
                            <h4>🔄 Replit</h4>
                            <p>Développement cloud intégré</p>
                            <small>Recommandé pour débutants</small>
                        </div>
                        <div class="platform-card" onclick="selectPlatform('vercel')">
                            <h4>▲ Vercel</h4>
                            <p>Déploiement serverless</p>
                            <small>Idéal pour production</small>
                        </div>
                        <div class="platform-card" onclick="selectPlatform('netlify')">
                            <h4>🌐 Netlify</h4>
                            <p>JAMstack hosting</p>
                            <small>CI/CD automatique</small>
                        </div>
                        <div class="platform-card" onclick="selectPlatform('cpanel')">
                            <h4>🔧 cPanel</h4>
                            <p>Hébergement traditionnel</p>
                            <small>Node.js supporté</small>
                        </div>
                        <div class="platform-card" onclick="selectPlatform('vps')">
                            <h4>🖥️ VPS/Dédié</h4>
                            <p>Serveur personnel</p>
                            <small>Contrôle total</small>
                        </div>
                        <div class="platform-card" onclick="selectPlatform('docker')">
                            <h4>🐳 Docker</h4>
                            <p>Conteneurisation</p>
                            <small>Portable et scalable</small>
                        </div>
                        <div class="platform-card" onclick="selectPlatform('local')">
                            <h4>💻 Local</h4>
                            <p>Développement local</p>
                            <small>Windows, Mac, Linux</small>
                        </div>
                        <div class="platform-card" onclick="selectPlatform('aws')">
                            <h4>☁️ AWS</h4>
                            <p>Amazon Web Services</p>
                            <small>Enterprise grade</small>
                        </div>
                    </div>

                    <script>
                    function selectPlatform(platform) {
                        document.querySelectorAll('.platform-card').forEach(card => {
                            card.classList.remove('selected');
                        });
                        event.target.closest('.platform-card').classList.add('selected');
                        
                        setTimeout(() => {
                            window.location.href = '?step=install&platform=' + platform;
                        }, 500);
                    }
                    </script>
                </div>
            </div>
            <?php
        }

        function showInstallationProcess($platform) {
            ?>
            <div class="card">
                <div class="card-header">
                    🛠️ Installation pour <?= getPlatformName($platform) ?>
                </div>
                <div class="card-body">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress" style="width: 25%"></div>
                    </div>
                    
                    <?php
                    switch ($platform) {
                        case 'replit':
                            showReplitInstructions();
                            break;
                        case 'vercel':
                            showVercelInstructions();
                            break;
                        case 'netlify':
                            showNetlifyInstructions();
                            break;
                        case 'cpanel':
                            showCPanelInstructions();
                            break;
                        case 'vps':
                            showVPSInstructions();
                            break;
                        case 'docker':
                            showDockerInstructions();
                            break;
                        case 'local':
                            showLocalInstructions();
                            break;
                        case 'aws':
                            showAWSInstructions();
                            break;
                        default:
                            showGenericInstructions();
                    }
                    ?>
                    
                    <div style="margin-top: 30px;">
                        <a href="?step=configure&platform=<?= $platform ?>" class="btn">Configuration →</a>
                        <a href="?step=platform" class="btn secondary">← Retour</a>
                    </div>
                </div>
            </div>
            <?php
        }

        function showConfiguration($platform) {
            ?>
            <div class="card">
                <div class="card-header">
                    ⚙️ Configuration <?= getPlatformName($platform) ?>
                </div>
                <div class="card-body">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%"></div>
                    </div>

                    <form method="post" action="?step=complete&platform=<?= $platform ?>">
                        <div class="step">
                            <h3>Variables d'Environnement</h3>
                            <p>Configurez les paramètres de votre application :</p>
                            
                            <div style="margin: 15px 0;">
                                <label><strong>Mode d'Environnement :</strong></label><br>
                                <input type="radio" name="node_env" value="development" checked> Développement
                                <input type="radio" name="node_env" value="production" style="margin-left: 20px;"> Production
                            </div>

                            <div style="margin: 15px 0;">
                                <label><strong>Port d'Application :</strong></label><br>
                                <input type="number" name="port" value="5000" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>

                            <div style="margin: 15px 0;">
                                <label><strong>URL de Base de Données :</strong></label><br>
                                <input type="text" name="database_url" placeholder="postgresql://user:pass@host:port/db" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <small style="color: #666;">Laissez vide pour utiliser la base par défaut</small>
                            </div>
                        </div>

                        <div class="step">
                            <h3>Sécurité</h3>
                            <div style="margin: 15px 0;">
                                <label><strong>Clé Secrète Session :</strong></label><br>
                                <input type="text" name="session_secret" value="<?= generateSecureKey() ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        </div>

                        <?php if ($platform === 'cpanel' || $platform === 'vps'): ?>
                        <div class="step">
                            <h3>Configuration Serveur</h3>
                            <div style="margin: 15px 0;">
                                <label><strong>Domaine :</strong></label><br>
                                <input type="text" name="domain" placeholder="example.com" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div style="margin: 15px 0;">
                                <input type="checkbox" name="ssl_enabled" checked> Activer HTTPS/SSL
                            </div>
                        </div>
                        <?php endif; ?>

                        <button type="submit" class="btn">Finaliser l'Installation →</button>
                    </form>
                </div>
            </div>
            <?php
        }

        function showCompletion() {
            if ($_POST) {
                $config = $_POST;
                generateConfigFiles($config);
            }
            ?>
            <div class="card">
                <div class="card-header">
                    🎉 Installation Terminée !
                </div>
                <div class="card-body">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%"></div>
                    </div>

                    <div class="alert success">
                        ✅ <?= $config['app_name'] ?> a été installé avec succès !
                    </div>

                    <div class="step">
                        <h3>Prochaines Étapes</h3>
                        <ol>
                            <li>Les fichiers de configuration ont été générés automatiquement</li>
                            <li>Installez les dépendances : <code>npm install</code></li>
                            <li>Démarrez l'application : <code>npm run dev</code></li>
                            <li>Accédez à votre application sur <a href="http://localhost:5000" target="_blank">http://localhost:5000</a></li>
                        </ol>
                    </div>

                    <div class="step">
                        <h3>Identifiants par Défaut</h3>
                        <p><strong>Utilisateur :</strong> admin</p>
                        <p><strong>Mot de passe :</strong> admin</p>
                        <div class="alert warning">
                            ⚠️ Changez ces identifiants immédiatement après votre première connexion !
                        </div>
                    </div>

                    <div class="step">
                        <h3>Commandes Utiles</h3>
                        <div class="code-block">
# Développement
npm run dev

# Build de production
npm run build

# Démarrage production
npm start

# Vérification TypeScript
npm run check

# Migration base de données
npm run db:push
                        </div>
                    </div>

                    <div class="step">
                        <h3>Support & Documentation</h3>
                        <p>📚 <a href="README.md" target="_blank">Documentation complète</a></p>
                        <p>🔧 <a href="replit.md" target="_blank">Guide technique</a></p>
                        <p>❓ <a href="https://github.com/your-repo/issues" target="_blank">Support technique</a></p>
                    </div>

                    <a href="http://localhost:5000" class="btn" target="_blank">Ouvrir l'Application →</a>
                    <a href="?" class="btn secondary">Nouvelle Installation</a>
                </div>
            </div>
            <?php
        }

        // Fonctions utilitaires
        function performSystemChecks() {
            $checks = [];
            
            // Vérification PHP
            $phpVersion = phpversion();
            $checks[] = [
                'name' => 'Version PHP',
                'status' => version_compare($phpVersion, '7.4.0', '>=') ? 'success' : 'error',
                'message' => $phpVersion . (version_compare($phpVersion, '7.4.0', '>=') ? ' ✓' : ' (7.4+ requis)')
            ];

            // Vérification extensions PHP
            foreach (['curl', 'json', 'zip'] as $ext) {
                $checks[] = [
                    'name' => "Extension $ext",
                    'status' => extension_loaded($ext) ? 'success' : 'error',
                    'message' => extension_loaded($ext) ? 'Activée ✓' : 'Manquante ✗'
                ];
            }

            // Vérification permissions
            $checks[] = [
                'name' => 'Permissions écriture',
                'status' => is_writable('.') ? 'success' : 'error',
                'message' => is_writable('.') ? 'OK ✓' : 'Accès refusé ✗'
            ];

            return $checks;
        }

        function detectEnvironment() {
            // Détection Replit
            if (getenv('REPL_ID')) {
                return [
                    'name' => 'Replit',
                    'type' => 'Cloud Development',
                    'recommendation' => 'Parfait pour le développement et prototypage rapide'
                ];
            }

            // Détection cPanel
            if (isset($_SERVER['cPanel']) || file_exists('/usr/local/cpanel')) {
                return [
                    'name' => 'cPanel',
                    'type' => 'Hébergement Partagé',
                    'recommendation' => 'Vérifiez que Node.js est activé dans votre compte'
                ];
            }

            // Détection Docker
            if (file_exists('/.dockerenv')) {
                return [
                    'name' => 'Docker Container',
                    'type' => 'Conteneurisé',
                    'recommendation' => 'Environnement isolé et portable'
                ];
            }

            // Détection VPS/Cloud
            if (function_exists('shell_exec')) {
                $os = shell_exec('uname -s 2>/dev/null');
                if ($os) {
                    return [
                        'name' => trim($os) . ' Server',
                        'type' => 'VPS/Serveur Dédié',
                        'recommendation' => 'Contrôle total sur l\'environnement'
                    ];
                }
            }

            // Par défaut
            return [
                'name' => 'Environnement Web Standard',
                'type' => 'Hébergement Web',
                'recommendation' => 'Configuration manuelle nécessaire'
            ];
        }

        function checkNodeEnvironment() {
            $checks = [];
            
            // Vérification Node.js
            $nodeVersion = shell_exec('node --version 2>/dev/null');
            if ($nodeVersion) {
                $nodeVersion = trim(str_replace('v', '', $nodeVersion));
                $checks[] = [
                    'name' => 'Node.js',
                    'status' => version_compare($nodeVersion, '18.0.0', '>=') ? 'success' : 'warning',
                    'message' => "v$nodeVersion " . (version_compare($nodeVersion, '18.0.0', '>=') ? '✓' : '(18+ recommandé)')
                ];
            } else {
                $checks[] = [
                    'name' => 'Node.js',
                    'status' => 'error',
                    'message' => 'Non installé ✗'
                ];
            }

            // Vérification npm
            $npmVersion = shell_exec('npm --version 2>/dev/null');
            if ($npmVersion) {
                $npmVersion = trim($npmVersion);
                $checks[] = [
                    'name' => 'npm',
                    'status' => version_compare($npmVersion, '9.0.0', '>=') ? 'success' : 'warning',
                    'message' => "v$npmVersion " . (version_compare($npmVersion, '9.0.0', '>=') ? '✓' : '(9+ recommandé)')
                ];
            } else {
                $checks[] = [
                    'name' => 'npm',
                    'status' => 'error',
                    'message' => 'Non installé ✗'
                ];
            }

            return $checks;
        }

        function allChecksPass($checks) {
            foreach ($checks as $check) {
                if ($check['status'] === 'error') {
                    return false;
                }
            }
            return true;
        }

        function showTroubleshooting($checks, $nodeChecks) {
            ?>
            <div class="step">
                <h3>🔧 Guide de Résolution</h3>
                
                <?php foreach (array_merge($checks, $nodeChecks) as $check): ?>
                    <?php if ($check['status'] === 'error'): ?>
                        <div class="alert error">
                            <strong><?= $check['name'] ?> :</strong> <?= $check['message'] ?><br>
                            <?php
                            switch ($check['name']) {
                                case 'Node.js':
                                    echo "• Installez Node.js depuis <a href='https://nodejs.org' target='_blank'>nodejs.org</a><br>";
                                    echo "• Ou utilisez un gestionnaire de versions comme nvm";
                                    break;
                                case 'npm':
                                    echo "• npm est inclus avec Node.js<br>";
                                    echo "• Mettez à jour avec: <code>npm install -g npm@latest</code>";
                                    break;
                                default:
                                    echo "• Contactez votre hébergeur pour activer cette extension";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php
        }

        function getPlatformName($platform) {
            $names = [
                'replit' => 'Replit',
                'vercel' => 'Vercel',
                'netlify' => 'Netlify',
                'cpanel' => 'cPanel',
                'vps' => 'VPS/Serveur Dédié',
                'docker' => 'Docker',
                'local' => 'Développement Local',
                'aws' => 'Amazon Web Services'
            ];
            return $names[$platform] ?? 'Plateforme Inconnue';
        }

        function generateSecureKey() {
            return bin2hex(random_bytes(32));
        }

        function generateConfigFiles($config) {
            // Génération du fichier .env
            $envContent = "# Configuration générée automatiquement\n";
            $envContent .= "NODE_ENV=" . ($config['node_env'] ?? 'development') . "\n";
            $envContent .= "PORT=" . ($config['port'] ?? '5000') . "\n";
            $envContent .= "SESSION_SECRET=" . ($config['session_secret'] ?? generateSecureKey()) . "\n";
            
            if (!empty($config['database_url'])) {
                $envContent .= "DATABASE_URL=" . $config['database_url'] . "\n";
            }
            
            file_put_contents('.env', $envContent);

            // Génération du script de démarrage
            $startScript = "#!/bin/bash\n";
            $startScript .= "# Script de démarrage généré automatiquement\n";
            $startScript .= "echo \"Démarrage d'IntraSphere React...\"\n";
            $startScript .= "npm install\n";
            $startScript .= "npm run dev\n";
            
            file_put_contents('start.sh', $startScript);
            chmod('start.sh', 0755);
        }

        // Instructions spécifiques par plateforme
        function showReplitInstructions() {
            ?>
            <div class="step">
                <h3>🔄 Installation sur Replit</h3>
                <p>Votre environnement Replit est déjà configuré !</p>
                <ol>
                    <li>Cliquez sur le bouton "Run" en haut</li>
                    <li>L'application se lancera automatiquement</li>
                    <li>Utilisez le panneau "Deployments" pour publier en production</li>
                </ol>
                <div class="alert info">
                    💡 Replit gère automatiquement Node.js, les dépendances et l'hébergement !
                </div>
            </div>
            <?php
        }

        function showVercelInstructions() {
            ?>
            <div class="step">
                <h3>▲ Déploiement sur Vercel</h3>
                <div class="code-block">
# Installation Vercel CLI
npm i -g vercel

# Déploiement en une commande
vercel

# Ou via GitHub (recommandé)
# 1. Push votre code sur GitHub
# 2. Connectez le repo sur vercel.com
# 3. Déploiement automatique à chaque commit
                </div>
                <div class="alert success">
                    ✅ Vercel détecte automatiquement les projets React/Vite !
                </div>
            </div>
            <?php
        }

        function showNetlifyInstructions() {
            ?>
            <div class="step">
                <h3>🌐 Déploiement sur Netlify</h3>
                <div class="code-block">
# Build de production
npm run build

# Déploiement via CLI
npm install -g netlify-cli
netlify deploy --prod --dir=dist/public

# Ou glisser-déposer le dossier dist/public sur netlify.com
                </div>
                <p><strong>Configuration build sur Netlify :</strong></p>
                <ul>
                    <li>Build command: <code>npm run build</code></li>
                    <li>Publish directory: <code>dist/public</code></li>
                </ul>
            </div>
            <?php
        }

        function showCPanelInstructions() {
            ?>
            <div class="step">
                <h3>🔧 Installation cPanel</h3>
                <div class="alert warning">
                    ⚠️ Vérifiez que Node.js est activé dans votre cPanel
                </div>
                <ol>
                    <li>Uploadez les fichiers dans <code>public_html</code></li>
                    <li>Activez Node.js dans cPanel (version 18+)</li>
                    <li>Configurez le point d'entrée : <code>server/index.js</code></li>
                    <li>Installez les dépendances via Terminal cPanel</li>
                </ol>
                <div class="code-block">
cd public_html
npm install
npm run build
npm start
                </div>
            </div>
            <?php
        }

        function showVPSInstructions() {
            ?>
            <div class="step">
                <h3>🖥️ Installation VPS/Serveur Dédié</h3>
                <div class="code-block">
# Installation Node.js (Ubuntu/Debian)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Clone et installation
git clone &lt;your-repo&gt; intrasphere
cd intrasphere
npm install

# Configuration Nginx (optionnel)
sudo nano /etc/nginx/sites-available/intrasphere

# Démarrage avec PM2
npm install -g pm2
pm2 start npm --name "intrasphere" -- start
pm2 startup
pm2 save
                </div>
            </div>
            <?php
        }

        function showDockerInstructions() {
            ?>
            <div class="step">
                <h3>🐳 Déploiement Docker</h3>
                <div class="code-block">
# Build de l'image
docker build -t intrasphere .

# Démarrage du conteneur
docker run -d \
  --name intrasphere \
  -p 5000:5000 \
  -e NODE_ENV=production \
  intrasphere

# Ou avec Docker Compose
docker-compose up -d
                </div>
                <p>Le Dockerfile est déjà configuré pour un déploiement optimisé !</p>
            </div>
            <?php
        }

        function showLocalInstructions() {
            ?>
            <div class="step">
                <h3>💻 Installation Locale</h3>
                <div class="code-block">
# Windows
# Installez Node.js depuis nodejs.org
# Puis dans PowerShell/CMD :
git clone &lt;repo-url&gt; intrasphere
cd intrasphere
npm install
npm run dev

# macOS
brew install node
git clone &lt;repo-url&gt; intrasphere
cd intrasphere && npm install && npm run dev

# Linux
sudo apt update && sudo apt install nodejs npm
git clone &lt;repo-url&gt; intrasphere
cd intrasphere && npm install && npm run dev
                </div>
            </div>
            <?php
        }

        function showAWSInstructions() {
            ?>
            <div class="step">
                <h3>☁️ Déploiement AWS</h3>
                <p><strong>Option 1: AWS Amplify (Recommandé)</strong></p>
                <div class="code-block">
# Installation Amplify CLI
npm install -g @aws-amplify/cli
amplify configure

# Initialisation du projet
amplify init
amplify add hosting
amplify publish
                </div>
                
                <p><strong>Option 2: EC2 + Load Balancer</strong></p>
                <div class="code-block">
# Instance EC2 avec User Data
#!/bin/bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
# Configuration automatique...
                </div>
            </div>
            <?php
        }

        function showGenericInstructions() {
            ?>
            <div class="step">
                <h3>📋 Instructions Générales</h3>
                <div class="code-block">
# 1. Prérequis
node --version  # v18.0.0+
npm --version   # v9.0.0+

# 2. Installation
npm install

# 3. Configuration
# Créez un fichier .env avec vos paramètres

# 4. Développement
npm run dev

# 5. Production
npm run build
npm start
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>