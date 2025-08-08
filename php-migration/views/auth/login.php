<?php
$title = 'Connexion - IntraSphere';
$description = 'Connectez-vous à votre espace IntraSphere';
ob_start();
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <!-- Logo et titre -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mb-4">
                <i data-lucide="zap" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">IntraSphere</h1>
            <p class="text-gray-400">Connectez-vous à votre espace</p>
        </div>
        
        <!-- Formulaire de connexion -->
        <div class="glass-card p-8">
            <form method="POST" action="/login" class="space-y-6">
                <input type="hidden" name="_token" value="<?= csrfToken() ?>">
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                        Nom d'utilisateur
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        class="input-glass w-full"
                        placeholder="Entrez votre nom d'utilisateur"
                        value="<?= h($_POST['username'] ?? '') ?>"
                        autocomplete="username"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Mot de passe
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="input-glass w-full"
                        placeholder="Entrez votre mot de passe"
                        autocomplete="current-password"
                    >
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-600 bg-transparent">
                        <span class="ml-2 text-sm text-gray-300">Se souvenir de moi</span>
                    </label>
                    
                    <a href="/forgot-password" class="text-sm text-purple-400 hover:text-purple-300 transition-colors">
                        Mot de passe oublié ?
                    </a>
                </div>
                
                <button type="submit" class="btn-primary w-full py-3 text-center font-semibold">
                    Se connecter
                </button>
            </form>
            
            <!-- Informations de demo -->
            <div class="mt-8 p-4 bg-blue-500 bg-opacity-10 border border-blue-500 border-opacity-20 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-300 mb-2">Comptes de démonstration</h3>
                <div class="space-y-1 text-xs text-blue-200">
                    <p><strong>Admin:</strong> admin / admin123</p>
                    <p><strong>Employé:</strong> employee / employee123</p>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-sm text-gray-500">
                IntraSphere v<?= APP_VERSION ?> 
                <span class="mx-2">•</span>
                <a href="/about" class="text-purple-400 hover:text-purple-300 transition-colors">
                    À propos
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Particules d'arrière-plan (optionnel) -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
    <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus automatique sur le champ username
    document.getElementById('username').focus();
    
    // Gestion de la soumission du formulaire
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function(e) {
        // Désactiver le bouton pour éviter les double-clics
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i>Connexion...';
        
        // Réactiver après 3 secondes si pas de redirection
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Se connecter';
            lucide.createIcons();
        }, 3000);
    });
    
    // Animation d'entrée
    const container = document.querySelector('.max-w-md');
    container.style.opacity = '0';
    container.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        container.style.transition = 'all 0.6s ease';
        container.style.opacity = '1';
        container.style.transform = 'translateY(0)';
    }, 100);
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout/app.php';
?>