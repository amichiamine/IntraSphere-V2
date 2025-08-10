<?php
/**
 * Assistant de validation unifié
 * Compatible avec les validations frontend et backend
 */

class ValidationHelper {
    
    /**
     * Règles de validation disponibles
     */
    private static array $rules = [
        'required' => 'validateRequired',
        'email' => 'validateEmail',
        'min_length' => 'validateMinLength',
        'max_length' => 'validateMaxLength',
        'numeric' => 'validateNumeric',
        'integer' => 'validateInteger',
        'boolean' => 'validateBoolean',
        'date' => 'validateDate',
        'url' => 'validateUrl',
        'regex' => 'validateRegex',
        'in' => 'validateIn',
        'unique' => 'validateUnique',
        'exists' => 'validateExists'
    ];
    
    /**
     * Valider un ensemble de données
     */
    public static function validate(array $data, array $rules): array {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                if (isset(self::$rules[$ruleName])) {
                    $method = self::$rules[$ruleName];
                    $result = self::$method($value, $ruleValue, $field, $data);
                    
                    if ($result !== true) {
                        $errors[$field][] = $result;
                    }
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Validation: champ requis
     */
    private static function validateRequired($value, $ruleValue, $field): bool|string {
        if (is_null($value) || $value === '') {
            return "Le champ {$field} est requis";
        }
        return true;
    }
    
    /**
     * Validation: format email
     */
    private static function validateEmail($value, $ruleValue, $field): bool|string {
        if (!is_null($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "Le champ {$field} doit être un email valide";
        }
        return true;
    }
    
    /**
     * Validation: longueur minimale
     */
    private static function validateMinLength($value, $ruleValue, $field): bool|string {
        if (!is_null($value) && strlen($value) < (int)$ruleValue) {
            return "Le champ {$field} doit contenir au moins {$ruleValue} caractères";
        }
        return true;
    }
    
    /**
     * Validation: longueur maximale
     */
    private static function validateMaxLength($value, $ruleValue, $field): bool|string {
        if (!is_null($value) && strlen($value) > (int)$ruleValue) {
            return "Le champ {$field} ne peut contenir plus de {$ruleValue} caractères";
        }
        return true;
    }
    
    /**
     * Validation: valeur numérique
     */
    private static function validateNumeric($value, $ruleValue, $field): bool|string {
        if (!is_null($value) && !is_numeric($value)) {
            return "Le champ {$field} doit être numérique";
        }
        return true;
    }
    
    /**
     * Validation: entier
     */
    private static function validateInteger($value, $ruleValue, $field): bool|string {
        if (!is_null($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
            return "Le champ {$field} doit être un entier";
        }
        return true;
    }
    
    /**
     * Validation: booléen
     */
    private static function validateBoolean($value, $ruleValue, $field): bool|string {
        if (!is_null($value) && !is_bool($value) && !in_array($value, [0, 1, '0', '1', 'true', 'false'])) {
            return "Le champ {$field} doit être un booléen";
        }
        return true;
    }
    
    /**
     * Validation: format date
     */
    private static function validateDate($value, $ruleValue, $field): bool|string {
        if (!is_null($value)) {
            $format = $ruleValue ?: 'Y-m-d H:i:s';
            $date = DateTime::createFromFormat($format, $value);
            if (!$date || $date->format($format) !== $value) {
                return "Le champ {$field} doit être une date au format {$format}";
            }
        }
        return true;
    }
    
    /**
     * Validation: format URL
     */
    private static function validateUrl($value, $ruleValue, $field): bool|string {
        if (!is_null($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            return "Le champ {$field} doit être une URL valide";
        }
        return true;
    }
    
    /**
     * Validation: expression régulière
     */
    private static function validateRegex($value, $ruleValue, $field): bool|string {
        if (!is_null($value) && !preg_match($ruleValue, $value)) {
            return "Le champ {$field} n'a pas le format attendu";
        }
        return true;
    }
    
    /**
     * Validation: valeur dans liste
     */
    private static function validateIn($value, $ruleValue, $field): bool|string {
        if (!is_null($value)) {
            $allowedValues = explode(',', $ruleValue);
            if (!in_array($value, $allowedValues)) {
                return "Le champ {$field} doit être l'une des valeurs: " . implode(', ', $allowedValues);
            }
        }
        return true;
    }
    
    /**
     * Validation: valeur unique en base
     */
    private static function validateUnique($value, $ruleValue, $field, $data): bool|string {
        if (!is_null($value)) {
            [$table, $column] = explode(',', $ruleValue . ',' . $field);
            $column = $column ?: $field;
            
            $db = Database::getInstance();
            $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
            $params = [$value];
            
            // Exclure l'ID actuel pour les mises à jour
            if (isset($data['id'])) {
                $sql .= " AND id != ?";
                $params[] = $data['id'];
            }
            
            $result = $db->fetchOne($sql, $params);
            if ($result['count'] > 0) {
                return "Le champ {$field} doit être unique";
            }
        }
        return true;
    }
    
    /**
     * Validation: valeur existe en base
     */
    private static function validateExists($value, $ruleValue, $field): bool|string {
        if (!is_null($value)) {
            [$table, $column] = explode(',', $ruleValue . ',' . $field);
            $column = $column ?: 'id';
            
            $db = Database::getInstance();
            $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
            $result = $db->fetchOne($sql, [$value]);
            
            if ($result['count'] === 0) {
                return "Le champ {$field} référence un élément inexistant";
            }
        }
        return true;
    }
    
    /**
     * Validation spéciale: mot de passe fort
     */
    public static function validateStrongPassword(string $password): array {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial";
        }
        
        return $errors;
    }
    
    /**
     * Sanitisation des données
     */
    public static function sanitize(array $data, array $rules = []): array {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Nettoyage de base
                $value = trim($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                
                // Règles spécifiques
                if (isset($rules[$key])) {
                    switch ($rules[$key]) {
                        case 'email':
                            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                            break;
                        case 'url':
                            $value = filter_var($value, FILTER_SANITIZE_URL);
                            break;
                        case 'int':
                            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                            break;
                        case 'float':
                            $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            break;
                    }
                }
            }
            
            $sanitized[$key] = $value;
        }
        
        return $sanitized;
    }
}
?>