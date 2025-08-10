<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Administration - IntraSphere' ?></title>
    <meta name="description" content="<?= $description ?? 'Panneau d\'administration général' ?>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .admin-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.4);
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
        }
        
        .badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
        }
        
        .warning-card {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .success-card {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .chart-placeholder {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="glass fixed top-4 left-4 right-4 z-50 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="/dashboard" class="text-2xl font-bold text-white hover:text-white/80">
                    <i class="fas fa-globe mr-2"></i>IntraSphere
                </a>
                <span class="badge px-3 py-1 text-sm text-white/80">
                    <i class="fas fa-shield-alt mr-1"></i>Administration
                </span>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="badge px-3 py-2 text-sm text-white/90">
                    <i class="fas fa-user-shield mr-2"></i>
                    <?= htmlspecialchars($user['name'] ?? 'Administrateur') ?>
                </div>
                <a href="/logout" class="badge px-4 py-2 text-white hover:text-white/80">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="pt-24 pb-8 px-4">
        <!-- En-tête -->
        <div class="max-w-7xl mx-auto mb-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-4 floating-animation">
                    <i class="fas fa-cogs mr-3"></i>
                    Administration
                </h1>
                <p class="text-white/80 text-lg">
                    Gérez et supervisez votre plateforme IntraSphere
                </p>
            </div>
        </div>

        <!-- Statistiques système -->
        <div class="max-w-7xl mx-auto mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Vue d'ensemble du système</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white/80 text-sm font-medium">Utilisateurs actifs</h3>
                        <i class="fas fa-users text-2xl text-green-400"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-1" id="active-users">-</div>
                    <div class="text-white/60 text-xs">Total: <span id="total-users">-</span></div>
                </div>
                
                <div class="stat-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white/80 text-sm font-medium">Contenu</h3>
                        <i class="fas fa-file-alt text-2xl text-blue-400"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-1" id="total-content">-</div>
                    <div class="text-white/60 text-xs">Documents, annonces, formations</div>
                </div>
                
                <div class="stat-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white/80 text-sm font-medium">Messages</h3>
                        <i class="fas fa-envelope text-2xl text-purple-400"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-1" id="total-messages">-</div>
                    <div class="text-white/60 text-xs">Cette semaine: <span id="week-messages">-</span></div>
                </div>
                
                <div class="stat-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white/80 text-sm font-medium">Stockage</h3>
                        <i class="fas fa-hdd text-2xl text-yellow-400"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-1" id="storage-used">-</div>
                    <div class="text-white/60 text-xs">Disponible: <span id="storage-free">-</span></div>
                </div>
            </div>
        </div>

        <!-- Accès rapides -->
        <div class="max-w-7xl mx-auto mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Accès rapides</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="/admin/users" class="admin-card p-6 group block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-4xl text-blue-400 group-hover:text-blue-300">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="badge px-3 py-1 text-xs text-white" id="pending-users">0</div>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Gestion Utilisateurs</h3>
                    <p class="text-white/70 text-sm">
                        Administrer les comptes, rôles et permissions
                    </p>
                </a>
                
                <a href="/admin/permissions" class="admin-card p-6 group block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-4xl text-green-400 group-hover:text-green-300">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="badge px-3 py-1 text-xs text-white">OK</div>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Permissions</h3>
                    <p class="text-white/70 text-sm">
                        Configurer les droits d'accès et autorisations
                    </p>
                </a>
                
                <a href="/admin/system" class="admin-card p-6 group block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-4xl text-purple-400 group-hover:text-purple-300">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="badge px-3 py-1 text-xs text-white" id="system-status">-</div>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Configuration</h3>
                    <p class="text-white/70 text-sm">
                        Paramètres système et informations serveur
                    </p>
                </a>
                
                <a href="/admin/logs" class="admin-card p-6 group block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-4xl text-orange-400 group-hover:text-orange-300">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <div class="badge px-3 py-1 text-xs text-white" id="error-count">0</div>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Journaux</h3>
                    <p class="text-white/70 text-sm">
                        Logs système, erreurs et activité
                    </p>
                </a>
                
                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-4xl text-red-400">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <button 
                            onclick="toggleMaintenanceMode()" 
                            class="badge px-3 py-1 text-xs text-white hover:bg-white/30"
                            id="maintenance-btn"
                        >
                            OFF
                        </button>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Maintenance</h3>
                    <p class="text-white/70 text-sm">
                        Activer/désactiver le mode maintenance
                    </p>
                </div>
                
                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-4xl text-cyan-400">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="badge px-3 py-1 text-xs text-white">Live</div>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Statistiques</h3>
                    <p class="text-white/70 text-sm">
                        Analytics et métriques d'utilisation
                    </p>
                </div>
            </div>
        </div>

        <!-- Activité récente et alertes -->
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Alertes système -->
                <div class="glass p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">
                        <i class="fas fa-bell mr-2"></i>
                        Alertes Système
                    </h3>
                    <div id="system-alerts" class="space-y-4">
                        <!-- Les alertes seront chargées ici -->
                    </div>
                </div>
                
                <!-- Activité récente -->
                <div class="glass p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">
                        <i class="fas fa-history mr-2"></i>
                        Activité Récente
                    </h3>
                    <div id="recent-activity" class="space-y-4">
                        <!-- L'activité sera chargée ici -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let maintenanceMode = false;
        
        // Charger les statistiques admin
        async function loadAdminStats() {
            try {
                const response = await fetch('/api/stats');
                const stats = await response.json();
                
                document.getElementById('total-users').textContent = stats.totalUsers || 0;
                document.getElementById('active-users').textContent = stats.activeUsers || 0;
                document.getElementById('total-content').textContent = 
                    (stats.totalAnnouncements || 0) + (stats.totalDocuments || 0) + (stats.totalTrainings || 0);
                document.getElementById('total-messages').textContent = stats.totalMessages || 0;
                document.getElementById('week-messages').textContent = stats.weekMessages || 0;
                
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            }
        }
        
        // Charger les informations système
        async function loadSystemInfo() {
            try {
                const response = await fetch('/api/admin/system-info');
                const info = await response.json();
                
                document.getElementById('storage-used').textContent = info.disk_usage?.used || '-';
                document.getElementById('storage-free').textContent = info.disk_usage?.free || '-';
                document.getElementById('system-status').textContent = 'OK';
                
            } catch (error) {
                console.error('Erreur lors du chargement des infos système:', error);
                document.getElementById('system-status').textContent = 'ERR';
            }
        }
        
        // Basculer le mode maintenance
        async function toggleMaintenanceMode() {
            try {
                const response = await fetch('/api/admin/maintenance-mode', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ enabled: !maintenanceMode })
                });
                
                const result = await response.json();
                if (response.ok) {
                    maintenanceMode = result.maintenance_mode;
                    document.getElementById('maintenance-btn').textContent = maintenanceMode ? 'ON' : 'OFF';
                    document.getElementById('maintenance-btn').className = 
                        `badge px-3 py-1 text-xs text-white hover:bg-white/30 ${maintenanceMode ? 'bg-red-500/30' : ''}`;
                    
                    alert(result.message);
                }
            } catch (error) {
                console.error('Erreur lors du basculement maintenance:', error);
                alert('Erreur lors du basculement du mode maintenance');
            }
        }
        
        // Charger les alertes système
        async function loadSystemAlerts() {
            const alertsContainer = document.getElementById('system-alerts');
            
            // Exemple d'alertes (à remplacer par de vraies données)
            const sampleAlerts = [
                {
                    type: 'success',
                    icon: 'fas fa-check-circle',
                    title: 'Système opérationnel',
                    message: 'Tous les services fonctionnent normalement',
                    time: 'Maintenant'
                },
                {
                    type: 'warning',
                    icon: 'fas fa-exclamation-triangle',
                    title: 'Espace disque',
                    message: 'L\'espace disque atteint 75% de sa capacité',
                    time: 'Il y a 2h'
                }
            ];
            
            alertsContainer.innerHTML = sampleAlerts.map(alert => `
                <div class="admin-card ${alert.type === 'warning' ? 'warning-card' : 'success-card'} p-4">
                    <div class="flex items-start space-x-3">
                        <div class="text-xl ${alert.type === 'warning' ? 'text-yellow-400' : 'text-green-400'}">
                            <i class="${alert.icon}"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-medium mb-1">${alert.title}</h4>
                            <p class="text-white/70 text-sm mb-2">${alert.message}</p>
                            <span class="text-white/50 text-xs">${alert.time}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Charger l'activité récente
        async function loadRecentActivity() {
            const activityContainer = document.getElementById('recent-activity');
            
            // Exemple d'activité (à remplacer par de vraies données)
            const sampleActivity = [
                {
                    icon: 'fas fa-user-plus',
                    title: 'Nouvel utilisateur',
                    message: 'marie.dupont a rejoint la plateforme',
                    time: 'Il y a 10 min'
                },
                {
                    icon: 'fas fa-file-upload',
                    title: 'Document ajouté',
                    message: 'Politique de sécurité mise à jour',
                    time: 'Il y a 1h'
                },
                {
                    icon: 'fas fa-cog',
                    title: 'Configuration',
                    message: 'Permissions modifiées par admin',
                    time: 'Il y a 2h'
                }
            ];
            
            activityContainer.innerHTML = sampleActivity.map(activity => `
                <div class="admin-card p-4">
                    <div class="flex items-start space-x-3">
                        <div class="text-lg text-white/60">
                            <i class="${activity.icon}"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-medium mb-1">${activity.title}</h4>
                            <p class="text-white/70 text-sm mb-2">${activity.message}</p>
                            <span class="text-white/50 text-xs">${activity.time}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Chargement initial
        document.addEventListener('DOMContentLoaded', () => {
            loadAdminStats();
            loadSystemInfo();
            loadSystemAlerts();
            loadRecentActivity();
            
            // Actualisation automatique toutes les 30 secondes
            setInterval(() => {
                loadAdminStats();
                loadSystemInfo();
            }, 30000);
        });
    </script>
</body>
</html>