/**
 * Validateur universel cross-platform (version TypeScript)
 * Compatible avec la version PHP pour validation commune
 */

export interface ValidationResult {
  isValid: boolean;
  errors: string[];
}

export class UniversalValidator {
  
  /**
   * Validation email universelle
   */
  static validateEmail(email: string): ValidationResult {
    const errors: string[] = [];
    
    if (!email) {
      errors.push("L'adresse email est requise");
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      errors.push("L'adresse email n'est pas valide");
    } else if (email.length > 255) {
      errors.push("L'adresse email est trop longue (maximum 255 caractères)");
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation username universelle (harmonisée avec Zod)
   */
  static validateUsername(username: string): ValidationResult {
    const errors: string[] = [];
    
    if (!username) {
      errors.push("Le nom d'utilisateur est requis");
    } else if (username.length < 3) {
      errors.push("Le nom d'utilisateur doit contenir au moins 3 caractères");
    } else if (username.length > 50) {
      errors.push("Le nom d'utilisateur ne peut pas dépasser 50 caractères");
    } else if (!/^[a-zA-Z0-9_.-]+$/.test(username)) {
      errors.push("Le nom d'utilisateur ne peut contenir que des lettres, chiffres, points, tirets et underscores");
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation nom complet
   */
  static validateFullName(name: string): ValidationResult {
    const errors: string[] = [];
    
    if (!name) {
      errors.push("Le nom complet est requis");
    } else if (name.length < 2) {
      errors.push("Le nom complet doit contenir au moins 2 caractères");
    } else if (name.length > 100) {
      errors.push("Le nom complet ne peut pas dépasser 100 caractères");
    } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(name)) {
      errors.push("Le nom complet contient des caractères non autorisés");
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation rôle utilisateur
   */
  static validateRole(role: string): ValidationResult {
    const allowedRoles = ['employee', 'moderator', 'admin'];
    const errors: string[] = [];
    
    if (!role) {
      errors.push("Le rôle est requis");
    } else if (!allowedRoles.includes(role)) {
      errors.push(`Rôle invalide. Rôles autorisés : ${allowedRoles.join(', ')}`);
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation URL
   */
  static validateUrl(url: string): ValidationResult {
    const errors: string[] = [];
    
    if (url && !/^https?:\/\/.+/.test(url)) {
      errors.push("L'URL n'est pas valide");
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation téléphone (format français)
   */
  static validatePhone(phone: string): ValidationResult {
    const errors: string[] = [];
    
    if (phone) {
      // Supprimer les espaces et tirets
      const cleanPhone = phone.replace(/[\s\-\.]/g, '');
      
      if (!/^(?:\+33|0)[1-9](?:[0-9]{8})$/.test(cleanPhone)) {
        errors.push("Le numéro de téléphone n'est pas valide (format français attendu)");
      }
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation titre/nom de contenu
   */
  static validateTitle(title: string, minLength: number = 3, maxLength: number = 200): ValidationResult {
    const errors: string[] = [];
    
    if (!title) {
      errors.push("Le titre est requis");
    } else if (title.length < minLength) {
      errors.push(`Le titre doit contenir au moins ${minLength} caractères`);
    } else if (title.length > maxLength) {
      errors.push(`Le titre ne peut pas dépasser ${maxLength} caractères`);
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation description/contenu
   */
  static validateDescription(description: string, maxLength: number = 2000): ValidationResult {
    const errors: string[] = [];
    
    if (description.length > maxLength) {
      errors.push(`La description ne peut pas dépasser ${maxLength} caractères`);
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation de fichier upload
   */
  static validateFileUpload(file: File): ValidationResult {
    const errors: string[] = [];
    
    if (!file) {
      errors.push("Aucun fichier sélectionné");
      return { isValid: false, errors };
    }
    
    if (file.size > (5 * 1024 * 1024)) { // 5MB max
      errors.push("Le fichier ne peut pas dépasser 5MB");
    }
    
    if (!file.name) {
      errors.push("Le nom du fichier est requis");
    }
    
    return {
      isValid: errors.length === 0,
      errors
    };
  }
  
  /**
   * Validation complète d'un utilisateur
   */
  static validateUser(userData: {
    username?: string;
    name?: string;
    email?: string;
    role?: string;
    password?: string;
  }): ValidationResult {
    const allErrors: string[] = [];
    
    // Validation du nom d'utilisateur
    const usernameValidation = this.validateUsername(userData.username || '');
    if (!usernameValidation.isValid) {
      allErrors.push(...usernameValidation.errors);
    }
    
    // Validation du nom complet
    const nameValidation = this.validateFullName(userData.name || '');
    if (!nameValidation.isValid) {
      allErrors.push(...nameValidation.errors);
    }
    
    // Validation de l'email (si fourni)
    if (userData.email) {
      const emailValidation = this.validateEmail(userData.email);
      if (!emailValidation.isValid) {
        allErrors.push(...emailValidation.errors);
      }
    }
    
    // Validation du rôle
    const roleValidation = this.validateRole(userData.role || '');
    if (!roleValidation.isValid) {
      allErrors.push(...roleValidation.errors);
    }
    
    // Validation du mot de passe (si fourni)
    if (userData.password) {
      // Import dynamique pour éviter les dépendances circulaires
      const { AuthService } = require('../../server/services/auth');
      const passwordValidation = AuthService.validatePasswordStrength(userData.password);
      if (!passwordValidation.isValid) {
        allErrors.push(...passwordValidation.errors);
      }
    }
    
    return {
      isValid: allErrors.length === 0,
      errors: allErrors
    };
  }
}