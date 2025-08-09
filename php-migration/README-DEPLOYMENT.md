# 🚀 Guide de Déploiement IntraSphere PHP

## 📁 Fichiers de Configuration Inclus

### Configuration Base de Données
- **`.env.example`** - Modèle de configuration à copier
- **`config/database-examples.php`** - Exemples selon hébergeurs
- **`config/setup.php`** - Classe de configuration automatique
- **`config-wizard.php`** - Assistant de configuration graphique
- **`install.php`** - Installation automatique des tables
- **`test-db.php`** - Test de connexion base de données

## 🎯 3 Méthodes de Déploiement

### Method 1: Assistant Graphique (Recommandé)
```
1. Uploadez tous les fichiers sur votre hébergement
2. Naviguez vers: votre-site.com/config-wizard.php
3. Choisissez votre type d'hébergement
4. Remplissez les paramètres
5. L'assistant teste et configure automatiquement
```

### Method 2: Configuration Manuelle
```
1. Copiez .env.example vers .env
2. Modifiez les valeurs selon votre hébergeur
3. Lancez install.php pour créer les tables
```

### Method 3: Modification Directe
```
1. Ouvrez config/database-examples.php
2. Copiez la configuration de votre hébergeur
3. Collez dans config/database.php
```

## 🏠 Configurations Hébergeurs

### cPanel (Hébergement Mutualisé)
```env
DB_DRIVER=mysql
DB_HOST=localhost  
DB_PORT=3306
DB_NAME=cpanel_user_intrasphere
DB_USER=cpanel_user_intrasphere
DB_PASSWORD=your_password
```

### OVH Mutualisé
```env
DB_DRIVER=mysql
DB_HOST=mysql-intrasphere.hosting.ovh.net
DB_PORT=3306
DB_NAME=intrasphere
DB_USER=intrasphere
DB_PASSWORD=ovh_generated_key
```

### 1&1 / Ionos
```env
DB_DRIVER=mysql
DB_HOST=db12345.hosting.1and1.com
DB_PORT=3306
DB_NAME=db12345678
DB_USER=dbo12345678
DB_PASSWORD=ionos_password
```

### VPS / Serveur Dédié
```env
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=intrasphere
DB_USER=intrasphere_user
DB_PASSWORD=secure_password
```

### PostgreSQL
```env
DB_DRIVER=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_NAME=intrasphere
DB_USER=postgres
DB_PASSWORD=postgres_password
```

### Développement Local (XAMPP/WAMP)
```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=intrasphere
DB_USER=root
DB_PASSWORD=
```

## ✅ Vérification Post-Déploiement

1. **Test connexion**: `votre-site.com/test-db.php`
2. **Installation tables**: `votre-site.com/install.php`
3. **Accès application**: `votre-site.com/`
4. **Login par défaut**: `admin` / `admin123`

## 🔒 Sécurité

Après installation, supprimez:
- `config-wizard.php`
- `install.php` 
- `test-db.php`
- `README-DEPLOYMENT.md`

## 📞 Support

En cas de problème:
1. Vérifiez les logs d'erreur de votre hébergeur
2. Utilisez `test-db.php` pour diagnostiquer
3. Consultez `config/database-examples.php` pour votre hébergeur