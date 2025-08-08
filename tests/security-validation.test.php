<?php
/**
 * Tests de validation de sécurité pour l'harmonisation
 * Vérification de la cohérence entre versions PHP et TypeScript
 */

require_once __DIR__ . '/../php-migration/src/utils/PasswordValidator.php';
require_once __DIR__ . '/../php-migration/src/utils/RateLimiter.php';
require_once __DIR__ . '/../php-migration/src/utils/PermissionManager.php';
require_once __DIR__ . '/../shared/validators/UniversalValidator.php';

class SecurityValidationTest {
    
    private static array $results = [];
    
    /**
     * Test de validation des mots de passe
     */
    public static function testPasswordValidation(): bool {
        $testCases = [
            // [password, expected_valid]
            ['123456', false], // Trop court
            ['password', false], // Pas de majuscule/chiffre/spécial
            ['Password123', false], // Pas de caractère spécial
            ['Password123!', true], // Valide
            ['P@ssw0rd', true], // Valide
            ['', false], // Vide
            ['Aa1!', false], // Trop court (< 8)
        ];
        
        $passed = 0;
        $total = count($testCases);
        
        foreach ($testCases as [$password, $expected]) {
            $result = PasswordValidator::validatePasswordStrength($password);
            if ($result['isValid'] === $expected) {
                $passed++;
            } else {
                echo "ÉCHEC: Password '$password' - Attendu: " . ($expected ? 'valide' : 'invalide') . 
                     ", Obtenu: " . ($result['isValid'] ? 'valide' : 'invalide') . "\n";
            }
        }
        
        $success = $passed === $total;
        self::$results['password_validation'] = [
            'passed' => $passed,
            'total' => $total,
            'success' => $success
        ];
        
        return $success;
    }
    
    /**
     * Test de rate limiting
     */
    public static function testRateLimiting(): bool {
        $key = 'test_user_' . time();
        $maxAttempts = 3;
        $window = 60;
        
        $results = [];
        
        // Première tentative - doit passer
        $results[] = RateLimiter::checkRateLimit($key, $maxAttempts, $window);
        
        // Deuxième tentative - doit passer
        $results[] = RateLimiter::checkRateLimit($key, $maxAttempts, $window);
        
        // Troisième tentative - doit passer
        $results[] = RateLimiter::checkRateLimit($key, $maxAttempts, $window);
        
        // Quatrième tentative - doit échouer
        $results[] = RateLimiter::checkRateLimit($key, $maxAttempts, $window);
        
        $expected = [true, true, true, false];
        $success = $results === $expected;
        
        if (!$success) {
            echo "ÉCHEC Rate Limiting: Attendu " . json_encode($expected) . 
                 ", Obtenu " . json_encode($results) . "\n";
        }
        
        // Test des tentatives restantes
        $remaining = RateLimiter::getRemainingAttempts($key, $maxAttempts, $window);
        $expectedRemaining = 0;
        
        if ($remaining !== $expectedRemaining) {
            echo "ÉCHEC Tentatives restantes: Attendu {$expectedRemaining}, Obtenu {$remaining}\n";
            $success = false;
        }
        
        // Nettoyer
        RateLimiter::resetAttempts($key);
        
        self::$results['rate_limiting'] = [
            'basic_test' => $results === $expected,
            'remaining_test' => $remaining === $expectedRemaining,
            'success' => $success
        ];
        
        return $success;
    }
    
    /**
     * Test de validation universelle
     */
    public static function testUniversalValidation(): bool {
        $testCases = [
            // Email
            ['validateEmail', 'test@example.com', true],
            ['validateEmail', 'invalid-email', false],
            ['validateEmail', '', false],
            
            // Username
            ['validateUsername', 'validuser123', true],
            ['validateUsername', 'ab', false], // Trop court
            ['validateUsername', 'user@invalid', false], // Caractères interdits
            
            // Nom complet
            ['validateFullName', 'Jean Dupont', true],
            ['validateFullName', 'A', false], // Trop court
            ['validateFullName', 'Jean123', false], // Chiffres interdits
            
            // Rôle
            ['validateRole', 'admin', true],
            ['validateRole', 'employee', true],
            ['validateRole', 'invalid_role', false],
        ];
        
        $passed = 0;
        $total = count($testCases);
        
        foreach ($testCases as [$method, $value, $expected]) {
            $result = UniversalValidator::$method($value);
            if ($result['isValid'] === $expected) {
                $passed++;
            } else {
                echo "ÉCHEC Universal Validation {$method}('$value'): Attendu " . 
                     ($expected ? 'valide' : 'invalide') . ", Obtenu " . 
                     ($result['isValid'] ? 'valide' : 'invalide') . "\n";
            }
        }
        
        $success = $passed === $total;
        self::$results['universal_validation'] = [
            'passed' => $passed,
            'total' => $total,
            'success' => $success
        ];
        
        return $success;
    }
    
    /**
     * Test de compatibilité des configurations
     */
    public static function testConfigCompatibility(): bool {
        // Vérifier que les configurations Rate Limiting sont cohérentes
        $phpConfigs = RateLimiter::getConfig('login');
        $expectedConfigs = [
            'login' => ['maxAttempts' => 5, 'windowSeconds' => 300],
            'forgot_password' => ['maxAttempts' => 3, 'windowSeconds' => 3600],
            'register' => ['maxAttempts' => 3, 'windowSeconds' => 900],
        ];
        
        $success = true;
        
        foreach ($expectedConfigs as $endpoint => $expected) {
            $actual = RateLimiter::getConfig($endpoint);
            if ($actual !== $expected) {
                echo "ÉCHEC Config {$endpoint}: Attendu " . json_encode($expected) . 
                     ", Obtenu " . json_encode($actual) . "\n";
                $success = false;
            }
        }
        
        // Vérifier les permissions système
        $permissions = PermissionManager::getAllPermissions();
        $requiredPermissions = [
            'manage_users',
            'manage_announcements',
            'manage_documents',
            'manage_events',
            'manage_trainings'
        ];
        
        foreach ($requiredPermissions as $permission) {
            if (!isset($permissions[$permission])) {
                echo "ÉCHEC Permission manquante: {$permission}\n";
                $success = false;
            }
        }
        
        self::$results['config_compatibility'] = [
            'rate_limit_configs' => true,
            'system_permissions' => count($requiredPermissions) === count(array_intersect($requiredPermissions, array_keys($permissions))),
            'success' => $success
        ];
        
        return $success;
    }
    
    /**
     * Exécuter tous les tests
     */
    public static function runAllTests(): array {
        echo "=== Tests de Validation de Sécurité ===\n\n";
        
        $tests = [
            'Password Validation' => 'testPasswordValidation',
            'Rate Limiting' => 'testRateLimiting',
            'Universal Validation' => 'testUniversalValidation',
            'Config Compatibility' => 'testConfigCompatibility',
        ];
        
        $totalPassed = 0;
        $totalTests = count($tests);
        
        foreach ($tests as $name => $method) {
            echo "Test: {$name}... ";
            $result = self::$method();
            echo $result ? "✅ PASSÉ\n" : "❌ ÉCHEC\n";
            if ($result) $totalPassed++;
        }
        
        echo "\n=== Résultats ===\n";
        echo "Tests passés: {$totalPassed}/{$totalTests}\n";
        echo "Taux de réussite: " . round(($totalPassed / $totalTests) * 100, 1) . "%\n\n";
        
        if ($totalPassed === $totalTests) {
            echo "🎉 TOUS LES TESTS SONT PASSÉS - HARMONISATION VALIDÉE\n";
        } else {
            echo "⚠️  Certains tests ont échoué - Vérification nécessaire\n";
        }
        
        return [
            'total_tests' => $totalTests,
            'passed_tests' => $totalPassed,
            'success_rate' => ($totalPassed / $totalTests) * 100,
            'all_passed' => $totalPassed === $totalTests,
            'detailed_results' => self::$results
        ];
    }
}

// Exécuter les tests si le script est appelé directement
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    SecurityValidationTest::runAllTests();
}
?>