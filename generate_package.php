<?php
/**
 * Générateur de package final IntraSphere PHP
 * Crée un package ZIP complet avec tous les outils
 */

// Configuration
$timestamp = date('Y-m-d-H-i-s');
$packageName = "intrasphere-php-package-{$timestamp}.zip";

echo "<h1>📦 Génération du Package IntraSphere</h1>";

// Créer l'archive ZIP
$zip = new ZipArchive();
$result = $zip->open($packageName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

if ($result !== TRUE) {
    die("Erreur lors de la création du ZIP: $result");
}

echo "<h2>Ajout des fichiers au package...</h2>";

// Fonction pour ajouter récursivement un dossier
function addFolderToZip($zip, $folder, $baseFolder = '') {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = $baseFolder . substr($filePath, strlen($folder) + 1);
            
            // Exclure certains fichiers
            if (strpos($relativePath, '.git') === false && 
                strpos($relativePath, 'node_modules') === false &&
                strpos($relativePath, '.zip') === false) {
                $zip->addFile($filePath, $relativePath);
                echo "✅ " . $relativePath . "<br>";
            }
        }
    }
}

// Ajouter le dossier php-migration complet
if (is_dir('php-migration')) {
    addFolderToZip($zip, 'php-migration', 'intrasphere/');
    echo "<p>✅ Dossier php-migration ajouté</p>";
}

// Ajouter les scripts d'installation et de diagnostic
$rootFiles = [
    'install_fixed.php' => 'Installation corrigée',
    'reset_installation.php' => 'Reset de l\'installation',
    'debug_index.php' => 'Diagnostic système',
    'simple_index.php' => 'Version simplifiée',
    'test_intrasphere.php' => 'Script de test final',
    'index_fixed.php' => 'Index corrigé de référence'
];

foreach ($rootFiles as $file => $description) {
    if (file_exists($file)) {
        $zip->addFile($file, $file);
        echo "✅ $description ($file)<br>";
    }
}

// Créer un fichier README pour le package
$readmeContent = "# IntraSphere PHP - Package Complet

## 📋 Contenu du Package

### 🚀 Application Principale
- **intrasphere/** : Application PHP complète
- **intrasphere/.env** : Configuration de base de données
- **intrasphere/index.php** : Point d'entrée principal (CORRIGÉ)

### 🔧 Scripts d'Installation
- **install_fixed.php** : Installation automatique corrigée
- **reset_installation.php** : Reset complet de l'installation

### 🧪 Scripts de Diagnostic
- **debug_index.php** : Diagnostic complet du système
- **simple_index.php** : Version simplifiée fonctionnelle
- **test_intrasphere.php** : Tests finaux et vérifications
- **index_fixed.php** : Version de référence corrigée

## 🛠️ Installation

### Étape 1 : Téléchargement
1. Extrayez ce package sur votre serveur web
2. Assurez-vous que PHP 7.4+ et MySQL 5.7+ sont installés

### Étape 2 : Installation Automatique
1. Accédez à **install_fixed.php** dans votre navigateur
2. Suivez l'assistant d'installation
3. Configurez votre base de données

### Étape 3 : Tests
1. Utilisez **test_intrasphere.php** pour vérifier l'installation
2. Testez la connexion avec **simple_index.php**
3. Accédez à l'application via **intrasphere/index.php**

## 👥 Comptes de Test

- **Administrateur :** admin / admin123
- **Employé :** marie.martin / password123  
- **Modérateur :** pierre.dubois / password123

## 🚨 Résolution de Problèmes

### Erreur 500
1. Exécutez **debug_index.php** pour identifier le problème
2. Utilisez **reset_installation.php** pour nettoyer
3. Relancez l'installation avec **install_fixed.php**

### Base de Données
- Vérifiez la configuration dans **.env**
- Testez la connexion avec **debug_index.php**
- Assurez-vous que MySQL est accessible

## 📁 Structure

```
intrasphere-php-package/
├── intrasphere/               # Application principale
│   ├── config/               # Configuration
│   ├── src/                  # Code source
│   ├── views/                # Templates
│   ├── public/               # Assets publics
│   ├── .env                  # Configuration DB
│   └── index.php            # Point d'entrée
├── install_fixed.php         # Installation
├── debug_index.php          # Diagnostic
├── simple_index.php         # Version test
└── README.md               # Ce fichier
```

## 🌟 Fonctionnalités

- **Dashboard** : Vue d'ensemble avec statistiques
- **Annonces** : Gestion des communications
- **Documents** : Partage de fichiers
- **Messages** : Messagerie interne
- **Formations** : Gestion e-learning
- **Administration** : Gestion utilisateurs
- **Réclamations** : Suivi des demandes

## 🔒 Sécurité

- Authentification sécurisée
- Contrôle d'accès basé sur les rôles
- Protection contre les attaques courantes
- Sessions sécurisées

## 📞 Support

En cas de problème :
1. Consultez **debug_index.php** pour le diagnostic
2. Vérifiez les logs de votre hébergeur
3. Assurez-vous de la compatibilité PHP/MySQL

---
**IntraSphere** - Plateforme intranet d'entreprise
Version : 2.0 - Package PHP Complet
Date : " . date('Y-m-d H:i:s') . "
";

$zip->addFromString('README.md', $readmeContent);

// Créer un fichier de version
$versionInfo = [
    'version' => '2.0',
    'type' => 'PHP Package Complet',
    'date' => date('Y-m-d H:i:s'),
    'status' => 'Production Ready',
    'features' => [
        'Installation automatique corrigée',
        'Diagnostic système complet', 
        'Application PHP complète',
        'Scripts de maintenance',
        'Documentation complète'
    ],
    'requirements' => [
        'PHP 7.4+',
        'MySQL 5.7+',
        'Apache/Nginx',
        'Extension PDO'
    ]
];

$zip->addFromString('VERSION.json', json_encode($versionInfo, JSON_PRETTY_PRINT));

// Fermer l'archive
$zip->close();

echo "<h2>✅ Package généré avec succès !</h2>";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>📦 Package créé : $packageName</h3>";
echo "<p><strong>Taille :</strong> " . formatBytes(filesize($packageName)) . "</p>";
echo "<p><strong>Fichiers inclus :</strong> " . $zip->numFiles . "</p>";
echo "<p><a href='$packageName' download style='background: #28a745; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;'>⬇️ Télécharger le Package</a></p>";
echo "</div>";

echo "<h2>📋 Contenu du Package</h2>";
echo "<ul>";
echo "<li>✅ Application IntraSphere complète</li>";
echo "<li>✅ Scripts d'installation corrigés</li>";
echo "<li>✅ Outils de diagnostic et test</li>";
echo "<li>✅ Documentation complète</li>";
echo "<li>✅ Scripts de maintenance</li>";
echo "</ul>";

echo "<h2>🚀 Prochaines Étapes</h2>";
echo "<ol>";
echo "<li>Téléchargez le package ZIP</li>";
echo "<li>Extrayez-le sur votre serveur web</li>";
echo "<li>Exécutez <strong>install_fixed.php</strong></li>";
echo "<li>Testez avec <strong>test_intrasphere.php</strong></li>";
echo "<li>Accédez à l'application via <strong>intrasphere/index.php</strong></li>";
echo "</ol>";

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

?>

<style>
    body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
    h1 { color: #8B5CF6; text-align: center; }
    h2 { color: #495057; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; }
    .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; }
    .info { background: #cce7ff; border: 1px solid #99d6ff; color: #004085; padding: 15px; border-radius: 5px; }
    a { color: #8B5CF6; }
    ol, ul { margin-left: 20px; }
</style>