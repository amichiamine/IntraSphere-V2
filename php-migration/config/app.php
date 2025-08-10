<?php
/**
 * Fonctions utilitaires globales
 */

/**
 * Échapper les caractères HTML
 */
function h(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Générer un token CSRF
 */
function csrfToken(): string {
    if (!isset($_SESSION['_token'])) {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_token'];
}

/**
 * Vérifier un token CSRF
 */
function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token);
}

/**
 * Redirection sécurisée
 */
function redirect(string $url, int $status = 302): void {
    header('Location: ' . $url, true, $status);
    exit;
}

/**
 * Retourner une réponse JSON
 */
function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Log d'erreur simple
 */
function logError(string $message, array $context = []): void {
    $logFile = LOGS_PATH . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? json_encode($context) : '';
    $logLine = "[{$timestamp}] ERROR: {$message} {$contextStr}" . PHP_EOL;
    file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
}

/**
 * Générer un ID unique
 */
function generateId(string $prefix = ''): string {
    return $prefix . uniqid('', true);
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

/**
 * Récupérer l'utilisateur connecté
 */
function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Vérifier le rôle de l'utilisateur
 */
function hasRole(string $role): bool {
    $user = currentUser();
    return $user && $user['role'] === $role;
}

/**
 * Vérifier si l'utilisateur est admin
 */
function isAdmin(): bool {
    return hasRole('admin');
}

/**
 * Formater une date
 */
function formatDate(string $date, string $format = 'd/m/Y H:i'): string {
    return date($format, strtotime($date));
}

/**
 * Tronquer un texte
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Nettoyer une chaîne pour URL
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

/**
 * Vérifier une adresse email
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Générer un mot de passe aléatoire
 */
function generatePassword(int $length = 12): string {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($characters), 0, $length);
}
?>