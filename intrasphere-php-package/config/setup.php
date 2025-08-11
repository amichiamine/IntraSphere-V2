<?php
/**
 * Script de configuration rapide
 * Génère la configuration selon le type d'hébergement
 */

class DatabaseSetup {
    private $hostingTypes = [
        'cpanel' => [
            'name' => 'cPanel (Hébergement mutualisé)',
            'template' => [
                'host' => 'localhost',
                'port' => '3306',
                'driver' => 'mysql',
                'dbname_format' => '{user}_{dbname}',
                'username_format' => '{user}_{dbname}'
            ]
        ],
        'ovh' => [
            'name' => 'OVH Mutualisé',
            'template' => [
                'host' => 'mysql-{dbname}.hosting.ovh.net',
                'port' => '3306',
                'driver' => 'mysql',
                'dbname_format' => '{dbname}',
                'username_format' => '{dbname}'
            ]
        ],
        'ionos' => [
            'name' => '1&1 / Ionos',
            'template' => [
                'host' => 'db{id}.hosting.1and1.com',
                'port' => '3306',
                'driver' => 'mysql',
                'dbname_format' => 'db{id}',
                'username_format' => 'dbo{id}'
            ]
        ],
        'vps' => [
            'name' => 'VPS / Serveur Dédié',
            'template' => [
                'host' => '127.0.0.1',
                'port' => '3306',
                'driver' => 'mysql',
                'dbname_format' => '{dbname}',
                'username_format' => '{username}'
            ]
        ],
        'postgresql' => [
            'name' => 'PostgreSQL',
            'template' => [
                'host' => 'localhost',
                'port' => '5432',
                'driver' => 'pgsql',
                'dbname_format' => '{dbname}',
                'username_format' => '{username}'
            ]
        ],
        'local' => [
            'name' => 'Développement Local (XAMPP/WAMP)',
            'template' => [
                'host' => 'localhost',
                'port' => '3306',
                'driver' => 'mysql',
                'dbname_format' => '{dbname}',
                'username_format' => 'root',
                'password' => ''
            ]
        ]
    ];
    
    public function generateConfig($hostingType, $params) {
        if (!isset($this->hostingTypes[$hostingType])) {
            throw new Exception("Type d'hébergement non supporté");
        }
        
        $template = $this->hostingTypes[$hostingType]['template'];
        $config = [];
        
        foreach ($template as $key => $value) {
            if (is_string($value)) {
                // Remplacer les placeholders
                $config[$key] = $this->replacePlaceholders($value, $params);
            } else {
                $config[$key] = $value;
            }
        }
        
        // Ajouter les valeurs personnalisées
        $config = array_merge($config, $params);
        
        return $config;
    }
    
    private function replacePlaceholders($template, $params) {
        foreach ($params as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
    
    public function generateEnvFile($config) {
        $content = "# Configuration IntraSphere générée automatiquement\n";
        $content .= "# " . date('Y-m-d H:i:s') . "\n\n";
        
        $content .= "DB_DRIVER={$config['driver']}\n";
        $content .= "DB_HOST={$config['host']}\n";
        $content .= "DB_PORT={$config['port']}\n";
        $content .= "DB_NAME={$config['dbname']}\n";
        $content .= "DB_USER={$config['username']}\n";
        $content .= "DB_PASSWORD={$config['password']}\n\n";
        
        $content .= "APP_ENV=production\n";
        $content .= "SESSION_SECRET=" . bin2hex(random_bytes(32)) . "\n";
        
        return $content;
    }
    
    public function getHostingTypes() {
        return $this->hostingTypes;
    }
    
    public function testConnection($config) {
        try {
            $dsn = $config['driver'] === 'mysql' ? 
                "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4" :
                "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5  // Timeout 5 secondes
            ]);
            
            return ['success' => true, 'message' => 'Connexion réussie'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>