# INVENTAIRE EXHAUSTIF - BACKEND (DUAL STACK)

## 🏗️ ARCHITECTURE GÉNÉRALE

### Version Node.js/Express (Principale)

#### Technologies principales
- **Runtime**: Node.js avec Express.js
- **Language**: TypeScript avec ES modules
- **Base de données**: PostgreSQL avec Drizzle ORM
- **Validation**: Zod schemas partagés
- **Authentification**: Sessions avec bcrypt
- **Email**: Nodemailer
- **WebSockets**: ws
- **Sécurité**: Helmet, rate limiting, CSRF protection

#### Structure des dossiers
```
server/
├── index.ts                      # Point d'entrée principal
├── config.ts                     # Configuration générale
├── db.ts                         # Configuration base de données
├── vite.ts                       # Configuration Vite pour dev/prod
├── testData.ts                   # Données de test
├── migrations.ts                 # Migrations de sécurité
├── data/
│   └── storage.ts               # Interface IStorage + MemStorage
├── middleware/
│   └── security.ts              # Middleware sécurité
├── routes/                      # Routes API RESTful
│   ├── index.ts                 # Configuration des routes
│   ├── auth.ts                  # /api/auth/*
│   ├── admin.ts                 # /api/admin/*
│   ├── content.ts               # /api/announcements, /api/documents
│   ├── messaging.ts             # /api/messages, /api/complaints, /api/forum
│   ├── training.ts              # /api/training/*
│   └── users.ts                 # /api/users/*
├── services/
│   ├── auth.ts                  # AuthService
│   └── email.ts                 # EmailService
├── utils/
│   └── rate-limiter.ts          # Limitation de débit
└── public/                      # Assets statiques (production)
    ├── assets/
    └── index.html
```

### Version PHP (Migration)

#### Technologies principales
- **Language**: PHP 8+ (POO)
- **Base de données**: MySQL/PostgreSQL compatible
- **Architecture**: MVC pattern
- **Routage**: Router personnalisé
- **Sécurité**: Password hashing, SQL prepared statements

#### Structure des dossiers
```
php-migration/
├── index.php                    # Point d'entrée
├── README.md                    # Documentation PHP
├── config/
│   ├── app.php                  # Configuration application
│   ├── bootstrap.php            # Autoloader et initialisation
│   └── database.php             # Configuration BDD
├── sql/
│   └── create_tables.sql        # Script de création des tables
├── src/
│   ├── Router.php               # Routeur personnalisé
│   ├── controllers/             # Contrôleurs MVC
│   │   ├── BaseController.php   # Contrôleur de base
│   │   ├── AdminController.php  # Administration
│   │   ├── AnnouncementsController.php
│   │   ├── DashboardController.php
│   │   ├── DocumentsController.php
│   │   ├── MessagesController.php
│   │   ├── TrainingsController.php
│   │   ├── UploadController.php # Upload de fichiers
│   │   └── Api/                 # API Controllers
│   ├── models/                  # Modèles de données
│   │   ├── BaseModel.php        # Modèle de base
│   │   ├── User.php             # Utilisateurs
│   │   ├── Announcement.php     # Annonces
│   │   ├── Document.php         # Documents
│   │   ├── Message.php          # Messages
│   │   ├── Complaint.php        # Réclamations
│   │   ├── Training.php         # Formations
│   │   ├── Content.php          # Contenu multimédia
│   │   ├── Event.php            # Événements
│   │   └── Permission.php       # Permissions
│   └── utils/                   # Utilitaires
│       ├── helpers.php          # Fonctions helper
│       ├── CacheManager.php     # Gestion du cache
│       ├── Logger.php           # Logging
│       ├── PasswordValidator.php # Validation mot de passe
│       ├── PermissionManager.php # Gestion des permissions
│       └── RateLimiter.php      # Limitation de débit
└── views/                       # Vues PHP/HTML
    ├── layout/
    │   └── app.php              # Layout principal
    ├── admin/
    │   └── index.php            # Interface admin
    ├── auth/
    │   └── login.php            # Connexion
    ├── dashboard/
    │   └── index.php            # Tableau de bord
    ├── announcements/
    │   ├── index.php
    │   └── create.php
    ├── documents/
    │   └── index.php
    ├── messages/
    │   └── index.php
    └── trainings/
        └── index.php
```

## 🗄️ MODÈLES DE DONNÉES

### Schémas partagés (`shared/schema.ts`)

#### Entités principales
1. **Users** - Utilisateurs et employés
   - Champs: id, username, password, name, role, avatar
   - Champs étendus: employeeId, department, position, isActive
   - Contacts: phone, email
   - Timestamps: createdAt, updatedAt

2. **Announcements** - Annonces et communications
   - Champs: id, title, content, type, authorId, authorName
   - Visual: imageUrl, icon, isImportant
   - Timestamp: createdAt

3. **Documents** - Bibliothèque de documents
   - Champs: id, title, description, category, fileName, fileUrl
   - Versioning: version, updatedAt

4. **Events** - Événements et rendez-vous
   - Champs: id, title, description, date, location, type
   - Relations: organizerId
   - Timestamp: createdAt

5. **Messages** - Messagerie interne
   - Champs: id, senderId, recipientId, subject, content
   - État: isRead
   - Timestamp: createdAt

6. **Complaints** - Système de réclamations
   - Champs: id, submitterId, assignedToId, title, description
   - Classification: category, priority, status
   - Timestamps: createdAt, updatedAt

7. **Permissions** - Délégation de droits
   - Champs: id, userId, grantedBy, permission
   - Timestamp: createdAt

#### Entités de formation
8. **Trainings** - Formations disponibles
   - Info: id, title, description, category, difficulty, duration
   - Instructeur: instructorId, instructorName
   - Planning: startDate, endDate, location
   - Capacité: maxParticipants, currentParticipants
   - Statuts: isMandatory, isActive, isVisible
   - Médias: thumbnailUrl, documentUrls[]

9. **TrainingParticipants** - Participants aux formations
   - Relations: trainingId, userId
   - État: status, registeredAt, completionDate, score

#### Entités forum et contenu
10. **ForumCategories** - Catégories du forum
11. **ForumTopics** - Sujets de discussion
12. **ForumPosts** - Messages du forum
13. **ForumLikes** - Système de likes
14. **ForumUserStats** - Statistiques utilisateurs
15. **Contents** - Contenu multimédia
16. **Categories** - Catégories de contenu
17. **EmployeeCategories** - Catégories d'employés

## 🔐 SYSTÈME D'AUTHENTIFICATION

### Version Node.js/Express

#### Routes d'authentification (`routes/auth.ts`)
- **POST /api/auth/login**: Connexion avec rate limiting
- **POST /api/auth/register**: Inscription avec validation
- **POST /api/auth/logout**: Déconnexion et nettoyage session
- **GET /api/auth/me**: Profil utilisateur actuel

#### AuthService (`services/auth.ts`)
- `validatePasswordStrength()`: Validation robuste des mots de passe
- `hashPassword()`: Hachage sécurisé bcrypt
- `verifyPassword()`: Vérification des mots de passe
- Session management avec express-session

#### Sécurité implémentée
- Helmet.js pour headers de sécurité
- Rate limiting par IP/endpoint
- CSRF protection
- Session sécurisée avec PostgreSQL store
- Sanitisation des entrées
- Validation Zod stricte

### Version PHP

#### Modèle User (`models/User.php`)
- `create()`: Création avec hachage automatique
- `findByUsername()`: Recherche par nom d'utilisateur
- `findByEmployeeId()`: Recherche par ID employé
- `authenticate()`: Vérification des identifiants
- `changePassword()`: Changement de mot de passe
- `getDirectory()`: Annuaire des employés
- `search()`: Recherche d'utilisateurs
- `getStats()`: Statistiques

#### Sécurité PHP
- `password_hash()`/`password_verify()` pour les mots de passe
- Prepared statements contre l'injection SQL
- Validation et sanitisation des entrées
- Gestion des sessions sécurisée
- Headers de sécurité

## 🛠️ ROUTES ET ENDPOINTS

### API RESTful (Node.js)

#### Authentification (`/api/auth/`)
- `POST /login` - Connexion
- `POST /register` - Inscription  
- `POST /logout` - Déconnexion
- `GET /me` - Profil actuel

#### Administration (`/api/admin/`)
- `GET /stats` - Statistiques générales
- `GET /users` - Liste utilisateurs (admin)
- `POST /users/:id/role` - Changer rôle
- `DELETE /users/:id` - Supprimer utilisateur
- `GET /permissions` - Gestion permissions
- `POST /permissions` - Accorder permission

#### Contenu (`/api/`)
- **Annonces**: `GET|POST|PUT|DELETE /announcements/:id?`
- **Documents**: `GET|POST|PUT|DELETE /documents/:id?`
- **Contenus**: `GET|POST|PUT|DELETE /contents/:id?`

#### Messagerie (`/api/`)
- **Messages**: `GET|POST /messages`, `PUT /messages/:id/read`
- **Réclamations**: `GET|POST|PUT /complaints/:id?`
- **Forum**: 
  - `GET|POST /forum/categories`
  - `GET|POST /forum/topics`
  - `GET|POST /forum/posts`
  - `POST|DELETE /forum/likes`

#### Formation (`/api/training/`)
- `GET /` - Liste des formations
- `POST /` - Créer formation (admin)
- `GET /:id` - Détails formation
- `POST /:id/register` - S'inscrire
- `GET /:id/participants` - Liste participants
- `POST /:id/complete` - Marquer terminé

#### Utilisateurs (`/api/users/`)
- `GET /` - Annuaire
- `GET /:id` - Profil utilisateur
- `PUT /:id` - Modifier profil
- `GET /search` - Recherche utilisateurs

### Routes MVC (PHP)

#### Routes principales
- `GET /` - Dashboard
- `GET /login` - Page de connexion
- `POST /auth` - Authentification
- `GET /logout` - Déconnexion

#### Routes d'administration
- `GET /admin` - Panel admin
- `GET /admin/users` - Gestion utilisateurs
- `GET /admin/permissions` - Gestion permissions
- `GET /admin/system` - Configuration système

#### Routes de contenu
- `GET /announcements` - Liste annonces
- `GET /announcements/create` - Créer annonce
- `POST /announcements` - Sauvegarder annonce
- `GET /documents` - Bibliothèque documents
- `GET /trainings` - Formations disponibles

## 💾 STOCKAGE ET BASE DE DONNÉES

### Version Node.js (Drizzle ORM)

#### Configuration (`db.ts`)
- Pool de connexions PostgreSQL
- Configuration optimisée pour production
- SSL/TLS support
- Connection retry logic

#### Interface IStorage (`data/storage.ts`)
- Pattern d'interface pour toutes les opérations CRUD
- MemStorage pour développement/test
- PostgreSQL storage pour production
- Méthodes par entité (Users, Announcements, etc.)

#### Migrations (`migrations.ts`)
- Migration de sécurité des mots de passe
- Updates de schéma automatiques
- Backup/restore procedures

### Version PHP (MySQL/PostgreSQL)

#### Configuration (`config/database.php`)
- Support multi-base (MySQL/PostgreSQL)
- Pool de connexions
- Configuration par environnement

#### Modèles de base (`models/BaseModel.php`)
- CRUD operations standardisées
- Query builder simplifié
- Validation automatique
- Relations entre entités

#### Schéma SQL (`sql/create_tables.sql`)
- Tables relationnelles complètes
- Contraintes d'intégrité
- Index de performance
- Support des deux SGBD

## 🔧 SERVICES ET UTILITAIRES

### Services Node.js

#### EmailService (`services/email.ts`)
- Configuration Nodemailer
- Templates d'emails
- Queue de traitement
- Retry logic

#### RateLimiter (`utils/rate-limiter.ts`)
- Limitation par endpoint
- Redis/Memory store
- Configuration flexible
- Logging des tentatives

### Utilitaires PHP

#### CacheManager (`utils/CacheManager.php`)
- Cache en mémoire/fichier
- Invalidation intelligente
- TTL configurable

#### Logger (`utils/Logger.php`)
- Logging structuré
- Rotation des logs
- Niveaux de log

#### PasswordValidator (`utils/PasswordValidator.php`)
- Validation complexité
- Règles configurables
- Messages localisés

#### PermissionManager (`utils/PermissionManager.php`)
- Vérification des droits
- Cache des permissions
- Délégation de rôles

## 🛡️ SÉCURITÉ BACKEND

### Protections communes
- Hachage sécurisé des mots de passe
- Protection contre l'injection SQL
- Validation stricte des entrées
- Rate limiting
- Sessions sécurisées
- Headers de sécurité
- CSRF protection

### Spécifique Node.js
- Helmet.js pour headers HTTP
- express-rate-limit
- express-session avec store PostgreSQL
- Zod validation
- bcrypt pour les mots de passe

### Spécifique PHP
- Prepared statements
- password_hash/password_verify
- htmlspecialchars pour XSS
- CSRF tokens
- Session hijacking protection

## 📊 MONITORING ET LOGGING

### Node.js
- Console logging avec formatage
- Request logging middleware
- Performance monitoring
- Error tracking

### PHP  
- Logger centralisé
- Error handling global
- Performance metrics
- Debug modes

## 🚀 DÉPLOIEMENT ET CONFIGURATION

### Node.js/Express
- Variables d'environnement (.env)
- Configuration par environnement
- PM2 pour la production
- Docker support

### PHP
- Configuration Apache/Nginx
- PHP-FPM optimization
- Environment variables
- Cache OPcache

## 🔄 COMPATIBILITÉ ET MIGRATION

### Équivalences fonctionnelles
- **Authentification**: Sessions PHP ↔ Express sessions
- **ORM**: Modèles PHP ↔ Drizzle ORM
- **Validation**: Validation PHP ↔ Zod schemas
- **Routage**: Router PHP ↔ Express routes
- **Cache**: CacheManager ↔ Memory/Redis cache

### Points de migration
- Conversion des schémas de données
- Adaptation des endpoints API
- Migration des sessions utilisateurs
- Transfert des fichiers uploadés
- Synchronisation des permissions

---
*Inventaire généré le 8 août 2025 - Versions Node.js/Express + PHP*