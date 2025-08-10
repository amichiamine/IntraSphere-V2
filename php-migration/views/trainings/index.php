<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Formations - IntraSphere' ?></title>
    <meta name="description" content="<?= $description ?? 'Catalogue des formations disponibles' ?>">
    
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
        
        .training-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        
        .training-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.4);
        }
        
        .training-card.mandatory {
            border-color: rgba(239, 68, 68, 0.5);
            background: rgba(239, 68, 68, 0.1);
        }
        
        .badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
        }
        
        .badge.mandatory {
            background: rgba(239, 68, 68, 0.3);
            border-color: rgba(239, 68, 68, 0.5);
        }
        
        .badge.available {
            background: rgba(34, 197, 94, 0.3);
            border-color: rgba(34, 197, 94, 0.5);
        }
        
        .filter-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .filter-btn.active {
            background: rgba(139, 92, 246, 0.3);
            border-color: rgba(139, 92, 246, 0.5);
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
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .progress-bar {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: linear-gradient(135deg, #34d399, #10b981);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
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
                    <i class="fas fa-graduation-cap mr-1"></i>Formations
                </span>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="/trainings/my-trainings" class="badge px-3 py-2 text-sm text-white hover:text-white/80">
                    <i class="fas fa-user-graduate mr-2"></i>Mes formations
                </a>
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
        <div class="max-w-7xl mx-auto mb-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-4 floating-animation">
                    <i class="fas fa-graduation-cap mr-3"></i>
                    Catalogue de Formations
                </h1>
                <p class="text-white/80 text-lg">
                    Développez vos compétences avec nos formations professionnelles
                </p>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="glass p-4 text-center">
                    <div class="text-2xl font-bold text-white mb-1" id="total-trainings">-</div>
                    <div class="text-white/70 text-sm">Formations disponibles</div>
                </div>
                <div class="glass p-4 text-center">
                    <div class="text-2xl font-bold text-white mb-1" id="my-trainings-count">-</div>
                    <div class="text-white/70 text-sm">Mes inscriptions</div>
                </div>
                <div class="glass p-4 text-center">
                    <div class="text-2xl font-bold text-white mb-1" id="upcoming-count">-</div>
                    <div class="text-white/70 text-sm">À venir</div>
                </div>
                <div class="glass p-4 text-center">
                    <div class="text-2xl font-bold text-white mb-1" id="mandatory-count">-</div>
                    <div class="text-white/70 text-sm">Obligatoires</div>
                </div>
            </div>
            
            <!-- Barre de recherche et filtres -->
            <div class="glass p-6 mb-6">
                <div class="flex flex-col lg:flex-row gap-4 items-center mb-4">
                    <div class="search-box flex-1 flex items-center px-4 py-3">
                        <i class="fas fa-search text-white/60 mr-3"></i>
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Rechercher une formation..." 
                            class="bg-transparent text-white placeholder-white/60 flex-1 outline-none"
                        >
                    </div>
                    
                    <div class="flex gap-3">
                        <?php if (isset($user) && in_array($user['role'] ?? '', ['admin', 'moderator'])): ?>
                        <a href="/trainings/create" class="btn-primary px-6 py-3 text-white font-semibold">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle formation
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Filtres -->
                <div class="flex flex-wrap gap-3" id="training-filters">
                    <button class="filter-btn active px-4 py-2 text-white" data-filter="">
                        <i class="fas fa-th mr-2"></i>Toutes
                    </button>
                    <button class="filter-btn px-4 py-2 text-white" data-filter="upcoming">
                        <i class="fas fa-clock mr-2"></i>À venir
                    </button>
                    <button class="filter-btn px-4 py-2 text-white" data-filter="mandatory">
                        <i class="fas fa-exclamation-circle mr-2"></i>Obligatoires
                    </button>
                    <button class="filter-btn px-4 py-2 text-white" data-filter="available">
                        <i class="fas fa-door-open mr-2"></i>Inscriptions ouvertes
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des formations -->
        <div class="max-w-7xl mx-auto">
            <div id="trainings-container">
                <!-- Skeleton loader -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                    <div class="training-card p-6 animate-pulse">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="h-6 bg-white/20 rounded w-3/4 mb-2"></div>
                                <div class="h-4 bg-white/10 rounded w-1/2"></div>
                            </div>
                            <div class="h-6 w-20 bg-white/20 rounded-full"></div>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="h-4 bg-white/10 rounded"></div>
                            <div class="h-4 bg-white/10 rounded w-5/6"></div>
                        </div>
                        <div class="h-10 bg-white/20 rounded"></div>
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
        let currentFilter = '';
        let currentSearch = '';
        
        // Charger les formations
        async function loadTrainings(page = 1, filter = '', search = '') {
            try {
                let url = `/api/trainings?page=${page}&limit=12`;
                if (filter === 'upcoming') url += '&upcoming=true';
                else if (filter === 'mandatory') url += '&mandatory=true';
                else if (filter && filter !== 'available') url += `&category=${filter}`;
                
                if (search) url += `&search=${encodeURIComponent(search)}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                renderTrainings(data.trainings || data, filter);
                renderPagination(data.pagination);
            } catch (error) {
                console.error('Erreur lors du chargement des formations:', error);
                document.getElementById('trainings-container').innerHTML = `
                    <div class="col-span-full">
                        <div class="training-card p-8 text-center">
                            <div class="text-6xl text-white/40 mb-4">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                Erreur de chargement
                            </h3>
                            <p class="text-white/70">
                                Impossible de charger les formations. Veuillez réessayer.
                            </p>
                        </div>
                    </div>
                `;
            }
        }
        
        // Charger les statistiques
        async function loadStats() {
            try {
                const response = await fetch('/api/trainings/stats');
                const stats = await response.json();
                
                document.getElementById('total-trainings').textContent = stats.total || 0;
                document.getElementById('upcoming-count').textContent = stats.upcoming || 0;
                document.getElementById('mandatory-count').textContent = stats.mandatory || 0;
                
                // Charger mes formations
                const myResponse = await fetch('/api/trainings/my-trainings');
                const myTrainings = await myResponse.json();
                document.getElementById('my-trainings-count').textContent = myTrainings.length || 0;
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            }
        }
        
        // Rendu des formations
        function renderTrainings(trainings, filter = '') {
            const container = document.getElementById('trainings-container');
            
            if (!trainings || trainings.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full">
                        <div class="training-card p-8 text-center">
                            <div class="text-6xl text-white/40 mb-4">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                Aucune formation trouvée
                            </h3>
                            <p class="text-white/70">
                                Il n'y a actuellement aucune formation correspondant à vos critères.
                            </p>
                        </div>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    ${trainings.map(training => {
                        const isMandatory = training.is_mandatory;
                        const startDate = training.start_date ? new Date(training.start_date) : null;
                        const endDate = training.end_date ? new Date(training.end_date) : null;
                        const now = new Date();
                        
                        const isUpcoming = startDate && startDate > now;
                        const isOngoing = startDate && endDate && startDate <= now && now <= endDate;
                        const isPast = endDate && endDate < now;
                        
                        let statusBadge = '';
                        if (isMandatory) {
                            statusBadge = '<span class="badge mandatory px-2 py-1 text-xs text-white">Obligatoire</span>';
                        } else if (isUpcoming) {
                            statusBadge = '<span class="badge available px-2 py-1 text-xs text-white">À venir</span>';
                        } else if (isOngoing) {
                            statusBadge = '<span class="badge px-2 py-1 text-xs text-white bg-blue-500/30">En cours</span>';
                        } else if (isPast) {
                            statusBadge = '<span class="badge px-2 py-1 text-xs text-white bg-gray-500/30">Terminé</span>';
                        } else {
                            statusBadge = '<span class="badge available px-2 py-1 text-xs text-white">Disponible</span>';
                        }
                        
                        const dateStr = startDate ? 
                            startDate.toLocaleDateString('fr-FR', { 
                                day: 'numeric', 
                                month: 'short', 
                                year: 'numeric' 
                            }) : 'Date non définie';
                        
                        const duration = startDate && endDate ? 
                            Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + ' jours' : 
                            'Durée non définie';
                        
                        const participantsInfo = training.max_participants ? 
                            `${training.current_participants || 0}/${training.max_participants}` : 
                            `${training.current_participants || 0}`;
                        
                        const progress = training.max_participants ? 
                            ((training.current_participants || 0) / training.max_participants) * 100 : 0;
                        
                        return `
                            <div class="training-card ${isMandatory ? 'mandatory' : ''} p-6 cursor-pointer" onclick="viewTraining('${training.id}')">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-white mb-2 truncate" title="${training.title}">
                                            ${training.title}
                                        </h3>
                                        <div class="text-white/70 text-sm mb-2">
                                            <i class="fas fa-user mr-2"></i>
                                            ${training.instructor_name || 'Instructeur non défini'}
                                        </div>
                                    </div>
                                    ${statusBadge}
                                </div>
                                
                                <p class="text-white/80 text-sm mb-4 line-clamp-3">
                                    ${training.description.substring(0, 120)}${training.description.length > 120 ? '...' : ''}
                                </p>
                                
                                <div class="space-y-3 mb-4">
                                    <div class="flex items-center text-sm text-white/70">
                                        <i class="fas fa-calendar w-5 mr-2"></i>
                                        <span>${dateStr}</span>
                                    </div>
                                    
                                    <div class="flex items-center text-sm text-white/70">
                                        <i class="fas fa-clock w-5 mr-2"></i>
                                        <span>${duration}</span>
                                    </div>
                                    
                                    ${training.location ? `
                                        <div class="flex items-center text-sm text-white/70">
                                            <i class="fas fa-map-marker-alt w-5 mr-2"></i>
                                            <span>${training.location}</span>
                                        </div>
                                    ` : ''}
                                    
                                    <div class="flex items-center text-sm text-white/70">
                                        <i class="fas fa-users w-5 mr-2"></i>
                                        <span>${participantsInfo} participants</span>
                                    </div>
                                    
                                    ${training.max_participants ? `
                                        <div class="progress-bar h-2">
                                            <div class="progress-fill" style="width: ${Math.min(progress, 100)}%"></div>
                                        </div>
                                    ` : ''}
                                </div>
                                
                                <div class="flex gap-2">
                                    <button 
                                        onclick="event.stopPropagation(); registerTraining('${training.id}')"
                                        class="flex-1 btn-primary px-4 py-2 text-center text-white text-sm font-medium ${isPast || (training.max_participants && training.current_participants >= training.max_participants) ? 'opacity-50 cursor-not-allowed' : ''}"
                                        ${isPast || (training.max_participants && training.current_participants >= training.max_participants) ? 'disabled' : ''}
                                    >
                                        <i class="fas fa-user-plus mr-2"></i>
                                        ${isPast ? 'Terminé' : (training.max_participants && training.current_participants >= training.max_participants) ? 'Complet' : 'S\'inscrire'}
                                    </button>
                                    
                                    <button 
                                        onclick="event.stopPropagation(); viewTraining('${training.id}')"
                                        class="btn-secondary px-4 py-2 text-white text-sm"
                                    >
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }
        
        // Pagination
        function renderPagination(pagination) {
            if (!pagination) return;
            
            const paginationDiv = document.getElementById('pagination');
            paginationDiv.innerHTML = `
                <span class="text-white/80">
                    Page ${pagination.current || currentPage} sur ${pagination.total || 1}
                </span>
            `;
        }
        
        // Voir une formation
        function viewTraining(id) {
            window.location.href = `/trainings/${id}`;
        }
        
        // S'inscrire à une formation
        async function registerTraining(id) {
            try {
                const response = await fetch(`/api/trainings/${id}/register`, {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    alert('Inscription réussie !');
                    loadTrainings(currentPage, currentFilter, currentSearch);
                } else {
                    alert(data.message || 'Erreur lors de l\'inscription');
                }
            } catch (error) {
                console.error('Erreur lors de l\'inscription:', error);
                alert('Erreur lors de l\'inscription');
            }
        }
        
        // Gestionnaires d'événements
        document.getElementById('search-input').addEventListener('input', (e) => {
            currentSearch = e.target.value;
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                loadTrainings(1, currentFilter, currentSearch);
            }, 500);
        });
        
        // Filtres
        document.querySelectorAll('[data-filter]').forEach(button => {
            button.addEventListener('click', (e) => {
                // Retirer la classe active
                document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
                
                // Ajouter la classe active au bouton cliqué
                e.target.classList.add('active');
                
                currentFilter = e.target.dataset.filter;
                loadTrainings(1, currentFilter, currentSearch);
            });
        });
        
        // Chargement initial
        document.addEventListener('DOMContentLoaded', () => {
            loadTrainings();
            loadStats();
        });
    </script>
</body>
</html>