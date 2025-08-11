<?php
/**
 * Page d'erreur 500 - Erreur interne du serveur
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur - IntraSphere</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .error-container { 
            text-align: center; background: rgba(255,255,255,0.95); 
            padding: 60px 40px; border-radius: 20px; 
            backdrop-filter: blur(10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px; margin: 20px;
        }
        .error-icon { 
            font-size: 6rem; margin-bottom: 20px; color: #ef4444;
        }
        .error-code { 
            font-size: 4rem; font-weight: 300; color: #ef4444; 
            line-height: 1; margin-bottom: 20px;
        }
        .error-title { 
            font-size: 2rem; color: #333; margin-bottom: 15px; font-weight: 600;
        }
        .error-message { 
            color: #666; margin-bottom: 30px; font-size: 1.1rem; line-height: 1.6;
        }
        .error-details { 
            background: #f8f9fa; padding: 15px; border-radius: 8px; 
            margin: 20px 0; text-align: left; font-family: monospace; font-size: 14px;
            border-left: 4px solid #ef4444; color: #721c24;
        }
        .error-actions { margin-top: 30px; }
        .btn { 
            display: inline-block; padding: 15px 30px; 
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white; text-decoration: none; border-radius: 8px;
            font-weight: 600; transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-secondary { 
            background: linear-gradient(45deg, #6B7280, #9CA3AF);
            margin-left: 15px;
        }
        .back-link { 
            margin-top: 20px; display: block; color: #ef4444; 
            text-decoration: none; font-weight: 500;
        }
        .back-link:hover { text-decoration: underline; }
        .help-text { 
            margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;
            font-size: 14px; color: #6b7280;
        }
        @media (max-width: 768px) {
            .error-code { font-size: 3rem; }
            .error-title { font-size: 1.5rem; }
            .btn { display: block; margin: 10px 0; }
            .btn-secondary { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <div class="error-code">500</div>
        <h1 class="error-title">Erreur interne du serveur</h1>
        <p class="error-message">
            Une erreur inattendue s'est produite sur le serveur. 
            Notre équipe a été automatiquement notifiée de ce problème.
        </p>
        
        <?php if (isset($error_message) && !empty($error_message)): ?>
        <div class="error-details">
            <strong>Détails technique :</strong><br>
            <?= htmlspecialchars($error_message) ?>
        </div>
        <?php endif; ?>
        
        <div class="error-actions">
            <a href="/intrasphere/" class="btn">Retour à l'accueil</a>
            <a href="javascript:location.reload()" class="btn btn-secondary">Recharger la page</a>
        </div>
        <a href="/intrasphere/dashboard" class="back-link">← Retour au tableau de bord</a>
        
        <div class="help-text">
            <p><strong>Code d'erreur :</strong> 500-<?= date('YmdHis') ?></p>
            <p>Si le problème persiste, contactez l'administrateur système.</p>
        </div>
    </div>
</body>
</html>