<?php
/**
 * 🚀 IntraSphere React - Page d'Accueil d'Installation
 * 
 * Point d'entrée principal pour l'installation automatique
 * Redirige automatiquement vers la meilleure option d'installation
 */

// Détection de l'état de l'application
$hasNodeJS = shell_exec('node --version 2>/dev/null') !== null;
$isConfigured = file_exists('.env') && file_exists('node_modules');
$isRunning = false;

if ($hasNodeJS) {
    $isRunning = @file_get_contents('http://localhost:5000/api/stats') !== false;
}

// Redirection intelligente
if ($isRunning) {
    // Application déjà en marche - redirection directe
    header('Location: http://localhost:5000');
    exit;
} elseif (isset($_GET['mode'])) {
    // Mode spécifique demandé
    switch ($_GET['mode']) {
        case 'quick':
            header('Location: quick-start-react.php');
            exit;
        case 'full':
            header('Location: deploy-react-universal.php');
            exit;
        case 'config':
            header('Location: config-wizard-react.php');
            exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 IntraSphere React - Installation Automatique</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            color: white;
            position: relative;
        }
        .hero-content {
            max-width: 800px;
            padding: 40px;
        }
        .hero h1 {
            font-size: 4em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hero .subtitle {
            font-size: 1.5em;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .installation-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        .option-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            color: #333;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border: 3px solid transparent;
        }
        .option-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border-color: #8B5CF6;
        }
        .option-card .icon {
            font-size: 3em;
            margin-bottom: 20px;
        }
        .option-card h3 {
            color: #8B5CF6;
            font-size: 1.5em;
            margin-bottom: 15px;
        }
        .option-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .option-card .features {
            text-align: left;
            font-size: 0.9em;
            color: #888;
        }
        .option-card .features li {
            margin-bottom: 5px;
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
        .status-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 0.9em;
        }
        .status-bar.success { background: rgba(16, 185, 129, 0.9); }
        .status-bar.warning { background: rgba(245, 158, 11, 0.9); }
        .status-bar.error { background: rgba(239, 68, 68, 0.9); }
        .footer {
            text-align: center;
            padding: 30px;
            background: rgba(0,0,0,0.1);
            color: white;
            margin-top: 50px;
        }
        .footer a {
            color: #A78BFA;
            text-decoration: none;
            margin: 0 15px;
        }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php if ($isRunning): ?>
    <div class="status-bar success">
        ✅ IntraSphere React fonctionne déjà ! Redirection automatique vers l'application...
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'http://localhost:5000';
        }, 3000);
    </script>
    
    <?php elseif ($isConfigured): ?>
    <div class="status-bar warning">
        ⚡ Application configurée mais non démarrée. Utilisez le démarrage rapide.
    </div>
    
    <?php elseif ($hasNodeJS): ?>
    <div class="status-bar warning">
        ⚙️ Node.js détecté. Installation des dépendances requise.
    </div>
    
    <?php else: ?>
    <div class="status-bar error">
        ❌ Node.js non installé. Installation complète recommandée.
    </div>
    <?php endif; ?>

    <div class="hero">
        <div class="hero-content">
            <h1>🚀 IntraSphere React</h1>
            <p class="subtitle">Plateforme Intranet Moderne - Installation Automatique</p>
            <p>Choisissez votre méthode d'installation préférée :</p>

            <div class="installation-options">
                <!-- Démarrage Ultra-Rapide -->
                <div class="option-card" onclick="window.location.href='quick-start-react.php'">
                    <div class="icon">⚡</div>
                    <h3>Démarrage Express</h3>
                    <p>Installation et démarrage automatique en un clic</p>
                    <ul class="features">
                        <li>✅ Configuration automatique</li>
                        <li>✅ Démarrage immédiat</li>
                        <li>✅ Idéal pour tests rapides</li>
                        <li>⏱️ Prêt en 2 minutes</li>
                    </ul>
                    <div style="margin-top: 20px;">
                        <span class="btn">Démarrage Express →</span>
                    </div>
                </div>

                <!-- Installation Complète -->
                <div class="option-card" onclick="window.location.href='deploy-react-universal.php'">
                    <div class="icon">🛠️</div>
                    <h3>Installation Complète</h3>
                    <p>Assistant d'installation guidé pour tous environnements</p>
                    <ul class="features">
                        <li>✅ Détection environnement automatique</li>
                        <li>✅ Support 8 plateformes</li>
                        <li>✅ Configuration optimisée</li>
                        <li>⏱️ Installation en 5 minutes</li>
                    </ul>
                    <div style="margin-top: 20px;">
                        <span class="btn">Installation Guidée →</span>
                    </div>
                </div>

                <!-- Configuration Avancée -->
                <div class="option-card" onclick="window.location.href='config-wizard-react.php'">
                    <div class="icon">⚙️</div>
                    <h3>Configuration Avancée</h3>
                    <p>Paramétrage personnalisé pour utilisateurs experts</p>
                    <ul class="features">
                        <li>✅ Options de sécurité avancées</li>
                        <li>✅ Variables environnement custom</li>
                        <li>✅ Configuration serveur</li>
                        <li>⏱️ Configuration en 10 minutes</li>
                    </ul>
                    <div style="margin-top: 20px;">
                        <span class="btn">Configuration Expert →</span>
                    </div>
                </div>
            </div>

            <?php if ($isConfigured): ?>
            <div style="margin-top: 40px; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 15px;">
                <h3>🎯 Application Déjà Configurée</h3>
                <p>Votre application est prête ! Utilisez le démarrage express pour la lancer immédiatement.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <strong>📚 Documentation & Ressources :</strong><br>
        <a href="README.md" target="_blank">Guide d'Utilisation</a>
        <a href="INSTALLATION-REACT-GUIDE.md" target="_blank">Guide Installation Complet</a>
        <a href="VERIFICATION-COMPLETE-REACT-PLUG-PLAY.md" target="_blank">Rapport de Validation</a>
        <a href="replit.md" target="_blank">Documentation Technique</a>
    </div>

    <script>
    // Auto-détection et recommandation intelligente
    <?php if (!$isRunning): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.option-card');
        
        // Mise en évidence de l'option recommandée
        <?php if ($isConfigured): ?>
        // Application configurée -> recommander démarrage express
        cards[0].style.border = '3px solid #10B981';
        cards[0].style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
        <?php elseif ($hasNodeJS): ?>
        // Node.js installé -> recommander installation complète
        cards[1].style.border = '3px solid #8B5CF6';
        cards[1].style.boxShadow = '0 0 20px rgba(139, 92, 246, 0.3)';
        <?php else: ?>
        // Rien d'installé -> recommander installation complète
        cards[1].style.border = '3px solid #F59E0B';
        cards[1].style.boxShadow = '0 0 20px rgba(245, 158, 11, 0.3)';
        <?php endif; ?>
    });
    <?php endif; ?>

    // Raccourcis clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === '1') window.location.href = 'quick-start-react.php';
        if (e.key === '2') window.location.href = 'deploy-react-universal.php';
        if (e.key === '3') window.location.href = 'config-wizard-react.php';
    });
    </script>
</body>
</html>