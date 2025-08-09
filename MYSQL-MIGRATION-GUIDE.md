# 🔄 Guide Migration MySQL pour IntraSphere React

## 📋 Vue d'ensemble

La version React d'IntraSphere peut maintenant fonctionner avec **MySQL** en plus de PostgreSQL (Neon Database). Ce guide explique comment basculer vers MySQL.

## 🎯 Fichiers Créés pour MySQL

### Configuration Base de Données
- **`server/db-mysql.ts`** - Connexion et configuration MySQL
- **`shared/schema-mysql.ts`** - Schema Drizzle adapté pour MySQL
- **`drizzle-mysql.config.ts`** - Configuration Drizzle Kit pour MySQL
- **`server/db-config.ts`** - Détection automatique du type de base
- **`.env.mysql.example`** - Modèles de configuration MySQL

## 🚀 Comment Activer MySQL

### Étape 1: Configuration Variables d'Environnement

Copiez `.env.mysql.example` vers `.env` et configurez selon votre hébergement :

#### **Développement Local (XAMPP/WAMP)**
```env
MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_USER=root
MYSQL_PASSWORD=
MYSQL_DATABASE=intrasphere
```

#### **Hébergement cPanel**
```env
MYSQL_HOST=localhost
MYSQL_USER=cpanel_user_intrasphere
MYSQL_PASSWORD=your_cpanel_password
MYSQL_DATABASE=cpanel_user_intrasphere
```

#### **OVH Mutualisé**
```env
MYSQL_HOST=mysql-intrasphere.hosting.ovh.net
MYSQL_USER=intrasphere
MYSQL_PASSWORD=ovh_generated_password
MYSQL_DATABASE=intrasphere
```

#### **1&1/Ionos**
```env
MYSQL_HOST=db12345.hosting.1and1.com
MYSQL_USER=dbo12345678
MYSQL_PASSWORD=ionos_password
MYSQL_DATABASE=db12345678
```

### Étape 2: Installation Dépendances

La dépendance `mysql2` est déjà installée automatiquement.

### Étape 3: Migration Base de Données

```bash
# Générer les migrations MySQL
npm run db:generate:mysql

# Appliquer les migrations
npm run db:push:mysql

# Ou directement push du schema
npx drizzle-kit push --config=drizzle-mysql.config.ts
```

### Étape 4: Modification Code Application

Le système détecte automatiquement MySQL grâce à `server/db-config.ts`. Aucune modification manuelle requise !

## 🔧 Scripts NPM Recommandés

Ajoutez dans `package.json` :

```json
{
  "scripts": {
    "db:generate:mysql": "drizzle-kit generate --config=drizzle-mysql.config.ts",
    "db:push:mysql": "drizzle-kit push --config=drizzle-mysql.config.ts",
    "db:migrate:mysql": "drizzle-kit migrate --config=drizzle-mysql.config.ts",
    "db:studio:mysql": "drizzle-kit studio --config=drizzle-mysql.config.ts"
  }
}
```

## 📊 Différences Schema MySQL vs PostgreSQL

### Types de Données
| PostgreSQL | MySQL | Description |
|------------|--------|-------------|
| `varchar()` | `varchar(length)` | Longueur obligatoire |
| `text` | `text` | Identique |
| `timestamp` | `timestamp` | Format différent |
| `gen_random_uuid()` | IDs manuels | Génération UUID |

### ENUM Types
- **PostgreSQL**: `text` avec validation
- **MySQL**: `mysqlEnum()` natif

### Auto-Update Timestamps
- **PostgreSQL**: `defaultNow()`
- **MySQL**: `ON UPDATE CURRENT_TIMESTAMP`

## 🔄 Détection Automatique

Le fichier `server/db-config.ts` détecte automatiquement :

1. **MySQL** si présence de : `MYSQL_HOST`, `MYSQL_USER`, ou `MYSQL_DATABASE`
2. **PostgreSQL** si présence de : `DATABASE_URL`
3. **Erreur** si aucune configuration détectée

## ✅ Vérification Installation MySQL

```bash
# Tester connexion
node -e "
require('dotenv').config();
const mysql = require('mysql2/promise');
mysql.createConnection({
  host: process.env.MYSQL_HOST,
  user: process.env.MYSQL_USER,
  password: process.env.MYSQL_PASSWORD,
  database: process.env.MYSQL_DATABASE
}).then(() => console.log('✅ MySQL OK'))
.catch(err => console.log('❌ MySQL Error:', err.message))
"
```

## 🚨 Limitations MySQL vs PostgreSQL

### Fonctionnalités PostgreSQL Non Disponibles
- UUID génération automatique via `gen_random_uuid()`
- JSON/JSONB types natifs avancés
- Extensions PostgreSQL (postgis, etc.)

### Solutions MySQL
- **UUIDs** : Génération côté application avec `crypto.randomUUID()`
- **JSON** : Type `JSON` MySQL ou stockage `TEXT` avec parsing
- **Relations** : Clés étrangères standards supportées

## 🔄 Migration Données PostgreSQL → MySQL

```sql
-- Export depuis PostgreSQL
pg_dump --data-only --inserts intrasphere > data_export.sql

-- Import vers MySQL (après adaptation des types)
mysql -u user -p intrasphere < data_export_adapted.sql
```

## 📞 Support Multi-Base

L'application supporte maintenant :
- **PostgreSQL** (Neon Database) - Configuration actuelle
- **MySQL** - Nouvelle configuration
- **Détection automatique** selon variables d'environnement
- **Schemas compatibles** pour les deux bases

Choisissez selon votre hébergement et préférences !