<?php
/**
 * Script de correction automatique du fichier .htaccess
 * Détecte l'hébergement et applique la configuration adaptée
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Détection automatique du type d'hébergement
function detectHostingType() {
    $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';
    $serverName = $_SERVER['SERVER_NAME'] ?? '';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    
    // OVH
    if (stripos($serverName, 'ovh') !== false || 
        stripos($documentRoot, 'ovh') !== false ||
        stripos($serverSoftware, 'ovh') !== false) {
        return 'ovh';
    }
    
    // 1&1/Ionos
    if (stripos($serverName, '1and1') !== false || 
        stripos($serverName, 'ionos') !== false ||
        stripos($documentRoot, '1and1') !== false) {
        return '1and1';
    }
    
    // Tests de compatibilité
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules) && 
            in_array('mod_authz_core', $modules)) {
            return 'advanced';
        }
    }
    
    // Version de sécurité basique
    return 'basic';
}

function getHtaccessConfig($type) {
    switch ($type) {
        case 'ovh':
            return file_get_contents(__DIR__ . '/.htaccess-ovh');
        
        case 'advanced':
            return file_get_contents(__DIR__ . '/.htaccess-minimal');
            
        case 'compatible':
            return file_get_contents(__DIR__ . '/.htaccess-compatible');
            
        case 'basic':
        default:
            return file_get_contents(__DIR__ . '/.htaccess-basic');
    }
}

$hostingType = detectHostingType();
$action = $_POST['action'] ?? '';

if ($action === 'apply_config') {
    $selectedType = $_POST['config_type'] ?? $hostingType;
    
    try {
        $htaccessContent = getHtaccessConfig($selectedType);
        
        // Backup de l'ancien fichier
        if (file_exists(__DIR__ . '/.htaccess')) {
            copy(__DIR__ . '/.htaccess', __DIR__ . '/.htaccess.backup.' . date('Y-m-d-H-i-s'));
        }
        
        // Écriture du nouveau fichier
        if (file_put_contents(__DIR__ . '/.htaccess', $htaccessContent) !== false) {
            $success = "Configuration .htaccess appliquée avec succès (type: {$selectedType})";
        } else {
            $error = "Impossible d'écrire le fichier .htaccess";
        }
        
    } catch (Exception $e) {
        $error = "Erreur lors de l'application: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correction .htaccess - IntraSphere</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
            margin: 0; padding: 20px; min-height: 100vh;
        }
        .container { 
            max-width: 700px; margin: 0 auto; 
            background: rgba(255, 255, 255, 0.95); 
            padding: 40px; border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 2rem; color: #8B5CF6; margin-bottom: 10px; }
        
        .config-option { 
            border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; 
            margin: 15px 0; cursor: pointer; transition: all 0.3s;
        }
        .config-option:hover { border-color: #8B5CF6; }
        .config-option.selected { border-color: #8B5CF6; background: #f8fafc; }
        
        .config-title { font-weight: 600; color: #1f2937; margin-bottom: 8px; }
        .config-desc { color: #6b7280; font-size: 0.9rem; margin-bottom: 10px; }
        .config-features { font-size: 0.8rem; color: #374151; }
        
        .btn { 
            display: inline-block; padding: 12px 24px; background: #8B5CF6; 
            color: white; text-decoration: none; border-radius: 8px; 
            font-weight: 600; border: none; cursor: pointer; margin: 10px 5px;
        }
        .btn:hover { background: #7C3AED; }
        .btn-secondary { background: #6b7280; }
        
        .alert { padding: 15px; border-radius: 8px; margin: 20px 0; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }
        
        .system-info { 
            background: #f8fafc; padding: 15px; border-radius: 8px; 
            font-family: monospace; font-size: 0.9rem; margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔧 Correction .htaccess</div>
            <p>Configuration automatique du fichier .htaccess pour votre hébergement</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($success) ?><br>
                <a href="reset_installation.php" style="color: #065f46; text-decoration: underline;">
                    → Tester l'installation maintenant
                </a>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-info">
            <strong>Détection automatique:</strong><br>
            Type d'hébergement détecté: <strong><?= ucfirst($hostingType) ?></strong><br>
            Serveur: <?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Non défini') ?><br>
            Domaine: <?= htmlspecialchars($_SERVER['SERVER_NAME'] ?? 'Non défini') ?>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="apply_config">
            <h3>Choisissez la configuration .htaccess adaptée :</h3>

            <div class="config-option" onclick="selectConfig('basic')">
                <input type="radio" name="config_type" value="basic" id="basic" <?= $hostingType === 'basic' ? 'checked' : '' ?>>
                <div class="config-title">Configuration de Base</div>
                <div class="config-desc">Protection minimale, compatible avec tous les hébergeurs</div>
                <div class="config-features">• Protection des fichiers .env et .sql • Aucune fonctionnalité avancée</div>
            </div>

            <div class="config-option" onclick="selectConfig('compatible')">
                <input type="radio" name="config_type" value="compatible" id="compatible">
                <div class="config-title">Configuration Compatible</div>
                <div class="config-desc">Sécurité renforcée avec syntaxe Apache 2.2 compatible</div>
                <div class="config-features">• Protection des dossiers système • Syntaxe Order/Deny compatible</div>
            </div>

            <div class="config-option" onclick="selectConfig('ovh')">
                <input type="radio" name="config_type" value="ovh" id="ovh" <?= $hostingType === 'ovh' ? 'checked' : '' ?>>
                <div class="config-title">Configuration OVH Optimisée</div>
                <div class="config-desc">Spécialement optimisée pour l'hébergement mutualisé OVH</div>
                <div class="config-features">• Cache et compression • Protection avancée • Optimisations OVH</div>
            </div>

            <div class="config-option" onclick="selectConfig('advanced')">
                <input type="radio" name="config_type" value="advanced" id="advanced" <?= $hostingType === 'advanced' ? 'checked' : '' ?>>
                <div class="config-title">Configuration Avancée</div>
                <div class="config-desc">Toutes les fonctionnalités pour serveurs modernes</div>
                <div class="config-features">• Apache 2.4+ requis • Toutes les optimisations • Sécurité maximale</div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn">Appliquer la Configuration</button>
                <a href="reset_installation.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>

        <div class="system-info">
            <strong>Informations système détectées:</strong><br>
            SERVER_SOFTWARE: <?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') ?><br>
            DOCUMENT_ROOT: <?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') ?><br>
            SERVER_NAME: <?= htmlspecialchars($_SERVER['SERVER_NAME'] ?? 'N/A') ?><br>
            mod_rewrite: <?= function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? 'Disponible' : 'Indéterminé' ?>
        </div>
    </div>

    <script>
        function selectConfig(type) {
            // Désélectionner tous
            document.querySelectorAll('.config-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Sélectionner le choix
            event.currentTarget.classList.add('selected');
            document.getElementById(type).checked = true;
        }
        
        // Sélectionner la configuration par défaut
        document.addEventListener('DOMContentLoaded', function() {
            const defaultRadio = document.querySelector('input[type="radio"]:checked');
            if (defaultRadio) {
                defaultRadio.closest('.config-option').classList.add('selected');
            }
        });
    </script>
</body>
</html>