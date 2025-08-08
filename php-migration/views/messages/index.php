<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Messagerie - IntraSphere' ?></title>
    <meta name="description" content="<?= $description ?? 'Votre messagerie interne' ?>">
    
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
        
        .message-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .message-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(4px);
        }
        
        .message-item.unread {
            border-color: rgba(139, 92, 246, 0.5);
            background: rgba(139, 92, 246, 0.1);
        }
        
        .sidebar-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        
        .sidebar-item.active {
            background: rgba(139, 92, 246, 0.3);
            border-color: rgba(139, 92, 246, 0.5);
        }
        
        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.2);
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
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .unread-badge {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            min-width: 20px;
            text-align: center;
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
                <span class="sidebar-item px-3 py-1 text-sm text-white/80">
                    <i class="fas fa-envelope mr-1"></i>Messagerie
                </span>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="sidebar-item px-3 py-2 text-sm text-white/90">
                    <i class="fas fa-user mr-2"></i>
                    <?= htmlspecialchars($user['name'] ?? 'Utilisateur') ?>
                </div>
                <span class="unread-badge" id="unread-count">0</span>
                <a href="/logout" class="sidebar-item px-4 py-2 text-white hover:text-white/80">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="pt-24 pb-8 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="glass p-6 mb-6">
                        <h2 class="text-xl font-bold text-white mb-4 text-center floating-animation">
                            <i class="fas fa-comments mr-2"></i>
                            Messagerie
                        </h2>
                        
                        <!-- Bouton nouveau message -->
                        <a href="/messages/compose" class="btn-primary w-full py-3 px-4 text-center text-white font-semibold mb-4 block">
                            <i class="fas fa-plus mr-2"></i>
                            Nouveau message
                        </a>
                        
                        <!-- Menu navigation -->
                        <div class="space-y-2">
                            <button class="sidebar-item active w-full px-4 py-3 text-left text-white" data-type="inbox">
                                <i class="fas fa-inbox mr-3"></i>
                                Boîte de réception
                                <span class="unread-badge float-right" id="inbox-count">0</span>
                            </button>
                            
                            <button class="sidebar-item w-full px-4 py-3 text-left text-white" data-type="sent">
                                <i class="fas fa-paper-plane mr-3"></i>
                                Messages envoyés
                            </button>
                            
                            <button class="sidebar-item w-full px-4 py-3 text-left text-white" data-type="conversations">
                                <i class="fas fa-users mr-3"></i>
                                Conversations
                            </button>
                        </div>
                    </div>
                    
                    <!-- Contacts récents -->
                    <div class="glass p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">
                            <i class="fas fa-user-friends mr-2"></i>
                            Contacts récents
                        </h3>
                        <div id="recent-contacts" class="space-y-2">
                            <!-- Les contacts seront chargés ici -->
                        </div>
                    </div>
                </div>

                <!-- Zone principale -->
                <div class="lg:col-span-3">
                    <!-- Barre de recherche -->
                    <div class="glass p-4 mb-6">
                        <div class="search-box flex items-center px-4 py-3">
                            <i class="fas fa-search text-white/60 mr-3"></i>
                            <input 
                                type="text" 
                                id="search-input"
                                placeholder="Rechercher dans les messages..." 
                                class="bg-transparent text-white placeholder-white/60 flex-1 outline-none"
                            >
                        </div>
                    </div>
                    
                    <!-- Liste des messages -->
                    <div class="glass p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-white" id="section-title">
                                <i class="fas fa-inbox mr-2"></i>
                                Boîte de réception
                            </h3>
                            <div class="flex items-center space-x-2">
                                <button id="refresh-btn" class="sidebar-item px-3 py-2 text-white hover:text-white/80">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button id="mark-all-read-btn" class="sidebar-item px-3 py-2 text-white hover:text-white/80" title="Tout marquer comme lu">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div id="messages-container">
                            <!-- Skeleton loader -->
                            <div class="space-y-4">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <div class="message-item p-4 animate-pulse">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-10 h-10 bg-white/20 rounded-full"></div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="h-4 bg-white/20 rounded w-1/4"></div>
                                                <div class="h-3 bg-white/10 rounded w-20"></div>
                                            </div>
                                            <div class="h-4 bg-white/20 rounded w-1/2 mb-2"></div>
                                            <div class="h-3 bg-white/10 rounded w-3/4"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="flex justify-center mt-6">
                            <div class="sidebar-item px-6 py-3" id="pagination">
                                <!-- La pagination sera générée par JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentType = 'inbox';
        let currentPage = 1;
        let currentSearch = '';
        
        // Charger les messages
        async function loadMessages(type = 'inbox', page = 1, search = '') {
            try {
                let url = `/api/messages?type=${type}&page=${page}&limit=10`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                renderMessages(data.messages || data);
                renderPagination(data.pagination);
                updateSectionTitle(type);
            } catch (error) {
                console.error('Erreur lors du chargement des messages:', error);
                document.getElementById('messages-container').innerHTML = `
                    <div class="message-item p-8 text-center">
                        <div class="text-6xl text-white/40 mb-4">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">
                            Erreur de chargement
                        </h3>
                        <p class="text-white/70">
                            Impossible de charger les messages. Veuillez réessayer.
                        </p>
                    </div>
                `;
            }
        }
        
        // Rendu des messages
        function renderMessages(messages) {
            const container = document.getElementById('messages-container');
            
            if (!messages || messages.length === 0) {
                container.innerHTML = `
                    <div class="message-item p-8 text-center">
                        <div class="text-6xl text-white/40 mb-4">
                            <i class="fas fa-envelope-open"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">
                            Aucun message
                        </h3>
                        <p class="text-white/70">
                            Vous n'avez pas encore de messages dans cette section.
                        </p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = messages.map(message => {
                const date = new Date(message.created_at);
                const isToday = date.toDateString() === new Date().toDateString();
                const timeStr = isToday ? 
                    date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) :
                    date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
                
                const isUnread = !message.is_read && currentType === 'inbox';
                const senderName = currentType === 'sent' ? 
                    message.recipient_name : 
                    message.sender_name;
                
                return `
                    <div class="message-item ${isUnread ? 'unread' : ''} p-4 cursor-pointer mb-4" onclick="viewMessage('${message.id}')">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-blue-400 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-white font-semibold truncate">
                                        ${senderName || 'Utilisateur inconnu'}
                                        ${isUnread ? '<span class="ml-2 w-2 h-2 bg-purple-400 rounded-full inline-block"></span>' : ''}
                                    </h4>
                                    <span class="text-white/60 text-sm whitespace-nowrap ml-4">
                                        ${timeStr}
                                    </span>
                                </div>
                                <h5 class="text-white/90 font-medium mb-1 truncate">
                                    ${message.subject}
                                </h5>
                                <p class="text-white/70 text-sm line-clamp-2">
                                    ${message.content.substring(0, 150)}${message.content.length > 150 ? '...' : ''}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Charger le nombre de messages non lus
        async function loadUnreadCount() {
            try {
                const response = await fetch('/api/messages/unread-count');
                const data = await response.json();
                
                const count = data.unread_count || 0;
                document.getElementById('unread-count').textContent = count;
                document.getElementById('inbox-count').textContent = count;
                
                // Masquer le badge si pas de messages non lus
                if (count === 0) {
                    document.getElementById('unread-count').style.display = 'none';
                    document.getElementById('inbox-count').style.display = 'none';
                }
            } catch (error) {
                console.error('Erreur lors du chargement du compteur:', error);
            }
        }
        
        // Mettre à jour le titre de section
        function updateSectionTitle(type) {
            const titles = {
                'inbox': '<i class="fas fa-inbox mr-2"></i>Boîte de réception',
                'sent': '<i class="fas fa-paper-plane mr-2"></i>Messages envoyés',
                'conversations': '<i class="fas fa-users mr-2"></i>Conversations'
            };
            
            document.getElementById('section-title').innerHTML = titles[type] || titles['inbox'];
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
        
        // Voir un message
        function viewMessage(id) {
            window.location.href = `/messages/${id}`;
        }
        
        // Marquer tous les messages comme lus
        async function markAllAsRead() {
            if (currentType !== 'inbox') return;
            
            try {
                await fetch('/api/messages/bulk-read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mark_all: true })
                });
                
                loadMessages(currentType, currentPage, currentSearch);
                loadUnreadCount();
            } catch (error) {
                console.error('Erreur lors de la mise à jour:', error);
            }
        }
        
        // Gestionnaires d'événements
        document.getElementById('search-input').addEventListener('input', (e) => {
            currentSearch = e.target.value;
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                loadMessages(currentType, 1, currentSearch);
            }, 500);
        });
        
        // Navigation sidebar
        document.querySelectorAll('[data-type]').forEach(button => {
            button.addEventListener('click', (e) => {
                // Retirer la classe active
                document.querySelectorAll('[data-type]').forEach(b => b.classList.remove('active'));
                
                // Ajouter la classe active au bouton cliqué
                e.currentTarget.classList.add('active');
                
                currentType = e.currentTarget.dataset.type;
                currentPage = 1;
                loadMessages(currentType, currentPage, currentSearch);
            });
        });
        
        // Bouton actualiser
        document.getElementById('refresh-btn').addEventListener('click', () => {
            loadMessages(currentType, currentPage, currentSearch);
            loadUnreadCount();
        });
        
        // Bouton marquer tout comme lu
        document.getElementById('mark-all-read-btn').addEventListener('click', markAllAsRead);
        
        // Chargement initial
        document.addEventListener('DOMContentLoaded', () => {
            loadMessages();
            loadUnreadCount();
            
            // Actualisation automatique toutes les 30 secondes
            setInterval(() => {
                loadUnreadCount();
                if (currentType === 'inbox') {
                    loadMessages(currentType, currentPage, currentSearch);
                }
            }, 30000);
        });
    </script>
</body>
</html>