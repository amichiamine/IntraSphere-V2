# INVENTAIRE EXHAUSTIF BACKEND - IntraSphere Enterprise

## 🏗️ STRUCTURE GÉNÉRALE BACKEND

### 📁 Structure des Dossiers
```
server/
├── index.ts                      # Point d'entrée principal du serveur
├── config.ts                     # Configuration générale du serveur
├── db.ts                         # Configuration de la base de données
├── migrations.ts                 # Migrations et sécurité des données
├── testData.ts                   # Données de test et d'initialisation
├── vite.ts                       # Configuration Vite pour le serveur
├── data/                         # Couche d'accès aux données
├── routes/                       # Routes API et logique métier
├── services/                     # Services métier
├── middleware/                   # Middlewares de sécurité
└── public/                       # Fichiers statiques générés
```

## 🎯 COUCHE D'ACCÈS AUX DONNÉES (server/data/)

### 🗃️ Storage Interface (data/storage.ts)
**Interface IStorage** - Contrat d'accès aux données
- **Méthodes Utilisateurs** : 15 méthodes
- **Méthodes Annonces** : 5 méthodes  
- **Méthodes Documents** : 5 méthodes
- **Méthodes Événements** : 5 méthodes
- **Méthodes Messages** : 6 méthodes
- **Méthodes Réclamations** : 7 méthodes
- **Méthodes Permissions** : 4 méthodes
- **Méthodes Formations** : 8 méthodes
- **Méthodes Forum** : 12 méthodes
- **Total** : 67 méthodes CRUD

### 🏪 Implémentation MemStorage
**Base de données en mémoire** pour développement et tests :
1. **Map stockage** - Structures de données en mémoire
2. **Génération d'IDs** - UUIDs automatiques
3. **Relations** - Gestion des clés étrangères
4. **Filtrage** - Recherche et tri
5. **Validation** - Contrôles d'intégrité

## 🛠️ ROUTES API (server/routes/api.ts)

### 🔐 Routes d'Authentification
1. `POST /api/auth/login` - Connexion utilisateur
2. `POST /api/auth/register` - Inscription (admin only)
3. `GET /api/auth/me` - Profil utilisateur actuel
4. `POST /api/auth/logout` - Déconnexion

### 👥 Routes Utilisateurs
1. `GET /api/users` - Liste des utilisateurs
2. `GET /api/users/:id` - Détail utilisateur
3. `PUT /api/users/:id` - Modification utilisateur
4. `DELETE /api/users/:id` - Suppression utilisateur
5. `PUT /api/users/:id/status` - Activation/Désactivation
6. `PUT /api/users/:id/password` - Changement mot de passe

### 📢 Routes Annonces
1. `GET /api/announcements` - Liste des annonces
2. `GET /api/announcements/:id` - Détail annonce
3. `POST /api/announcements` - Création annonce
4. `PUT /api/announcements/:id` - Modification annonce
5. `DELETE /api/announcements/:id` - Suppression annonce

### 📄 Routes Documents
1. `GET /api/documents` - Liste des documents
2. `GET /api/documents/:id` - Détail document
3. `POST /api/documents` - Upload document
4. `PUT /api/documents/:id` - Modification document
5. `DELETE /api/documents/:id` - Suppression document

### 📅 Routes Événements
1. `GET /api/events` - Liste des événements
2. `GET /api/events/:id` - Détail événement
3. `POST /api/events` - Création événement
4. `PUT /api/events/:id` - Modification événement
5. `DELETE /api/events/:id` - Suppression événement

### 💌 Routes Messages
1. `GET /api/messages` - Messagerie inbox
2. `GET /api/messages/sent` - Messages envoyés
3. `GET /api/messages/:id` - Détail message
4. `POST /api/messages` - Envoi message
5. `PUT /api/messages/:id/read` - Marquer comme lu
6. `DELETE /api/messages/:id` - Suppression message

### 🎫 Routes Réclamations
1. `GET /api/complaints` - Liste réclamations
2. `GET /api/complaints/:id` - Détail réclamation
3. `POST /api/complaints` - Nouvelle réclamation
4. `PUT /api/complaints/:id` - Modification réclamation
5. `PUT /api/complaints/:id/assign` - Assigner réclamation
6. `PUT /api/complaints/:id/status` - Changer statut
7. `DELETE /api/complaints/:id` - Suppression réclamation

### 🔑 Routes Permissions
1. `GET /api/permissions/user/:id` - Permissions utilisateur
2. `POST /api/permissions` - Accorder permission
3. `DELETE /api/permissions/:id` - Révoquer permission
4. `GET /api/permissions/check/:userId/:permission` - Vérifier permission

### 🎓 Routes Formations
1. `GET /api/trainings` - Liste formations
2. `GET /api/trainings/:id` - Détail formation
3. `POST /api/trainings` - Création formation
4. `PUT /api/trainings/:id` - Modification formation
5. `DELETE /api/trainings/:id` - Suppression formation
6. `POST /api/trainings/:id/register` - Inscription formation
7. `DELETE /api/trainings/:id/unregister` - Désinscription
8. `PUT /api/trainings/:id/complete` - Compléter formation

### 💬 Routes Forum
1. `GET /api/forum/categories` - Catégories forum
2. `POST /api/forum/categories` - Créer catégorie
3. `GET /api/forum/topics` - Liste sujets
4. `GET /api/forum/topics/:id` - Détail sujet
5. `POST /api/forum/topics` - Créer sujet
6. `PUT /api/forum/topics/:id` - Modifier sujet
7. `DELETE /api/forum/topics/:id` - Supprimer sujet
8. `GET /api/forum/topics/:id/posts` - Posts d'un sujet
9. `POST /api/forum/posts` - Créer post
10. `PUT /api/forum/posts/:id` - Modifier post
11. `DELETE /api/forum/posts/:id` - Supprimer post
12. `POST /api/forum/posts/:id/like` - Liker post

### 📊 Routes Statistiques
1. `GET /api/stats` - Statistiques générales
2. `GET /api/stats/users` - Stats utilisateurs
3. `GET /api/stats/content` - Stats contenu
4. `GET /api/stats/activity` - Stats activité

## 🔧 SERVICES MÉTIER (server/services/)

### 🔐 Service d'Authentification (services/auth.ts)
**Classe AuthService** - Gestion sécurisée des mots de passe :
1. `hashPassword(password: string)` - Hachage bcrypt
2. `verifyPassword(password: string, hash: string)` - Vérification
3. `generateSecurePassword()` - Génération sécurisée
4. **Configuration bcrypt** - Rounds : 12

### 📧 Service Email (services/email.ts)
**Classe EmailService** - Notifications par email :
1. `sendWelcomeEmail(to, name)` - Email de bienvenue
2. `sendResetPasswordEmail(to, name, resetLink)` - Reset password
3. `sendNotification(to, subject, content)` - Notifications génériques
4. **Configuration Nodemailer** - SMTP configurable
5. **Templates HTML** - Emails stylisés

## 🛡️ MIDDLEWARE DE SÉCURITÉ (server/middleware/)

### 🔒 Sécurité Principal (middleware/security.ts)
**Configuration sécuritaire complète** :
1. **Helmet.js** - Protection en-têtes HTTP
2. **Rate Limiting** - Limitation des requêtes
3. **CORS Configuration** - Politique d'origine
4. **Session Security** - Configuration sessions sécurisées
5. **Input Sanitization** - Nettoyage des entrées
6. **CSRF Protection** - Protection contre attaques
7. **Content Security Policy** - Politique de contenu

### 🔐 Middlewares d'Authentification
1. `requireAuth` - Vérification session
2. `requireRole(roles)` - Contrôle des rôles
3. `checkPermission(permission)` - Vérification permissions

## 🗄️ SCHÉMA DE BASE DE DONNÉES (shared/schema.ts)

### 👥 Table Users
**Champs (14)** :
- `id` (UUID, PK)
- `username` (Unique)
- `password` (Hashed)
- `name`, `role`, `avatar`
- `employeeId` (Unique)
- `department`, `position`
- `isActive` (Boolean)
- `phone`, `email`
- `createdAt`, `updatedAt`

### 📢 Table Announcements
**Champs (9)** :
- `id` (UUID, PK)
- `title`, `content`, `type`
- `authorId` (FK Users), `authorName`
- `imageUrl`, `icon`
- `createdAt`, `isImportant`

### 📄 Table Documents
**Champs (7)** :
- `id` (UUID, PK)
- `title`, `description`, `category`
- `fileName`, `fileUrl`
- `updatedAt`, `version`

### 📅 Table Events
**Champs (7)** :
- `id` (UUID, PK)
- `title`, `description`, `date`
- `location`, `type`
- `organizerId` (FK Users), `createdAt`

### 💌 Table Messages
**Champs (7)** :
- `id` (UUID, PK)
- `senderId` (FK Users)
- `recipientId` (FK Users)
- `subject`, `content`
- `isRead`, `createdAt`

### 🎫 Table Complaints
**Champs (9)** :
- `id` (UUID, PK)
- `submitterId` (FK Users)
- `assignedToId` (FK Users)
- `title`, `description`, `category`
- `priority`, `status`
- `createdAt`, `updatedAt`

### 🔑 Table Permissions
**Champs (5)** :
- `id` (UUID, PK)
- `userId` (FK Users)
- `grantedBy` (FK Users)
- `permission`, `createdAt`

### 🎓 Table Trainings
**Champs (16)** :
- `id` (UUID, PK)
- `title`, `description`, `category`
- `difficulty`, `duration`
- `instructorId` (FK Users), `instructorName`
- `startDate`, `endDate`, `location`
- `maxParticipants`, `currentParticipants`
- `isMandatory`, `isActive`, `isVisible`
- `thumbnailUrl`, `documentUrls` (Array)
- `createdAt`, `updatedAt`

### 🎓 Table TrainingParticipants
**Champs (7)** :
- `id` (UUID, PK)
- `trainingId` (FK Trainings)
- `userId` (FK Users)
- `registeredAt`, `status`
- `completionDate`, `score`

### 💬 Tables Forum (4 tables)
**ForumCategories (5 champs)** :
- `id`, `name`, `description`, `color`, `createdAt`

**ForumTopics (11 champs)** :
- `id`, `title`, `content`, `categoryId`
- `authorId`, `authorName`, `createdAt`
- `isLocked`, `isPinned`, `lastPostAt`, `postCount`

**ForumPosts (7 champs)** :
- `id`, `content`, `topicId`
- `authorId`, `authorName`
- `createdAt`, `updatedAt`

**ForumLikes (4 champs)** :
- `id`, `postId`, `userId`, `createdAt`

### 📊 Schémas Zod
**Total : 15 schémas de validation**
1. `insertUserSchema` + types
2. `insertAnnouncementSchema` + types
3. `insertDocumentSchema` + types
4. `insertEventSchema` + types
5. `insertMessageSchema` + types
6. `insertComplaintSchema` + types
7. `insertPermissionSchema` + types
8. `insertTrainingSchema` + types
9. `insertTrainingParticipantSchema` + types
10. `insertForumCategorySchema` + types
11. `insertForumTopicSchema` + types
12. `insertForumPostSchema` + types
13. `insertForumLikeSchema` + types
14. `insertContentSchema` + types
15. `insertCategorySchema` + types

## ⚙️ CONFIGURATION SERVEUR

### 🚀 Point d'Entrée (server/index.ts)
**Configuration Express complète** :
1. **Trust Proxy** - Configuration selon environnement
2. **Middleware Stack** - Sécurité, parsing, sessions
3. **Logging** - Requêtes API avec durée
4. **Error Handling** - Gestion d'erreurs globale
5. **Vite Integration** - Développement uniquement
6. **Static Serving** - Production
7. **Port Configuration** - 5000 par défaut

### 🔧 Configuration Générale (server/config.ts)
**Variables d'environnement et constantes** :
1. **Database URL** - PostgreSQL/Neon
2. **Session Secret** - Clé de chiffrement
3. **Email Configuration** - SMTP settings
4. **Security Settings** - Rate limits, CORS
5. **File Upload** - Tailles et types autorisés

### 🗃️ Configuration BDD (server/db.ts)
**Connexion Drizzle ORM** :
1. **Neon Serverless** - PostgreSQL cloud
2. **Connection Pool** - Gestion des connexions
3. **SSL Configuration** - Sécurité transport
4. **Logging** - Requêtes SQL en développement

### 📦 Configuration Vite (server/vite.ts)
**Intégration développement** :
1. **Dev Server** - Serveur de développement
2. **Middleware** - Intégration Express
3. **Static Serving** - Fichiers statiques
4. **HMR** - Hot Module Replacement

## 🔄 MIGRATIONS ET DONNÉES (server/migrations.ts)

### 🔒 Migration de Sécurité
**Mise à jour des mots de passe** :
1. **Détection** - Mots de passe non hachés
2. **Hachage bcrypt** - Conversion sécurisée
3. **Sauvegarde** - Préservation des données
4. **Validation** - Vérification post-migration
5. **Logging** - Traçabilité des opérations

### 🧪 Données de Test (server/testData.ts)
**Jeu de données initial** :
1. **Utilisateurs par défaut** - Admin, employés types
2. **Annonces exemples** - Contenus de démonstration
3. **Documents types** - Politiques, procédures
4. **Événements** - Réunions, formations
5. **Structure complète** - Tous les types de données

## 🔐 SÉCURITÉ BACKEND

### 🛡️ Mesures de Protection
1. **Hachage bcrypt** - Mots de passe (rounds: 12)
2. **Sessions sécurisées** - HttpOnly, Secure cookies
3. **Rate Limiting** - Protection DoS
4. **Input Sanitization** - Prévention injections
5. **CSRF Protection** - Tokens de validation
6. **Headers Security** - Helmet.js
7. **SQL Injection** - ORM avec requêtes préparées
8. **XSS Protection** - Sanitisation et CSP

### 🔑 Contrôle d'Accès
1. **Authentication** - Sessions utilisateur
2. **Authorization** - Contrôle par rôles
3. **Permissions** - Système granulaire
4. **Role-based** - Admin, Modérateur, Employé
5. **Resource-based** - Propriétaire de ressource

## 📊 ANALYTICS ET MONITORING

### 📈 Métriques Collectées
1. **Requêtes API** - Durée, statut, fréquence
2. **Utilisateurs** - Connexions, activité
3. **Contenu** - Vues, interactions
4. **Erreurs** - Tracking et logging
5. **Performance** - Temps de réponse

### 🎯 Endpoints de Statistiques
1. **Stats générales** - Vue d'ensemble
2. **Stats utilisateurs** - Activité, rôles
3. **Stats contenu** - Publications, vues
4. **Stats système** - Performance, erreurs

## 🌐 INTÉGRATIONS EXTERNES

### 📧 Service Email
1. **Nodemailer** - Client SMTP
2. **Templates HTML** - Emails stylisés
3. **Configuration** - Variables environnement
4. **Types d'emails** - Bienvenue, notifications, reset

### 🔄 Services Tiers
1. **LibreTranslate** - Service de traduction
2. **File Storage** - Gestion des uploads
3. **External APIs** - Intégrations futures

## 🔧 TECHNOLOGIES BACKEND

### 🚀 Runtime et Framework
1. **Node.js** - Runtime JavaScript
2. **Express.js** - Framework web
3. **TypeScript** - Typage statique
4. **ES Modules** - Système de modules moderne

### 🗄️ Base de Données
1. **PostgreSQL** - Base de données principale
2. **Neon Serverless** - Cloud PostgreSQL
3. **Drizzle ORM** - ORM type-safe
4. **Drizzle Kit** - Outils de migration

### 🔐 Sécurité
1. **bcrypt** - Hachage mots de passe
2. **express-session** - Gestion sessions
3. **helmet** - Sécurité HTTP headers
4. **express-rate-limit** - Rate limiting
5. **connect-pg-simple** - Session store PostgreSQL

### 📧 Communication
1. **nodemailer** - Client email
2. **libretranslate** - Traduction
3. **ws** - WebSockets (futur)

### 🔧 Outils de Développement
1. **tsx** - Exécution TypeScript
2. **esbuild** - Bundler production
3. **drizzle-kit** - Migrations BDD
4. **zod** - Validation schémas

## 📁 FICHIERS STATIQUES (server/public/)

### 🏗️ Build Production
1. **index.html** - Point d'entrée SPA
2. **assets/** - CSS et JS compilés
3. **Optimisation** - Compression, minification
4. **Cache** - Headers de cache appropriés

## ⚡ PERFORMANCE

### 🚀 Optimisations
1. **Connection Pooling** - Gestion des connexions BDD
2. **Query Optimization** - Requêtes Drizzle efficaces
3. **Memory Storage** - Cache en mémoire (dev)
4. **Compression** - Réponses compressées
5. **Static Caching** - Cache des fichiers statiques

### 📊 Monitoring
1. **Request Logging** - Durée et statut
2. **Error Tracking** - Logging des erreurs
3. **Performance Metrics** - Temps de réponse
4. **Database Monitoring** - Requêtes lentes

## 🔄 PROCESSUS DE DÉPLOIEMENT

### 🏗️ Build Process
1. **Frontend Build** - Vite compilation
2. **Backend Build** - esbuild bundling
3. **Database Push** - Drizzle migrations
4. **Static Assets** - Optimisation

### 🌐 Production Ready
1. **Environment Variables** - Configuration production
2. **Security Headers** - Production security
3. **SSL/TLS** - Transport sécurisé
4. **Process Management** - PM2 ou similaire

---
*Inventaire généré le : 08/01/2025*
*Version : Backend v1.0 - IntraSphere Enterprise*