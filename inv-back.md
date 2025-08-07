# INVENTAIRE EXHAUSTIF BACKEND - IntraSphere (August 7, 2025)

## ARCHITECTURE GLOBALE

### Structure des Dossiers
```
server/
├── data/
│   └── storage.ts              # Interface IStorage et implémentation MemStorage
├── middleware/
│   └── security.ts             # Middlewares de sécurité
├── routes/
│   └── api.ts                  # 99 endpoints API REST complets
├── services/
│   ├── auth.ts                 # Service d'authentification bcrypt
│   ├── email.ts                # Service d'envoi d'emails
│   └── websocket.ts            # Service WebSocket temps réel
├── config.ts                   # Configuration serveur
├── db.ts                       # Configuration base de données Neon
├── index.ts                    # Point d'entrée serveur Express
├── migrations.ts               # Migrations base de données
├── testData.ts                 # Données de test et échantillons
└── vite.ts                     # Configuration Vite middleware
```

### Technologies Backend
- **Runtime**: Node.js avec Express.js
- **Language**: TypeScript strict avec ES modules
- **Base de données**: PostgreSQL Neon serverless
- **ORM**: Drizzle ORM pour type-safety
- **Validation**: Zod schemas partagés
- **Authentification**: Sessions + bcrypt
- **WebSocket**: ws pour temps réel
- **Email**: Nodemailer service

## ENDPOINTS API COMPLETS (99 endpoints)

### 1. AUTHENTIFICATION (4 endpoints)
1. **POST /api/auth/login** - Connexion utilisateur
2. **POST /api/auth/register** - Inscription utilisateur 
3. **POST /api/auth/logout** - Déconnexion
4. **GET /api/auth/me** - Profil utilisateur actuel

### 2. GESTION UTILISATEURS (8 endpoints)
5. **GET /api/users** - Liste tous les utilisateurs
6. **GET /api/users/:id** - Détails utilisateur spécifique
7. **POST /api/users** - Création utilisateur (admin)
8. **PATCH /api/users/:id** - Mise à jour utilisateur
9. **DELETE /api/users/:id** - Suppression utilisateur (admin)
10. **GET /api/users/directory** - Annuaire employés public
11. **GET /api/users/by-employee-id/:employeeId** - Recherche par ID employé
12. **PATCH /api/users/:id/activate** - Activation/désactivation compte

### 3. DASHBOARD & STATISTIQUES (6 endpoints)
13. **GET /api/dashboard/stats** - Statistiques générales dashboard
14. **GET /api/dashboard/recent-activity** - Activité récente
15. **GET /api/dashboard/notifications** - Notifications utilisateur
16. **GET /api/dashboard/quick-stats** - Stats rapides
17. **GET /api/dashboard/metrics** - Métriques avancées
18. **GET /api/analytics/overview** - Vue d'ensemble analytics

### 4. ANNONCES (5 endpoints)
19. **GET /api/announcements** - Liste toutes les annonces
20. **GET /api/announcements/:id** - Détails annonce spécifique
21. **POST /api/announcements** - Création annonce
22. **PATCH /api/announcements/:id** - Mise à jour annonce
23. **DELETE /api/announcements/:id** - Suppression annonce

### 5. DOCUMENTS (5 endpoints)
24. **GET /api/documents** - Liste tous les documents
25. **GET /api/documents/:id** - Détails document spécifique
26. **POST /api/documents** - Upload nouveau document
27. **PATCH /api/documents/:id** - Mise à jour document
28. **DELETE /api/documents/:id** - Suppression document

### 6. ÉVÉNEMENTS (7 endpoints)
29. **GET /api/events** - Liste tous les événements
30. **GET /api/events/:id** - Détails événement spécifique
31. **POST /api/events** - Création événement
32. **PATCH /api/events/:id** - Mise à jour événement
33. **DELETE /api/events/:id** - Suppression événement
34. **POST /api/events/:id/rsvp** - Inscription événement
35. **GET /api/events/:id/participants** - Liste participants

### 7. MESSAGERIE INTERNE (4 endpoints)
36. **GET /api/messages** - Messages utilisateur connecté
37. **GET /api/messages/:id** - Détails message spécifique
38. **POST /api/messages** - Envoi nouveau message
39. **PATCH /api/messages/:id/read** - Marquer message comme lu

### 8. RÉCLAMATIONS (3 endpoints)
40. **GET /api/complaints** - Liste toutes les réclamations
41. **POST /api/complaints** - Création réclamation
42. **PATCH /api/complaints/:id** - Mise à jour statut réclamation

### 9. PERMISSIONS & DÉLÉGATIONS (4 endpoints)
43. **GET /api/permissions** - Toutes les permissions système
44. **GET /api/permissions/:userId** - Permissions utilisateur spécifique
45. **POST /api/permissions** - Création permission/délégation
46. **DELETE /api/permissions/:id** - Révocation permission

### 10. GESTION DE CONTENU (4 endpoints)
47. **GET /api/contents** - Liste tout le contenu
48. **POST /api/contents** - Création contenu multimédia
49. **PATCH /api/contents/:id** - Mise à jour contenu
50. **DELETE /api/contents/:id** - Suppression contenu

### 11. CATÉGORIES (4 endpoints)
51. **GET /api/categories** - Liste toutes les catégories
52. **POST /api/categories** - Création catégorie
53. **PATCH /api/categories/:id** - Mise à jour catégorie
54. **DELETE /api/categories/:id** - Suppression catégorie

### 12. CATÉGORIES EMPLOYÉS (4 endpoints)
55. **GET /api/employee-categories** - Catégories employés (admin/mod)
56. **POST /api/employee-categories** - Création catégorie (admin)
57. **PATCH /api/employee-categories/:id** - Mise à jour (admin)
58. **DELETE /api/employee-categories/:id** - Suppression (admin)

### 13. PARAMÈTRES SYSTÈME (2 endpoints)
59. **GET /api/system-settings** - Paramètres système (admin/mod)
60. **PATCH /api/system-settings** - Mise à jour paramètres (admin)

### 14. FORMATIONS/TRAININGS (4 endpoints)
61. **GET /api/trainings** - Liste toutes les formations
62. **POST /api/trainings** - Création formation (admin/mod)
63. **PATCH /api/trainings/:id** - Mise à jour formation
64. **DELETE /api/trainings/:id** - Suppression formation (admin)

### 15. PARTICIPANTS FORMATIONS (4 endpoints)
65. **GET /api/training-participants/:trainingId** - Participants formation
66. **POST /api/training-participants** - Inscription formation
67. **PATCH /api/training-participants/:id** - Mise à jour statut
68. **DELETE /api/training-participants/:id** - Désinscription

### 16. PLATEFORME E-LEARNING - COURS (7 endpoints)
69. **GET /api/courses** - Liste tous les cours e-learning
70. **GET /api/courses/:id** - Détails cours spécifique
71. **POST /api/courses** - Création cours (admin/mod)
72. **PUT /api/courses/:id** - Mise à jour cours complète
73. **DELETE /api/courses/:id** - Suppression cours (admin)
74. **GET /api/courses/featured** - Cours mis en avant
75. **GET /api/courses/by-category/:category** - Cours par catégorie

### 17. PLATEFORME E-LEARNING - LEÇONS (4 endpoints)
76. **GET /api/courses/:courseId/lessons** - Leçons d'un cours
77. **GET /api/lessons/:id** - Détails leçon spécifique
78. **POST /api/lessons** - Création leçon (admin/mod)
79. **PUT /api/lessons/:id** - Mise à jour leçon

### 18. INSCRIPTIONS E-LEARNING (2 endpoints)
80. **GET /api/my-enrollments** - Inscriptions utilisateur
81. **POST /api/enroll/:courseId** - Inscription à un cours

### 19. SUIVI PROGRESSION (4 endpoints)
82. **POST /api/lessons/:lessonId/complete** - Marquer leçon terminée
83. **GET /api/courses/:courseId/my-progress** - Progression cours
84. **GET /api/my-progress** - Progression globale utilisateur
85. **GET /api/progress-analytics** - Analytics progression

### 20. RESSOURCES E-LEARNING (3 endpoints)
86. **GET /api/resources** - Toutes les ressources
87. **POST /api/resources** - Création ressource (admin/mod)
88. **PUT /api/resources/:id** - Mise à jour ressource

### 21. CERTIFICATS (1 endpoint)
89. **GET /api/my-certificates** - Certificats utilisateur

### 22. FORUM - CATÉGORIES (4 endpoints)
90. **GET /api/forum/categories** - Catégories forum
91. **POST /api/forum/categories** - Création catégorie
92. **PUT /api/forum/categories/:id** - Mise à jour catégorie
93. **DELETE /api/forum/categories/:id** - Suppression catégorie

### 23. FORUM - SUJETS (4 endpoints)  
94. **GET /api/forum/topics** - Tous les sujets forum
95. **POST /api/forum/topics** - Création nouveau sujet
96. **PUT /api/forum/topics/:id** - Mise à jour sujet
97. **DELETE /api/forum/topics/:id** - Suppression sujet

### 24. ANALYTICS AVANCÉS (2 endpoints)
98. **GET /api/training-analytics** - Analytics formation détaillés
99. **GET /api/analytics/dashboard** - Analytics dashboard admin

## COUCHE DATA/STORAGE (Interface IStorage)

### Interface IStorage Complète (75+ méthodes)

#### Gestion Utilisateurs (11 méthodes)
- `getUser(id: string)` - Récupération utilisateur par ID
- `getUserByUsername(username: string)` - Recherche par nom d'utilisateur
- `getUserByEmployeeId(employeeId: string)` - Recherche par ID employé
- `createUser(user: InsertUser)` - Création nouvel utilisateur
- `updateUser(id: string, user: Partial<User>)` - Mise à jour utilisateur
- `getUsers()` - Liste tous les utilisateurs
- `deleteUser(id: string)` - Suppression utilisateur
- `activateUser(id: string)` - Activation compte
- `deactivateUser(id: string)` - Désactivation compte
- `getUsersByRole(role: string)` - Utilisateurs par rôle
- `getUsersByDepartment(department: string)` - Utilisateurs par département

#### Gestion Annonces (5 méthodes)
- `getAnnouncements()` - Toutes les annonces
- `getAnnouncementById(id: string)` - Annonce spécifique
- `createAnnouncement(announcement: InsertAnnouncement)` - Création
- `updateAnnouncement(id: string, announcement: Partial<Announcement>)` - Mise à jour
- `deleteAnnouncement(id: string)` - Suppression

#### Gestion Documents (5 méthodes)
- `getDocuments()` - Tous les documents
- `getDocumentById(id: string)` - Document spécifique
- `createDocument(document: InsertDocument)` - Création
- `updateDocument(id: string, document: Partial<Document>)` - Mise à jour
- `deleteDocument(id: string)` - Suppression

#### Gestion Événements (5 méthodes)
- `getEvents()` - Tous les événements
- `getEventById(id: string)` - Événement spécifique
- `createEvent(event: InsertEvent)` - Création
- `updateEvent(id: string, event: Partial<Event>)` - Mise à jour
- `deleteEvent(id: string)` - Suppression

#### Messagerie (4 méthodes)
- `getMessages(userId: string)` - Messages utilisateur
- `getMessageById(id: string)` - Message spécifique
- `createMessage(message: InsertMessage)` - Envoi message
- `markMessageAsRead(id: string)` - Marquer comme lu

#### Réclamations (5 méthodes)
- `getComplaints()` - Toutes les réclamations
- `getComplaintById(id: string)` - Réclamation spécifique
- `getComplaintsByUser(userId: string)` - Réclamations par utilisateur
- `createComplaint(complaint: InsertComplaint)` - Création
- `updateComplaint(id: string, complaint: Partial<Complaint>)` - Mise à jour

#### Permissions (4 méthodes)
- `getPermissions(userId: string)` - Permissions utilisateur
- `createPermission(permission: InsertPermission)` - Création permission
- `revokePermission(id: string)` - Révocation permission
- `hasPermission(userId: string, permission: string)` - Vérification permission

#### Contenu (5 méthodes)
- `getContents()` - Tout le contenu
- `getContentById(id: string)` - Contenu spécifique
- `createContent(content: InsertContent)` - Création
- `updateContent(id: string, content: Partial<Content>)` - Mise à jour
- `deleteContent(id: string)` - Suppression

#### Catégories (10 méthodes)
- `getCategories()` - Toutes les catégories
- `getCategoryById(id: string)` - Catégorie spécifique
- `createCategory(category: InsertCategory)` - Création
- `updateCategory(id: string, category: Partial<Category>)` - Mise à jour
- `deleteCategory(id: string)` - Suppression
- `getEmployeeCategories()` - Catégories employés
- `getEmployeeCategoryById(id: string)` - Catégorie employé spécifique
- `createEmployeeCategory(category: InsertEmployeeCategory)` - Création
- `updateEmployeeCategory(id: string, category: Partial<EmployeeCategory>)` - Mise à jour
- `deleteEmployeeCategory(id: string)` - Suppression

#### Paramètres Système (2 méthodes)
- `getSystemSettings()` - Paramètres système
- `updateSystemSettings(settings: Partial<SystemSettings>)` - Mise à jour

#### Plateforme E-Learning (35+ méthodes)
**Formations**:
- `getTrainings()` - Toutes les formations
- `getTrainingById(id: string)` - Formation spécifique
- `createTraining(training: InsertTraining)` - Création
- `updateTraining(id: string, training: Partial<Training>)` - Mise à jour
- `deleteTraining(id: string)` - Suppression

**Cours**:
- `getCourses()` - Tous les cours
- `getCourseById(id: string)` - Cours spécifique
- `createCourse(course: InsertCourse)` - Création
- `updateCourse(id: string, course: Partial<Course>)` - Mise à jour
- `deleteCourse(id: string)` - Suppression
- `getFeaturedCourses()` - Cours mis en avant
- `getCoursesByCategory(category: string)` - Cours par catégorie

**Leçons**:
- `getLessons(courseId: string)` - Leçons d'un cours
- `getLessonById(id: string)` - Leçon spécifique
- `createLesson(lesson: InsertLesson)` - Création
- `updateLesson(id: string, lesson: Partial<Lesson>)` - Mise à jour
- `deleteLesson(id: string)` - Suppression

**Inscriptions & Progression**:
- `getUserEnrollments(userId: string)` - Inscriptions utilisateur
- `enrollUser(userId: string, courseId: string)` - Inscription cours
- `updateLessonProgress(userId: string, lessonId: string, courseId: string, completed: boolean)` - Progression leçon
- `getUserLessonProgress(userId: string, courseId: string)` - Progression cours
- `getUserProgress(userId: string)` - Progression globale

**Ressources**:
- `getResources()` - Toutes les ressources
- `getResourceById(id: string)` - Ressource spécifique
- `createResource(resource: InsertResource)` - Création
- `updateResource(id: string, resource: Partial<Resource>)` - Mise à jour
- `deleteResource(id: string)` - Suppression

**Certificats**:
- `getUserCertificates(userId: string)` - Certificats utilisateur
- `generateCertificate(userId: string, courseId: string)` - Génération certificat

#### Forum (15+ méthodes)
**Catégories**:
- `getForumCategories()` - Catégories forum
- `getForumCategoryById(id: string)` - Catégorie spécifique
- `createForumCategory(category: InsertForumCategory)` - Création
- `updateForumCategory(id: string, category: Partial<ForumCategory>)` - Mise à jour
- `deleteForumCategory(id: string)` - Suppression

**Sujets**:
- `getForumTopics(categoryId?: string)` - Sujets forum
- `getForumTopicById(id: string)` - Sujet spécifique
- `createForumTopic(topic: InsertForumTopic)` - Création
- `updateForumTopic(id: string, topic: Partial<ForumTopic>)` - Mise à jour
- `deleteForumTopic(id: string)` - Suppression

**Posts & Interactions**:
- `getForumPosts(topicId: string)` - Posts d'un sujet
- `createForumPost(post: InsertForumPost)` - Création post
- `updateForumPost(id: string, post: Partial<ForumPost>)` - Mise à jour
- `deleteForumPost(id: string)` - Suppression
- `likeForumPost(userId: string, postId: string)` - Like post
- `getForumUserStats(userId: string)` - Statistiques utilisateur forum

## SERVICES BACKEND (3 services)

### 1. AuthService (auth.ts)
- **Fonctionnalités**: Hash/vérification passwords bcrypt, gestion sessions
- **Méthodes principales**:
  - `hashPassword(password: string)` - Hash sécurisé
  - `verifyPassword(password: string, hash: string)` - Vérification
  - `generateSessionToken()` - Génération tokens session
  - Intégration Express sessions

### 2. EmailService (email.ts)  
- **Fonctionnalités**: Envoi emails notifications, bienvenue, confirmations
- **Méthodes principales**:
  - `sendWelcomeEmail(email: string, name: string)` - Email bienvenue
  - `sendNotificationEmail(email: string, subject: string, content: string)` - Notifications
  - `sendPasswordResetEmail(email: string, token: string)` - Reset password
  - Configuration Nodemailer SMTP

### 3. WebSocketService (websocket.ts)
- **Fonctionnalités**: Communication temps réel, notifications push
- **Méthodes principales**:
  - `broadcast(message: any)` - Diffusion globale
  - `sendToUser(userId: string, message: any)` - Message privé
  - `notifyNewAnnouncement(announcement: any)` - Notification annonce
  - `notifyNewMessage(message: any)` - Notification message
  - Gestion connexions/déconnexions utilisateurs

## MIDDLEWARES DE SÉCURITÉ (security.ts)

### Middlewares d'Authentification
- **requireAuth**: Vérification session utilisateur connecté
- **requireRole(roles: string[])**: Contrôle d'accès par rôles
- **validateRequest**: Validation données entrantes Zod

### Sécurité Générale
- **Helmet**: Headers sécurité HTTP
- **Rate Limiting**: Limitation requêtes par IP
- **CORS**: Configuration domaines autorisés
- **Session Security**: Configuration sessions sécurisées
- **Request Logging**: Logs détaillés requêtes/erreurs

## CONFIGURATION & INFRASTRUCTURE

### Configuration Base de Données (db.ts)
- **Pool de connexions**: Neon PostgreSQL serverless
- **WebSocket constructor**: Configuration ws pour Neon
- **Drizzle ORM**: Configuration avec schémas typés
- **Variables d'environnement**: DATABASE_URL requise
- **Gestion d'erreurs**: Validation connexion DB

### Migrations (migrations.ts)
- **Migration automatique**: Passwords bcrypt au démarrage
- **Données de test**: Injection testData.ts
- **Validation schéma**: Vérification structure DB
- **Logs migrations**: Feedback détaillé processus

### Configuration Serveur (config.ts)
- **Variables d'environnement**: PORT, NODE_ENV, SESSION_SECRET
- **Configuration sessions**: Sécurité et durée de vie
- **Configuration CORS**: Domaines et headers autorisés
- **Configuration logging**: Niveaux et formats logs

### Intégration Vite (vite.ts)
- **Middleware mode**: Intégration serveur dev Vite
- **HMR**: Hot Module Replacement
- **Static serving**: Service fichiers statiques
- **Template transformation**: Gestion index.html
- **Error handling**: Gestion erreurs Vite SSR

## SCHÉMAS ET VALIDATION (shared/schema.ts)

### Tables PostgreSQL (25 tables)
1. **users** - Utilisateurs avec profils employés étendus
2. **announcements** - Annonces avec types et importance
3. **documents** - Documents avec versioning
4. **events** - Événements avec organisateurs
5. **messages** - Messagerie interne
6. **complaints** - Réclamations avec statuts
7. **permissions** - Délégations et permissions
8. **contents** - Contenu multimédia
9. **categories** - Catégories contenu
10. **employeeCategories** - Types d'employés
11. **systemSettings** - Configuration système
12. **trainings** - Formations présentielles
13. **trainingParticipants** - Participants formations
14. **courses** - Cours e-learning
15. **lessons** - Leçons des cours
16. **quizzes** - Quiz et évaluations
17. **enrollments** - Inscriptions cours
18. **lessonProgress** - Progression leçons
19. **quizAttempts** - Tentatives quiz
20. **certificates** - Certificats obtenus
21. **resources** - Ressources pédagogiques
22. **forumCategories** - Catégories forum
23. **forumTopics** - Sujets forum
24. **forumPosts** - Posts forum
25. **forumLikes** - Likes/réactions forum

### Schemas Zod de Validation (25+ schémas)
- **Insert schemas**: Validation création entités
- **Update schemas**: Validation modifications partielles
- **Relations**: Gestion relations entre entités
- **Extended validation**: Règles métier personnalisées
- **Type inference**: Types TypeScript automatiques

## DONNÉES DE TEST (testData.ts)

### Utilisateurs de Test (5 utilisateurs)
- **admin** (Marie Dupont) - Directrice Générale
- **moderator** (Pierre Martin) - Responsable RH  
- **employee** (Sophie Bernard) - Développeuse
- **jdoe** (Jean Doe) - Chef de projet Marketing
- **adurand** (Alice Durand) - Commerciale

### Annonces de Test (5+ annonces)
- Politique télétravail, formations obligatoires, réunions
- Nouveaux arrivants, maintenance serveurs
- Types: important, formation, event, info

### Documents de Test (5+ documents)
- Règlement intérieur, politique sécurité
- Guides procédures, fiches techniques
- Catégories: regulation, policy, guide, procedure

### Événements de Test (5+ événements)
- Réunions équipe, formations, événements sociaux
- Types: meeting, training, social, other

## MÉTRIQUES BACKEND

- **Total lignes de code**: 5,703 lignes TypeScript
- **Endpoints API**: 99 endpoints REST complets
- **Méthodes Storage**: 75+ méthodes interface IStorage
- **Services**: 3 services métier (Auth, Email, WebSocket)
- **Middlewares**: 5+ middlewares sécurité
- **Tables DB**: 25 tables PostgreSQL
- **Schémas Validation**: 25+ schémas Zod
- **Utilisateurs test**: 5 profils différents
- **Gestion erreurs**: Logging complet et structured

## POINTS FORTS ARCHITECTURE BACKEND

1. **API REST complète** - 99 endpoints couvrant tous les besoins
2. **Type Safety strict** - TypeScript + Drizzle ORM + Zod
3. **Architecture en couches** - Services, Storage, Routes séparés
4. **Sécurité robuste** - Sessions, bcrypt, rate limiting, CORS
5. **Temps réel** - WebSocket intégré pour notifications push
6. **Scalabilité** - Pattern Storage interface, Neon serverless
7. **Validation stricte** - Zod schemas partagés frontend/backend
8. **Logging avancé** - Traces détaillées erreurs et requêtes
9. **Tests intégrés** - Données de test et utilisateurs prêts
10. **E-Learning complet** - Plateforme formation avec progression

## COMPATIBILITÉ & INTÉGRATIONS

- **Frontend**: Types partagés shared/schema.ts
- **Database**: PostgreSQL Neon avec migrations automatiques  
- **Authentication**: Sessions Express avec bcrypt
- **Real-time**: WebSocket pour notifications instantanées
- **Email**: Nodemailer pour communications automatiques
- **Security**: Helmet + Rate limiting + CORS configurés
- **Development**: Vite middleware intégré HMR
- **Production**: Configuration environnement flexible