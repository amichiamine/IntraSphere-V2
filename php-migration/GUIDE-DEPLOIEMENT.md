# Guide de déploiement IntraSphere PHP

## Installation rapide sur hébergement partagé

### 1. Upload des fichiers
- Téléchargez tous les fichiers dans `public_html/intrasphere/`
- Assurez-vous que les permissions sont correctement définies (755 pour les dossiers, 644 pour les fichiers)

### 2. Configuration de la base de données
- Créez une base de données MySQL/MariaDB via votre panel de contrôle
- Notez les informations de connexion (host, nom de la base, utilisateur, mot de passe)

### 3. Installation automatique
1. Accédez à `https://votredomaine.com/intrasphere/install-simple.php`
2. Remplissez les informations de base de données
3. L'installation créera automatiquement les tables et les données de test

### 4. Test de l'installation
- Accédez à `https://votredomaine.com/intrasphere/test-simple.php` pour vérifier l'installation
- Si tout est vert, l'installation a réussi

### 5. Accès à l'application
- URL : `https://votredomaine.com/intrasphere/`
- Comptes de test :
  - **Admin** : `admin` / `admin123`
  - **Employé** : `marie.martin` / `password123`

### 6. Nettoyage (optionnel)
- Supprimez les fichiers d'installation pour la sécurité :
  - `install.php`
  - `install-simple.php`
  - `debug.php`
  - `test-install.php`
  - `test-simple.php`

## Configuration personnalisée

### Fichier .env
Créez un fichier `.env` avec vos paramètres :
```
DB_HOST=localhost
DB_NAME=votre_base
DB_USER=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
DB_DRIVER=mysql
```

### Structure de dossiers requise
```
intrasphere/
├── config/          # Configuration
├── src/            # Code source (contrôleurs, modèles)
├── views/          # Templates PHP
├── logs/           # Logs d'erreur (permissions 777)
├── public/uploads/ # Fichiers uploadés (permissions 777)
└── index.php       # Point d'entrée
```

## Dépannage

### Erreur 500
1. Vérifiez `logs/error.log`
2. Testez avec `debug.php`
3. Vérifiez les permissions des dossiers

### Base de données
1. Vérifiez les informations de connexion
2. Assurez-vous que l'utilisateur a les droits CREATE/INSERT/SELECT
3. Testez la connexion avec `test-simple.php`

### Permissions
- Dossiers : `chmod 755`
- Fichiers PHP : `chmod 644`
- Dossiers logs et uploads : `chmod 777`

## Support

Pour toute question ou problème :
1. Consultez les logs d'erreur
2. Utilisez les outils de diagnostic inclus
3. Vérifiez la documentation technique

L'application est conçue pour fonctionner sur la plupart des hébergements partagés PHP 7.4+ avec MySQL/MariaDB.