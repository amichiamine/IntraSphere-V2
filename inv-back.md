# INVENTAIRE EXHAUSTIF BACKEND - IntraSphere

## 📋 APERÇU GÉNÉRAL

**Architecture Backend**: Node.js + Express.js + TypeScript
**Base de données**: PostgreSQL avec Drizzle ORM
**Authentification**: Sessions + bcrypt + Passport.js (prêt)
**Communication temps réel**: WebSocket (ws)
**Validation**: Zod schemas partagés
**Sécurité**: Helmet, rate limiting, sanitization

---

## 🏗️ STRUCTURE DES DOSSIERS

```
server/
├── index.ts                    # Point d'entrée principal du serveur
├── config.ts                   # Configuration générale
├── db.ts                       # Configuration base de données
├── migrations.ts               # Migrations et initialisations
├── testData.ts                 # Données de test et développement
├── vite.ts                     # Configuration Vite pour développement
│
├── data/
│   └── storage.ts              # Interface et implémentation storage
│
├── middleware/
│   └── security.ts             # Middleware de sécurité
│
├── routes/
│   └── api.ts                  # Routes API principales
│
├── services/
│   ├── auth.ts                 # Service d'authentification
│   ├── email.ts                # Service d'envoi d'emails
│   └── websocket.ts            # Service WebSocket temps réel
│
├── utils/
│   ├── process-monitor.ts      # Monitoring des processus
│   └── vite-stabilizer.ts      # Stabilisation Vite HMR
│
└── public/                     # Assets statiques (build)
    ├── assets/
    └── index.html
```

---

## 🗄️ SCHÉMA DE BASE DE DONNÉES

### Tables Principales (21 tables)

#### **Users** (`users`)
```sql
- id: varchar (PK, UUID)
- username: text (UNIQUE, NOT NULL)
- password: text (NOT NULL) -- bcrypt hash
- name: text (NOT NULL)
- role: text (DEFAULT 'employee') -- employee, admin, moderator
- avatar: text
- employeeId: varchar (UNIQUE) -- ID interne
- department: varchar
- position: varchar
- isActive: boolean (DEFAULT true)
- phone: varchar
- email: varchar
- createdAt: timestamp (DEFAULT NOW())
- updatedAt: timestamp (DEFAULT NOW())
```

#### **Announcements** (`announcements`)
```sql
- id: varchar (PK, UUID)
- title: text (NOT NULL)
- content: text (NOT NULL)
- type: text (DEFAULT 'info') -- info, important, event, formation
- authorId: varchar → users.id
- authorName: text (NOT NULL)
- imageUrl: text
- icon: text (DEFAULT '📢')
- createdAt: timestamp (NOT NULL)
- isImportant: boolean (DEFAULT false)
```

#### **Documents** (`documents`)
```sql
- id: varchar (PK, UUID)
- title: text (NOT NULL)
- description: text
- category: text (NOT NULL) -- regulation, policy, guide, procedure
- fileName: text (NOT NULL)
- fileUrl: text (NOT NULL)
- updatedAt: timestamp (NOT NULL)
- version: text (DEFAULT '1.0')
```

#### **Events** (`events`)
```sql
- id: varchar (PK, UUID)
- title: text (NOT NULL)
- description: text
- date: timestamp (NOT NULL)
- location: text
- type: text (DEFAULT 'meeting') -- meeting, training, social, other
- organizerId: varchar → users.id
- createdAt: timestamp
```

#### **Messages** (`messages`)
```sql
- id: varchar (PK, UUID)
- fromUserId: varchar → users.id (NOT NULL)
- toUserId: varchar → users.id (NOT NULL)
- subject: text (NOT NULL)
- content: text (NOT NULL)
- isRead: boolean (DEFAULT false)
- createdAt: timestamp
- parentMessageId: varchar → messages.id -- Pour threads
```

#### **Complaints** (`complaints`)
```sql
- id: varchar (PK, UUID)
- reporterId: varchar → users.id
- subject: text (NOT NULL)
- description: text (NOT NULL)
- category: text (NOT NULL) -- harassment, discrimination, safety, other
- status: text (DEFAULT 'open') -- open, in_progress, resolved, closed
- priority: text (DEFAULT 'medium') -- low, medium, high, urgent
- assignedTo: varchar → users.id
- isAnonymous: boolean (DEFAULT false)
- createdAt: timestamp
- updatedAt: timestamp
```

#### **Permissions** (`permissions`)
```sql
- id: varchar (PK, UUID)
- userId: varchar → users.id (NOT NULL)
- permission: text (NOT NULL) -- Nom de la permission
- resource: text -- Ressource concernée (optionnel)
- grantedBy: varchar → users.id (NOT NULL)
- createdAt: timestamp
```

#### **Contents** (`contents`)
```sql
- id: varchar (PK, UUID)
- title: text (NOT NULL)
- content: text (NOT NULL)
- type: text (DEFAULT 'article') -- article, news, tutorial, faq
- categoryId: varchar → categories.id
- authorId: varchar → users.id
- authorName: text
- imageUrl: text
- tags: text[] -- Tableau de tags
- isPublished: boolean (DEFAULT false)
- publishedAt: timestamp
- createdAt: timestamp
- updatedAt: timestamp
```

#### **Categories** (`categories`)
```sql
- id: varchar (PK, UUID)
- name: text (NOT NULL)
- description: text
- type: text (NOT NULL) -- content, document, training
- parentId: varchar → categories.id -- Hiérarchie
- icon: text
- color: text
- isActive: boolean (DEFAULT true)
- createdAt: timestamp
```

#### **EmployeeCategories** (`employee_categories`)
```sql
- id: varchar (PK, UUID)
- name: text (NOT NULL)
- description: text
- permissions: text[] -- Permissions par défaut
- isActive: boolean (DEFAULT true)
- createdAt: timestamp
```

#### **SystemSettings** (`system_settings`)
```sql
- id: varchar (PK)
- showAnnouncements: boolean (DEFAULT true)
- showContent: boolean (DEFAULT true)
- showDocuments: boolean (DEFAULT true)
- showForum: boolean (DEFAULT true)
- showMessages: boolean (DEFAULT true)
- showComplaints: boolean (DEFAULT true)
- showTraining: boolean (DEFAULT true)
- updatedAt: timestamp
```

### Formation & E-Learning (8 tables)

#### **Trainings** (`trainings`)
```sql
- id: varchar (PK, UUID)
- title: text (NOT NULL)
- description: text
- type: text (DEFAULT 'online') -- online, classroom, hybrid
- duration: integer -- En minutes
- maxParticipants: integer
- instructorId: varchar → users.id
- startDate: timestamp
- endDate: timestamp
- isActive: boolean (DEFAULT true)
- createdAt: timestamp
```

#### **TrainingParticipants** (`training_participants`)
```sql
- id: varchar (PK, UUID)
- trainingId: varchar → trainings.id (NOT NULL)
- userId: varchar → users.id (NOT NULL)
- status: text (DEFAULT 'enrolled') -- enrolled, completed, cancelled
- progress: integer (DEFAULT 0) -- Pourcentage
- score: real -- Note finale
- certificateIssued: boolean (DEFAULT false)
- enrolledAt: timestamp
- completedAt: timestamp
```

#### **Courses** (`courses`)
```sql
- id: varchar (PK, UUID)
- title: text (NOT NULL)
- description: text
- category: text (NOT NULL)
- difficulty: text (DEFAULT 'beginner') -- beginner, intermediate, advanced
- estimatedDuration: integer -- En heures
- instructorId: varchar → users.id
- thumbnailUrl: text
- isPublished: boolean (DEFAULT false)
- tags: text[]
- createdAt: timestamp
- updatedAt: timestamp
```

#### **Lessons** (`lessons`)
```sql
- id: varchar (PK, UUID)
- courseId: varchar → courses.id (NOT NULL)
- title: text (NOT NULL)
- content: text (NOT NULL)
- type: text (DEFAULT 'text') -- text, video, interactive
- duration: integer -- En minutes
- orderIndex: integer (NOT NULL)
- resources: text[] -- URLs des ressources
- isRequired: boolean (DEFAULT true)
- createdAt: timestamp
```

#### **Quizzes** (`quizzes`)
```sql
- id: varchar (PK, UUID)
- courseId: varchar → courses.id (NOT NULL)
- title: text (NOT NULL)
- description: text
- questions: jsonb (NOT NULL) -- Questions et réponses
- passingScore: integer (DEFAULT 70) -- Score minimum
- timeLimit: integer -- En minutes (optionnel)
- maxAttempts: integer (DEFAULT 3)
- isActive: boolean (DEFAULT true)
- createdAt: timestamp
```

#### **Enrollments** (`enrollments`)
```sql
- id: varchar (PK, UUID)
- userId: varchar → users.id (NOT NULL)
- courseId: varchar → courses.id (NOT NULL)
- status: text (DEFAULT 'active') -- active, completed, dropped
- progress: integer (DEFAULT 0) -- Pourcentage
- startedAt: timestamp
- completedAt: timestamp
- lastAccessedAt: timestamp
```

#### **LessonProgress** (`lesson_progress`)
```sql
- id: varchar (PK, UUID)
- userId: varchar → users.id (NOT NULL)
- lessonId: varchar → lessons.id (NOT NULL)
- courseId: varchar → courses.id (NOT NULL)
- isCompleted: boolean (DEFAULT false)
- timeSpent: integer (DEFAULT 0) -- En secondes
- completedAt: timestamp
```

#### **QuizAttempts** (`quiz_attempts`)
```sql
- id: varchar (PK, UUID)
- userId: varchar → users.id (NOT NULL)
- quizId: varchar → quizzes.id (NOT NULL)
- score: integer (NOT NULL)
- answers: jsonb (NOT NULL) -- Réponses données
- isPassed: boolean (NOT NULL)
- completedAt: timestamp (NOT NULL)
```

#### **Certificates** (`certificates`)
```sql
- id: varchar (PK, UUID)
- userId: varchar → users.id (NOT NULL)
- courseId: varchar → courses.id (NOT NULL)
- title: text (NOT NULL)
- description: text
- issuedAt: timestamp (NOT NULL)
- expiresAt: timestamp -- Optionnel
```

#### **Resources** (`resources`)
```sql
- id: varchar (PK, UUID)
- title: text (NOT NULL)
- description: text
- type: text (NOT NULL) -- document, video, link, tool
- url: text (NOT NULL)
- category: text (NOT NULL)
- tags: text[]
- isPublic: boolean (DEFAULT true)
- uploadedBy: varchar → users.id
- createdAt: timestamp
```

### Forum System (5 tables)

#### **ForumCategories** (`forum_categories`)
```sql
- id: varchar (PK, UUID)
- name: text (NOT NULL)
- description: text
- icon: text
- color: text
- isActive: boolean (DEFAULT true)
- topicsCount: integer (DEFAULT 0)
- postsCount: integer (DEFAULT 0)
- lastActivityAt: timestamp
- createdAt: timestamp
```

#### **ForumTopics** (`forum_topics`)
```sql
- id: varchar (PK, UUID)
- categoryId: varchar → forum_categories.id (NOT NULL)
- title: text (NOT NULL)
- authorId: varchar → users.id (NOT NULL)
- authorName: text (NOT NULL)
- isSticky: boolean (DEFAULT false)
- isLocked: boolean (DEFAULT false)
- postsCount: integer (DEFAULT 0)
- viewsCount: integer (DEFAULT 0)
- lastPostAt: timestamp
- lastPostAuthor: text
- createdAt: timestamp
```

#### **ForumPosts** (`forum_posts`)
```sql
- id: varchar (PK, UUID)
- topicId: varchar → forum_topics.id (NOT NULL)
- authorId: varchar → users.id (NOT NULL)
- authorName: text (NOT NULL)
- content: text (NOT NULL)
- isDeleted: boolean (DEFAULT false)
- deletedBy: varchar → users.id
- deletedAt: timestamp
- likesCount: integer (DEFAULT 0)
- createdAt: timestamp
- updatedAt: timestamp
```

#### **ForumLikes** (`forum_likes`)
```sql
- id: varchar (PK, UUID)
- postId: varchar → forum_posts.id (NOT NULL)
- userId: varchar → users.id (NOT NULL)
- createdAt: timestamp
-- UNIQUE(postId, userId)
```

#### **ForumUserStats** (`forum_user_stats`)
```sql
- id: varchar (PK, UUID)
- userId: varchar → users.id (NOT NULL, UNIQUE)
- postsCount: integer (DEFAULT 0)
- topicsCount: integer (DEFAULT 0)
- likesReceived: integer (DEFAULT 0)
- likesGiven: integer (DEFAULT 0)
- lastActivityAt: timestamp
- reputation: integer (DEFAULT 0)
```

---

## 🔧 INTERFACE DE STOCKAGE (IStorage)

### Méthodes Users (6 méthodes)
- `getUser(id: string): Promise<User | undefined>`
- `getUserByUsername(username: string): Promise<User | undefined>`
- `getUserByEmployeeId(employeeId: string): Promise<User | undefined>`
- `createUser(user: InsertUser): Promise<User>`
- `updateUser(id: string, user: Partial<User>): Promise<User>`
- `getUsers(): Promise<User[]>`

### Méthodes Announcements (5 méthodes)
- `getAnnouncements(): Promise<Announcement[]>`
- `getAnnouncementById(id: string): Promise<Announcement | undefined>`
- `createAnnouncement(announcement: InsertAnnouncement): Promise<Announcement>`
- `updateAnnouncement(id: string, announcement: Partial<Announcement>): Promise<Announcement>`
- `deleteAnnouncement(id: string): Promise<void>`

### Méthodes Documents (5 méthodes)
- `getDocuments(): Promise<Document[]>`
- `getDocumentById(id: string): Promise<Document | undefined>`
- `createDocument(document: InsertDocument): Promise<Document>`
- `updateDocument(id: string, document: Partial<Document>): Promise<Document>`
- `deleteDocument(id: string): Promise<void>`

### Méthodes Events (5 méthodes)
- `getEvents(): Promise<Event[]>`
- `getEventById(id: string): Promise<Event | undefined>`
- `createEvent(event: InsertEvent): Promise<Event>`
- `updateEvent(id: string, event: Partial<Event>): Promise<Event>`
- `deleteEvent(id: string): Promise<void>`

### Méthodes Messages (4 méthodes)
- `getMessages(userId: string): Promise<Message[]>`
- `getMessageById(id: string): Promise<Message | undefined>`
- `createMessage(message: InsertMessage): Promise<Message>`
- `markMessageAsRead(id: string): Promise<void>`

### Méthodes Complaints (5 méthodes)
- `getComplaints(): Promise<Complaint[]>`
- `getComplaintById(id: string): Promise<Complaint | undefined>`
- `getComplaintsByUser(userId: string): Promise<Complaint[]>`
- `createComplaint(complaint: InsertComplaint): Promise<Complaint>`
- `updateComplaint(id: string, complaint: Partial<Complaint>): Promise<Complaint>`

### Méthodes Permissions (4 méthodes)
- `getPermissions(userId: string): Promise<Permission[]>`
- `createPermission(permission: InsertPermission): Promise<Permission>`
- `revokePermission(id: string): Promise<void>`
- `hasPermission(userId: string, permission: string): Promise<boolean>`

### Méthodes Contents (5 méthodes)
- `getContents(): Promise<Content[]>`
- `getContentById(id: string): Promise<Content | undefined>`
- `createContent(content: InsertContent): Promise<Content>`
- `updateContent(id: string, content: Partial<Content>): Promise<Content>`
- `deleteContent(id: string): Promise<void>`

### Méthodes Categories (5 méthodes)
- `getCategories(): Promise<Category[]>`
- `getCategoryById(id: string): Promise<Category | undefined>`
- `createCategory(category: InsertCategory): Promise<Category>`
- `updateCategory(id: string, category: Partial<Category>): Promise<Category>`
- `deleteCategory(id: string): Promise<void>`

### Méthodes EmployeeCategories (5 méthodes)
- `getEmployeeCategories(): Promise<EmployeeCategory[]>`
- `getEmployeeCategoryById(id: string): Promise<EmployeeCategory | undefined>`
- `createEmployeeCategory(category: InsertEmployeeCategory): Promise<EmployeeCategory>`
- `updateEmployeeCategory(id: string, category: Partial<EmployeeCategory>): Promise<EmployeeCategory>`
- `deleteEmployeeCategory(id: string): Promise<void>`

### Méthodes SystemSettings (2 méthodes)
- `getSystemSettings(): Promise<SystemSettings>`
- `updateSystemSettings(settings: Partial<SystemSettings>): Promise<SystemSettings>`

### Méthodes Trainings (5 méthodes)
- `getTrainings(): Promise<Training[]>`
- `getTrainingById(id: string): Promise<Training | undefined>`
- `createTraining(training: InsertTraining): Promise<Training>`
- `updateTraining(id: string, training: Partial<Training>): Promise<Training>`
- `deleteTraining(id: string): Promise<void>`

### Méthodes TrainingParticipants (5 méthodes)
- `getTrainingParticipants(trainingId: string): Promise<TrainingParticipant[]>`
- `getUserTrainingParticipations(userId: string): Promise<TrainingParticipant[]>`
- `addTrainingParticipant(participant: InsertTrainingParticipant): Promise<TrainingParticipant>`
- `updateTrainingParticipant(id: string, participant: Partial<TrainingParticipant>): Promise<TrainingParticipant>`
- `removeTrainingParticipant(trainingId: string, userId: string): Promise<void>`

### E-Learning Methods (30 méthodes)

#### Courses (5 méthodes)
- `getCourses(): Promise<Course[]>`
- `getCourseById(id: string): Promise<Course | undefined>`
- `createCourse(course: InsertCourse): Promise<Course>`
- `updateCourse(id: string, course: Partial<Course>): Promise<Course>`
- `deleteCourse(id: string): Promise<void>`

#### Lessons (6 méthodes)
- `getLessons(courseId: string): Promise<Lesson[]>`
- `getLessonById(id: string): Promise<Lesson | undefined>`
- `createLesson(lesson: InsertLesson): Promise<Lesson>`
- `updateLesson(id: string, lesson: Partial<Lesson>): Promise<Lesson>`
- `deleteLesson(id: string): Promise<void>`
- `getCourseLessons(courseId: string): Promise<Lesson[]>`

#### Quizzes (5 méthodes)
- `getQuizzes(courseId: string): Promise<Quiz[]>`
- `getQuizById(id: string): Promise<Quiz | undefined>`
- `createQuiz(quiz: InsertQuiz): Promise<Quiz>`
- `updateQuiz(id: string, quiz: Partial<Quiz>): Promise<Quiz>`
- `deleteQuiz(id: string): Promise<void>`

#### Enrollments & Progress (6 méthodes)
- `getUserEnrollments(userId: string): Promise<Enrollment[]>`
- `getEnrollmentById(id: string): Promise<Enrollment | undefined>`
- `enrollUser(userId: string, courseId: string): Promise<Enrollment>`
- `updateEnrollmentProgress(id: string, progress: Partial<Enrollment>): Promise<Enrollment>`
- `getUserLessonProgress(userId: string, courseId: string): Promise<LessonProgress[]>`
- `updateLessonProgress(userId: string, lessonId: string, courseId: string, completed: boolean): Promise<LessonProgress>`

#### Quiz Attempts (2 méthodes)
- `getUserQuizAttempts(userId: string, quizId: string): Promise<QuizAttempt[]>`
- `createQuizAttempt(attempt: Omit<QuizAttempt, 'id' | 'completedAt'>): Promise<QuizAttempt>`

#### Certificates (2 méthodes)
- `getUserCertificates(userId: string): Promise<Certificate[]>`
- `createCertificate(certificate: Omit<Certificate, 'id' | 'issuedAt'>): Promise<Certificate>`

#### Resources (5 méthodes)
- `getResources(): Promise<Resource[]>`
- `getResourceById(id: string): Promise<Resource | undefined>`
- `createResource(resource: InsertResource): Promise<Resource>`
- `updateResource(id: string, resource: Partial<Resource>): Promise<Resource>`
- `deleteResource(id: string): Promise<void>`

### Forum System Methods (15 méthodes)

#### Forum Categories (5 méthodes)
- `getForumCategories(): Promise<ForumCategory[]>`
- `getForumCategoryById(id: string): Promise<ForumCategory | undefined>`
- `createForumCategory(category: InsertForumCategory): Promise<ForumCategory>`
- `updateForumCategory(id: string, category: Partial<ForumCategory>): Promise<ForumCategory>`
- `deleteForumCategory(id: string): Promise<void>`

#### Forum Topics (5 méthodes)
- `getForumTopics(categoryId?: string): Promise<ForumTopic[]>`
- `getForumTopicById(id: string): Promise<ForumTopic | undefined>`
- `createForumTopic(topic: InsertForumTopic): Promise<ForumTopic>`
- `updateForumTopic(id: string, topic: Partial<ForumTopic>): Promise<ForumTopic>`
- `deleteForumTopic(id: string): Promise<void>`

#### Forum Posts (5 méthodes)
- `getForumPosts(topicId: string): Promise<ForumPost[]>`
- `getForumPostById(id: string): Promise<ForumPost | undefined>`
- `createForumPost(post: InsertForumPost): Promise<ForumPost>`
- `updateForumPost(id: string, post: Partial<ForumPost>): Promise<ForumPost>`
- `deleteForumPost(id: string, deletedBy: string): Promise<void>`

### Méthodes Search (4 méthodes)
- `searchUsers(query: string): Promise<User[]>`
- `searchContent(query: string): Promise<Content[]>`
- `searchDocuments(query: string): Promise<Document[]>`
- `searchAnnouncements(query: string): Promise<Announcement[]>`

### Méthodes Analytics & Stats (5 méthodes)
- `getStats(): Promise<StatsObject>`
- `getAllTrainingParticipants(): Promise<TrainingParticipant[]>`
- `markLessonComplete(userId: string, courseId: string, lessonId: string): Promise<void>`
- `getTrainingRecommendations(userId: string): Promise<Course[]>`
- `getForumUserStats(userId: string): Promise<ForumUserStats | undefined>`

### Méthodes Utilitaires (2 méthodes)
- `resetToTestData(): Promise<void>`
- `updateForumUserStats(userId: string, stats: Partial<ForumUserStats>): Promise<ForumUserStats>`

**Total Méthodes Interface**: 140+ méthodes

---

## 🚀 ROUTES API (60+ endpoints)

### 🔐 Authentication Routes (`/api/auth/*`)

#### **POST /api/auth/login**
- **Validation**: Username + password requis
- **Sécurité**: bcrypt pour vérification mot de passe
- **Session**: Création session utilisateur
- **Réponse**: User sans password
- **Statuts**: 200 (success), 401 (invalid), 400 (missing)

#### **POST /api/auth/register**
- **Validation**: Schema insertUserSchema
- **Vérification**: Username unique
- **Hash**: bcrypt pour mot de passe
- **Email**: Envoi email de bienvenue
- **Rôle**: 'employee' par défaut
- **Statuts**: 201 (created), 409 (exists), 400 (invalid)

#### **GET /api/auth/me**
- **Auth**: Session requise
- **Validation**: Utilisateur actif
- **Réponse**: Profil utilisateur courant
- **Statuts**: 200 (success), 401 (not auth)

#### **POST /api/auth/logout**
- **Action**: Destruction session
- **Réponse**: Message de confirmation
- **Statuts**: 200 (success)

### 📊 Statistics Routes (`/api/stats`)

#### **GET /api/stats**
- **Fonction**: Métriques dashboard
- **Données**: 
  - Total users, announcements, documents, events
  - Messages, complaints counts
  - New announcements, updated documents
  - Connected users, pending complaints
- **Format**: JSON avec métriques
- **Statuts**: 200 (success), 500 (error)

### 📢 Announcements Routes (`/api/announcements/*`)

#### **GET /api/announcements**
- **Fonction**: Liste toutes les annonces
- **Tri**: Par date création (descendant)
- **Statuts**: 200 (success), 500 (error)

#### **GET /api/announcements/:id**
- **Fonction**: Annonce spécifique par ID
- **Statuts**: 200 (found), 404 (not found), 500 (error)

#### **POST /api/announcements**
- **Auth**: Requise (admin/moderator)
- **Validation**: Schema insertAnnouncementSchema
- **Fonction**: Création nouvelle annonce
- **Statuts**: 201 (created), 400 (invalid), 500 (error)

#### **PATCH /api/announcements/:id**
- **Auth**: Requise (admin/moderator)
- **Fonction**: Modification annonce
- **Statuts**: 200 (updated), 404 (not found), 500 (error)

#### **DELETE /api/announcements/:id**
- **Auth**: Requise (admin/moderator)
- **Fonction**: Suppression annonce
- **Statuts**: 200 (deleted), 404 (not found), 500 (error)

### 📄 Documents Routes (`/api/documents/*`)

#### **GET /api/documents**
- **Fonction**: Liste tous les documents
- **Tri**: Par date mise à jour
- **Statuts**: 200 (success), 500 (error)

#### **GET /api/documents/:id**
- **Fonction**: Document spécifique
- **Statuts**: 200 (found), 404 (not found), 500 (error)

#### **POST /api/documents**
- **Auth**: Requise (admin/moderator)
- **Validation**: Schema insertDocumentSchema
- **Fonction**: Upload nouveau document
- **Statuts**: 201 (created), 400 (invalid), 500 (error)

#### **PATCH /api/documents/:id**
- **Auth**: Requise (admin/moderator)
- **Fonction**: Modification document
- **Auto**: Version increment
- **Statuts**: 200 (updated), 404 (not found), 500 (error)

#### **DELETE /api/documents/:id**
- **Auth**: Requise (admin/moderator)
- **Fonction**: Suppression document
- **Statuts**: 200 (deleted), 404 (not found), 500 (error)

### 📅 Events Routes (`/api/events/*`)

#### **GET /api/events**
- **Fonction**: Liste tous les événements
- **Tri**: Par date événement
- **Statuts**: 200 (success), 500 (error)

#### **GET /api/events/:id**
- **Fonction**: Événement spécifique
- **Statuts**: 200 (found), 404 (not found), 500 (error)

#### **POST /api/events**
- **Auth**: Requise (admin/moderator)
- **Validation**: Schema insertEventSchema
- **Fonction**: Création nouvel événement
- **Statuts**: 201 (created), 400 (invalid), 500 (error)

#### **PATCH /api/events/:id**
- **Auth**: Requise (admin/moderator)
- **Fonction**: Modification événement
- **Statuts**: 200 (updated), 404 (not found), 500 (error)

#### **DELETE /api/events/:id**
- **Auth**: Requise (admin/moderator)
- **Fonction**: Suppression événement
- **Statuts**: 200 (deleted), 404 (not found), 500 (error)

### 👥 Users Routes (`/api/users/*`)

#### **GET /api/users**
- **Auth**: Requise
- **Fonction**: Liste utilisateurs (annuaire)
- **Filtrage**: Utilisateurs actifs uniquement
- **Statuts**: 200 (success), 500 (error)

#### **POST /api/users**
- **Auth**: Requise (admin uniquement)
- **Validation**: Schema insertUserSchema
- **Hash**: Mot de passe bcrypt
- **Fonction**: Création compte utilisateur
- **Statuts**: 201 (created), 400 (invalid), 409 (exists), 500 (error)

#### **PATCH /api/users/:id**
- **Auth**: Requise (admin ou self)
- **Validation**: Propriétaire ou admin
- **Hash**: Mot de passe si modifié
- **Fonction**: Modification profil
- **Statuts**: 200 (updated), 403 (forbidden), 500 (error)

#### **GET /api/users/:id**
- **Auth**: Requise
- **Fonction**: Profil utilisateur spécifique
- **Statuts**: 200 (found), 404 (not found), 500 (error)

### 💬 Messages Routes (`/api/messages/*`)

#### **GET /api/messages**
- **Auth**: Requise
- **Fonction**: Messages de l'utilisateur connecté
- **Filtrage**: Reçus et envoyés
- **Tri**: Par date (récent en premier)
- **Statuts**: 200 (success), 500 (error)

#### **POST /api/messages**
- **Auth**: Requise
- **Validation**: Schema insertMessageSchema
- **Fonction**: Envoi nouveau message
- **Notification**: WebSocket temps réel
- **Statuts**: 201 (sent), 400 (invalid), 500 (error)

#### **PATCH /api/messages/:id/read**
- **Auth**: Requise
- **Validation**: Destinataire uniquement
- **Fonction**: Marquer comme lu
- **Statuts**: 200 (marked), 403 (forbidden), 404 (not found)

### 📝 Complaints Routes (`/api/complaints/*`)

#### **GET /api/complaints**
- **Auth**: Requise
- **Permissions**: Admin voit tout, user ses propres
- **Tri**: Par date création
- **Statuts**: 200 (success), 500 (error)

#### **POST /api/complaints**
- **Auth**: Requise
- **Validation**: Schema insertComplaintSchema
- **Anonymat**: Option anonyme supportée
- **Statut**: 'open' par défaut
- **Statuts**: 201 (created), 400 (invalid), 500 (error)

#### **PATCH /api/complaints/:id**
- **Auth**: Requise (admin/moderator)
- **Fonction**: Mise à jour statut/assignation
- **Workflow**: open → in_progress → resolved → closed
- **Statuts**: 200 (updated), 403 (forbidden), 404 (not found)

### 📚 Content Routes (`/api/contents/*`)

#### **GET /api/contents**
- **Fonction**: Liste contenus publiés
- **Filtrage**: Contenu publié uniquement
- **Tri**: Par date publication
- **Statuts**: 200 (success), 500 (error)

#### **POST /api/contents**
- **Auth**: Requise (admin/moderator)
- **Validation**: Schema insertContentSchema
- **Auto**: AuthorId et authorName
- **Statuts**: 201 (created), 400 (invalid), 500 (error)

#### **PATCH /api/contents/:id**
- **Auth**: Requise (admin/moderator)
- **Fonction**: Modification contenu
- **Auto**: UpdatedAt timestamp
- **Statuts**: 200 (updated), 404 (not found), 500 (error)

### 🎓 Training Routes (`/api/trainings/*`)

#### **GET /api/trainings**
- **Fonction**: Liste formations disponibles
- **Filtrage**: Formations actives
- **Statuts**: 200 (success), 500 (error)

#### **POST /api/trainings**
- **Auth**: Requise (admin/moderator)
- **Validation**: Schema insertTrainingSchema
- **Fonction**: Création formation
- **Statuts**: 201 (created), 400 (invalid), 500 (error)

#### **GET /api/trainings/:id/participants**
- **Auth**: Requise
- **Fonction**: Participants à une formation
- **Statuts**: 200 (success), 404 (not found), 500 (error)

#### **POST /api/trainings/:id/participants**
- **Auth**: Requise
- **Validation**: Places disponibles
- **Fonction**: Inscription formation
- **Statuts**: 201 (enrolled), 400 (full), 409 (already enrolled)

### 🎓 E-Learning Routes (`/api/courses/*`, `/api/lessons/*`, etc.)

#### **GET /api/courses**
- **Fonction**: Catalogue cours e-learning
- **Filtrage**: Cours publiés
- **Statuts**: 200 (success), 500 (error)

#### **POST /api/courses**
- **Auth**: Requise (admin/moderator)
- **Validation**: Schema insertCourseSchema
- **Fonction**: Création cours
- **Statuts**: 201 (created), 400 (invalid), 500 (error)

#### **GET /api/courses/:id/lessons**
- **Fonction**: Leçons d'un cours
- **Tri**: Par orderIndex
- **Statuts**: 200 (success), 404 (course not found)

#### **POST /api/courses/:id/enroll**
- **Auth**: Requise
- **Validation**: Cours existant et publié
- **Fonction**: Inscription au cours
- **Statuts**: 201 (enrolled), 409 (already enrolled)

### 💬 Forum Routes (`/api/forum/*`)

#### **GET /api/forum/categories**
- **Fonction**: Catégories forum actives
- **Stats**: Nombre topics et posts
- **Statuts**: 200 (success), 500 (error)

#### **GET /api/forum/topics**
- **Params**: categoryId optionnel
- **Fonction**: Topics par catégorie ou tous
- **Tri**: Par dernière activité
- **Statuts**: 200 (success), 500 (error)

#### **POST /api/forum/topics**
- **Auth**: Requise
- **Validation**: Schema insertForumTopicSchema
- **Auto**: AuthorId et authorName
- **Statuts**: 201 (created), 400 (invalid), 500 (error)

#### **GET /api/forum/topics/:id/posts**
- **Fonction**: Posts d'un topic
- **Tri**: Par date création
- **Pagination**: Support prévu
- **Statuts**: 200 (success), 404 (topic not found)

#### **POST /api/forum/topics/:id/posts**
- **Auth**: Requise
- **Validation**: Topic existant et non verrouillé
- **Auto**: AuthorId et authorName
- **Statuts**: 201 (posted), 400 (locked), 404 (not found)

### 🔍 Search Routes (`/api/search/*`)

#### **GET /api/search/global**
- **Params**: q (query), type (optionnel)
- **Fonction**: Recherche multi-entités
- **Types**: users, content, documents, announcements
- **Statuts**: 200 (results), 400 (missing query)

### ⚙️ Admin Routes (`/api/admin/*`)

#### **GET /api/admin/settings**
- **Auth**: Requise (admin uniquement)
- **Fonction**: Configuration système
- **Statuts**: 200 (settings), 403 (forbidden)

#### **PATCH /api/admin/settings**
- **Auth**: Requise (admin uniquement)
- **Validation**: Schema settings
- **Fonction**: Mise à jour configuration
- **Statuts**: 200 (updated), 403 (forbidden)

#### **POST /api/admin/reset-test-data**
- **Auth**: Requise (admin uniquement)
- **Fonction**: Réinitialisation données test
- **Usage**: Développement uniquement
- **Statuts**: 200 (reset), 403 (forbidden)

---

## 🔒 MIDDLEWARE DE SÉCURITÉ

### **Security Configuration** (`middleware/security.ts`)

#### **Helmet Configuration**
- **CSP**: Content Security Policy
- **HSTS**: HTTP Strict Transport Security
- **X-Frame-Options**: DENY
- **X-Content-Type-Options**: nosniff
- **Referrer-Policy**: same-origin

#### **Rate Limiting**
- **Global**: 100 req/15min par IP
- **Auth routes**: 5 req/15min par IP
- **API routes**: 50 req/15min par IP
- **Headers**: X-RateLimit-* pour info client

#### **Input Sanitization**
- **HTML**: Échappement automatique
- **SQL**: Protection injection (via ORM)
- **XSS**: Nettoyage input utilisateur
- **Path traversal**: Validation chemins

#### **Session Security**
- **Secret**: Généré aléatoirement
- **Secure**: HTTPS uniquement en production
- **HttpOnly**: Protection XSS
- **SameSite**: Protection CSRF
- **MaxAge**: 24h par défaut

### **Authentication Middleware**

#### **requireAuth**
- **Validation**: Session userId présente
- **Réponse**: 401 si non authentifié
- **Usage**: Routes protégées

#### **requireRole(roles: string[])**
- **Validation**: Rôle utilisateur dans liste
- **Lookup**: Récupération user par session
- **Réponse**: 403 si permissions insuffisantes
- **Injection**: req.user pour routes suivantes

---

## 🔧 SERVICES

### **AuthService** (`services/auth.ts`)

#### **hashPassword(password: string): Promise<string>**
- **Algorithm**: bcrypt avec salt 12
- **Sécurité**: Résistant rainbow tables
- **Performance**: ~250ms par hash

#### **verifyPassword(password: string, hash: string): Promise<boolean>**
- **Validation**: Comparaison bcrypt sécurisée
- **Timing**: Constant-time pour éviter timing attacks

### **EmailService** (`services/email.ts`)

#### **Configuration**
- **Provider**: Nodemailer
- **SMTP**: Configurable via env vars
- **Templates**: HTML + text fallback

#### **sendWelcomeEmail(email: string, name: string): Promise<void>**
- **Template**: Email de bienvenue personnalisé
- **Contenu**: Informations compte + liens utiles
- **Fallback**: Graceful failure si service indisponible

#### **sendNotification(email: string, subject: string, content: string): Promise<void>**
- **Usage**: Notifications importantes
- **Format**: HTML avec fallback text
- **Queue**: Prévu pour volume important

### **WebSocketService** (`services/websocket.ts`)

#### **Configuration**
- **Library**: ws (WebSocket library)
- **Path**: /ws
- **Heartbeat**: Ping/pong automatique

#### **Events Supportés**
- **user_joined**: Connexion utilisateur
- **user_left**: Déconnexion utilisateur
- **new_message**: Nouveau message privé
- **new_announcement**: Nouvelle annonce
- **new_forum_post**: Nouveau post forum
- **notification**: Notification générique

#### **Broadcasting**
- **All users**: Diffusion générale
- **Specific user**: Message privé
- **Role-based**: Par rôle utilisateur
- **Room-based**: Par groupe/département

#### **Connection Management**
- **Auth**: Validation session WebSocket
- **Heartbeat**: Keep-alive automatique
- **Cleanup**: Nettoyage connexions fermées
- **Reconnection**: Support reconnexion automatique

---

## 🗄️ IMPLÉMENTATION STORAGE

### **MemStorage Class** (`data/storage.ts`)

#### **Stockage In-Memory**
- **Maps**: Structures optimisées pour performance
- **Persistence**: Données perdues au redémarrage
- **Avantages**: Rapidité, simplicité développement
- **Inconvénients**: Non persistent, limite mémoire

#### **Collections Principales**
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
private employeeCategories: Map<string, EmployeeCategory>
private trainings: Map<string, Training>
private trainingParticipants: Map<string, TrainingParticipant>
private courses: Map<string, Course>
private lessons: Map<string, Lesson>
private quizzes: Map<string, Quiz>
private enrollments: Map<string, Enrollment>
private lessonProgress: Map<string, LessonProgress>
private quizAttempts: Map<string, QuizAttempt>
private certificates: Map<string, Certificate>
private resources: Map<string, Resource>
private forumCategories: Map<string, ForumCategory>
private forumTopics: Map<string, ForumTopic>
private forumPosts: Map<string, ForumPost>
private forumLikes: Map<string, ForumLike>
private forumUserStats: Map<string, ForumUserStats>
```

#### **Données d'Initialisation**
- **Users**: 3 utilisateurs de test (admin, moderator, employee)
- **Announcements**: 5 annonces d'exemple
- **Documents**: 8 documents de procédures
- **Events**: 4 événements planifiés
- **Categories**: Structure hiérarchique
- **Forum**: Catégories et topics de démo
- **Courses**: 3 cours e-learning complets

#### **Méthodes de Recherche**
- **Users**: Par username, employeeId, département
- **Content**: Full-text search dans title/content
- **Documents**: Par catégorie, title, description
- **Announcements**: Par type, importance, contenu

#### **Analytics & Stats**
- **Compteurs**: Automatiques pour entités
- **Agrégations**: Calculs temps réel
- **Métriques**: Dashboard et reporting
- **Tendances**: Données historiques (simulées)

---

## ⚙️ CONFIGURATION

### **Database Configuration** (`db.ts`)
- **Provider**: PostgreSQL via Drizzle ORM
- **Connection**: Pool de connexions
- **Migrations**: Automatiques au démarrage
- **Environment**: DATABASE_URL configuré

### **Server Configuration** (`config.ts`)
- **Port**: 5000 par défaut, configurable
- **Host**: 0.0.0.0 (accessible externe)
- **Environment**: NODE_ENV pour mode
- **Security**: Headers et CORS configurés

### **Process Monitoring** (`utils/process-monitor.ts`)
- **Port cleanup**: Libération port au démarrage
- **Health checks**: Monitoring continu
- **Memory**: Surveillance utilisation
- **Restart**: Redémarrage automatique si besoin

### **Vite Integration** (`vite.ts`)
- **Development**: Serveur Vite intégré
- **HMR**: Hot module replacement
- **Proxy**: API routes proxifiées
- **Build**: Assets statiques en production

---

## 🔄 MIGRATIONS ET DONNÉES

### **Migration System** (`migrations.ts`)

#### **Password Migration**
- **Fonction**: Migration bcrypt pour users existants
- **Détection**: Mots de passe non hashés
- **Process**: Hash automatique au démarrage
- **Logging**: Progression et erreurs

#### **Schema Evolution**
- **Drizzle**: Migration automatique schema
- **Versioning**: Suivi versions database
- **Rollback**: Possible avec backup

### **Test Data** (`testData.ts`)
- **Users**: Comptes pré-configurés
- **Content**: Données d'exemple complètes
- **Relations**: Liens entre entités
- **Reset**: Fonction réinitialisation

---

## 📦 DÉPENDANCES PRINCIPALES

### **Core Runtime**
- **express**: 4.21.2 (Serveur HTTP)
- **typescript**: 5.6.3 (Langage)
- **tsx**: 4.19.1 (Exécution TS)

### **Database & ORM**
- **drizzle-orm**: 0.39.1 (ORM principal)
- **drizzle-zod**: 0.7.0 (Validation schemas)
- **@neondatabase/serverless**: 0.10.4 (PostgreSQL)

### **Authentication & Security**
- **bcrypt**: 6.0.0 (Hash mots de passe)
- **express-session**: 1.18.2 (Sessions)
- **passport**: 0.7.0 (Auth strategies)
- **passport-local**: 1.0.0 (Local auth)
- **helmet**: 8.1.0 (Headers sécurité)
- **express-rate-limit**: 8.0.1 (Rate limiting)

### **Communication**
- **ws**: 8.18.0 (WebSocket serveur)
- **nodemailer**: 7.0.5 (Envoi emails)

### **Validation & Utils**
- **zod**: 3.24.2 (Schema validation)
- **zod-validation-error**: 3.4.0 (Error handling)

### **Development**
- **drizzle-kit**: 0.30.4 (CLI migrations)
- **esbuild**: 0.25.0 (Build tool)

---

## 🔧 UTILITAIRES

### **Process Monitor** (`utils/process-monitor.ts`)

#### **ensurePortAvailable(port: number): Promise<void>**
- **Fonction**: Vérification port disponible
- **Cleanup**: Fermeture processus existants
- **Timeout**: Attente libération port

#### **ServerMonitor Class**
- **Health checks**: Monitoring continu serveur
- **Memory tracking**: Surveillance mémoire
- **Performance**: Métriques performance
- **Alerts**: Notifications problèmes

### **Vite Stabilizer** (`utils/vite-stabilizer.ts`)
- **HMR**: Stabilisation hot reload
- **Reconnection**: Gestion reconnexions
- **Error filtering**: Suppression erreurs dev
- **WebSocket**: Optimisation connexions

---

## 🌐 INTÉGRATIONS EXTERNES

### **Google Cloud Storage** (Préparé)
- **Library**: @google-cloud/storage 7.16.0
- **Usage**: Upload fichiers et documents
- **Authentication**: Service account
- **Buckets**: Séparation par environnement

### **LibreTranslate** (Intégré)
- **Library**: libretranslate 1.0.1
- **Usage**: Traduction automatique contenu
- **Languages**: Multi-langue support
- **Fallback**: Service local ou externe

### **Google Auth** (Préparé)
- **Library**: google-auth-library 10.2.1
- **Usage**: SSO Google Workspace
- **OAuth**: OpenID Connect
- **Integration**: Avec system permissions

---

## 📊 MÉTRIQUES ET MONITORING

### **Application Metrics**
- **Response time**: Temps réponse API
- **Request count**: Nombre requêtes par endpoint
- **Error rate**: Taux d'erreur par route
- **Active users**: Utilisateurs connectés

### **Database Metrics** (Préparé)
- **Query time**: Performance requêtes
- **Connection pool**: Utilisation connexions
- **Data size**: Taille données par table
- **Growth rate**: Évolution données

### **System Metrics**
- **Memory usage**: RAM utilisée
- **CPU usage**: Charge processeur
- **Disk space**: Espace disque
- **Network**: Bande passante

---

## 🔐 SÉCURITÉ

### **Input Validation**
- **Zod schemas**: Validation stricte
- **Sanitization**: Nettoyage inputs
- **Type safety**: TypeScript strict
- **SQL injection**: Protection ORM

### **Authentication Security**
- **bcrypt**: Hash passwords (salt 12)
- **Session management**: Secure cookies
- **Rate limiting**: Protection brute force
- **Account lockout**: Prévu pour échecs

### **API Security**
- **CORS**: Configuration restrictive
- **Headers**: Security headers (Helmet)
- **Content-Type**: Validation strict
- **File upload**: Validation types/tailles

### **Data Protection**
- **Encryption**: Données sensibles
- **Access control**: Permissions granulaires
- **Audit logs**: Traçabilité actions
- **Backup**: Stratégie sauvegarde

---

## 🚀 PERFORMANCES

### **Caching Strategy**
- **In-memory**: Données fréquentes
- **Query optimization**: Index database
- **CDN ready**: Assets statiques
- **Compression**: Gzip responses

### **Scalability**
- **Stateless**: Sessions externalisables
- **Database**: Pool connexions
- **Load balancing**: Ready pour cluster
- **Microservices**: Architecture modulaire

---

## 🔄 CI/CD ET DÉPLOIEMENT

### **Build Process**
- **TypeScript**: Compilation production
- **Bundle**: Optimisation assets
- **Minification**: Code compressé
- **Tree shaking**: Élimination dead code

### **Environment Management**
- **Development**: Local avec HMR
- **Staging**: Test complet
- **Production**: Optimisé performance
- **Configuration**: Variables env

### **Health Checks**
- **Endpoint**: /health avec métriques
- **Database**: Connexion test
- **External services**: Disponibilité
- **Response format**: JSON standardisé

---

## 📝 LOGGING ET DEBUGGING

### **Request Logging**
- **Format**: Structured JSON logs
- **Metrics**: Duration, status, response size
- **Filtering**: Exclude static assets
- **Levels**: Error, warn, info, debug

### **Error Handling**
- **Global handler**: Catch all errors
- **Status codes**: Standardisés
- **Error format**: Consistent JSON
- **Stack traces**: Development uniquement

### **Debug Tools**
- **Development**: Source maps
- **Performance**: Timing logs
- **Memory**: Usage tracking
- **Profiling**: Ready pour production

---

## 📋 RÉSUMÉ TECHNIQUE

**Total Tables**: 21 tables PostgreSQL
**Total API Endpoints**: 60+ routes RESTful
**Total Interface Methods**: 140+ méthodes typées
**Authentication**: Session-based avec bcrypt
**Real-time**: WebSocket intégré
**Security**: Headers + Rate limiting + Validation
**Storage**: Interface-based (MemStorage/PostgreSQL)
**Validation**: Zod schemas partagés
**Performance**: Optimisé avec monitoring
**Scalability**: Architecture modulaire et stateless

Cette architecture backend offre une base solide, sécurisée et scalable pour IntraSphere, avec toutes les fonctionnalités d'une application d'entreprise moderne, incluant un système de formation e-learning complet, un forum communautaire, et des outils d'administration avancés.