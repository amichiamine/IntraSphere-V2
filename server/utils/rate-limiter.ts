/**
 * Rate Limiter TypeScript - Harmonisé avec la version PHP
 * Compatible avec express-rate-limit et le système PHP
 */

interface RateLimitConfig {
  maxAttempts: number;
  windowSeconds: number;
}

interface RateLimitData {
  attempts: number[];
  lastReset: number;
}

export class RateLimiter {
  private static storage = new Map<string, RateLimitData>();

  /**
   * Vérifier et enregistrer une tentative
   */
  static checkRateLimit(
    key: string, 
    maxAttempts: number = 5, 
    windowSeconds: number = 300
  ): boolean {
    const now = Date.now();
    const windowStart = now - (windowSeconds * 1000);

    // Récupérer ou initialiser les données
    let data = this.storage.get(key);
    if (!data) {
      data = { attempts: [], lastReset: now };
      this.storage.set(key, data);
    }

    // Nettoyer les anciennes tentatives
    data.attempts = data.attempts.filter(timestamp => timestamp > windowStart);

    // Vérifier la limite
    if (data.attempts.length >= maxAttempts) {
      return false;
    }

    // Enregistrer la tentative
    data.attempts.push(now);
    
    return true;
  }

  /**
   * Obtenir le nombre de tentatives restantes
   */
  static getRemainingAttempts(
    key: string, 
    maxAttempts: number = 5, 
    windowSeconds: number = 300
  ): number {
    const now = Date.now();
    const windowStart = now - (windowSeconds * 1000);

    const data = this.storage.get(key);
    if (!data) {
      return maxAttempts;
    }

    const recentAttempts = data.attempts.filter(timestamp => timestamp > windowStart);
    return Math.max(0, maxAttempts - recentAttempts.length);
  }

  /**
   * Obtenir le temps d'attente avant la prochaine tentative
   */
  static getRetryAfter(key: string, windowSeconds: number = 300): number {
    const data = this.storage.get(key);
    if (!data || data.attempts.length === 0) {
      return 0;
    }

    const oldestAttempt = Math.min(...data.attempts);
    const nextAllowedTime = oldestAttempt + (windowSeconds * 1000);
    
    return Math.max(0, Math.ceil((nextAllowedTime - Date.now()) / 1000));
  }

  /**
   * Réinitialiser les tentatives pour une clé
   */
  static resetAttempts(key: string): void {
    this.storage.delete(key);
  }

  /**
   * Configurations prédéfinies (harmonisées avec PHP)
   */
  static getConfig(endpoint: string): RateLimitConfig {
    const configs: Record<string, RateLimitConfig> = {
      'login': { maxAttempts: 5, windowSeconds: 300 }, // 5 tentatives en 5 minutes
      'forgot_password': { maxAttempts: 3, windowSeconds: 3600 }, // 3 tentatives en 1 heure
      'register': { maxAttempts: 3, windowSeconds: 900 }, // 3 tentatives en 15 minutes
      'api_general': { maxAttempts: 100, windowSeconds: 3600 }, // 100 requêtes par heure
      'upload': { maxAttempts: 10, windowSeconds: 600 }, // 10 uploads en 10 minutes
    };

    return configs[endpoint] ?? { maxAttempts: 50, windowSeconds: 3600 };
  }

  /**
   * Middleware Express pour rate limiting
   */
  static middleware(endpoint: string, identifier?: string) {
    return (req: any, res: any, next: any) => {
      const config = this.getConfig(endpoint);
      const key = `${endpoint}_${identifier ?? req.ip ?? 'unknown'}`;

      if (!this.checkRateLimit(key, config.maxAttempts, config.windowSeconds)) {
        const retryAfter = this.getRetryAfter(key, config.windowSeconds);
        return res.status(429).json({
          message: `Trop de tentatives. Réessayez dans ${retryAfter} secondes.`,
          retryAfter
        });
      }

      next();
    };
  }

  /**
   * Nettoyer les entrées expirées
   */
  static cleanup(): number {
    let cleaned = 0;
    const now = Date.now();

    // Utiliser Array.from pour compatibilité TypeScript
    for (const [key, data] of Array.from(this.storage.entries())) {
      // Supprimer les données anciennes (plus de 24h)
      if (now - data.lastReset > 24 * 60 * 60 * 1000) {
        this.storage.delete(key);
        cleaned++;
      }
    }

    return cleaned;
  }

  /**
   * Obtenir les statistiques du rate limiter
   */
  static getStats(): {
    totalKeys: number;
    memoryUsage: number;
  } {
    this.cleanup();

    return {
      totalKeys: this.storage.size,
      memoryUsage: process.memoryUsage().heapUsed
    };
  }
}

// Nettoyer automatiquement toutes les heures
setInterval(() => {
  RateLimiter.cleanup();
}, 60 * 60 * 1000);