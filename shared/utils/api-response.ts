/**
 * Gestionnaire de réponses API unifié (version TypeScript)
 * Compatible avec le format PHP/Express
 */

import type { Response } from 'express';

export interface ApiSuccessResponse<T = any> {
  message?: string;
  data?: T;
}

export interface ApiErrorResponse {
  message: string;
  errors?: string[];
  debug?: any;
}

export interface PaginatedResponse<T = any> {
  data: T[];
  pagination: {
    current_page: number;
    per_page: number;
    total: number;
    total_pages: number;
    has_next: boolean;
    has_prev: boolean;
  };
}

export class ApiResponse {
  
  /**
   * Réponse de succès
   */
  static success<T>(res: Response, data?: T, message?: string, statusCode: number = 200): void {
    const response: ApiSuccessResponse<T> = {};
    
    if (message) {
      response.message = message;
    }
    
    if (data !== undefined) {
      if (data && typeof data === 'object' && 'data' in data && 'pagination' in data) {
        // Réponse paginée
        res.status(statusCode).json(data);
        return;
      } else {
        response.data = data;
      }
    }
    
    res.status(statusCode).json(response);
  }
  
  /**
   * Réponse d'erreur
   */
  static error(res: Response, message: string, statusCode: number = 400, errors?: string[]): void {
    const response: ApiErrorResponse = { message };
    
    if (errors && errors.length > 0) {
      response.errors = errors;
    }
    
    // Ajouter des détails supplémentaires en mode debug
    if (process.env.NODE_ENV === 'development' && statusCode >= 500) {
      const stack = new Error().stack;
      response.debug = {
        stack: stack?.split('\n').slice(1, 6)
      };
    }
    
    res.status(statusCode).json(response);
  }
  
  /**
   * Réponse de validation
   */
  static validationError(res: Response, errors: string[], message: string = "Données invalides"): void {
    this.error(res, message, 422, errors);
  }
  
  /**
   * Réponse non autorisé
   */
  static unauthorized(res: Response, message: string = "Non autorisé"): void {
    this.error(res, message, 401);
  }
  
  /**
   * Réponse interdit
   */
  static forbidden(res: Response, message: string = "Accès refusé"): void {
    this.error(res, message, 403);
  }
  
  /**
   * Réponse non trouvé
   */
  static notFound(res: Response, message: string = "Ressource non trouvée"): void {
    this.error(res, message, 404);
  }
  
  /**
   * Réponse rate limit
   */
  static rateLimited(res: Response, message: string = "Trop de requêtes", retryAfter: number = 60): void {
    res.set('Retry-After', retryAfter.toString());
    this.error(res, message, 429);
  }
  
  /**
   * Réponse paginée
   */
  static paginated<T>(
    res: Response, 
    data: T[], 
    total: number, 
    page: number, 
    limit: number, 
    message?: string
  ): void {
    const response: PaginatedResponse<T> = {
      data,
      pagination: {
        current_page: page,
        per_page: limit,
        total,
        total_pages: Math.ceil(total / limit),
        has_next: page < Math.ceil(total / limit),
        has_prev: page > 1
      }
    };
    
    this.success(res, response, message);
  }
  
  /**
   * Réponse de création
   */
  static created<T>(res: Response, data: T, message: string = "Créé avec succès"): void {
    this.success(res, data, message, 201);
  }
  
  /**
   * Réponse de mise à jour
   */
  static updated<T>(res: Response, data?: T, message: string = "Mis à jour avec succès"): void {
    this.success(res, data, message);
  }
  
  /**
   * Réponse de suppression
   */
  static deleted(res: Response, message: string = "Supprimé avec succès"): void {
    this.success(res, undefined, message);
  }
  
  /**
   * Réponse sans contenu
   */
  static noContent(res: Response): void {
    res.status(204).end();
  }
  
  /**
   * Réponse de redirection
   */
  static redirect(res: Response, url: string, statusCode: number = 302): void {
    res.redirect(statusCode, url);
  }
  
  /**
   * Formater une erreur d'exception
   */
  static fromException(res: Response, error: Error, statusCode: number = 500): void {
    const message = process.env.NODE_ENV === 'development' ? error.message : "Erreur interne du serveur";
    
    const errors: string[] = [];
    if (process.env.NODE_ENV === 'development') {
      errors.push(error.stack || error.message);
    }
    
    this.error(res, message, statusCode, errors);
  }
  
  /**
   * Formater les erreurs de validation
   */
  static formatValidationErrors(validationResult: { errors?: string[] }): string[] {
    return validationResult.errors || [];
  }
  
  /**
   * Middleware pour les en-têtes de sécurité
   */
  static setSecurityHeaders(res: Response): void {
    res.set({
      'X-Content-Type-Options': 'nosniff',
      'X-Frame-Options': 'DENY',
      'X-XSS-Protection': '1; mode=block',
      'Content-Security-Policy': "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'"
    });
    
    // HTTPS uniquement en production
    if (process.env.NODE_ENV === 'production') {
      res.set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
  }
}