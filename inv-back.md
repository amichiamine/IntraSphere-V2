# INVENTAIRE EXHAUSTIF - BACKEND

## Vue d'ensemble de l'architecture Backend
- **Runtime**: Node.js avec TypeScript
- **Framework**: Express.js
- **ORM**: Drizzle ORM
- **Base de données**: PostgreSQL (avec fallback en mémoire)
- **Validation**: Zod
- **Authentification**: Sessions Express avec bcrypt
- **Communication temps réel**: WebSocket (ws)
- **Sécurité**: Helmet, CORS, rate limiting

## Structure des dossiers Backend

### `/server` - Racine Backend
```
server/
├── index.ts                      # Point d'entrée principal du serveur
├── config.ts                     # Configuration environnement
├── db.ts                         # Configuration base de données
├── migrations.ts                 # Migrations et setup initial
├── testData.ts                   # Données de test et seed
├── vite.ts                       # Intégration Vite development
├── data/                         # Couche de données
│   └── storage.ts               # Interface storage et implémentation
├── middleware/                   # Middlewares Express
│   └── security.ts             # Sécurité et authentification
├── routes/                       # Routes et endpoints API
│   └── api.ts                   # Toutes les routes API
└── services/                     # Services métier
    ├── auth.ts                  # Service d'authentification
    ├── email.ts                 # Service email/SMTP
    └── websocket.ts             # Gestionnaire WebSocket
```

### `/shared` - Schémas partagés
```
shared/
└── schema.ts                     # Schémas Drizzle et types TypeScript
```

### `/config` - Configuration
```
config/
├── components.json              # Configuration shadcn/ui
├── drizzle.config.ts           # Configuration Drizzle ORM
├── postcss.config.js           # Configuration PostCSS
└── tailwind.config.ts          # Configuration TailwindCSS
```

## Modèle de Données (Drizzle Schema)

### Tables Principales

#### `users` - Utilisateurs
```typescript
{
  id: varchar (UUID, primary key)
  username: text (unique, not null)
  password: text (not null, hashed avec bcrypt)
  name: text (not null)
  role: text (employee|admin|moderator, default: employee)
  avatar: text (URL optionnelle)
  employeeId: varchar (unique, pour communication interne)
  department: varchar
  position: varchar
  isActive: boolean (default: true)
  phone: varchar
  email: varchar
  createdAt: timestamp (auto)
  updatedAt: timestamp (auto)
}
```

#### `announcements` - Annonces
```typescript
{
  id: varchar (UUID, primary key)
  title: text (not null)
  content: text (not null)
  type: text (info|important|event|formation, default: info)
  authorId: varchar (foreign key users.id)
  authorName: text (not null)
  imageUrl: text (optionnel)
  icon: text (default: 📢)
  createdAt: timestamp (not null, auto)
  isImportant: boolean (default: false)
}
```

#### `documents` - Documents
```typescript
{
  id: varchar (UUID, primary key)
  title: text (not null)
  description: text
  category: text (regulation|policy|guide|procedure, not null)
  fileName: text (not null)
  fileUrl: text (not null)
  updatedAt: timestamp (not null, auto)
  version: text (default: 1.0)
}
```

#### `events` - Événements
```typescript
{
  id: varchar (UUID, primary key)
  title: text (not null)
  description: text
  date: timestamp (not null)
  location: text
  type: text (meeting|training|social|other, default: meeting)
  organizerId: varchar (foreign key users.id)
  createdAt: timestamp (auto)
}
```

#### `messages` - Messagerie interne
```typescript
{
  id: varchar (UUID, primary key)
  senderId: varchar (foreign key users.id, not null)
  recipientId: varchar (foreign key users.id, not null)
  subject: text (not null)
  content: text (not null)
  isRead: boolean (default: false)
  createdAt: timestamp (auto)
}
```

#### `complaints` - Réclamations
```typescript
{
  id: varchar (UUID, primary key)
  submitterId: varchar (foreign key users.id, not null)
  assignedToId: varchar (foreign key users.id)
  title: text (not null)
  description: text (not null)
  category: text (hr|it|facilities|other, not null)
  priority: text (low|medium|high|urgent, default: medium)
  status: text (open|in_progress|resolved|closed, default: open)
  createdAt: timestamp (auto)
  updatedAt: timestamp (auto)
}
```

#### `permissions` - Délégation de permissions
```typescript
{
  id: varchar (UUID, primary key)
  userId: varchar (foreign key users.id, not null)
  grantedBy: varchar (foreign key users.id, not null)
  permission: text (not null)
  // Permissions possibles:
  // - manage_announcements
  // - manage_documents
  // - manage_events
  // - manage_users
  // - validate_topics
  // - validate_posts
  // - manage_employee_categories
  // - manage_trainings
  createdAt: timestamp (auto)
}
```

### Tables de Formation

#### `trainings` - Formations
```typescript
{
  id: varchar (UUID, primary key)
  title: text (not null)
  description: text
  category: text (technical|management|safety|compliance|other, not null)
  difficulty: text (beginner|intermediate|advanced, default: beginner)
  duration: integer (en minutes, not null)
  instructorId: varchar (foreign key users.id)
  instructorName: text (not null)
  startDate: timestamp
  endDate: timestamp
  location: text
  maxParticipants: integer
  currentParticipants: integer (default: 0)
  isMandatory: boolean (default: false)
  isActive: boolean (default: true)
  isVisible: boolean (default: true)
  thumbnailUrl: text
  documentUrls: text[] (array, default: [])
  createdAt: timestamp (not null, auto)
  updatedAt: timestamp (auto)
}
```

#### `trainingParticipants` - Participants formations
```typescript
{
  id: varchar (UUID, primary key)
  trainingId: varchar (foreign key trainings.id, cascade delete, not null)
  userId: varchar (foreign key users.id, cascade delete, not null)
  registeredAt: timestamp (auto)
  status: text (registered|completed|cancelled, default: registered)
  completionDate: timestamp
  score: integer (0-100)
  feedback: text
}
```

### Tables E-Learning

#### `courses` - Cours/modules
```typescript
{
  id: varchar (UUID, primary key)
  title: text (not null)
  description: text
  category: text (technical|compliance|soft-skills|leadership, not null)
  difficulty: text (beginner|intermediate|advanced, default: beginner)
  duration: integer (en minutes)
  thumbnailUrl: text
  authorId: varchar (foreign key users.id)
  authorName: text (not null)
  isPublished: boolean (default: false)
  isMandatory: boolean (default: false)
  prerequisites: text (JSON array course IDs)
  tags: text (JSON array tags)
  createdAt: timestamp (auto)
  updatedAt: timestamp (auto)
}
```

#### `lessons` - Leçons/chapitres
```typescript
{
  id: varchar (UUID, primary key)
  courseId: varchar (foreign key courses.id, not null)
  title: text (not null)
  description: text
  content: text (HTML content, not null)
  order: integer (default: 0)
  duration: integer (en minutes)
  videoUrl: text
  documentUrl: text
  isRequired: boolean (default: true)
  createdAt: timestamp (auto)
  updatedAt: timestamp (auto)
}
```

#### `quizzes` - Quiz et évaluations
```typescript
{
  id: varchar (UUID, primary key)
  courseId: varchar (foreign key courses.id)
  lessonId: varchar (foreign key lessons.id)
  title: text (not null)
  description: text
  questions: text (JSON array questions, not null)
  passingScore: integer (pourcentage, default: 70)
  timeLimit: integer (en minutes)
  allowRetries: boolean (default: true)
  maxAttempts: integer (default: 3)
  isRequired: boolean (default: false)
  createdAt: timestamp (auto)
  updatedAt: timestamp (auto)
}
```

#### `enrollments` - Inscriptions et progression
```typescript
{
  id: varchar (UUID, primary key)
  userId: varchar (foreign key users.id, not null)
  courseId: varchar (foreign key courses.id, not null)
  enrolledAt: timestamp (auto)
  startedAt: timestamp
  completedAt: timestamp
  progress: integer (pourcentage, default: 0)
  status: text (enrolled|in-progress|completed|failed, default: enrolled)
  certificateUrl: text
  timeSpent: integer (en minutes, default: 0)
  score: integer (pourcentage moyen)
  courseTitle: text (dénormalisé pour analytics)
}
```

#### `lessonProgress` - Progression leçons
```typescript
{
  id: varchar (UUID, primary key)
  userId: varchar (foreign key users.id, not null)
  lessonId: varchar (foreign key lessons.id, not null)
  courseId: varchar (foreign key courses.id, not null)
  isCompleted: boolean (default: false)
  timeSpent: integer (en minutes, default: 0)
  completedAt: timestamp
  createdAt: timestamp (auto)
}
```

#### `quizAttempts` - Tentatives de quiz
```typescript
{
  id: varchar (UUID, primary key)
  userId: varchar (foreign key users.id, not null)
  quizId: varchar (foreign key quizzes.id, not null)
  answers: text (JSON answers)
  score: integer (pourcentage)
  passed: boolean
  startedAt: timestamp (auto)
  completedAt: timestamp
  timeSpent: integer (en minutes)
}
```

#### `certificates` - Certificats
```typescript
{
  id: varchar (UUID, primary key)
  userId: varchar (foreign key users.id, not null)
  courseId: varchar (foreign key courses.id, not null)
  certificateUrl: text (not null)
  issuedAt: timestamp (auto)
  expiresAt: timestamp
  metadata: text (JSON metadata)
}
```

### Tables de Contenu

#### `contents` - Contenu multimédia
```typescript
{
  id: varchar (UUID, primary key)
  title: text (not null)
  type: text (video|image|document|audio, not null)
  category: text (not null)
  description: text
  fileUrl: text (not null)
  thumbnailUrl: text
  duration: text
  viewCount: integer (default: 0)
  rating: integer (default: 0)
  tags: text[] (array)
  isPopular: boolean (default: false)
  isFeatured: boolean (default: false)
  createdAt: timestamp (not null, auto)
  updatedAt: timestamp (not null, auto)
}
```

#### `categories` - Catégories
```typescript
{
  id: varchar (UUID, primary key)
  name: text (unique, not null)
  description: text
  icon: text (default: 📁)
  color: text (default: #3B82F6)
  isVisible: boolean (default: true)
  sortOrder: integer (default: 0)
  createdAt: timestamp (not null, auto)
}
```

#### `employeeCategories` - Catégories employés
```typescript
{
  id: varchar (UUID, primary key)
  name: text (unique, not null)
  description: text
  color: text (default: #10B981)
  permissions: text[] (array codes permissions, default: [])
  isActive: boolean (default: true)
  createdAt: timestamp (not null, auto)
}
```

#### `resources` - Ressources e-learning
```typescript
{
  id: varchar (UUID, primary key)
  title: text (not null)
  description: text
  type: text (document|video|link|tool, not null)
  category: text (not null)
  url: text (not null)
  downloadUrl: text
  thumbnailUrl: text
  tags: text[] (array)
  isPublic: boolean (default: true)
  requiredRole: text
  createdAt: timestamp (not null, auto)
  updatedAt: timestamp (auto)
}
```

### Tables Forum

#### `forumCategories` - Catégories forum
```typescript
{
  id: varchar (UUID, primary key)
  name: text (unique, not null)
  description: text
  icon: text (default: 💬)
  color: text (default: #3B82F6)
  isVisible: boolean (default: true)
  sortOrder: integer (default: 0)
  moderatorIds: text[] (array user IDs)
  permissions: text[] (array permissions)
  createdAt: timestamp (auto)
}
```

#### `forumTopics` - Sujets forum
```typescript
{
  id: varchar (UUID, primary key)
  categoryId: varchar (foreign key forumCategories.id, not null)
  authorId: varchar (foreign key users.id, not null)
  title: text (not null)
  description: text
  isSticky: boolean (default: false)
  isLocked: boolean (default: false)
  tags: text[] (array)
  viewCount: integer (default: 0)
  postCount: integer (default: 0)
  lastPostAt: timestamp
  lastPostAuthor: text
  createdAt: timestamp (auto)
  updatedAt: timestamp (auto)
}
```

#### `forumPosts` - Posts forum
```typescript
{
  id: varchar (UUID, primary key)
  topicId: varchar (foreign key forumTopics.id, not null)
  authorId: varchar (foreign key users.id, not null)
  content: text (not null)
  isEdited: boolean (default: false)
  editedAt: timestamp
  likeCount: integer (default: 0)
  isDeleted: boolean (default: false)
  deletedAt: timestamp
  deletedBy: varchar (foreign key users.id)
  createdAt: timestamp (auto)
}
```

#### `forumLikes` - Likes/réactions forum
```typescript
{
  id: varchar (UUID, primary key)
  postId: varchar (foreign key forumPosts.id, not null)
  userId: varchar (foreign key users.id, not null)
  reactionType: text (like|love|laugh|angry, default: like)
  createdAt: timestamp (auto)
}
```

#### `forumUserStats` - Statistiques utilisateur forum
```typescript
{
  id: varchar (UUID, primary key)
  userId: varchar (foreign key users.id, unique, not null)
  postCount: integer (default: 0)
  topicCount: integer (default: 0)
  likeCount: integer (default: 0)
  reputation: integer (default: 0)
  joinedAt: timestamp (auto)
  lastActiveAt: timestamp (auto)
}
```

### Tables Configuration

#### `systemSettings` - Paramètres système
```typescript
{
  id: varchar (primary key, default: "settings")
  showAnnouncements: boolean (default: true)
  showContent: boolean (default: true)
  showDocuments: boolean (default: true)
  showForum: boolean (default: true)
  showMessages: boolean (default: true)
  showComplaints: boolean (default: true)
  showTraining: boolean (default: true)
  updatedAt: timestamp (auto)
}
```

## API Routes et Endpoints

### Routes d'Authentification (`/api/auth/*`)
- **POST** `/api/auth/login` - Connexion utilisateur
- **POST** `/api/auth/register` - Inscription utilisateur
- **GET** `/api/auth/me` - Informations utilisateur connecté
- **POST** `/api/auth/logout` - Déconnexion

### Routes de Contenu
- **GET** `/api/announcements` - Liste des annonces
- **GET** `/api/announcements/:id` - Annonce spécifique
- **POST** `/api/announcements` - Créer annonce
- **PATCH** `/api/announcements/:id` - Modifier annonce
- **DELETE** `/api/announcements/:id` - Supprimer annonce

- **GET** `/api/documents` - Liste des documents
- **GET** `/api/documents/:id` - Document spécifique
- **POST** `/api/documents` - Créer document
- **PATCH** `/api/documents/:id` - Modifier document
- **DELETE** `/api/documents/:id` - Supprimer document

- **GET** `/api/events` - Liste des événements
- **GET** `/api/events/:id` - Événement spécifique
- **POST** `/api/events` - Créer événement
- **PATCH** `/api/events/:id` - Modifier événement
- **DELETE** `/api/events/:id` - Supprimer événement

- **GET** `/api/contents` - Liste du contenu multimédia
- **GET** `/api/contents/:id` - Contenu spécifique
- **POST** `/api/contents` - Créer contenu
- **PATCH** `/api/contents/:id` - Modifier contenu
- **DELETE** `/api/contents/:id` - Supprimer contenu

### Routes Utilisateurs et Communication
- **GET** `/api/users` - Liste des utilisateurs (admin)
- **POST** `/api/users` - Créer utilisateur (admin)
- **PATCH** `/api/users/:id` - Modifier utilisateur
- **DELETE** `/api/users/:id` - Désactiver utilisateur (soft delete)

- **GET** `/api/messages/:userId` - Messages d'un utilisateur
- **POST** `/api/messages` - Envoyer message
- **PATCH** `/api/messages/:id/read` - Marquer comme lu

- **GET** `/api/complaints` - Liste des réclamations
- **POST** `/api/complaints` - Créer réclamation
- **PATCH** `/api/complaints/:id` - Modifier réclamation

### Routes de Formation
- **GET** `/api/trainings` - Liste des formations
- **GET** `/api/trainings/:id` - Formation spécifique
- **POST** `/api/trainings` - Créer formation
- **PATCH** `/api/trainings/:id` - Modifier formation
- **DELETE** `/api/trainings/:id` - Supprimer formation

- **GET** `/api/training-participants/:trainingId` - Participants formation
- **POST** `/api/training-participants` - Inscrire à formation
- **PATCH** `/api/training-participants/:id` - Modifier participation
- **DELETE** `/api/training-participants/:trainingId/:userId` - Désinscrire

### Routes E-Learning
- **GET** `/api/courses` - Liste des cours
- **GET** `/api/courses/:id` - Cours spécifique
- **POST** `/api/courses` - Créer cours
- **PATCH** `/api/courses/:id` - Modifier cours
- **DELETE** `/api/courses/:id` - Supprimer cours

- **GET** `/api/courses/:id/lessons` - Leçons d'un cours
- **POST** `/api/lessons` - Créer leçon
- **PATCH** `/api/lessons/:id` - Modifier leçon
- **DELETE** `/api/lessons/:id` - Supprimer leçon

- **GET** `/api/my-enrollments` - Inscriptions utilisateur
- **POST** `/api/enroll/:courseId` - S'inscrire à un cours
- **PATCH** `/api/enrollments/:id` - Mettre à jour progression

- **GET** `/api/my-certificates` - Certificats utilisateur
- **POST** `/api/certificates` - Générer certificat

- **GET** `/api/resources` - Ressources e-learning
- **POST** `/api/resources` - Créer ressource
- **PATCH** `/api/resources/:id` - Modifier ressource
- **DELETE** `/api/resources/:id` - Supprimer ressource

### Routes Forum
- **GET** `/api/forum/categories` - Catégories forum
- **POST** `/api/forum/categories` - Créer catégorie
- **PATCH** `/api/forum/categories/:id` - Modifier catégorie
- **DELETE** `/api/forum/categories/:id` - Supprimer catégorie

- **GET** `/api/forum/topics` - Sujets forum (avec filtre catégorie)
- **GET** `/api/forum/topics/:id` - Sujet spécifique
- **POST** `/api/forum/topics` - Créer sujet
- **PATCH** `/api/forum/topics/:id` - Modifier sujet
- **DELETE** `/api/forum/topics/:id` - Supprimer sujet

- **GET** `/api/forum/posts/:topicId` - Posts d'un sujet
- **POST** `/api/forum/posts` - Créer post
- **PATCH** `/api/forum/posts/:id` - Modifier post
- **DELETE** `/api/forum/posts/:id` - Supprimer post

- **POST** `/api/forum/posts/:postId/like` - Liker un post
- **GET** `/api/forum/stats/me` - Statistiques utilisateur forum

### Routes Administration
- **GET** `/api/stats` - Statistiques globales
- **GET** `/api/permissions/:userId` - Permissions utilisateur
- **POST** `/api/permissions` - Accorder permission
- **DELETE** `/api/permissions/:id` - Révoquer permission

- **GET** `/api/categories` - Catégories de contenu
- **POST** `/api/categories` - Créer catégorie
- **PATCH** `/api/categories/:id` - Modifier catégorie
- **DELETE** `/api/categories/:id` - Supprimer catégorie

- **GET** `/api/employee-categories` - Catégories employés
- **POST** `/api/employee-categories` - Créer catégorie employé
- **PATCH** `/api/employee-categories/:id` - Modifier catégorie
- **DELETE** `/api/employee-categories/:id` - Supprimer catégorie

- **GET** `/api/system-settings` - Paramètres système
- **PATCH** `/api/system-settings` - Modifier paramètres

### Routes Recherche et Recommandations
- **GET** `/api/search/users?q=...` - Rechercher utilisateurs
- **GET** `/api/search/content?q=...` - Rechercher contenu
- **GET** `/api/search/documents?q=...` - Rechercher documents
- **GET** `/api/search/announcements?q=...` - Rechercher annonces
- **GET** `/api/training-recommendations` - Recommandations formation

## Services Backend

### Service d'Authentification (`AuthService`)
**Fonctionnalités :**
- Hash de mots de passe avec bcrypt (12 rounds)
- Vérification de mots de passe
- Validation de force de mot de passe

**Méthodes :**
```typescript
static async hashPassword(password: string): Promise<string>
static async verifyPassword(password: string, hash: string): Promise<boolean>
static validatePasswordStrength(password: string): { isValid: boolean; errors: string[] }
```

### Service Email (`emailService`)
**Fonctionnalités :**
- Configuration SMTP
- Envoi d'emails de bienvenue
- Notifications par email
- Templates HTML

**Configuration :**
- Support SMTP standard
- Authentification utilisateur/mot de passe
- TLS/SSL
- Templates personnalisables

### Gestionnaire WebSocket (`WebSocketManager`)
**Fonctionnalités :**
- Gestion de connexions multiples
- Système de canaux/channels
- Heartbeat pour détecter déconnexions
- Broadcast de messages

**Events supportés :**
- `AUTHENTICATE`: Authentification client
- `JOIN_CHANNEL`: Rejoindre un canal
- `LEAVE_CHANNEL`: Quitter un canal
- `CHAT_MESSAGE`: Message de chat
- `USER_TYPING`: Indicateur de frappe
- `MARK_NOTIFICATION_READ`: Marquer notification lue

**Méthodes principales :**
```typescript
sendToClient(ws: WebSocketClient, message: WebSocketMessage): void
sendToUser(userId: string, message: WebSocketMessage): void
broadcastToChannel(channelId: string, message: WebSocketMessage, exclude?: string): void
joinChannel(ws: WebSocketClient, channelId: string): void
leaveChannel(ws: WebSocketClient, channelId: string): void
```

## Storage Interface et Implémentation

### Interface de Storage (`IStorage`)
**Types d'opérations :**
- CRUD complet pour toutes les entités
- Recherche et filtrage
- Statistiques et analytics
- Gestion des relations

### Implémentation Mémoire (`MemStorage`)
**Caractéristiques :**
- Stockage en Map pour performance
- Données de test préchargées
- Simulation des relations
- Méthodes de reset pour tests

**Collections principales :**
```typescript
private users: Map<string, User>
private announcements: Map<string, Announcement>
private documents: Map<string, Document>
private events: Map<string, Event>
private messages: Map<string, Message>
private complaints: Map<string, Complaint>
private permissions: Map<string, Permission>
private contents: Map<string, Content>
private categories: Map<string, Category>
private trainings: Map<string, Training>
private courses: Map<string, Course>
private forumCategories: Map<string, ForumCategory>
// ... autres collections
```

## Middleware et Sécurité

### Middleware de Sécurité (`security.ts`)
**Fonctionnalités :**
- Configuration Helmet pour sécurité HTTP
- CORS configuré pour frontend
- Rate limiting par IP
- Sessions Express sécurisées

### Middleware d'Authentification
**Fonctions :**
- `requireAuth`: Vérification session active
- `requireRole(roles[])`: Vérification rôle utilisateur
- Extension des types Express pour session

## Configuration (`config.ts`)

### Variables d'Environnement
```typescript
interface AppConfig {
  port: number                    // PORT (default: 5000)
  nodeEnv: string                // NODE_ENV (development|production|test)
  sessionSecret: string          // SESSION_SECRET
  bcryptSaltRounds: number       // BCRYPT_SALT_ROUNDS (default: 12)
  databaseUrl?: string           // DATABASE_URL
  smtp: {                        // Configuration SMTP
    enabled: boolean             // SMTP_ENABLED
    host?: string               // SMTP_HOST
    port?: number               // SMTP_PORT
    secure?: boolean            // SMTP_SECURE
    user?: string               // SMTP_USER
    pass?: string               // SMTP_PASS
    from?: string               // EMAIL_FROM
  }
  upload: {                      // Configuration upload
    maxFileSize: number         // MAX_FILE_SIZE (default: 10MB)
    allowedTypes: string[]      // ALLOWED_FILE_TYPES
    storageType: string         // STORAGE_TYPE (local|cloud)
    storagePath: string         // STORAGE_PATH
  }
  features: {                    // Feature flags
    registration: boolean       // ENABLE_REGISTRATION
    emailNotifications: boolean // ENABLE_EMAIL_NOTIFICATIONS
    fileUpload: boolean         // ENABLE_FILE_UPLOAD
    forum: boolean              // ENABLE_FORUM
    training: boolean           // ENABLE_TRAINING
  }
}
```

### Validation de Configuration
- Vérifications de sécurité pour production
- Warnings pour configurations manquantes
- Validation SMTP
- Erreurs bloquantes en production

## Base de Données et Migrations

### Configuration Drizzle
- Support PostgreSQL natif
- Fallback en mémoire pour développement
- Pool de connexions
- Migrations automatiques

### Système de Migrations (`migrations.ts`)
**Fonctionnalités :**
- Migration des mots de passe vers bcrypt
- Setup initial des données
- Migration incrémentale
- Rollback support

### Données de Test (`testData.ts`)
**Contenu :**
- 3 utilisateurs par défaut (admin, moderator, employee)
- Annonces d'exemple
- Documents de test
- Événements
- Messages et réclamations
- Formations et cours
- Catégories forum

## Performances et Optimisation

### Stratégies Implémentées
- Connection pooling PostgreSQL
- Cache en mémoire pour storage
- Requêtes optimisées avec indexes
- Pagination automatique
- Lazy loading des relations

### Monitoring et Logs
- Logs structurés par service
- Monitoring WebSocket connexions
- Tracking des performances API
- Error tracking détaillé

## Points d'Intégration Frontend

### Session Management
- Sessions Express avec store sécurisé
- Cookies httpOnly et secure
- Expiration automatique
- Cross-tab synchronization

### API Response Format
```typescript
// Success responses
{ data: T }

// Error responses  
{ 
  message: string, 
  errors?: ValidationError[] 
}

// Statistics responses
{
  totalUsers: number,
  totalAnnouncements: number,
  // ... autres métriques
}
```

### WebSocket Integration
- Path: `/ws`
- Query params: `userId` pour authentification
- JSON message format standardisé
- Automatic reconnection support

## Sécurité et Authentification

### Authentification
- Sessions Express avec store PostgreSQL
- Mots de passe hashés avec bcrypt (12 rounds)
- Protection CSRF
- Rate limiting par endpoint

### Autorisation
- Système de rôles (employee, moderator, admin)
- Permissions granulaires par module
- Validation côté serveur pour toutes les opérations
- Contrôle d'accès par route

### Sécurité Données
- Validation Zod pour tous les inputs
- Sanitization des données
- Protection injection SQL (ORM)
- HTTPS en production

## État Actuel et Limitations

### Points Forts
- API RESTful complète et cohérente
- Architecture modulaire et extensible
- Sécurité robuste
- Support temps réel avec WebSocket
- Système de permissions flexible
- Configuration environment-based

### Améliorations Possibles
- Cache Redis pour scalabilité
- Queue system pour traitement asynchrone
- Monitoring APM (Application Performance Monitoring)
- API rate limiting plus granulaire
- Backup automatique base de données
- Health checks détaillés
- Documentation OpenAPI/Swagger
- Tests d'intégration automatisés

### Compatibilité et Déploiement
- Node.js 18+
- PostgreSQL 12+
- Compatible Docker
- Support cloud providers
- Variables d'environnement standardisées
- Prêt pour CI/CD