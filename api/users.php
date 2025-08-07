<?php
/**
 * API Utilisateurs - Compatible avec l'API Express.js
 */

require_once 'config/database.php';
require_once 'config/session.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['REQUEST_URI'];
    
    // Extraire l'ID si présent dans l'URL
    $pathParts = explode('/', trim($path, '/'));
    $userId = null;
    
    $apiIndex = array_search('api', $pathParts);
    if ($apiIndex !== false && isset($pathParts[$apiIndex + 2])) {
        $userId = $pathParts[$apiIndex + 2];
    }
    
    switch ($method) {
        case 'GET':
            SessionManager::requireAuth();
            
            if ($userId) {
                // Récupérer un utilisateur spécifique
                $stmt = $conn->prepare("
                    SELECT id, username, name, role, avatar, employee_id, department, position, phone, email, created_at, is_active 
                    FROM users 
                    WHERE id = ?
                ");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                
                if (!$user) {
                    http_response_code(404);
                    echo json_encode(['message' => 'User not found']);
                    exit;
                }
                
                echo json_encode($user);
            } else {
                // Récupérer tous les utilisateurs (admin/moderator seulement)
                SessionManager::requireRole(['admin', 'moderator']);
                
                $stmt = $conn->prepare("
                    SELECT id, username, name, role, avatar, employee_id, department, position, phone, email, created_at, is_active 
                    FROM users 
                    ORDER BY name ASC
                ");
                $stmt->execute();
                $users = $stmt->fetchAll();
                
                echo json_encode($users);
            }
            break;
            
        case 'POST':
            // Créer un nouvel utilisateur
            SessionManager::requireRole(['admin']);
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validation des données
            if (!isset($input['username']) || !isset($input['password']) || !isset($input['name'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Username, password and name are required']);
                exit;
            }
            
            // Vérifier si le nom d'utilisateur existe déjà
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$input['username']]);
            if ($stmt->fetchColumn() > 0) {
                http_response_code(409);
                echo json_encode(['message' => 'Username already exists']);
                exit;
            }
            
            // Hacher le mot de passe
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            
            // Insérer le nouvel utilisateur
            $stmt = $conn->prepare("
                INSERT INTO users (username, password, name, role, avatar, employee_id, department, position, phone, email, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $input['username'],
                $hashedPassword,
                $input['name'],
                $input['role'] ?? 'employee',
                $input['avatar'] ?? null,
                $input['employeeId'] ?? null,
                $input['department'] ?? null,
                $input['position'] ?? null,
                $input['phone'] ?? null,
                $input['email'] ?? null,
                isset($input['isActive']) ? ($input['isActive'] ? 1 : 0) : 1
            ]);
            
            // Récupérer l'utilisateur créé
            $id = $conn->lastInsertId();
            if (!$id) {
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? ORDER BY created_at DESC LIMIT 1");
                $stmt->execute([$input['username']]);
                $user = $stmt->fetch();
            } else {
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch();
            }
            
            // Retirer le mot de passe de la réponse
            unset($user['password']);
            
            http_response_code(201);
            echo json_encode($user);
            break;
            
        case 'PUT':
            // Mettre à jour un utilisateur
            if (!$userId) {
                http_response_code(400);
                echo json_encode(['message' => 'User ID required']);
                exit;
            }
            
            $currentUser = SessionManager::getUser();
            
            // Vérifier les permissions (admin ou utilisateur modifie son propre profil)
            if ($currentUser['role'] !== 'admin' && $currentUser['id'] !== $userId) {
                http_response_code(403);
                echo json_encode(['message' => 'Insufficient permissions']);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Construire la requête de mise à jour
            $updateFields = [];
            $updateValues = [];
            
            $allowedFields = ['name', 'avatar', 'employee_id', 'department', 'position', 'phone', 'email'];
            
            // Les admins peuvent modifier plus de champs
            if ($currentUser['role'] === 'admin') {
                $allowedFields = array_merge($allowedFields, ['username', 'role', 'is_active']);
            }
            
            foreach ($allowedFields as $field) {
                $inputKey = $field === 'employee_id' ? 'employeeId' : 
                           ($field === 'is_active' ? 'isActive' : $field);
                           
                if (isset($input[$inputKey])) {
                    $updateFields[] = $field . " = ?";
                    $updateValues[] = $field === 'is_active' ? ($input[$inputKey] ? 1 : 0) : $input[$inputKey];
                }
            }
            
            // Gestion du mot de passe (si fourni)
            if (isset($input['password']) && !empty($input['password'])) {
                $updateFields[] = "password = ?";
                $updateValues[] = password_hash($input['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updateFields)) {
                http_response_code(400);
                echo json_encode(['message' => 'No valid fields to update']);
                exit;
            }
            
            $updateValues[] = $userId;
            
            $stmt = $conn->prepare("UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?");
            $stmt->execute($updateValues);
            
            // Récupérer l'utilisateur mis à jour
            $stmt = $conn->prepare("
                SELECT id, username, name, role, avatar, employee_id, department, position, phone, email, created_at, is_active 
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['message' => 'User not found']);
                exit;
            }
            
            // Si l'utilisateur modifie son propre profil, mettre à jour la session
            if ($currentUser['id'] === $userId) {
                SessionManager::setUser($user);
            }
            
            echo json_encode($user);
            break;
            
        case 'DELETE':
            // Supprimer un utilisateur
            SessionManager::requireRole(['admin']);
            
            if (!$userId) {
                http_response_code(400);
                echo json_encode(['message' => 'User ID required']);
                exit;
            }
            
            $currentUser = SessionManager::getUser();
            
            // Empêcher l'admin de se supprimer lui-même
            if ($currentUser['id'] === $userId) {
                http_response_code(400);
                echo json_encode(['message' => 'Cannot delete your own account']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'User not found']);
                exit;
            }
            
            http_response_code(200);
            echo json_encode(['message' => 'User deleted successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Users API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error']);
}
?>