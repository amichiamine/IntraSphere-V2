<?php
/**
 * Générateur de package IntraSphere PHP
 * Crée un package ZIP prêt pour déploiement
 */

// Configuration
$packageName = 'intrasphere-php-' . date('Y-m-d');
$tempDir = 'temp_package';

// Créer le dossier temporaire
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

// Structure des fichiers à inclure
$files = [
    // Fichiers de configuration
    'config/bootstrap.php',
    'config/app.php', 
    'config/database.php',
    'config/database-examples.php',
    'config/setup.php',
    
    // Fichiers source
    'src/Router.php',
    'src/controllers/BaseController.php',
    'src/controllers/AuthController.php',
    'src/controllers/DashboardController.php',
    'src/controllers/AdminController.php',
    'src/controllers/AnnouncementsController.php',
    'src/controllers/DocumentsController.php',
    'src/controllers/MessagesController.php',
    'src/controllers/TrainingsController.php',
    'src/controllers/ErrorController.php',
    'src/controllers/UploadController.php',
    
    // Contrôleurs API
    'src/controllers/Api/AuthController.php',
    'src/controllers/Api/AdminController.php',
    'src/controllers/Api/AnnouncementsController.php',
    'src/controllers/Api/DocumentsController.php',
    'src/controllers/Api/MessagesController.php',
    'src/controllers/Api/TrainingsController.php',
    'src/controllers/Api/UsersController.php',
    'src/controllers/Api/NotificationsController.php',
    'src/controllers/Api/SystemController.php',
    'src/controllers/Api/ComplaintsController.php',
    'src/controllers/Api/EventsController.php',
    
    // Modèles
    'src/models/BaseModel.php',
    'src/models/User.php',
    'src/models/Announcement.php',
    'src/models/Document.php',
    'src/models/Message.php',
    'src/models/Training.php',
    'src/models/Complaint.php',
    'src/models/Event.php',
    'src/models/Permission.php',
    'src/models/Content.php',
    
    // Utilitaires
    'src/utils/helpers.php',
    'src/utils/ResponseFormatter.php',
    'src/utils/Logger.php',
    'src/utils/CacheManager.php',
    'src/utils/CacheManagerOptimized.php',
    'src/utils/NotificationManager.php',
    'src/utils/PasswordValidator.php',
    'src/utils/PermissionManager.php',
    'src/utils/RateLimiter.php',
    'src/utils/ValidationHelper.php',
    'src/utils/ArrayGuard.php',
    
    // Vues
    'views/layout/app.php',
    'views/auth/login.php',
    'views/dashboard/index.php',
    'views/admin/index.php',
    'views/announcements/index.php',
    'views/announcements/create.php',
    'views/documents/index.php',
    'views/messages/index.php',
    'views/trainings/index.php',
    'views/error/404.php',
    'views/error/500.php',
    
    // SQL
    'sql/create_tables.sql',
    'sql/insert_demo_data.sql',
    
    // Fichier principal
    'index.php'
];

// Créer la structure de dossiers dans le package
$directories = [
    'config',
    'src',
    'src/controllers',
    'src/controllers/Api',
    'src/models',
    'src/utils',
    'views',
    'views/admin',
    'views/announcements',
    'views/auth',
    'views/dashboard',
    'views/documents',
    'views/error',
    'views/layout',
    'views/messages',
    'views/trainings',
    'public',
    'public/uploads',
    'logs',
    'sql'
];

foreach ($directories as $dir) {
    $fullPath = $tempDir . '/' . $dir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
    }
}

// Copier les fichiers existants
foreach ($files as $file) {
    $sourcePath = $file;
    $destPath = $tempDir . '/' . $file;
    
    if (file_exists($sourcePath)) {
        $destDir = dirname($destPath);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        copy($sourcePath, $destPath);
        echo "✓ Copié: $file\n";
    } else {
        echo "⚠ Fichier manquant: $file\n";
    }
}

// Copier l'installateur
copy('install.php', $tempDir . '/install.php');

// Créer un fichier README pour le package
$readmeContent = "# IntraSphere PHP - Package de Déploiement

## Installation Rapide

1. Extrayez tous les fichiers sur votre serveur web
2. Ouvrez votre navigateur et allez sur : http://votre-domaine.com/install.php
3. Suivez l'assistant d'installation automatisé
4. Supprimez le fichier install.php après installation

## Configuration Requise

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur (ou MariaDB 10.2+)
- Extensions PHP : PDO, PDO_MySQL, JSON, OpenSSL
- Serveur web : Apache ou Nginx

## Fonctionnalités Incluses

✅ Système d'authentification complet
✅ Gestion des utilisateurs et rôles
✅ Annonces et communications
✅ Gestion documentaire
✅ Système de messagerie interne
✅ Module de formations
✅ Système de réclamations
✅ Tableau de bord admin
✅ API REST complète
✅ Interface responsive (mobile-friendly)
✅ Sécurité renforcée (CSRF, XSS, SQL injection)

## Support Hébergeurs

- ✅ cPanel (hébergement mutualisé)
- ✅ OVH Mutualisé
- ✅ 1&1 / Ionos
- ✅ Développement local (XAMPP/WAMP)
- ✅ VPS et serveurs dédiés

## Comptes par Défaut

Après installation, vous pourrez vous connecter avec :

**Administrateur :**
- Nom d'utilisateur : admin
- Mot de passe : (défini pendant l'installation)

**Comptes de test :**
- marie.martin / password123 (Employé)
- pierre.dubois / password123 (Modérateur)

## Structure du Projet

```
intrasphere-php/
├── config/              # Configuration application
├── src/
│   ├── controllers/     # Contrôleurs web et API
│   ├── models/         # Modèles de données
│   └── utils/          # Utilitaires et helpers
├── views/              # Templates et vues
├── public/             # Fichiers publics et uploads
├── sql/                # Scripts SQL
├── logs/               # Fichiers de log
└── index.php           # Point d'entrée principal
```

## Sécurité

- Protection CSRF sur tous les formulaires
- Validation et échappement des données
- Hachage sécurisé des mots de passe
- Headers de sécurité HTTP
- Protection contre les injections SQL
- Rate limiting sur les APIs

## Support

Pour toute question ou assistance, consultez la documentation complète ou contactez l'équipe de développement.

---

**Version :** 1.0.0
**Date :** " . date('Y-m-d') . "
**Compatibilité :** PHP 7.4+, MySQL 5.7+
";

file_put_contents($tempDir . '/README.md', $readmeContent);

// Créer un fichier .env.example
$envExample = "# Configuration IntraSphere - Exemple
# Copiez ce fichier en .env et adaptez les valeurs

DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=intrasphere
DB_USER=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

APP_ENV=production
SESSION_SECRET=genere_automatiquement_lors_installation

# Sécurité
ALLOWED_ORIGINS=*
RATE_LIMIT_ENABLED=true
RATE_LIMIT_MAX_REQUESTS=100
RATE_LIMIT_WINDOW=3600

# Upload
MAX_FILE_SIZE=10485760
ALLOWED_EXTENSIONS=pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif

# Email (optionnel)
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME=IntraSphere
";

file_put_contents($tempDir . '/.env.example', $envExample);

// Créer un fichier .htaccess de base
$htaccessContent = "# IntraSphere - Configuration Apache
RewriteEngine On

# Redirection des erreurs
ErrorDocument 404 /views/error/404.php
ErrorDocument 500 /views/error/500.php

# Sécurité - Masquer les fichiers sensibles
<Files \".env\">
    Order allow,deny
    Deny from all
</Files>

<Files \"*.log\">
    Order allow,deny
    Deny from all
</Files>

# Protection contre les injections
<IfModule mod_rewrite.c>
    RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
    RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} proc/self/environ [OR]
    RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|\%3D) [OR]
    RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [OR]
    RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC]
    RewriteRule .* - [F]
</IfModule>

# Headers de sécurité
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains\"
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
</IfModule>

# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache des ressources statiques
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType image/png \"access plus 1 month\"
    ExpiresByType image/jpg \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 month\"
    ExpiresByType image/gif \"access plus 1 month\"
    ExpiresByType image/ico \"access plus 1 month\"
</IfModule>
";

file_put_contents($tempDir . '/.htaccess', $htaccessContent);

// Créer le fichier de configuration pour Nginx (optionnel)
$nginxConfig = "# Configuration Nginx pour IntraSphere
# À adapter selon votre configuration

server {
    listen 80;
    server_name votre-domaine.com;
    root /path/to/intrasphere;
    index index.php;

    # Sécurité
    location ~ /\\.env {
        deny all;
    }
    
    location ~ \\.log$ {
        deny all;
    }

    # PHP
    location ~ \\.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Headers de sécurité
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection \"1; mode=block\";
    add_header Strict-Transport-Security \"max-age=31536000; includeSubDomains\";
    add_header Referrer-Policy \"strict-origin-when-cross-origin\";

    # Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Cache statique
    location ~* \\.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 30d;
        add_header Cache-Control \"public, immutable\";
    }
}
";

file_put_contents($tempDir . '/nginx.conf.example', $nginxConfig);

echo "\n" . str_repeat("=", 50) . "\n";
echo "PACKAGE INTRASPHERE PHP GÉNÉRÉ AVEC SUCCÈS !\n";
echo str_repeat("=", 50) . "\n\n";

echo "Fichiers inclus dans le package :\n";
echo "📁 $tempDir/\n";
echo "├── 📄 install.php (Installateur automatisé)\n";
echo "├── 📄 README.md (Documentation)\n";
echo "├── 📄 .env.example (Configuration exemple)\n";
echo "├── 📄 .htaccess (Configuration Apache)\n";
echo "├── 📄 nginx.conf.example (Configuration Nginx)\n";
echo "├── 📁 config/ (Configuration application)\n";
echo "├── 📁 src/ (Code source)\n";
echo "│   ├── 📁 controllers/ (Contrôleurs web et API)\n";
echo "│   ├── 📁 models/ (Modèles de données)\n";
echo "│   └── 📁 utils/ (Utilitaires)\n";
echo "├── 📁 views/ (Templates)\n";
echo "├── 📁 public/ (Fichiers publics)\n";
echo "├── 📁 sql/ (Scripts base de données)\n";
echo "└── 📄 index.php (Point d'entrée)\n\n";

echo "✅ Package prêt pour déploiement !\n";
echo "📦 Dossier : $tempDir/\n\n";

echo "PROCHAINES ÉTAPES :\n";
echo "1. Créer l'archive ZIP\n";
echo "2. Télécharger sur l'hébergement\n";
echo "3. Extraire les fichiers\n";
echo "4. Lancer install.php\n\n";

// Créer l'archive ZIP
if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    $zipFile = $packageName . '.zip';
    
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        
        // Fonction récursive pour ajouter tous les fichiers
        function addFolderToZip($zip, $folder, $zipFolder = '') {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $filePath = $file->getRealPath();
                    $relativePath = $zipFolder . substr($filePath, strlen(realpath($folder)) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
        
        addFolderToZip($zip, $tempDir);
        $zip->close();
        
        echo "📦 ARCHIVE ZIP CRÉÉE : $zipFile\n";
        echo "📊 Taille : " . number_format(filesize($zipFile) / 1024, 2) . " KB\n\n";
        
        echo "🎉 PACKAGE PLUG & PLAY PRÊT !\n";
        echo "═══════════════════════════════════════\n\n";
        
    } else {
        echo "❌ Erreur lors de la création de l'archive ZIP\n";
    }
} else {
    echo "⚠ Extension ZipArchive non disponible\n";
    echo "Vous pouvez créer manuellement l'archive du dossier : $tempDir\n";
}

// Nettoyage optionnel
echo "Voulez-vous supprimer le dossier temporaire ? (y/N): ";
if (trim(fgets(STDIN)) === 'y') {
    function deleteDirectory($dir) {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
    
    deleteDirectory($tempDir);
    echo "🧹 Dossier temporaire supprimé\n";
}

echo "\n✨ GÉNÉRATION TERMINÉE !\n";
?>