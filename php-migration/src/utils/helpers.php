<?php
/**
 * Fonctions utilitaires globales
 */

/**
 * Échapper les données pour l'affichage HTML
 */
function h($string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Générer une URL absolue
 */
function url(string $path = ''): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Générer une URL d'asset
 */
function asset(string $path): string {
    return rtrim(ASSETS_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Générer une URL d'upload
 */
function upload(string $path): string {
    return rtrim(UPLOADS_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Formater une date
 */
function formatDate($date, string $format = 'd/m/Y'): string {
    if (!$date) return '';
    
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    
    return $date->format($format);
}

/**
 * Formater une date relative (il y a X temps)
 */
function timeAgo($date): string {
    if (!$date) return '';
    
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->y > 0) {
        return $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
    } elseif ($diff->m > 0) {
        return $diff->m . ' mois';
    } elseif ($diff->d > 0) {
        return $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
    } elseif ($diff->h > 0) {
        return $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
    } elseif ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    } else {
        return 'À l\'instant';
    }
}

/**
 * Formater la taille d'un fichier
 */
function formatFileSize(int $bytes): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Générer un UUID simple
 */
function generateId(string $prefix = ''): string {
    return $prefix . uniqid('', true);
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

/**
 * Obtenir l'utilisateur connecté
 */
function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Vérifier le rôle de l'utilisateur
 */
function hasRole(string $role): bool {
    $user = currentUser();
    if (!$user) return false;
    
    $roleHierarchy = [
        'employee' => 1,
        'moderator' => 2,
        'admin' => 3
    ];
    
    $userLevel = $roleHierarchy[$user['role']] ?? 0;
    $requiredLevel = $roleHierarchy[$role] ?? 999;
    
    return $userLevel >= $requiredLevel;
}

/**
 * Vérifier une permission
 */
function hasPermission(string $permission): bool {
    $user = currentUser();
    if (!$user) return false;
    
    // Admin a toutes les permissions
    if ($user['role'] === 'admin') return true;
    
    $permissionModel = new Permission();
    return $permissionModel->hasPermission($user['id'], $permission);
}

/**
 * Truncate du texte
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Slug d'une chaîne
 */
function slug(string $text): string {
    $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
    $text = preg_replace('/\s+/', '-', trim($text));
    return strtolower($text);
}

/**
 * Validation d'email
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validation d'URL
 */
function isValidUrl(string $url): bool {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
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
    $sessionToken = $_SESSION['_token'] ?? '';
    return hash_equals($sessionToken, $token);
}

/**
 * Flasher un message en session
 */
function flash(string $type, string $message): void {
    $_SESSION['flash'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Récupérer les messages flash
 */
function getFlashMessages(): array {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Redirection avec message flash
 */
function redirectWithMessage(string $url, string $type, string $message): void {
    flash($type, $message);
    header("Location: {$url}");
    exit;
}

/**
 * Convertir un tableau en JSON sécurisé
 */
function jsonEncode($data): string {
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
}

/**
 * Logger une activité
 */
function logActivity(string $action, array $data = []): void {
    if (!LOG_ENABLED) return;
    
    $user = currentUser();
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'user_id' => $user['id'] ?? 'anonymous',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'data' => $data
    ];
    
    error_log('ACTIVITY: ' . json_encode($logEntry));
}

/**
 * Upload d'un fichier
 */
function uploadFile(array $file, string $directory = 'general'): array {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'Aucun fichier uploadé'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erreur lors de l\'upload'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Fichier trop volumineux'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_FILE_TYPES)) {
        return ['success' => false, 'error' => 'Type de fichier non autorisé'];
    }
    
    $uploadDir = UPLOADS_PATH . '/' . $directory;
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = generateId() . '.' . $extension;
    $filePath = $uploadDir . '/' . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return [
            'success' => true,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_url' => upload($directory . '/' . $fileName),
            'file_size' => $file['size'],
            'original_name' => $file['name']
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de sauvegarder le fichier'];
    }
}

/**
 * Supprimer un fichier uploadé
 */
function deleteUploadedFile(string $filePath): bool {
    $fullPath = UPLOADS_PATH . '/' . ltrim($filePath, '/');
    
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    
    return false;
}

/**
 * Créer un thumbnail d'image
 */
function createThumbnail(string $sourcePath, string $thumbnailPath, int $maxWidth = 300, int $maxHeight = 300): bool {
    if (!extension_loaded('gd')) {
        return false;
    }
    
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    [$width, $height, $type] = $imageInfo;
    
    // Créer l'image source selon le type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    
    // Calculer les nouvelles dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = intval($width * $ratio);
    $newHeight = intval($height * $ratio);
    
    // Créer l'image de destination
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    // Préserver la transparence pour PNG
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
    }
    
    // Redimensionner
    imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Sauvegarder selon le type original
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($thumbnail, $thumbnailPath, 85);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($thumbnail, $thumbnailPath);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($thumbnail, $thumbnailPath);
            break;
    }
    
    // Nettoyer la mémoire
    imagedestroy($source);
    imagedestroy($thumbnail);
    
    return $success;
}

/**
 * Valider et nettoyer une donnée
 */
function sanitize($value, string $type = 'string') {
    if ($value === null) return null;
    
    switch ($type) {
        case 'string':
            return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        case 'int':
            return (int) $value;
        case 'float':
            return (float) $value;
        case 'bool':
            return (bool) $value;
        case 'email':
            return filter_var($value, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($value, FILTER_SANITIZE_URL);
        default:
            return $value;
    }
}

/**
 * Détection d'appareil mobile
 */
function isMobile(): bool {
    return preg_match('/(android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini)/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
}

/**
 * Obtenir l'IP du client
 */
function getClientIp(): string {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            return trim($ip);
        }
    }
    
    return 'unknown';
}
?>