<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Documents - IntraSphere' ?></title>
    <meta name="description" content="<?= $description ?? 'Gestionnaire de documents d\'entreprise' ?>">
    
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
        
        .document-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        
        .document-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.4);
        }
        
        .category-filter {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .category-filter.active {
            background: rgba(139, 92, 246, 0.3);
            border-color: rgba(139, 92, 246, 0.5);
        }
        
        .file-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
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
                <span class="category-filter px-3 py-1 text-sm text-white/80">Documents</span>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="category-filter px-3 py-2 text-sm text-white/90">
                    <i class="fas fa-user mr-2"></i>
                    <?= htmlspecialchars($user['name'] ?? 'Utilisateur') ?>
                </div>
                <a href="/logout" class="category-filter px-4 py-2 text-white hover:text-white/80">
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
                    <i class="fas fa-folder-open mr-3"></i>
                    Gestionnaire de Documents
                </h1>
                <p class="text-white/80 text-lg">
                    Accédez à tous les documents de l'entreprise en toute sécurité
                </p>
            </div>
            
            <!-- Barre de recherche et contrôles -->
            <div class="glass p-6 mb-6">
                <div class="flex flex-col lg:flex-row gap-4 items-center mb-4">
                    <div class="search-box flex-1 flex items-center px-4 py-3">
                        <i class="fas fa-search text-white/60 mr-3"></i>
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Rechercher un document..." 
                            class="bg-transparent text-white placeholder-white/60 flex-1 outline-none"
                        >
                    </div>
                    
                    <div class="flex gap-3">
                        <?php if (isset($user) && in_array($user['role'] ?? '', ['admin', 'moderator'])): ?>
                        <a href="/documents/upload" class="btn-primary px-6 py-3 text-white font-semibold">
                            <i class="fas fa-upload mr-2"></i>
                            Télécharger
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Filtres par catégorie -->
                <div class="flex flex-wrap gap-3" id="category-filters">
                    <button class="category-filter active px-4 py-2 text-white" data-category="">
                        <i class="fas fa-th mr-2"></i>Toutes
                    </button>
                    <button class="category-filter px-4 py-2 text-white" data-category="regulation">
                        <i class="fas fa-gavel mr-2"></i>Réglementation
                    </button>
                    <button class="category-filter px-4 py-2 text-white" data-category="policy">
                        <i class="fas fa-file-contract mr-2"></i>Politique
                    </button>
                    <button class="category-filter px-4 py-2 text-white" data-category="guide">
                        <i class="fas fa-book mr-2"></i>Guide
                    </button>
                    <button class="category-filter px-4 py-2 text-white" data-category="procedure">
                        <i class="fas fa-list-ol mr-2"></i>Procédure
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des documents -->
        <div class="max-w-7xl mx-auto">
            <div id="documents-container">
                <!-- Skeleton loader -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                    <div class="document-card p-6 animate-pulse">
                        <div class="flex items-start space-x-4 mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg"></div>
                            <div class="flex-1">
                                <div class="h-5 bg-white/20 rounded mb-2"></div>
                                <div class="h-4 bg-white/10 rounded w-2/3"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-3 bg-white/10 rounded"></div>
                            <div class="h-3 bg-white/10 rounded w-5/6"></div>
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
        let currentCategory = '';
        let currentSearch = '';
        
        // Charger les documents
        async function loadDocuments(page = 1, category = '', search = '') {
            try {
                let url = `/api/documents?page=${page}&limit=12`;
                if (category) url += `&category=${category}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                renderDocuments(data.documents || data);
                renderPagination(data.pagination);
            } catch (error) {
                console.error('Erreur lors du chargement des documents:', error);
                document.getElementById('documents-container').innerHTML = `
                    <div class="col-span-full">
                        <div class="document-card p-8 text-center">
                            <div class="text-6xl text-white/40 mb-4">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                Erreur de chargement
                            </h3>
                            <p class="text-white/70">
                                Impossible de charger les documents. Veuillez réessayer.
                            </p>
                        </div>
                    </div>
                `;
            }
        }
        
        // Rendu des documents
        function renderDocuments(documents) {
            const container = document.getElementById('documents-container');
            
            if (!documents || documents.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full">
                        <div class="document-card p-8 text-center">
                            <div class="text-6xl text-white/40 mb-4">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                Aucun document trouvé
                            </h3>
                            <p class="text-white/70">
                                Il n'y a actuellement aucun document correspondant à vos critères.
                            </p>
                        </div>
                    </div>
                `;
                return;
            }
            
            const getFileIcon = (fileName) => {
                if (!fileName) return 'fas fa-file';
                
                const ext = fileName.split('.').pop().toLowerCase();
                const iconMap = {
                    'pdf': 'fas fa-file-pdf text-red-400',
                    'doc': 'fas fa-file-word text-blue-400',
                    'docx': 'fas fa-file-word text-blue-400',
                    'xls': 'fas fa-file-excel text-green-400',
                    'xlsx': 'fas fa-file-excel text-green-400',
                    'ppt': 'fas fa-file-powerpoint text-orange-400',
                    'pptx': 'fas fa-file-powerpoint text-orange-400',
                    'txt': 'fas fa-file-alt text-gray-400',
                    'zip': 'fas fa-file-archive text-yellow-400',
                    'rar': 'fas fa-file-archive text-yellow-400'
                };
                
                return iconMap[ext] || 'fas fa-file text-white/60';
            };
            
            const categoryLabels = {
                'regulation': 'Réglementation',
                'policy': 'Politique',
                'guide': 'Guide',
                'procedure': 'Procédure'
            };
            
            container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    ${documents.map(doc => {
                        const uploadDate = new Date(doc.created_at).toLocaleDateString('fr-FR', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                        
                        const fileSize = doc.file_size ? 
                            (doc.file_size / 1024 / 1024).toFixed(1) + ' MB' : 
                            'Taille inconnue';
                        
                        return `
                            <div class="document-card p-6 cursor-pointer" onclick="viewDocument('${doc.id}')">
                                <div class="flex items-start space-x-4 mb-4">
                                    <div class="file-icon">
                                        <i class="${getFileIcon(doc.file_name)} text-2xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-white mb-1 truncate" title="${doc.title}">
                                            ${doc.title}
                                        </h3>
                                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-white/20 text-white/80 mb-2">
                                            ${categoryLabels[doc.category] || doc.category}
                                        </span>
                                    </div>
                                </div>
                                
                                ${doc.description ? `
                                    <p class="text-white/70 text-sm mb-4 line-clamp-2">
                                        ${doc.description}
                                    </p>
                                ` : ''}
                                
                                <div class="flex items-center justify-between text-sm text-white/60 mb-4">
                                    <div class="flex items-center space-x-3">
                                        <span><i class="fas fa-eye mr-1"></i> ${doc.view_count || 0}</span>
                                        <span><i class="fas fa-download mr-1"></i> ${doc.download_count || 0}</span>
                                    </div>
                                    <span class="text-xs">${fileSize}</span>
                                </div>
                                
                                <div class="flex items-center justify-between text-xs text-white/50">
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        ${uploadDate}
                                    </span>
                                    <span>
                                        v${doc.version || '1.0'}
                                    </span>
                                </div>
                                
                                <div class="mt-4 flex gap-2">
                                    <button 
                                        onclick="event.stopPropagation(); downloadDocument('${doc.id}')"
                                        class="flex-1 btn-primary px-4 py-2 text-center text-white text-sm font-medium"
                                    >
                                        <i class="fas fa-download mr-2"></i>
                                        Télécharger
                                    </button>
                                    <button 
                                        onclick="event.stopPropagation(); viewDocument('${doc.id}')"
                                        class="px-4 py-2 category-filter text-white text-sm"
                                    >
                                        <i class="fas fa-eye"></i>
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
        
        // Voir un document
        function viewDocument(id) {
            window.location.href = `/documents/${id}`;
        }
        
        // Télécharger un document
        async function downloadDocument(id) {
            try {
                const response = await fetch(`/api/documents/${id}/download`, {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.download_url) {
                    window.open(data.download_url, '_blank');
                } else {
                    alert('Impossible de télécharger le document');
                }
            } catch (error) {
                console.error('Erreur lors du téléchargement:', error);
                alert('Erreur lors du téléchargement');
            }
        }
        
        // Gestionnaires d'événements
        document.getElementById('search-input').addEventListener('input', (e) => {
            currentSearch = e.target.value;
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                loadDocuments(1, currentCategory, currentSearch);
            }, 500);
        });
        
        // Filtres par catégorie
        document.querySelectorAll('[data-category]').forEach(button => {
            button.addEventListener('click', (e) => {
                // Retirer la classe active de tous les boutons
                document.querySelectorAll('[data-category]').forEach(b => b.classList.remove('active'));
                
                // Ajouter la classe active au bouton cliqué
                e.target.classList.add('active');
                
                currentCategory = e.target.dataset.category;
                loadDocuments(1, currentCategory, currentSearch);
            });
        });
        
        // Chargement initial
        document.addEventListener('DOMContentLoaded', () => {
            loadDocuments();
        });
    </script>
</body>
</html>