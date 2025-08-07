<?php
/**
 * Configuration de base de données pour API PHP - IntraSphere
 * Compatible avec le schéma Drizzle existant
 */

class Database {
    private $connection = null;
    private $config;
    
    public function __construct() {
        // Configuration par défaut - adaptable selon l'environnement
        $this->config = [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'dbname' => $_ENV['DB_NAME'] ?? 'intrasphere',
            'username' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8mb4'
        ];
        
        // Détecter si c'est un environnement cPanel
        if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.') !== false) {
            // Configuration cPanel typique
            $this->detectCPanelConfig();
        }
    }
    
    private function detectCPanelConfig() {
        // Auto-détection des paramètres cPanel
        $domain_parts = explode('.', $_SERVER['HTTP_HOST']);
        $username = $domain_parts[0]; // Souvent le username cPanel
        
        $this->config = array_merge($this->config, [
            'host' => 'localhost',
            'dbname' => $username . '_intrasphere',
            'username' => $username . '_intrasphere',
            // Password doit être configuré manuellement
        ]);
    }
    
    public function getConnection() {
        if ($this->connection === null) {
            try {
                $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
                $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // Fallback vers SQLite si MySQL échoue
                try {
                    $this->connection = new PDO('sqlite:../data/intrasphere.db', null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    $this->createSQLiteTables();
                } catch (PDOException $sqlite_e) {
                    throw new Exception("Impossible de se connecter à la base de données: " . $e->getMessage());
                }
            }
        }
        return $this->connection;
    }
    
    private function createSQLiteTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id TEXT PRIMARY KEY DEFAULT (lower(hex(randomblob(16)))),
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            name TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'employee',
            avatar TEXT,
            employee_id TEXT UNIQUE,
            department TEXT,
            position TEXT,
            is_active BOOLEAN DEFAULT 1,
            phone TEXT,
            email TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS announcements (
            id TEXT PRIMARY KEY DEFAULT (lower(hex(randomblob(16)))),
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            type TEXT NOT NULL DEFAULT 'info',
            author_id TEXT,
            author_name TEXT NOT NULL,
            image_url TEXT,
            icon TEXT DEFAULT '📢',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            is_important BOOLEAN DEFAULT 0,
            FOREIGN KEY (author_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS documents (
            id TEXT PRIMARY KEY DEFAULT (lower(hex(randomblob(16)))),
            title TEXT NOT NULL,
            description TEXT,
            category TEXT NOT NULL,
            file_name TEXT NOT NULL,
            file_url TEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            version TEXT DEFAULT '1.0'
        );

        CREATE TABLE IF NOT EXISTS events (
            id TEXT PRIMARY KEY DEFAULT (lower(hex(randomblob(16)))),
            title TEXT NOT NULL,
            description TEXT,
            date DATETIME NOT NULL,
            location TEXT,
            type TEXT NOT NULL DEFAULT 'meeting',
            organizer_id TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (organizer_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS messages (
            id TEXT PRIMARY KEY DEFAULT (lower(hex(randomblob(16)))),
            sender_id TEXT NOT NULL,
            recipient_id TEXT NOT NULL,
            subject TEXT NOT NULL,
            content TEXT NOT NULL,
            is_read BOOLEAN DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id),
            FOREIGN KEY (recipient_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS complaints (
            id TEXT PRIMARY KEY DEFAULT (lower(hex(randomblob(16)))),
            submitter_id TEXT NOT NULL,
            assigned_to_id TEXT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'open',
            priority TEXT NOT NULL DEFAULT 'medium',
            category TEXT NOT NULL DEFAULT 'general',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (submitter_id) REFERENCES users(id),
            FOREIGN KEY (assigned_to_id) REFERENCES users(id)
        );
        ";
        
        $this->connection->exec($sql);
        
        // Insérer utilisateur admin par défaut si n'existe pas
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $this->insertDefaultAdmin();
        }
    }
    
    private function insertDefaultAdmin() {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, name, role, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['admin', $hashedPassword, 'Administrateur', 'admin', 1]);
        
        // Insérer quelques données de démonstration
        $this->insertSampleData();
    }
    
    private function insertSampleData() {
        // Insérer quelques annonces de démonstration
        $announcements = [
            [
                'title' => 'Bienvenue sur IntraSphere',
                'content' => 'Plateforme intranet mise en ligne avec succès. Toutes les fonctionnalités sont disponibles.',
                'type' => 'info',
                'author_name' => 'Administrateur',
                'icon' => '🎉',
                'is_important' => 1
            ],
            [
                'title' => 'Maintenance programmée',
                'content' => 'Une maintenance de routine aura lieu ce weekend pour optimiser les performances.',
                'type' => 'important',
                'author_name' => 'Administrateur',
                'icon' => '🔧',
                'is_important' => 0
            ]
        ];
        
        foreach ($announcements as $announcement) {
            $stmt = $this->connection->prepare("
                INSERT INTO announcements (title, content, type, author_name, icon, is_important) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $announcement['title'],
                $announcement['content'],
                $announcement['type'],
                $announcement['author_name'],
                $announcement['icon'],
                $announcement['is_important']
            ]);
        }
    }
    
    public function createMySQLTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
            username VARCHAR(255) NOT NULL UNIQUE,
            password TEXT NOT NULL,
            name TEXT NOT NULL,
            role VARCHAR(50) NOT NULL DEFAULT 'employee',
            avatar TEXT,
            employee_id VARCHAR(255) UNIQUE,
            department VARCHAR(255),
            position VARCHAR(255),
            is_active BOOLEAN DEFAULT TRUE,
            phone VARCHAR(50),
            email VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS announcements (
            id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            type VARCHAR(50) NOT NULL DEFAULT 'info',
            author_id VARCHAR(36),
            author_name TEXT NOT NULL,
            image_url TEXT,
            icon VARCHAR(10) DEFAULT '📢',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_important BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS documents (
            id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
            title TEXT NOT NULL,
            description TEXT,
            category VARCHAR(100) NOT NULL,
            file_name TEXT NOT NULL,
            file_url TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            version VARCHAR(10) DEFAULT '1.0'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS events (
            id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
            title TEXT NOT NULL,
            description TEXT,
            date TIMESTAMP NOT NULL,
            location TEXT,
            type VARCHAR(50) NOT NULL DEFAULT 'meeting',
            organizer_id VARCHAR(36),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS messages (
            id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
            sender_id VARCHAR(36) NOT NULL,
            recipient_id VARCHAR(36) NOT NULL,
            subject TEXT NOT NULL,
            content TEXT NOT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS complaints (
            id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
            submitter_id VARCHAR(36) NOT NULL,
            assigned_to_id VARCHAR(36),
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'open',
            priority VARCHAR(10) NOT NULL DEFAULT 'medium',
            category VARCHAR(50) NOT NULL DEFAULT 'general',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $this->connection->exec($sql);
        
        // Insérer utilisateur admin par défaut si n'existe pas
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $this->insertDefaultAdmin();
        }
    }
}
?>