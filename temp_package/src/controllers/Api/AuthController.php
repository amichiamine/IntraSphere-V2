<?php
namespace Api;

/**
 * Contrôleur API d'authentification
 * Équivalent aux routes /api/auth/* TypeScript
 */

class AuthController extends \BaseController {
    private User $userModel;
    
    public function __construct() {
        $this->userModel = new \User();
    }
    
    /**
     * POST /api/auth/login
     */
    public function login(): void {
        $data = $this->getJsonInput();
        
        $this->validateRequired($data, ['username', 'password']);
        
        // Rate limiting unifié pour éviter les attaques par force brute
        if (!RateLimiter::middleware('login', $data['username'] ?? null)) {
            $retryAfter = RateLimiter::getRetryAfter('login_' . ($data['username'] ?? $_SERVER['REMOTE_ADDR']));
            $this->error("Trop de tentatives de connexion. Réessayez dans {$retryAfter} secondes.", 429);
        }
        
        $user = $this->userModel->authenticate($data['username'], $data['password']);
        
        if (!$user) {
            $this->logActivity('login_failed', ['username' => $data['username']]);
            $this->error('Identifiants incorrects', 401);
        }
        
        // Créer la session
        $_SESSION['user'] = $user;
        $_SESSION['login_time'] = time();
        
        $this->logActivity('login_success', ['user_id' => $user['id']]);
        
        $this->json([
            'message' => 'Connexion réussie',
            'user' => $user
        ]);
    }
    
    /**
     * POST /api/auth/logout
     */
    public function logout(): void {
        $user = $this->requireAuth();
        
        $this->logActivity('logout', ['user_id' => $user['id']]);
        
        // Détruire la session
        session_destroy();
        
        $this->json(['message' => 'Déconnexion réussie']);
    }
    
    /**
     * GET /api/auth/me
     */
    public function me(): void {
        $user = $this->requireAuth();
        
        // Récupérer les informations fraîches de l'utilisateur
        $freshUser = $this->userModel->find($user['id']);
        
        if (!$freshUser || !$freshUser['is_active']) {
            session_destroy();
            $this->error('Session expirée', 401);
        }
        
        // Supprimer le mot de passe
        unset($freshUser['password']);
        
        // Mettre à jour la session
        $_SESSION['user'] = $freshUser;
        
        $this->json($freshUser);
    }
    
    /**
     * POST /api/auth/register
     */
    public function register(): void {
        $data = $this->getJsonInput();
        
        $this->validateRequired($data, ['username', 'password', 'name']);
        
        // Vérifier si l'utilisateur existe déjà
        if ($this->userModel->findByUsername($data['username'])) {
            $this->error('Nom d\'utilisateur déjà utilisé');
        }
        
        // Valider le mot de passe avec standards harmonisés
        $passwordValidation = PasswordValidator::validatePasswordStrength($data['password']);
        if (!$passwordValidation['isValid']) {
            $this->error(implode(', ', $passwordValidation['errors']));
        }
        
        // Créer l'utilisateur
        $userData = $this->sanitizeInput([
            'username' => $data['username'],
            'password' => $data['password'],
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'role' => 'employee' // Rôle par défaut
        ]);
        
        try {
            $user = $this->userModel->create($userData);
            unset($user['password']);
            
            $this->logActivity('user_registered', ['user_id' => $user['id']]);
            
            $this->json([
                'message' => 'Compte créé avec succès',
                'user' => $user
            ], 201);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la création du compte');
        }
    }
    
    /**
     * POST /api/auth/change-password
     */
    public function changePassword(): void {
        $user = $this->requireAuth();
        $data = $this->getJsonInput();
        
        $this->validateRequired($data, ['current_password', 'new_password']);
        
        // Vérifier le mot de passe actuel
        $currentUser = $this->userModel->find($user['id']);
        if (!password_verify($data['current_password'], $currentUser['password'])) {
            $this->error('Mot de passe actuel incorrect', 400);
        }
        
        // Valider le nouveau mot de passe avec standards harmonisés
        $passwordValidation = PasswordValidator::validatePasswordStrength($data['new_password']);
        if (!$passwordValidation['isValid']) {
            $this->error(implode(', ', $passwordValidation['errors']));
        }
        
        // Changer le mot de passe
        $success = $this->userModel->changePassword($user['id'], $data['new_password']);
        
        if ($success) {
            $this->logActivity('password_changed', ['user_id' => $user['id']]);
            $this->json(['message' => 'Mot de passe modifié avec succès']);
        } else {
            $this->error('Erreur lors du changement de mot de passe', 500);
        }
    }
    
    /**
     * POST /api/auth/forgot-password
     */
    public function forgotPassword(): void {
        $data = $this->getJsonInput();
        
        $this->validateRequired($data, ['email']);
        
        // Rate limiting unifié pour éviter le spam
        if (!RateLimiter::middleware('forgot_password', $data['email'])) {
            $retryAfter = RateLimiter::getRetryAfter('forgot_password_' . $data['email']);
            $this->error("Trop de demandes de réinitialisation. Réessayez dans {$retryAfter} secondes.", 429);
        }
        
        // Chercher l'utilisateur par email
        $users = $this->userModel->where(['email' => $data['email']]);
        
        if (empty($users)) {
            // Ne pas révéler si l'email existe ou non
            $this->json(['message' => 'Si l\'email existe, un lien de réinitialisation a été envoyé']);
            return;
        }
        
        $user = $users[0];
        
        // Générer un token de réinitialisation
        $resetToken = bin2hex(random_bytes(32));
        $resetExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Stocker le token (dans une vraie app, on aurait une table séparée)
        $_SESSION['password_reset'][$resetToken] = [
            'user_id' => $user['id'],
            'expires' => $resetExpiry
        ];
        
        // Envoyer l'email (simulation)
        $resetLink = BASE_URL . "/reset-password?token={$resetToken}";
        
        $this->logActivity('password_reset_requested', [
            'user_id' => $user['id'],
            'email' => $data['email']
        ]);
        
        // Dans un vrai environnement, on enverrait un email
        // EmailService::sendPasswordResetEmail($user['email'], $resetLink);
        
        $this->json([
            'message' => 'Si l\'email existe, un lien de réinitialisation a été envoyé',
            'reset_link' => $resetLink // Seulement pour le développement
        ]);
    }
    
    /**
     * POST /api/auth/reset-password
     */
    public function resetPassword(): void {
        $data = $this->getJsonInput();
        
        $this->validateRequired($data, ['token', 'new_password']);
        
        // Vérifier le token
        $resetData = $_SESSION['password_reset'][$data['token']] ?? null;
        
        if (!$resetData || strtotime($resetData['expires']) < time()) {
            $this->error('Token de réinitialisation invalide ou expiré', 400);
        }
        
        // Valider le nouveau mot de passe
        if (strlen($data['new_password']) < 6) {
            $this->error('Le mot de passe doit faire au moins 6 caractères');
        }
        
        // Changer le mot de passe
        $success = $this->userModel->changePassword($resetData['user_id'], $data['new_password']);
        
        if ($success) {
            // Supprimer le token utilisé
            unset($_SESSION['password_reset'][$data['token']]);
            
            $this->logActivity('password_reset_completed', ['user_id' => $resetData['user_id']]);
            
            $this->json(['message' => 'Mot de passe réinitialisé avec succès']);
        } else {
            $this->error('Erreur lors de la réinitialisation', 500);
        }
    }
    
    /**
     * GET /api/auth/session-info
     */
    public function sessionInfo(): void {
        $user = $this->requireAuth();
        
        $sessionData = [
            'user_id' => $user['id'],
            'login_time' => $_SESSION['login_time'] ?? null,
            'session_duration' => time() - ($_SESSION['login_time'] ?? time()),
            'expires_in' => SESSION_LIFETIME - (time() - ($_SESSION['login_time'] ?? time()))
        ];
        
        $this->json($sessionData);
    }
}
?>