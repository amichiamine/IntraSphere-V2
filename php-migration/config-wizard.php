<?php
/**
 * Assistant de configuration avancé pour IntraSphere
 */

require_once 'config/setup.php';

$setup = new DatabaseSetup();
$step = $_GET['step'] ?? 1;
$hostingTypes = $setup->getHostingTypes();

if ($_POST) {
    $step = $_POST['step'] ?? 1;
    
    if ($step == 2) {
        $hostingType = $_POST['hosting_type'];
        $params = [
            'dbname' => $_POST['dbname'] ?? 'intrasphere',
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'user' => $_POST['cpanel_user'] ?? '',
            'id' => $_POST['hosting_id'] ?? ''
        ];
        
        try {
            $config = $setup->generateConfig($hostingType, $params);
            $testResult = $setup->testConnection($config);
            
            if ($testResult['success']) {
                $envContent = $setup->generateEnvFile($config);
                file_put_contents('.env', $envContent);
                $step = 3;
                $success = true;
            } else {
                $error = $testResult['message'];
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant Configuration - IntraSphere</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; padding: 20px;
        }
        .wizard { 
            max-width: 800px; margin: 0 auto; 
            background: rgba(255,255,255,0.95); 
            border-radius: 20px; overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header { 
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white; padding: 30px; text-align: center;
        }
        .content { padding: 40px; }
        .step-indicator { 
            display: flex; justify-content: center; margin-bottom: 40px;
        }
        .step-item { 
            width: 40px; height: 40px; border-radius: 50%;
            background: #e5e7eb; color: #6b7280;
            display: flex; align-items: center; justify-content: center;
            margin: 0 15px; position: relative; font-weight: bold;
        }
        .step-item.active { background: #8b5cf6; color: white; }
        .step-item.completed { background: #10b981; color: white; }
        .step-item::after { 
            content: ''; position: absolute; top: 50%; left: 100%;
            width: 30px; height: 2px; background: #e5e7eb;
            transform: translateY(-50%);
        }
        .step-item:last-child::after { display: none; }
        .form-group { margin-bottom: 25px; }
        label { 
            display: block; margin-bottom: 8px; 
            font-weight: 600; color: #374151; 
        }
        input, select { 
            width: 100%; padding: 15px; border: 2px solid #e5e7eb;
            border-radius: 10px; font-size: 16px; transition: all 0.3s;
        }
        input:focus, select:focus { 
            outline: none; border-color: #8b5cf6; 
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        .btn { 
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white; padding: 15px 30px; border: none;
            border-radius: 10px; cursor: pointer; font-size: 16px;
            transition: transform 0.2s; font-weight: 600;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-full { width: 100%; }
        .error { 
            background: #fef2f2; color: #dc2626; 
            padding: 15px; border-radius: 10px; margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
        .success { 
            background: #f0fdf4; color: #16a34a; 
            padding: 15px; border-radius: 10px; margin-bottom: 20px;
            border-left: 4px solid #16a34a;
        }
        .hosting-cards { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px; margin-bottom: 30px;
        }
        .hosting-card { 
            border: 2px solid #e5e7eb; border-radius: 15px; 
            padding: 20px; text-align: center; cursor: pointer;
            transition: all 0.3s; background: white;
        }
        .hosting-card:hover { border-color: #8b5cf6; transform: translateY(-5px); }
        .hosting-card.selected { 
            border-color: #8b5cf6; background: #f3f4f6; 
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        .hosting-icon { font-size: 30px; margin-bottom: 10px; }
        .dynamic-fields { 
            background: #f8fafc; padding: 20px; 
            border-radius: 10px; margin-top: 20px; 
        }
        .completion { text-align: center; }
        .completion-icon { font-size: 80px; color: #10b981; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="wizard">
        <div class="header">
            <h1>🧙‍♂️ Assistant Configuration IntraSphere</h1>
            <p>Configuration automatique selon votre hébergement</p>
        </div>
        
        <div class="content">
            <div class="step-indicator">
                <div class="step-item <?= $step >= 1 ? 'active' : '' ?>">1</div>
                <div class="step-item <?= $step >= 2 ? 'active' : '' ?>">2</div>
                <div class="step-item <?= $step >= 3 ? 'active' : '' ?>">3</div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($step == 1): ?>
                <h2>Choisissez votre type d'hébergement</h2>
                <p style="margin-bottom: 30px; color: #6b7280;">Sélectionnez le type d'hébergement pour une configuration optimale :</p>
                
                <form method="POST">
                    <input type="hidden" name="step" value="2">
                    <div class="hosting-cards">
                        <?php foreach ($hostingTypes as $key => $type): ?>
                            <div class="hosting-card" onclick="selectHosting('<?= $key ?>')">
                                <div class="hosting-icon">
                                    <?php
                                    $icons = [
                                        'cpanel' => '🏠', 'ovh' => '☁️', 'ionos' => '🌐',
                                        'vps' => '🖥️', 'postgresql' => '🐘', 'local' => '💻'
                                    ];
                                    echo $icons[$key] ?? '🔧';
                                    ?>
                                </div>
                                <h3><?= htmlspecialchars($type['name']) ?></h3>
                                <input type="radio" name="hosting_type" value="<?= $key ?>" style="display: none;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-full" id="continue-btn" disabled>
                        Continuer avec ce type d'hébergement
                    </button>
                </form>
                
            <?php elseif ($step == 2): ?>
                <h2>Configuration des paramètres</h2>
                <p style="margin-bottom: 30px; color: #6b7280;">
                    Type sélectionné: <strong><?= htmlspecialchars($hostingTypes[$_POST['hosting_type']]['name']) ?></strong>
                </p>
                
                <form method="POST">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="hosting_type" value="<?= $_POST['hosting_type'] ?>">
                    
                    <div class="form-group">
                        <label>Nom de la base de données</label>
                        <input type="text" name="dbname" value="intrasphere" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom d'utilisateur</label>
                        <input type="text" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password">
                    </div>
                    
                    <?php if ($_POST['hosting_type'] == 'cpanel'): ?>
                        <div class="dynamic-fields">
                            <h3>Paramètres cPanel</h3>
                            <div class="form-group">
                                <label>Nom d'utilisateur cPanel</label>
                                <input type="text" name="cpanel_user" placeholder="ex: cpanel_user">
                                <small>Utilisé pour le format: cpanel_user_intrasphere</small>
                            </div>
                        </div>
                    <?php elseif ($_POST['hosting_type'] == 'ionos'): ?>
                        <div class="dynamic-fields">
                            <h3>Paramètres 1&1/Ionos</h3>
                            <div class="form-group">
                                <label>ID Base de données</label>
                                <input type="text" name="hosting_id" placeholder="ex: 12345678">
                                <small>ID fourni par 1&1 (ex: db12345678)</small>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn-full">
                        Tester et Configurer
                    </button>
                </form>
                
            <?php elseif ($step == 3): ?>
                <div class="completion">
                    <div class="completion-icon">🎉</div>
                    <h2>Configuration Terminée !</h2>
                    <p style="margin: 20px 0;">
                        La configuration de la base de données a été générée et testée avec succès.
                    </p>
                    
                    <div style="margin: 30px 0;">
                        <a href="install.php" class="btn" style="text-decoration: none; display: inline-block;">
                            Installer les Tables
                        </a>
                        <a href="/" class="btn" style="text-decoration: none; display: inline-block; margin-left: 10px;">
                            Accéder à IntraSphere
                        </a>
                    </div>
                    
                    <div style="background: #f8fafc; padding: 20px; border-radius: 10px; text-align: left;">
                        <h3>Fichier .env généré :</h3>
                        <pre style="background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; overflow-x: auto;">
<?= htmlspecialchars(file_get_contents('.env')) ?>
                        </pre>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function selectHosting(type) {
            // Désélectionner tous
            document.querySelectorAll('.hosting-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Sélectionner le nouveau
            event.target.closest('.hosting-card').classList.add('selected');
            
            // Cocher le radio
            document.querySelector(`input[value="${type}"]`).checked = true;
            
            // Activer le bouton
            document.getElementById('continue-btn').disabled = false;
        }
    </script>
</body>
</html>