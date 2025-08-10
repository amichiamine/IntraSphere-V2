<?php
/**
 * Contrôleur d'authentification
 */

require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {
    
    public function showLogin() {
        // Si déjà connecté, rediriger vers le dashboard
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            header('Location: /intrasphere/dashboard');
            exit;
        }
        
        include VIEWS_PATH . '/auth/login.php';
    }
    
    public function login() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }
            
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                throw new Exception('Nom d\'utilisateur et mot de passe requis');
            }
            
            // Connexion à la base de données
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $stmt = $connection->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !password_verify($password, $user['password'])) {
                throw new Exception('Identifiants incorrects');
            }
            
            // Créer la session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role'],
                'department' => $user['department'] ?? '',
                'position' => $user['position'] ?? ''
            ];
            $_SESSION['login_time'] = time();
            
            // Redirection selon le rôle
            $redirectUrl = $user['role'] === 'admin' ? '/intrasphere/admin' : '/intrasphere/dashboard';
            
            // Si requête AJAX
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'redirect' => $redirectUrl,
                    'user' => $_SESSION['user']
                ]);
                exit;
            }
            
            // Redirection normale
            header('Location: ' . $redirectUrl);
            exit;
            
        } catch (Exception $e) {
            $error = $e->getMessage();
            
            // Si requête AJAX
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $error
                ]);
                exit;
            }
            
            // Afficher la page de login avec erreur
            include VIEWS_PATH . '/auth/login.php';
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: /intrasphere/login');
        exit;
    }
}
?>