<?php
/**
 * API Statistiques - Compatible avec l'API Express.js
 */

require_once 'config/database.php';

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
    $db = new Database();
    $conn = $db->getConnection();
    
    // Statistiques publiques (pas besoin d'authentification)
    $stats = [];
    
    // Nombre total d'utilisateurs actifs
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE is_active = 1");
    $stmt->execute();
    $stats['totalUsers'] = (int) $stmt->fetchColumn();
    
    // Nombre total d'annonces
    $stmt = $conn->prepare("SELECT COUNT(*) FROM announcements");
    $stmt->execute();
    $stats['totalAnnouncements'] = (int) $stmt->fetchColumn();
    
    // Nombre total de documents
    $stmt = $conn->prepare("SELECT COUNT(*) FROM documents");
    $stmt->execute();
    $stats['totalDocuments'] = (int) $stmt->fetchColumn();
    
    // Nombre total d'événements à venir
    $stmt = $conn->prepare("SELECT COUNT(*) FROM events WHERE date >= datetime('now')");
    $stmt->execute();
    $stats['upcomingEvents'] = (int) $stmt->fetchColumn();
    
    // Nombre de messages non lus (si table existe)
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE is_read = 0");
        $stmt->execute();
        $stats['unreadMessages'] = (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        $stats['unreadMessages'] = 0;
    }
    
    // Nombre de réclamations ouvertes (si table existe)
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM complaints WHERE status = 'open'");
        $stmt->execute();
        $stats['openComplaints'] = (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        $stats['openComplaints'] = 0;
    }
    
    // Répartition par rôle
    $stmt = $conn->prepare("
        SELECT role, COUNT(*) as count 
        FROM users 
        WHERE is_active = 1 
        GROUP BY role
    ");
    $stmt->execute();
    $roleDistribution = $stmt->fetchAll();
    
    $stats['roleDistribution'] = [];
    foreach ($roleDistribution as $role) {
        $stats['roleDistribution'][$role['role']] = (int) $role['count'];
    }
    
    // Annonces récentes (7 derniers jours)
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM announcements 
        WHERE created_at >= datetime('now', '-7 days')
    ");
    $stmt->execute();
    $stats['recentAnnouncements'] = (int) $stmt->fetchColumn();
    
    // Nouveaux utilisateurs (30 derniers jours)
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM users 
        WHERE created_at >= datetime('now', '-30 days') AND is_active = 1
    ");
    $stmt->execute();
    $stats['newUsersThisMonth'] = (int) $stmt->fetchColumn();
    
    http_response_code(200);
    echo json_encode($stats);
    
} catch (Exception $e) {
    error_log("Stats API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error']);
}
?>