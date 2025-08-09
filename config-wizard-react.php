<?php
/**
 * 🧙‍♂️ IntraSphere React - Assistant de Configuration Intelligent
 * 
 * Configuration automatique pour tous types d'hébergement :
 * - Génération automatique des fichiers .env
 * - Configuration base de données
 * - Scripts de déploiement personnalisés
 * - Optimisation par environnement
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$config = [
    'app_name' => 'IntraSphere React',
    'version' => '2.0.0',
    'supported_db' => ['postgresql', 'mysql', 'sqlite'],
    'environments' => ['development', 'production', 'staging']
];

// Détection automatique de l'environnement
function detectHostingEnvironment() {
    $environment = [
        'type' => 'unknown',
        'name' => 'Environnement non reconnu',
        'features' => [],
        'recommendations' => []
    ];
    
    // Détection Replit
    if (getenv('REPL_ID')) {
        $environment = [
            'type' => 'replit',
            'name' => 'Replit',
            'features' => ['auto_ssl', 'auto_domain', 'git_integration', 'zero_config'],
            'recommendations' => [
                'Utilisez les Secrets Replit pour les variables sensibles',
                'Le port 5000 est automatiquement exposé',
                'Déployez via le bouton "Deploy" pour la production'
            ]
        ];
    }
    // Détection cPanel
    elseif (isset($_SERVER['cPanel']) || file_exists('/usr/local/cpanel') || strpos($_SERVER['DOCUMENT_ROOT'] ?? '', 'public_html') !== false) {
        $environment = [
            'type' => 'cpanel',
            'name' => 'cPanel/Hébergement Partagé',
            'features' => ['shared_hosting', 'nodejs_selector', 'ssl_available'],
            'recommendations' => [
                'Activez Node.js dans votre panneau cPanel',
                'Utilisez le sélecteur de version Node.js',
                'Configurez le point d\'entrée sur server/index.js',
                'Utilisez un sous-domaine pour Node.js si nécessaire'
            ]
        ];
    }
    // Détection Docker
    elseif (file_exists('/.dockerenv')) {
        $environment = [
            'type' => 'docker',
            'name' => 'Conteneur Docker',
            'features' => ['containerized', 'scalable', 'isolated'],
            'recommendations' => [
                'Utilisez des variables d\'environnement pour la configuration',
                'Montez les volumes pour la persistance des données',
                'Configurez les health checks',
                'Utilisez docker-compose pour les services multiples'
            ]
        ];
    }
    // Détection VPS/Cloud
    elseif (function_exists('shell_exec')) {
        $os = shell_exec('uname -s 2>/dev/null');
        if ($os) {
            $environment = [
                'type' => 'vps',
                'name' => 'VPS/Serveur Dédié (' . trim($os) . ')',
                'features' => ['full_control', 'custom_ports', 'ssl_certs', 'reverse_proxy'],
                'recommendations' => [
                    'Configurez un reverse proxy (Nginx/Apache)',
                    'Utilisez PM2 pour la gestion des processus',
                    'Configurez un firewall approprié',
                    'Mettez en place des sauvegardes automatiques'
                ]
            ];
        }
    }
    
    return $environment;
}

// Génération du fichier .env optimisé
function generateEnvFile($config) {
    $env_content = "# Configuration IntraSphere React\n";
    $env_content .= "# Générée automatiquement le " . date('Y-m-d H:i:s') . "\n\n";
    
    // Configuration de base
    $env_content .= "# Application\n";
    $env_content .= "NODE_ENV=" . $config['environment'] . "\n";
    $env_content .= "PORT=" . $config['port'] . "\n";
    $env_content .= "APP_NAME=\"IntraSphere React\"\n";
    $env_content .= "APP_VERSION=2.0.0\n\n";
    
    // Base de données
    if (!empty($config['database_url'])) {
        $env_content .= "# Base de données\n";
        $env_content .= "DATABASE_URL=\"" . $config['database_url'] . "\"\n\n";
    }
    
    // Sécurité
    $env_content .= "# Sécurité\n";
    $env_content .= "SESSION_SECRET=\"" . $config['session_secret'] . "\"\n";
    $env_content .= "BCRYPT_ROUNDS=12\n\n";
    
    // Configuration spécifique à l'environnement
    switch ($config['hosting_type']) {
        case 'replit':
            $env_content .= "# Configuration Replit\n";
            $env_content .= "REPLIT_URL=\"https://" . (getenv('REPL_SLUG') ?: 'your-repl') . "." . (getenv('REPL_OWNER') ?: 'username') . ".repl.co\"\n";
            break;
            
        case 'cpanel':
            $env_content .= "# Configuration cPanel\n";
            $env_content .= "CPANEL_MODE=true\n";
            if (!empty($config['domain'])) {
                $env_content .= "BASE_URL=\"https://" . $config['domain'] . "\"\n";
            }
            break;
            
        case 'vps':
            $env_content .= "# Configuration VPS\n";
            $env_content .= "TRUST_PROXY=true\n";
            if (!empty($config['domain'])) {
                $env_content .= "BASE_URL=\"https://" . $config['domain'] . "\"\n";
            }
            break;
    }
    
    // Options avancées
    if ($config['environment'] === 'production') {
        $env_content .= "\n# Production\n";
        $env_content .= "COMPRESSION=true\n";
        $env_content .= "RATE_LIMIT_ENABLED=true\n";
        $env_content .= "CORS_ORIGIN=\"" . ($config['cors_origin'] ?? '*') . "\"\n";
    } else {
        $env_content .= "\n# Développement\n";
        $env_content .= "DEBUG=true\n";
        $env_content .= "HOT_RELOAD=true\n";
    }
    
    return $env_content;
}

// Génération des scripts de déploiement
function generateDeploymentScripts($config) {
    $scripts = [];
    
    // Script principal de démarrage
    $start_script = "#!/bin/bash\n";
    $start_script .= "# Script de démarrage IntraSphere React\n";
    $start_script .= "# Généré pour: " . $config['hosting_type'] . "\n\n";
    
    switch ($config['hosting_type']) {
        case 'replit':
            $start_script .= "echo \"🚀 Démarrage sur Replit...\"\n";
            $start_script .= "npm install\n";
            $start_script .= "npm run dev\n";
            break;
            
        case 'cpanel':
            $start_script .= "echo \"🚀 Démarrage sur cPanel...\"\n";
            $start_script .= "cd public_html\n";
            $start_script .= "npm install --production\n";
            $start_script .= "npm run build\n";
            $start_script .= "npm start\n";
            break;
            
        case 'vps':
            $start_script .= "echo \"🚀 Démarrage sur VPS...\"\n";
            $start_script .= "npm install --production\n";
            $start_script .= "npm run build\n";
            if ($config['use_pm2']) {
                $start_script .= "pm2 start ecosystem.config.js\n";
            } else {
                $start_script .= "npm start\n";
            }
            break;
            
        default:
            $start_script .= "echo \"🚀 Démarrage générique...\"\n";
            $start_script .= "npm install\n";
            $start_script .= "npm run build\n";
            $start_script .= "npm start\n";
    }
    
    $scripts['start.sh'] = $start_script;
    
    // Configuration PM2 pour VPS
    if ($config['hosting_type'] === 'vps' && $config['use_pm2']) {
        $pm2_config = "module.exports = {\n";
        $pm2_config .= "  apps: [{\n";
        $pm2_config .= "    name: 'intrasphere-react',\n";
        $pm2_config .= "    script: 'npm',\n";
        $pm2_config .= "    args: 'start',\n";
        $pm2_config .= "    instances: 'max',\n";
        $pm2_config .= "    exec_mode: 'cluster',\n";
        $pm2_config .= "    env: {\n";
        $pm2_config .= "      NODE_ENV: '" . $config['environment'] . "',\n";
        $pm2_config .= "      PORT: " . $config['port'] . "\n";
        $pm2_config .= "    },\n";
        $pm2_config .= "    error_file: './logs/err.log',\n";
        $pm2_config .= "    out_file: './logs/out.log',\n";
        $pm2_config .= "    log_file: './logs/combined.log',\n";
        $pm2_config .= "    time: true\n";
        $pm2_config .= "  }]\n";
        $pm2_config .= "};\n";
        
        $scripts['ecosystem.config.js'] = $pm2_config;
    }
    
    // Configuration Nginx pour VPS
    if ($config['hosting_type'] === 'vps' && !empty($config['domain'])) {
        $nginx_config = "server {\n";
        $nginx_config .= "    listen 80;\n";
        $nginx_config .= "    server_name " . $config['domain'] . ";\n";
        $nginx_config .= "    return 301 https://\$server_name\$request_uri;\n";
        $nginx_config .= "}\n\n";
        $nginx_config .= "server {\n";
        $nginx_config .= "    listen 443 ssl http2;\n";
        $nginx_config .= "    server_name " . $config['domain'] . ";\n\n";
        $nginx_config .= "    # SSL Configuration (à adapter)\n";
        $nginx_config .= "    ssl_certificate /etc/ssl/certs/" . $config['domain'] . ".crt;\n";
        $nginx_config .= "    ssl_certificate_key /etc/ssl/private/" . $config['domain'] . ".key;\n\n";
        $nginx_config .= "    location / {\n";
        $nginx_config .= "        proxy_pass http://localhost:" . $config['port'] . ";\n";
        $nginx_config .= "        proxy_http_version 1.1;\n";
        $nginx_config .= "        proxy_set_header Upgrade \$http_upgrade;\n";
        $nginx_config .= "        proxy_set_header Connection 'upgrade';\n";
        $nginx_config .= "        proxy_set_header Host \$host;\n";
        $nginx_config .= "        proxy_set_header X-Real-IP \$remote_addr;\n";
        $nginx_config .= "        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;\n";
        $nginx_config .= "        proxy_set_header X-Forwarded-Proto \$scheme;\n";
        $nginx_config .= "        proxy_cache_bypass \$http_upgrade;\n";
        $nginx_config .= "    }\n";
        $nginx_config .= "}\n";
        
        $scripts['nginx.conf'] = $nginx_config;
    }
    
    return $scripts;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration IntraSphere React</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
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
        .card-body { padding: 25px; }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #374151;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #8B5CF6;
        }
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .form-check input {
            margin-right: 10px;
        }
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
        .btn:hover { transform: translateY(-2px); }
        .btn.secondary { background: #6B7280; }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .alert.success { background: #D1FAE5; border-left: 4px solid #10B981; }
        .alert.info { background: #DBEAFE; border-left: 4px solid #3B82F6; }
        .alert.warning { background: #FEF3C7; border-left: 4px solid #F59E0B; }
        .code-block {
            background: #1a1a1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            overflow-x: auto;
            font-size: 0.9em;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .environment-card {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .environment-card:hover, .environment-card.selected {
            border-color: #8B5CF6;
            background: #f8f9ff;
        }
        .environment-card h4 {
            color: #8B5CF6;
            margin-bottom: 8px;
        }
        .small { font-size: 0.85em; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧙‍♂️ Assistant de Configuration</h1>
            <p>Configuration automatique d'IntraSphere React</p>
        </div>

        <?php
        $step = $_GET['step'] ?? $_POST['step'] ?? 'detect';
        
        switch ($step) {
            case 'detect':
                showEnvironmentDetection();
                break;
            case 'configure':
                showConfigurationForm();
                break;
            case 'generate':
                showConfigurationGeneration();
                break;
            case 'complete':
                showCompletionPage();
                break;
            default:
                showEnvironmentDetection();
        }

        function showEnvironmentDetection() {
            $env = detectHostingEnvironment();
            ?>
            <div class="card">
                <div class="card-header">
                    🔍 Détection de l'Environnement
                </div>
                <div class="card-body">
                    <div class="alert info">
                        <strong>Environnement détecté :</strong> <?= $env['name'] ?> (<?= $env['type'] ?>)
                    </div>
                    
                    <?php if (!empty($env['features'])): ?>
                    <h3>Fonctionnalités disponibles :</h3>
                    <ul>
                        <?php foreach ($env['features'] as $feature): ?>
                        <li><?= $feature ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    
                    <?php if (!empty($env['recommendations'])): ?>
                    <h3>Recommandations :</h3>
                    <ul>
                        <?php foreach ($env['recommendations'] as $rec): ?>
                        <li><?= $rec ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    
                    <form method="get">
                        <input type="hidden" name="step" value="configure">
                        <input type="hidden" name="detected_env" value="<?= $env['type'] ?>">
                        <button type="submit" class="btn">Configurer pour cet environnement →</button>
                    </form>
                </div>
            </div>
            <?php
        }

        function showConfigurationForm() {
            $detected_env = $_GET['detected_env'] ?? 'unknown';
            ?>
            <div class="card">
                <div class="card-header">
                    ⚙️ Configuration de l'Application
                </div>
                <div class="card-body">
                    <form method="post" action="?step=generate">
                        <input type="hidden" name="hosting_type" value="<?= $detected_env ?>">
                        
                        <div class="form-group">
                            <label>Environnement d'exécution</label>
                            <div class="grid">
                                <div class="environment-card" onclick="selectEnvironment('development')">
                                    <h4>🛠️ Développement</h4>
                                    <p>Hot reload, debugging activé</p>
                                    <div class="small">Recommandé pour le développement local</div>
                                </div>
                                <div class="environment-card" onclick="selectEnvironment('production')">
                                    <h4>🚀 Production</h4>
                                    <p>Optimisé, sécurisé, performant</p>
                                    <div class="small">Pour déploiement public</div>
                                </div>
                                <div class="environment-card" onclick="selectEnvironment('staging')">
                                    <h4>🧪 Staging</h4>
                                    <p>Tests en conditions réelles</p>
                                    <div class="small">Environnement de test</div>
                                </div>
                            </div>
                            <input type="hidden" name="environment" id="environment" value="development">
                        </div>

                        <div class="form-group">
                            <label for="port">Port d'application</label>
                            <input type="number" name="port" id="port" value="5000" class="form-control" min="1000" max="65535">
                            <div class="small">Port sur lequel l'application sera accessible</div>
                        </div>

                        <div class="form-group">
                            <label for="database_url">URL de la base de données (optionnel)</label>
                            <input type="text" name="database_url" id="database_url" class="form-control" 
                                   placeholder="postgresql://user:password@host:port/database">
                            <div class="small">Laissez vide pour utiliser la base par défaut</div>
                        </div>

                        <?php if ($detected_env === 'cpanel' || $detected_env === 'vps'): ?>
                        <div class="form-group">
                            <label for="domain">Nom de domaine</label>
                            <input type="text" name="domain" id="domain" class="form-control" 
                                   placeholder="example.com">
                            <div class="small">Domaine principal de votre application</div>
                        </div>
                        <?php endif; ?>

                        <?php if ($detected_env === 'vps'): ?>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="use_pm2" id="use_pm2" checked>
                                <label for="use_pm2">Utiliser PM2 (gestionnaire de processus)</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="setup_nginx" id="setup_nginx">
                                <label for="setup_nginx">Générer la configuration Nginx</label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="cors_origin">Origine CORS (Production)</label>
                            <input type="text" name="cors_origin" id="cors_origin" class="form-control" 
                                   placeholder="https://votre-domaine.com ou * pour tout autoriser">
                            <div class="small">Contrôle l'accès cross-origin pour la sécurité</div>
                        </div>

                        <div class="form-group">
                            <label for="session_secret">Clé secrète de session</label>
                            <input type="text" name="session_secret" id="session_secret" class="form-control" 
                                   value="<?= bin2hex(random_bytes(32)) ?>" readonly>
                            <div class="small">Clé générée automatiquement pour sécuriser les sessions</div>
                        </div>

                        <button type="submit" class="btn">Générer la Configuration →</button>
                    </form>

                    <script>
                    function selectEnvironment(env) {
                        document.querySelectorAll('.environment-card').forEach(card => {
                            card.classList.remove('selected');
                        });
                        event.target.closest('.environment-card').classList.add('selected');
                        document.getElementById('environment').value = env;
                    }
                    
                    // Sélection par défaut
                    document.querySelector('.environment-card').classList.add('selected');
                    </script>
                </div>
            </div>
            <?php
        }

        function showConfigurationGeneration() {
            if ($_POST) {
                $config = $_POST;
                $env_content = generateEnvFile($config);
                $scripts = generateDeploymentScripts($config);
                
                // Sauvegarde des fichiers
                file_put_contents('.env', $env_content);
                foreach ($scripts as $filename => $content) {
                    file_put_contents($filename, $content);
                    if (pathinfo($filename, PATHINFO_EXTENSION) === 'sh') {
                        chmod($filename, 0755);
                    }
                }
                ?>
                <div class="card">
                    <div class="card-header">
                        ✅ Configuration Générée
                    </div>
                    <div class="card-body">
                        <div class="alert success">
                            Les fichiers de configuration ont été générés avec succès !
                        </div>

                        <h3>Fichiers créés :</h3>
                        <ul>
                            <li><strong>.env</strong> - Variables d'environnement</li>
                            <?php foreach (array_keys($scripts) as $filename): ?>
                            <li><strong><?= $filename ?></strong> - <?= getFileDescription($filename) ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <h3>Fichier .env généré :</h3>
                        <div class="code-block"><?= htmlspecialchars($env_content) ?></div>

                        <?php if (isset($scripts['start.sh'])): ?>
                        <h3>Script de démarrage :</h3>
                        <div class="code-block"><?= htmlspecialchars($scripts['start.sh']) ?></div>
                        <?php endif; ?>

                        <h3>Prochaines étapes :</h3>
                        <ol>
                            <li>Vérifiez et ajustez les variables dans le fichier .env si nécessaire</li>
                            <li>Installez les dépendances : <code>npm install</code></li>
                            <li>Démarrez l'application : <code>./start.sh</code> ou <code>npm run dev</code></li>
                            <?php if ($config['environment'] === 'production'): ?>
                            <li>Configurez votre serveur web (Nginx/Apache) si nécessaire</li>
                            <li>Activez HTTPS avec un certificat SSL</li>
                            <?php endif; ?>
                        </ol>

                        <a href="?step=complete" class="btn">Terminer la Configuration →</a>
                    </div>
                </div>
                <?php
            }
        }

        function showCompletionPage() {
            ?>
            <div class="card">
                <div class="card-header">
                    🎉 Configuration Terminée !
                </div>
                <div class="card-body">
                    <div class="alert success">
                        IntraSphere React est maintenant configuré et prêt à être utilisé !
                    </div>

                    <h3>🚀 Commandes de démarrage :</h3>
                    <div class="code-block">
# Développement
npm run dev

# Production
npm run build && npm start

# Avec PM2 (si configuré)
pm2 start ecosystem.config.js
                    </div>

                    <h3>🌐 Accès à l'application :</h3>
                    <p>Une fois démarrée, votre application sera accessible sur :</p>
                    <ul>
                        <li><strong>Local :</strong> <a href="http://localhost:5000" target="_blank">http://localhost:5000</a></li>
                        <li><strong>Réseau :</strong> http://votre-ip:5000</li>
                        <li><strong>Domaine :</strong> https://votre-domaine.com (si configuré)</li>
                    </ul>

                    <h3>👤 Identifiants par défaut :</h3>
                    <div class="alert warning">
                        <strong>Utilisateur :</strong> admin<br>
                        <strong>Mot de passe :</strong> admin<br>
                        ⚠️ <strong>Changez ces identifiants immédiatement après la première connexion !</strong>
                    </div>

                    <h3>📚 Documentation utile :</h3>
                    <ul>
                        <li><a href="README.md" target="_blank">README.md</a> - Guide d'utilisation</li>
                        <li><a href="replit.md" target="_blank">replit.md</a> - Documentation technique</li>
                        <li><a href="VERIFICATION-COMPLETE-REACT-PLUG-PLAY.md" target="_blank">Rapport de vérification</a></li>
                    </ul>

                    <h3>🔧 Support technique :</h3>
                    <p>En cas de problème, consultez :</p>
                    <ul>
                        <li>Les logs de l'application (dossier logs/)</li>
                        <li>La documentation technique</li>
                        <li>Les issues GitHub du projet</li>
                    </ul>

                    <div style="margin-top: 30px;">
                        <a href="http://localhost:5000" class="btn" target="_blank">Ouvrir l'Application →</a>
                        <a href="?" class="btn secondary">Nouvelle Configuration</a>
                    </div>
                </div>
            </div>
            <?php
        }

        function getFileDescription($filename) {
            $descriptions = [
                'start.sh' => 'Script de démarrage automatique',
                'ecosystem.config.js' => 'Configuration PM2 pour clustering',
                'nginx.conf' => 'Configuration Nginx pour reverse proxy',
                'docker-compose.yml' => 'Configuration Docker Compose'
            ];
            return $descriptions[$filename] ?? 'Fichier de configuration';
        }
        ?>
    </div>
</body>
</html>