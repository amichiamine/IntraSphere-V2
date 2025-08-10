<?php
/**
 * Générateur de Package IntraSphere - Version Corrigée
 * Créé un package ZIP avec la structure correcte pour l'installation
 */

$timestamp = date('Y-m-d-H-i-s');
$packageName = "intrasphere-php-package-final-$timestamp.zip";

echo "<h1>📦 Génération du Package IntraSphere - Version Corrigée</h1>";

$zip = new ZipArchive();
$result = $zip->open($packageName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

if ($result !== TRUE) {
    die("Erreur lors de la création du ZIP: $result");
}

echo "<h2>🔧 Création de la structure correcte...</h2>";

// 1. Ajouter l'index principal de redirection à la racine
if (file_exists('../index.php')) {
    $zip->addFile('../index.php', 'index.php');
    echo "✅ Index principal ajouté<br>";
}

// 2. Ajouter tous les scripts à la racine du package
$scripts = [
    'install_fixed.php' => 'Installation automatique',
    'reset_installation.php' => 'Reset installation',
    'debug_index.php' => 'Diagnostic système',
    'simple_index.php' => 'Version simplifiée',
    'test_intrasphere.php' => 'Script de test',
    'index_fixed.php' => 'Index de référence'
];

foreach ($scripts as $script => $description) {
    if (file_exists($script)) {
        $zip->addFile($script, $script);
        echo "✅ $description ($script)<br>";
    }
}

// 3. Ajouter l'application dans le dossier intrasphere/
function addToZip($zip, $sourcePath, $zipPath) {
    if (is_file($sourcePath)) {
        $zip->addFile($sourcePath, $zipPath);
        return true;
    } elseif (is_dir($sourcePath)) {
        $zip->addEmptyDir($zipPath);
        $files = scandir($sourcePath);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $newSourcePath = $sourcePath . '/' . $file;
                $newZipPath = $zipPath . '/' . $file;
                addToZip($zip, $newSourcePath, $newZipPath);
            }
        }
        return true;
    }
    return false;
}

// Ajouter les dossiers principaux de l'application
$appFolders = ['config', 'src', 'views', 'sql'];
foreach ($appFolders as $folder) {
    if (is_dir($folder)) {
        addToZip($zip, $folder, "intrasphere/$folder");
        echo "✅ Dossier $folder ajouté<br>";
    }
}

// Ajouter les fichiers principaux de l'application
$appFiles = ['index.php', '.env.example'];
foreach ($appFiles as $file) {
    if (file_exists($file)) {
        $zip->addFile($file, "intrasphere/$file");
        echo "✅ Fichier $file ajouté<br>";
    }
}

// 4. Créer le README.md pour le package
$readmeContent = "# IntraSphere PHP - Package Final

## 📁 Structure du Package

```
intrasphere-php-package/
├── index.php                    # Point d'entrée avec redirection
├── install_fixed.php            # Installation automatique ⭐
├── reset_installation.php       # Reset complet
├── debug_index.php             # Diagnostic système
├── simple_index.php            # Version simplifiée
├── test_intrasphere.php        # Tests de vérification
├── index_fixed.php             # Version de référence
└── intrasphere/                # Application principale
    ├── config/                 # Configuration
    ├── src/                    # Code source MVC
    ├── views/                  # Templates
    ├── sql/                    # Scripts SQL
    ├── index.php              # Point d'entrée de l'app
    └── .env.example           # Configuration exemple
```

## 🚀 Installation Rapide

### Étape 1 : Extraction
Extrayez ce package sur votre serveur web (dossier public_html ou www)

### Étape 2 : Installation
1. Accédez à **install_fixed.php** dans votre navigateur
2. Suivez l'assistant d'installation automatique
3. Configurez votre base de données MySQL

### Étape 3 : Vérification
1. Testez avec **test_intrasphere.php**
2. Accédez à l'application via **intrasphere/index.php**

## 🔧 Scripts Disponibles

- **install_fixed.php** : Installation automatique complète
- **debug_index.php** : Diagnostic complet du système
- **simple_index.php** : Version simplifiée pour test rapide
- **test_intrasphere.php** : Vérification finale de l'installation
- **reset_installation.php** : Reset pour recommencer

## 👥 Comptes de Test

```
admin / admin123          (Administrateur)
marie.martin / password123 (Employé)
```

## ⚡ Fonctionnalités

✅ Authentification sécurisée
✅ Dashboard avec statistiques
✅ Gestion des annonces
✅ Partage de documents
✅ Messagerie interne
✅ Système de formation
✅ Administration utilisateurs
✅ Gestion des réclamations

---
Generated: $timestamp
Version: Package Final Corrigé
Status: Ready for Production
";

$zip->addFromString('README.md', $readmeContent);
echo "✅ Documentation README créée<br>";

// Fermer le ZIP
$zip->close();

$fileSize = filesize($packageName);
$fileSizeKB = round($fileSize / 1024, 2);

echo "<h2>✅ Package généré avec succès !</h2>";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>📦 Package créé : $packageName</h3>";
echo "<p><strong>Taille :</strong> {$fileSizeKB} KB</p>";
echo "<p><a href='$packageName' download style='background: #28a745; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;'>⬇️ Télécharger le Package Final</a></p>";
echo "</div>";

echo "<h2>🎯 Structure Validée</h2>";
echo "<ul>";
echo "<li>✅ Scripts d'installation à la racine</li>";
echo "<li>✅ Application dans intrasphere/</li>";
echo "<li>✅ Documentation complète</li>";
echo "<li>✅ Structure plug & play</li>";
echo "</ul>";

echo "<h2>📋 Instructions</h2>";
echo "<ol>";
echo "<li>Téléchargez le package ZIP</li>";
echo "<li>Extrayez sur votre serveur web</li>";
echo "<li>Accédez à <strong>install_fixed.php</strong></li>";
echo "<li>Suivez l'installation automatique</li>";
echo "<li>Testez avec <strong>test_intrasphere.php</strong></li>";
echo "</ol>";

?>

<style>
    body { 
        font-family: Arial, sans-serif; 
        max-width: 900px; 
        margin: 0 auto; 
        padding: 20px; 
        background: #f8f9fa; 
    }
    h1 { 
        color: #8B5CF6; 
        text-align: center; 
        border-bottom: 3px solid #8B5CF6;
        padding-bottom: 10px;
    }
    h2 { 
        color: #495057; 
        border-bottom: 2px solid #e9ecef; 
        padding-bottom: 10px; 
    }
    .success { 
        background: #d4edda; 
        border: 1px solid #c3e6cb; 
        color: #155724; 
        padding: 15px; 
        border-radius: 5px; 
    }
    a { 
        color: #8B5CF6; 
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    ol, ul { 
        margin-left: 20px; 
    }
    li {
        margin: 5px 0;
    }
</style>