<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - IntraSphere</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .login-container { 
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 40px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%; max-width: 400px;
        }
        .logo { 
            text-align: center; margin-bottom: 30px; 
            font-size: 2rem; font-weight: 700; color: #8B5CF6;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; margin-bottom: 8px; font-weight: 600; color: #374151;
        }
        .form-group input { 
            width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb;
            border-radius: 8px; font-size: 16px; transition: border-color 0.3s;
        }
        .form-group input:focus { 
            outline: none; border-color: #8B5CF6;
        }
        .btn-login { 
            width: 100%; padding: 14px; background: linear-gradient(135deg, #8B5CF6, #A78BFA);
            color: white; border: none; border-radius: 8px; font-size: 16px;
            font-weight: 600; cursor: pointer; transition: transform 0.2s;
        }
        .btn-login:hover { transform: translateY(-2px); }
        .error { 
            background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c;
            padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;
        }
        .demo-accounts { 
            margin-top: 30px; padding: 20px; background: #f3f4f6;
            border-radius: 8px; font-size: 14px; color: #6b7280;
        }
        .demo-accounts h3 { color: #374151; margin-bottom: 10px; }
        .demo-account { margin: 5px 0; }
        .demo-account strong { color: #8B5CF6; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🚀 IntraSphere</div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/intrasphere/login">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Se connecter</button>
        </form>
        
        <div class="demo-accounts">
            <h3>Comptes de test</h3>
            <div class="demo-account"><strong>Admin :</strong> admin / admin123</div>
            <div class="demo-account"><strong>Employé :</strong> marie.martin / password123</div>
        </div>
    </div>
</body>
</html>