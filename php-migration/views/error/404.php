<?php
/**
 * Page d'erreur 404 - Page non trouvée
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - IntraSphere</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .error-container { 
            text-align: center; background: rgba(255,255,255,0.95); 
            padding: 60px 40px; border-radius: 20px; 
            backdrop-filter: blur(10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px; margin: 20px;
        }
        .error-code { 
            font-size: 8rem; font-weight: 300; color: #8B5CF6; 
            line-height: 1; margin-bottom: 20px;
        }
        .error-title { 
            font-size: 2rem; color: #333; margin-bottom: 15px; font-weight: 600;
        }
        .error-message { 
            color: #666; margin-bottom: 30px; font-size: 1.1rem; line-height: 1.6;
        }
        .error-actions { margin-top: 30px; }
        .btn { 
            display: inline-block; padding: 15px 30px; 
            background: linear-gradient(45deg, #8B5CF6, #A78BFA);
            color: white; text-decoration: none; border-radius: 8px;
            font-weight: 600; transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-secondary { 
            background: linear-gradient(45deg, #6B7280, #9CA3AF);
            margin-left: 15px;
        }
        .back-link { 
            margin-top: 20px; display: block; color: #8B5CF6; 
            text-decoration: none; font-weight: 500;
        }
        .back-link:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .error-code { font-size: 5rem; }
            .error-title { font-size: 1.5rem; }
            .btn { display: block; margin: 10px 0; }
            .btn-secondary { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Page non trouvée</h1>
        <p class="error-message">
            La page que vous recherchez n'existe pas ou a été déplacée.
            Veuillez vérifier l'URL ou utiliser les liens ci-dessous.
        </p>
        <div class="error-actions">
            <a href="/intrasphere/" class="btn">Retour à l'accueil</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Page précédente</a>
        </div>
        <a href="/intrasphere/dashboard" class="back-link">← Retour au tableau de bord</a>
    </div>
</body>
</html>