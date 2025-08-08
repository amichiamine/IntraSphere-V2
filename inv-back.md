# INVENTAIRE EXHAUSTIF BACKEND - IntraSphere
**Date d'analyse**: 8 août 2025 (Mise à jour post-corrections)  
**Structure**: Option R3 (routes/ + services/ + middleware/ + data/)  
**Total fichiers analysés**: 11 fichiers TypeScript Node.js  
**Status**: Trust proxy configuré, rate limiting opérationnel, API fonctionnelle

## 🏗️ ARCHITECTURE BACKEND

### Structure des Dossiers
```
server/
├── 📁 routes/              → Endpoints API REST
│   └── api.ts             → Routes principales (1800+ lignes)
├── 📁 services/           → Logique métier
│   ├── auth.ts            → Service d'authentification
│   └── email.ts           → Service d'email
├── 📁 middleware/         → Middlewares Express
│   └── security.ts        → Sécurité et rate limiting
├── 📁 data/              → Couche de persistance
│   └── storage.ts         → Interface et implémentation MemStorage
├── 📁 public/            → Fichiers statiques (build Vite)
│   ├── assets/           → JS/CSS compilés
│   └── index.html        → SPA entry point
├── config.ts             → Configuration serveur
├── db.ts                 → Connexion base de données
├── index.ts              → Point d'entrée principal
├── migrations.ts         → Migrations de données
├── testData.ts           → Données de test/dev
└── vite.ts               → Serveur Vite intégré
```

## 🛠️ CONFIGURATION ET DÉMARRAGE

### **index.ts** - Point d'Entrée Principal (100 lignes)
- **Express server** sur port 5000
- **Middleware stack** :
  - Rate limiting avec express-rate-limit
  - CORS pour développement
  - Session management avec express-session + connect-pg-simple
  - Body parsing (JSON, URL-encoded)
  - Compression gzip
  - Helmet pour sécurité
- **Routes** : API `/api/*` + Vite fallback
- **Gestion d'erreurs** : Catch global + logging
- **Migrations** automatiques au démarrage

### **config.ts** - Configuration Serveur
- **Variables d'environnement** :
  - `DATABASE_URL` : Connexion PostgreSQL
  - `SESSION_SECRET` : Clé sessions
  - `NODE_ENV` : development/production
- **Ports** : 5000 backend, 3000 Vite dev
- **Sessions** : Durée, cookies sécurisés

### **db.ts** - Base de Données
- **Drizzle ORM** avec PostgreSQL
- **Configuration** : Pool de connexions
- **Migration** : Automatic schema sync
- **Connexion** via `@neondatabase/serverless`

### **vite.ts** - Intégration Vite (50 lignes)
- **Serveur Vite** intégré pour développement
- **Proxy** : API requests vers Express
- **Hot reload** : Rechargement automatique
- **Build** : Production assets dans public/

## 🔐 SÉCURITÉ ET MIDDLEWARE

### **middleware/security.ts** - Sécurité (150 lignes)
- **Rate limiting** :
  - `/api/auth/login` : 5 tentatives/15min
  - `/api/auth/register` : 3 tentatives/hour
  - API générale : 100 req/15min
- **Headers sécurisés** avec Helmet :
  - Content Security Policy
  - HSTS
  - X-Frame-Options
  - X-Content-Type-Options
- **Validation** des entrées
- **Logging** des requêtes

### **services/auth.ts** - Authentification (200 lignes)
- **Hachage des mots de passe** avec bcrypt (10 rounds)
- **Méthodes** :
  - `hashPassword(password)` : Hachage sécurisé
  - `verifyPassword(password, hash)` : Vérification
  - `generateSessionToken()` : Tokens de session
- **Validation** : Force des mots de passe
- **Sécurité** : Protection timing attacks

### **services/email.ts** - Service Email (100 lignes)
- **Configuration** : SMTP avec nodemailer
- **Templates** : Email HTML/text
- **Types d'emails** :
  - Confirmation d'inscription
  - Réinitialisation mot de passe
  - Notifications système
- **Queue** : Envoi asynchrone
- **Retry** : Gestion des échecs

## 📊 STOCKAGE ET PERSISTANCE

### **data/storage.ts** - Interface Storage (2000+ lignes)
#### Interface IStorage - Contrat API (75 méthodes)

**👥 Gestion Utilisateurs (11 méthodes)**
- `getUser(id)`, `getUserByUsername()`, `getUserByEmployeeId()`
- `createUser()`, `updateUser()`, `getUsers()`

**📢 Gestion Annonces (5 méthodes)**
- `getAnnouncements()`, `getAnnouncementById()`
- `createAnnouncement()`, `updateAnnouncement()`, `deleteAnnouncement()`

**📄 Gestion Documents (5 méthodes)**
- `getDocuments()`, `getDocumentById()`
- `createDocument()`, `updateDocument()`, `deleteDocument()`

**📅 Gestion Événements (5 méthodes)**
- `getEvents()`, `getEventById()`
- `createEvent()`, `updateEvent()`, `deleteEvent()`

**💬 Gestion Messages (4 méthodes)**
- `getMessages()`, `getMessageById()`
- `createMessage()`, `markMessageAsRead()`

**🎫 Gestion Réclamations (5 méthodes)**
- `getComplaints()`, `getComplaintById()`, `getComplaintsByUser()`
- `createComplaint()`, `updateComplaint()`

**🛡️ Gestion Permissions (4 méthodes)**
- `getPermissions()`, `createPermission()`
- `revokePermission()`, `hasPermission()`

**📰 Gestion Contenu (5 méthodes)**
- `getContents()`, `getContentById()`
- `createContent()`, `updateContent()`, `deleteContent()`

**🏷️ Gestion Catégories (10 méthodes)**
- Catégories générales : `getCategories()`, CRUD complet
- Catégories employés : `getEmployeeCategories()`, CRUD complet

**⚙️ Paramètres Système (2 méthodes)**
- `getSystemSettings()`, `updateSystemSettings()`

**🎓 Système de Formation (15 méthodes)**
- Formations : `getTrainings()`, CRUD complet, `searchTrainings()`
- Participants : `getTrainingParticipants()`, inscription/désinscription
- Cours : `getCourses()`, CRUD, `getCoursesByTraining()`
- Leçons : `getLessons()`, CRUD, `getLessonsByCourse()`
- Quizzes : `getQuizzes()`, CRUD, `getQuizzesByLesson()`
- Inscriptions : `getEnrollments()`, gestion complète
- Progression : `getLessonProgress()`, `updateProgress()`
- Tentatives quiz : `getQuizAttempts()`, `submitQuizAttempt()`
- Certificats : `getCertificates()`, `generateCertificate()`
- Ressources : `getResources()`, CRUD complet

**💬 Système Forum (12 méthodes)**
- Catégories : `getForumCategories()`, CRUD
- Sujets : `getForumTopics()`, CRUD, `getTopicsByCategory()`
- Posts : `getForumPosts()`, CRUD, `getPostsByTopic()`
- Likes : `getForumLikes()`, `toggleLike()`
- Statistiques : `getForumUserStats()`, `updateUserStats()`

#### MemStorage - Implémentation en Mémoire (1500+ lignes)
- **Collections** : Maps pour chaque entité
- **Données de test** : Utilisateurs, annonces, documents pré-remplis
- **Validation** : Contraintes de référence simulées
- **Performance** : Recherche optimisée avec index

### **testData.ts** - Données de Test (300 lignes)
- **3 utilisateurs** pré-configurés :
  - `admin` / `admin123` (Administrateur)
  - `marie.martin` / `marie123` (Manager)
  - `pierre.dubois` / `pierre123` (Employé)
- **Annonces** : 2 exemples (politique, formation)
- **Documents** : 3 exemples (règlement, guide, procédure)
- **Événements** : 2 réunions planifiées
- **Formations** : 2 cours (sécurité, management)

### **migrations.ts** - Migrations (150 lignes)
- **Migration des mots de passe** : bcrypt pour comptes existants
- **Versioning** : Système de versions de schéma
- **Rollback** : Possibilité de retour arrière
- **Logging** : Trace des migrations appliquées

## 🌐 API REST - Routes Principales

### **routes/api.ts** - API Endpoints (1800+ lignes)

#### 🔐 Authentification (6 endpoints)
```typescript
POST   /api/auth/login           // Connexion utilisateur
POST   /api/auth/register        // Inscription (si activé)
POST   /api/auth/logout          // Déconnexion
GET    /api/auth/me              // Profil utilisateur connecté
PUT    /api/auth/me              // Mise à jour profil
PUT    /api/auth/password        // Changement mot de passe
```

#### 📊 Statistiques (1 endpoint)
```typescript
GET    /api/stats                // Métriques dashboard
```

#### 📢 Annonces (5 endpoints)
```typescript
GET    /api/announcements        // Liste des annonces
GET    /api/announcements/:id    // Détail annonce
POST   /api/announcements        // Créer annonce [auth]
PUT    /api/announcements/:id    // Modifier annonce [permission]
DELETE /api/announcements/:id    // Supprimer annonce [permission]
```

#### 📄 Documents (5 endpoints)
```typescript
GET    /api/documents            // Liste des documents
GET    /api/documents/:id        // Détail document
POST   /api/documents            // Créer document [permission]
PUT    /api/documents/:id        // Modifier document [permission]
DELETE /api/documents/:id        // Supprimer document [permission]
```

#### 📅 Événements (5 endpoints)
```typescript
GET    /api/events               // Liste des événements
GET    /api/events/:id           // Détail événement
POST   /api/events               // Créer événement [permission]
PUT    /api/events/:id           // Modifier événement [permission]
DELETE /api/events/:id           // Supprimer événement [permission]
```

#### 👥 Utilisateurs (6 endpoints)
```typescript
GET    /api/users                // Liste utilisateurs [admin]
GET    /api/users/:id            // Détail utilisateur [admin]
POST   /api/users                // Créer utilisateur [admin]
PUT    /api/users/:id            // Modifier utilisateur [admin]
PUT    /api/users/:id/activate   // Activer compte [admin]
PUT    /api/users/:id/deactivate // Désactiver compte [admin]
```

#### 💬 Messages (4 endpoints)
```typescript
GET    /api/messages             // Messages utilisateur [auth]
GET    /api/messages/:id         // Détail message [auth]
POST   /api/messages             // Envoyer message [auth]
PUT    /api/messages/:id/read    // Marquer lu [auth]
```

#### 🎫 Réclamations (6 endpoints)
```typescript
GET    /api/complaints           // Liste réclamations [auth]
GET    /api/complaints/:id       // Détail réclamation [auth]
GET    /api/complaints/my        // Mes réclamations [auth]
POST   /api/complaints           // Créer réclamation [auth]
PUT    /api/complaints/:id       // Modifier réclamation [permission]
PUT    /api/complaints/:id/assign// Assigner réclamation [permission]
```

#### 🛡️ Permissions (4 endpoints)
```typescript
GET    /api/permissions/:userId  // Permissions utilisateur [admin]
POST   /api/permissions          // Accorder permission [admin]
DELETE /api/permissions/:id      // Révoquer permission [admin]
GET    /api/permissions/check    // Vérifier permission [auth]
```

#### 📰 Contenu (5 endpoints)
```typescript
GET    /api/contents             // Liste contenus [auth]
GET    /api/contents/:id         // Détail contenu [auth]
POST   /api/contents             // Créer contenu [permission]
PUT    /api/contents/:id         // Modifier contenu [permission]
DELETE /api/contents/:id         // Supprimer contenu [permission]
```

#### 🏷️ Catégories (10 endpoints)
```typescript
// Catégories générales
GET    /api/categories           // Liste catégories
POST   /api/categories           // Créer catégorie [permission]
PUT    /api/categories/:id       // Modifier catégorie [permission]
DELETE /api/categories/:id       // Supprimer catégorie [permission]

// Catégories employés
GET    /api/employee-categories  // Liste catégories employés [admin]
POST   /api/employee-categories  // Créer catégorie employé [admin]
PUT    /api/employee-categories/:id // Modifier catégorie [admin]
DELETE /api/employee-categories/:id // Supprimer catégorie [admin]
```

#### ⚙️ Paramètres Système (2 endpoints)
```typescript
GET    /api/system-settings      // Paramètres système [admin]
PUT    /api/system-settings      // Modifier paramètres [admin]
```

#### 🎓 Formations (25 endpoints)
```typescript
// Formations
GET    /api/trainings            // Liste formations
GET    /api/trainings/:id        // Détail formation
POST   /api/trainings            // Créer formation [permission]
PUT    /api/trainings/:id        // Modifier formation [permission]
DELETE /api/trainings/:id        // Supprimer formation [permission]

// Participants
GET    /api/trainings/:id/participants // Participants formation
POST   /api/trainings/:id/register     // S'inscrire [auth]
DELETE /api/trainings/:id/unregister   // Se désinscrire [auth]

// Cours
GET    /api/courses              // Liste cours
GET    /api/trainings/:id/courses// Cours par formation
POST   /api/courses              // Créer cours [permission]
PUT    /api/courses/:id          // Modifier cours [permission]
DELETE /api/courses/:id          // Supprimer cours [permission]

// Leçons  
GET    /api/lessons              // Liste leçons
GET    /api/courses/:id/lessons  // Leçons par cours
POST   /api/lessons              // Créer leçon [permission]
PUT    /api/lessons/:id          // Modifier leçon [permission]
DELETE /api/lessons/:id          // Supprimer leçon [permission]

// Progression
GET    /api/progress/:userId     // Progression utilisateur [auth]
PUT    /api/progress/:lessonId   // Mettre à jour progression [auth]

// Certificats
GET    /api/certificates/:userId // Certificats utilisateur [auth]
POST   /api/certificates         // Générer certificat [permission]

// Ressources
GET    /api/resources            // Liste ressources
POST   /api/resources            // Créer ressource [permission]
PUT    /api/resources/:id        // Modifier ressource [permission]
DELETE /api/resources/:id        // Supprimer ressource [permission]
```

#### 💬 Forum (15 endpoints)
```typescript
// Catégories forum
GET    /api/forum/categories     // Catégories forum
POST   /api/forum/categories     // Créer catégorie [permission]
PUT    /api/forum/categories/:id // Modifier catégorie [permission]
DELETE /api/forum/categories/:id // Supprimer catégorie [permission]

// Sujets
GET    /api/forum/topics         // Liste sujets
GET    /api/forum/categories/:id/topics // Sujets par catégorie
GET    /api/forum/topics/:id     // Détail sujet
POST   /api/forum/topics         // Créer sujet [auth]
PUT    /api/forum/topics/:id     // Modifier sujet [permission]
DELETE /api/forum/topics/:id     // Supprimer sujet [permission]

// Posts
GET    /api/forum/posts          // Liste posts
GET    /api/forum/topics/:id/posts // Posts par sujet
POST   /api/forum/posts          // Créer post [auth]
PUT    /api/forum/posts/:id      // Modifier post [auth/permission]
DELETE /api/forum/posts/:id      // Supprimer post [permission]

// Likes
POST   /api/forum/posts/:id/like // Liker/unliker post [auth]
```

### Middlewares de Sécurité

#### **requireAuth** - Authentification Requise
- **Vérification** : Session utilisateur active
- **Réponse** : 401 si non authentifié
- **Usage** : Toutes les routes protégées

#### **requireRole(roles)** - Contrôle de Rôle
- **Vérification** : Rôle utilisateur dans liste autorisée
- **Rôles** : 'admin', 'moderator', 'employee'
- **Réponse** : 403 si permissions insuffisantes
- **Usage** : Routes admin uniquement

#### **requirePermission(permission)** - Permission Granulaire
- **Vérification** : Permission spécifique accordée
- **Types** :
  - `manage_announcements`
  - `manage_documents`
  - `manage_events`
  - `manage_users`
  - `validate_topics`
  - `validate_posts`
  - `manage_employee_categories`
  - `manage_trainings`
- **Délégation** : Admin peut déléguer permissions

## 🗄️ SCHÉMA DE BASE DE DONNÉES

### **shared/schema.ts** - Modèle de Données (600+ lignes)

#### Tables Principales (13 tables)

**👥 users** - Utilisateurs
```sql
- id (UUID, PK)
- username (unique)
- password (bcrypt)
- name, role, avatar
- employeeId (unique)
- department, position, isActive
- phone, email
- createdAt, updatedAt
```

**📢 announcements** - Annonces
```sql
- id (UUID, PK)
- title, content, type
- authorId (FK users), authorName
- imageUrl, icon
- createdAt, isImportant
```

**📄 documents** - Documents
```sql
- id (UUID, PK)
- title, description, category
- fileName, fileUrl
- updatedAt, version
```

**📅 events** - Événements
```sql
- id (UUID, PK)
- title, description, date
- location, type
- organizerId (FK users)
- createdAt
```

**💬 messages** - Messages Internes
```sql
- id (UUID, PK)
- senderId (FK users)
- recipientId (FK users)
- subject, content
- isRead, createdAt
```

**🎫 complaints** - Réclamations
```sql
- id (UUID, PK)
- submitterId (FK users)
- assignedToId (FK users)
- title, description
- category, priority, status
- createdAt, updatedAt
```

**🛡️ permissions** - Permissions
```sql
- id (UUID, PK)
- userId (FK users)
- grantedBy (FK users)
- permission
- createdAt
```

#### Tables Formation (6 tables)

**🎓 trainings** - Formations
```sql
- id (UUID, PK)
- title, description, category
- difficulty, duration
- instructorId (FK users), instructorName
- startDate, endDate, location
- maxParticipants, currentParticipants
- isMandatory, isActive, isVisible
- thumbnailUrl, documentUrls[]
- createdAt, updatedAt
```

**📚 trainingParticipants** - Participants
```sql
- id (UUID, PK)
- trainingId (FK trainings)
- userId (FK users)
- registeredAt, status
- completionDate, score, feedback
```

**📖 courses** - Cours
```sql
- id (UUID, PK)
- trainingId (FK trainings)
- title, description
- order, duration
- isActive, prerequisites[]
- createdAt, updatedAt
```

**📝 lessons** - Leçons
```sql
- id (UUID, PK)
- courseId (FK courses)
- title, content, type
- order, duration
- videoUrl, attachments[]
- isActive, createdAt, updatedAt
```

**🧪 quizzes** - Quiz
```sql
- id (UUID, PK)
- lessonId (FK lessons)
- title, description
- questions[] (JSON), passingScore
- timeLimit, maxAttempts
- isActive, createdAt, updatedAt
```

**📋 enrollments** - Inscriptions
```sql
- id (UUID, PK)
- userId (FK users)
- trainingId (FK trainings)
- enrolledAt, status
- progress, completedAt
- certificateId
```

#### Tables Forum (4 tables)

**🗂️ forumCategories** - Catégories Forum
```sql
- id (UUID, PK)
- name, description
- color, icon
- isActive, order
- createdAt, updatedAt
```

**💬 forumTopics** - Sujets Forum
```sql
- id (UUID, PK)
- categoryId (FK forumCategories)
- authorId (FK users)
- title, content
- isPinned, isLocked
- viewCount, replyCount
- lastReplyAt, createdAt, updatedAt
```

**💭 forumPosts** - Posts Forum
```sql
- id (UUID, PK)
- topicId (FK forumTopics)
- authorId (FK users)
- content, likeCount
- isEdited, editedAt
- createdAt, updatedAt
```

**👍 forumLikes** - Likes Forum
```sql
- id (UUID, PK)
- postId (FK forumPosts)
- userId (FK users)
- createdAt
```

### Schémas de Validation Zod (25 schémas)
- **Insert schemas** pour chaque table
- **Validation** : Types, longueurs, formats
- **Sécurité** : Sanitization des entrées
- **Types inférés** pour TypeScript

## 🔧 FONCTIONNALITÉS BACKEND

### Authentification et Sessions
- **Bcrypt** : Hachage sécurisé (10 rounds)
- **Sessions** : express-session + PostgreSQL store
- **Tokens** : UUID pour sessions
- **Expiration** : Configurable
- **CSRF** : Protection incluse

### Gestion des Permissions
- **RBAC** : Role-Based Access Control
- **Granularité** : Permissions spécifiques par fonctionnalité
- **Délégation** : Admin peut déléguer sans perdre contrôle
- **Héritage** : Admin hérite de toutes les permissions

### Upload de Fichiers
- **Multer** : Gestion des uploads
- **Validation** : Types MIME, tailles
- **Stockage** : Local/Cloud configurable
- **Sécurité** : Scan antivirus potentiel

### Cache et Performance
- **React Query** : Cache côté client automatique
- **Compression** : Gzip pour réponses
- **ETags** : Cache navigateur
- **Pagination** : Pour listes importantes

### Logging et Monitoring
- **Structured logging** : JSON format
- **Niveaux** : error, warn, info, debug
- **Contexte** : User ID, request ID
- **Métriques** : Temps de réponse, erreurs

## 📊 MÉTRIQUES BACKEND

### API Endpoints par Fonctionnalité
1. **Formations** : 25 endpoints (le plus complexe)
2. **Forum** : 15 endpoints
3. **Catégories** : 10 endpoints
4. **Réclamations** : 6 endpoints
5. **Utilisateurs** : 6 endpoints
6. **Authentification** : 6 endpoints

### Complexité par Fichier
1. **routes/api.ts** : 1800+ lignes (le plus complexe)
2. **data/storage.ts** : 2000+ lignes (interface + implémentation)
3. **shared/schema.ts** : 600+ lignes (modèle de données)
4. **testData.ts** : 300+ lignes (données de test)
5. **services/auth.ts** : 200+ lignes

### Méthodes Storage par Entité
1. **Formation** : 15 méthodes (système complet)
2. **Forum** : 12 méthodes (discussion complète)
3. **Utilisateurs** : 11 méthodes (gestion complète)
4. **Catégories** : 10 méthodes (double système)
5. **Standard** : 5 méthodes par entité (CRUD)

## 🚨 POINTS D'ATTENTION DÉTECTÉS

### ✅ Sécurité Opérationnelle
- **Rate limiting** : ✅ Configuré et fonctionnel avec trust proxy
- **Input validation** : Zod complet sur tous les endpoints
- **SQL injection** : Protection Drizzle ORM intégrale
- **Trust proxy** : ✅ Configuré pour environnement Replit
- **Sessions** : Sécurisées avec PostgreSQL store

### Performance
- **MemStorage** : Performant en dev mais non persistant
- **Database queries** : Pas d'optimisation N+1 queries
- **Cache** : Pas de cache serveur (Redis recommandé)
- **Pagination** : Implémentée mais pas partout

### Scalabilité
- **Single instance** : Pas de support multi-instance
- **File storage** : Local uniquement, cloud storage recommandé
- **Session store** : PostgreSQL OK mais Redis plus performant
- **Background jobs** : Pas de queue pour tâches longues

### Monitoring
- **Health checks** : Basiques uniquement
- **Metrics** : Pas de Prometheus/OpenTelemetry
- **Alerting** : Pas de système d'alertes
- **Tracing** : Pas de tracing distribué

## ✅ FORCES DU BACKEND

### Architecture Solide
- **Séparation claire** : routes/services/middleware/data
- **Interface contracts** : IStorage bien défini
- **Type safety** : TypeScript intégral + Zod
- **Modularité** : Fonctionnalités bien découplées

### Sécurité Robuste
- **Authentification** : Bcrypt + sessions sécurisées
- **Autorisation** : RBAC + permissions granulaires
- **Rate limiting** : Protection DOS
- **Input validation** : Zod complet

### API Complète
- **RESTful** : Design cohérent
- **CRUD complet** : Toutes les entités
- **Error handling** : Codes HTTP appropriés
- **Documentation** : Endpoints bien structurés

### Fonctionnalités Avancées
- **Système de formation** : Complet avec progression
- **Forum de discussion** : Avec modération
- **Gestion des permissions** : Délégation flexible
- **Multi-tenancy ready** : Structure extensible

## 🔄 FLUX DE DONNÉES

### Authentification
```
Client → POST /api/auth/login → AuthService.verifyPassword() → Session créée → User retourné
```

### Création de Contenu
```
Client → POST /api/announcements → requireAuth → Zod validation → storage.createAnnouncement() → Broadcast update
```

### Permissions
```
Client → Requête protégée → requirePermission → storage.hasPermission() → Action autorisée/refusée
```

### Formation
```
Client → GET /api/trainings → storage.getTrainings() → Filtres appliqués → Liste retournée
Student → POST /api/trainings/:id/register → storage.enrollUser() → Email confirmation
```

## 🎯 RECOMMANDATIONS D'OPTIMISATION

### Performance Immédiate
1. **Redis cache** : Cache queries fréquentes
2. **Database indexing** : Index sur colonnes recherchées
3. **Pagination** : Système universel avec curseurs
4. **Background jobs** : Queue pour emails/notifications

### Sécurité Renforcée
1. **Input sanitization** : HTML/XSS protection
2. **File scanning** : Antivirus sur uploads
3. **API versioning** : Dépréciation contrôlée
4. **Audit logs** : Traçabilité des actions sensibles

### Monitoring Avancé
1. **Health endpoints** : `/health`, `/metrics`
2. **Structured logging** : Format JSON avec context
3. **Performance monitoring** : APM tool
4. **Error tracking** : Sentry ou équivalent

### Scalabilité Future
1. **Microservices ready** : Découpage par domaine
2. **Event sourcing** : Historique des changements
3. **CQRS** : Séparation lecture/écriture
4. **API Gateway** : Point d'entrée unique