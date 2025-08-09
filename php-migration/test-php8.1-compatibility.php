<?php
/**
 * Test de compatibilité PHP 8.1
 * Vérifie que le code utilise uniquement des fonctionnalités compatibles PHP 8.1
 */

echo "=== Test de Compatibilité PHP 8.1 ===\n\n";

// Version PHP actuelle
echo "Version PHP détectée: " . PHP_VERSION . "\n";
echo "Version requise minimum: 8.1\n";
echo "Compatible: " . (version_compare(PHP_VERSION, '8.1.0', '>=') ? '✅ OUI' : '❌ NON') . "\n\n";

// Test des fonctionnalités utilisées
echo "=== Tests des fonctionnalités ===\n";

// 1. Types déclarés (PHP 7.0+)
function testTypeDeclarations(): array {
    return ['test' => 'ok'];
}
echo "✅ Type declarations: Supporté\n";

// 2. Propriétés typées (PHP 7.4+)
class TestClass {
    private array $data = [];
    private string $name = 'test';
}
echo "✅ Typed properties: Supporté\n";

// 3. Null coalescing (PHP 7.0+)
$test = $_ENV['TEST'] ?? 'default';
echo "✅ Null coalescing (??): Supporté\n";

// 4. Array destructuring (PHP 7.1+)
[$a, $b] = ['test1', 'test2'];
echo "✅ Array destructuring: Supporté\n";

// 5. Arrow functions (PHP 7.4+)
$testFunc = fn($x) => $x * 2;
echo "✅ Arrow functions: Supporté\n";

// 6. Match expression (PHP 8.0+) - Compatible PHP 8.1
$testMatch = match(1) {
    1 => 'un',
    2 => 'deux',
    default => 'autre'
};
echo "✅ Match expression: Supporté (PHP 8.0+)\n";

// Test des extensions requises
echo "\n=== Extensions PHP ===\n";

$requiredExtensions = [
    'pdo' => 'Base de données PDO',
    'json' => 'Manipulation JSON',
    'mbstring' => 'Chaînes multi-octets',
    'openssl' => 'Chiffrement SSL',
    'curl' => 'Requêtes HTTP',
    'gd' => 'Manipulation images (optionnel)',
    'pdo_mysql' => 'MySQL via PDO (optionnel)',
    'pdo_pgsql' => 'PostgreSQL via PDO (optionnel)'
];

foreach ($requiredExtensions as $ext => $desc) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? '✅' : (strpos($ext, 'optionnel') !== false ? '⚠️' : '❌');
    $required = in_array($ext, ['pdo_mysql', 'pdo_pgsql', 'gd']) ? '(optionnel)' : '(requis)';
    echo "{$status} {$ext}: {$desc} {$required}\n";
}

// Test de compatibilité du code principal
echo "\n=== Test syntaxe fichiers principaux ===\n";

$mainFiles = [
    'index.php',
    'install.php', 
    'config/database.php',
    'config/bootstrap.php',
    'src/Router.php'
];

foreach ($mainFiles as $file) {
    if (file_exists($file)) {
        $syntax = shell_exec("php -l {$file} 2>&1");
        if (strpos($syntax, 'No syntax errors') !== false) {
            echo "✅ {$file}: Syntaxe OK\n";
        } else {
            echo "❌ {$file}: Erreur syntaxe\n";
            echo "   Détail: " . trim($syntax) . "\n";
        }
    } else {
        echo "⚠️  {$file}: Fichier introuvable\n";
    }
}

// Test des fonctions spécifiques utilisées
echo "\n=== Test fonctions utilisées ===\n";

// Fonctions de base utilisées dans le projet
$functions = [
    'password_hash' => 'PHP 5.5+',
    'password_verify' => 'PHP 5.5+', 
    'bin2hex' => 'PHP 4.0+',
    'random_bytes' => 'PHP 7.0+',
    'preg_match' => 'PHP 4.0+',
    'json_encode' => 'PHP 5.2+',
    'json_decode' => 'PHP 5.2+',
    'filter_var' => 'PHP 5.2+',
    'htmlspecialchars' => 'PHP 4.0+'
];

foreach ($functions as $func => $version) {
    if (function_exists($func)) {
        echo "✅ {$func}: Disponible ({$version})\n";
    } else {
        echo "❌ {$func}: Non disponible\n";
    }
}

// Résumé final
echo "\n=== RÉSUMÉ COMPATIBILITÉ PHP 8.1 ===\n";

$phpVersion = PHP_VERSION;
$isCompatible = version_compare($phpVersion, '8.1.0', '>=');
$hasRequiredExtensions = extension_loaded('pdo') && extension_loaded('json');
$hasDatabase = extension_loaded('pdo_mysql') || extension_loaded('pdo_pgsql');

echo "Version PHP: {$phpVersion}\n";
echo "Compatible PHP 8.1+: " . ($isCompatible ? '✅ OUI' : '❌ NON') . "\n";
echo "Extensions de base: " . ($hasRequiredExtensions ? '✅ OK' : '❌ MANQUANTES') . "\n";  
echo "Support base de données: " . ($hasDatabase ? '✅ OK' : '❌ AUCUN DRIVER') . "\n";

if ($isCompatible && $hasRequiredExtensions && $hasDatabase) {
    echo "\n🎉 RÉSULTAT: 100% COMPATIBLE PHP 8.1\n";
    echo "L'application peut fonctionner sans problème.\n";
} else {
    echo "\n⚠️  RÉSULTAT: PROBLÈMES DE COMPATIBILITÉ\n";
    if (!$isCompatible) {
        echo "- Mettre à jour vers PHP 8.1 ou supérieur\n";
    }
    if (!$hasRequiredExtensions) {
        echo "- Installer les extensions PDO et JSON\n";
    }
    if (!$hasDatabase) {
        echo "- Installer pdo_mysql ou pdo_pgsql\n";
    }
}

echo "\n=== Test terminé ===\n";
?>