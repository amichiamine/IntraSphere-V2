# INVENTAIRE BACKEND - INTRASPHERE LEARNING PLATFORM

## ARCHITECTURE GÉNÉRALE
### Technologies Principales
- **Runtime**: Node.js avec TypeScript
- **Framework Web**: Express.js
- **Base de Données**: PostgreSQL avec Drizzle ORM
- **Communication Temps Réel**: WebSocket (ws)
- **Authentification**: Sessions Express avec bcrypt
- **Sécurité**: Helmet, express-rate-limit, sanitization
- **Email**: Nodemailer
- **Stockage Cloud**: Google Cloud Storage

### Structure des Dossiers
```
server/
├── config.ts               # Configuration générale
├── index.ts                # Point d'entrée principal
├── db.ts                   # Configuration base de données
├── vite.ts                 # Configuration Vite pour développement
├── migrations.ts           # Migrations et sécurité
├── testData.ts            # Données de test
├── data/
│   └── storage.ts         # Interface et implémentation storage
├── middleware/
│   └── security.ts        # Middleware de sécurité
├── routes/
│   └── api.ts             # Routes API principales
└── services/
    ├── auth.ts            # Service d'authentification
    ├── email.ts           # Service email
    └── websocket.ts       # Service WebSocket
```

## CONFIGURATION ET ENVIRONNEMENT

### Variables d'Environnement
- **NODE_ENV** - Environnement (development/production)
- **DATABASE_URL** - URL connexion PostgreSQL
- **SESSION_SECRET** - Secret pour sessions
- **PORT** - Port serveur (défaut: 5000)
- **REPL_ID** - Identifiant Replit

### Configuration de Sécurité
- **Trust Proxy** - Configuration pour production
- **Helmet** - Protection headers HTTP
- **Rate Limiting** - Protection contre spamming
- **CORS** - Configuration cross-origin
- **Session Security** - Cookies sécurisés

## BASE DE DONNÉES (DRIZZLE ORM)

### Tables Principales (17 tables)

#### 1. **users** - Gestion des utilisateurs
```typescript
id, username, password, name, role, avatar
employeeId, department, position, isActive
phone, email, createdAt, updatedAt
```
- **Rôles**: employee, admin, moderator
- **Index**: username unique, employeeId unique

#### 2. **announcements** - Annonces système
```typescript
id, title, content, type, authorId, authorName
imageUrl, icon, createdAt, isImportant
```
- **Types**: info, important, event, formation

#### 3. **documents** - Gestion documentaire
```typescript
id, title, description, category, fileName
fileUrl, updatedAt, version
```
- **Catégories**: regulation, policy, guide, procedure

#### 4. **events** - Événements
```typescript
id, title, description, date, location
type, organizerId, createdAt
```
- **Types**: meeting, training, social, other

#### 5. **messages** - Messagerie interne
```typescript
id, senderId, recipientId, subject
content, isRead, createdAt
```

#### 6. **complaints** - Système de réclamations
```typescript
id, submitterId, assignedToId, title, description
category, priority, status, createdAt, updatedAt
```
- **Catégories**: hr, it, facilities, other
- **Priorités**: low, medium, high, urgent
- **Statuts**: open, in_progress, resolved, closed

#### 7. **permissions** - Délégation de permissions
```typescript
id, userId, grantedBy, permission, createdAt
```
- **Permissions**: manage_announcements, manage_documents, manage_events, manage_users, validate_topics, validate_posts, manage_employee_categories, manage_trainings

#### 8. **contents** - Bibliothèque de contenu
```typescript
id, title, type, category, description
fileUrl, thumbnailUrl, duration, viewCount
rating, tags, isPopular, isFeatured
createdAt, updatedAt
```
- **Types**: video, image, document, audio

#### 9. **categories** - Catégories de contenu
```typescript
id, name, description, icon, color
isVisible, sortOrder, createdAt
```

#### 10. **employeeCategories** - Catégories d'employés
```typescript
id, name, description, color
permissions[], isActive, createdAt
```

#### 11. **systemSettings** - Paramètres système
```typescript
id, showAnnouncements, showContent, showDocuments
showForum, showMessages, showComplaints
showTraining, updatedAt
```

#### 12. **trainings** - Formations
```typescript
id, title, description, category, difficulty
duration, instructorId, instructorName
startDate, endDate, location, maxParticipants
currentParticipants, isMandatory, isActive
isVisible, thumbnailUrl, documentUrls[]
createdAt, updatedAt
```

#### 13. **trainingParticipants** - Participants formations
```typescript
id, trainingId, userId, registeredAt
status, completionDate, score, feedback
```

### Tables E-Learning (8 tables)

#### 14. **courses** - Cours e-learning
```typescript
id, title, description, category, difficulty
duration, thumbnailUrl, authorId, authorName
isPublished, isMandatory, prerequisites
tags, createdAt, updatedAt
```

#### 15. **lessons** - Leçons de cours
```typescript
id, courseId, title, description, content
order, duration, videoUrl, documentUrl
isRequired, createdAt, updatedAt
```

#### 16. **quizzes** - Quiz et évaluations
```typescript
id, courseId, lessonId, title, description
questions, passingScore, timeLimit
allowRetries, maxAttempts, isRequired
createdAt, updatedAt
```

#### 17. **enrollments** - Inscriptions cours
```typescript
id, userId, courseId, enrolledAt, startedAt
completedAt, progress, status, certificateUrl
```

#### 18. **lessonProgress** - Progression leçons
```typescript
id, userId, lessonId, courseId, isCompleted
timeSpent, completedAt, createdAt
```

#### 19. **quizAttempts** - Tentatives quiz
```typescript
id, userId, quizId, score, answers
completedAt, createdAt
```

#### 20. **certificates** - Certificats
```typescript
id, userId, courseId, type, status
issuedAt, expiresAt, certificateUrl
```

#### 21. **resources** - Ressources partagées
```typescript
id, title, description, type, fileUrl
category, isPublic, authorId, createdAt
```

### Tables Forum (5 tables)

#### 22. **forumCategories** - Catégories forum
```typescript
id, name, description, icon, color
isVisible, sortOrder, createdAt
```

#### 23. **forumTopics** - Sujets forum
```typescript
id, categoryId, title, description, authorId
authorName, isSticky, isLocked, viewCount
postCount, lastPostAt, createdAt
```

#### 24. **forumPosts** - Messages forum
```typescript
id, topicId, authorId, authorName, content
isDeleted, deletedBy, likeCount
createdAt, updatedAt
```

#### 25. **forumLikes** - Likes forum
```typescript
id, postId, userId, createdAt
```

#### 26. **forumUserStats** - Statistiques utilisateur forum
```typescript
userId, postCount, likeCount, topicCount
reputation, lastActiveAt
```

## INTERFACE DE STOCKAGE (IStorage)

### Méthodes Utilisateurs (6 méthodes)
- `getUser(id)` - Récupérer utilisateur par ID
- `getUserByUsername(username)` - Par nom d'utilisateur
- `getUserByEmployeeId(employeeId)` - Par ID employé
- `createUser(user)` - Créer utilisateur
- `updateUser(id, user)` - Mettre à jour
- `getUsers()` - Liste tous utilisateurs

### Méthodes Annonces (5 méthodes)
- `getAnnouncements()` - Liste annonces
- `getAnnouncementById(id)` - Par ID
- `createAnnouncement(announcement)` - Créer
- `updateAnnouncement(id, data)` - Mettre à jour
- `deleteAnnouncement(id)` - Supprimer

### Méthodes Documents (5 méthodes)
- `getDocuments()` - Liste documents
- `getDocumentById(id)` - Par ID
- `createDocument(document)` - Créer
- `updateDocument(id, data)` - Mettre à jour
- `deleteDocument(id)` - Supprimer

### Méthodes Événements (5 méthodes)
- `getEvents()` - Liste événements
- `getEventById(id)` - Par ID
- `createEvent(event)` - Créer
- `updateEvent(id, data)` - Mettre à jour
- `deleteEvent(id)` - Supprimer

### Méthodes Messagerie (8 méthodes)
- `getMessages(userId)` - Messages utilisateur
- `getMessageById(id)` - Par ID
- `createMessage(message)` - Créer
- `markMessageAsRead(id)` - Marquer lu
- `getUserConversations(userId)` - Conversations
- `getConversationMessages(user1, user2)` - Messages conversation
- `getUnreadMessageCount(userId)` - Nombre non lus
- `deleteMessage(id)` - Supprimer

### Méthodes Réclamations (6 méthodes)
- `getComplaints()` - Liste réclamations
- `getComplaintById(id)` - Par ID
- `getUserComplaints(userId)` - Par utilisateur
- `createComplaint(complaint)` - Créer
- `updateComplaint(id, data)` - Mettre à jour
- `deleteComplaint(id)` - Supprimer

### Méthodes Permissions (4 méthodes)
- `getPermissions(userId)` - Par utilisateur
- `createPermission(permission)` - Créer
- `hasPermission(userId, permission)` - Vérifier
- `deletePermission(id)` - Supprimer

### Méthodes Contenu (11 méthodes)
- `getContents()` - Liste contenu
- `getContentById(id)` - Par ID
- `createContent(content)` - Créer
- `updateContent(id, data)` - Mettre à jour
- `deleteContent(id)` - Supprimer
- `getCategories()` - Catégories
- `createCategory(category)` - Créer catégorie
- `updateCategory(id, data)` - Mettre à jour catégorie
- `deleteCategory(id)` - Supprimer catégorie
- `incrementContentView(id)` - Incrémenter vues
- `getPopularContent()` - Contenu populaire

### Méthodes Formations (12 méthodes)
- `getTrainings()` - Liste formations
- `getTrainingById(id)` - Par ID
- `createTraining(training)` - Créer
- `updateTraining(id, data)` - Mettre à jour
- `deleteTraining(id)` - Supprimer
- `getTrainingParticipants(trainingId)` - Participants
- `getUserTrainingParticipations(userId)` - Participations utilisateur
- `addTrainingParticipant(participant)` - Ajouter participant
- `updateTrainingParticipant(id, data)` - Mettre à jour participant
- `removeTrainingParticipant(trainingId, userId)` - Retirer participant
- `getAllTrainingParticipants()` - Tous participants
- `getTrainingRecommendations(userId)` - Recommandations

### Méthodes E-Learning (15 méthodes)
- `getCourses()` - Liste cours
- `getCourseById(id)` - Par ID
- `createCourse(course)` - Créer
- `updateCourse(id, data)` - Mettre à jour
- `deleteCourse(id)` - Supprimer
- `getLessons(courseId)` - Leçons cours
- `getCourseLessons(courseId)` - Alias leçons
- `createLesson(lesson)` - Créer leçon
- `updateLesson(id, data)` - Mettre à jour leçon
- `deleteLesson(id)` - Supprimer leçon
- `enrollUser(userId, courseId)` - Inscrire utilisateur
- `getUserEnrollments(userId)` - Inscriptions utilisateur
- `updateEnrollmentProgress(id, progress)` - Mettre à jour progression
- `getUserLessonProgress(userId, courseId)` - Progression leçons
- `markLessonComplete(userId, courseId, lessonId)` - Marquer leçon complète

### Méthodes Forum (12 méthodes)
- `getForumCategories()` - Catégories forum
- `createForumCategory(category)` - Créer catégorie
- `updateForumCategory(id, data)` - Mettre à jour catégorie
- `deleteForumCategory(id)` - Supprimer catégorie
- `getForumTopics(categoryId?)` - Sujets forum
- `createForumTopic(topic)` - Créer sujet
- `updateForumTopic(id, data)` - Mettre à jour sujet
- `deleteForumTopic(id)` - Supprimer sujet
- `getForumPosts(topicId)` - Messages forum
- `createForumPost(post)` - Créer message
- `updateForumPost(id, data)` - Mettre à jour message
- `deleteForumPost(id, deletedBy)` - Supprimer message

### Méthodes Recherche (4 méthodes)
- `searchUsers(query)` - Rechercher utilisateurs
- `searchContent(query)` - Rechercher contenu
- `searchDocuments(query)` - Rechercher documents
- `searchAnnouncements(query)` - Rechercher annonces

### Méthodes Statistiques (2 méthodes)
- `getStats()` - Statistiques générales
- `resetToTestData()` - Réinitialiser données test

## ROUTES API

### Authentification (4 routes)
- `POST /api/auth/login` - Connexion utilisateur
- `POST /api/auth/register` - Inscription utilisateur
- `POST /api/auth/logout` - Déconnexion
- `GET /api/auth/me` - Profil utilisateur actuel

### Utilisateurs (6 routes)
- `GET /api/users` - Liste utilisateurs
- `GET /api/users/:id` - Utilisateur par ID
- `PUT /api/users/:id` - Mettre à jour utilisateur
- `POST /api/users` - Créer utilisateur
- `DELETE /api/users/:id` - Supprimer utilisateur
- `GET /api/users/search` - Rechercher utilisateurs

### Annonces (5 routes)
- `GET /api/announcements` - Liste annonces
- `GET /api/announcements/:id` - Annonce par ID
- `POST /api/announcements` - Créer annonce
- `PUT /api/announcements/:id` - Mettre à jour annonce
- `DELETE /api/announcements/:id` - Supprimer annonce

### Documents (5 routes)
- `GET /api/documents` - Liste documents
- `GET /api/documents/:id` - Document par ID
- `POST /api/documents` - Créer document
- `PUT /api/documents/:id` - Mettre à jour document
- `DELETE /api/documents/:id` - Supprimer document

### Événements (5 routes)
- `GET /api/events` - Liste événements
- `GET /api/events/:id` - Événement par ID
- `POST /api/events` - Créer événement
- `PUT /api/events/:id` - Mettre à jour événement
- `DELETE /api/events/:id` - Supprimer événement

### Messagerie (8 routes)
- `GET /api/messages` - Messages utilisateur
- `GET /api/messages/:id` - Message par ID
- `POST /api/messages` - Créer message
- `PUT /api/messages/:id/read` - Marquer lu
- `GET /api/conversations` - Conversations
- `GET /api/conversations/:userId` - Messages conversation
- `GET /api/messages/unread-count` - Nombre non lus
- `DELETE /api/messages/:id` - Supprimer message

### Réclamations (6 routes)
- `GET /api/complaints` - Liste réclamations
- `GET /api/complaints/:id` - Réclamation par ID
- `GET /api/complaints/user/:userId` - Par utilisateur
- `POST /api/complaints` - Créer réclamation
- `PUT /api/complaints/:id` - Mettre à jour réclamation
- `DELETE /api/complaints/:id` - Supprimer réclamation

### Permissions (4 routes)
- `GET /api/permissions/:userId` - Permissions utilisateur
- `POST /api/permissions` - Créer permission
- `GET /api/permissions/:userId/:permission` - Vérifier permission
- `DELETE /api/permissions/:id` - Supprimer permission

### Contenu (11 routes)
- `GET /api/content` - Liste contenu
- `GET /api/content/:id` - Contenu par ID
- `POST /api/content` - Créer contenu
- `PUT /api/content/:id` - Mettre à jour contenu
- `DELETE /api/content/:id` - Supprimer contenu
- `POST /api/content/:id/view` - Incrémenter vues
- `GET /api/content/popular` - Contenu populaire
- `GET /api/categories` - Catégories
- `POST /api/categories` - Créer catégorie
- `PUT /api/categories/:id` - Mettre à jour catégorie
- `DELETE /api/categories/:id` - Supprimer catégorie

### Formations (12 routes)
- `GET /api/trainings` - Liste formations
- `GET /api/trainings/:id` - Formation par ID
- `POST /api/trainings` - Créer formation
- `PUT /api/trainings/:id` - Mettre à jour formation
- `DELETE /api/trainings/:id` - Supprimer formation
- `GET /api/trainings/:id/participants` - Participants
- `POST /api/trainings/:id/participants` - Ajouter participant
- `DELETE /api/trainings/:trainingId/participants/:userId` - Retirer participant
- `PUT /api/training-participants/:id` - Mettre à jour participant
- `GET /api/users/:userId/trainings` - Formations utilisateur
- `GET /api/training-participants` - Tous participants
- `GET /api/users/:userId/training-recommendations` - Recommandations

### E-Learning (15 routes)
- `GET /api/courses` - Liste cours
- `GET /api/courses/:id` - Cours par ID
- `POST /api/courses` - Créer cours
- `PUT /api/courses/:id` - Mettre à jour cours
- `DELETE /api/courses/:id` - Supprimer cours
- `GET /api/courses/:id/lessons` - Leçons cours
- `POST /api/lessons` - Créer leçon
- `PUT /api/lessons/:id` - Mettre à jour leçon
- `DELETE /api/lessons/:id` - Supprimer leçon
- `POST /api/enrollments` - Inscrire utilisateur
- `GET /api/users/:userId/enrollments` - Inscriptions utilisateur
- `PUT /api/enrollments/:id/progress` - Mettre à jour progression
- `GET /api/users/:userId/lesson-progress/:courseId` - Progression leçons
- `POST /api/lesson-progress` - Marquer leçon complète
- `GET /api/users/:userId/certificates` - Certificats utilisateur

### Forum (12 routes)
- `GET /api/forum/categories` - Catégories forum
- `POST /api/forum/categories` - Créer catégorie
- `PUT /api/forum/categories/:id` - Mettre à jour catégorie
- `DELETE /api/forum/categories/:id` - Supprimer catégorie
- `GET /api/forum/topics` - Sujets forum
- `POST /api/forum/topics` - Créer sujet
- `PUT /api/forum/topics/:id` - Mettre à jour sujet
- `DELETE /api/forum/topics/:id` - Supprimer sujet
- `GET /api/forum/topics/:id/posts` - Messages sujet
- `POST /api/forum/posts` - Créer message
- `PUT /api/forum/posts/:id` - Mettre à jour message
- `DELETE /api/forum/posts/:id` - Supprimer message

### Statistiques et Recherche (5 routes)
- `GET /api/stats` - Statistiques générales
- `POST /api/reset-test-data` - Réinitialiser données test
- `GET /api/search/users` - Rechercher utilisateurs
- `GET /api/search/content` - Rechercher contenu
- `GET /api/search/global` - Recherche globale

## MIDDLEWARE DE SÉCURITÉ

### Fonctionnalités de Sécurité
- **configureSecurity(app)** - Configuration Helmet et CORS
- **sanitizeInput** - Sanitisation des entrées
- **getSessionConfig()** - Configuration sessions sécurisées
- **requireAuth** - Middleware authentification requis
- **requireRole(roles[])** - Middleware contrôle rôles

### Protection Implémentée
- **Rate Limiting** - Protection contre spam
- **Input Sanitization** - Nettoyage données entrées
- **Session Security** - Sessions sécurisées avec secrets
- **CORS Protection** - Configuration cross-origin
- **Helmet Security** - Headers HTTP sécurisés

## SERVICES

### Service d'Authentification (AuthService)
- **hashPassword(password)** - Hacher mot de passe avec bcrypt
- **verifyPassword(password, hash)** - Vérifier mot de passe
- **generateToken()** - Générer tokens sécurisés

### Service Email (emailService)
- **sendWelcomeEmail(email, name)** - Email de bienvenue
- **sendPasswordResetEmail(email, token)** - Reset mot de passe
- **sendNotificationEmail(email, subject, content)** - Notifications

### Service WebSocket
- **setupWebSocket(server)** - Configuration WebSocket
- **broadcastToAll(message)** - Diffusion globale
- **sendToUser(userId, message)** - Message utilisateur
- **notifyUserUpdate(userId, data)** - Notification mise à jour

## MIGRATIONS ET DONNÉES

### Système de Migrations
- **runMigrations()** - Exécuter migrations de sécurité
- **migratePasswords()** - Migration mots de passe bcrypt
- **initializeDefaultData()** - Données par défaut

### Données de Test
- **3 utilisateurs de test** (admin, marie.martin, pierre.dubois)
- **Données d'exemple** pour toutes les entités
- **resetToTestData()** - Réinitialisation complète

## CONFIGURATION ET DÉPLOIEMENT

### Configuration Express
- **Parsing JSON** - Limite 50MB
- **Sessions sécurisées** - Cookies HttpOnly
- **Middleware de logging** - Logs détaillés API
- **Gestion d'erreurs** - Error handlers globaux

### Configuration Vite (Développement)
- **Proxy API** - /api/* vers backend
- **HMR** - Hot Module Replacement
- **Serve Static** - Fichiers statiques production

## WEBSOCKET ET TEMPS RÉEL

### Fonctionnalités WebSocket
- **Connexions persistantes** - Maintien connexion client
- **Diffusion en temps réel** - Notifications instantanées
- **Gestion des déconnexions** - Nettoyage automatique
- **Sécurisation** - Authentification requise

### Messages Types
- **user_update** - Mise à jour utilisateur
- **announcement_created** - Nouvelle annonce
- **message_received** - Nouveau message
- **training_enrolled** - Inscription formation

## LOGS ET MONITORING

### Système de Logs
- **Logs API détaillés** - Méthode, path, status, durée
- **Logs d'erreurs** - Stack traces complètes
- **Logs de sécurité** - Tentatives intrusion
- **Logs WebSocket** - Connexions/déconnexions

### Monitoring
- **Performance tracking** - Temps réponse API
- **Error tracking** - Gestion erreurs centralisée
- **Database monitoring** - Requêtes et performance

## ÉTAT DES FONCTIONNALITÉS

### ✅ Fonctionnalités Complètes
- Authentification bcrypt sécurisée
- CRUD complet pour toutes entités
- Système de permissions granulaire
- WebSocket temps réel
- Sécurité complète (Helmet, Rate Limiting, CORS)
- API REST complète (120+ endpoints)
- E-Learning system complet
- Forum avec modération
- Système de recherche
- Analytics et statistiques
- Migrations automatiques
- Service email

### 🔄 En Développement
- Optimisations performance base de données
- Analytics avancées
- Système de cache Redis

### 📋 Sécurité et Qualité
- Validation Zod sur toutes entrées
- Sanitisation complète des données
- Sessions sécurisées
- Protection CSRF
- Rate limiting par IP
- Gestion d'erreurs complète
- Logs de sécurité