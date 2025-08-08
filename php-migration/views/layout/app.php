<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'IntraSphere') ?></title>
    <meta name="description" content="<?= h($description ?? 'Plateforme intranet d\'entreprise moderne') ?>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS with Glass Morphism -->
    <style>
        /* Variables CSS pour le thème Glass Morphism */
        :root {
            --primary: #8B5CF6;
            --primary-dark: #7C3AED;
            --secondary: #A78BFA;
            --accent: #C4B5FD;
            --background: #0F172A;
            --surface: rgba(255, 255, 255, 0.1);
            --surface-hover: rgba(255, 255, 255, 0.15);
            --text-primary: #F8FAFC;
            --text-secondary: #CBD5E1;
            --text-muted: #94A3B8;
            --border: rgba(255, 255, 255, 0.2);
            --shadow: rgba(0, 0, 0, 0.3);
        }
        
        /* Mode sombre par défaut */
        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #334155 100%);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        
        /* Effet Glass Morphism */
        .glass {
            background: var(--surface);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 8px 32px var(--shadow);
        }
        
        .glass-card {
            background: var(--surface);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 12px 40px var(--shadow);
            transition: all 0.3s ease;
        }
        
        .glass-card:hover {
            background: var(--surface-hover);
            transform: translateY(-2px);
            box-shadow: 0 16px 48px var(--shadow);
        }
        
        /* Boutons avec effet glass */
        .btn-glass {
            background: var(--surface);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            color: var(--text-primary);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn-glass:hover {
            background: var(--surface-hover);
            transform: translateY(-1px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }
        
        /* Navigation avec effet glass */
        .nav-glass {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        
        /* Sidebar avec effet glass */
        .sidebar {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border);
        }
        
        /* Inputs avec effet glass */
        .input-glass {
            background: var(--surface);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .input-glass:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .input-glass::placeholder {
            color: var(--text-muted);
        }
        
        /* Badges et tags */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .badge-primary {
            background: rgba(139, 92, 246, 0.2);
            color: var(--accent);
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        
        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #6EE7B7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #FDE68A;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        .badge-error {
            background: rgba(239, 68, 68, 0.2);
            color: #FECACA;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        /* Animations fluides */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        
        /* Scrollbar personnalisée */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--surface);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--surface-hover);
        }
        
        /* Mode responsive */
        @media (max-width: 768px) {
            .glass-card {
                border-radius: 12px;
                margin: 0.5rem;
            }
        }
    </style>
    
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body class="min-h-screen">
    <!-- Navigation principale -->
    <?php if (isLoggedIn()): ?>
        <nav class="nav-glass fixed top-0 left-0 right-0 z-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo et titre -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                        <i data-lucide="zap" class="w-5 h-5 text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">IntraSphere</h1>
                </div>
                
                <!-- Menu utilisateur -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="btn-glass p-2 relative">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- Messages -->
                    <button class="btn-glass p-2 relative">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                    </button>
                    
                    <!-- Profil utilisateur -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                            <span class="text-sm font-semibold text-white">
                                <?= h(substr(currentUser()['name'] ?? 'U', 0, 1)) ?>
                            </span>
                        </div>
                        <span class="text-sm font-medium hidden md:block">
                            <?= h(currentUser()['name'] ?? 'Utilisateur') ?>
                        </span>
                    </div>
                    
                    <!-- Déconnexion -->
                    <form method="POST" action="/logout" class="inline">
                        <button type="submit" class="btn-glass p-2" title="Déconnexion">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        
        <!-- Sidebar -->
        <aside class="sidebar fixed top-16 left-0 h-full w-64 overflow-y-auto animate-slide-in hidden lg:block">
            <div class="p-6">
                <nav class="space-y-2">
                    <a href="/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                        <i data-lucide="home" class="w-5 h-5"></i>
                        <span>Tableau de bord</span>
                    </a>
                    
                    <a href="/announcements" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                        <i data-lucide="megaphone" class="w-5 h-5"></i>
                        <span>Annonces</span>
                    </a>
                    
                    <a href="/documents" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        <span>Documents</span>
                    </a>
                    
                    <a href="/messages" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                        <span>Messages</span>
                    </a>
                    
                    <a href="/trainings" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                        <span>Formations</span>
                    </a>
                    
                    <?php if (hasRole('moderator')): ?>
                        <div class="border-t border-white border-opacity-20 my-4"></div>
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</p>
                        
                        <?php if (hasRole('admin')): ?>
                            <a href="/admin" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                                <i data-lucide="settings" class="w-5 h-5"></i>
                                <span>Admin</span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </nav>
            </div>
        </aside>
    <?php endif; ?>
    
    <!-- Contenu principal -->
    <main class="<?= isLoggedIn() ? 'lg:ml-64 pt-16' : '' ?> min-h-screen">
        <div class="p-6">
            <!-- Messages flash -->
            <?php foreach (getFlashMessages() as $flash): ?>
                <div class="glass-card mb-4 p-4 border-l-4 <?= $flash['type'] === 'error' ? 'border-red-500' : ($flash['type'] === 'success' ? 'border-green-500' : 'border-blue-500') ?>">
                    <p class="<?= $flash['type'] === 'error' ? 'text-red-200' : ($flash['type'] === 'success' ? 'text-green-200' : 'text-blue-200') ?>">
                        <?= h($flash['message']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
            
            <!-- Contenu de la page -->
            <div class="animate-fade-in">
                <?= $content ?>
            </div>
        </div>
    </main>
    
    <!-- Scripts JavaScript -->
    <script>
        // Initialiser Lucide icons
        lucide.createIcons();
        
        // Theme toggle et autres interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des notifications temps réel (simulation)
            function updateNotifications() {
                fetch('/api/notifications/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.querySelector('.notification-badge');
                        if (badge && data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = 'block';
                        }
                    })
                    .catch(() => {});
            }
            
            // Mettre à jour les notifications toutes les 30 secondes
            setInterval(updateNotifications, 30000);
            updateNotifications();
            
            // Gestion responsive du menu mobile
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.querySelector('.sidebar');
            
            if (mobileMenuButton && sidebar) {
                mobileMenuButton.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                });
            }
            
            // Auto-hide flash messages
            setTimeout(function() {
                const flashMessages = document.querySelectorAll('.flash-message');
                flashMessages.forEach(msg => {
                    msg.style.transition = 'opacity 0.5s ease';
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 500);
                });
            }, 5000);
        });
        
        // Helper pour les requêtes API
        window.api = {
            get: (url) => fetch(url).then(r => r.json()),
            post: (url, data) => fetch(url, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            }).then(r => r.json()),
            put: (url, data) => fetch(url, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            }).then(r => r.json()),
            delete: (url) => fetch(url, {method: 'DELETE'}).then(r => r.json())
        };
    </script>
    
    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>
</body>
</html>