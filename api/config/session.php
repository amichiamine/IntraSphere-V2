<?php
/**
 * Gestion des sessions pour API PHP - IntraSphere
 * Compatible avec l'authentification Express.js
 */

class SessionManager {
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuration sécurisée des sessions
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_lifetime', 3600); // 1 heure
            
            session_start();
        }
    }
    
    public static function setUser($user) {
        self::start();
        $_SESSION['userId'] = $user['id'];
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time();
    }
    
    public static function getUser() {
        self::start();
        
        // Vérifier l'expiration de session
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 3600) {
            self::destroy();
            return null;
        }
        
        $_SESSION['last_activity'] = time();
        return $_SESSION['user'] ?? null;
    }
    
    public static function getUserId() {
        self::start();
        return $_SESSION['userId'] ?? null;
    }
    
    public static function isAuthenticated() {
        return self::getUserId() !== null;
    }
    
    public static function destroy() {
        self::start();
        $_SESSION = [];
        
        // Détruire le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
    
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Authentication required']);
            exit;
        }
    }
    
    public static function requireRole($allowedRoles) {
        self::requireAuth();
        $user = self::getUser();
        
        if (!$user || !in_array($user['role'], $allowedRoles)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Insufficient permissions']);
            exit;
        }
    }
}
?>