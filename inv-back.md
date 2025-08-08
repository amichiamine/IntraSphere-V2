# Inventaire Backend IntraSphere - Version PHP

## Architecture Générale Backend PHP

### Technologies
- **Langage**: PHP 8.0+
- **Base de données**: MySQL/PostgreSQL avec PDO
- **Architecture**: MVC Pattern
- **Router**: Router personnalisé
- **Authentification**: Sessions PHP natives
- **Security**: Headers sécurisés, validation des entrées
- **ORM**: PDO avec modèles personnalisés

### Structure des Dossiers
```
php-migration/
├── index.php (Point d'entrée principal)
├── config/ (Configuration)
│   ├── app.php (Configuration générale)
│   ├── database.php (Connexion DB)
│   └── bootstrap.php (Autoloader et initialisation)
├── src/ (Code source)
│   ├── Router.php (Routeur principal)
│   ├── controllers/ (Contrôleurs)
│   │   ├── BaseController.php
│   │   ├── Api/ (Contrôleurs API REST)
│   │   └── [Contrôleurs de pages]
│   ├── models/ (Modèles de données)
│   └── utils/ (Utilitaires)
├── views/ (Templates PHP)
│   ├── layout/ (Layout principal)
│   └── [Vues par module]
└── sql/ (Scripts de base de données)
```

## Configuration (`config/`)

### app.php - Configuration Générale
**Constants Définies:**
- `APP_NAME`: 'IntraSphere'
- `APP_VERSION`: '2.0.0-PHP'
- `APP_ENV`: production/development
- `APP_DEBUG`: Mode debug conditionnel
- `BASE_URL`, `ASSETS_URL`, `UPLOADS_URL`: URLs de base
- `SECRET_KEY`: Clé de sécurité
- `PASSWORD_HASH_ALGO`: Algorithme de hashage
- `DEFAULT_PAGE_SIZE`: 20, `MAX_PAGE_SIZE`: 100
- `MAX_FILE_SIZE`: 50MB
- `ALLOWED_FILE_TYPES`: Extensions autorisées
- `MAIL_FROM`, `MAIL_FROM_NAME`: Configuration email
- `SESSION_LIFETIME`: 3600s, `SESSION_NAME`: 'INTRASPHERE_SESSION'
- `CACHE_ENABLED`: true, `CACHE_TTL`: 300s
- `LOG_ENABLED`: true, `LOG_LEVEL`: DEBUG/ERROR

**Constantes Métier:**
- `USER_ROLES`: employee, moderator, admin
- `PERMISSIONS`: manage_announcements, manage_documents, manage_events, manage_users, manage_trainings, validate_topics, validate_posts, manage_employee_categories
- `CONTENT_TYPES`: video, image, document, audio
- `ANNOUNCEMENT_TYPES`: info, important, event, formation
- `COMPLAINT_PRIORITIES`: low, medium, high, urgent

### database.php - Configuration Base de Données
**Classe `Database` (Singleton):**
- Support MySQL et PostgreSQL
- Configuration via variables d'environnement
- Connexion PDO avec options sécurisées
- Méthodes: `query()`, `fetchAll()`, `fetchOne()`

### bootstrap.php - Initialisation
**Fonctionnalités:**
- Autoloader PSR-4 personnalisé
- Définition des chemins constants
- Configuration timezone (Europe/Paris)
- Configuration sessions sécurisées
- Chargement des helpers globaux

## Routeur (`src/Router.php`)

### Fonctionnalités
- **addRoute()**: Enregistrement des routes avec méthode HTTP
- **dispatch()**: Distribution des requêtes
- **convertPathToPattern()**: Conversion des patterns d'URL
- **callHandler()**: Exécution des contrôleurs
- Support des paramètres d'URL dynamiques
- Gestion des erreurs 404

## Contrôleurs

### BaseController (`src/controllers/BaseController.php`)

#### Méthodes de Réponse
- **json()**: Réponse JSON avec status code
- **error()**: Réponse d'erreur JSON
- **view()**: Rendu de template PHP

#### Sécurité et Authentification
- **requireAuth()**: Vérification authentification
- **requireRole()**: Vérification rôle hiérarchique
- **requirePermission()**: Vérification permission spécifique
- Hiérarchie des rôles: employee(1) < moderator(2) < admin(3)

#### Validation et Sanitisation
- **getJsonInput()**: Récupération données JSON
- **validateRequired()**: Validation champs obligatoires
- **sanitizeInput()**: Nettoyage des entrées
- **validateEmail()**: Validation email
- **validateFileUpload()**: Validation uploads

#### Utilitaires
- **paginate()**: Système de pagination
- **checkRateLimit()**: Limitation de taux
- **logActivity()**: Journalisation des actions

### Contrôleurs de Pages

#### AdminController
**Routes:**
- `GET /admin` → index(): Panneau d'administration
- `GET /admin/users` → users(): Gestion utilisateurs
- `GET /admin/permissions` → permissions(): Gestion permissions
- `GET /admin/system` → system(): Configuration système
- `GET /admin/logs` → logs(): Journaux système

#### AnnouncementsController
**Routes:**
- `GET /announcements` → index(): Liste des annonces
- `GET /announcements/create` → create(): Formulaire création
- `GET /announcements/:id` → show(): Détail annonce
- `GET /announcements/:id/edit` → edit(): Formulaire édition

#### DashboardController
**Routes:**
- `GET /dashboard` → index(): Tableau de bord principal

#### DocumentsController
**Routes:**
- `GET /documents` → index(): Bibliothèque de documents
- Actions CRUD pour les documents

#### MessagesController
**Routes:**
- `GET /messages` → index(): Messagerie interne
- Gestion des conversations

#### TrainingsController
**Routes:**
- `GET /trainings` → index(): Catalogue des formations
- Gestion des inscriptions

#### UploadController
**Fonctionnalités:**
- Upload de fichiers sécurisé
- Validation des types et tailles
- Génération de noms uniques

### Contrôleurs API (`src/controllers/Api/`)

#### AuthController (`Api/AuthController.php`)
**Endpoints:**
- `POST /api/auth/login` → login(): Authentification
- `POST /api/auth/logout` → logout(): Déconnexion
- `GET /api/auth/me` → me(): Profil utilisateur
- `POST /api/auth/register` → register(): Inscription
- `POST /api/auth/change-password` → changePassword(): Changement mot de passe
- `POST /api/auth/forgot-password` → forgotPassword(): Mot de passe oublié

**Sécurité:**
- Rate limiting sur login/forgot-password
- Validation des mots de passe
- Hashage sécurisé
- Journalisation des tentatives

#### AdminController (`Api/AdminController.php`)
**Endpoints:**
- `GET /api/stats` → stats(): Statistiques générales
- `GET /api/permissions` → permissions(): Liste des permissions
- `GET /api/admin/users-overview` → usersOverview(): Vue d'ensemble utilisateurs
- `GET /api/admin/content-overview` → contentOverview(): Vue d'ensemble contenu
- `GET /api/admin/system-info` → systemInfo(): Informations système

**Statistiques Fournies:**
- Compteurs globaux (utilisateurs, annonces, documents, messages, formations)
- Statistiques détaillées (actifs, importants, récents, non lus, à venir)
- Tendances sur 7 jours
- Répartitions par rôle, type, catégorie

#### AnnouncementsController (`Api/AnnouncementsController.php`)
**Endpoints CRUD:**
- `GET /api/announcements` → index(): Liste avec pagination
- `GET /api/announcements/:id` → show(): Détail
- `POST /api/announcements` → create(): Création
- `PUT /api/announcements/:id` → update(): Mise à jour
- `DELETE /api/announcements/:id` → delete(): Suppression

#### ComplaintsController (`Api/ComplaintsController.php`)
**Endpoints:**
- CRUD complet pour réclamations
- Gestion des statuts et priorités
- Attribution aux modérateurs

#### DocumentsController (`Api/DocumentsController.php`)
**Endpoints:**
- CRUD complet pour documents
- Gestion des catégories et versions
- Upload et téléchargement

#### EventsController (`Api/EventsController.php`)
**Endpoints:**
- CRUD complet pour événements
- Gestion des participants
- Calendrier

#### MessagesController (`Api/MessagesController.php`)
**Endpoints:**
- CRUD complet pour messages
- Conversations et threads
- Statuts de lecture

#### TrainingsController (`Api/TrainingsController.php`)
**Endpoints:**
- CRUD complet pour formations
- Gestion des participants
- Système de progression

#### UsersController (`Api/UsersController.php`)
**Endpoints:**
- CRUD complet pour utilisateurs
- Gestion des rôles et permissions
- Annuaire avec recherche

## Modèles (`src/models/`)

### BaseModel (`src/models/BaseModel.php`)
**Fonctionnalités:**
- Pattern Active Record
- Connexion database singleton
- Méthodes CRUD génériques: `find()`, `findAll()`, `create()`, `update()`, `delete()`
- Recherche conditionnelle: `where()`
- Comptage: `count()`
- Génération UUID: `generateUUID()`
- Validation abstraite: `validate()`

### User (`src/models/User.php`)
**Méthodes Spécialisées:**
- `create()`: Création avec hashage mot de passe
- `findByUsername()`: Recherche par nom d'utilisateur
- `findByEmployeeId()`: Recherche par ID employé
- `authenticate()`: Vérification authentification
- `changePassword()`: Changement de mot de passe
- `getDirectory()`: Annuaire des employés actifs
- `search()`: Recherche dans l'annuaire
- `getStats()`: Statistiques utilisateurs
- `toggleStatus()`: Activation/désactivation
- `updateRole()`: Mise à jour du rôle

**Validation:**
- Nom d'utilisateur minimum 3 caractères
- Email valide si fourni
- Mot de passe minimum 6 caractères

### Announcement (`src/models/Announcement.php`)
**Méthodes Spécialisées:**
- `create()`: Création avec valeurs par défaut
- `findAllWithAuthor()`: Liste avec infos auteur
- `getImportant()`: Annonces importantes
- `getByType()`: Filtrage par type
- `getRecent()`: Annonces récentes (7 jours)
- `search()`: Recherche textuelle
- `toggleImportance()`: Basculer importance
- `getStats()`: Statistiques des annonces
- `bulkDelete()`: Suppression en masse

### Document (`src/models/Document.php`)
**Fonctionnalités:**
- Gestion des catégories
- Versioning des documents
- Statistiques d'accès

### Message (`src/models/Message.php`)
**Fonctionnalités:**
- Conversations entre utilisateurs
- Statuts de lecture
- Fil de discussion

### Training (`src/models/Training.php`)
**Fonctionnalités:**
- Gestion des formations
- Participants et inscriptions
- Progression et évaluations

### Event (`src/models/Event.php`)
**Fonctionnalités:**
- Gestion du calendrier
- Participants aux événements
- Types d'événements

### Complaint (`src/models/Complaint.php`)
**Fonctionnalités:**
- Système de réclamations
- Statuts et priorités
- Attribution

### Content (`src/models/Content.php`)
**Fonctionnalités:**
- Contenu multimédia
- Métadonnées et tags
- Système de notation

### Permission (`src/models/Permission.php`)
**Fonctionnalités:**
- Gestion fine des permissions
- Attribution par utilisateur
- Vérifications d'accès

## Vues (`views/`)

### Layout Principal (`views/layout/app.php`)
**Fonctionnalités:**
- Template HTML5 responsive
- Intégration Tailwind CSS CDN
- Thème Glass Morphism complet
- Variables CSS personnalisées
- Mode sombre par défaut
- Navigation avec effet glass
- Sidebar avec backdrop filter
- Inputs stylisés
- Système de grille responsive

**Styles Glass Morphism:**
- `.glass`: Effet de base avec backdrop-filter
- `.glass-card`: Cartes interactives
- `.btn-glass`: Boutons avec effet
- `.nav-glass`: Navigation transparente
- `.sidebar`: Barre latérale
- `.input-glass`: Champs de saisie

### Dashboard (`views/dashboard/index.php`)
**Fonctionnalités:**
- Interface utilisateur moderne
- Statistiques visuelles
- Navigation principale fixe
- Cartes de statistiques animées
- Actions rapides
- Effets d'animation CSS
- Intégration Font Awesome
- Responsive design complet

**Éléments UI:**
- Navigation glassmorphism
- Cartes de statistiques flottantes
- Actions rapides avec hover effects
- Animations CSS (floating)
- Affichage des informations utilisateur

### Autres Vues
- `auth/login.php`: Formulaire de connexion
- `announcements/index.php`: Liste des annonces
- `announcements/create.php`: Création d'annonce
- `documents/index.php`: Bibliothèque documentaire
- `messages/index.php`: Interface de messagerie
- `trainings/index.php`: Catalogue des formations
- `admin/index.php`: Panneau d'administration

## Base de Données (`sql/create_tables.sql`)

### Tables Principales

#### users
**Champs:**
- `id`, `username`, `password`, `name`, `role`
- `avatar`, `employee_id`, `department`, `position`
- `is_active`, `phone`, `email`
- `created_at`, `updated_at`

#### announcements
**Champs:**
- `id`, `title`, `content`, `type`, `author_id`
- `author_name`, `image_url`, `icon`, `is_important`
- `created_at`

#### documents
**Champs:**
- `id`, `title`, `description`, `category`
- `file_name`, `file_url`, `version`
- `updated_at`

#### events
**Champs:**
- `id`, `title`, `description`, `date`
- `location`, `type`, `organizer_id`
- `created_at`

#### messages
**Champs:**
- `id`, `sender_id`, `recipient_id`
- `subject`, `content`, `is_read`
- `created_at`

#### complaints
**Champs:**
- `id`, `submitter_id`, `assigned_to_id`
- `title`, `description`, `category`
- `priority`, `status`
- `created_at`, `updated_at`

#### permissions
**Champs:**
- `id`, `user_id`, `permission`
- `granted_by`, `created_at`

#### contents
**Champs:**
- `id`, `title`, `type`, `category`
- `description`, `file_url`, `thumbnail_url`
- `duration`, `view_count`, `rating`, `tags`
- `is_popular`, `is_featured`
- `created_at`, `updated_at`

#### trainings
**Champs:**
- `id`, `title`, `description`, `category`
- `difficulty`, `duration`, `instructor_id`
- `start_date`, `end_date`, `location`
- `max_participants`, `current_participants`
- `is_mandatory`, `is_active`, `thumbnail_url`
- `created_at`, `updated_at`

### Tables E-Learning Étendues
- `courses`: Cours structurés
- `lessons`: Leçons par cours
- `quizzes`: Évaluations
- `enrollments`: Inscriptions
- `lesson_progress`: Progression
- `quiz_attempts`: Tentatives de quiz
- `certificates`: Certificats

### Tables Forum
- `forum_categories`: Catégories de forum
- `forum_topics`: Sujets de discussion
- `forum_posts`: Messages de forum
- `forum_likes`: Système de likes
- `forum_user_stats`: Statistiques utilisateur

## Utilitaires (`src/utils/`)

### helpers.php
**Fonctions Globales:**
- `h()`: Échappement HTML
- `url()`: Génération d'URLs
- `asset()`: Génération d'URLs d'assets
- `csrf_token()`: Génération de tokens CSRF
- `old()`: Récupération valeurs précédentes
- `redirect()`: Redirection HTTP
- `session_flash()`: Messages flash
- Helpers de formatage de dates
- Utilitaires de validation

## Sécurité

### Mesures Implémentées
- **Headers sécurisés**: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection, HSTS
- **Validation des entrées**: Sanitisation et validation stricte
- **Authentification sécurisée**: Hashage PASSWORD_DEFAULT
- **Sessions sécurisées**: Configuration httponly, secure, strict mode
- **Rate limiting**: Protection contre force brute
- **CSRF protection**: Tokens anti-CSRF
- **SQL Injection**: Requêtes préparées PDO
- **File upload**: Validation stricte des types et tailles

### Rate Limiting
- Login: 5 tentatives par 5 minutes
- Forgot password: 3 tentatives par heure
- Configuration flexible par endpoint

## API REST

### Standards
- **Méthodes HTTP**: GET, POST, PUT, DELETE
- **Réponses JSON**: Structure cohérente
- **Codes de statut**: Usage approprié (200, 201, 400, 401, 403, 404, 500)
- **Pagination**: Offset/limit avec métadonnées
- **Filtrage**: Query parameters pour recherche
- **Validation**: Schémas de validation Zod-like

### Endpoints Complets
- `/api/auth/*`: Authentification complète
- `/api/admin/*`: Administration et statistiques
- `/api/announcements/*`: CRUD annonces
- `/api/documents/*`: CRUD documents
- `/api/events/*`: CRUD événements
- `/api/messages/*`: CRUD messagerie
- `/api/complaints/*`: CRUD réclamations
- `/api/trainings/*`: CRUD formations
- `/api/users/*`: CRUD utilisateurs

## Points d'Amélioration Identifiés
- **Cache**: Implémentation Redis/Memcached
- **Logs**: Système de logging structuré
- **API Documentation**: Swagger/OpenAPI
- **Tests**: PHPUnit pour tests automatisés
- **Migration**: Système de migrations de base de données
- **Queue**: Système de queues pour tâches asynchrones
- **WebSockets**: Temps réel pour messagerie
- **Docker**: Containerisation pour déploiement
- **Monitoring**: Métriques et alertes
- **i18n**: Internationalisation complète