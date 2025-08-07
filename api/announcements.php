<?php
/**
 * API Annonces - Compatible avec l'API Express.js
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
    $announcementId = null;
    
    // Chercher l'ID dans le chemin (format: /api/announcements/id)
    $apiIndex = array_search('api', $pathParts);
    if ($apiIndex !== false && isset($pathParts[$apiIndex + 2])) {
        $announcementId = $pathParts[$apiIndex + 2];
    }
    
    switch ($method) {
        case 'GET':
            if ($announcementId) {
                // Récupérer une annonce spécifique
                $stmt = $conn->prepare("
                    SELECT a.*, u.name as author_name 
                    FROM announcements a 
                    LEFT JOIN users u ON a.author_id = u.id 
                    WHERE a.id = ?
                ");
                $stmt->execute([$announcementId]);
                $announcement = $stmt->fetch();
                
                if (!$announcement) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Announcement not found']);
                    exit;
                }
                
                echo json_encode($announcement);
            } else {
                // Récupérer toutes les annonces
                $stmt = $conn->prepare("
                    SELECT a.*, u.name as author_name 
                    FROM announcements a 
                    LEFT JOIN users u ON a.author_id = u.id 
                    ORDER BY a.is_important DESC, a.created_at DESC 
                    LIMIT 50
                ");
                $stmt->execute();
                $announcements = $stmt->fetchAll();
                
                echo json_encode($announcements);
            }
            break;
            
        case 'POST':
            // Créer une nouvelle annonce
            SessionManager::requireRole(['admin', 'moderator']);
            
            $input = json_decode(file_get_contents('php://input'), true);
            $user = SessionManager::getUser();
            
            // Validation des données
            if (!isset($input['title']) || !isset($input['content'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Title and content are required']);
                exit;
            }
            
            // Insérer la nouvelle annonce
            $stmt = $conn->prepare("
                INSERT INTO announcements (title, content, type, author_id, author_name, image_url, icon, is_important) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $input['title'],
                $input['content'],
                $input['type'] ?? 'info',
                $user['id'],
                $user['name'],
                $input['imageUrl'] ?? null,
                $input['icon'] ?? '📢',
                isset($input['isImportant']) ? ($input['isImportant'] ? 1 : 0) : 0
            ]);
            
            // Récupérer l'annonce créée
            $id = $conn->lastInsertId();
            if (!$id) {
                // Pour SQLite qui peut ne pas retourner lastInsertId correctement
                $stmt = $conn->prepare("SELECT * FROM announcements WHERE title = ? AND author_id = ? ORDER BY created_at DESC LIMIT 1");
                $stmt->execute([$input['title'], $user['id']]);
                $announcement = $stmt->fetch();
            } else {
                $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
                $stmt->execute([$id]);
                $announcement = $stmt->fetch();
            }
            
            http_response_code(201);
            echo json_encode($announcement);
            break;
            
        case 'PUT':
            // Mettre à jour une annonce
            SessionManager::requireRole(['admin', 'moderator']);
            
            if (!$announcementId) {
                http_response_code(400);
                echo json_encode(['message' => 'Announcement ID required']);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $conn->prepare("
                UPDATE announcements 
                SET title = ?, content = ?, type = ?, image_url = ?, icon = ?, is_important = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $input['title'] ?? '',
                $input['content'] ?? '',
                $input['type'] ?? 'info',
                $input['imageUrl'] ?? null,
                $input['icon'] ?? '📢',
                isset($input['isImportant']) ? ($input['isImportant'] ? 1 : 0) : 0,
                $announcementId
            ]);
            
            // Récupérer l'annonce mise à jour
            $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
            $stmt->execute([$announcementId]);
            $announcement = $stmt->fetch();
            
            if (!$announcement) {
                http_response_code(404);
                echo json_encode(['message' => 'Announcement not found']);
                exit;
            }
            
            echo json_encode($announcement);
            break;
            
        case 'DELETE':
            // Supprimer une annonce
            SessionManager::requireRole(['admin']);
            
            if (!$announcementId) {
                http_response_code(400);
                echo json_encode(['message' => 'Announcement ID required']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
            $stmt->execute([$announcementId]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Announcement not found']);
                exit;
            }
            
            http_response_code(200);
            echo json_encode(['message' => 'Announcement deleted successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Announcements API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error']);
}
?>