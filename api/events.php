<?php
/**
 * API Événements - Compatible avec l'API Express.js
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
    $eventId = null;
    
    $apiIndex = array_search('api', $pathParts);
    if ($apiIndex !== false && isset($pathParts[$apiIndex + 2])) {
        $eventId = $pathParts[$apiIndex + 2];
    }
    
    switch ($method) {
        case 'GET':
            SessionManager::requireAuth();
            
            if ($eventId) {
                // Récupérer un événement spécifique
                $stmt = $conn->prepare("
                    SELECT e.*, u.name as organizer_name 
                    FROM events e 
                    LEFT JOIN users u ON e.organizer_id = u.id 
                    WHERE e.id = ?
                ");
                $stmt->execute([$eventId]);
                $event = $stmt->fetch();
                
                if (!$event) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Event not found']);
                    exit;
                }
                
                echo json_encode($event);
            } else {
                // Récupérer tous les événements
                $upcoming = $_GET['upcoming'] ?? null;
                
                if ($upcoming === 'true') {
                    $stmt = $conn->prepare("
                        SELECT e.*, u.name as organizer_name 
                        FROM events e 
                        LEFT JOIN users u ON e.organizer_id = u.id 
                        WHERE e.date >= datetime('now') 
                        ORDER BY e.date ASC
                    ");
                } else {
                    $stmt = $conn->prepare("
                        SELECT e.*, u.name as organizer_name 
                        FROM events e 
                        LEFT JOIN users u ON e.organizer_id = u.id 
                        ORDER BY e.date DESC
                    ");
                }
                
                $stmt->execute();
                $events = $stmt->fetchAll();
                
                echo json_encode($events);
            }
            break;
            
        case 'POST':
            // Créer un nouvel événement
            SessionManager::requireRole(['admin', 'moderator']);
            
            $input = json_decode(file_get_contents('php://input'), true);
            $user = SessionManager::getUser();
            
            if (!isset($input['title']) || !isset($input['date'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Title and date are required']);
                exit;
            }
            
            // Valider le format de date
            $date = date('Y-m-d H:i:s', strtotime($input['date']));
            if (!$date) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid date format']);
                exit;
            }
            
            $stmt = $conn->prepare("
                INSERT INTO events (title, description, date, location, type, organizer_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $input['title'],
                $input['description'] ?? null,
                $date,
                $input['location'] ?? null,
                $input['type'] ?? 'meeting',
                $user['id']
            ]);
            
            // Récupérer l'événement créé
            $id = $conn->lastInsertId();
            if (!$id) {
                $stmt = $conn->prepare("
                    SELECT e.*, u.name as organizer_name 
                    FROM events e 
                    LEFT JOIN users u ON e.organizer_id = u.id 
                    WHERE e.title = ? AND e.organizer_id = ? 
                    ORDER BY e.created_at DESC LIMIT 1
                ");
                $stmt->execute([$input['title'], $user['id']]);
                $event = $stmt->fetch();
            } else {
                $stmt = $conn->prepare("
                    SELECT e.*, u.name as organizer_name 
                    FROM events e 
                    LEFT JOIN users u ON e.organizer_id = u.id 
                    WHERE e.id = ?
                ");
                $stmt->execute([$id]);
                $event = $stmt->fetch();
            }
            
            http_response_code(201);
            echo json_encode($event);
            break;
            
        case 'PUT':
            // Mettre à jour un événement
            SessionManager::requireRole(['admin', 'moderator']);
            
            if (!$eventId) {
                http_response_code(400);
                echo json_encode(['message' => 'Event ID required']);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Valider le format de date si fourni
            $date = null;
            if (isset($input['date'])) {
                $date = date('Y-m-d H:i:s', strtotime($input['date']));
                if (!$date) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid date format']);
                    exit;
                }
            }
            
            $stmt = $conn->prepare("
                UPDATE events 
                SET title = ?, description = ?, date = ?, location = ?, type = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $input['title'] ?? '',
                $input['description'] ?? null,
                $date ?? $input['date'] ?? '',
                $input['location'] ?? null,
                $input['type'] ?? 'meeting',
                $eventId
            ]);
            
            // Récupérer l'événement mis à jour
            $stmt = $conn->prepare("
                SELECT e.*, u.name as organizer_name 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id 
                WHERE e.id = ?
            ");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch();
            
            if (!$event) {
                http_response_code(404);
                echo json_encode(['message' => 'Event not found']);
                exit;
            }
            
            echo json_encode($event);
            break;
            
        case 'DELETE':
            // Supprimer un événement
            SessionManager::requireRole(['admin', 'moderator']);
            
            if (!$eventId) {
                http_response_code(400);
                echo json_encode(['message' => 'Event ID required']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$eventId]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Event not found']);
                exit;
            }
            
            http_response_code(200);
            echo json_encode(['message' => 'Event deleted successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Events API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error']);
}
?>