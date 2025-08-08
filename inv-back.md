# 🔧 INVENTAIRE BACKEND COMPLET - PHP MIGRATION REFERENCE

## 🎯 Vue d'ensemble
- **Total fichiers TypeScript**: 17 fichiers
- **Total endpoints API**: 107 routes RESTful
- **Framework actuel**: Express.js + TypeScript + Node.js
- **ORM**: Drizzle ORM avec PostgreSQL
- **Validation**: Zod schemas partagés
- **Architecture**: Interface storage + implémentation MemStorage
- **Sécurité**: Helmet + bcrypt + sessions + rate limiting

---

## 🏗️ ARCHITECTURE BACKEND

### 🔗 Point d'entrée principal
- **`server/index.ts`**: Bootstrap Express, middleware sécurité, routing, configuration
- **`server/routes/index.ts`**: Registration de tous les modules de routes

### 🔧 Configuration
- **`server/config.ts`**: Configuration serveur et variables d'environnement
- **`server/db.ts`**: Configuration base de données Drizzle ORM
- **`server/vite.ts`**: Intégration Vite pour développement

---

## 📊 SCHÉMA DE BASE DE DONNÉES

### 🗃️ Tables principales (shared/schema.ts)
**13 tables principales avec relations**

1. **users** - Gestion utilisateurs
   - **Champs**: id, username, password, name, role, avatar, employeeId, department, position, isActive, phone, email, createdAt, updatedAt
   - **Relations**: Messages, événements, formations, permissions

2. **announcements** - Annonces entreprise
   - **Champs**: id, title, content, type, authorId, authorName, imageUrl, icon, createdAt, isImportant
   - **Types**: info, important, event, formation

3. **documents** - Gestion documentaire
   - **Champs**: id, title, description, category, fileName, fileUrl, updatedAt, version
   - **Catégories**: regulation, policy, guide, procedure

4. **events** - Événements et calendrier
   - **Champs**: id, title, description, date, location, type, organizerId, createdAt
   - **Types**: meeting, training, social, other

5. **messages** - Messagerie interne
   - **Champs**: id, senderId, recipientId, subject, content, isRead, createdAt
   - **Relations**: Users (sender/recipient)

6. **complaints** - Système de réclamations
   - **Champs**: id, submitterId, assignedToId, title, description, category, priority, status, createdAt, updatedAt
   - **Workflow**: open → in_progress → resolved → closed

7. **permissions** - Système de permissions
   - **Champs**: id, userId, grantedBy, permission, createdAt
   - **Types**: manage_announcements, manage_documents, manage_events, manage_users, validate_topics, validate_posts, manage_employee_categories, manage_trainings

8. **contents** - Bibliothèque de contenu
   - **Champs**: id, title, type, category, description, fileUrl, thumbnailUrl, duration, viewCount, rating, tags, isPopular, isFeatured, createdAt, updatedAt
   - **Types**: video, image, document, audio

9. **categories** - Catégories de contenu
   - **Champs**: id, name, description, icon, color, isVisible, sortOrder, createdAt

10. **employeeCategories** - Catégories d'employés
    - **Champs**: id, name, description, color, permissions, isActive, createdAt

11. **systemSettings** - Paramètres système
    - **Champs**: id, showAnnouncements, showContent, showDocuments, showForum, showMessages, showComplaints, showTraining, updatedAt

12. **trainings** - Formations
    - **Champs**: id, title, description, category, difficulty, duration, instructorId, instructorName, startDate, endDate, location, maxParticipants, currentParticipants, isMandatory, isActive, isVisible, thumbnailUrl, documentUrls, createdAt, updatedAt

13. **trainingParticipants** - Participants formations
    - **Champs**: id, trainingId, userId, registeredAt, status, completionDate, score, feedback

### 🎓 E-Learning Extension (8 tables supplémentaires)
1. **courses** - Cours e-learning
2. **lessons** - Leçons de cours
3. **quizzes** - Quiz et évaluations
4. **enrollments** - Inscriptions cours
5. **lessonProgress** - Progression leçons
6. **quizAttempts** - Tentatives de quiz
7. **certificates** - Certificats obtenus
8. **resources** - Ressources pédagogiques

### 🗣️ Forum System (4 tables)
1. **forumCategories** - Catégories forum
2. **forumTopics** - Sujets de discussion
3. **forumPosts** - Messages forum
4. **forumLikes** - Likes sur posts

---

## 🛣️ SYSTÈME DE ROUTES

### 📁 Modules de routes (6 modules)

#### 🔐 Auth Routes (server/routes/auth.ts)
**6 endpoints d'authentification**
1. **POST /api/auth/login** - Connexion utilisateur
2. **POST /api/auth/register** - Inscription utilisateur
3. **POST /api/auth/logout** - Déconnexion
4. **GET /api/auth/me** - Profil utilisateur connecté
5. **POST /api/auth/change-password** - Changement mot de passe
6. **POST /api/auth/forgot-password** - Mot de passe oublié

#### 👥 Users Routes (server/routes/users.ts)
**12 endpoints de gestion utilisateurs**
1. **GET /api/users** - Liste tous les utilisateurs
2. **GET /api/users/:id** - Détails utilisateur
3. **POST /api/users** - Créer utilisateur (admin)
4. **PATCH /api/users/:id** - Modifier utilisateur
5. **DELETE /api/users/:id** - Supprimer utilisateur (admin)
6. **PATCH /api/users/:id/role** - Changer rôle (admin)
7. **PATCH /api/users/:id/status** - Activer/désactiver utilisateur
8. **GET /api/users/:id/permissions** - Permissions utilisateur
9. **POST /api/users/bulk-update** - Mise à jour en masse
10. **GET /api/users/search** - Recherche utilisateurs
11. **GET /api/users/directory** - Annuaire employés
12. **POST /api/users/bulk-delete** - Suppression en masse (admin)

#### 📢 Content Routes (server/routes/content.ts)
**45 endpoints de gestion contenu**

**Announcements (10 endpoints)**
1. **GET /api/announcements** - Liste annonces
2. **GET /api/announcements/:id** - Détail annonce
3. **POST /api/announcements** - Créer annonce (admin/mod)
4. **PUT /api/announcements/:id** - Modifier annonce
5. **DELETE /api/announcements/:id** - Supprimer annonce
6. **GET /api/announcements/important** - Annonces importantes
7. **PATCH /api/announcements/:id/importance** - Modifier importance
8. **GET /api/announcements/by-type/:type** - Par type
9. **GET /api/announcements/recent** - Récentes
10. **POST /api/announcements/bulk-delete** - Suppression masse

**Documents (10 endpoints)**
11. **GET /api/documents** - Liste documents
12. **GET /api/documents/:id** - Détail document
13. **POST /api/documents** - Upload document
14. **PUT /api/documents/:id** - Modifier document
15. **DELETE /api/documents/:id** - Supprimer document
16. **GET /api/documents/by-category/:category** - Par catégorie
17. **POST /api/documents/upload** - Upload fichier
18. **GET /api/documents/:id/download** - Télécharger
19. **GET /api/documents/recent** - Documents récents
20. **POST /api/documents/bulk-delete** - Suppression masse

**Events (10 endpoints)**
21. **GET /api/events** - Liste événements
22. **GET /api/events/:id** - Détail événement
23. **POST /api/events** - Créer événement
24. **PUT /api/events/:id** - Modifier événement
25. **DELETE /api/events/:id** - Supprimer événement
26. **GET /api/events/upcoming** - Événements à venir
27. **GET /api/events/by-type/:type** - Par type
28. **GET /api/events/calendar** - Vue calendrier
29. **POST /api/events/bulk-delete** - Suppression masse
30. **GET /api/events/my-events** - Mes événements

**Content Library (10 endpoints)**
31. **GET /api/content** - Bibliothèque contenu
32. **GET /api/content/:id** - Détail contenu
33. **POST /api/content** - Ajouter contenu
34. **PUT /api/content/:id** - Modifier contenu
35. **DELETE /api/content/:id** - Supprimer contenu
36. **GET /api/content/featured** - Contenu mis en avant
37. **GET /api/content/popular** - Contenu populaire
38. **PATCH /api/content/:id/view** - Incrémenter vues
39. **POST /api/content/bulk-delete** - Suppression masse
40. **GET /api/content/by-category/:category** - Par catégorie

**Categories (5 endpoints)**
41. **GET /api/categories** - Liste catégories
42. **POST /api/categories** - Créer catégorie
43. **PUT /api/categories/:id** - Modifier catégorie
44. **DELETE /api/categories/:id** - Supprimer catégorie
45. **PATCH /api/categories/:id/visibility** - Changer visibilité

#### 💬 Messaging Routes (server/routes/messaging.ts)
**24 endpoints de communication**

**Messages (8 endpoints)**
1. **GET /api/messages** - Boîte de réception
2. **GET /api/messages/sent** - Messages envoyés
3. **GET /api/messages/:id** - Détail message
4. **POST /api/messages** - Envoyer message
5. **PATCH /api/messages/:id/read** - Marquer lu
6. **DELETE /api/messages/:id** - Supprimer message
7. **GET /api/messages/unread-count** - Nombre non lus
8. **POST /api/messages/bulk-delete** - Suppression masse

**Complaints (8 endpoints)**
9. **GET /api/complaints** - Liste réclamations
10. **GET /api/complaints/:id** - Détail réclamation
11. **POST /api/complaints** - Créer réclamation
12. **PATCH /api/complaints/:id** - Modifier réclamation
13. **PATCH /api/complaints/:id/status** - Changer statut
14. **PATCH /api/complaints/:id/assign** - Assigner réclamation
15. **GET /api/complaints/my-complaints** - Mes réclamations
16. **GET /api/complaints/assigned** - Réclamations assignées

**Forum (8 endpoints)**
17. **GET /api/forum/categories** - Catégories forum
18. **GET /api/forum/topics** - Sujets forum
19. **GET /api/forum/topics/:id** - Détail sujet
20. **POST /api/forum/topics** - Créer sujet
21. **GET /api/forum/topics/:id/posts** - Posts du sujet
22. **POST /api/forum/topics/:id/posts** - Ajouter post
23. **POST /api/forum/posts/:id/like** - Liker post
24. **DELETE /api/forum/posts/:id/like** - Retirer like

#### 🎓 Training Routes (server/routes/training.ts)
**15 endpoints de formation**

**Trainings (10 endpoints)**
1. **GET /api/trainings** - Liste formations
2. **GET /api/trainings/:id** - Détail formation
3. **POST /api/trainings** - Créer formation
4. **PUT /api/trainings/:id** - Modifier formation
5. **DELETE /api/trainings/:id** - Supprimer formation
6. **POST /api/trainings/:id/register** - S'inscrire
7. **DELETE /api/trainings/:id/unregister** - Se désinscrire
8. **GET /api/trainings/:id/participants** - Participants
9. **GET /api/my-trainings** - Mes formations
10. **PATCH /api/trainings/:id/complete** - Marquer complétée

**E-Learning (5 endpoints)**
11. **GET /api/courses** - Catalogue cours
12. **GET /api/courses/:id/lessons** - Leçons cours
13. **GET /api/lessons/:id/resources** - Ressources leçon
14. **POST /api/resources** - Ajouter ressource
15. **GET /api/my-certificates** - Mes certificats

#### 👔 Admin Routes (server/routes/admin.ts)
**5 endpoints d'administration**
1. **GET /api/permissions** - Toutes les permissions
2. **POST /api/permissions** - Créer permission
3. **DELETE /api/permissions/:id** - Révoquer permission
4. **GET /api/stats** - Statistiques système
5. **POST /api/admin/reset-test-data** - Réinitialiser données test

---

## 🔒 SÉCURITÉ ET MIDDLEWARE

### 🛡️ Middleware de sécurité (server/middleware/security.ts)
1. **configureSecurity()** - Configuration Helmet
2. **sanitizeInput()** - Nettoyage des entrées
3. **getSessionConfig()** - Configuration sessions sécurisées
4. **Rate limiting** - Limitation requêtes

### 🔐 Authentification (server/services/auth.ts)
1. **hashPassword()** - Hachage bcrypt
2. **verifyPassword()** - Vérification mot de passe
3. **requireAuth** - Middleware authentification
4. **requireRole** - Middleware autorisation par rôle

### 📧 Services externes (server/services/email.ts)
1. **sendEmail()** - Envoi d'emails
2. **sendPasswordResetEmail()** - Email récupération MDP

---

## 🗃️ COUCHE DE DONNÉES

### 📦 Interface Storage (server/data/storage.ts)
**Interface IStorage avec 95+ méthodes CRUD**

#### 👤 Users (6 méthodes)
1. **getUser(id)** - Récupérer utilisateur
2. **getUserByUsername(username)** - Par nom d'utilisateur
3. **getUserByEmployeeId(employeeId)** - Par ID employé
4. **createUser(user)** - Créer utilisateur
5. **updateUser(id, user)** - Modifier utilisateur
6. **getUsers()** - Liste utilisateurs

#### 📢 Announcements (5 méthodes)
7. **getAnnouncements()** - Liste annonces
8. **getAnnouncementById(id)** - Par ID
9. **createAnnouncement(announcement)** - Créer
10. **updateAnnouncement(id, announcement)** - Modifier
11. **deleteAnnouncement(id)** - Supprimer

#### 📄 Documents (5 méthodes)
12. **getDocuments()** - Liste documents
13. **getDocumentById(id)** - Par ID
14. **createDocument(document)** - Créer
15. **updateDocument(id, document)** - Modifier
16. **deleteDocument(id)** - Supprimer

#### 📅 Events (5 méthodes)
17. **getEvents()** - Liste événements
18. **getEventById(id)** - Par ID
19. **createEvent(event)** - Créer
20. **updateEvent(id, event)** - Modifier
21. **deleteEvent(id)** - Supprimer

#### 💬 Messages (4 méthodes)
22. **getMessages(userId)** - Messages utilisateur
23. **getMessageById(id)** - Par ID
24. **createMessage(message)** - Créer
25. **markMessageAsRead(id)** - Marquer lu

#### 🎫 Complaints (5 méthodes)
26. **getComplaints()** - Liste réclamations
27. **getComplaintById(id)** - Par ID
28. **getComplaintsByUser(userId)** - Par utilisateur
29. **createComplaint(complaint)** - Créer
30. **updateComplaint(id, complaint)** - Modifier

#### 🔑 Permissions (4 méthodes)
31. **getPermissions(userId)** - Permissions utilisateur
32. **createPermission(permission)** - Créer
33. **revokePermission(id)** - Révoquer
34. **hasPermission(userId, permission)** - Vérifier

#### 📦 Contents (5 méthodes)
35. **getContents()** - Liste contenu
36. **getContentById(id)** - Par ID
37. **createContent(content)** - Créer
38. **updateContent(id, content)** - Modifier
39. **deleteContent(id)** - Supprimer

#### 🏷️ Categories (5 méthodes)
40. **getCategories()** - Liste catégories
41. **getCategoryById(id)** - Par ID
42. **createCategory(category)** - Créer
43. **updateCategory(id, category)** - Modifier
44. **deleteCategory(id)** - Supprimer

#### 👥 Employee Categories (5 méthodes)
45. **getEmployeeCategories()** - Liste catégories employés
46. **getEmployeeCategoryById(id)** - Par ID
47. **createEmployeeCategory(category)** - Créer
48. **updateEmployeeCategory(id, category)** - Modifier
49. **deleteEmployeeCategory(id)** - Supprimer

#### ⚙️ System Settings (2 méthodes)
50. **getSystemSettings()** - Paramètres système
51. **updateSystemSettings(settings)** - Modifier paramètres

#### 🎓 Trainings (5 méthodes)
52. **getTrainings()** - Liste formations
53. **getTrainingById(id)** - Par ID
54. **createTraining(training)** - Créer
55. **updateTraining(id, training)** - Modifier
56. **deleteTraining(id)** - Supprimer

#### 👨‍🎓 Training Participants (5 méthodes)
57. **getTrainingParticipants(trainingId)** - Participants formation
58. **getUserTrainingParticipations(userId)** - Participations utilisateur
59. **addTrainingParticipant(participant)** - Ajouter participant
60. **updateTrainingParticipant(id, participant)** - Modifier participation
61. **removeTrainingParticipant(trainingId, userId)** - Retirer participant

#### 📊 Statistics (1 méthode)
62. **getStats()** - Statistiques globales

#### 🔄 Test Data (1 méthode)
63. **resetToTestData()** - Réinitialiser données test

### 🎓 E-Learning Extension (35+ méthodes supplémentaires)
- **Courses** (5 méthodes CRUD)
- **Lessons** (5 méthodes CRUD)
- **Quizzes** (8 méthodes avec tentatives)
- **Enrollments** (5 méthodes inscription)
- **Progress** (5 méthodes suivi)
- **Certificates** (3 méthodes certification)
- **Resources** (4 méthodes ressources)

### 🗣️ Forum Extension (20+ méthodes supplémentaires)
- **Forum Categories** (5 méthodes CRUD)
- **Forum Topics** (5 méthodes CRUD)
- **Forum Posts** (5 méthodes CRUD)
- **Forum Likes** (3 méthodes like/unlike)
- **Forum Stats** (2 méthodes statistiques)

---

## 🔄 MIGRATIONS ET DONNÉES

### 📊 Migration système (server/migrations.ts)
1. **runMigrations()** - Exécution migrations
2. **Password migration** - Migration mots de passe bcrypt
3. **Data integrity** - Vérification cohérence

### 🧪 Données de test (server/testData.ts)
1. **Utilisateurs de démonstration** (admin, employés)
2. **Contenu exemple** (annonces, documents, événements)
3. **Données réalistes** pour tous les modules

---

## ⚡ PERFORMANCE ET OPTIMISATION

### 📈 Gestion mémoire
- **MemStorage** - Stockage en mémoire haute performance
- **Cache automatique** - Optimisation requêtes fréquentes
- **Lazy loading** - Chargement à la demande

### 🔍 Logging et monitoring
1. **Request logging** - Log automatique des requêtes API
2. **Error tracking** - Gestion centralisée des erreurs
3. **Performance metrics** - Temps de réponse

---

## 🌐 MIGRATION PHP - ÉQUIVALENCES RECOMMANDÉES

### 🏗️ Structure PHP équivalente
```
api/
├── index.php (Router principal)
├── config/ (Configuration)
├── controllers/ (Logique métier)
├── models/ (Accès données)
├── middleware/ (Auth, validation, sécurité)
├── services/ (Services externes)
└── migrations/ (Scripts de migration)
```

### 🔄 Correspondances techniques

#### 🎯 Framework et Architecture
- **Express.js → Slim Framework ou Router PHP custom**
- **TypeScript → PHP 8+ avec types stricts**
- **Node.js modules → Classes PHP avec namespaces**
- **Middleware Express → Middleware PSR-15**

#### 🗃️ Base de données
- **Drizzle ORM → PDO + Active Record pattern**
- **PostgreSQL → MySQL/PostgreSQL (PDO compatible)**
- **Zod validation → PHP Validation classes**
- **TypeScript types → PHP DocBlocks + strict types**

#### 🔒 Sécurité
- **bcrypt → password_hash() / password_verify()**
- **Express sessions → $_SESSION + session_start()**
- **Helmet → Security headers PHP**
- **Rate limiting → PHP rate limiting library**

#### 📡 API et Communication
- **RESTful Express → RESTful PHP**
- **JSON responses → json_encode() + headers**
- **Middleware auth → PHP auth classes**
- **Error handling → Try/catch + error handlers**

#### 🛠️ Services
- **Email service → PHPMailer ou mail()**
- **File upload → $_FILES + validation**
- **Image processing → GD library ou Imagick**
- **Logging → Monolog ou custom logger**

### 📊 Modèle de données PHP
```php
// Exemple de classe User PHP équivalente
class User {
    public string $id;
    public string $username;
    public string $password;
    public string $name;
    public string $role;
    public ?string $avatar = null;
    public ?string $employeeId = null;
    public ?string $department = null;
    public ?string $position = null;
    public bool $isActive = true;
    public ?string $phone = null;
    public ?string $email = null;
    public DateTime $createdAt;
    public DateTime $updatedAt;
}
```

### 🔧 Configuration PHP recommandée
- **PHP 8.1+** pour les features modernes
- **PDO** pour l'accès base de données
- **Composer** pour la gestion des dépendances
- **PSR-4** pour l'autoloading des classes
- **Environment variables** pour la configuration

---

## 📊 RÉSUMÉ QUANTITATIF BACKEND

### 📈 Métriques techniques
- **Fichiers TypeScript**: 17
- **Endpoints API**: 107
- **Tables base de données**: 21 (13 principales + 8 e-learning)
- **Méthodes IStorage**: 95+
- **Modules de routes**: 6
- **Middleware de sécurité**: 4
- **Services externes**: 2
- **Migrations**: 1 système complet

### 🎯 Modules fonctionnels
1. **Authentification** (6 endpoints)
2. **Gestion utilisateurs** (12 endpoints)
3. **Gestion contenu** (45 endpoints)
4. **Communication** (24 endpoints)
5. **Formation** (15 endpoints)
6. **Administration** (5 endpoints)

### 🔒 Sécurité
- **Authentification**: Session-based avec bcrypt
- **Autorisation**: Role-based access control (RBAC)
- **Validation**: Zod schemas côté serveur
- **Protection**: Helmet, rate limiting, input sanitization
- **Sessions**: Sécurisées avec PostgreSQL store

Cette architecture backend robuste et modulaire permet une migration progressive vers PHP en conservant toutes les fonctionnalités métier et la sécurité.