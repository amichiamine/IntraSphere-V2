# 🚀 IntraSphere - Plateforme Intranet d'Entreprise

Version PHP Pure - Package de déploiement automatisé pour hébergement web mutualisé

## 📋 Description

IntraSphere est une plateforme intranet complète conçue pour améliorer la communication et la collaboration au sein de votre entreprise. Cette version PHP pure est optimisée pour le déploiement sur des hébergements web mutualisés standard.

## ✨ Fonctionnalités

### 🔐 Authentification et Sécurité
- Système d'authentification sécurisé avec hashage bcrypt
- Gestion des rôles (Employé, Modérateur, Administrateur)  
- Protection CSRF intégrée
- Headers de sécurité HTTP
- Rate limiting pour prévenir les attaques

### 📢 Communication
- **Annonces** : Diffusion d'informations importantes
- **Messagerie interne** : Communication privée entre employés
- **Forum de discussion** : Échanges par sujets et catégories
- **Notifications multi-canaux** : Email, SMS, navigateur, in-app

### 📚 Gestion des Documents
- Upload et organisation des fichiers
- Catégorisation et tags
- Contrôle d'accès par rôles
- Recherche avancée
- Historique des versions

### 🎓 Formation et E-learning
- Création de modules de formation
- Suivi des progressions
- Quiz et évaluations
- Certificats automatiques
- Statistiques d'apprentissage

### 👥 Administration
- Gestion des utilisateurs et permissions
- Tableau de bord avec analytics
- Configuration système
- Logs et monitoring
- Outils de maintenance

### 📊 Reporting
- Statistiques d'utilisation
- Rapports d'activité
- Métriques de performance
- Export des données

## 🛠 Prérequis Techniques

### Serveur Web
- **PHP** : 7.4+ (recommandé : 8.0+)
- **Base de données** : MySQL 5.7+ ou PostgreSQL 12+
- **Serveur web** : Apache ou Nginx avec mod_rewrite
- **Espace disque** : Minimum 100MB
- **Mémoire** : 128MB minimum

### Extensions PHP Requises
- PDO (MySQL ou PostgreSQL)
- OpenSSL
- mbstring
- fileinfo
- JSON
- Session

### Extensions PHP Optionnelles
- GD ou ImageMagick (traitement d'images)
- Zip (compression de fichiers)
- Curl (notifications externes)

## 🚀 Installation Automatisée

### Étape 1 : Upload du Package
1. Téléchargez le package `intrasphere-deployment.zip`
2. Uploadez et décompressez dans votre dossier `public_html/intrasphere/`
3. Assurez-vous que tous les fichiers sont présents

### Étape 2 : Assistant d'Installation
1. Accédez à `https://votre-domaine.com/intrasphere/install.php`
2. Suivez l'assistant d'installation pas à pas :
   - **Vérification système** : Test de compatibilité
   - **Type d'hébergement** : Configuration prédéfinie
   - **Base de données** : Paramètres de connexion
   - **Compte admin** : Création du super-utilisateur
   - **Sécurité** : Configuration des paramètres

### Étape 3 : Configuration Post-Installation
1. Connectez-vous avec le compte administrateur créé
2. Configurez les paramètres de votre entreprise
3. Créez les comptes utilisateurs
4. Personnalisez les permissions et rôles

## 🏢 Configurations d'Hébergement Supportées

### cPanel (Standard)
- Hébergeurs : OVH, 1&1, Hostinger, etc.
- Configuration : MySQL, mod_rewrite activé
- Chemin : `public_html/intrasphere/`

### Hébergement Spécialisé
- **OVH Mutualisé** : Configuration optimisée
- **1&1 / Ionos** : Paramètres pré-configurés  
- **VPS/Dédié** : Support PostgreSQL
- **Développement Local** : XAMPP, WAMP, MAMP

## 📁 Structure du Projet

```
intrasphere/
├── install.php                 # Assistant d'installation
├── index.php                   # Point d'entrée principal
├── .env.example                # Configuration d'environnement
├── .htaccess                   # Configuration Apache
├── config/                     # Configuration système
│   ├── app.php                 # Configuration application
│   ├── database.php            # Configuration base de données
│   └── bootstrap.php           # Chargeur de classes
├── src/                        # Code source PHP
│   ├── controllers/            # Contrôleurs MVC
│   ├── models/                 # Modèles de données
│   └── utils/                  # Utilitaires et helpers
├── views/                      # Templates PHP
│   ├── auth/                   # Pages d'authentification
│   ├── dashboard/              # Tableau de bord
│   ├── admin/                  # Interface d'administration
│   └── layout/                 # Layouts de base
├── public/                     # Fichiers publics
│   ├── uploads/                # Fichiers uploadés
│   ├── css/                    # Feuilles de style
│   └── js/                     # Scripts JavaScript
├── sql/                        # Scripts de base de données
├── logs/                       # Fichiers de logs
└── tmp/                        # Fichiers temporaires
```

## 🔧 Configuration Avancée

### Variables d'Environnement (.env)
```env
# Environnement
APP_ENV=production
APP_DEBUG=false

# Base de données
DB_HOST=localhost
DB_NAME=intrasphere
DB_USER=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

# Sécurité
APP_KEY=clé-secrète-générée-automatiquement
SESSION_LIFETIME=3600
```

### Paramètres de Sécurité
- Protection CSRF automatique
- Hashage sécurisé des mots de passe
- Sessions sécurisées avec expiration
- Headers de sécurité HTTP
- Protection contre l'injection SQL

### Configuration du Cache
- Cache en mémoire pour les performances
- Support APCu si disponible
- Cache fichier en fallback
- TTL configurable par type de données

## 👤 Comptes de Démonstration

Après installation, les comptes suivants sont créés :

### Administrateur
- **Utilisateur** : admin
- **Mot de passe** : Le mot de passe que vous avez défini

### Employé de Test
- **Utilisateur** : marie.martin
- **Mot de passe** : password123

### Modérateur de Test
- **Utilisateur** : jean.dupont
- **Mot de passe** : moderator123

## 🆘 Dépannage

### Problèmes Courants

#### Erreur de connexion à la base de données
- Vérifiez les paramètres dans le fichier `.env`
- Assurez-vous que la base de données existe
- Vérifiez les permissions utilisateur

#### Erreur 500 (Erreur serveur)
- Vérifiez les logs Apache/PHP
- Assurez-vous que mod_rewrite est activé
- Vérifiez les permissions de fichiers (755 pour dossiers, 644 pour fichiers)

#### Page blanche après installation
- Vérifiez les logs dans le dossier `logs/`
- Assurez-vous que toutes les extensions PHP requises sont installées
- Vérifiez la configuration PHP (memory_limit, upload_max_filesize)

#### Problème d'upload de fichiers
- Vérifiez les permissions du dossier `public/uploads/`
- Ajustez `upload_max_filesize` dans la configuration PHP
- Vérifiez l'espace disque disponible

### Support Technique

Pour obtenir de l'aide :

1. **Documentation** : Consultez la documentation intégrée
2. **Logs** : Vérifiez les fichiers de logs pour les erreurs détaillées
3. **Configuration** : Utilisez l'outil de diagnostic intégré
4. **Community** : Forums et guides d'utilisateurs

## 🔄 Mise à Jour

### Sauvegarde Avant Mise à Jour
1. Sauvegardez votre base de données
2. Sauvegardez vos fichiers personnalisés
3. Notez votre configuration actuelle

### Processus de Mise à Jour
1. Téléchargez la nouvelle version
2. Remplacez les fichiers système (gardez `.env` et `uploads/`)
3. Exécutez les scripts de migration si nécessaire
4. Testez les fonctionnalités principales

## 📜 Licence et Copyright

IntraSphere est une solution propriétaire développée pour l'usage en entreprise.

**Version** : 1.0.0
**Date de release** : 2025
**Compatibilité** : PHP 7.4+ / MySQL 5.7+ / PostgreSQL 12+

---

🚀 **Prêt à transformer votre communication d'entreprise avec IntraSphere !**

Pour toute question technique ou commerciale, consultez la documentation intégrée ou contactez le support.