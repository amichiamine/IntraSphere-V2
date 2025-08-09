<?php
/**
 * 🚀 IntraSphere React - Démarrage Ultra-Rapide
 * 
 * Page de démarrage immédiat en un clic
 * Aucune configuration requise
 */

// Détection automatique si l'app est déjà configurée
$isConfigured = file_exists('.env') && file_exists('node_modules');
$hasNodeJS = shell_exec('node --version 2>/dev/null') !== null;
$isRunning = false;

// Test si l'application tourne déjà
if ($hasNodeJS) {
    $isRunning = @file_get_contents('http://localhost:5000') !== false;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 IntraSphere React - Démarrage Express</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        .container {
            max-width: 600px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }
        .logo {
            font-size: 4em;
            margin-bottom: 20px;
        }
        h1 {
            color: #8B5CF6;
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 1.2em;
            margin-bottom: 30px;
        }
        .status {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            margin: 10px;
            font-size: 1.1em;
        }
        .status.success { background: #D1FAE5; color: #065F46; }
        .status.warning { background: #FEF3C7; color: #92400E; }
        .status.error { background: #FEE2E2; color: #991B1B; }
        .btn {
            background: linear-gradient(135deg, #8B5CF6, #A78BFA);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.2em;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn.secondary { background: #6B7280; }
        .btn.success { background: #10B981; }
        .quick-links {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }
        .quick-links a {
            display: inline-block;
            margin: 5px 10px;
            color: #8B5CF6;
            text-decoration: none;
            font-weight: 500;
        }
        .quick-links a:hover { text-decoration: underline; }
        .command-box {
            background: #1a1a1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 20px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🚀</div>
        <h1>IntraSphere React</h1>
        <p class="subtitle">Plateforme Intranet Moderne</p>

        <?php if ($isRunning): ?>
            <!-- Application déjà en cours d'exécution -->
            <div class="status success">✅ Application en ligne !</div>
            <p>Votre application IntraSphere fonctionne parfaitement.</p>
            <a href="http://localhost:5000" class="btn success" target="_blank">Ouvrir l'Application →</a>
            
        <?php elseif ($isConfigured && $hasNodeJS): ?>
            <!-- Configuré mais pas démarré -->
            <div class="status warning">⚡ Prêt à démarrer</div>
            <p>Votre application est configurée. Cliquez pour la démarrer !</p>
            <button onclick="startApp()" class="btn" id="startBtn">Démarrer l'Application</button>
            
            <div class="command-box">
$ npm run dev<br>
<span id="output">Cliquez sur "Démarrer" pour voir les logs...</span>
            </div>
            
        <?php elseif ($hasNodeJS): ?>
            <!-- Node.js installé mais pas configuré -->
            <div class="status warning">⚙️ Configuration requise</div>
            <p>Node.js détecté. Installez les dépendances pour continuer.</p>
            <button onclick="installApp()" class="btn" id="installBtn">Installer les Dépendances</button>
            
            <div class="command-box">
$ npm install<br>
<span id="output">Cliquez sur "Installer" pour commencer...</span>
            </div>
            
        <?php else: ?>
            <!-- Node.js non installé -->
            <div class="status error">❌ Node.js requis</div>
            <p>Node.js n'est pas installé. Choisissez votre méthode d'installation :</p>
            
            <a href="deploy-react-universal.php" class="btn">Assistant d'Installation Complet</a>
            <button onclick="installNodeJS()" class="btn secondary">Installation Node.js</button>
            
            <div class="command-box">
# Automatique (recommandé)<br>
./install-nodejs.sh<br><br>
# Manuel<br>
# Visitez nodejs.org
            </div>
        <?php endif; ?>

        <div class="quick-links">
            <strong>Liens Rapides :</strong><br>
            <a href="deploy-react-universal.php">Installation Complète</a> |
            <a href="config-wizard-react.php">Configuration Avancée</a> |
            <a href="README.md" target="_blank">Documentation</a> |
            <a href="INSTALLATION-REACT-GUIDE.md" target="_blank">Guide Installation</a>
        </div>

        <?php if ($isConfigured): ?>
        <div class="quick-links">
            <strong>Identifiants par défaut :</strong><br>
            Utilisateur: <code>admin</code> | Mot de passe: <code>admin</code>
        </div>
        <?php endif; ?>
    </div>

    <script>
    async function startApp() {
        const btn = document.getElementById('startBtn');
        const output = document.getElementById('output');
        
        btn.disabled = true;
        btn.textContent = 'Démarrage en cours...';
        output.innerHTML = 'Démarrage du serveur de développement...<br>';
        
        try {
            const response = await fetch('quick-start-react.php?action=start', {
                method: 'POST'
            });
            
            if (response.ok) {
                output.innerHTML += '✅ Serveur démarré !<br>';
                output.innerHTML += '🌐 Application disponible sur http://localhost:5000<br>';
                
                // Attendre 3 secondes puis ouvrir l'app
                setTimeout(() => {
                    window.open('http://localhost:5000', '_blank');
                }, 3000);
                
                btn.textContent = 'Application Démarrée ✅';
                btn.className = 'btn success';
            } else {
                throw new Error('Erreur de démarrage');
            }
        } catch (error) {
            output.innerHTML += '❌ Erreur: ' + error.message + '<br>';
            output.innerHTML += '💡 Essayez manuellement: npm run dev<br>';
            btn.disabled = false;
            btn.textContent = 'Réessayer';
        }
    }

    async function installApp() {
        const btn = document.getElementById('installBtn');
        const output = document.getElementById('output');
        
        btn.disabled = true;
        btn.textContent = 'Installation en cours...';
        output.innerHTML = 'Installation des dépendances...<br>';
        
        try {
            const response = await fetch('quick-start-react.php?action=install', {
                method: 'POST'
            });
            
            if (response.ok) {
                output.innerHTML += '✅ Dépendances installées !<br>';
                output.innerHTML += '⚡ Prêt à démarrer l\'application<br>';
                
                btn.textContent = 'Démarrer l\'Application';
                btn.onclick = startApp;
                btn.disabled = false;
            } else {
                throw new Error('Erreur d\'installation');
            }
        } catch (error) {
            output.innerHTML += '❌ Erreur: ' + error.message + '<br>';
            output.innerHTML += '💡 Essayez manuellement: npm install<br>';
            btn.disabled = false;
            btn.textContent = 'Réessayer';
        }
    }

    function installNodeJS() {
        window.location.href = 'deploy-react-universal.php';
    }

    // Auto-refresh pour détecter les changements
    <?php if (!$isRunning && $hasNodeJS): ?>
    setTimeout(() => {
        location.reload();
    }, 30000); // Refresh toutes les 30 secondes
    <?php endif; ?>
    </script>
</body>
</html>

<?php
// Actions AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'install':
            // Installation des dépendances
            $output = shell_exec('npm install 2>&1');
            if (strpos($output, 'error') === false) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'output' => $output]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'output' => $output]);
            }
            break;
            
        case 'start':
            // Démarrage de l'application en arrière-plan
            if (PHP_OS_FAMILY === 'Windows') {
                pclose(popen('start /B npm run dev', 'r'));
            } else {
                shell_exec('nohup npm run dev > /dev/null 2>&1 &');
            }
            
            // Attendre que le serveur soit prêt
            $timeout = 30;
            $started = false;
            for ($i = 0; $i < $timeout; $i++) {
                sleep(1);
                if (@file_get_contents('http://localhost:5000') !== false) {
                    $started = true;
                    break;
                }
            }
            
            if ($started) {
                http_response_code(200);
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Timeout']);
            }
            break;
    }
    exit;
}
?>