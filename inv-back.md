# INVENTAIRE COMPLET - BACKEND EXPRESS.JS

## ARCHITECTURE GÉNÉRALE

### Structure des Répertoires
```
server/
├── index.ts                      # Point d'entrée principal
├── config.ts                     # Configuration générale
├── db.ts                         # Configuration Drizzle + Neon
├── vite.ts                       # Configuration Vite intégrée
├── migrations.ts                 # Migrations de sécurité
├── testData.ts                   # Données de test/démo
├── data/
│   └── storage.ts               # Interface de stockage abstraite
├── middleware/
│   └── security.ts              # Middleware de sécurité
├── routes/                      # Routes API organisées par domaine
│   ├── index.ts                 # Registre des routes
│   ├── auth.ts                  # Authentification
│   ├── users.ts                 # Gestion des utilisateurs
│   ├── content.ts               # Gestion de contenu
│   ├── messaging.ts             # Messagerie interne
│   ├── training.ts              # Formation/apprentissage
│   └── admin.ts                 # Administration système
├── services/
│   ├── auth.ts                  # Services d'authentification
│   └── email.ts                 # Services d'email
├── utils/
│   └── rate-limiter.ts         # Limitation de taux
└── public/                      # Assets statiques (build)
    ├── index.html
    └── assets/

shared/                          # Ressources partagées client/serveur
├── schema.ts                    # Schémas Drizzle + validation Zod
├── constants/
│   ├── security-constants.ts    # Constantes de sécurité
│   └── security-constants.php   # Version PHP (migration)
├── services/
│   ├── error-handler.ts         # Gestionnaire d'erreurs
│   └── ErrorHandler.php         # Version PHP
├── utils/
│   ├── api-response.ts          # Réponses API standardisées
│   └── ApiResponse.php          # Version PHP
└── validators/
    ├── universal-validator.ts   # Validation universelle
    └── UniversalValidator.php   # Version PHP
```

### Technologies et Stack Backend
- **Runtime**: Node.js avec TypeScript
- **Framework**: Express.js 4.21
- **Base de données**: PostgreSQL via Neon DB (@neondatabase/serverless)
- **ORM**: Drizzle ORM 0.39 avec Drizzle Kit
- **Validation**: Zod + drizzle-zod
- **Authentification**: bcrypt + express-session
- **Sécurité**: helmet + express-rate-limit
- **Email**: nodemailer
- **Traduction**: libretranslate
- **WebSocket**: ws (pour temps réel)
- **Monitoring**: Memoization et cache

## SCHÉMA DE BASE DE DONNÉES

### Tables Principales (schema.ts)
**Total : 22 tables définies**

#### Gestion des Utilisateurs
- `users` - Utilisateurs avec rôles (admin, moderator, employee)
  - 14 champs : id, username, password, name, role, avatar, employeeId, department, position, isActive, phone, email, createdAt, updatedAt
- `permissions` - Délégations de permissions granulaires
  - 5 champs : id, userId, grantedBy, permission, createdAt

#### Gestion de Contenu
- `announcements` - Annonces avec types (info, important, event, formation)
  - 8 champs : id, title, content, type, authorId, authorName, imageUrl, icon, createdAt, isImportant
- `documents` - Bibliothèque de documents
  - 6 champs : id, title, description, category, fileName, fileUrl, updatedAt, version
- `events` - Calendrier des événements
  - 7 champs : id, title, description, date, location, type, organizerId, createdAt
- `contents` - Contenu générique
- `categories` - Catégories de contenu
- `employeeCategories` - Catégories d'employés

#### Communication
- `messages` - Messagerie interne
  - 7 champs : id, senderId, recipientId, subject, content, isRead, createdAt
- `complaints` - Système de réclamations
  - 9 champs : id, submitterId, assignedToId, title, description, category, priority, status, createdAt, updatedAt

#### Système de Formation
- `trainings` - Formations présentielles
  - 16 champs : id, title, description, category, difficulty, duration, instructorId, instructorName, startDate, endDate, location, maxParticipants, currentParticipants, isMandatory, isActive, isVisible, thumbnailUrl, documentUrls, createdAt, updatedAt
- `trainingParticipants` - Participants aux formations
  - 7 champs : id, trainingId, userId, registeredAt, status, completionDate, score, feedback

#### E-Learning
- `courses` - Cours en ligne
  - 14 champs : id, title, description, category, difficulty, estimatedHours, thumbnailUrl, isPublished, createdBy, createdAt, updatedAt
- `lessons` - Leçons des cours
  - 12 champs : id, courseId, title, description, content, order, type, duration, videoUrl, documentUrls, isRequired, createdAt
- `quizzes` - Quiz d'évaluation
  - 9 champs : id, lessonId, title, description, questions, passingScore, timeLimit, attempts, createdAt
- `enrollments` - Inscriptions aux cours
  - 6 champs : id, courseId, userId, enrolledAt, status, completedAt
- `lessonProgress` - Progression dans les leçons
  - 6 champs : id, lessonId, userId, completedAt, timeSpent, status
- `quizAttempts` - Tentatives de quiz
  - 6 champs : id, quizId, userId, score, answers, attemptedAt
- `certificates` - Certificats d'achèvement
  - 6 champs : id, courseId, userId, issuedAt, certificateUrl, verificationCode
- `resources` - Ressources téléchargeables
  - 8 champs : id, title, description, type, fileUrl, fileName, fileSize, createdAt

#### Forum
- `forumCategories` - Catégories de forum
- `forumTopics` - Sujets de discussion
- `forumPosts` - Posts dans les sujets
- `forumLikes` - Système de likes
- `forumUserStats` - Statistiques utilisateur

#### Système
- `systemSettings` - Paramètres système globaux

## SERVICES ET MIDDLEWARE

### Services d'Authentification (auth.ts)
**AuthService** avec méthodes statiques :
- `hashPassword(password)` - Hachage bcrypt avec 12 rounds
- `verifyPassword(password, hash)` - Vérification bcrypt
- `validatePasswordStrength(password)` - Validation complexité :
  - Minimum 8 caractères
  - Au moins 1 majuscule
  - Au moins 1 minuscule  
  - Au moins 1 chiffre
  - Au moins 1 caractère spécial

### Services d'Email (email.ts)
**EmailService** pour notifications :
- Email de bienvenue
- Notifications système
- Rappels de formation

### Middleware de Sécurité (security.ts)
**Fonctions de sécurité :**
- `configureSecurity(app)` - Configuration Helmet CSP
- `sanitizeInput(req, res, next)` - Sanitisation des entrées
- `getSessionConfig()` - Configuration sessions sécurisées
- **Rate Limiting** :
  - Auth : 5 tentatives/15min
  - API générale : 100 requêtes/15min

### Rate Limiting (rate-limiter.ts)
**RateLimiter** avec profils :
- Login/Register : limite stricte
- API générale : limite élevée
- Middleware Express intégré

## ROUTES API COMPLÈTES

### Authentication Routes (/api/auth/*)
- `POST /api/auth/login` - Connexion avec validation
- `POST /api/auth/register` - Inscription avec hashage
- `GET /api/auth/me` - Profil utilisateur actuel
- `POST /api/auth/logout` - Déconnexion et nettoyage session

### Users Routes (/api/users/*)
- `GET /api/users` - Liste des utilisateurs (admin/moderator)
- `GET /api/users/:id` - Détails utilisateur
- `PUT /api/users/:id` - Mise à jour profil
- `DELETE /api/users/:id` - Suppression utilisateur (admin)
- `PUT /api/users/:id/password` - Changement mot de passe
- `GET /api/users/:id/trainings` - Formations utilisateur
- `POST /api/users/:id/status` - Changement statut (actif/inactif)

### Content Routes (/api/*)
**Announcements :**
- `GET /api/announcements` - Liste publique
- `GET /api/announcements/:id` - Détail annonce
- `POST /api/announcements` - Création (admin/moderator)
- `PUT /api/announcements/:id` - Modification
- `DELETE /api/announcements/:id` - Suppression

**Documents :**
- `GET /api/documents` - Bibliothèque de documents
- `GET /api/documents/:id` - Téléchargement document
- `POST /api/documents` - Upload document
- `PUT /api/documents/:id` - Mise à jour métadonnées
- `DELETE /api/documents/:id` - Suppression

**Events :**
- `GET /api/events` - Calendrier des événements
- `POST /api/events` - Création événement
- `PUT /api/events/:id` - Modification
- `DELETE /api/events/:id` - Suppression

### Messaging Routes (/api/*)
**Messages internes :**
- `GET /api/messages` - Boîte de réception
- `GET /api/messages/:userId` - Conversation avec utilisateur
- `POST /api/messages` - Envoi message
- `PATCH /api/messages/:id/read` - Marquer comme lu
- `DELETE /api/messages/:id` - Suppression

**Complaints :**
- `GET /api/complaints` - Liste des réclamations
- `GET /api/complaints/:id` - Détail réclamation
- `POST /api/complaints` - Nouvelle réclamation
- `PUT /api/complaints/:id` - Mise à jour statut
- `POST /api/complaints/:id/assign` - Assignation
- `DELETE /api/complaints/:id` - Suppression

### Training Routes (/api/*)
**Formations présentielles :**
- `GET /api/trainings` - Catalogue formations
- `GET /api/trainings/:id` - Détail formation
- `POST /api/trainings` - Création formation (admin)
- `PUT /api/trainings/:id` - Modification
- `DELETE /api/trainings/:id` - Suppression
- `POST /api/trainings/:id/participants` - Inscription
- `DELETE /api/trainings/:trainingId/participants/:userId` - Désinscription

**E-Learning :**
- `GET /api/courses` - Catalogue cours en ligne
- `GET /api/courses/:id` - Détail cours
- `POST /api/courses` - Création cours
- `GET /api/courses/:id/lessons` - Leçons du cours
- `POST /api/courses/:id/lessons` - Ajout leçon
- `GET /api/lessons/:id` - Contenu leçon
- `POST /api/lessons/:id/complete` - Marquer terminé
- `GET /api/my-enrollments` - Mes inscriptions
- `POST /api/enroll/:courseId` - Inscription cours
- `GET /api/my-certificates` - Mes certificats
- `GET /api/resources` - Ressources téléchargeables

### Admin Routes (/api/*)
**Gestion permissions :**
- `GET /api/permissions` - Toutes les permissions
- `GET /api/permissions/:userId` - Permissions utilisateur
- `POST /api/permissions` - Octroi permission
- `DELETE /api/permissions/:id` - Révocation permission
- `POST /api/admin/bulk-permissions` - Permissions en lot
- `GET /api/admin/permission-check/:userId/:permission` - Vérification

**Analytics :**
- `GET /api/admin/analytics/overview` - Vue d'ensemble
- `GET /api/admin/analytics/users` - Statistiques utilisateurs
- `GET /api/admin/analytics/content` - Statistiques contenu
- `POST /api/admin/reset-test-data` - Reset données test

**Configuration :**
- `GET /api/system-settings` - Paramètres système
- `PUT /api/system-settings` - Mise à jour paramètres
- `GET /api/employee-categories` - Catégories employés
- `POST /api/employee-categories` - Nouvelle catégorie

### Stats et Monitoring
- `GET /api/stats` - Statistiques globales :
  - Nombre d'utilisateurs, annonces, documents, événements
  - Messages, réclamations, nouvelles annonces
  - Documents mis à jour, utilisateurs connectés
  - Réclamations en attente

## INTERFACE DE STOCKAGE

### IStorage Interface (storage.ts)
**Interface abstraite avec 50+ méthodes :**

#### Gestion Utilisateurs (6 méthodes)
- `getUser(id)`, `getUserByUsername(username)`, `getUserByEmployeeId(employeeId)`
- `createUser(user)`, `updateUser(id, user)`, `getUsers()`

#### Gestion Contenu (15 méthodes)
- **Announcements** : get, getById, create, update, delete
- **Documents** : get, getById, create, update, delete  
- **Events** : get, getById, create, update, delete

#### Communication (11 méthodes)
- **Messages** : get, getById, create, markAsRead, delete
- **Complaints** : get, getById, getByUser, create, update, delete

#### Permissions (4 méthodes)
- `getPermissions(userId)`, `createPermission()`, `revokePermission()`, `hasPermission()`

#### Formation (15 méthodes)
- **Trainings** : get, getById, create, update, delete
- **Participants** : getParticipants, getUserParticipations, add, update, remove
- **Courses** : get, getById, create, update, delete
- **Lessons** : get, getById, create, update, delete

#### E-Learning (10 méthodes)
- **Enrollments** : get, create, update
- **Progress** : getProgress, updateProgress
- **Certificates** : get, issue
- **Resources** : get, getById, create, delete

#### Système (3 méthodes)
- `getStats()` - Statistiques complètes
- `resetToTestData()` - Reset données test
- `getSystemSettings()`, `updateSystemSettings()`

## SÉCURITÉ ET AUTHENTIFICATION

### Authentification
- **Sessions Express** avec store PostgreSQL
- **Cookies sécurisés** avec HttpOnly, Secure, SameSite
- **Timeout session** automatique
- **Rate limiting** sur routes sensibles

### Autorisation
- **Contrôle par rôles** : admin > moderator > employee
- **Permissions granulaires** : 63 permissions définies
- **Middleware de vérification** sur toutes les routes protégées
- **Validation ownership** (utilisateur ne peut modifier que ses données)

### Sécurité des Données
- **Validation Zod** sur toutes les entrées
- **Sanitisation** automatique des chaînes
- **Protection SQL injection** via Drizzle ORM
- **Hash bcrypt** avec 12 rounds pour mots de passe
- **Headers de sécurité** via Helmet

### Monitoring et Logs
- **Logging structuré** de toutes les requêtes API
- **Capture des erreurs** avec stack traces
- **Métriques de performance** (temps de réponse)
- **Rate limiting** avec compteurs par IP

## CONFIGURATION ET DÉPLOIEMENT

### Variables d'Environnement
- `DATABASE_URL` - Connexion Neon PostgreSQL
- `NODE_ENV` - Environment (development/production)
- `PORT` - Port serveur (défaut 5000)
- `SESSION_SECRET` - Secret pour sessions
- `REPL_ID` - Identifiant Replit (dev uniquement)

### Configuration de Production
- **Trust proxy** désactivé en production
- **HTTPS uniquement** pour cookies sécurisés
- **Rate limiting** plus strict
- **Compression gzip** activée
- **Assets statiques** servis par Express

### Base de Données
- **Neon PostgreSQL** serverless
- **WebSocket** pour connexions temps réel
- **Pool de connexions** géré automatiquement
- **Migrations** via Drizzle Kit
- **Schema versioning** avec timestamps

## INTÉGRATIONS ET SERVICES EXTERNES

### Services Email
- **Nodemailer** pour envoi d'emails
- **Templates HTML** pour notifications
- **Support SMTP** configurable
- **Emails de bienvenue** automatiques

### Traduction
- **LibreTranslate** pour traduction automatique
- **Support multilingue** des contenus
- **Interface française** par défaut

### Monitoring
- **Memoization** pour cache en mémoire
- **MemoryStore** pour sessions courtes
- **Performance tracking** des requêtes

## ARCHITECTURE DE DONNÉES

### Patterns utilisés
- **Repository Pattern** via IStorage
- **Dependency Injection** pour services
- **Middleware Pipeline** pour Express
- **Transaction Management** avec Drizzle
- **Schema Validation** avec Zod

### Performance
- **Requêtes optimisées** avec Drizzle
- **Index automatiques** sur clés primaires/étrangères
- **Pagination** sur listes longues
- **Cache en mémoire** pour données fréquentes
- **Connection pooling** PostgreSQL

### Extensibilité
- **Interface abstraite** pour storage
- **Plugins middleware** modulaires
- **Routes modulaires** par domaine
- **Services découplés**
- **Configuration externalisée**

## COMPATIBILITÉ ET MIGRATION

### Support Legacy
- **Conversion PHP** - Utilitaires de migration inclus
- **APIs compatibles** avec ancien système
- **Validation universelle** partagée
- **Constantes communes** entre versions

### Versions Supportées
- **Node.js** : 18+ minimum
- **PostgreSQL** : 13+ via Neon
- **TypeScript** : 5.x
- **Express** : 4.21+

### Migration Path
- **Scripts de migration** automatiques
- **Conversion de données** depuis autres systèmes
- **Validation de cohérence** post-migration
- **Rollback** en cas d'échec

Cette analyse révèle un backend robuste et sécurisé avec une architecture moderne, une couverture fonctionnelle complète et des pratiques de sécurité avancées, prêt pour un environnement de production d'entreprise.