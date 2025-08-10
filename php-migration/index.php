<?php
/**
 * IntraSphere - PHP Pure Migration
 * Point d'entrée principal de l'application
 */

// Configuration et autoloader
require_once __DIR__ . '/config/bootstrap.php';

// Gestion des erreurs et debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de la session
session_start();

// Configuration sécurisée
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Router principal
$router = new Router();

// Routes d'authentification
$router->addRoute('GET', '/', 'AuthController@showLogin');
$router->addRoute('GET', '/login', 'AuthController@showLogin');
$router->addRoute('POST', '/login', 'AuthController@login');
$router->addRoute('POST', '/logout', 'AuthController@logout');
$router->addRoute('GET', '/dashboard', 'DashboardController@index');

// Routes API
$router->addRoute('GET', '/api/auth/me', 'Api\AuthController@me');
$router->addRoute('POST', '/api/auth/login', 'Api\AuthController@login');
$router->addRoute('POST', '/api/auth/logout', 'Api\AuthController@logout');

// Routes API Notifications (temps réel et multi-canal)
$router->addRoute('GET', '/api/notifications', 'Api\NotificationsController@index');
$router->addRoute('GET', '/api/notifications/unread-count', 'Api\NotificationsController@unreadCount');
$router->addRoute('PATCH', '/api/notifications/:id/read', 'Api\NotificationsController@markAsRead');
$router->addRoute('POST', '/api/notifications/mark-all-read', 'Api\NotificationsController@markAllAsRead');
$router->addRoute('GET', '/api/notifications/stream', 'Api\NotificationsController@stream');
$router->addRoute('POST', '/api/notifications/test', 'Api\NotificationsController@test');

// Routes API Utilisateurs
$router->addRoute('GET', '/api/users', 'Api\UsersController@index');
$router->addRoute('GET', '/api/users/:id', 'Api\UsersController@show');
$router->addRoute('POST', '/api/users', 'Api\UsersController@create');
$router->addRoute('PATCH', '/api/users/:id', 'Api\UsersController@update');
$router->addRoute('DELETE', '/api/users/:id', 'Api\UsersController@delete');

// Routes API Annonces
$router->addRoute('GET', '/api/announcements', 'Api\AnnouncementsController@index');
$router->addRoute('GET', '/api/announcements/:id', 'Api\AnnouncementsController@show');
$router->addRoute('POST', '/api/announcements', 'Api\AnnouncementsController@create');
$router->addRoute('PUT', '/api/announcements/:id', 'Api\AnnouncementsController@update');
$router->addRoute('DELETE', '/api/announcements/:id', 'Api\AnnouncementsController@delete');

// Routes API Documents
$router->addRoute('GET', '/api/documents', 'Api\DocumentsController@index');
$router->addRoute('GET', '/api/documents/:id', 'Api\DocumentsController@show');
$router->addRoute('POST', '/api/documents', 'Api\DocumentsController@create');
$router->addRoute('PUT', '/api/documents/:id', 'Api\DocumentsController@update');
$router->addRoute('DELETE', '/api/documents/:id', 'Api\DocumentsController@delete');

// Routes API Messages
$router->addRoute('GET', '/api/messages', 'Api\MessagesController@index');
$router->addRoute('POST', '/api/messages', 'Api\MessagesController@create');
$router->addRoute('PATCH', '/api/messages/:id/read', 'Api\MessagesController@markAsRead');

// Routes API Formations
$router->addRoute('GET', '/api/trainings', 'Api\TrainingsController@index');
$router->addRoute('GET', '/api/trainings/:id', 'Api\TrainingsController@show');
$router->addRoute('POST', '/api/trainings', 'Api\TrainingsController@create');

// Routes API Admin - Complètes
$router->addRoute('GET', '/api/stats', 'Api\AdminController@stats');
$router->addRoute('GET', '/api/permissions', 'Api\AdminController@permissions');
$router->addRoute('GET', '/api/admin/users-overview', 'Api\AdminController@usersOverview');
$router->addRoute('GET', '/api/admin/content-overview', 'Api\AdminController@contentOverview');
$router->addRoute('GET', '/api/admin/system-info', 'Api\AdminController@systemInfo');
$router->addRoute('POST', '/api/admin/maintenance-mode', 'Api\AdminController@toggleMaintenanceMode');

// Routes API Système (cache, performance, santé)
$router->addRoute('GET', '/api/system/cache/stats', 'Api\SystemController@cacheStats');
$router->addRoute('POST', '/api/system/cache/clear', 'Api\SystemController@clearCache');
$router->addRoute('POST', '/api/system/cache/cleanup', 'Api\SystemController@cleanupCache');
$router->addRoute('GET', '/api/system/health', 'Api\SystemController@health');
$router->addRoute('GET', '/api/system/performance', 'Api\SystemController@performance');

// Routes API Réclamations (workflow complet)
$router->addRoute('GET', '/api/complaints', 'Api\ComplaintsController@index');
$router->addRoute('GET', '/api/complaints/:id', 'Api\ComplaintsController@show');
$router->addRoute('POST', '/api/complaints', 'Api\ComplaintsController@create');
$router->addRoute('PATCH', '/api/complaints/:id', 'Api\ComplaintsController@update');
$router->addRoute('DELETE', '/api/complaints/:id', 'Api\ComplaintsController@delete');
$router->addRoute('GET', '/api/complaints/stats', 'Api\ComplaintsController@stats');
$router->addRoute('POST', '/api/complaints/:id/assign', 'Api\ComplaintsController@assign');
$router->addRoute('GET', '/api/complaints/my-complaints', 'Api\ComplaintsController@myComplaints');

// Routes API Documents - Nouvelles
$router->addRoute('GET', '/api/documents', 'Api\DocumentsController@index');
$router->addRoute('GET', '/api/documents/:id', 'Api\DocumentsController@show');
$router->addRoute('POST', '/api/documents', 'Api\DocumentsController@create');
$router->addRoute('PUT', '/api/documents/:id', 'Api\DocumentsController@update');
$router->addRoute('DELETE', '/api/documents/:id', 'Api\DocumentsController@delete');
$router->addRoute('GET', '/api/documents/categories', 'Api\DocumentsController@categories');
$router->addRoute('GET', '/api/documents/recent', 'Api\DocumentsController@recent');
$router->addRoute('GET', '/api/documents/stats', 'Api\DocumentsController@stats');
$router->addRoute('POST', '/api/documents/bulk-delete', 'Api\DocumentsController@bulkDelete');
$router->addRoute('POST', '/api/documents/:id/download', 'Api\DocumentsController@download');

// Routes API Messages - Nouvelles  
$router->addRoute('GET', '/api/messages', 'Api\MessagesController@index');
$router->addRoute('GET', '/api/messages/:id', 'Api\MessagesController@show');
$router->addRoute('POST', '/api/messages', 'Api\MessagesController@create');
$router->addRoute('DELETE', '/api/messages/:id', 'Api\MessagesController@delete');
$router->addRoute('PATCH', '/api/messages/:id/read', 'Api\MessagesController@markAsRead');
$router->addRoute('GET', '/api/messages/unread-count', 'Api\MessagesController@unreadCount');
$router->addRoute('GET', '/api/messages/conversations', 'Api\MessagesController@conversations');
$router->addRoute('GET', '/api/messages/conversation/:user_id', 'Api\MessagesController@conversation');
$router->addRoute('POST', '/api/messages/bulk-read', 'Api\MessagesController@bulkRead');
$router->addRoute('DELETE', '/api/messages/conversation/:user_id', 'Api\MessagesController@deleteConversation');
$router->addRoute('GET', '/api/messages/stats', 'Api\MessagesController@stats');

// Routes API Events - Nouvelles
$router->addRoute('GET', '/api/events', 'Api\EventsController@index');
$router->addRoute('GET', '/api/events/:id', 'Api\EventsController@show');
$router->addRoute('POST', '/api/events', 'Api\EventsController@create');
$router->addRoute('PUT', '/api/events/:id', 'Api\EventsController@update');
$router->addRoute('DELETE', '/api/events/:id', 'Api\EventsController@delete');
$router->addRoute('GET', '/api/events/upcoming', 'Api\EventsController@upcoming');
$router->addRoute('GET', '/api/events/calendar', 'Api\EventsController@calendar');
$router->addRoute('GET', '/api/events/my-events', 'Api\EventsController@myEvents');
$router->addRoute('GET', '/api/events/types', 'Api\EventsController@types');
$router->addRoute('GET', '/api/events/stats', 'Api\EventsController@stats');
$router->addRoute('POST', '/api/events/bulk-delete', 'Api\EventsController@bulkDelete');

// Routes API Trainings - Complètes
$router->addRoute('GET', '/api/trainings', 'Api\TrainingsController@index');
$router->addRoute('GET', '/api/trainings/:id', 'Api\TrainingsController@show');
$router->addRoute('POST', '/api/trainings', 'Api\TrainingsController@create');
$router->addRoute('PUT', '/api/trainings/:id', 'Api\TrainingsController@update');
$router->addRoute('DELETE', '/api/trainings/:id', 'Api\TrainingsController@delete');
$router->addRoute('POST', '/api/trainings/:id/register', 'Api\TrainingsController@register');
$router->addRoute('DELETE', '/api/trainings/:id/register', 'Api\TrainingsController@unregister');
$router->addRoute('GET', '/api/trainings/:id/participants', 'Api\TrainingsController@participants');
$router->addRoute('GET', '/api/trainings/my-trainings', 'Api\TrainingsController@myTrainings');
$router->addRoute('GET', '/api/trainings/categories', 'Api\TrainingsController@categories');
$router->addRoute('GET', '/api/trainings/stats', 'Api\TrainingsController@stats');

// Routes API Complaints - Nouvelles
$router->addRoute('GET', '/api/complaints', 'Api\ComplaintsController@index');
$router->addRoute('GET', '/api/complaints/:id', 'Api\ComplaintsController@show');
$router->addRoute('POST', '/api/complaints', 'Api\ComplaintsController@create');
$router->addRoute('PUT', '/api/complaints/:id', 'Api\ComplaintsController@update');
$router->addRoute('DELETE', '/api/complaints/:id', 'Api\ComplaintsController@delete');
$router->addRoute('PATCH', '/api/complaints/:id/assign', 'Api\ComplaintsController@assign');
$router->addRoute('PATCH', '/api/complaints/:id/status', 'Api\ComplaintsController@changeStatus');
$router->addRoute('PATCH', '/api/complaints/:id/priority', 'Api\ComplaintsController@changePriority');
$router->addRoute('GET', '/api/complaints/my-complaints', 'Api\ComplaintsController@myComplaints');
$router->addRoute('GET', '/api/complaints/assigned-to-me', 'Api\ComplaintsController@assignedToMe');
$router->addRoute('GET', '/api/complaints/stats', 'Api\ComplaintsController@stats');
$router->addRoute('POST', '/api/complaints/bulk-delete', 'Api\ComplaintsController@bulkDelete');

// Pages principales - Étendues
$router->addRoute('GET', '/announcements', 'AnnouncementsController@index');
$router->addRoute('GET', '/announcements/create', 'AnnouncementsController@create');
$router->addRoute('GET', '/announcements/:id', 'AnnouncementsController@show');
$router->addRoute('GET', '/announcements/:id/edit', 'AnnouncementsController@edit');

$router->addRoute('GET', '/documents', 'DocumentsController@index');
$router->addRoute('GET', '/documents/upload', 'DocumentsController@upload');
$router->addRoute('GET', '/documents/:id', 'DocumentsController@show');

$router->addRoute('GET', '/messages', 'MessagesController@index');
$router->addRoute('GET', '/messages/compose', 'MessagesController@compose');
$router->addRoute('GET', '/messages/:id', 'MessagesController@show');

$router->addRoute('GET', '/trainings', 'TrainingsController@index');
$router->addRoute('GET', '/trainings/create', 'TrainingsController@create');
$router->addRoute('GET', '/trainings/:id', 'TrainingsController@show');
$router->addRoute('GET', '/trainings/my-trainings', 'TrainingsController@myTrainings');

$router->addRoute('GET', '/admin', 'AdminController@index');
$router->addRoute('GET', '/admin/users', 'AdminController@users');
$router->addRoute('GET', '/admin/permissions', 'AdminController@permissions');
$router->addRoute('GET', '/admin/system', 'AdminController@system');
$router->addRoute('GET', '/admin/logs', 'AdminController@logs');

// Upload et gestion de fichiers
$router->addRoute('POST', '/upload', 'UploadController@handle');
$router->addRoute('DELETE', '/upload/:filename', 'UploadController@delete');

// Gestion 404
$router->setNotFoundHandler('ErrorController@notFound');

// Dispatch de la requête
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    include __DIR__ . '/views/error/500.php';
}
?>