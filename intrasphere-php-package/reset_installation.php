<?php
/**
 * Script de réinitialisation de l'installation IntraSphere
 * Utilise ce script pour recommencer l'installation depuis le début
 */

// Sécurité : Ce script ne doit être accessible qu'aux administrateurs
session_start();

// Vérifier l'authentification ou demander confirmation
$confirmReset = $_POST['confirm_reset'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $confirmReset === 'yes') {
    try {
        // Supprimer le fichier d'installation complétée
        if (file_exists(__DIR__ . '/config/installed.php')) {
            unlink(__DIR__ . '/config/installed.php');
        }
        
        // Supprimer le fichier .env
        if (file_exists(__DIR__ . '/.env')) {
            unlink(__DIR__ . '/.env');
        }
        
        // Nettoyer la session
        session_destroy();
        
        // Restaurer le script d'installation
        if (file_exists(__DIR__ . '/install.php.bak')) {
            rename(__DIR__ . '/install.php.bak', __DIR__ . '/install.php');
        }
        
        $success = true;
        
    } catch (Exception $e) {
        $error = "Erreur lors de la réinitialisation: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation - IntraSphere</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .reset-container { 
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 40px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 100%; max-width: 500px; text-align: center;
        }
        .logo { 
            font-size: 2rem; font-weight: 700; color: #8B5CF6; margin-bottom: 20px;
        }
        .warning { 
            background: #fef3c7; border: 1px solid #f59e0b; color: #92400e;
            padding: 20px; border-radius: 8px; margin: 20px 0; text-align: left;
        }
        .success { 
            background: #d1fae5; border: 1px solid #10b981; color: #065f46;
            padding: 20px; border-radius: 8px; margin: 20px 0;
        }
        .error { 
            background: #fee2e2; border: 1px solid #ef4444; color: #991b1b;
            padding: 20px; border-radius: 8px; margin: 20px 0;
        }
        .btn { 
            padding: 14px 28px; border: none; border-radius: 8px; font-size: 16px;
            font-weight: 600; cursor: pointer; margin: 10px; text-decoration: none;
            display: inline-block;
        }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-secondary:hover { background: #4b5563; }
        .btn-primary { background: #8B5CF6; color: white; }
        .btn-primary:hover { background: #7C3AED; }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo">🔄 Réinitialisation IntraSphere</div>
        
        <?php if (isset($success) && $success): ?>
            <div class="success">
                <h3>✅ Réinitialisation réussie !</h3>
                <p>L'installation a été réinitialisée avec succès. Vous pouvez maintenant recommencer le processus d'installation.</p>
            </div>
            <a href="install.php" class="btn btn-primary">Recommencer l'installation</a>
            
        <?php elseif (isset($error)): ?>
            <div class="error">
                <h3>❌ Erreur</h3>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
            <a href="reset_installation.php" class="btn btn-secondary">Réessayer</a>
            
        <?php else: ?>
            <h2>Réinitialiser l'installation</h2>
            <p>Cette action va supprimer la configuration actuelle et vous permettre de recommencer l'installation depuis le début.</p>
            
            <div class="warning">
                <h3>⚠️ Attention</h3>
                <p><strong>Cette action est irréversible !</strong></p>
                <ul style="margin: 10px 0; text-align: left;">
                    <li>La configuration actuelle sera supprimée</li>
                    <li>Les paramètres de base de données seront perdus</li>
                    <li>Les sessions utilisateur seront fermées</li>
                    <li>Vous devrez reconfigurer entièrement l'application</li>
                </ul>
                <p><strong>Note:</strong> Les données de la base de données ne seront PAS supprimées.</p>
            </div>
            
            <form method="POST">
                <input type="hidden" name="confirm_reset" value="yes">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser l\'installation ?')">
                    Confirmer la réinitialisation
                </button>
            </form>
            
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        <?php endif; ?>
    </div>
</body>
</html>