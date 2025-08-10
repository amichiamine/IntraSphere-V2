<?php
/**
 * Script de réinitialisation IntraSphere PHP
 * Nettoie complètement une installation pour repartir à zéro
 * Version : 1.0.0
 */

// Démarrage du buffer de sortie
ob_start();

// Configuration des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction de redirection sécurisée
function safeRedirect($url) {
    if (ob_get_level()) {
        ob_end_clean();
    }
    header('Location: ' . $url);
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation IntraSphere - Nettoyage Installation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            min-height: 100vh;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            text-align: center;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #dc2626;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .warning-box {
            background: #fef2f2;
            border: 2px solid #fecaca;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            color: #991b1b;
        }
        
        .warning-box h3 {
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .warning-box ul {
            text-align: left;
            margin-left: 20px;
        }
        
        .warning-box li {
            margin: 8px 0;
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
            margin: 10px;
        }
        
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        
        .btn-danger:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .btn-success {
            background: #059669;
            color: white;
        }
        
        .btn-success:hover {
            background: #047857;
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
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
        
        .log-output {
            background: #1f2937;
            color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
            max-height: 400px;
            overflow-y: auto;
            margin: 20px 0;
            text-align: left;
        }
        
        .progress-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            text-align: left;
        }
        
        .progress-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .progress-icon.pending {
            background: #e5e7eb;
            color: #6b7280;
        }
        
        .progress-icon.running {
            background: #3b82f6;
            color: white;
        }
        
        .progress-icon.success {
            background: #059669;
            color: white;
        }
        
        .progress-icon.error {
            background: #dc2626;
            color: white;
        }
        
        .checkbox-group {
            text-align: left;
            margin: 20px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .checkbox-group label {
            display: block;
            margin: 10px 0;
            cursor: pointer;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .form-group {
            margin: 20px 0;
            text-align: left;
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
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>🧹 Réinitialisation</h1>
                <p>Nettoyage complet de l'installation IntraSphere</p>
            </div>
            
            <?php
            
            class IntraSphereReset {
                private $resetActions = [
                    'session' => 'Nettoyer les données de session',
                    'env' => 'Supprimer le fichier .env',
                    'htaccess' => 'Supprimer le fichier .htaccess',
                    'logs' => 'Vider les fichiers de logs',
                    'uploads' => 'Nettoyer le dossier uploads',
                    'database' => 'Supprimer les tables de base de données'
                ];
                
                public function showResetForm() {
                    ?>
                    <div class="warning-box">
                        <h3>⚠️ ATTENTION - Action Irréversible</h3>
                        <p>Cette opération va supprimer définitivement :</p>
                        <ul>
                            <li>Toutes les données de session d'installation</li>
                            <li>Les fichiers de configuration (.env, .htaccess)</li>
                            <li>Les logs d'installation</li>
                            <li>Les fichiers uploadés (optionnel)</li>
                            <li>Toutes les tables de base de données (optionnel)</li>
                        </ul>
                        <p><strong>Vous pourrez ensuite recommencer l'installation depuis le début.</strong></p>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="reset">
                        
                        <div class="checkbox-group">
                            <h4 style="margin-bottom: 15px;">Sélectionnez les éléments à nettoyer :</h4>
                            
                            <label>
                                <input type="checkbox" name="reset_items[]" value="session" checked>
                                <strong>Session d'installation</strong> - Remet l'installateur à l'étape 1
                            </label>
                            
                            <label>
                                <input type="checkbox" name="reset_items[]" value="env" checked>
                                <strong>Fichier de configuration (.env)</strong> - Supprime la configuration BDD
                            </label>
                            
                            <label>
                                <input type="checkbox" name="reset_items[]" value="htaccess">
                                <strong>Fichier .htaccess</strong> - Supprime les règles de sécurité
                            </label>
                            
                            <label>
                                <input type="checkbox" name="reset_items[]" value="logs">
                                <strong>Fichiers de logs</strong> - Vide tous les logs d'erreur
                            </label>
                            
                            <label>
                                <input type="checkbox" name="reset_items[]" value="uploads">
                                <strong>Dossier uploads</strong> - Supprime tous les fichiers uploadés
                            </label>
                            
                            <label>
                                <input type="checkbox" name="reset_items[]" value="database">
                                <strong>Tables de base de données</strong> - ⚠️ Supprime TOUTES les données !
                            </label>
                        </div>
                        
                        <div id="database-config" style="display: none;">
                            <h4>Configuration base de données (pour suppression des tables) :</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                                <div class="form-group">
                                    <label>Serveur :</label>
                                    <input type="text" name="db_host" value="localhost">
                                </div>
                                <div class="form-group">
                                    <label>Port :</label>
                                    <input type="text" name="db_port" value="3306">
                                </div>
                                <div class="form-group">
                                    <label>Base de données :</label>
                                    <input type="text" name="db_name" value="intrasphere">
                                </div>
                                <div class="form-group">
                                    <label>Utilisateur :</label>
                                    <input type="text" name="db_user" value="">
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label>Mot de passe :</label>
                                    <input type="password" name="db_password" value="">
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px;">
                            <button type="submit" class="btn btn-danger">
                                🧹 Lancer la Réinitialisation
                            </button>
                            <a href="install.php" class="btn btn-secondary">
                                ← Retour à l'installation
                            </a>
                        </div>
                    </form>
                    
                    <script>
                        // Afficher/masquer la config BDD
                        document.addEventListener('change', function(e) {
                            if (e.target.value === 'database' && e.target.type === 'checkbox') {
                                const configDiv = document.getElementById('database-config');
                                configDiv.style.display = e.target.checked ? 'block' : 'none';
                            }
                        });
                        
                        // Confirmation avant soumission
                        document.querySelector('form').addEventListener('submit', function(e) {
                            const checkedItems = document.querySelectorAll('input[name="reset_items[]"]:checked');
                            const items = Array.from(checkedItems).map(cb => cb.parentElement.textContent.trim()).join('\n- ');
                            
                            if (!confirm(`Êtes-vous sûr de vouloir réinitialiser :\n\n- ${items}\n\nCette action est IRRÉVERSIBLE !`)) {
                                e.preventDefault();
                            }
                        });
                    </script>
                    <?php
                }
                
                public function performReset() {
                    $resetItems = $_POST['reset_items'] ?? [];
                    $results = [];
                    $errors = [];
                    
                    ?>
                    <div class="log-output" id="resetLog">
                        <div style="color: #3b82f6; font-weight: bold; margin-bottom: 15px;">🧹 DÉMARRAGE DE LA RÉINITIALISATION</div>
                    <?php
                    
                    foreach ($resetItems as $item) {
                        echo "<div class='progress-item'>";
                        echo "<div class='progress-icon running'>⏳</div>";
                        echo "<span>Nettoyage : " . $this->resetActions[$item] . "</span>";
                        echo "</div>";
                        
                        $result = $this->executeResetAction($item);
                        
                        if ($result['success']) {
                            $results[] = $result['message'];
                            echo "<script>
                                const lastIcon = document.querySelector('.progress-icon.running:last-of-type');
                                if (lastIcon) {
                                    lastIcon.className = 'progress-icon success';
                                    lastIcon.innerHTML = '✓';
                                }
                            </script>";
                        } else {
                            $errors[] = $result['message'];
                            echo "<script>
                                const lastIcon = document.querySelector('.progress-icon.running:last-of-type');
                                if (lastIcon) {
                                    lastIcon.className = 'progress-icon error';
                                    lastIcon.innerHTML = '✗';
                                }
                            </script>";
                        }
                        
                        echo "<div style='color: " . ($result['success'] ? '#059669' : '#dc2626') . "; margin-left: 35px;'>";
                        echo ($result['success'] ? '✅ ' : '❌ ') . htmlspecialchars($result['message']);
                        echo "</div><br>";
                        
                        // Pause pour l'effet visuel
                        usleep(500000); // 0.5 secondes
                        if (ob_get_level()) {
                            ob_flush();
                            flush();
                        }
                    }
                    
                    ?>
                        <div style="color: #059669; font-weight: bold; margin-top: 20px;">
                            🎉 RÉINITIALISATION TERMINÉE
                        </div>
                    </div>
                    
                    <?php if (empty($errors)): ?>
                        <div class="alert alert-success">
                            <h3>✅ Réinitialisation réussie !</h3>
                            <p>L'installation a été nettoyée avec succès. Vous pouvez maintenant recommencer l'installation depuis le début.</p>
                        </div>
                        
                        <div style="text-align: center; margin-top: 30px;">
                            <a href="install.php" class="btn btn-success">
                                🚀 Recommencer l'Installation
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-error">
                            <h3>⚠️ Réinitialisation partielle</h3>
                            <p>Certains éléments n'ont pas pu être nettoyés :</p>
                            <ul style="margin-left: 20px; margin-top: 10px;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div style="text-align: center; margin-top: 30px;">
                            <a href="install.php" class="btn btn-success">
                                🚀 Essayer l'Installation Quand Même
                            </a>
                            <button onclick="window.location.reload()" class="btn btn-secondary">
                                🔄 Réessayer la Réinitialisation
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php
                }
                
                private function executeResetAction($action) {
                    switch ($action) {
                        case 'session':
                            return $this->resetSession();
                        
                        case 'env':
                            return $this->removeEnvFile();
                        
                        case 'htaccess':
                            return $this->removeHtaccessFile();
                        
                        case 'logs':
                            return $this->clearLogs();
                        
                        case 'uploads':
                            return $this->clearUploads();
                        
                        case 'database':
                            return $this->dropDatabaseTables();
                        
                        default:
                            return ['success' => false, 'message' => "Action inconnue : $action"];
                    }
                }
                
                private function resetSession() {
                    try {
                        // Nettoyer toutes les variables de session liées à l'installation
                        unset($_SESSION['install_step']);
                        unset($_SESSION['db_config']);
                        unset($_SESSION['hosting_type']);
                        unset($_SESSION['installation_progress']);
                        
                        // Optionnel : détruire complètement la session
                        session_destroy();
                        session_start();
                        
                        return ['success' => true, 'message' => 'Session nettoyée - retour à l\'étape 1'];
                    } catch (Exception $e) {
                        return ['success' => false, 'message' => 'Erreur session : ' . $e->getMessage()];
                    }
                }
                
                private function removeEnvFile() {
                    try {
                        if (file_exists('.env')) {
                            if (unlink('.env')) {
                                return ['success' => true, 'message' => 'Fichier .env supprimé'];
                            } else {
                                return ['success' => false, 'message' => 'Impossible de supprimer .env'];
                            }
                        } else {
                            return ['success' => true, 'message' => 'Fichier .env inexistant'];
                        }
                    } catch (Exception $e) {
                        return ['success' => false, 'message' => 'Erreur .env : ' . $e->getMessage()];
                    }
                }
                
                private function removeHtaccessFile() {
                    try {
                        if (file_exists('.htaccess')) {
                            if (unlink('.htaccess')) {
                                return ['success' => true, 'message' => 'Fichier .htaccess supprimé'];
                            } else {
                                return ['success' => false, 'message' => 'Impossible de supprimer .htaccess'];
                            }
                        } else {
                            return ['success' => true, 'message' => 'Fichier .htaccess inexistant'];
                        }
                    } catch (Exception $e) {
                        return ['success' => false, 'message' => 'Erreur .htaccess : ' . $e->getMessage()];
                    }
                }
                
                private function clearLogs() {
                    try {
                        $logFiles = 0;
                        $clearedFiles = 0;
                        
                        if (is_dir('logs')) {
                            $files = glob('logs/*.log');
                            $logFiles = count($files);
                            
                            foreach ($files as $file) {
                                if (file_put_contents($file, '') !== false) {
                                    $clearedFiles++;
                                }
                            }
                        }
                        
                        // Nettoyer aussi les logs PHP si accessibles
                        if (ini_get('error_log') && file_exists(ini_get('error_log'))) {
                            file_put_contents(ini_get('error_log'), '');
                        }
                        
                        return ['success' => true, 'message' => "$clearedFiles/$logFiles fichiers de logs nettoyés"];
                    } catch (Exception $e) {
                        return ['success' => false, 'message' => 'Erreur logs : ' . $e->getMessage()];
                    }
                }
                
                private function clearUploads() {
                    try {
                        $deletedFiles = 0;
                        
                        if (is_dir('public/uploads')) {
                            $files = glob('public/uploads/*');
                            
                            foreach ($files as $file) {
                                if (is_file($file)) {
                                    if (unlink($file)) {
                                        $deletedFiles++;
                                    }
                                }
                            }
                        }
                        
                        return ['success' => true, 'message' => "$deletedFiles fichiers supprimés du dossier uploads"];
                    } catch (Exception $e) {
                        return ['success' => false, 'message' => 'Erreur uploads : ' . $e->getMessage()];
                    }
                }
                
                private function dropDatabaseTables() {
                    try {
                        $config = [
                            'host' => $_POST['db_host'],
                            'port' => $_POST['db_port'],
                            'dbname' => $_POST['db_name'],
                            'username' => $_POST['db_user'],
                            'password' => $_POST['db_password']
                        ];
                        
                        if (empty($config['host']) || empty($config['username'])) {
                            return ['success' => false, 'message' => 'Configuration BDD manquante'];
                        }
                        
                        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
                        $pdo = new PDO($dsn, $config['username'], $config['password'], [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                        ]);
                        
                        // Tables IntraSphere à supprimer
                        $tables = [
                            'permissions',
                            'trainings', 
                            'complaints',
                            'messages',
                            'events',
                            'documents',
                            'announcements',
                            'users'
                        ];
                        
                        $droppedTables = 0;
                        
                        // Désactiver les contraintes de clés étrangères
                        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
                        
                        foreach ($tables as $table) {
                            try {
                                $pdo->exec("DROP TABLE IF EXISTS `$table`");
                                $droppedTables++;
                            } catch (PDOException $e) {
                                // Continuer même si une table n'existe pas
                            }
                        }
                        
                        // Réactiver les contraintes
                        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
                        
                        return ['success' => true, 'message' => "$droppedTables tables supprimées de la base de données"];
                        
                    } catch (PDOException $e) {
                        return ['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()];
                    } catch (Exception $e) {
                        return ['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()];
                    }
                }
            }
            
            // Traitement du formulaire
            $resetter = new IntraSphereReset();
            
            if ($_POST && isset($_POST['action']) && $_POST['action'] === 'reset') {
                $resetter->performReset();
            } else {
                $resetter->showResetForm();
            }
            
            ?>
        </div>
    </div>
</body>
</html>