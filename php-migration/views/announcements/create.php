<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Nouvelle annonce - IntraSphere' ?></title>
    <meta name="description" content="<?= $description ?? 'Créer une nouvelle annonce' ?>">
    
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
        
        .form-input {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            color: white;
        }
        
        .form-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(139, 92, 246, 0.5);
            outline: none;
        }
        
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
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
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .editor-toolbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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
                <a href="/announcements" class="badge px-3 py-1 text-sm text-white hover:text-white/80">
                    <i class="fas fa-arrow-left mr-1"></i>Retour aux annonces
                </a>
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
        <div class="max-w-4xl mx-auto">
            <!-- En-tête -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-4 floating-animation">
                    <i class="fas fa-bullhorn mr-3"></i>
                    Nouvelle Annonce
                </h1>
                <p class="text-white/80 text-lg">
                    Créez une nouvelle annonce pour informer vos collaborateurs
                </p>
            </div>

            <!-- Formulaire -->
            <div class="glass p-8">
                <form id="announcement-form" class="space-y-6">
                    <!-- Titre -->
                    <div>
                        <label class="block text-white font-semibold mb-2">
                            <i class="fas fa-heading mr-2"></i>
                            Titre de l'annonce *
                        </label>
                        <input 
                            type="text" 
                            id="title"
                            name="title"
                            required
                            placeholder="Saisissez le titre de l'annonce..." 
                            class="form-input w-full px-4 py-3 text-white placeholder-white/60"
                        >
                    </div>

                    <!-- Type et priorité -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-tag mr-2"></i>
                                Type d'annonce *
                            </label>
                            <select 
                                id="type"
                                name="type"
                                required
                                class="form-input w-full px-4 py-3 text-white"
                            >
                                <option value="">Sélectionnez un type</option>
                                <option value="info">Information</option>
                                <option value="important">Important</option>
                                <option value="event">Événement</option>
                                <option value="formation">Formation</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Priorité
                            </label>
                            <select 
                                id="priority"
                                name="priority"
                                class="form-input w-full px-4 py-3 text-white"
                            >
                                <option value="normal">Normale</option>
                                <option value="high">Élevée</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div>
                        <label class="block text-white font-semibold mb-2">
                            <i class="fas fa-align-left mr-2"></i>
                            Contenu de l'annonce *
                        </label>
                        
                        <!-- Barre d'outils simplifiée -->
                        <div class="editor-toolbar p-3 rounded-t-lg flex items-center space-x-2">
                            <button type="button" onclick="formatText('bold')" class="badge px-3 py-1 text-sm text-white hover:bg-white/30">
                                <i class="fas fa-bold"></i>
                            </button>
                            <button type="button" onclick="formatText('italic')" class="badge px-3 py-1 text-sm text-white hover:bg-white/30">
                                <i class="fas fa-italic"></i>
                            </button>
                            <button type="button" onclick="formatText('underline')" class="badge px-3 py-1 text-sm text-white hover:bg-white/30">
                                <i class="fas fa-underline"></i>
                            </button>
                            <div class="border-l border-white/20 h-6 mx-2"></div>
                            <button type="button" onclick="insertList('ul')" class="badge px-3 py-1 text-sm text-white hover:bg-white/30">
                                <i class="fas fa-list-ul"></i>
                            </button>
                            <button type="button" onclick="insertList('ol')" class="badge px-3 py-1 text-sm text-white hover:bg-white/30">
                                <i class="fas fa-list-ol"></i>
                            </button>
                        </div>
                        
                        <textarea 
                            id="content"
                            name="content"
                            required
                            rows="10"
                            placeholder="Rédigez le contenu de votre annonce..." 
                            class="form-input w-full px-4 py-3 text-white placeholder-white/60 rounded-t-none"
                        ></textarea>
                    </div>

                    <!-- Options avancées -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-calendar mr-2"></i>
                                Date de publication
                            </label>
                            <input 
                                type="datetime-local" 
                                id="publish_date"
                                name="publish_date"
                                class="form-input w-full px-4 py-3 text-white"
                            >
                            <p class="text-white/60 text-sm mt-1">Laisser vide pour publier immédiatement</p>
                        </div>

                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-eye mr-2"></i>
                                Visibilité
                            </label>
                            <select 
                                id="visibility"
                                name="visibility"
                                class="form-input w-full px-4 py-3 text-white"
                            >
                                <option value="all">Tous les utilisateurs</option>
                                <option value="employees">Employés seulement</option>
                                <option value="managers">Managers et plus</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cases à cocher -->
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="send_notification"
                                name="send_notification"
                                checked
                                class="w-4 h-4 text-purple-600 rounded"
                            >
                            <label for="send_notification" class="ml-3 text-white">
                                Envoyer une notification par email
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="pin_announcement"
                                name="pin_announcement"
                                class="w-4 h-4 text-purple-600 rounded"
                            >
                            <label for="pin_announcement" class="ml-3 text-white">
                                Épingler cette annonce en haut
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="allow_comments"
                                name="allow_comments"
                                checked
                                class="w-4 h-4 text-purple-600 rounded"
                            >
                            <label for="allow_comments" class="ml-3 text-white">
                                Autoriser les commentaires
                            </label>
                        </div>
                    </div>

                    <!-- Aperçu -->
                    <div id="preview-section" class="hidden">
                        <label class="block text-white font-semibold mb-2">
                            <i class="fas fa-eye mr-2"></i>
                            Aperçu de l'annonce
                        </label>
                        <div id="preview-content" class="form-input p-4 min-h-32">
                            <!-- L'aperçu sera généré ici -->
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6">
                        <div class="flex items-center space-x-4">
                            <button 
                                type="button" 
                                onclick="togglePreview()" 
                                class="btn-secondary px-6 py-3 text-white font-semibold"
                            >
                                <i class="fas fa-eye mr-2"></i>
                                Aperçu
                            </button>
                            
                            <button 
                                type="button" 
                                onclick="saveDraft()" 
                                class="btn-secondary px-6 py-3 text-white font-semibold"
                            >
                                <i class="fas fa-save mr-2"></i>
                                Brouillon
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <a 
                                href="/announcements" 
                                class="btn-secondary px-6 py-3 text-white font-semibold"
                            >
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            
                            <button 
                                type="submit" 
                                class="btn-primary px-8 py-3 text-white font-semibold"
                            >
                                <i class="fas fa-bullhorn mr-2"></i>
                                Publier l'annonce
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let previewVisible = false;

        // Soumettre le formulaire
        document.getElementById('announcement-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            // Traiter les cases à cocher
            data.send_notification = document.getElementById('send_notification').checked;
            data.pin_announcement = document.getElementById('pin_announcement').checked;
            data.allow_comments = document.getElementById('allow_comments').checked;
            
            try {
                const response = await fetch('/api/announcements', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Annonce publiée avec succès !');
                    window.location.href = '/announcements';
                } else {
                    alert(result.message || 'Erreur lors de la publication');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la publication de l\'annonce');
            }
        });
        
        // Fonctions de formatage du texte
        function formatText(command) {
            document.execCommand(command, false, null);
            document.getElementById('content').focus();
        }
        
        function insertList(type) {
            const listType = type === 'ul' ? 'insertUnorderedList' : 'insertOrderedList';
            document.execCommand(listType, false, null);
            document.getElementById('content').focus();
        }
        
        // Basculer l'aperçu
        function togglePreview() {
            previewVisible = !previewVisible;
            const previewSection = document.getElementById('preview-section');
            const previewContent = document.getElementById('preview-content');
            
            if (previewVisible) {
                const title = document.getElementById('title').value;
                const content = document.getElementById('content').value;
                const type = document.getElementById('type').value;
                
                const typeLabels = {
                    'info': 'Information',
                    'important': 'Important',
                    'event': 'Événement',
                    'formation': 'Formation'
                };
                
                previewContent.innerHTML = `
                    <div class="border-l-4 border-purple-400 pl-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-white">${title || 'Titre de l\'annonce'}</h3>
                            ${type ? `<span class="badge px-2 py-1 text-xs text-white">${typeLabels[type]}</span>` : ''}
                        </div>
                        <div class="text-white/80 whitespace-pre-wrap">${content || 'Contenu de l\'annonce...'}</div>
                        <div class="text-white/60 text-sm mt-3">
                            <i class="fas fa-user mr-1"></i>
                            Par ${document.querySelector('[name="user"]')?.value || 'Vous'} • 
                            <i class="fas fa-calendar mr-1"></i>
                            Maintenant
                        </div>
                    </div>
                `;
                previewSection.classList.remove('hidden');
            } else {
                previewSection.classList.add('hidden');
            }
        }
        
        // Sauvegarder en brouillon
        async function saveDraft() {
            const formData = new FormData(document.getElementById('announcement-form'));
            const data = Object.fromEntries(formData.entries());
            data.status = 'draft';
            
            try {
                const response = await fetch('/api/announcements', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    alert('Brouillon sauvegardé !');
                }
            } catch (error) {
                console.error('Erreur lors de la sauvegarde:', error);
            }
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            // Définir la date/heure actuelle par défaut
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('publish_date').value = now.toISOString().slice(0, 16);
        });
    </script>
</body>
</html>