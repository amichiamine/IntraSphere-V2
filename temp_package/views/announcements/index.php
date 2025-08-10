<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Annonces - IntraSphere' ?></title>
    <meta name="description" content="<?= $description ?? 'Consultez toutes les annonces de l\'entreprise' ?>">
    
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
        
        .announcement-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        
        .announcement-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.4);
        }
        
        .badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
        }
        
        .search-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
        }
        
        .search-box:focus-within {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.8), rgba(99, 102, 241, 0.8));
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.9), rgba(99, 102, 241, 0.9));
            transform: translateY(-1px);
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
    <!-- Navigation -->
    <nav class="glass fixed top-4 left-4 right-4 z-50 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="/dashboard" class="text-2xl font-bold text-white hover:text-white/80">
                    <i class="fas fa-globe mr-2"></i>IntraSphere
                </a>
                <span class="badge px-3 py-1 text-sm text-white/80">Annonces</span>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="badge px-3 py-2 text-sm text-white/90">
                    <i class="fas fa-user mr-2"></i>
                    <?= htmlspecialchars($user['name'] ?? 'Utilisateur') ?>
                </div>
                <a href="/logout" class="badge px-4 py-2 text-white hover:text-white/80">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="pt-24 pb-8 px-4">
        <!-- En-tête de section -->
        <div class="max-w-6xl mx-auto mb-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-4 floating-animation">
                    <i class="fas fa-bullhorn mr-3"></i>
                    Annonces
                </h1>
                <p class="text-white/80 text-lg">
                    Restez informé des dernières actualités de l'entreprise
                </p>
            </div>
            
            <!-- Barre de recherche et filtres -->
            <div class="glass p-6 mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="search-box flex-1 flex items-center px-4 py-3">
                        <i class="fas fa-search text-white/60 mr-3"></i>
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Rechercher dans les annonces..." 
                            class="bg-transparent text-white placeholder-white/60 flex-1 outline-none"
                        >
                    </div>
                    
                    <div class="flex gap-3">
                        <select id="type-filter" class="badge px-4 py-3 text-white bg-transparent outline-none">
                            <option value="">Tous les types</option>
                            <option value="info">Information</option>
                            <option value="important">Important</option>
                            <option value="event">Événement</option>
                            <option value="formation">Formation</option>
                        </select>
                        
                        <?php if (isset($user) && in_array($user['role'] ?? '', ['admin', 'moderator'])): ?>
                        <a href="/announcements/create" class="btn-primary px-6 py-3 text-white font-semibold">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle annonce
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des annonces -->
        <div class="max-w-6xl mx-auto">
            <div id="announcements-container">
                <!-- Skeleton loader -->
                <div class="space-y-6 mb-6">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                    <div class="announcement-card p-6 animate-pulse">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="h-6 bg-white/20 rounded w-3/4 mb-2"></div>
                                <div class="h-4 bg-white/10 rounded w-1/2"></div>
                            </div>
                            <div class="h-8 w-20 bg-white/20 rounded-full"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 bg-white/10 rounded"></div>
                            <div class="h-4 bg-white/10 rounded w-5/6"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-center mt-8">
                <div class="glass px-6 py-3" id="pagination">
                    <!-- La pagination sera générée par JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentType = '';
        let currentSearch = '';
        
        // Charger les annonces
        async function loadAnnouncements(page = 1, type = '', search = '') {
            try {
                let url = `/api/announcements?page=${page}&limit=10`;
                if (type) url += `&type=${type}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                renderAnnouncements(data.announcements || data);
                renderPagination(data.pagination);
            } catch (error) {
                console.error('Erreur lors du chargement des annonces:', error);
                document.getElementById('announcements-container').innerHTML = `
                    <div class="announcement-card p-8 text-center">
                        <div class="text-6xl text-white/40 mb-4">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">
                            Erreur de chargement
                        </h3>
                        <p class="text-white/70">
                            Impossible de charger les annonces. Veuillez réessayer.
                        </p>
                    </div>
                `;
            }
        }
        
        // Rendu des annonces
        function renderAnnouncements(announcements) {
            const container = document.getElementById('announcements-container');
            
            if (!announcements || announcements.length === 0) {
                container.innerHTML = `
                    <div class="announcement-card p-8 text-center">
                        <div class="text-6xl text-white/40 mb-4">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">
                            Aucune annonce trouvée
                        </h3>
                        <p class="text-white/70">
                            Il n'y a actuellement aucune annonce correspondant à vos critères.
                        </p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = announcements.map(announcement => {
                const typeColors = {
                    'info': 'bg-blue-500/80',
                    'important': 'bg-red-500/80',
                    'event': 'bg-green-500/80',
                    'formation': 'bg-purple-500/80'
                };
                
                const typeLabels = {
                    'info': 'Information',
                    'important': 'Important',
                    'event': 'Événement',
                    'formation': 'Formation'
                };
                
                const date = new Date(announcement.created_at).toLocaleDateString('fr-FR', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                
                return `
                    <div class="announcement-card p-6 mb-6 cursor-pointer" onclick="viewAnnouncement('${announcement.id}')">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-white mb-2">
                                    ${announcement.title}
                                </h3>
                                <div class="flex items-center text-white/70 text-sm mb-2">
                                    <i class="fas fa-user mr-2"></i>
                                    Par ${announcement.author_name || 'Auteur inconnu'}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-calendar mr-2"></i>
                                    ${date}
                                </div>
                            </div>
                            <span class="badge px-3 py-1 text-xs text-white ${typeColors[announcement.type] || 'bg-gray-500/80'}">
                                ${typeLabels[announcement.type] || announcement.type}
                            </span>
                        </div>
                        
                        <p class="text-white/80 mb-4 line-clamp-3">
                            ${announcement.content.substring(0, 200)}${announcement.content.length > 200 ? '...' : ''}
                        </p>
                        
                        <div class="flex items-center justify-between text-sm text-white/60">
                            <div class="flex items-center space-x-4">
                                <span><i class="fas fa-eye mr-1"></i> ${announcement.view_count || 0} vues</span>
                            </div>
                            <span class="text-white/80 hover:text-white">
                                Lire la suite <i class="fas fa-chevron-right ml-1"></i>
                            </span>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Pagination
        function renderPagination(pagination) {
            if (!pagination) return;
            
            const paginationDiv = document.getElementById('pagination');
            // Implémentation simple de pagination
            paginationDiv.innerHTML = `
                <span class="text-white/80">
                    Page ${pagination.current || currentPage} sur ${pagination.total || 1}
                </span>
            `;
        }
        
        // Voir une annonce
        function viewAnnouncement(id) {
            window.location.href = `/announcements/${id}`;
        }
        
        // Gestionnaires d'événements
        document.getElementById('search-input').addEventListener('input', (e) => {
            currentSearch = e.target.value;
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                loadAnnouncements(1, currentType, currentSearch);
            }, 500);
        });
        
        document.getElementById('type-filter').addEventListener('change', (e) => {
            currentType = e.target.value;
            loadAnnouncements(1, currentType, currentSearch);
        });
        
        // Chargement initial
        document.addEventListener('DOMContentLoaded', () => {
            loadAnnouncements();
        });
    </script>
</body>
</html>