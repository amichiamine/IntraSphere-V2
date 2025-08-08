/**
 * Gestionnaire d'erreurs unifié TypeScript
 * Compatible avec la version PHP
 */

import type { Request, Response, NextFunction } from 'express';
import { ApiResponse } from '../utils/api-response';

export interface ErrorContext {
  [key: string]: any;
}

export class ErrorHandler {
  
  /**
   * Middleware de gestion d'erreurs Express
   */
  static middleware() {
    return (error: Error, req: Request, res: Response, next: NextFunction) => {
      console.error('Express Error:', error);
      
      // Logger l'erreur avec contexte
      this.logError(error.message, {
        url: req.url,
        method: req.method,
        ip: req.ip,
        userAgent: req.get('User-Agent'),
        stack: error.stack
      });
      
      ApiResponse.fromException(res, error);
    };
  }
  
  /**
   * Gestionnaire d'erreurs asynchrones
   */
  static asyncHandler(fn: Function) {
    return (req: Request, res: Response, next: NextFunction) => {
      Promise.resolve(fn(req, res, next)).catch(next);
    };
  }
  
  /**
   * Gestionnaire d'erreurs de validation
   */
  static handleValidationErrors(
    validationResult: { isValid: boolean; errors?: string[] }, 
    defaultMessage: string = "Données invalides"
  ): void {
    if (!validationResult.isValid) {
      const errors = validationResult.errors || [];
      throw new ValidationError(defaultMessage, errors);
    }
  }
  
  /**
   * Gestionnaire d'erreurs de base de données
   */
  static handleDatabaseError(error: Error, operation: string = 'database operation'): never {
    this.logError(`Database error during ${operation}: ${error.message}`, {
      operation,
      stack: error.stack
    });
    
    const message = process.env.NODE_ENV === 'development' 
      ? `Erreur base de données: ${error.message}` 
      : "Erreur de base de données";
      
    throw new DatabaseError(message, operation);
  }
  
  /**
   * Logger une erreur avec contexte
   */
  static logError(message: string, context: ErrorContext = {}, level: string = 'error'): void {
    const logEntry = {
      timestamp: new Date().toISOString(),
      level: level.toUpperCase(),
      message,
      context
    };
    
    // En développement, afficher dans la console
    if (process.env.NODE_ENV === 'development') {
      console.error(JSON.stringify(logEntry, null, 2));
    } else {
      // En production, utiliser un système de logging approprié
      console.error(JSON.stringify(logEntry));
    }
  }
  
  /**
   * Créer une erreur avec contexte
   */
  static createError(message: string, code: number = 500, context: ErrorContext = {}): Error {
    const error = new Error(message);
    (error as any).statusCode = code;
    (error as any).context = context;
    return error;
  }
  
  /**
   * Wrapper pour les opérations async avec gestion d'erreur
   */
  static async safeExecute<T>(
    operation: () => Promise<T>, 
    errorMessage: string = "Opération échouée"
  ): Promise<T> {
    try {
      return await operation();
    } catch (error) {
      this.logError(`${errorMessage}: ${(error as Error).message}`, {
        stack: (error as Error).stack
      });
      throw error;
    }
  }
  
  /**
   * Gestionnaire pour les erreurs 404
   */
  static notFoundHandler() {
    return (req: Request, res: Response) => {
      ApiResponse.notFound(res, `Route non trouvée: ${req.method} ${req.path}`);
    };
  }
  
  /**
   * Initialiser les gestionnaires d'erreurs globaux
   */
  static init(): void {
    // Gestionnaire pour les rejets de promesses non gérés
    process.on('unhandledRejection', (reason, promise) => {
      this.logError('Unhandled Promise Rejection', {
        reason: String(reason),
        promise: String(promise)
      }, 'critical');
      
      // En production, on pourrait vouloir arrêter le processus
      if (process.env.NODE_ENV === 'production') {
        process.exit(1);
      }
    });
    
    // Gestionnaire pour les exceptions non gérées
    process.on('uncaughtException', (error) => {
      this.logError('Uncaught Exception', {
        message: error.message,
        stack: error.stack
      }, 'critical');
      
      // Arrêter le processus de manière propre
      process.exit(1);
    });
  }
}

/**
 * Classes d'erreurs personnalisées
 */
export class ValidationError extends Error {
  public readonly statusCode = 422;
  public readonly errors: string[];
  
  constructor(message: string, errors: string[] = []) {
    super(message);
    this.name = 'ValidationError';
    this.errors = errors;
  }
}

export class DatabaseError extends Error {
  public readonly statusCode = 500;
  public readonly operation: string;
  
  constructor(message: string, operation: string) {
    super(message);
    this.name = 'DatabaseError';
    this.operation = operation;
  }
}

export class AuthenticationError extends Error {
  public readonly statusCode = 401;
  
  constructor(message: string = "Non authentifié") {
    super(message);
    this.name = 'AuthenticationError';
  }
}

export class AuthorizationError extends Error {
  public readonly statusCode = 403;
  
  constructor(message: string = "Accès refusé") {
    super(message);
    this.name = 'AuthorizationError';
  }
}

export class NotFoundError extends Error {
  public readonly statusCode = 404;
  
  constructor(message: string = "Ressource non trouvée") {
    super(message);
    this.name = 'NotFoundError';
  }
}

export class RateLimitError extends Error {
  public readonly statusCode = 429;
  public readonly retryAfter: number;
  
  constructor(message: string = "Trop de requêtes", retryAfter: number = 60) {
    super(message);
    this.name = 'RateLimitError';
    this.retryAfter = retryAfter;
  }
}

// Initialiser les gestionnaires d'erreurs
ErrorHandler.init();