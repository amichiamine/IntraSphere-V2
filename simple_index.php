<?php
/**
 * Version simplifiée de IntraSphere pour test
 */

// Affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage session
session_start();

// Définition des chemins
define('APP_ROOT', __DIR__ . '/php-migration');

// Charger l'environnement
$envFile = APP_ROOT . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Connexion base de données simple
try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $dbname = $_ENV['DB_NAME'] ?? 'intrasphere';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

// Gestion de la route
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Router simple
if ($method === 'POST' && $uri === '/login') {
    // Traitement de la connexion
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: /dashboard');
        exit;
    } else {
        $error = "Identifiants incorrects";
    }
}

if ($method === 'POST' && $uri === '/logout') {
    session_destroy();
    header('Location: /');
    exit;
}

// Vérification d'authentification
$isLoggedIn = isset($_SESSION['user']);
$currentUser = $_SESSION['user'] ?? null;

if (!$isLoggedIn && $uri !== '/' && $uri !== '/login') {
    header('Location: /');
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntraSphere - Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            color: #667eea;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .form-group {
            margin: 20px 0;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
        }
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .dashboard {
            text-align: left;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: #f8faff;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .user-info {
            background: #f0fdf4;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>🚀 IntraSphere</h1>
                <p>Plateforme intranet d'entreprise</p>
            </div>
            
            <?php if (!$isLoggedIn): ?>
                <!-- Formulaire de connexion -->
                <form method="POST" action="/login">
                    <?php if (isset($error)): ?>
                        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur :</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe :</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn">Se connecter</button>
                </form>
                
                <div style="margin-top: 30px; padding: 20px; background: #f8faff; border-radius: 8px;">
                    <h3>Comptes de test :</h3>
                    <p><strong>Admin :</strong> admin / admin123</p>
                    <p><strong>Employé :</strong> marie.martin / password123</p>
                    <p><strong>Modérateur :</strong> pierre.dubois / password123</p>
                </div>
                
            <?php else: ?>
                <!-- Dashboard simplifié -->
                <div class="dashboard">
                    <div class="user-info">
                        <h3>Bienvenue, <?= htmlspecialchars($currentUser['name']) ?> !</h3>
                        <p>Rôle : <?= htmlspecialchars($currentUser['role']) ?></p>
                        <p>Département : <?= htmlspecialchars($currentUser['department'] ?? 'Non défini') ?></p>
                    </div>
                    
                    <h2>Tableau de bord</h2>
                    
                    <?php
                    // Récupération des statistiques
                    $stats = [
                        'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
                        'announcements' => $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn(),
                        'documents' => $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn(),
                        'messages' => 0
                    ];
                    
                    // Messages de l'utilisateur
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE recipient_id = ?");
                    $stmt->execute([$currentUser['id']]);
                    $stats['messages'] = $stmt->fetchColumn();
                    ?>
                    
                    <div class="stats">
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['users'] ?></div>
                            <div>Utilisateurs</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['announcements'] ?></div>
                            <div>Annonces</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['documents'] ?></div>
                            <div>Documents</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['messages'] ?></div>
                            <div>Messages</div>
                        </div>
                    </div>
                    
                    <h3>Dernières annonces</h3>
                    <?php
                    $announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3")->fetchAll();
                    ?>
                    
                    <?php if (!empty($announcements)): ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <div style="background: #f8faff; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #667eea;">
                                <h4><?= htmlspecialchars($announcement['title']) ?></h4>
                                <p><?= htmlspecialchars(substr($announcement['content'], 0, 150)) ?>...</p>
                                <small>Par <?= htmlspecialchars($announcement['author_name']) ?> le <?= date('d/m/Y H:i', strtotime($announcement['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucune annonce disponible.</p>
                    <?php endif; ?>
                    
                    <div style="margin-top: 30px;">
                        <form method="POST" action="/logout" style="display: inline;">
                            <button type="submit" class="btn" style="background: #dc2626;">Se déconnecter</button>
                        </form>
                        <a href="index.php" class="btn" style="background: #059669; margin-left: 10px;">Version complète</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>