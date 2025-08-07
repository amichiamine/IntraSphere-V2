<?php
/**
 * API Documents - Compatible avec l'API Express.js
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
    $documentId = null;
    
    $apiIndex = array_search('api', $pathParts);
    if ($apiIndex !== false && isset($pathParts[$apiIndex + 2])) {
        $documentId = $pathParts[$apiIndex + 2];
    }
    
    switch ($method) {
        case 'GET':
            if ($documentId) {
                // Récupérer un document spécifique
                $stmt = $conn->prepare("SELECT * FROM documents WHERE id = ?");
                $stmt->execute([$documentId]);
                $document = $stmt->fetch();
                
                if (!$document) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Document not found']);
                    exit;
                }
                
                echo json_encode($document);
            } else {
                // Récupérer tous les documents
                $category = $_GET['category'] ?? null;
                
                if ($category) {
                    $stmt = $conn->prepare("SELECT * FROM documents WHERE category = ? ORDER BY updated_at DESC");
                    $stmt->execute([$category]);
                } else {
                    $stmt = $conn->prepare("SELECT * FROM documents ORDER BY updated_at DESC");
                    $stmt->execute();
                }
                
                $documents = $stmt->fetchAll();
                echo json_encode($documents);
            }
            break;
            
        case 'POST':
            // Créer un nouveau document
            SessionManager::requireRole(['admin', 'moderator']);
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['title']) || !isset($input['category']) || !isset($input['fileName']) || !isset($input['fileUrl'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Title, category, fileName and fileUrl are required']);
                exit;
            }
            
            $stmt = $conn->prepare("
                INSERT INTO documents (title, description, category, file_name, file_url, version) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $input['title'],
                $input['description'] ?? null,
                $input['category'],
                $input['fileName'],
                $input['fileUrl'],
                $input['version'] ?? '1.0'
            ]);
            
            // Récupérer le document créé
            $id = $conn->lastInsertId();
            if (!$id) {
                $stmt = $conn->prepare("SELECT * FROM documents WHERE title = ? AND file_url = ? ORDER BY updated_at DESC LIMIT 1");
                $stmt->execute([$input['title'], $input['fileUrl']]);
                $document = $stmt->fetch();
            } else {
                $stmt = $conn->prepare("SELECT * FROM documents WHERE id = ?");
                $stmt->execute([$id]);
                $document = $stmt->fetch();
            }
            
            http_response_code(201);
            echo json_encode($document);
            break;
            
        case 'PUT':
            // Mettre à jour un document
            SessionManager::requireRole(['admin', 'moderator']);
            
            if (!$documentId) {
                http_response_code(400);
                echo json_encode(['message' => 'Document ID required']);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $conn->prepare("
                UPDATE documents 
                SET title = ?, description = ?, category = ?, file_name = ?, file_url = ?, version = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $input['title'] ?? '',
                $input['description'] ?? null,
                $input['category'] ?? '',
                $input['fileName'] ?? '',
                $input['fileUrl'] ?? '',
                $input['version'] ?? '1.0',
                $documentId
            ]);
            
            // Récupérer le document mis à jour
            $stmt = $conn->prepare("SELECT * FROM documents WHERE id = ?");
            $stmt->execute([$documentId]);
            $document = $stmt->fetch();
            
            if (!$document) {
                http_response_code(404);
                echo json_encode(['message' => 'Document not found']);
                exit;
            }
            
            echo json_encode($document);
            break;
            
        case 'DELETE':
            // Supprimer un document
            SessionManager::requireRole(['admin']);
            
            if (!$documentId) {
                http_response_code(400);
                echo json_encode(['message' => 'Document ID required']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM documents WHERE id = ?");
            $stmt->execute([$documentId]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Document not found']);
                exit;
            }
            
            http_response_code(200);
            echo json_encode(['message' => 'Document deleted successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Documents API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error']);
}
?>