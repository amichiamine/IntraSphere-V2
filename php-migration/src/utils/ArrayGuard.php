<?php
/**
 * Utilitaire de protection contre les erreurs d'array
 * Équivalent aux corrections React pour éviter "filter is not a function"
 */

class ArrayGuard {
    
    /**
     * S'assurer qu'une variable est un tableau
     * Équivalent à Array.isArray() en JavaScript
     */
    public static function ensureArray($data): array {
        if (!is_array($data)) {
            return [];
        }
        return $data;
    }
    
    /**
     * Filtrer de façon sécurisée
     * Équivalent à safeMessages.filter() en React
     */
    public static function safeFilter($data, callable $callback = null): array {
        $safeData = self::ensureArray($data);
        
        if ($callback === null) {
            return $safeData;
        }
        
        return array_filter($safeData, $callback);
    }
    
    /**
     * Mapper de façon sécurisée
     * Équivalent à safeMessages.map() en React
     */
    public static function safeMap($data, callable $callback): array {
        $safeData = self::ensureArray($data);
        return array_map($callback, $safeData);
    }
    
    /**
     * Compter de façon sécurisée
     * Équivalent à safeMessages.length en React
     */
    public static function safeCount($data): int {
        $safeData = self::ensureArray($data);
        return count($safeData);
    }
    
    /**
     * Validation avec message d'erreur détaillé
     */
    public static function validateArrayInput($data, string $fieldName = 'data'): array {
        if ($data === null) {
            throw new InvalidArgumentException("$fieldName ne peut pas être null");
        }
        
        if (!is_array($data)) {
            $type = gettype($data);
            throw new InvalidArgumentException("$fieldName doit être un tableau, $type fourni");
        }
        
        return $data;
    }
    
    /**
     * Filtrer les IDs valides pour les opérations bulk
     * Similaire à la correction dans Message.php
     */
    public static function filterValidIds($ids): array {
        $safeIds = self::ensureArray($ids);
        
        return array_filter($safeIds, function($id) {
            return !empty($id) && (is_string($id) || is_numeric($id));
        });
    }
    
    /**
     * Protection contre les erreurs de sérialisation JSON
     */
    public static function safeJsonResponse($data): array {
        if ($data === null) {
            return [];
        }
        
        if (is_array($data)) {
            return $data;
        }
        
        // Si c'est un objet, le convertir en tableau
        if (is_object($data)) {
            return json_decode(json_encode($data), true) ?: [];
        }
        
        // Pour les types primitifs, les wrapper dans un tableau
        return [$data];
    }
    
    /**
     * Validation avancée pour les opérations de messagerie
     */
    public static function validateMessageIds($messageIds, bool $allowEmpty = false): array {
        if (!$allowEmpty && empty($messageIds)) {
            throw new InvalidArgumentException('La liste des IDs de messages ne peut pas être vide');
        }
        
        $safeIds = self::filterValidIds($messageIds);
        
        if (!$allowEmpty && empty($safeIds)) {
            throw new InvalidArgumentException('Aucun ID de message valide fourni');
        }
        
        return array_values($safeIds); // Réindexer le tableau
    }
}