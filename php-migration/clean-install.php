<?php
/**
 * Script de nettoyage pour réinstallation IntraSphere
 * Supprime les fichiers d'installation et permet une réinstallation propre
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_clean'])) {
    $filesDeleted = [];
    $errors = [];
    
    // Supprimer le verrou d'installation
    if (file_exists(__DIR__ . '/.installed')) {
        if (unlink(__DIR__ . '/.installed')) {
            $filesDeleted[] = '.installed';
        } else {
            $errors[] = 'Impossible de supprimer .installed';
        }
    }
    
    // Supprimer le fichier .env
    if (file_exists(__DIR__ . '/.env')) {
        if (unlink(__DIR__ . '/.env')) {
            $filesDeleted[] = '.env';
        } else {
            $errors[] = 'Impossible de supprimer .env';
        }
    }
    
    // Optionnel : supprimer .htaccess
    if (isset($_POST['delete_htaccess']) && file_exists(__DIR__ . '/.htaccess')) {
        if (unlink(__DIR__ . '/.htaccess')) {
            $filesDeleted[] = '.htaccess';
        } else {
            $errors[] = 'Impossible de supprimer .htaccess';
        }
    }
    
    // Optionnel : nettoyer la base de données
    if (isset($_POST['clean_database']) && !empty($_POST['db_host']) && !empty($_POST['db_name'])) {
        try {
            $pdo = new PDO(
                "mysql:host={$_POST['db_host']};dbname={$_POST['db_name']}", 
                $_POST['db_user'], 
                $_POST['db_password']
            );
            
            // Tables à supprimer
            $tables = [
                'forum_user_stats', 'forum_likes', 'forum_posts', 'forum_topics', 'forum_categories',
                'certificates', 'quiz_attempts', 'lesson_progress', 'enrollments', 'resources',
                'quizzes', 'lessons', 'courses', 'training_participants', 'trainings',
                'user_permissions', 'permissions', 'complaints', 'messages', 'events',
                'documents', 'announcements', 'contents', 'categories', 'employee_categories',
                'system_settings', 'users'
            ];
            
            foreach ($tables as $table) {
                try {
                    $pdo->exec("DROP TABLE IF EXISTS {$table}");
                    $filesDeleted[] = "Table {$table}";
                } catch (PDOException $e) {
                    $errors[] = "Erreur suppression table {$table}: " . $e->getMessage();
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur connexion base de données: " . $e->getMessage();
        }
    }
    
    $cleanCompleted = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage IntraSphere</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding: 20px; background: #dc3545; color: white; border-radius: 8px; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 4px; margin: 20px 0; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .checkbox-group { margin: 15px 0; }
        .checkbox-group input { width: auto; margin-right: 10px; }
        .btn { padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 5px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!isset($cleanCompleted)): ?>
            <div class="header">
                <h1>🧹 Nettoyage IntraSphere</h1>
                <p>Suppression des fichiers d'installation pour réinstaller</p>
            </div>
            
            <div class="warning">
                <strong>⚠️ Attention !</strong><br>
                Cette opération va supprimer les fichiers de configuration et potentiellement la base de données.
                Assurez-vous d'avoir sauvegardé vos données importantes avant de continuer.
            </div>
            
            <form method="POST">
                <h3>Options de nettoyage</h3>
                
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="delete_htaccess">
                        Supprimer le fichier .htaccess
                    </label>
                </div>
                
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="clean_database" id="clean_db" onchange="toggleDbFields()">
                        Nettoyer complètement la base de données (supprime toutes les tables)
                    </label>
                </div>
                
                <div id="db_fields" style="display: none; border: 1px solid #ddd; padding: 15px; border-radius: 4px; margin: 15px 0;">
                    <h4>Informations base de données</h4>
                    <div class="form-group">
                        <label>Serveur</label>
                        <input type="text" name="db_host" value="localhost">
                    </div>
                    <div class="form-group">
                        <label>Base de données</label>
                        <input type="text" name="db_name">
                    </div>
                    <div class="form-group">
                        <label>Utilisateur</label>
                        <input type="text" name="db_user">
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="db_password">
                    </div>
                </div>
                
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="confirm_clean" required>
                        <strong>Je confirme vouloir nettoyer IntraSphere</strong>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-danger">🧹 Nettoyer et Permettre Réinstallation</button>
                <a href="install-simple.php" class="btn btn-secondary">← Retour à l'installation</a>
            </form>
            
        <?php else: ?>
            <div class="header">
                <h1>✅ Nettoyage Terminé</h1>
                <p>IntraSphere est prêt pour une nouvelle installation</p>
            </div>
            
            <?php if (!empty($filesDeleted)): ?>
                <div class="success">
                    <h3>Éléments supprimés avec succès :</h3>
                    <ul>
                        <?php foreach ($filesDeleted as $file): ?>
                            <li><?= htmlspecialchars($file) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <h3>Erreurs rencontrées :</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <p>Vous pouvez maintenant procéder à une nouvelle installation.</p>
            
            <a href="install-simple.php" class="btn btn-danger">🚀 Nouvelle Installation</a>
        <?php endif; ?>
    </div>
    
    <script>
        function toggleDbFields() {
            const checkbox = document.getElementById('clean_db');
            const fields = document.getElementById('db_fields');
            fields.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
</body>
</html>