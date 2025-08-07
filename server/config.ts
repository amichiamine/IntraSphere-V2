/**
 * Environment-based configuration for IntraSphere
 */

export interface AppConfig {
  // Server configuration
  port: number;
  nodeEnv: 'development' | 'production' | 'test';
  
  // Security configuration
  sessionSecret: string;
  bcryptSaltRounds: number;
  
  // Database configuration
  databaseUrl?: string;
  
  // Email configuration
  smtp: {
    enabled: boolean;
    host?: string;
    port?: number;
    secure?: boolean;
    user?: string;
    pass?: string;
    from?: string;
  };
  
  // File upload configuration
  upload: {
    maxFileSize: number;
    allowedTypes: string[];
    storageType: 'local' | 'cloud';
    storagePath: string;
  };
  
  // Feature flags
  features: {
    registration: boolean;
    emailNotifications: boolean;
    fileUpload: boolean;
    forum: boolean;
    training: boolean;
  };
}

/**
 * Load and validate configuration from environment variables
 */
export function loadConfig(): AppConfig {
  const config: AppConfig = {
    // Server
    port: parseInt(process.env.PORT || '5000'),
    nodeEnv: (process.env.NODE_ENV as any) || 'development',
    
    // Security
    sessionSecret: process.env.SESSION_SECRET || 'intrasphere-dev-secret-change-in-production',
    bcryptSaltRounds: parseInt(process.env.BCRYPT_SALT_ROUNDS || '12'),
    
    // Database
    databaseUrl: process.env.DATABASE_URL,
    
    // Email
    smtp: {
      enabled: process.env.SMTP_ENABLED === 'true',
      host: process.env.SMTP_HOST,
      port: process.env.SMTP_PORT ? parseInt(process.env.SMTP_PORT) : 587,
      secure: process.env.SMTP_SECURE === 'true',
      user: process.env.SMTP_USER,
      pass: process.env.SMTP_PASS,
      from: process.env.EMAIL_FROM || 'noreply@intrasphere.com',
    },
    
    // File upload
    upload: {
      maxFileSize: parseInt(process.env.MAX_FILE_SIZE || '10485760'), // 10MB
      allowedTypes: (process.env.ALLOWED_FILE_TYPES || '.pdf,.doc,.docx,.jpg,.png,.gif').split(','),
      storageType: (process.env.STORAGE_TYPE as any) || 'local',
      storagePath: process.env.STORAGE_PATH || './uploads',
    },
    
    // Features
    features: {
      registration: process.env.ENABLE_REGISTRATION !== 'false',
      emailNotifications: process.env.ENABLE_EMAIL_NOTIFICATIONS === 'true',
      fileUpload: process.env.ENABLE_FILE_UPLOAD !== 'false',
      forum: process.env.ENABLE_FORUM !== 'false',
      training: process.env.ENABLE_TRAINING !== 'false',
    },
  };
  
  return config;
}

/**
 * Validate configuration and warn about missing settings
 */
export function validateConfig(config: AppConfig): void {
  const warnings: string[] = [];
  const errors: string[] = [];
  
  // Production checks
  if (config.nodeEnv === 'production') {
    if (config.sessionSecret === 'intrasphere-dev-secret-change-in-production') {
      errors.push('SESSION_SECRET must be changed in production');
    }
    
    if (!config.databaseUrl) {
      warnings.push('DATABASE_URL not configured - using in-memory storage');
    }
    
    if (config.features.emailNotifications && !config.smtp.enabled) {
      warnings.push('Email notifications enabled but SMTP not configured');
    }
  }
  
  // SMTP validation
  if (config.smtp.enabled) {
    if (!config.smtp.host || !config.smtp.user || !config.smtp.pass) {
      errors.push('SMTP enabled but missing required configuration (host, user, pass)');
    }
  }
  
  // File upload validation
  if (config.upload.maxFileSize > 50 * 1024 * 1024) { // 50MB
    warnings.push('MAX_FILE_SIZE is very large - consider reducing for performance');
  }
  
  // Log warnings and errors
  if (warnings.length > 0) {
    console.warn('⚠️  Configuration warnings:');
    warnings.forEach(warning => console.warn(`   - ${warning}`));
  }
  
  if (errors.length > 0) {
    console.error('❌ Configuration errors:');
    errors.forEach(error => console.error(`   - ${error}`));
    
    if (config.nodeEnv === 'production') {
      throw new Error('Configuration validation failed in production');
    }
  }
}

/**
 * Get environment-specific configuration
 */
export const config = loadConfig();
validateConfig(config);

/**
 * Environment helper functions
 */
export const isDevelopment = () => config.nodeEnv === 'development';
export const isProduction = () => config.nodeEnv === 'production';
export const isTest = () => config.nodeEnv === 'test';