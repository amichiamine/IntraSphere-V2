# Guide d'Installation - IntraSphere PHP

## 📋 Prérequis
- PHP 8.1+ avec extensions PDO, PDO_MySQL ou PDO_PostgreSQL
- Base de données MySQL 8.0+ ou PostgreSQL 12+
- Serveur web (Apache/Nginx) avec mod_rewrite activé

## 🚀 Installation sur Hébergement

### Étape 1: Copier les fichiers
```bash
# Copier tout le contenu du dossier php-migration vers votre hébergement
# Structure recommandée :
/public_html/intrasphere/
├── config/
├── src/
├── views/
├── sql/
├── .htaccess
└── index.php
```

### Étape 2: Configuration de la Base de Données

#### A. Créer la base de données
1. **Accédez à votre panneau de contrôle** (cPanel, Plesk, etc.)
2. **Créez une nouvelle base de données** : `intrasphere`
3. **Créez un utilisateur dédié** avec tous les privilèges
4. **Notez les informations de connexion**

#### B. Variables d'environnement
Créez un fichier `.env` à la racine :

```env
# Configuration Base de Données
DB_DRIVER=mysql          # ou 'pgsql' pour PostgreSQL
DB_HOST=localhost        # ou IP du serveur DB
DB_PORT=3306            # 3306 pour MySQL, 5432 pour PostgreSQL
DB_NAME=intrasphere     # nom de votre base
DB_USER=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

# Configuration Sécurité
APP_ENV=production
SESSION_SECRET=votre_cle_secrete_longue_et_unique
```

#### C. Hébergement Mutualisé (sans .env)
Si votre hébergeur ne supporte pas les fichiers .env, modifiez directement `config/database.php` :

```php
// Remplacez les lignes 13-17 par vos vraies valeurs :
$host = 'localhost';  // ou mysql.votre-hebergeur.com
$port = '3306';       // port MySQL de l'hébergeur
$dbname = 'votre_nom_base';
$username = 'votre_user';
$password = 'votre_password';
```

### Étape 3: Installation du Schema

#### Option A: Interface Web (Recommandé)
1. Naviguez vers `https://votre-site.com/intrasphere/install.php`
2. Suivez l'assistant d'installation
3. Les tables seront créées automatiquement

#### Option B: Manuel via phpMyAdmin
1. Connectez-vous à **phpMyAdmin**
2. Sélectionnez votre base `intrasphere`
3. Importez le fichier `sql/create_tables.sql`
4. Exécutez le script `sql/insert_demo_data.sql`

#### Option C: Ligne de commande
```bash
# MySQL
mysql -u votre_user -p votre_base < sql/create_tables.sql
mysql -u votre_user -p votre_base < sql/insert_demo_data.sql

# PostgreSQL  
psql -U votre_user -d votre_base -f sql/create_tables.sql
psql -U votre_user -d votre_base -f sql/insert_demo_data.sql
```

## 🔧 Configuration Hébergeur Spécifique

### cPanel/Hébergement Mutualisé
```php
// config/database.php - Configuration typique cPanel
$host = 'localhost';
$dbname = 'cpanel_user_intrasphere';  // format: utilisateur_nom_base
$username = 'cpanel_user_intrasphere';
$password = 'mot_de_passe_genere';
```

### Hébergement VPS/Dédié
```php
// Utilisez les vraies informations de votre serveur
$host = '127.0.0.1';  // ou IP interne
$dbname = 'intrasphere';
$username = 'intrasphere_user';
$password = 'mot_de_passe_securise';
```

### Hébergement Cloud (OVH, 1&1, etc.)
```php
// Format typique hébergeurs cloud
$host = 'mysql-intrasphere.hosting.ovh.net';  // hostname fourni
$dbname = 'intrasphere';
$username = 'intrasphere';
$password = 'cle_fournie_par_hebergeur';
```

## 🛠️ Configuration Apache (.htaccess)

Le fichier `.htaccess` est déjà inclus avec :
```apache
# Réécriture d'URL pour SPA
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Sécurité
Header always set X-Frame-Options DENY
Header always set X-Content-Type-Options nosniff
```

## ✅ Vérification de l'Installation

1. **Test de connexion DB** : `https://votre-site.com/intrasphere/test-db.php`
2. **Page de login** : `https://votre-site.com/intrasphere/`
3. **Identifiants par défaut** :
   - **Admin** : `admin` / `admin123`
   - **Employé** : `marie.martin` / `password123`

## 🚨 Sécurité Post-Installation

1. **Supprimez** les fichiers de test : `install.php`, `test-db.php`
2. **Changez** les mots de passe par défaut
3. **Configurez HTTPS** obligatoire
4. **Limitez** l'accès aux dossiers `config/` et `sql/`

## 📞 Support Technique

En cas de problème :
1. Vérifiez les logs d'erreur PHP de votre hébergeur
2. Testez la connexion DB avec le script de diagnostic
3. Consultez la documentation de votre hébergeur pour PDO