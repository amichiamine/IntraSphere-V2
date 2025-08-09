import mysql from 'mysql2/promise';
import { drizzle } from 'drizzle-orm/mysql2';
import * as schema from "@shared/schema";

// Configuration MySQL
const mysqlConfig = {
  host: process.env.MYSQL_HOST || 'localhost',
  port: parseInt(process.env.MYSQL_PORT || '3306'),
  user: process.env.MYSQL_USER || 'root',
  password: process.env.MYSQL_PASSWORD || '',
  database: process.env.MYSQL_DATABASE || 'intrasphere',
  ssl: process.env.MYSQL_SSL === 'true' ? {
    rejectUnauthorized: false
  } : undefined,
  charset: 'utf8mb4'
};

// Créer la connexion MySQL
export const mysqlConnection = mysql.createPool(mysqlConfig);

// Initialiser Drizzle avec MySQL
export const mysqlDb = drizzle(mysqlConnection, { 
  schema, 
  mode: 'default' 
});

// Export pour compatibilité
export const db = mysqlDb;
export const pool = mysqlConnection;