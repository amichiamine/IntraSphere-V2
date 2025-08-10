<?php
/**
 * Validateur de mots de passe unifié
 * Conforme aux standards TypeScript (AuthService)
 */

class PasswordValidator {
    
    /**
     * Valider la force d'un mot de passe selon les standards de sécurité
     * Conforme à server/services/auth.ts validatePasswordStrength()
     */
    public static function validatePasswordStrength(string $password): array {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }
        
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial';
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Valider un mot de passe avec gestion des erreurs
     */
    public static function validate(string $password): bool {
        $validation = self::validatePasswordStrength($password);
        return $validation['isValid'];
    }
    
    /**
     * Obtenir les erreurs de validation
     */
    public static function getValidationErrors(string $password): array {
        $validation = self::validatePasswordStrength($password);
        return $validation['errors'];
    }
    
    /**
     * Hacher un mot de passe de manière sécurisée
     * Conforme aux standards TypeScript
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Vérifier un mot de passe contre son hash
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Générer un mot de passe temporaire sécurisé
     */
    public static function generateTemporaryPassword(int $length = 12): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        // Garantir au moins un caractère de chaque type
        $password .= chr(rand(65, 90)); // Majuscule
        $password .= chr(rand(97, 122)); // Minuscule
        $password .= chr(rand(48, 57)); // Chiffre
        $password .= '!@#$%^&*'[rand(0, 7)]; // Caractère spécial
        
        // Compléter avec des caractères aléatoires
        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        // Mélanger les caractères
        return str_shuffle($password);
    }
}
?>