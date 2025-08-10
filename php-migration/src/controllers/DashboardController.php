<?php
/**
 * Contrôleur du tableau de bord
 */

require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController {
    
    public function index() {
        // Vérifier l'authentification
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            header('Location: /intrasphere/login');
            exit;
        }
        
        $user = $_SESSION['user'];
        
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            
            // Statistiques générales
            $stats = [
                'announcements' => 0,
                'documents' => 0,
                'messages' => 0,
                'users' => 0
            ];
            
            // Compter les annonces
            $stmt = $connection->prepare("SELECT COUNT(*) as count FROM announcements WHERE is_active = 1");
            $stmt->execute();
            $result = $stmt->fetch();
            $stats['announcements'] = $result['count'] ?? 0;
            
            // Compter les documents
            $stmt = $connection->prepare("SELECT COUNT(*) as count FROM documents WHERE is_active = 1");
            $stmt->execute();
            $result = $stmt->fetch();
            $stats['documents'] = $result['count'] ?? 0;
            
            // Compter les messages
            $stmt = $connection->prepare("SELECT COUNT(*) as count FROM messages WHERE recipient_id = ? OR sender_id = ?");
            $stmt->execute([$user['id'], $user['id']]);
            $result = $stmt->fetch();
            $stats['messages'] = $result['count'] ?? 0;
            
            // Compter les utilisateurs (pour admin)
            if ($user['role'] === 'admin') {
                $stmt = $connection->prepare("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
                $stmt->execute();
                $result = $stmt->fetch();
                $stats['users'] = $result['count'] ?? 0;
            }
            
            // Dernières annonces
            $stmt = $connection->prepare("SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC LIMIT 5");
            $stmt->execute();
            $recent_announcements = $stmt->fetchAll();
            
            // Messages récents
            $stmt = $connection->prepare("
                SELECT m.*, u.name as sender_name 
                FROM messages m 
                LEFT JOIN users u ON m.sender_id = u.id 
                WHERE m.recipient_id = ? 
                ORDER BY m.created_at DESC 
                LIMIT 5
            ");
            $stmt->execute([$user['id']]);
            $recent_messages = $stmt->fetchAll();
            
            include VIEWS_PATH . '/dashboard/index.php';
            
        } catch (Exception $e) {
            error_log("Erreur dashboard: " . $e->getMessage());
            include VIEWS_PATH . '/error/500.php';
        }
    }
}
?>