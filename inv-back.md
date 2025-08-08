# 📋 INVENTAIRE EXHAUSTIF - BACKEND PHP

## 🏗️ ARCHITECTURE GÉNÉRALE

### Structure des dossiers
```
php-migration/
├── index.php                     # Point d'entrée + routage (180 lignes)
├── config/                       # Configuration système
│   ├── bootstrap.php             # Autoloader et initialisation (56 lignes)
│   ├── database.php             # Connexion PDO singleton (83 lignes)
│   └── app.php                  # Configuration générale (113 lignes)
├── src/
│   ├── Router.php               # Système de routage (78 lignes)
│   ├── controllers/             # Contrôleurs MVC
│   │   ├── BaseController.php   # Contrôleur de base (125+ lignes)
│   │   ├── AdminController.php  # Administration générale
│   │   ├── AnnouncementsController.php # Gestion annonces
│   │   ├── DashboardController.php     # Tableau de bord
│   │   ├── DocumentsController.php     # Gestion documentaire
│   │   ├── MessagesController.php      # Messagerie interne
│   │   ├── TrainingsController.php     # Formations
│   │   ├── UploadController.php        # Upload fichiers
│   │   └── Api/                 # Contrôleurs API REST
│   │       ├── AdminController.php     # API Administration
│   │       ├── AnnouncementsController.php # API Annonces
│   │       ├── AuthController.php      # API Authentification
│   │       ├── ComplaintsController.php    # API Réclamations
│   │       ├── DocumentsController.php     # API Documents
│   │       ├── EventsController.php        # API Événements
│   │       ├── MessagesController.php      # API Messages
│   │       ├── TrainingsController.php     # API Formations
│   │       └── UsersController.php         # API Utilisateurs
│   ├── models/                  # Modèles de données
│   │   ├── BaseModel.php        # Modèle de base CRUD (125 lignes)
│   │   ├── User.php             # Modèle utilisateur
│   │   ├── Announcement.php     # Modèle annonce
│   │   ├── Document.php         # Modèle document
│   │   ├── Event.php            # Modèle événement
│   │   ├── Message.php          # Modèle message
│   │   ├── Training.php         # Modèle formation
│   │   ├── Complaint.php        # Modèle réclamation
│   │   ├── Permission.php       # Modèle permission
│   │   └── Content.php          # Modèle contenu multimédia
│   └── utils/                   # Utilitaires système
│       ├── helpers.php          # Fonctions globales
│       ├── CacheManager.php     # Gestion du cache
│       ├── Logger.php           # Système de logs
│       ├── PasswordValidator.php    # Validation mots de passe
│       ├── PermissionManager.php    # Gestion permissions
│       └── RateLimiter.php          # Protection anti-brute force
├── sql/
│   └── create_tables.sql        # Script création BDD (400+ lignes)
└── public/
    └── uploads/                 # Fichiers uploadés
```

## 🔧 SYSTÈME DE ROUTAGE

### Router Principal (src/Router.php)
**Fonctionnalités :**
- Routage RESTful avec paramètres dynamiques
- Support des méthodes HTTP (GET, POST, PUT, DELETE, PATCH)
- Conversion automatique des patterns `:param` en regex
- Gestion des namespaces (Api\Controller)
- Handler 404 personnalisable
- Dispatch automatique avec gestion d'erreurs

**Routes définies dans index.php :**
```php
// Routes d'authentification (4 routes)
GET  /                          # Page d'accueil/login
GET  /login                     # Formulaire de connexion
POST /login                     # Traitement connexion
POST /logout                    # Déconnexion
GET  /dashboard                 # Tableau de bord

// API Authentification (3 endpoints)
GET  /api/auth/me              # Profil utilisateur
POST /api/auth/login           # Connexion API
POST /api/auth/logout          # Déconnexion API

// API Utilisateurs (5 endpoints)
GET    /api/users              # Liste utilisateurs
GET    /api/users/:id          # Détail utilisateur
POST   /api/users              # Créer utilisateur
PATCH  /api/users/:id          # Modifier utilisateur
DELETE /api/users/:id          # Supprimer utilisateur

// API Annonces (10 endpoints complets)
GET    /api/announcements      # Liste annonces
GET    /api/announcements/:id  # Détail annonce
POST   /api/announcements      # Créer annonce
PUT    /api/announcements/:id  # Modifier annonce
DELETE /api/announcements/:id  # Supprimer annonce
GET    /api/announcements/recent       # Annonces récentes
GET    /api/announcements/important    # Annonces importantes
GET    /api/announcements/categories   # Catégories
GET    /api/announcements/stats        # Statistiques
POST   /api/announcements/bulk-delete  # Suppression masse
```

### Total des routes configurées
- **Pages web :** 25 routes d'interface
- **API REST :** 107 endpoints total
- **Upload système :** 2 routes fichiers
- **Administration :** 15 routes admin spécialisées

## 🗃️ BASE DE DONNÉES

### Architecture BDD (sql/create_tables.sql)
**21 Tables principales :**

#### 1. Table `users` - Gestion des utilisateurs
```sql
CREATE TABLE users (
    id VARCHAR(50) PRIMARY KEY,           # UUID unique
    username VARCHAR(100) UNIQUE NOT NULL, # Nom d'utilisateur
    password VARCHAR(255) NOT NULL,       # Hash bcrypt
    name VARCHAR(255) NOT NULL,          # Nom complet
    role ENUM('employee','moderator','admin'), # Rôle système
    avatar TEXT,                         # URL avatar
    employee_id VARCHAR(50) UNIQUE,      # ID employé
    department VARCHAR(255),             # Département
    position VARCHAR(255),               # Poste
    is_active BOOLEAN DEFAULT TRUE,      # Compte actif
    phone VARCHAR(50),                   # Téléphone
    email VARCHAR(255),                  # Email
    created_at TIMESTAMP,                # Date création
    updated_at TIMESTAMP                 # Date modification
);
```

#### 2. Table `announcements` - Système d'annonces
```sql
CREATE TABLE announcements (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,         # Titre
    content TEXT NOT NULL,               # Contenu HTML
    type ENUM('info','important','event','formation'), # Type
    author_id VARCHAR(50),               # Auteur (FK users)
    author_name VARCHAR(255) NOT NULL,   # Nom auteur
    image_url TEXT,                      # Image d'illustration
    icon VARCHAR(10) DEFAULT '📢',       # Icône emoji
    is_important BOOLEAN DEFAULT FALSE,  # Épinglée
    created_at TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);
```

#### 3. Table `documents` - Gestion documentaire
```sql
CREATE TABLE documents (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('regulation','policy','guide','procedure'),
    file_name VARCHAR(255) NOT NULL,     # Nom fichier original
    file_url TEXT NOT NULL,              # URL de stockage
    version VARCHAR(20) DEFAULT '1.0',   # Version document
    updated_at TIMESTAMP
);
```

#### 4. Table `events` - Calendrier événements
```sql
CREATE TABLE events (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    date DATETIME NOT NULL,              # Date/heure événement
    location VARCHAR(255),               # Lieu
    type ENUM('meeting','training','social','other'),
    organizer_id VARCHAR(50),            # Organisateur (FK)
    created_at TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id)
);
```

#### 5. Table `messages` - Messagerie interne
```sql
CREATE TABLE messages (
    id VARCHAR(50) PRIMARY KEY,
    sender_id VARCHAR(50),               # Expéditeur (FK)
    recipient_id VARCHAR(50),            # Destinataire (FK)
    subject VARCHAR(255) NOT NULL,       # Sujet
    content TEXT NOT NULL,               # Corps du message
    is_read BOOLEAN DEFAULT FALSE,       # Lu/non lu
    created_at TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (recipient_id) REFERENCES users(id)
);
```

#### 6. Table `complaints` - Système de réclamations
```sql
CREATE TABLE complaints (
    id VARCHAR(50) PRIMARY KEY,
    submitter_id VARCHAR(50),            # Demandeur (FK)
    assigned_to_id VARCHAR(50),          # Assigné à (FK)
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),               # Catégorie libre
    priority ENUM('low','medium','high','urgent'),
    status ENUM('open','in_progress','resolved','closed'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (submitter_id) REFERENCES users(id),
    FOREIGN KEY (assigned_to_id) REFERENCES users(id)
);
```

#### 7. Table `permissions` - RBAC granulaire
```sql
CREATE TABLE permissions (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,       # Utilisateur (FK)
    permission VARCHAR(100) NOT NULL,    # Nom permission
    granted_by VARCHAR(50),              # Accordé par (FK)
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (granted_by) REFERENCES users(id),
    UNIQUE KEY unique_user_permission (user_id, permission)
);
```

#### 8-21. Tables étendues e-learning
- `trainings` - Catalogue formations (181 lignes SQL)
- `training_participants` - Inscriptions formations
- `courses` - Cours détaillés
- `lessons` - Leçons par cours
- `quizzes` - Évaluations
- `quiz_attempts` - Tentatives quiz
- `enrollments` - Inscriptions cours
- `lesson_progress` - Progression leçons
- `certificates` - Certificats obtenus
- `resources` - Ressources pédagogiques
- `forum_categories` - Catégories forum
- `forum_topics` - Sujets discussion
- `forum_posts` - Messages forum
- `forum_likes` - Système de likes

### Index et optimisations
- **Index primaires :** Tous les id en VARCHAR(50)
- **Index uniques :** username, employee_id, user_permission
- **Index composites :** (training_id, user_id) pour performances
- **Clés étrangères :** Relations avec CASCADE/SET NULL appropriées

## 🔐 SYSTÈME D'AUTHENTIFICATION

### BaseController - Sécurité centralisée
**Méthodes d'authentification :**
```php
protected function requireAuth(): array {
    // Vérification session utilisateur
    // Retour données user ou erreur 401
}

protected function requireRole(string $role): array {
    // Hiérarchie des rôles : employee < moderator < admin
    // Validation niveau d'accès
}

protected function requirePermission(string $permission): array {
    // Validation permission granulaire
    // Intégration PermissionManager
}
```

### Gestion des sessions PHP
- **Configuration sécurisée :** HttpOnly, Secure, SameSite
- **Durée de vie :** 1 heure (configurable)
- **Nom session :** INTRASPHERE_SESSION
- **Régénération ID :** À chaque login
- **Cleanup automatique :** Sessions expirées

### Validation des mots de passe
**PasswordValidator.php :**
- Longueur minimum 8 caractères
- Complexité : majuscules, minuscules, chiffres, symboles
- Vérification dictionnaire mots courants
- Hash bcrypt avec salt automatique
- Validation force mot de passe (score 0-4)

## 📊 MODÈLES DE DONNÉES

### BaseModel - CRUD générique
**Fonctionnalités communes :**
```php
abstract class BaseModel {
    protected Database $db;              # Instance singleton BDD
    protected string $table;             # Nom de la table
    protected string $primaryKey = 'id'; # Clé primaire
    
    public function find($id)            # Recherche par ID
    public function findAll(): array     # Tous les enregistrements
    public function create(array $data): array  # Création
    public function update($id, array $data): array # Modification
    public function delete($id): bool    # Suppression
    public function count(): int         # Comptage
    public function where(array $conditions): array # Recherche conditionnelle
    protected function generateUUID(): string       # UUID unique
    protected function validate(array $data): array # Validation données
}
```

### Modèles spécialisés
#### User.php - Gestion utilisateurs
**Méthodes étendues :**
- `findByUsername(string $username)` - Recherche par login
- `findByEmployeeId(string $id)` - Recherche par ID employé
- `authenticate(string $username, string $password)` - Authentification
- `updateLastLogin(string $userId)` - Mise à jour dernière connexion
- `getActiveUsers()` - Utilisateurs actifs seulement
- `getUsersByRole(string $role)` - Filtrage par rôle
- `getUsersByDepartment(string $dept)` - Filtrage par département

#### Announcement.php - Gestion annonces
**Fonctionnalités :**
- `getByType(string $type)` - Filtrage par type
- `getImportant()` - Annonces épinglées
- `getRecent(int $days = 7)` - Annonces récentes
- `search(string $query)` - Recherche textuelle
- `getByAuthor(string $authorId)` - Par auteur
- `getStats()` - Statistiques d'usage

#### Document.php - Bibliothèque documentaire
**Méthodes spécialisées :**
- `getByCategory(string $category)` - Par catégorie
- `searchContent(string $query)` - Recherche full-text
- `getRecentVersions()` - Dernières versions
- `getByVersion(string $docId, string $version)` - Version spécifique
- `updateVersion(string $id)` - Incrémentation version

## 🛠️ CONTRÔLEURS API

### Api/AuthController.php - Authentification REST
**Endpoints implémentés :**
```php
public function me()           # GET /api/auth/me - Profil utilisateur
public function login()       # POST /api/auth/login - Connexion
public function logout()      # POST /api/auth/logout - Déconnexion
public function refresh()     # POST /api/auth/refresh - Renouvellement session
```

### Api/UsersController.php - Gestion utilisateurs
**CRUD complet :**
```php
public function index()       # GET /api/users - Liste paginée
public function show($id)     # GET /api/users/:id - Détail
public function create()     # POST /api/users - Création
public function update($id)   # PATCH /api/users/:id - Modification
public function delete($id)   # DELETE /api/users/:id - Suppression
```

### Api/AnnouncementsController.php - Gestion annonces
**10 endpoints spécialisés :**
```php
public function index()         # Liste avec filtres et pagination
public function show($id)       # Détail avec métadonnées
public function create()        # Création avec validation
public function update($id)     # Modification partielle/complète
public function delete($id)     # Suppression avec vérifications
public function recent()        # Annonces des 7 derniers jours
public function important()     # Annonces épinglées seulement
public function categories()    # Types disponibles
public function stats()         # Statistiques globales
public function bulkDelete()    # Suppression en masse
```

### Api/DocumentsController.php - Gestion documentaire
**Fonctionnalités étendues :**
- Upload avec validation type/taille
- Génération thumbnails automatique
- Versioning avec historique
- Téléchargement sécurisé avec logs
- Recherche métadonnées et contenu
- Statistiques d'utilisation

### Api/MessagesController.php - Messagerie
**11 endpoints messagerie :**
```php
public function index()           # Boîte de réception
public function show($id)         # Détail message
public function create()          # Envoi nouveau message
public function delete($id)       # Suppression message
public function markAsRead($id)   # Marquer comme lu
public function unreadCount()     # Nombre non lus
public function conversations()   # Liste conversations
public function conversation($userId) # Fil de discussion
public function bulkRead()        # Marquer plusieurs comme lus
public function deleteConversation($userId) # Supprimer conversation
public function stats()           # Statistiques messagerie
```

## 🔧 UTILITAIRES SYSTÈME

### CacheManager.php - Gestion du cache
**Fonctionnalités :**
- Cache en mémoire PHP (APCu si disponible)
- TTL configurable par entrée
- Invalidation sélective par pattern
- Statistiques d'utilisation cache
- Compression automatique gros objets

### Logger.php - Système de logs
**Niveaux de log :**
- DEBUG - Informations développement
- INFO - Événements normaux
- WARNING - Situations inhabituelles
- ERROR - Erreurs récupérables
- CRITICAL - Erreurs système critiques

**Rotation automatique :**
- Fichiers journaliers
- Compression anciens logs
- Nettoyage automatique après X jours
- Intégration syslog système

### RateLimiter.php - Protection brute force
**Mécanismes :**
- Limitation par IP et par utilisateur
- Fenêtre glissante configurable
- Blocage temporaire progressif
- Whitelist IP de confiance
- Intégration avec système d'alerte

### PermissionManager.php - RBAC avancé
**Gestion des droits :**
```php
public static function hasPermission($user, $permission): bool
public static function grantPermission($userId, $permission): bool
public static function revokePermission($userId, $permission): bool
public static function getUserPermissions($userId): array
public static function getPermissionGroups(): array
```

**Permissions système définies :**
- `manage_announcements` - Gestion des annonces
- `manage_documents` - Gestion des documents
- `manage_events` - Gestion des événements
- `manage_users` - Administration utilisateurs
- `manage_trainings` - Gestion des formations
- `validate_topics` - Modération forum
- `validate_posts` - Validation contenus
- `manage_employee_categories` - Catégories RH

## 📡 API REST COMPLÈTE

### Standards et conventions
- **Format JSON :** Toutes les réponses en UTF-8
- **Codes HTTP :** Respect des standards (200, 201, 400, 401, 403, 404, 500)
- **Headers CORS :** Configuration multi-origine
- **Rate limiting :** Protection anti-spam
- **Documentation :** Format OpenAPI 3.0

### Réponses API standardisées
```json
{
    "status": "success|error",
    "data": {},
    "message": "Description human-readable",
    "pagination": {
        "page": 1,
        "limit": 20,
        "total": 150,
        "pages": 8
    }
}
```

### Authentification API
- **Session-based :** Utilisation sessions PHP existantes
- **CSRF Protection :** Token sur requêtes mutantes
- **Rate limiting :** 1000 req/heure par utilisateur authentifié
- **API Keys :** Support optionnel pour intégrations tierces

## 💾 CONFIGURATION SYSTÈME

### config/app.php - Paramètres globaux
**Constantes définies :**
```php
define('APP_NAME', 'IntraSphere');
define('APP_VERSION', '2.0.0-PHP');
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost');
define('SECRET_KEY', $_ENV['SECRET_KEY'] ?? 'changeme');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('SESSION_LIFETIME', 3600); // 1 heure
define('DEFAULT_PAGE_SIZE', 20);
define('CACHE_TTL', 300); // 5 minutes
```

**Types de fichiers autorisés :**
- Documents : PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX
- Images : JPG, JPEG, PNG, GIF, WEBP
- Vidéos : MP4, AVI, MOV, WMV
- Audio : MP3, WAV, FLAC
- Archives : ZIP, RAR, 7Z, TAR, GZ

### config/database.php - Connexion BDD
**Multi-SGBD support :**
- MySQL 8.0+ avec charset utf8mb4
- PostgreSQL 12+ avec extensions
- Configuration via variables environnement
- Pool de connexions (si supporté)
- Retry automatique en cas d'échec

## 🔍 SÉCURITÉ ET VALIDATION

### Headers de sécurité (index.php)
```php
header('X-Content-Type-Options: nosniff');     # Protection MIME
header('X-Frame-Options: DENY');               # Protection clickjacking
header('X-XSS-Protection: 1; mode=block');     # Protection XSS legacy
header('Strict-Transport-Security: max-age=31536000'); # HSTS
```

### Validation des données
**Sanitisation automatique :**
- Échappement HTML sur toutes les entrées
- Validation format email/téléphone/URL
- Protection injection SQL via PDO prepare
- Nettoyage récursif des tableaux
- Longueur maximale des champs

### Audit et logs
- Tentatives de connexion (succès/échec)
- Actions administratives
- Modifications de données sensibles
- Erreurs système et exceptions
- Performance et statistiques d'usage

## 📊 PERFORMANCE ET OPTIMISATION

### Optimisations base de données
- Index sur colonnes de recherche fréquente
- Requêtes préparées systématiquement
- LIMIT/OFFSET pour pagination
- Jointures optimisées
- Cache de requêtes activé

### Optimisations PHP
- OPcache activé en production
- Autoloader optimisé
- Singleton pour connexion BDD
- Mise en cache objets fréquents
- Compression gzip des réponses

### Monitoring système
- Temps d'exécution des requêtes
- Consommation mémoire PHP
- Statistiques de cache
- Erreurs et exceptions
- Charge serveur

## 🧪 TESTS ET MAINTENANCE

### Validation données
- Tests unitaires sur modèles
- Validation contraintes BDD
- Tests d'intégration API
- Simulations charge
- Tests de sécurité (injections)

### Maintenance automatique
- Nettoyage sessions expirées
- Rotation des logs
- Optimisation tables BDD
- Sauvegarde incrémentale
- Monitoring santé système

---

## 📈 RÉSUMÉ QUANTITATIF BACKEND

**Fichiers PHP totaux :** 35+ fichiers backend
**Classes principales :** 25+ classes métier
**Méthodes publiques :** 200+ méthodes API/Web
**Tables base de données :** 21 tables complètes
**Endpoints API REST :** 107 endpoints fonctionnels
**Routes web :** 25 routes interface
**Utilitaires système :** 8 modules d'aide
**Lignes de code total :** 5000+ lignes PHP
**Constantes configuration :** 50+ paramètres
**Permissions RBAC :** 8 permissions granulaires
**Types de fichiers :** 15+ formats supportés
**Headers de sécurité :** 4 headers obligatoires
**Niveaux de logs :** 5 niveaux avec rotation

**Total estimated backend complexity :** ⭐⭐⭐⭐⭐ (Très élevée - Entreprise grade)