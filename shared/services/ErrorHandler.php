<?php
/**
 * Gestionnaire d'erreurs unifié PHP
 * Compatible avec la gestion d'erreurs TypeScript
 */

class ErrorHandler {
    
    /**
     * Gestionnaire d'erreurs global
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        // Ne pas traiter les erreurs supprimées par @
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $errorType = self::getErrorType($errno);
        
        Logger::error("PHP Error: {$errstr}", [
            'type' => $errorType,
            'file' => $errfile,
            'line' => $errline,
            'errno' => $errno
        ]);
        
        // En mode debug, afficher l'erreur
        if (APP_DEBUG) {
            echo "<br><b>{$errorType}:</b> {$errstr} in <b>{$errfile}</b> on line <b>{$errline}</b><br>";
        }
        
        return true;
    }
    
    /**
     * Gestionnaire d'exceptions global
     */
    public static function handleException(Throwable $exception): void {
        Logger::critical("Uncaught Exception: {$exception->getMessage()}", [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Réponse appropriée selon le contexte
        if (self::isApiRequest()) {
            ApiResponse::fromException($exception);
        } else {
            if (APP_DEBUG) {
                echo "<h1>Erreur fatale</h1>";
                echo "<p><strong>Message:</strong> {$exception->getMessage()}</p>";
                echo "<p><strong>Fichier:</strong> {$exception->getFile()}:{$exception->getLine()}</p>";
                echo "<pre>{$exception->getTraceAsString()}</pre>";
            } else {
                echo "<h1>Erreur du serveur</h1>";
                echo "<p>Une erreur inattendue s'est produite. Veuillez réessayer plus tard.</p>";
            }
        }
        
        exit(1);
    }
    
    /**
     * Gestionnaire d'erreurs fatales
     */
    public static function handleFatalError(): void {
        $error = error_get_last();
        
        if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR))) {
            Logger::critical("Fatal Error: {$error['message']}", [
                'file' => $error['file'],
                'line' => $error['line'],
                'type' => self::getErrorType($error['type'])
            ]);
            
            if (self::isApiRequest()) {
                ApiResponse::error("Erreur fatale du serveur", 500);
            } else {
                if (!APP_DEBUG) {
                    echo "<h1>Erreur du serveur</h1>";
                    echo "<p>Une erreur fatale s'est produite.</p>";
                }
            }
        }
    }
    
    /**
     * Initialiser les gestionnaires d'erreurs
     */
    public static function init(): void {
        // Gestionnaire d'erreurs
        set_error_handler([self::class, 'handleError']);
        
        // Gestionnaire d'exceptions
        set_exception_handler([self::class, 'handleException']);
        
        // Gestionnaire d'erreurs fatales
        register_shutdown_function([self::class, 'handleFatalError']);
        
        // Configuration des erreurs
        if (APP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
            ini_set('display_errors', '0');
        }
        
        ini_set('log_errors', '1');
    }
    
    /**
     * Obtenir le type d'erreur en texte
     */
    private static function getErrorType(int $errno): string {
        switch ($errno) {
            case E_ERROR:
                return 'Fatal Error';
            case E_WARNING:
                return 'Warning';
            case E_PARSE:
                return 'Parse Error';
            case E_NOTICE:
                return 'Notice';
            case E_CORE_ERROR:
                return 'Core Error';
            case E_CORE_WARNING:
                return 'Core Warning';
            case E_COMPILE_ERROR:
                return 'Compile Error';
            case E_COMPILE_WARNING:
                return 'Compile Warning';
            case E_USER_ERROR:
                return 'User Error';
            case E_USER_WARNING:
                return 'User Warning';
            case E_USER_NOTICE:
                return 'User Notice';
            case E_STRICT:
                return 'Strict Standards';
            case E_RECOVERABLE_ERROR:
                return 'Recoverable Error';
            case E_DEPRECATED:
                return 'Deprecated';
            case E_USER_DEPRECATED:
                return 'User Deprecated';
            default:
                return 'Unknown Error';
        }
    }
    
    /**
     * Vérifier si c'est une requête API
     */
    private static function isApiRequest(): bool {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($uri, '/api/') !== false || 
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
    
    /**
     * Créer une exception personnalisée avec contexte
     */
    public static function createException(string $message, int $code = 0, array $context = []): Exception {
        $contextStr = !empty($context) ? ' Context: ' . json_encode($context) : '';
        return new Exception($message . $contextStr, $code);
    }
    
    /**
     * Logger une erreur avec contexte
     */
    public static function logError(string $message, array $context = [], string $level = 'error'): void {
        switch ($level) {
            case 'debug':
                Logger::debug($message, $context);
                break;
            case 'info':
                Logger::info($message, $context);
                break;
            case 'warning':
                Logger::warning($message, $context);
                break;
            case 'critical':
                Logger::critical($message, $context);
                break;
            default:
                Logger::error($message, $context);
        }
    }
    
    /**
     * Valider et traiter les erreurs de validation
     */
    public static function handleValidationErrors(array $validationResult, string $defaultMessage = "Données invalides"): void {
        if (!$validationResult['isValid']) {
            $errors = $validationResult['errors'] ?? [];
            
            if (self::isApiRequest()) {
                ApiResponse::validationError($errors, $defaultMessage);
            } else {
                throw new Exception($defaultMessage . ': ' . implode(', ', $errors));
            }
        }
    }
    
    /**
     * Traiter les erreurs de base de données
     */
    public static function handleDatabaseError(Exception $e, string $operation = 'database operation'): void {
        Logger::database($operation, $e->getMessage());
        
        if (self::isApiRequest()) {
            $message = APP_DEBUG ? "Erreur base de données: " . $e->getMessage() : "Erreur de base de données";
            ApiResponse::error($message, 500);
        } else {
            throw new Exception("Erreur lors de l'opération: {$operation}");
        }
    }
}

// Initialiser le gestionnaire d'erreurs
ErrorHandler::init();
?>