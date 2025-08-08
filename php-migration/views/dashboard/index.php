<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Tableau de bord - IntraSphere' ?></title>
    <meta name="description" content="<?= $description ?? 'Vue d\'ensemble de votre espace de travail IntraSphere' ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Glass Morphism CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.4);
        }
        
        .nav-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.02);
        }
        
        .quick-action {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 14px;
            transition: all 0.3s ease;
        }
        
        .quick-action:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.2));
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(31, 38, 135, 0.3);
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <!-- Navigation principale -->
    <nav class="glass fixed top-4 left-4 right-4 z-50 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-globe mr-2"></i>IntraSphere
                </h1>
                <span class="glass-dark px-3 py-1 text-sm text-white/80">Tableau de bord</span>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="glass-dark px-3 py-2 text-sm text-white/90">
                    <i class="fas fa-user mr-2"></i>
                    <?= htmlspecialchars($user['name'] ?? 'Utilisateur') ?>
                </div>
                <a href="/logout" class="nav-item px-4 py-2 text-white hover:text-white/80">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="pt-24 pb-8 px-4">
        <!-- Statistiques principales -->
        <div class="max-w-7xl mx-auto mb-8">
            <h2 class="text-3xl font-bold text-white mb-6 text-center">
                Bienvenue sur votre tableau de bord
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6 floating-animation">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-sm mb-1">Utilisateurs</p>
                            <p class="text-3xl font-bold text-white" id="total-users">-</p>
                        </div>
                        <div class="text-4xl text-white/60">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card p-6 floating-animation" style="animation-delay: -1s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-sm mb-1">Annonces</p>
                            <p class="text-3xl font-bold text-white" id="total-announcements">-</p>
                        </div>
                        <div class="text-4xl text-white/60">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card p-6 floating-animation" style="animation-delay: -2s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-sm mb-1">Documents</p>
                            <p class="text-3xl font-bold text-white" id="total-documents">-</p>
                        </div>
                        <div class="text-4xl text-white/60">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card p-6 floating-animation" style="animation-delay: -3s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-sm mb-1">Messages</p>
                            <p class="text-3xl font-bold text-white" id="total-messages">-</p>
                        </div>
                        <div class="text-4xl text-white/60">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="max-w-7xl mx-auto mb-8">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">Actions rapides</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="/announcements" class="quick-action p-6 text-center group block">
                    <div class="text-4xl text-white/80 mb-3 group-hover:text-white transition-colors">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Annonces</h4>
                    <p class="text-white/70 text-sm">Consulter les dernières annonces</p>
                </a>
                
                <a href="/documents" class="quick-action p-6 text-center group block">
                    <div class="text-4xl text-white/80 mb-3 group-hover:text-white transition-colors">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Documents</h4>
                    <p class="text-white/70 text-sm">Parcourir les documents</p>
                </a>
                
                <a href="/messages" class="quick-action p-6 text-center group block">
                    <div class="text-4xl text-white/80 mb-3 group-hover:text-white transition-colors">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Messages</h4>
                    <p class="text-white/70 text-sm">Messagerie interne</p>
                </a>
                
                <a href="/trainings" class="quick-action p-6 text-center group block">
                    <div class="text-4xl text-white/80 mb-3 group-hover:text-white transition-colors">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Formations</h4>
                    <p class="text-white/70 text-sm">Catalogue de formations</p>
                </a>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="max-w-4xl mx-auto">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">Activité récente</h3>
            
            <div class="glass p-6">
                <div class="space-y-4" id="recent-activity">
                    <div class="flex items-start space-x-4 p-4 glass-dark rounded-lg">
                        <div class="text-2xl text-white/60">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-semibold mb-1">Nouvelle annonce publiée</h4>
                            <p class="text-white/70 text-sm mb-2">Politique de télétravail mise à jour</p>
                            <span class="text-white/50 text-xs">Il y a 2 heures</span>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4 p-4 glass-dark rounded-lg">
                        <div class="text-2xl text-white/60">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-semibold mb-1">Document ajouté</h4>
                            <p class="text-white/70 text-sm mb-2">Guide de sécurité informatique</p>
                            <span class="text-white/50 text-xs">Il y a 4 heures</span>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4 p-4 glass-dark rounded-lg">
                        <div class="text-2xl text-white/60">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-semibold mb-1">Formation programmée</h4>
                            <p class="text-white/70 text-sm mb-2">Introduction aux nouveaux outils</p>
                            <span class="text-white/50 text-xs">Il y a 1 jour</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript pour charger les données -->
    <script>
        // Chargement des statistiques
        async function loadStats() {
            try {
                const response = await fetch('/api/stats');
                const stats = await response.json();
                
                document.getElementById('total-users').textContent = stats.totalUsers || 0;
                document.getElementById('total-announcements').textContent = stats.totalAnnouncements || 0;
                document.getElementById('total-documents').textContent = stats.totalDocuments || 0;
                document.getElementById('total-messages').textContent = stats.totalMessages || 0;
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            }
        }
        
        // Chargement au démarrage
        document.addEventListener('DOMContentLoaded', loadStats);
    </script>
</body>
</html>