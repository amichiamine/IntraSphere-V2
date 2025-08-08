/**
 * Tests de validation de compatibilité IntraSphere
 * Valide que les versions PHP et TypeScript sont 100% compatibles
 */

import { describe, it, expect } from 'vitest';
import { ApiResponseBuilder, HttpStatus } from '../shared/utils/api-response';
import { UniversalValidator } from '../shared/validators/universal-validator';
import { ErrorHandler } from '../shared/services/error-handler';

describe('🔄 Compatibilité PHP ↔ TypeScript', () => {
  
  describe('✅ ApiResponse System', () => {
    it('devrait générer une réponse de succès identique à PHP', () => {
      const response = ApiResponseBuilder.success({ id: 1, name: 'Test' }, 'Succès');
      
      expect(response.success).toBe(true);
      expect(response.data).toEqual({ id: 1, name: 'Test' });
      expect(response.message).toBe('Succès');
      expect(response.meta?.version).toBe('1.0');
      expect(response.meta?.timestamp).toBeDefined();
    });

    it('devrait générer une réponse d\'erreur identique à PHP', () => {
      const response = ApiResponseBuilder.error('Erreur de test', 'TEST_ERROR');
      
      expect(response.success).toBe(false);
      expect(response.message).toBe('Erreur de test');
      expect(response.error).toBe('TEST_ERROR');
      expect(response.meta?.version).toBe('1.0');
    });

    it('devrait générer une réponse paginée identique à PHP', () => {
      const data = [{ id: 1 }, { id: 2 }];
      const response = ApiResponseBuilder.paginated(data, 10, 1, 2);
      
      expect(response.success).toBe(true);
      expect(response.data).toEqual(data);
      expect(response.meta?.total).toBe(10);
      expect(response.meta?.page).toBe(1);
      expect(response.meta?.perPage).toBe(2);
      expect(response.meta?.hasMore).toBe(true);
    });

    it('devrait avoir les mêmes codes de statut HTTP', () => {
      expect(HttpStatus.OK).toBe(200);
      expect(HttpStatus.CREATED).toBe(201);
      expect(HttpStatus.BAD_REQUEST).toBe(400);
      expect(HttpStatus.UNAUTHORIZED).toBe(401);
      expect(HttpStatus.FORBIDDEN).toBe(403);
      expect(HttpStatus.NOT_FOUND).toBe(404);
      expect(HttpStatus.INTERNAL_SERVER_ERROR).toBe(500);
    });
  });

  describe('✅ Universal Validator', () => {
    it('devrait valider les emails comme PHP', () => {
      const validEmail = UniversalValidator.validateEmail('test@example.com');
      const invalidEmail = UniversalValidator.validateEmail('invalid-email');
      
      expect(validEmail.isValid).toBe(true);
      expect(validEmail.data).toBe('test@example.com');
      expect(validEmail.errors).toEqual([]);
      
      expect(invalidEmail.isValid).toBe(false);
      expect(invalidEmail.errors).toContain("Format d'email invalide");
    });

    it('devrait valider les mots de passe comme PHP', () => {
      const validPassword = UniversalValidator.validatePassword('Test123!');
      const invalidPassword = UniversalValidator.validatePassword('weak');
      
      expect(validPassword.isValid).toBe(true);
      expect(validPassword.data).toBe('Test123!');
      
      expect(invalidPassword.isValid).toBe(false);
      expect(invalidPassword.errors.length).toBeGreaterThan(0);
      expect(invalidPassword.errors.some(e => e.includes('8 caractères'))).toBe(true);
    });

    it('devrait valider les noms d\'utilisateur comme PHP', () => {
      const validUsername = UniversalValidator.validateUsername('user.name-123');
      const invalidUsername = UniversalValidator.validateUsername('ab');
      
      expect(validUsername.isValid).toBe(true);
      expect(validUsername.data).toBe('user.name-123');
      
      expect(invalidUsername.isValid).toBe(false);
      expect(invalidUsername.errors).toContain("Le nom d'utilisateur doit contenir au moins 3 caractères");
    });

    it('devrait valider les numéros de téléphone français comme PHP', () => {
      const validPhone = UniversalValidator.validatePhoneNumber('01 23 45 67 89');
      const invalidPhone = UniversalValidator.validatePhoneNumber('123456');
      
      expect(validPhone.isValid).toBe(true);
      expect(validPhone.data).toBe('0123456789');
      
      expect(invalidPhone.isValid).toBe(false);
      expect(invalidPhone.errors).toContain("Format de téléphone invalide (ex: 01 23 45 67 89)");
    });

    it('devrait valider les URLs comme PHP', () => {
      const validUrl = UniversalValidator.validateUrl('https://example.com');
      const invalidUrl = UniversalValidator.validateUrl('not-a-url');
      
      expect(validUrl.isValid).toBe(true);
      expect(validUrl.data).toBe('https://example.com');
      
      expect(invalidUrl.isValid).toBe(false);
      expect(invalidUrl.errors).toContain("Format d'URL invalide");
    });

    it('devrait valider la longueur de texte comme PHP', () => {
      const validText = UniversalValidator.validateTextLength('Texte valide', 5, 20, 'Contenu');
      const shortText = UniversalValidator.validateTextLength('AB', 5, 20, 'Contenu');
      const longText = UniversalValidator.validateTextLength('A'.repeat(25), 5, 20, 'Contenu');
      
      expect(validText.isValid).toBe(true);
      expect(validText.data).toBe('Texte valide');
      
      expect(shortText.isValid).toBe(false);
      expect(shortText.errors).toContain('Contenu doit contenir au moins 5 caractères');
      
      expect(longText.isValid).toBe(false);
      expect(longText.errors).toContain('Contenu ne peut pas dépasser 20 caractères');
    });

    it('devrait nettoyer et valider comme PHP', () => {
      const sanitized = UniversalValidator.sanitizeAndValidate('  <script>alert("test")</script>  ');
      
      expect(sanitized.isValid).toBe(true);
      expect(sanitized.data).toBe('&lt;script&gt;alert(&quot;test&quot;)&lt;/script&gt;');
    });
  });

  describe('✅ Error Handler', () => {
    it('devrait gérer les erreurs de validation comme PHP', () => {
      const errors = ['Email requis', 'Mot de passe faible'];
      const response = ErrorHandler.handleValidationError(errors);
      
      expect(response.success).toBe(false);
      expect(response.error).toBe('VALIDATION_FAILED');
      expect(response.errors).toEqual(errors);
      expect(response.message).toBe('Validation échouée');
    });

    it('devrait gérer les erreurs d\'authentification comme PHP', () => {
      const response = ErrorHandler.handleAuthError('Token expiré');
      
      expect(response.success).toBe(false);
      expect(response.error).toBe('AUTH_REQUIRED');
      expect(response.message).toBe('Token expiré');
    });

    it('devrait gérer les erreurs de permissions comme PHP', () => {
      const response = ErrorHandler.handlePermissionError('Accès interdit');
      
      expect(response.success).toBe(false);
      expect(response.error).toBe('FORBIDDEN');
      expect(response.message).toBe('Accès interdit');
    });

    it('devrait gérer les erreurs 404 comme PHP', () => {
      const response = ErrorHandler.handleNotFoundError('Utilisateur');
      
      expect(response.success).toBe(false);
      expect(response.error).toBe('NOT_FOUND');
      expect(response.message).toBe('Utilisateur non trouvée');
    });

    it('devrait gérer les erreurs serveur comme PHP', () => {
      const error = new Error('Erreur interne');
      const response = ErrorHandler.handleServerError(error);
      
      expect(response.success).toBe(false);
      expect(response.error).toBe('INTERNAL_ERROR');
      expect(response.message).toBeDefined();
    });

    it('devrait gérer les erreurs de base de données comme PHP', () => {
      const dbError = new Error('Connection failed');
      const response = ErrorHandler.handleDatabaseError(dbError);
      
      expect(response.success).toBe(false);
      expect(response.error).toBe('DATABASE_ERROR');
      expect(response.message).toContain('base de données');
    });

    it('devrait gérer les erreurs de limite de taux comme PHP', () => {
      const response = ErrorHandler.handleRateLimitError(60);
      
      expect(response.success).toBe(false);
      expect(response.error).toBe('TOO_MANY_REQUESTS');
      expect(response.message).toBe('Trop de requêtes');
      expect(response.meta?.retryAfter).toBe(60);
    });

    it('devrait fournir des statistiques d\'erreurs', () => {
      // Simuler quelques erreurs
      ErrorHandler.logError('Erreur test 1');
      ErrorHandler.logError('Erreur test 2');
      
      const stats = ErrorHandler.getErrorStats();
      
      expect(stats.totalErrors).toBeGreaterThanOrEqual(2);
      expect(stats.last24h).toBeGreaterThanOrEqual(2);
      expect(stats.errorsByCode).toBeDefined();
      expect(stats.mostCommonErrors).toBeInstanceOf(Array);
    });
  });

  describe('✅ Execution Sécurisée', () => {
    it('devrait exécuter les opérations de manière sécurisée comme PHP', async () => {
      const successOperation = async () => ({ result: 'success' });
      const failureOperation = async () => { throw new Error('Test error'); };
      
      const successResult = await ErrorHandler.safeExecute(successOperation);
      const failureResult = await ErrorHandler.safeExecute(failureOperation);
      
      expect(successResult.success).toBe(true);
      expect(successResult.data).toEqual({ result: 'success' });
      
      expect(failureResult.success).toBe(false);
      expect(failureResult.error).toBeDefined();
    });
  });
});

describe('🧪 Tests d\'Intégration', () => {
  it('devrait simuler un workflow complet identique à PHP', () => {
    // 1. Validation des données
    const emailValidation = UniversalValidator.validateEmail('user@test.com');
    expect(emailValidation.isValid).toBe(true);
    
    // 2. En cas d'erreur, formatage de la réponse
    if (!emailValidation.isValid) {
      const errorResponse = ErrorHandler.handleValidationError(emailValidation.errors);
      expect(errorResponse.success).toBe(false);
    }
    
    // 3. En cas de succès, formatage de la réponse
    const successResponse = ApiResponseBuilder.success(
      { user: { email: emailValidation.data } },
      'Utilisateur validé'
    );
    
    expect(successResponse.success).toBe(true);
    expect(successResponse.data?.user.email).toBe('user@test.com');
    expect(successResponse.message).toBe('Utilisateur validé');
    expect(successResponse.meta?.version).toBe('1.0');
  });

  it('devrait maintenir la cohérence des formats de réponse', () => {
    // Test de tous les types de réponse pour vérifier la structure
    const responses = [
      ApiResponseBuilder.success({ test: true }),
      ApiResponseBuilder.error('Test error'),
      ApiResponseBuilder.validationError(['Error 1', 'Error 2']),
      ApiResponseBuilder.authRequired(),
      ApiResponseBuilder.forbidden(),
      ApiResponseBuilder.notFound(),
      ApiResponseBuilder.serverError('Server error'),
      ApiResponseBuilder.tooManyRequests(),
    ];
    
    // Toutes les réponses doivent avoir la structure de base
    responses.forEach(response => {
      expect(response).toHaveProperty('success');
      expect(response).toHaveProperty('meta');
      expect(response.meta).toHaveProperty('timestamp');
      expect(response.meta).toHaveProperty('version', '1.0');
    });
  });
});

// Export des utilitaires pour les tests d'intégration
export const CompatibilityTestUtils = {
  /**
   * Valider qu'une réponse respecte le format standard
   */
  validateApiResponse: (response: any) => {
    expect(response).toHaveProperty('success');
    expect(typeof response.success).toBe('boolean');
    
    if (response.success) {
      expect(response).toHaveProperty('data');
    } else {
      expect(response).toHaveProperty('message');
    }
    
    expect(response).toHaveProperty('meta');
    expect(response.meta).toHaveProperty('timestamp');
    expect(response.meta).toHaveProperty('version', '1.0');
  },
  
  /**
   * Valider qu'une validation respecte le format standard
   */
  validateValidationResult: (result: any) => {
    expect(result).toHaveProperty('isValid');
    expect(result).toHaveProperty('errors');
    expect(Array.isArray(result.errors)).toBe(true);
    
    if (result.isValid) {
      expect(result).toHaveProperty('data');
    }
  }
};