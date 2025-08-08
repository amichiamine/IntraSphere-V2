<?php
/**
 * Validateur universel cross-platform
 * Compatible TypeScript/PHP pour validation commune
 */

class UniversalValidator {
    
    /**
     * Validation email universelle
     */
    public static function validateEmail(string $email): array {
        $errors = [];
        
        if (empty($email)) {
            $errors[] = "L'adresse email est requise";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide";
        } elseif (strlen($email) > 255) {
            $errors[] = "L'adresse email est trop longue (maximum 255 caractères)";
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation username universelle (harmonisée avec Zod)
     */
    public static function validateUsername(string $username): array {
        $errors = [];
        
        if (empty($username)) {
            $errors[] = "Le nom d'utilisateur est requis";
        } elseif (strlen($username) < 3) {
            $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
        } elseif (strlen($username) > 50) {
            $errors[] = "Le nom d'utilisateur ne peut pas dépasser 50 caractères";
        } elseif (!preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
            $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres, points, tirets et underscores";
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation nom complet
     */
    public static function validateFullName(string $name): array {
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Le nom complet est requis";
        } elseif (strlen($name) < 2) {
            $errors[] = "Le nom complet doit contenir au moins 2 caractères";
        } elseif (strlen($name) > 100) {
            $errors[] = "Le nom complet ne peut pas dépasser 100 caractères";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/u', $name)) {
            $errors[] = "Le nom complet contient des caractères non autorisés";
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation rôle utilisateur
     */
    public static function validateRole(string $role): array {
        $allowedRoles = ['employee', 'moderator', 'admin'];
        $errors = [];
        
        if (empty($role)) {
            $errors[] = "Le rôle est requis";
        } elseif (!in_array($role, $allowedRoles)) {
            $errors[] = "Rôle invalide. Rôles autorisés : " . implode(', ', $allowedRoles);
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation URL
     */
    public static function validateUrl(string $url): array {
        $errors = [];
        
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = "L'URL n'est pas valide";
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation téléphone (format français)
     */
    public static function validatePhone(string $phone): array {
        $errors = [];
        
        if (!empty($phone)) {
            // Supprimer les espaces et tirets
            $cleanPhone = preg_replace('/[\s\-\.]/', '', $phone);
            
            if (!preg_match('/^(?:\+33|0)[1-9](?:[0-9]{8})$/', $cleanPhone)) {
                $errors[] = "Le numéro de téléphone n'est pas valide (format français attendu)";
            }
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation titre/nom de contenu
     */
    public static function validateTitle(string $title, int $minLength = 3, int $maxLength = 200): array {
        $errors = [];
        
        if (empty($title)) {
            $errors[] = "Le titre est requis";
        } elseif (strlen($title) < $minLength) {
            $errors[] = "Le titre doit contenir au moins {$minLength} caractères";
        } elseif (strlen($title) > $maxLength) {
            $errors[] = "Le titre ne peut pas dépasser {$maxLength} caractères";
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation description/contenu
     */
    public static function validateDescription(string $description, int $maxLength = 2000): array {
        $errors = [];
        
        if (strlen($description) > $maxLength) {
            $errors[] = "La description ne peut pas dépasser {$maxLength} caractères";
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation de fichier upload
     */
    public static function validateFileUpload(array $file): array {
        $errors = [];
        
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'upload du fichier";
            return ['isValid' => false, 'errors' => $errors];
        }
        
        if (!isset($file['size']) || $file['size'] > (5 * 1024 * 1024)) { // 5MB max
            $errors[] = "Le fichier ne peut pas dépasser 5MB";
        }
        
        if (!isset($file['name']) || empty($file['name'])) {
            $errors[] = "Le nom du fichier est requis";
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validation complète d'un utilisateur
     */
    public static function validateUser(array $userData): array {
        $allErrors = [];
        
        // Validation du nom d'utilisateur
        $usernameValidation = self::validateUsername($userData['username'] ?? '');
        if (!$usernameValidation['isValid']) {
            $allErrors = array_merge($allErrors, $usernameValidation['errors']);
        }
        
        // Validation du nom complet
        $nameValidation = self::validateFullName($userData['name'] ?? '');
        if (!$nameValidation['isValid']) {
            $allErrors = array_merge($allErrors, $nameValidation['errors']);
        }
        
        // Validation de l'email (si fourni)
        if (!empty($userData['email'])) {
            $emailValidation = self::validateEmail($userData['email']);
            if (!$emailValidation['isValid']) {
                $allErrors = array_merge($allErrors, $emailValidation['errors']);
            }
        }
        
        // Validation du rôle
        $roleValidation = self::validateRole($userData['role'] ?? '');
        if (!$roleValidation['isValid']) {
            $allErrors = array_merge($allErrors, $roleValidation['errors']);
        }
        
        // Validation du mot de passe (si fourni)
        if (!empty($userData['password'])) {
            $passwordValidation = PasswordValidator::validatePasswordStrength($userData['password']);
            if (!$passwordValidation['isValid']) {
                $allErrors = array_merge($allErrors, $passwordValidation['errors']);
            }
        }
        
        return [
            'isValid' => empty($allErrors),
            'errors' => $allErrors
        ];
    }
}
?>