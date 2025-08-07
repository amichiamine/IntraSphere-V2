<?php
/**
 * Script d'installation de l'API PHP IntraSphere
 * À exécuter une seule fois lors du déploiement
 */

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    try {
        require_once 'config/database.php';
        
        $db = new Database();
        $conn = $db->getConnection();
        
        // Détecter le type de base de données
        $driverName = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        if ($driverName === 'mysql') {
            echo "<h3>🔄 Installation base de données MySQL...</h3>";
            $db->createMySQLTables();
            echo "<p>✅ Tables MySQL créées avec succès</p>";
        } else {
            echo "<h3>🔄 Installation base de données SQLite...</h3>";
            // Les tables SQLite sont créées automatiquement dans le constructeur
            echo "<p>✅ Tables SQLite créées avec succès</p>";
        }
        
        echo "<h3>✅ Installation terminée !</h3>";
        echo "<p><strong>Utilisateur administrateur créé :</strong></p>";
        echo "<ul>";
        echo "<li><strong>Nom d'utilisateur :</strong> admin</li>";
        echo "<li><strong>Mot de passe :</strong> admin123</li>";
        echo "</ul>";
        echo "<p><em>⚠️ Changez ce mot de passe après la première connexion !</em></p>";
        
        echo "<h3>🌐 Test de l'API</h3>";
        echo "<p><a href='stats.php' target='_blank'>Tester /api/stats</a></p>";
        
        echo "<p><strong>L'API IntraSphere est maintenant opérationnelle !</strong></p>";
        echo "<p><a href='../'>← Retour à l'application</a></p>";
        
    } catch (Exception $e) {
        echo "<h3>❌ Erreur d'installation</h3>";
        echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Vérifiez la configuration de votre base de données.</p>";
    }
} else {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation API IntraSphere</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            max-width: 800px; 
            margin: 2rem auto; 
            padding: 2rem; 
            line-height: 1.6; 
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header { 
            text-align: center; 
            margin-bottom: 2rem; 
            color: #4a5568;
        }
        .logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .card { 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            padding: 1.5rem; 
            margin-bottom: 1.5rem; 
            background: #f8fafc;
        }
        .btn { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 1rem;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn:hover { 
            transform: translateY(-2px);
        }
        .info { 
            background: #e3f2fd; 
            border-left: 4px solid #2196f3; 
            padding: 1rem; 
            margin: 1rem 0;
        }
        .warning { 
            background: #fff3e0; 
            border-left: 4px solid #ff9800; 
            padding: 1rem; 
            margin: 1rem 0;
        }
        ul { 
            margin: 1rem 0; 
        }
        li { 
            margin: 0.5rem 0; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🚀</div>
            <h1>Installation API IntraSphere</h1>
            <p>Configuration de l'API PHP pour hébergement traditionnel</p>
        </div>
        
        <div class="card">
            <h3>📋 Que va faire cette installation ?</h3>
            <ul>
                <li>✅ Créer les tables de base de données (MySQL ou SQLite)</li>
                <li>✅ Configurer l'utilisateur administrateur par défaut</li>
                <li>✅ Insérer les données de démonstration</li>
                <li>✅ Tester la connectivité API</li>
            </ul>
        </div>
        
        <div class="info">
            <h4>🔧 Configuration automatique</h4>
            <p>L'installation détectera automatiquement votre environnement :</p>
            <ul>
                <li><strong>MySQL disponible</strong> → Utilisation MySQL</li>
                <li><strong>MySQL indisponible</strong> → Utilisation SQLite (fichier local)</li>
            </ul>
        </div>
        
        <div class="warning">
            <h4>⚠️ Important</h4>
            <ul>
                <li>Exécutez cette installation <strong>une seule fois</strong></li>
                <li>L'utilisateur admin créé a le mot de passe <strong>admin123</strong></li>
                <li>Changez ce mot de passe après la première connexion</li>
            </ul>
        </div>
        
        <div class="card">
            <h3>🌐 Après l'installation</h3>
            <p>L'API sera accessible via les routes suivantes :</p>
            <ul>
                <li><code>GET /api/stats</code> - Statistiques publiques</li>
                <li><code>POST /api/auth/login</code> - Authentification</li>
                <li><code>GET /api/announcements</code> - Liste des annonces</li>
                <li><code>GET /api/users</code> - Gestion des utilisateurs</li>
                <li>... et toutes les autres routes compatibles Express.js</li>
            </ul>
        </div>
        
        <form method="post" style="text-align: center;">
            <button type="submit" name="install" class="btn">
                🚀 Installer l'API IntraSphere
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 2rem; color: #666;">
            <p>IntraSphere v2.1 - API PHP Compatible</p>
        </div>
    </div>
</body>
</html>
<?php
}
?>