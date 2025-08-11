<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - IntraSphere</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
            min-height: 100vh; 
        }
        .header {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 20px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        .logo { font-size: 1.5rem; font-weight: 700; color: #8B5CF6; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { 
            width: 40px; height: 40px; background: #8B5CF6; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;
        }
        .logout-btn { 
            background: #ef4444; color: white; border: none; padding: 8px 16px;
            border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block;
        }
        .logout-btn:hover { background: #dc2626; }
        .container { padding: 30px; max-width: 1200px; margin: 0 auto; }
        .welcome { text-align: center; margin-bottom: 40px; color: white; }
        .welcome h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .welcome p { font-size: 1.2rem; opacity: 0.9; }
        .stats-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px; margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 30px; border-radius: 16px; text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-number { font-size: 3rem; font-weight: 700; color: #8B5CF6; margin-bottom: 10px; }
        .stat-label { font-size: 1rem; color: #6b7280; font-weight: 600; }
        .content-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .content-card h3 { color: #8B5CF6; margin-bottom: 20px; font-size: 1.3rem; }
        .announcement-item, .message-item {
            padding: 15px 0; border-bottom: 1px solid #e5e7eb;
        }
        .announcement-item:last-child, .message-item:last-child { border-bottom: none; }
        .announcement-title, .message-title { font-weight: 600; color: #374151; margin-bottom: 5px; }
        .announcement-date, .message-date { font-size: 0.9rem; color: #6b7280; }
        .no-items { text-align: center; color: #6b7280; padding: 20px; }
        .navigation {
            margin-top: 40px; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;
        }
        .nav-btn {
            background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid white;
            padding: 15px 30px; border-radius: 10px; text-decoration: none;
            font-weight: 600; transition: all 0.3s; display: inline-block;
        }
        .nav-btn:hover { background: white; color: #8B5CF6; }
        @media (max-width: 768px) {
            .container { padding: 20px; }
            .welcome h1 { font-size: 2rem; }
            .content-grid { grid-template-columns: 1fr; }
            .header { flex-direction: column; gap: 15px; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚀 IntraSphere</div>
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
            <div>
                <div style="font-weight: 600;"><?= htmlspecialchars($user['name']) ?></div>
                <div style="font-size: 0.9rem; color: #6b7280;"><?= htmlspecialchars($user['role']) ?></div>
            </div>
            <form method="POST" action="/intrasphere/logout" style="margin: 0;">
                <button type="submit" class="logout-btn">Déconnexion</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h1>Bienvenue, <?= htmlspecialchars($user['name']) ?> !</h1>
            <p>Tableau de bord IntraSphere - <?= date('d/m/Y') ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['announcements'] ?></div>
                <div class="stat-label">Annonces</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['documents'] ?></div>
                <div class="stat-label">Documents</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['messages'] ?></div>
                <div class="stat-label">Messages</div>
            </div>
            <?php if ($user['role'] === 'admin'): ?>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['users'] ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <?php endif; ?>
        </div>

        <div class="content-grid">
            <div class="content-card">
                <h3>📢 Dernières annonces</h3>
                <?php if (!empty($recent_announcements)): ?>
                    <?php foreach ($recent_announcements as $announcement): ?>
                    <div class="announcement-item">
                        <div class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></div>
                        <div class="announcement-date"><?= date('d/m/Y H:i', strtotime($announcement['created_at'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-items">Aucune annonce disponible</div>
                <?php endif; ?>
            </div>

            <div class="content-card">
                <h3>💬 Messages récents</h3>
                <?php if (!empty($recent_messages)): ?>
                    <?php foreach ($recent_messages as $message): ?>
                    <div class="message-item">
                        <div class="message-title">De: <?= htmlspecialchars($message['sender_name'] ?? 'Inconnu') ?></div>
                        <div style="color: #374151; margin: 5px 0;"><?= htmlspecialchars(substr($message['content'], 0, 100)) ?>...</div>
                        <div class="message-date"><?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-items">Aucun message disponible</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="navigation">
            <a href="/intrasphere/announcements" class="nav-btn">📢 Annonces</a>
            <a href="/intrasphere/documents" class="nav-btn">📄 Documents</a>
            <a href="/intrasphere/messages" class="nav-btn">💬 Messages</a>
            <a href="/intrasphere/trainings" class="nav-btn">🎓 Formations</a>
            <?php if ($user['role'] === 'admin'): ?>
            <a href="/intrasphere/admin" class="nav-btn">⚙️ Administration</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Animation d'entrée
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stat-card, .content-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
    </script>
</body>
</html>