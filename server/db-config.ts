/**
 * Configuration dynamique de base de données
 * Permet de choisir entre PostgreSQL (Neon) et MySQL
 */

// Détecter le type de base de données selon les variables d'environnement
const isDatabaseMySQL = () => {
  return !!(process.env.MYSQL_HOST || process.env.MYSQL_USER || process.env.MYSQL_DATABASE);
};

const isDatabasePostgreSQL = () => {
  return !!process.env.DATABASE_URL;
};

// Export conditionnel selon le type de base détecté
if (isDatabaseMySQL()) {
  console.log('🔗 Using MySQL database configuration');
  
  // Importer la configuration MySQL
  const mysqlDb = require('./db-mysql');
  module.exports = {
    db: mysqlDb.db,
    pool: mysqlDb.pool,
    dbType: 'mysql'
  };
  
} else if (isDatabasePostgreSQL()) {
  console.log('🔗 Using PostgreSQL (Neon) database configuration');
  
  // Importer la configuration PostgreSQL par défaut
  const postgresDb = require('./db');
  module.exports = {
    db: postgresDb.db,
    pool: postgresDb.pool,
    dbType: 'postgresql'
  };
  
} else {
  throw new Error(`
⚠️  Aucune configuration de base de données détectée !

Pour MySQL, définissez:
- MYSQL_HOST=localhost
- MYSQL_USER=root
- MYSQL_PASSWORD=yourpassword
- MYSQL_DATABASE=intrasphere

Pour PostgreSQL, définissez:
- DATABASE_URL=postgresql://user:pass@host:port/db

Consultez .env.mysql.example pour des exemples complets.
  `);
}

// Export des types pour TypeScript
export type DatabaseType = 'mysql' | 'postgresql';