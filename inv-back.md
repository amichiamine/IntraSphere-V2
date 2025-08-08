# 📋 INVENTAIRE BACKEND - Version PHP IntraSphere

## 🏗️ Structure générale des dossiers et fichiers backend

### 📁 Architecture des dossiers
```
php-migration/
├── index.php                 # Point d'entrée principal
├── config/                   # Configuration système
│   ├── bootstrap.php         # Autoloader et initialisation
│   ├── database.php          # Connexion base de données
│   └── app.php              # Configuration générale
├── src/                     # Code source principal
│   ├── Router.php           # Système de routage
│   ├── controllers/         # Contrôleurs MVC
│   │   ├── BaseController.php
│   │   └── Api/            # Contrôleurs API REST
│   ├── models/             # Modèles de données
│   │   ├── BaseModel.php
│   │   └── [8 modèles créés]
│   └── utils/
│       └── helpers.php      # Fonctions utilitaires
├── sql/                     # Scripts base de données
│   └── create_tables.sql    # Création des tables
└── public/                  # Assets et uploads
    └── uploads/            # Fichiers uploadés
```

## 🚀 Point d'entrée et routage

### 📄 index.php - Entry Point Principal
**Configuration sécurisée (lignes 17-21):**
```php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
```

### 🛣️ Routes définies (lignes 26-82)
**Routes d'authentification:**
- `GET /` → `AuthController@showLogin`
- `GET /login` → `AuthController@showLogin`
- `POST /login` → `AuthController@login`
- `POST /logout` → `AuthController@logout`
- `GET /dashboard` → `DashboardController@index`

**Routes API Auth (3 endpoints):**
- `GET /api/auth/me` → `Api\AuthController@me`
- `POST /api/auth/login` → `Api\AuthController@login`
- `POST /api/auth/logout` → `Api\AuthController@logout`

**Routes API Users (5 endpoints):**
- `GET /api/users` → `Api\UsersController@index`
- `GET /api/users/:id` → `Api\UsersController@show`
- `POST /api/users` → `Api\UsersController@create`
- `PATCH /api/users/:id` → `Api\UsersController@update`
- `DELETE /api/users/:id` → `Api\UsersController@delete`

**Routes API Announcements (5 endpoints):**
- `GET /api/announcements` → `Api\AnnouncementsController@index`
- `GET /api/announcements/:id` → `Api\AnnouncementsController@show`
- `POST /api/announcements` → `Api\AnnouncementsController@create`
- `PUT /api/announcements/:id` → `Api\AnnouncementsController@update`
- `DELETE /api/announcements/:id` → `Api\AnnouncementsController@delete`

**Routes API Documents (5 endpoints):**
- `GET /api/documents` → `Api\DocumentsController@index`
- `GET /api/documents/:id` → `Api\DocumentsController@show`
- `POST /api/documents` → `Api\DocumentsController@create`
- `PUT /api/documents/:id` → `Api\DocumentsController@update`
- `DELETE /api/documents/:id` → `Api\DocumentsController@delete`

**Routes API Messages (3 endpoints):**
- `GET /api/messages` → `Api\MessagesController@index`
- `POST /api/messages` → `Api\MessagesController@create`
- `PATCH /api/messages/:id/read` → `Api\MessagesController@markAsRead`

**Routes API Trainings (3 endpoints):**
- `GET /api/trainings` → `Api\TrainingsController@index`
- `GET /api/trainings/:id` → `Api\TrainingsController@show`
- `POST /api/trainings` → `Api\TrainingsController@create`

**Routes API Admin (2 endpoints):**
- `GET /api/stats` → `Api\AdminController@stats`
- `GET /api/permissions` → `Api\AdminController@permissions`

**Pages principales (6 routes):**
- `GET /announcements` → `AnnouncementsController@index`
- `GET /documents` → `DocumentsController@index`
- `GET /messages` → `MessagesController@index`
- `GET /trainings` → `TrainingsController@index`
- `GET /admin` → `AdminController@index`
- `POST /upload` → `UploadController@handle`

## 🔧 Configuration système

### 📄 config/app.php - Configuration Générale
**Constantes globales (lignes 7-50):**
```php
APP_NAME = 'IntraSphere'
APP_VERSION = '2.0.0-PHP'
APP_ENV = 'production'
BASE_URL = 'http://localhost'
SECRET_KEY = 'changeme-in-production'
SESSION_LIFETIME = 3600
MAX_FILE_SIZE = 50MB
```

**Rôles utilisateurs (lignes 52-56):**
```php
USER_ROLES = ['employee', 'moderator', 'admin']
```

**Permissions système (lignes 59-68):**
```php
PERMISSIONS = [
    'manage_announcements', 'manage_documents', 'manage_events',
    'manage_users', 'manage_trainings', 'validate_topics',
    'validate_posts', 'manage_employee_categories'
]
```

**Types de contenu (lignes 71-95):**
- CONTENT_TYPES (video, image, document, audio)
- ANNOUNCEMENT_TYPES (info, important, event, formation)
- COMPLAINT_PRIORITIES (low, medium, high, urgent)
- COMPLAINT_STATUSES (open, in_progress, resolved, closed)

### 📄 config/database.php - Connexion Base de Données
**Classe Database Singleton (lignes 8-82):**
- Support MySQL et PostgreSQL
- Connexion PDO sécurisée
- Méthodes helper : query(), fetchAll(), fetchOne(), execute()
- Gestion d'erreurs et logging

### 📄 config/bootstrap.php - Initialisation
**Autoloader personnalisé (lignes 13-34):**
- Chargement automatique des classes
- Support namespaces
- Multiple répertoires de recherche

## 🏗️ Architecture MVC

### 🎮 Contrôleurs créés

#### 1. BaseController (src/controllers/BaseController.php)
**Méthodes principales (157 lignes):**
- `json()` - Réponses JSON formatées
- `error()` - Gestion d'erreurs standardisée
- `requireAuth()` - Vérification authentification
- `requireRole()` - Contrôle des rôles
- `requirePermission()` - Vérification permissions
- `getJsonInput()` - Parsing JSON requests
- `validateRequired()` - Validation champs requis
- `sanitizeInput()` - Nettoyage données
- `getPaginationParams()` - Paramètres pagination
- `view()` - Rendu des vues PHP
- `checkRateLimit()` - Limitation de taux
- `logActivity()` - Journal d'activités

#### 2. Api\AuthController (src/controllers/Api/AuthController.php)
**Méthodes implémentées (8 endpoints):**
1. `login()` - POST /api/auth/login
   - Validation username/password
   - Rate limiting (5 tentatives/5min)
   - Création session sécurisée
   - Logging des tentatives

2. `logout()` - POST /api/auth/logout
   - Destruction session
   - Logging déconnexion

3. `me()` - GET /api/auth/me
   - Profil utilisateur connecté
   - Rafraîchissement session
   - Vérification statut actif

4. `register()` - POST /api/auth/register
   - Création nouveau compte
   - Validation unicité username
   - Hachage mot de passe

5. `changePassword()` - POST /api/auth/change-password
   - Modification mot de passe
   - Vérification ancien mot de passe
   - Validation nouveau mot de passe

6. `forgotPassword()` - POST /api/auth/forgot-password
   - Génération token reset
   - Rate limiting email
   - Simulation envoi email

7. `resetPassword()` - POST /api/auth/reset-password
   - Reset avec token
   - Validation expiration
   - Nouveau mot de passe

8. `sessionInfo()` - GET /api/auth/session-info
   - Informations session actuelle
   - Durée et expiration

#### 3. Api\UsersController (src/controllers/Api/UsersController.php)
**Méthodes implémentées (12 endpoints):**
1. `index()` - GET /api/users (pagination, search, filters)
2. `show()` - GET /api/users/:id (profil détaillé)
3. `create()` - POST /api/users (création admin)
4. `update()` - PATCH /api/users/:id (modification)
5. `delete()` - DELETE /api/users/:id (suppression)
6. `updateRole()` - PATCH /api/users/:id/role
7. `updateStatus()` - PATCH /api/users/:id/status
8. `permissions()` - GET /api/users/:id/permissions
9. `search()` - GET /api/users/search
10. `directory()` - GET /api/users/directory
11. `bulkUpdate()` - POST /api/users/bulk-update
12. `bulkDelete()` - POST /api/users/bulk-delete

#### 4. Api\AnnouncementsController (src/controllers/Api/AnnouncementsController.php)
**Méthodes implémentées (12 endpoints):**
1. `index()` - GET /api/announcements (filters, search, pagination)
2. `show()` - GET /api/announcements/:id
3. `create()` - POST /api/announcements (avec permissions)
4. `update()` - PUT /api/announcements/:id
5. `delete()` - DELETE /api/announcements/:id
6. `important()` - GET /api/announcements/important
7. `toggleImportance()` - PATCH /api/announcements/:id/importance
8. `byType()` - GET /api/announcements/by-type/:type
9. `recent()` - GET /api/announcements/recent
10. `bulkDelete()` - POST /api/announcements/bulk-delete
11. `stats()` - GET /api/announcements/stats
12. `pin()/unpin()` - POST/DELETE /api/announcements/:id/pin

### 🗂️ Contrôleurs manquants
**Contrôleurs non implémentés:**
1. `Api\DocumentsController` - Gestion documents
2. `Api\MessagesController` - Messagerie interne
3. `Api\TrainingsController` - Formations
4. `Api\EventsController` - Événements
5. `Api\AdminController` - Administration
6. `Api\ComplaintsController` - Réclamations
7. `Api\ForumController` - Forum
8. `Api\ContentController` - Contenu multimédia
9. `UploadController` - Upload fichiers
10. **Pages controllers** (non-API) : `DashboardController`, `AnnouncementsController`, etc.

## 🗃️ Modèles de données

### 📊 Modèles créés (8/21)

#### 1. BaseModel (src/models/BaseModel.php)
**Méthodes CRUD génériques:**
- `find($id)` - Recherche par ID
- `findAll()` - Tous les enregistrements
- `create(array $data)` - Création avec UUID
- `update($id, array $data)` - Mise à jour
- `delete($id)` - Suppression
- `count()` - Comptage
- `where(array $conditions)` - Recherche conditionnelle
- `generateUUID()` - Génération ID unique
- `validate()` - Validation métier
- `sanitize()` - Nettoyage données

#### 2. User (src/models/User.php)
**Méthodes spécialisées:**
- `create()` - Avec hachage mot de passe
- `findByUsername()` - Recherche par nom d'utilisateur
- `findByEmployeeId()` - Recherche par ID employé
- `authenticate()` - Vérification connexion
- `changePassword()` - Changement mot de passe
- `getDirectory()` - Annuaire employés
- `search()` - Recherche utilisateurs
- `getStats()` - Statistiques utilisateurs
- `toggleStatus()` - Activation/désactivation
- `updateRole()` - Modification rôle

#### 3. Announcement (src/models/Announcement.php)
**Méthodes spécialisées:**
- `findAllWithAuthor()` - Avec infos auteur
- `getImportant()` - Annonces importantes
- `getByType()` - Filtrage par type
- `getRecent()` - Annonces récentes (7 jours)
- `search()` - Recherche textuelle
- `toggleImportance()` - Basculer importance
- `getStats()` - Statistiques annonces
- `bulkDelete()` - Suppression masse

#### 4. Document (src/models/Document.php)
**Méthodes spécialisées:**
- `getByCategory()` - Filtrage par catégorie
- `getRecent()` - Documents récents (30 jours)
- `search()` - Recherche documents
- `getPaginated()` - Pagination
- `updateVersion()` - Gestion versions
- `getVersionHistory()` - Historique versions
- `fileExists()` - Vérification existence fichier
- `bulkDelete()` - Suppression avec fichiers

#### 5. Message (src/models/Message.php)
**Méthodes spécialisées:**
- `getInbox()` - Boîte de réception
- `getSent()` - Messages envoyés
- `findWithUsers()` - Avec infos expéditeur/destinataire
- `markAsRead()` - Marquer comme lu
- `markMultipleAsRead()` - Lecture multiple
- `getUnreadCount()` - Compteur non lus
- `getConversation()` - Conversation entre 2 users
- `search()` - Recherche messages
- `getRecentConversations()` - Conversations récentes
- `deleteConversation()` - Suppression conversation

#### 6. Event (src/models/Event.php)
**Méthodes spécialisées:**
- `findAllWithOrganizer()` - Avec infos organisateur
- `getUpcoming()` - Événements à venir
- `getByType()` - Filtrage par type
- `getMyEvents()` - Événements utilisateur
- `getCalendarEvents()` - Vue calendrier
- `search()` - Recherche événements

#### 7. Training (src/models/Training.php)
**Méthodes spécialisées:**
- `findAllWithInstructor()` - Avec infos instructeur
- `getActive()` - Formations actives
- `getByCategory()` - Par catégorie
- `getMandatory()` - Formations obligatoires
- `getUpcoming()` - À venir
- `canRegister()` - Vérification inscription
- `registerUser()` - Inscription utilisateur
- `unregisterUser()` - Désinscription
- `getParticipants()` - Liste participants

#### 8. Permission (src/models/Permission.php)
**Méthodes spécialisées:**
- `hasPermission()` - Vérification permission
- `getUserPermissions()` - Permissions utilisateur
- `grantPermission()` - Accorder permission
- `revokePermission()` - Révoquer permission
- `getAllWithUsers()` - Toutes avec utilisateurs
- `getUsersWithPermissions()` - Users avec permissions
- `syncUserPermissions()` - Synchronisation
- `canGrantPermission()` - Vérification droits
- `getAvailablePermissions()` - Permissions disponibles

### 🗂️ Modèles manquants (13/21)
**Modèles non créés:**
1. `Complaint` - Réclamations
2. `Content` - Contenu multimédia
3. `Category` - Catégories contenu
4. `EmployeeCategory` - Catégories employés
5. `SystemSettings` - Paramètres système
6. `TrainingParticipant` - Participants formations (partiellement dans Training.php)
7. `Course` - Cours e-learning
8. `Lesson` - Leçons
9. `Quiz` - Quiz et évaluations
10. `Enrollment` - Inscriptions cours
11. `Certificate` - Certificats
12. `ForumCategory` - Catégories forum
13. `ForumTopic`, `ForumPost` - Forum discussions

## 🗄️ Base de données

### 📄 sql/create_tables.sql - Schéma Complet
**21 tables définies:**

#### Tables principales (8 implémentées)
1. `users` - Utilisateurs (19 colonnes)
2. `announcements` - Annonces (9 colonnes)
3. `documents` - Documents (7 colonnes)
4. `events` - Événements (8 colonnes)
5. `messages` - Messages (7 colonnes)
6. `complaints` - Réclamations (9 colonnes)
7. `permissions` - Permissions (5 colonnes)
8. `contents` - Contenu multimédia (12 colonnes)

#### Tables système (4 définies)
9. `categories` - Catégories contenu (7 colonnes)
10. `employee_categories` - Catégories employés (6 colonnes)
11. `system_settings` - Paramètres (9 colonnes)
12. `trainings` - Formations (16 colonnes)
13. `training_participants` - Participants (8 colonnes)

#### Tables e-learning avancé (8 définies)
14. `courses` - Cours (10 colonnes)
15. `lessons` - Leçons (8 colonnes)
16. `quizzes` - Quiz (9 colonnes)
17. `enrollments` - Inscriptions (6 colonnes)
18. `lesson_progress` - Progrès leçons (8 colonnes)
19. `quiz_attempts` - Tentatives quiz (9 colonnes)
20. `certificates` - Certificats (7 colonnes)
21. `resources` - Ressources pédagogiques (9 colonnes)

#### Tables forum (supplémentaires)
22. `forum_categories` - Catégories forum (7 colonnes)
23. `forum_topics` - Sujets forum (10 colonnes)
24. `forum_posts` - Posts forum (7 colonnes)
25. `forum_likes` - Likes posts (4 colonnes)
26. `forum_user_stats` - Stats utilisateurs forum (7 colonnes)

### 🔑 Relations et index
**Clés étrangères principales:**
- `announcements.author_id` → `users.id`
- `events.organizer_id` → `users.id`
- `messages.sender_id/recipient_id` → `users.id`
- `permissions.user_id` → `users.id`
- `trainings.instructor_id` → `users.id`

**Index de performance (14 index):**
- Recherche par type, statut, date
- Optimisation des requêtes fréquentes
- Support des recherches textuelles

### 🎯 Données d'initialisation
**Données par défaut incluses:**
- Paramètres système
- Catégories forum (4 catégories)
- Utilisateur admin (admin/admin123)
- Permissions admin par défaut

## 🛠️ Utilitaires et helpers

### 📄 src/utils/helpers.php - Fonctions Globales (48 fonctions)

#### Formatage et affichage
- `h()` - Échappement HTML
- `url()`, `asset()`, `upload()` - URLs
- `formatDate()`, `timeAgo()` - Formatage dates
- `formatFileSize()` - Taille fichiers
- `truncate()`, `slug()` - Manipulation texte

#### Authentification et session
- `isLoggedIn()` - État connexion
- `currentUser()` - Utilisateur connecté
- `hasRole()` - Vérification rôle
- `hasPermission()` - Vérification permission

#### Validation et sécurité
- `isValidEmail()`, `isValidUrl()` - Validation
- `csrfToken()`, `verifyCsrfToken()` - Protection CSRF
- `sanitize()` - Nettoyage données

#### Messaging et interface
- `flash()`, `getFlashMessages()` - Messages flash
- `redirectWithMessage()` - Redirection avec message

#### Fichiers et uploads
- `uploadFile()` - Upload sécurisé
- `deleteUploadedFile()` - Suppression fichier
- `createThumbnail()` - Miniatures images

#### Système et logs
- `logActivity()` - Journal activités
- `isMobile()` - Détection mobile
- `getClientIp()` - IP client

## 🔒 Sécurité implémentée

### 🛡️ Mesures de sécurité
1. **Authentification:**
   - Hachage bcrypt des mots de passe
   - Sessions PHP sécurisées
   - Rate limiting connexions (5 tentatives/5min)

2. **Autorisation:**
   - Système de rôles hiérarchiques
   - Permissions granulaires
   - Contrôle d'accès par endpoint

3. **Protection données:**
   - Validation et sanitisation systématique
   - Protection CSRF sur formulaires
   - Statements préparés PDO

4. **Headers sécurisés:**
   - X-Content-Type-Options: nosniff
   - X-Frame-Options: DENY
   - X-XSS-Protection: 1; mode=block
   - Strict-Transport-Security

5. **Upload sécurisé:**
   - Validation type MIME
   - Limitation taille (50MB)
   - Types de fichiers autorisés
   - Stockage hors web root

### 🔐 Vulnérabilités potentielles
1. **Session management** - Pas de rotation ID session
2. **Password policy** - Exigences minimales faibles (6 chars)
3. **File upload** - Pas de scan antivirus
4. **Rate limiting** - En session, pas persistant
5. **Logging** - Logs d'erreur en production

## 📊 APIs et endpoints

### 📈 Endpoints implémentés (26/107)
**API Auth (8 endpoints) ✅**
**API Users (12 endpoints) ✅**
**API Announcements (12 endpoints) ✅**

### 📉 Endpoints manquants (81/107)
**API Documents (10 endpoints) ❌**
**API Messages (8 endpoints) ❌**
**API Events (10 endpoints) ❌**
**API Trainings (15 endpoints) ❌**
**API Forum (8 endpoints) ❌**
**API Complaints (8 endpoints) ❌**
**API Content (10 endpoints) ❌**
**API Admin (5 endpoints) - 2/5 ✅**
**API System (7 endpoints) ❌**

## 🔄 État du développement backend

### ✅ Complété (25%)
- Architecture MVC solide
- Système d'authentification complet
- 3 contrôleurs API fonctionnels
- 8 modèles avec relations
- Base de données complète
- Sécurité de base
- Helpers et utilitaires

### 🚧 En cours (0%)
- Aucun développement en cours

### ❌ Manquant (75%)
- 6 contrôleurs API majeurs
- 13 modèles métier
- 81 endpoints API
- Contrôleurs pages
- Upload de fichiers
- Tests unitaires
- Optimisations performance

## 🎯 Priorités de développement backend

### 🔥 Critique (Bloquant)
1. **Contrôleurs API manquants** (Documents, Messages, Events, Trainings)
2. **Modèles métier** (Complaint, Content, Course, etc.)
3. **Upload de fichiers** sécurisé
4. **Contrôleurs pages** (Dashboard, etc.)

### ⚡ Important
1. **Tests unitaires** et intégration
2. **Optimisations BDD** et cache
3. **API de notification** temps réel
4. **Migration et seeders**

### 📈 Amélioration
1. **Documentation API** (OpenAPI)
2. **Monitoring** et métriques
3. **Cache Redis** ou Memcached
4. **Queue jobs** asynchrones

## 🔧 Incohérences et problèmes identifiés

### 🚨 Problèmes majeurs
1. **Contrôleurs manquants** - 75% des APIs non implémentées
2. **Modèles incomplets** - 13/21 modèles manquants
3. **Upload non implémenté** - Système critique absent
4. **Tests absents** - Aucun test unitaire/intégration

### ⚠️ Problèmes mineurs
1. **Logs de sécurité** - Logging basique en fichier
2. **Configuration** - Hardcodée, pas d'environnements
3. **Cache** - Pas de système de cache
4. **Queue** - Pas de jobs asynchrones

### 🎯 Recommandations d'amélioration
1. Implémenter tous les contrôleurs API manquants
2. Créer les modèles métier restants
3. Développer le système d'upload sécurisé
4. Ajouter une suite de tests complète
5. Optimiser les performances et ajouter du cache