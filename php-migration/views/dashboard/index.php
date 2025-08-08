<?php
$title = 'Tableau de bord - IntraSphere';
$description = 'Vue d\'ensemble de votre espace de travail';
$user = currentUser();
ob_start();
?>

<div class="max-w-7xl mx-auto">
    <!-- En-tête de bienvenue -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">
            Bonjour, <?= h($user['name']) ?> 👋
        </h1>
        <p class="text-gray-400">
            Voici un aperçu de votre espace de travail aujourd'hui
        </p>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Annonces -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Annonces</p>
                    <p class="text-2xl font-bold text-white" id="stats-announcements">-</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i data-lucide="megaphone" class="w-6 h-6 text-purple-400"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs text-green-400" id="stats-announcements-trend">-</span>
            </div>
        </div>
        
        <!-- Messages -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Messages</p>
                    <p class="text-2xl font-bold text-white" id="stats-messages">-</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i data-lucide="mail" class="w-6 h-6 text-blue-400"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs text-orange-400" id="stats-messages-unread">-</span>
            </div>
        </div>
        
        <!-- Documents -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Documents</p>
                    <p class="text-2xl font-bold text-white" id="stats-documents">-</p>
                </div>
                <div class="w-12 h-12 bg-green-500 bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-green-400"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs text-blue-400" id="stats-documents-recent">-</span>
            </div>
        </div>
        
        <!-- Formations -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Formations</p>
                    <p class="text-2xl font-bold text-white" id="stats-trainings">-</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-6 h-6 text-yellow-400"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs text-purple-400" id="stats-trainings-upcoming">-</span>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal en 2 colonnes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Annonces importantes -->
            <section class="glass-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <i data-lucide="megaphone" class="w-5 h-5 mr-2 text-purple-400"></i>
                        Annonces importantes
                    </h2>
                    <a href="/announcements" class="text-sm text-purple-400 hover:text-purple-300 transition-colors">
                        Voir tout
                    </a>
                </div>
                
                <div id="important-announcements" class="space-y-4">
                    <!-- Contenu chargé dynamiquement -->
                    <div class="animate-pulse">
                        <div class="h-4 bg-gray-600 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-700 rounded w-1/2"></div>
                    </div>
                </div>
            </section>
            
            <!-- Activité récente -->
            <section class="glass-card p-6">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                    <i data-lucide="activity" class="w-5 h-5 mr-2 text-blue-400"></i>
                    Activité récente
                </h2>
                
                <div id="recent-activity" class="space-y-4">
                    <!-- Contenu chargé dynamiquement -->
                    <div class="animate-pulse space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-600 rounded-full"></div>
                            <div class="flex-1">
                                <div class="h-3 bg-gray-600 rounded w-1/2 mb-1"></div>
                                <div class="h-2 bg-gray-700 rounded w-1/4"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Sidebar droite -->
        <div class="space-y-8">
            <!-- Événements à venir -->
            <section class="glass-card p-6">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i data-lucide="calendar" class="w-5 h-5 mr-2 text-green-400"></i>
                    Événements à venir
                </h2>
                
                <div id="upcoming-events" class="space-y-3">
                    <!-- Contenu chargé dynamiquement -->
                    <div class="animate-pulse">
                        <div class="h-3 bg-gray-600 rounded w-full mb-2"></div>
                        <div class="h-2 bg-gray-700 rounded w-2/3"></div>
                    </div>
                </div>
            </section>
            
            <!-- Messages récents -->
            <section class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i data-lucide="message-circle" class="w-5 h-5 mr-2 text-blue-400"></i>
                        Messages récents
                    </h2>
                    <span class="badge badge-primary" id="unread-messages-count">0</span>
                </div>
                
                <div id="recent-messages" class="space-y-3">
                    <!-- Contenu chargé dynamiquement -->
                    <div class="animate-pulse">
                        <div class="h-3 bg-gray-600 rounded w-3/4 mb-1"></div>
                        <div class="h-2 bg-gray-700 rounded w-1/2"></div>
                    </div>
                </div>
            </section>
            
            <!-- Liens rapides -->
            <section class="glass-card p-6">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i data-lucide="zap" class="w-5 h-5 mr-2 text-yellow-400"></i>
                    Liens rapides
                </h2>
                
                <div class="space-y-2">
                    <a href="/announcements/create" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white hover:bg-opacity-5 transition-colors">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-purple-400"></i>
                        <span class="text-sm">Nouvelle annonce</span>
                    </a>
                    
                    <a href="/messages/compose" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white hover:bg-opacity-5 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4 text-blue-400"></i>
                        <span class="text-sm">Nouveau message</span>
                    </a>
                    
                    <a href="/documents/upload" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white hover:bg-opacity-5 transition-colors">
                        <i data-lucide="upload" class="w-4 h-4 text-green-400"></i>
                        <span class="text-sm">Upload document</span>
                    </a>
                    
                    <a href="/profile" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white hover:bg-opacity-5 transition-colors">
                        <i data-lucide="user" class="w-4 h-4 text-orange-400"></i>
                        <span class="text-sm">Mon profil</span>
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Charger les statistiques
    loadDashboardStats();
    
    // Charger le contenu
    loadImportantAnnouncements();
    loadUpcomingEvents();
    loadRecentMessages();
    loadRecentActivity();
    
    // Mettre à jour toutes les 5 minutes
    setInterval(() => {
        loadDashboardStats();
        loadRecentMessages();
    }, 300000);
});

async function loadDashboardStats() {
    try {
        const stats = await api.get('/api/stats');
        
        document.getElementById('stats-announcements').textContent = stats.totalAnnouncements || 0;
        document.getElementById('stats-messages').textContent = stats.totalMessages || 0;
        document.getElementById('stats-documents').textContent = stats.totalDocuments || 0;
        document.getElementById('stats-trainings').textContent = stats.totalTrainings || 0;
        
        // Tendances
        document.getElementById('stats-announcements-trend').textContent = 
            `${stats.newAnnouncements || 0} cette semaine`;
        document.getElementById('stats-messages-unread').textContent = 
            `${stats.unreadMessages || 0} non lus`;
        document.getElementById('stats-documents-recent').textContent = 
            `${stats.recentDocuments || 0} récents`;
        document.getElementById('stats-trainings-upcoming').textContent = 
            `${stats.upcomingTrainings || 0} à venir`;
            
    } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
    }
}

async function loadImportantAnnouncements() {
    try {
        const announcements = await api.get('/api/announcements?important=true&limit=3');
        const container = document.getElementById('important-announcements');
        
        if (announcements.length === 0) {
            container.innerHTML = '<p class="text-gray-400 text-sm">Aucune annonce importante</p>';
            return;
        }
        
        container.innerHTML = announcements.map(ann => `
            <div class="p-4 bg-purple-500 bg-opacity-10 border border-purple-500 border-opacity-20 rounded-lg">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-medium text-white mb-1">${ann.title}</h3>
                        <p class="text-sm text-gray-300 mb-2">${truncateText(ann.content, 100)}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-400">
                            <span>Par ${ann.author_name}</span>
                            <span>${formatDate(ann.created_at)}</span>
                        </div>
                    </div>
                    <span class="badge badge-primary">${ann.type}</span>
                </div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Erreur lors du chargement des annonces:', error);
    }
}

async function loadUpcomingEvents() {
    try {
        const events = await api.get('/api/events/upcoming?limit=5');
        const container = document.getElementById('upcoming-events');
        
        if (events.length === 0) {
            container.innerHTML = '<p class="text-gray-400 text-sm">Aucun événement à venir</p>';
            return;
        }
        
        container.innerHTML = events.map(event => `
            <div class="flex items-center space-x-3 p-3 bg-green-500 bg-opacity-5 rounded-lg">
                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-white">${event.title}</p>
                    <p class="text-xs text-gray-400">${formatDate(event.date)}</p>
                </div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Erreur lors du chargement des événements:', error);
    }
}

async function loadRecentMessages() {
    try {
        const messages = await api.get('/api/messages?limit=5');
        const container = document.getElementById('recent-messages');
        const unreadCount = messages.filter(m => !m.is_read).length;
        
        document.getElementById('unread-messages-count').textContent = unreadCount;
        
        if (messages.length === 0) {
            container.innerHTML = '<p class="text-gray-400 text-sm">Aucun message</p>';
            return;
        }
        
        container.innerHTML = messages.map(msg => `
            <div class="flex items-center space-x-3 p-3 hover:bg-white hover:bg-opacity-5 rounded-lg cursor-pointer ${!msg.is_read ? 'bg-blue-500 bg-opacity-5' : ''}">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <span class="text-xs font-semibold text-white">${msg.sender_name.charAt(0)}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">${msg.subject}</p>
                    <p class="text-xs text-gray-400">${msg.sender_name} • ${formatDate(msg.created_at)}</p>
                </div>
                ${!msg.is_read ? '<div class="w-2 h-2 bg-blue-400 rounded-full"></div>' : ''}
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Erreur lors du chargement des messages:', error);
    }
}

async function loadRecentActivity() {
    try {
        // Simulation d'activité récente
        const activities = [
            { type: 'announcement', text: 'Nouvelle annonce publiée', time: '2h' },
            { type: 'document', text: 'Document mis à jour', time: '4h' },
            { type: 'training', text: 'Formation ajoutée', time: '1j' }
        ];
        
        const container = document.getElementById('recent-activity');
        
        container.innerHTML = activities.map(activity => `
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-600 bg-opacity-30 rounded-full flex items-center justify-center">
                    <i data-lucide="${getActivityIcon(activity.type)}" class="w-4 h-4 text-gray-400"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-white">${activity.text}</p>
                    <p class="text-xs text-gray-400">Il y a ${activity.time}</p>
                </div>
            </div>
        `).join('');
        
        lucide.createIcons();
        
    } catch (error) {
        console.error('Erreur lors du chargement de l\'activité:', error);
    }
}

function getActivityIcon(type) {
    const icons = {
        'announcement': 'megaphone',
        'document': 'file-text',
        'training': 'graduation-cap',
        'message': 'mail'
    };
    return icons[type] || 'activity';
}

function truncateText(text, length) {
    return text.length > length ? text.substring(0, length) + '...' : text;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 3600000) { // < 1 heure
        return Math.floor(diff / 60000) + ' min';
    } else if (diff < 86400000) { // < 1 jour
        return Math.floor(diff / 3600000) + 'h';
    } else {
        return Math.floor(diff / 86400000) + 'j';
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout/app.php';
?>