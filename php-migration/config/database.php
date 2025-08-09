<?php
/**
 * Configuration de la base de données
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        // Charger les variables d'environnement depuis .env
        $this->loadEnvFile();
        
        try {
            // Configuration par défaut (à adapter selon l'hébergement)
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $dbname = $_ENV['DB_NAME'] ?? 'intrasphere';
            $username = $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? '';
            
            // Support MySQL et PostgreSQL
            $driver = $_ENV['DB_DRIVER'] ?? 'mysql';
            
            if ($driver === 'mysql') {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            } else {
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
            }
            
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur connexion base de données: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }
    }
    
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchOne(string $sql, array $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function execute(string $sql, array $params = []): bool {
        return $this->query($sql, $params)->rowCount() > 0;
    }
    
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Charger le fichier .env
     */
    private function loadEnvFile() {
        $envFile = __DIR__ . '/../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Ignorer les commentaires
                if (strpos(trim($line), '#') === 0) continue;
                
                // Séparer clé=valeur
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
    }
}

// Instance globale
$GLOBALS['db'] = Database::getInstance();

/**
 * Helper pour récupérer la connexion DB
 */
function db(): Database {
    return $GLOBALS['db'];
}
?>