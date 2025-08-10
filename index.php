<?php
/**
 * IntraSphere - Index principal corrigé 
 * Redirection vers la version PHP corrigée
 */

// Vérifier si l'installation PHP existe
if (file_exists(__DIR__ . '/php-migration/index.php')) {
    // Rediriger vers la version PHP corrigée
    header('Location: php-migration/index.php');
    exit;
} else {
    // Afficher une page d'installation
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>IntraSphere - Installation Requise</title>
        <style>
            body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #8B5CF6, #A78BFA); 
                   min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
            .container { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
                        text-align: center; max-width: 500px; }
            h1 { color: #8B5CF6; margin-bottom: 20px; }
            .btn { background: #8B5CF6; color: white; padding: 15px 30px; border: none; border-radius: 8px; 
                   text-decoration: none; font-weight: 600; margin: 10px; display: inline-block; }
            .btn:hover { background: #7C3AED; }
            .steps { text-align: left; margin: 20px 0; }
            .step { margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚀 IntraSphere</h1>
            <h2>Installation Requise</h2>
            
            <div class="steps">
                <h3>Étapes d'installation :</h3>
                <div class="step">1. <a href="install_fixed.php" class="btn">Installer IntraSphere</a></div>
                <div class="step">2. <a href="test_intrasphere.php" class="btn">Tester l'installation</a></div>
                <div class="step">3. <a href="debug_index.php" class="btn">Diagnostic système</a></div>
            </div>
            
            <p><strong>Ou utilisez la version simplifiée :</strong></p>
            <a href="simple_index.php" class="btn">Version Simplifiée</a>
        </div>
    </body>
    </html>
    <?php
}
?>