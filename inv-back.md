# INVENTAIRE BACKEND - INTRASPHERE

## 📁 STRUCTURE GENERALE DU BACKEND

### Répertoire racine server/
```
server/
├── index.ts           # Point d'entrée principal du serveur
├── config.ts          # Configuration du serveur
├── db.ts              # Configuration base de données PostgreSQL
├── vite.ts            # Configuration Vite pour le serveur
├── migrations.ts      # Migrations et setup initial
├── testData.ts        # Données de test
├── data/              # Couche de données et stockage
├── routes/            # Routes API REST
├── services/          # Services métier
├── middleware/        # Middleware Express
└── public/            # Fichiers publics statiques
```

## 🚀 SERVEUR PRINCIPAL - server/index.ts

### Configuration Express
- **Framework** : Express.js avec TypeScript
- **Port** : 5000 (développement)
- **Middleware de sécurité** : Helmet pour les en-têtes sécurisés
- **Parsing** : JSON et URL-encoded
- **Sessions** : Express-session avec PostgreSQL store
- **Rate limiting** : Protection contre les attaques DoS

### Middleware Pipeline
1. **Logging personnalisé** - Capture des requêtes API avec temps de réponse
2. **Helmet** - Sécurisation des en-têtes HTTP
3. **JSON/URL parsing** - Traitement des corps de requête
4. **Session management** - Gestion des sessions utilisateur
5. **Rate limiting** - Limitation du nombre de requêtes
6. **Vite middleware** - Serveur de développement intégré

### Services initiaux
- **Migrations automatiques** - Migration des mots de passe au démarrage
- **Données de test** - Initialisation optionnelle des données de démonstration

## 🗄️ SCHÉMA DE BASE DE DONNÉES - shared/schema.ts

### Tables principales (13 tables)

#### Gestion des utilisateurs
**users** - Table des utilisateurs
- `id` (UUID, PK) - Identifiant unique
- `username` (TEXT, UNIQUE) - Nom d'utilisateur
- `password` (TEXT) - Mot de passe hashé
- `name` (TEXT) - Nom complet
- `role` (TEXT) - Rôle (employee, admin, moderator)
- `avatar` (TEXT) - URL de l'avatar
- `employeeId` (VARCHAR, UNIQUE) - Identifiant employé
- `department` (VARCHAR) - Département
- `position` (VARCHAR) - Poste
- `isActive` (BOOLEAN) - Statut actif
- `phone` (VARCHAR) - Téléphone
- `email` (VARCHAR) - Email
- `createdAt/updatedAt` (TIMESTAMP) - Horodatage

#### Système de contenu
**announcements** - Annonces
- `id` (UUID, PK) - Identifiant
- `title` (TEXT) - Titre
- `content` (TEXT) - Contenu
- `type` (TEXT) - Type (info, important, event, formation)
- `authorId/authorName` - Auteur
- `imageUrl` (TEXT) - URL image
- `icon` (TEXT) - Icône
- `isImportant` (BOOLEAN) - Priorité
- `createdAt` (TIMESTAMP) - Date création

**documents** - Documents
- `id` (UUID, PK) - Identifiant
- `title` (TEXT) - Titre
- `description` (TEXT) - Description
- `category` (TEXT) - Catégorie (regulation, policy, guide, procedure)
- `fileName/fileUrl` (TEXT) - Fichier
- `version` (TEXT) - Version
- `updatedAt` (TIMESTAMP) - Dernière mise à jour

**contents** - Contenu multimédia
- `id` (UUID, PK) - Identifiant
- `title` (TEXT) - Titre
- `type` (TEXT) - Type (video, image, document, audio)
- `category` (TEXT) - Catégorie
- `description` (TEXT) - Description
- `fileUrl/thumbnailUrl` (TEXT) - URLs
- `duration` (TEXT) - Durée
- `viewCount/rating` (INTEGER) - Métriques
- `tags` (TEXT[]) - Tags
- `isPopular/isFeatured` (BOOLEAN) - Statut

**categories** - Catégories de contenu
- `id` (UUID, PK) - Identifiant
- `name` (TEXT, UNIQUE) - Nom
- `description` (TEXT) - Description
- `icon` (TEXT) - Icône
- `color` (TEXT) - Couleur
- `isVisible` (BOOLEAN) - Visibilité
- `sortOrder` (INTEGER) - Ordre de tri
- `createdAt` (TIMESTAMP) - Date création

#### Système de communication
**messages** - Messages privés
- `id` (UUID, PK) - Identifiant
- `senderId/recipientId` (UUID, FK) - Expéditeur/Destinataire
- `subject` (TEXT) - Sujet
- `content` (TEXT) - Contenu
- `isRead` (BOOLEAN) - Lu/Non lu
- `createdAt` (TIMESTAMP) - Date création

**complaints** - Réclamations
- `id` (UUID, PK) - Identifiant
- `submitterId/assignedToId` (UUID, FK) - Soumetteur/Assigné
- `title` (TEXT) - Titre
- `description` (TEXT) - Description
- `category` (TEXT) - Catégorie (hr, it, facilities, other)
- `priority` (TEXT) - Priorité (low, medium, high, urgent)
- `status` (TEXT) - Statut (open, in_progress, resolved, closed)
- `createdAt/updatedAt` (TIMESTAMP) - Horodatage

#### Système de formation
**trainings** - Formations
- `id` (UUID, PK) - Identifiant
- `title` (TEXT) - Titre
- `description` (TEXT) - Description
- `category` (TEXT) - Catégorie (technical, management, safety, compliance, other)
- `difficulty` (TEXT) - Difficulté (beginner, intermediate, advanced)
- `duration` (INTEGER) - Durée en minutes
- `instructorId/instructorName` - Instructeur
- `startDate/endDate` (TIMESTAMP) - Dates
- `location` (TEXT) - Lieu
- `maxParticipants/currentParticipants` (INTEGER) - Participants
- `isMandatory/isActive/isVisible` (BOOLEAN) - Statuts
- `thumbnailUrl` (TEXT) - Miniature
- `documentUrls` (TEXT[]) - Documents
- `createdAt/updatedAt` (TIMESTAMP) - Horodatage

**trainingParticipants** - Participants aux formations
- `id` (UUID, PK) - Identifiant
- `trainingId/userId` (UUID, FK) - Formation/Utilisateur
- `registeredAt` (TIMESTAMP) - Date d'inscription
- `status` (TEXT) - Statut (registered, completed, cancelled)
- `completionDate` (TIMESTAMP) - Date de completion
- `score` (INTEGER) - Score (0-100)
- `feedback` (TEXT) - Commentaires

#### Système de permissions
**permissions** - Délégations de permissions
- `id` (UUID, PK) - Identifiant
- `userId/grantedBy` (UUID, FK) - Utilisateur/Accordé par
- `permission` (TEXT) - Permission accordée
- `createdAt` (TIMESTAMP) - Date création

#### Configuration système
**employeeCategories** - Catégories d'employés
- `id` (UUID, PK) - Identifiant
- `name` (TEXT, UNIQUE) - Nom
- `description` (TEXT) - Description
- `color` (TEXT) - Couleur
- `permissions` (TEXT[]) - Permissions
- `isActive` (BOOLEAN) - Statut actif
- `createdAt` (TIMESTAMP) - Date création

**systemSettings** - Paramètres système
- `id` (VARCHAR, PK) - "settings" (singleton)
- `showAnnouncements/showContent/showDocuments` (BOOLEAN) - Visibilité des modules
- `showForum/showMessages/showComplaints` (BOOLEAN) - Visibilité communication
- `showTraining` (BOOLEAN) - Visibilité formation
- `updatedAt` (TIMESTAMP) - Dernière mise à jour

**events** - Événements
- `id` (UUID, PK) - Identifiant
- `title` (TEXT) - Titre
- `description` (TEXT) - Description
- `date` (TIMESTAMP) - Date
- `location` (TEXT) - Lieu
- `type` (TEXT) - Type (meeting, training, social, other)
- `organizerId` (UUID, FK) - Organisateur
- `createdAt` (TIMESTAMP) - Date création

### Schémas de validation Zod (15 schémas)
- `insertUserSchema` - Validation utilisateur
- `insertAnnouncementSchema` - Validation annonce
- `insertDocumentSchema` - Validation document
- `insertEventSchema` - Validation événement
- `insertMessageSchema` - Validation message
- `insertComplaintSchema` - Validation réclamation
- `insertPermissionSchema` - Validation permission
- `insertContentSchema` - Validation contenu
- `insertCategorySchema` - Validation catégorie
- `insertEmployeeCategorySchema` - Validation catégorie employé
- `insertSystemSettingsSchema` - Validation paramètres
- `insertTrainingSchema` - Validation formation
- `insertTrainingParticipantSchema` - Validation participant

### Types TypeScript (13+ types)
- Types de sélection pour chaque table (`User`, `Announcement`, etc.)
- Types d'insertion pour chaque table (`InsertUser`, `InsertAnnouncement`, etc.)

## 🔄 ROUTES API - server/routes/api.ts

### Authentification (4 routes)
- `POST /api/auth/login` - Connexion utilisateur
- `POST /api/auth/register` - Inscription utilisateur
- `GET /api/auth/me` - Informations utilisateur actuel
- `POST /api/auth/logout` - Déconnexion

### Statistiques
- `GET /api/stats` - Statistiques du tableau de bord

### Annonces (3 routes)
- `GET /api/announcements` - Liste des annonces
- `GET /api/announcements/:id` - Annonce par ID
- `POST /api/announcements` - Création d'annonce

### Documents (5 routes CRUD complètes)
- `GET /api/documents` - Liste des documents
- `GET /api/documents/:id` - Document par ID
- `POST /api/documents` - Création de document
- `PATCH /api/documents/:id` - Mise à jour de document
- `DELETE /api/documents/:id` - Suppression de document

### Événements (3 routes)
- `GET /api/events` - Liste des événements
- `GET /api/events/:id` - Événement par ID
- `POST /api/events` - Création d'événement

### Utilisateurs (Administration - 6 routes)
- `GET /api/users` - Liste des utilisateurs
- `POST /api/users` - Création d'utilisateur
- `PATCH /api/users/:id` - Mise à jour utilisateur
- `DELETE /api/users/:id` - Suppression utilisateur
- `POST /api/users/:id/toggle-active` - Activer/Désactiver
- `GET /api/users/search` - Recherche d'utilisateurs

### Contenu multimédia (5 routes CRUD)
- `GET /api/content` - Liste du contenu
- `GET /api/content/:id` - Contenu par ID
- `POST /api/content` - Création de contenu
- `PATCH /api/content/:id` - Mise à jour de contenu
- `DELETE /api/content/:id` - Suppression de contenu

### Catégories (5 routes CRUD)
- `GET /api/categories` - Liste des catégories
- `GET /api/categories/:id` - Catégorie par ID
- `POST /api/categories` - Création de catégorie
- `PATCH /api/categories/:id` - Mise à jour de catégorie
- `DELETE /api/categories/:id` - Suppression de catégorie

### Messages privés (4 routes)
- `GET /api/messages` - Messages de l'utilisateur
- `GET /api/messages/:id` - Message par ID
- `POST /api/messages` - Envoi de message
- `PATCH /api/messages/:id/read` - Marquer comme lu

### Réclamations (6 routes)
- `GET /api/complaints` - Liste des réclamations
- `GET /api/complaints/:id` - Réclamation par ID
- `POST /api/complaints` - Création de réclamation
- `PATCH /api/complaints/:id` - Mise à jour de réclamation
- `GET /api/complaints/user/:userId` - Réclamations par utilisateur
- `PATCH /api/complaints/:id/assign` - Assignation de réclamation

### Permissions (4 routes)
- `GET /api/permissions/:userId` - Permissions utilisateur
- `POST /api/permissions` - Accorder permission
- `DELETE /api/permissions/:id` - Révoquer permission
- `GET /api/permissions/:userId/:permission` - Vérifier permission

### Catégories d'employés (5 routes CRUD)
- `GET /api/employee-categories` - Liste des catégories
- `POST /api/employee-categories` - Création de catégorie
- `PATCH /api/employee-categories/:id` - Mise à jour
- `DELETE /api/employee-categories/:id` - Suppression
- `GET /api/employee-categories/:id` - Catégorie par ID

### Paramètres système (2 routes)
- `GET /api/system-settings` - Paramètres actuels
- `PATCH /api/system-settings` - Mise à jour paramètres

### Formations (5 routes CRUD)
- `GET /api/trainings` - Liste des formations
- `GET /api/trainings/:id` - Formation par ID
- `POST /api/trainings` - Création de formation
- `PATCH /api/trainings/:id` - Mise à jour de formation
- `DELETE /api/trainings/:id` - Suppression de formation

### Participants aux formations (5 routes)
- `GET /api/trainings/:id/participants` - Participants d'une formation
- `POST /api/trainings/:id/participants` - Inscription à une formation
- `DELETE /api/trainings/:trainingId/participants/:userId` - Désinscription
- `GET /api/users/:userId/trainings` - Formations d'un utilisateur
- `PATCH /api/training-participants/:id` - Mise à jour participation

## 🛠️ MIDDLEWARE - server/middleware/

### Sécurité (server/middleware/security.ts)
- **Rate limiting** - Protection contre les attaques par déni de service
- **Headers sécurisés** - Configuration Helmet
- **CORS** - Configuration des origines autorisées
- **Validation des entrées** - Nettoyage des données

## 🔧 SERVICES - server/services/

### Service d'authentification (server/services/auth.ts)
- **AuthService.hashPassword()** - Hachage bcrypt des mots de passe
- **AuthService.verifyPassword()** - Vérification des mots de passe
- **Gestion des sessions** - Création et validation de sessions

### Service email (server/services/email.ts)
- **emailService.sendWelcomeEmail()** - Email de bienvenue
- **Configuration Nodemailer** - Service SMTP
- **Templates d'emails** - Messages standardisés

## 💾 COUCHE DE DONNÉES - server/data/

### Interface de stockage (server/data/storage.ts)
**IStorage** - Interface principale (100+ méthodes)

#### Gestion des utilisateurs (6 méthodes)
- `getUser(id)` - Récupérer utilisateur par ID
- `getUserByUsername(username)` - Récupérer par nom d'utilisateur
- `getUserByEmployeeId(employeeId)` - Récupérer par ID employé
- `createUser(user)` - Créer utilisateur
- `updateUser(id, user)` - Mettre à jour utilisateur
- `getUsers()` - Liste des utilisateurs

#### Gestion des annonces (5 méthodes)
- `getAnnouncements()` - Liste des annonces
- `getAnnouncementById(id)` - Annonce par ID
- `createAnnouncement(announcement)` - Créer annonce
- `updateAnnouncement(id, announcement)` - Mettre à jour
- `deleteAnnouncement(id)` - Supprimer

#### Gestion des documents (5 méthodes)
- `getDocuments()` - Liste des documents
- `getDocumentById(id)` - Document par ID
- `createDocument(document)` - Créer document
- `updateDocument(id, document)` - Mettre à jour
- `deleteDocument(id)` - Supprimer

#### Gestion des événements (5 méthodes)
- `getEvents()` - Liste des événements
- `getEventById(id)` - Événement par ID
- `createEvent(event)` - Créer événement
- `updateEvent(id, event)` - Mettre à jour
- `deleteEvent(id)` - Supprimer

#### Gestion des messages (4 méthodes)
- `getMessages(userId)` - Messages utilisateur
- `getMessageById(id)` - Message par ID
- `createMessage(message)` - Créer message
- `markMessageAsRead(id)` - Marquer comme lu

#### Gestion des réclamations (5 méthodes)
- `getComplaints()` - Liste des réclamations
- `getComplaintById(id)` - Réclamation par ID
- `getComplaintsByUser(userId)` - Par utilisateur
- `createComplaint(complaint)` - Créer réclamation
- `updateComplaint(id, complaint)` - Mettre à jour

#### Gestion des permissions (4 méthodes)
- `getPermissions(userId)` - Permissions utilisateur
- `createPermission(permission)` - Accorder permission
- `revokePermission(id)` - Révoquer permission
- `hasPermission(userId, permission)` - Vérifier permission

#### Gestion du contenu (5 méthodes)
- `getContents()` - Liste du contenu
- `getContentById(id)` - Contenu par ID
- `createContent(content)` - Créer contenu
- `updateContent(id, content)` - Mettre à jour
- `deleteContent(id)` - Supprimer

#### Gestion des catégories (5 méthodes)
- `getCategories()` - Liste des catégories
- `getCategoryById(id)` - Catégorie par ID
- `createCategory(category)` - Créer catégorie
- `updateCategory(id, category)` - Mettre à jour
- `deleteCategory(id)` - Supprimer

#### Gestion des formations (5 méthodes)
- `getTrainings()` - Liste des formations
- `getTrainingById(id)` - Formation par ID
- `createTraining(training)` - Créer formation
- `updateTraining(id, training)` - Mettre à jour
- `deleteTraining(id)` - Supprimer

#### Participants aux formations (5 méthodes)
- `getTrainingParticipants(trainingId)` - Participants
- `getUserTrainingParticipations(userId)` - Participations utilisateur
- `addTrainingParticipant(participant)` - Ajouter participant
- `updateTrainingParticipant(id, participant)` - Mettre à jour
- `removeTrainingParticipant(trainingId, userId)` - Supprimer

#### Autres méthodes
- `getStats()` - Statistiques système (10 métriques)
- `resetToTestData()` - Réinitialisation données test
- `searchUsers(query)` - Recherche utilisateurs

## ⚙️ CONFIGURATION

### Configuration serveur (server/config.ts)
- **Variables d'environnement** - PORT, NODE_ENV, DATABASE_URL
- **Configuration de session** - Secret, options de cookies
- **Configuration de base de données** - Pool de connexions

### Configuration base de données (server/db.ts)
- **Drizzle ORM** - Configuration avec PostgreSQL
- **Pool de connexions** - Gestion optimisée des connexions
- **Variables d'environnement** - URL de base de données

### Configuration Vite (server/vite.ts)
- **Middleware Vite** - Intégration serveur de développement
- **Hot reload** - Rechargement automatique
- **Alias de paths** - Résolution des imports

## 🔄 MIGRATIONS - server/migrations.ts

### Système de migration automatique
- **Migration des mots de passe** - Conversion vers bcrypt
- **Vérification au démarrage** - Migration automatique si nécessaire
- **Logs détaillés** - Suivi des opérations
- **Gestion d'erreurs** - Robustesse du processus

## 📊 DONNÉES DE TEST - server/testData.ts

### Jeu de données initial
- **3 utilisateurs** - admin, marie.martin, pierre.dubois
- **2 annonces** - Exemples d'annonces système
- **Rôles configurés** - Admin, moderator, employee
- **Données cohérentes** - Relations entre entités

## 🔧 DÉPENDANCES BACKEND PRINCIPALES

### Framework et serveur
- **Express.js** - Framework web
- **TypeScript** - Typage statique
- **Node.js** - Runtime JavaScript

### Base de données
- **PostgreSQL** - Base de données relationnelle
- **Drizzle ORM** - ORM type-safe
- **connect-pg-simple** - Store de session PostgreSQL

### Sécurité et authentification
- **bcrypt** - Hachage des mots de passe
- **express-session** - Gestion des sessions
- **helmet** - Sécurisation des en-têtes
- **express-rate-limit** - Limitation de débit

### Validation et transformation
- **Zod** - Validation de schémas
- **zod-validation-error** - Messages d'erreur améliorés

### Communication
- **nodemailer** - Service d'email
- **ws** - WebSockets pour temps réel

### Utilitaires
- **memoizee** - Cache en mémoire
- **memorystore** - Store de session en mémoire (fallback)

## 🔒 SÉCURITÉ BACKEND

### Authentification
- **Sessions sécurisées** - HttpOnly cookies
- **Mots de passe hashés** - bcrypt avec salt
- **Validation de rôles** - Middleware d'autorisation
- **Expiration de session** - Nettoyage automatique

### Protection des API
- **Rate limiting** - 100 req/15min par IP
- **Validation d'entrée** - Schémas Zod obligatoires
- **En-têtes sécurisés** - Configuration Helmet
- **Gestion d'erreurs** - Logs détaillés sans exposition

### Base de données
- **Requêtes préparées** - Protection injection SQL
- **Transactions** - Cohérence des données
- **Connexions poolées** - Optimisation des ressources

## 📈 PERFORMANCE ET SURVEILLANCE

### Logging
- **Logs de requêtes** - Temps de réponse, status codes
- **Logs d'erreurs** - Stack traces détaillées
- **Logs de migration** - Suivi des opérations DB

### Optimisations
- **Cache en mémoire** - memoizee pour requêtes fréquentes
- **Pool de connexions** - Réutilisation des connexions DB
- **Compression** - Réduction de la taille des réponses

### Monitoring
- **Health checks** - Vérification de l'état du serveur
- **Métriques système** - Stats en temps réel
- **Error tracking** - Capture et analyse des erreurs

## 🔄 ARCHITECTURE DE DONNÉES

### Pattern Repository
- **IStorage interface** - Abstraction de la couche de données
- **Implémentation PostgreSQL** - Stockage relationnel
- **Type safety** - Types Drizzle générés

### Validation multicouche
1. **Client-side** - Validation Zod côté frontend
2. **API-side** - Re-validation côté serveur
3. **Database-side** - Contraintes PostgreSQL

### Gestion d'état
- **Sessions serveur** - État d'authentification
- **Base de données** - Source de vérité
- **Cache applicatif** - Optimisation des performances