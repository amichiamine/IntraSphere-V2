<?php
/**
 * API d'authentification - Profil utilisateur actuel
 */

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

try {
    if (!SessionManager::isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['message' => 'Not authenticated']);
        exit;
    }
    
    // Récupérer les informations utilisateur actuelles depuis la base
    $db = new Database();
    $conn = $db->getConnection();
    
    $userId = SessionManager::getUserId();
    $stmt = $conn->prepare("SELECT id, username, name, role, avatar, employee_id, department, position, phone, email, created_at FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // L'utilisateur n'existe plus ou a été désactivé
        SessionManager::destroy();
        http_response_code(401);
        echo json_encode(['message' => 'User account not found or deactivated']);
        exit;
    }
    
    // Mettre à jour la session avec les données fraîches
    SessionManager::setUser($user);
    
    http_response_code(200);
    echo json_encode($user);
    
} catch (Exception $e) {
    error_log("Auth me error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error']);
}
?>