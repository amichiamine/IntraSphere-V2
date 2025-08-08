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

// Routes API Admin
$router->addRoute('GET', '/api/stats', 'Api\AdminController@stats');
$router->addRoute('GET', '/api/permissions', 'Api\AdminController@permissions');

// Pages principales
$router->addRoute('GET', '/announcements', 'AnnouncementsController@index');
$router->addRoute('GET', '/documents', 'DocumentsController@index');
$router->addRoute('GET', '/messages', 'MessagesController@index');
$router->addRoute('GET', '/trainings', 'TrainingsController@index');
$router->addRoute('GET', '/admin', 'AdminController@index');

// Gestion des uploads
$router->addRoute('POST', '/upload', 'UploadController@handle');

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