import { type User, type InsertUser, type Announcement, type InsertAnnouncement, type Document, type InsertDocument, type Event, type InsertEvent, type Message, type InsertMessage, type Complaint, type InsertComplaint, type Permission, type InsertPermission, type Content, type InsertContent, type Category, type InsertCategory, type EmployeeCategory, type InsertEmployeeCategory, type SystemSettings, type InsertSystemSettings, type Training, type InsertTraining, type TrainingParticipant, type InsertTrainingParticipant, type Course, type InsertCourse, type Lesson, type InsertLesson, type Quiz, type InsertQuiz, type Enrollment, type LessonProgress, type QuizAttempt, type Certificate, type Resource, type InsertResource, type ForumCategory, type InsertForumCategory, type ForumTopic, type InsertForumTopic, type ForumPost, type InsertForumPost, type ForumLike, type InsertForumLike, type ForumUserStats } from "@shared/schema";
import { randomUUID } from "crypto";

export interface IStorage {
  // Users
  getUser(id: string): Promise<User | undefined>;
  getUserByUsername(username: string): Promise<User | undefined>;
  getUserByEmployeeId(employeeId: string): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  updateUser(id: string, user: Partial<User>): Promise<User>;
  getUsers(): Promise<User[]>;
  
  // Announcements
  getAnnouncements(): Promise<Announcement[]>;
  getAnnouncementById(id: string): Promise<Announcement | undefined>;
  createAnnouncement(announcement: InsertAnnouncement): Promise<Announcement>;
  updateAnnouncement(id: string, announcement: Partial<Announcement>): Promise<Announcement>;
  deleteAnnouncement(id: string): Promise<void>;
  
  // Documents
  getDocuments(): Promise<Document[]>;
  getDocumentById(id: string): Promise<Document | undefined>;
  createDocument(document: InsertDocument): Promise<Document>;
  updateDocument(id: string, document: Partial<Document>): Promise<Document>;
  deleteDocument(id: string): Promise<void>;
  
  // Events
  getEvents(): Promise<Event[]>;
  getEventById(id: string): Promise<Event | undefined>;
  createEvent(event: InsertEvent): Promise<Event>;
  updateEvent(id: string, event: Partial<Event>): Promise<Event>;
  deleteEvent(id: string): Promise<void>;
  
  // Messages
  getMessages(userId: string): Promise<Message[]>;
  getMessageById(id: string): Promise<Message | undefined>;
  createMessage(message: InsertMessage): Promise<Message>;
  markMessageAsRead(id: string): Promise<void>;
  
  // Complaints
  getComplaints(): Promise<Complaint[]>;
  getComplaintById(id: string): Promise<Complaint | undefined>;
  getComplaintsByUser(userId: string): Promise<Complaint[]>;
  createComplaint(complaint: InsertComplaint): Promise<Complaint>;
  updateComplaint(id: string, complaint: Partial<Complaint>): Promise<Complaint>;
  
  // Permissions
  getPermissions(userId: string): Promise<Permission[]>;
  createPermission(permission: InsertPermission): Promise<Permission>;
  revokePermission(id: string): Promise<void>;
  hasPermission(userId: string, permission: string): Promise<boolean>;
  
  // Contents
  getContents(): Promise<Content[]>;
  getContentById(id: string): Promise<Content | undefined>;
  createContent(content: InsertContent): Promise<Content>;
  updateContent(id: string, content: Partial<Content>): Promise<Content>;
  deleteContent(id: string): Promise<void>;
  
  // Categories
  getCategories(): Promise<Category[]>;
  getCategoryById(id: string): Promise<Category | undefined>;
  createCategory(category: InsertCategory): Promise<Category>;
  updateCategory(id: string, category: Partial<Category>): Promise<Category>;
  deleteCategory(id: string): Promise<void>;
  
  // Employee Categories
  getEmployeeCategories(): Promise<EmployeeCategory[]>;
  getEmployeeCategoryById(id: string): Promise<EmployeeCategory | undefined>;
  createEmployeeCategory(category: InsertEmployeeCategory): Promise<EmployeeCategory>;
  updateEmployeeCategory(id: string, category: Partial<EmployeeCategory>): Promise<EmployeeCategory>;
  deleteEmployeeCategory(id: string): Promise<void>;
  
  // System Settings
  getSystemSettings(): Promise<SystemSettings>;
  updateSystemSettings(settings: Partial<SystemSettings>): Promise<SystemSettings>;
  
  // Trainings
  getTrainings(): Promise<Training[]>;
  getTrainingById(id: string): Promise<Training | undefined>;
  createTraining(training: InsertTraining): Promise<Training>;
  updateTraining(id: string, training: Partial<Training>): Promise<Training>;
  deleteTraining(id: string): Promise<void>;
  
  // Training Participants
  getTrainingParticipants(trainingId: string): Promise<TrainingParticipant[]>;
  getUserTrainingParticipations(userId: string): Promise<TrainingParticipant[]>;
  addTrainingParticipant(participant: InsertTrainingParticipant): Promise<TrainingParticipant>;
  updateTrainingParticipant(id: string, participant: Partial<TrainingParticipant>): Promise<TrainingParticipant>;
  removeTrainingParticipant(trainingId: string, userId: string): Promise<void>;
  
  // Stats
  getStats(): Promise<{
    totalUsers: number;
    totalAnnouncements: number;
    totalDocuments: number;
    totalEvents: number;
    totalMessages: number;
    totalComplaints: number;
    newAnnouncements: number;
    updatedDocuments: number;
    connectedUsers: number;
    pendingComplaints: number;
  }>;
  
  // Test data management
  resetToTestData(): Promise<void>;
  
  // E-Learning System Methods
  
  // Courses
  getCourses(): Promise<Course[]>;
  getCourseById(id: string): Promise<Course | undefined>;
  createCourse(course: InsertCourse): Promise<Course>;
  updateCourse(id: string, course: Partial<Course>): Promise<Course>;
  deleteCourse(id: string): Promise<void>;
  
  // Lessons
  getLessons(courseId: string): Promise<Lesson[]>;
  getLessonById(id: string): Promise<Lesson | undefined>;
  createLesson(lesson: InsertLesson): Promise<Lesson>;
  updateLesson(id: string, lesson: Partial<Lesson>): Promise<Lesson>;
  deleteLesson(id: string): Promise<void>;
  
  // Quizzes
  getQuizzes(courseId: string): Promise<Quiz[]>;
  getQuizById(id: string): Promise<Quiz | undefined>;
  createQuiz(quiz: InsertQuiz): Promise<Quiz>;
  updateQuiz(id: string, quiz: Partial<Quiz>): Promise<Quiz>;
  deleteQuiz(id: string): Promise<void>;
  
  // Enrollments and Progress
  getUserEnrollments(userId: string): Promise<Enrollment[]>;
  getEnrollmentById(id: string): Promise<Enrollment | undefined>;
  enrollUser(userId: string, courseId: string): Promise<Enrollment>;
  updateEnrollmentProgress(id: string, progress: Partial<Enrollment>): Promise<Enrollment>;
  getUserLessonProgress(userId: string, courseId: string): Promise<LessonProgress[]>;
  updateLessonProgress(userId: string, lessonId: string, courseId: string, completed: boolean): Promise<LessonProgress>;
  
  // Quiz Attempts
  getUserQuizAttempts(userId: string, quizId: string): Promise<QuizAttempt[]>;
  createQuizAttempt(attempt: Omit<QuizAttempt, 'id' | 'completedAt'>): Promise<QuizAttempt>;
  
  // Certificates
  getUserCertificates(userId: string): Promise<Certificate[]>;
  createCertificate(certificate: Omit<Certificate, 'id' | 'issuedAt'>): Promise<Certificate>;
  
  // Resources
  getResources(): Promise<Resource[]>;
  getResourceById(id: string): Promise<Resource | undefined>;
  createResource(resource: InsertResource): Promise<Resource>;
  updateResource(id: string, resource: Partial<Resource>): Promise<Resource>;
  deleteResource(id: string): Promise<void>;
  
  // Training Analytics
  getAllTrainingParticipants(): Promise<TrainingParticipant[]>;
  markLessonComplete(userId: string, courseId: string, lessonId: string): Promise<void>;
  
  // Search functionality
  searchUsers(query: string): Promise<User[]>;
  searchContent(query: string): Promise<Content[]>;
  searchDocuments(query: string): Promise<Document[]>;
  searchAnnouncements(query: string): Promise<Announcement[]>;
  
  // Forum System Methods
  
  // Forum Categories
  getForumCategories(): Promise<ForumCategory[]>;
  getForumCategoryById(id: string): Promise<ForumCategory | undefined>;
  createForumCategory(category: InsertForumCategory): Promise<ForumCategory>;
  updateForumCategory(id: string, category: Partial<ForumCategory>): Promise<ForumCategory>;
  deleteForumCategory(id: string): Promise<void>;
  
  // Forum Topics
  getForumTopics(categoryId?: string): Promise<ForumTopic[]>;
  getForumTopicById(id: string): Promise<ForumTopic | undefined>;
  createForumTopic(topic: InsertForumTopic): Promise<ForumTopic>;
  updateForumTopic(id: string, topic: Partial<ForumTopic>): Promise<ForumTopic>;
  deleteForumTopic(id: string): Promise<void>;
  
  // Forum Posts
  getForumPosts(topicId: string): Promise<ForumPost[]>;
  getForumPostById(id: string): Promise<ForumPost | undefined>;
  createForumPost(post: InsertForumPost): Promise<ForumPost>;
  updateForumPost(id: string, post: Partial<ForumPost>): Promise<ForumPost>;
  deleteForumPost(id: string): Promise<void>;
  
  // Training recommendations
  getTrainingRecommendations(userId: string): Promise<Course[]>;
  
  // Course lessons
  getCourseLessons(courseId: string): Promise<Lesson[]>;
  
  // Forum Posts
  getForumPosts(topicId: string): Promise<ForumPost[]>;
  getForumPostById(id: string): Promise<ForumPost | undefined>;
  createForumPost(post: InsertForumPost): Promise<ForumPost>;
  updateForumPost(id: string, post: Partial<ForumPost>): Promise<ForumPost>;
  deleteForumPost(id: string, deletedBy: string): Promise<void>;
  
  // Forum Likes/Reactions
  getForumPostLikes(postId: string): Promise<ForumLike[]>;
  toggleForumPostLike(like: InsertForumLike): Promise<ForumLike | null>;
  
  // Forum User Stats
  getForumUserStats(userId: string): Promise<ForumUserStats | undefined>;
  updateForumUserStats(userId: string, stats: Partial<ForumUserStats>): Promise<ForumUserStats>;
}

export class MemStorage implements IStorage {
  private users: Map<string, User>;
  private announcements: Map<string, Announcement>;
  private documents: Map<string, Document>;
  private events: Map<string, Event>;
  private messages: Map<string, Message>;
  private complaints: Map<string, Complaint>;
  private permissions: Map<string, Permission>;
  private contents: Map<string, Content>;
  private categories: Map<string, Category>;
  private employeeCategories: Map<string, EmployeeCategory>;
  private systemSettings: SystemSettings;
  // Training storage
  private trainings: Map<string, Training>;
  private trainingParticipants: Map<string, TrainingParticipant>;
  // E-learning storage
  private courses: Map<string, Course>;
  private lessons: Map<string, Lesson>;
  private quizzes: Map<string, Quiz>;
  private enrollments: Map<string, Enrollment>;
  private lessonProgress: Map<string, LessonProgress>;
  private quizAttempts: Map<string, QuizAttempt>;
  private certificates: Map<string, Certificate>;
  private resources: Map<string, Resource>;

  // Forum system storage
  private forumCategories: Map<string, ForumCategory>;
  private forumTopics: Map<string, ForumTopic>;
  private forumPosts: Map<string, ForumPost>;
  private forumLikes: Map<string, ForumLike>;
  private forumUserStats: Map<string, ForumUserStats>;

  // Configuration storage
  private viewsConfig = new Map<string, any>();
  private userSettings = new Map<string, any>();

  constructor() {
    this.users = new Map();
    this.announcements = new Map();
    this.documents = new Map();
    this.events = new Map();
    this.messages = new Map();
    this.complaints = new Map();
    this.permissions = new Map();
    this.contents = new Map();
    this.categories = new Map();
    this.employeeCategories = new Map();
    this.systemSettings = {
      id: "settings",
      showAnnouncements: true,
      showContent: true,
      showDocuments: true,
      showForum: true,
      showMessages: true,
      showComplaints: true,
      showTraining: true,
      updatedAt: new Date(),
    };
    // Initialize training storage
    this.trainings = new Map();
    this.trainingParticipants = new Map();
    // Initialize e-learning storage
    this.courses = new Map();
    this.lessons = new Map();
    this.quizzes = new Map();
    this.enrollments = new Map();
    this.lessonProgress = new Map();
    this.quizAttempts = new Map();
    this.certificates = new Map();
    this.resources = new Map();

    // Initialize forum storage
    this.forumCategories = new Map();
    this.forumTopics = new Map();
    this.forumPosts = new Map();
    this.forumLikes = new Map();
    this.forumUserStats = new Map();

    // Initialize configuration
    this.viewsConfig = new Map();
    this.userSettings = new Map();
    
    this.initializeData();
  }

  private initializeData() {
    // Create default users
    const defaultUsers: User[] = [
      {
        id: "user-1",
        username: "admin",
        password: "admin123",
        name: "Jean Dupont",
        role: "admin",
        avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=100&h=100",
        employeeId: "EMP001",
        department: "Direction",
        position: "Directeur Général",
        isActive: true,
        phone: "01 23 45 67 89",
        email: "jean.dupont@intrasphere.com",
        createdAt: new Date(),
        updatedAt: new Date()
      },
      {
        id: "user-2", 
        username: "marie.martin",
        password: "password123",
        name: "Marie Martin", 
        role: "moderator",
        avatar: "https://images.unsplash.com/photo-1494790108755-2616b612c0e7?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&h=100",
        employeeId: "EMP002",
        department: "Ressources Humaines",
        position: "Responsable RH",
        isActive: true,
        phone: "01 23 45 67 90",
        email: "marie.martin@intrasphere.com",
        createdAt: new Date(),
        updatedAt: new Date()
      },
      {
        id: "user-3",
        username: "pierre.dubois", 
        password: "password123",
        name: "Pierre Dubois",
        role: "employee",
        avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&h=100",
        employeeId: "EMP003",
        department: "Informatique",
        position: "Développeur",
        isActive: true,
        phone: "01 23 45 67 91",
        email: "pierre.dubois@intrasphere.com",
        createdAt: new Date(),
        updatedAt: new Date()
      }
    ];

    // Sample data
    const sampleAnnouncements: Announcement[] = [
      {
        id: "ann-1",
        title: "Nouvelle politique de télétravail",
        content: "À partir du 1er décembre, une nouvelle politique de télétravail hybride sera mise en place.",
        type: "important",
        authorId: "user-1",
        authorName: "Jean Dupont",
        imageUrl: null,
        icon: "📢",
        createdAt: new Date(Date.now() - 2 * 60 * 60 * 1000),
        isImportant: true
      },
      {
        id: "ann-2",
        title: "Soirée d'entreprise - 15 décembre", 
        content: "Nous organisons notre soirée de fin d'année le vendredi 15 décembre.",
        type: "event",
        authorId: "user-2",
        authorName: "Marie Martin",
        imageUrl: null,
        icon: "🎉",
        createdAt: new Date(Date.now() - 24 * 60 * 60 * 1000),
        isImportant: false
      }
    ];

    const sampleDocuments: Document[] = [
      {
        id: "doc-1",
        title: "Règlement intérieur 2024",
        description: "Version mise à jour du règlement intérieur",
        category: "regulation",
        fileName: "reglement-interieur-2024.pdf",
        fileUrl: "/documents/reglement-interieur-2024.pdf",
        updatedAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000),
        version: "2024.1"
      }
    ];

    const sampleEvents: Event[] = [
      {
        id: "event-1",
        title: "Réunion équipe marketing",
        description: "Point mensuel sur les campagnes",
        date: new Date(2024, 0, 16, 14, 0),
        location: "Salle de conférence A",
        type: "meeting",
        createdAt: new Date(),
        organizerId: "user-1"
      }
    ];

    // Sample messages
    const sampleMessages: Message[] = [
      {
        id: "msg-1",
        senderId: "user-2",
        recipientId: "user-1", 
        subject: "Nouvelle politique RH",
        content: "Bonjour Jean, j'aimerais discuter avec vous de la nouvelle politique de télétravail. Pouvez-vous me dire quand vous êtes disponible cette semaine ?",
        isRead: false,
        createdAt: new Date(Date.now() - 4 * 60 * 60 * 1000)
      },
      {
        id: "msg-2",
        senderId: "user-3",
        recipientId: "user-1",
        subject: "Problème technique",
        content: "Salut, j'ai un souci avec mon ordinateur qui ne démarre plus. Est-ce que tu peux m'aider ou me dire à qui m'adresser ?",
        isRead: true,
        createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000)
      }
    ];

    // Sample complaints
    const sampleComplaints: Complaint[] = [
      {
        id: "complaint-1",
        submitterId: "user-3",
        assignedToId: "user-2",
        title: "Climatisation défaillante",
        description: "La climatisation ne fonctionne pas correctement dans mon bureau. Il fait très chaud et cela impacte ma productivité.",
        category: "facilities",
        priority: "medium",
        status: "open",
        createdAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000),
        updatedAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000)
      }
    ];

    // Sample categories
    const sampleCategories: Category[] = [
      {
        id: "cat-1",
        name: "Formation",
        color: "#3B82F6",
        icon: "🎓",
        description: "Contenus de formation et développement des compétences",
        isVisible: true,
        sortOrder: 1,
        createdAt: new Date()
      },
      {
        id: "cat-2",
        name: "Actualités",
        color: "#10B981",
        icon: "📰",
        description: "Actualités et informations de l'entreprise",
        isVisible: true,
        sortOrder: 2,
        createdAt: new Date()
      },
      {
        id: "cat-3",
        name: "Corporate",
        color: "#8B5CF6",
        icon: "🏢",
        description: "Documents et communications corporate",
        isVisible: true,
        sortOrder: 3,
        createdAt: new Date()
      },
      {
        id: "cat-4",
        name: "Social",
        color: "#F59E0B",
        icon: "🎉",
        description: "Événements sociaux et team building",
        isVisible: true,
        sortOrder: 4,
        createdAt: new Date()
      }
    ];

    // Sample content
    const sampleContent: Content[] = [
      {
        id: "content-1",
        title: "Guide d'intégration nouveaux employés",
        type: "video",
        category: "Formation",
        description: "Vidéo d'accueil complète pour faciliter l'intégration des nouveaux collaborateurs dans l'entreprise.",
        thumbnailUrl: "https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=225",
        fileUrl: "/content/integration-guide.mp4",
        duration: "12 min",
        viewCount: 245,
        rating: 4,
        isPopular: true,
        isFeatured: true,
        tags: ["formation", "intégration", "nouveaux employés"],
        createdAt: new Date("2024-01-15T10:00:00Z"),
        updatedAt: new Date("2024-01-15T10:00:00Z")
      },
      {
        id: "content-2",
        title: "Présentation des nouveaux bureaux",
        type: "image",
        category: "Actualités",
        description: "Galerie photos complète des nouveaux espaces de travail aménagés avec les dernières technologies.",
        thumbnailUrl: "https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=225",
        fileUrl: "/content/bureaux-galerie.jpg",
        duration: null,
        viewCount: 156,
        rating: 4,
        isPopular: false,
        isFeatured: false, 
        tags: ["bureaux", "aménagement", "photos"],
        createdAt: new Date("2024-01-10T14:30:00Z"),
        updatedAt: new Date("2024-01-10T14:30:00Z")
      }
    ];

    // Sample forum data
    const sampleForumCategories: ForumCategory[] = [
      {
        id: "forum-cat-1",
        name: "Discussion Générale",
        description: "Discussions générales sur l'entreprise et le travail",
        color: "#3B82F6",
        icon: "💬",
        sortOrder: 1,
        isActive: true,
        isModerated: false,
        accessLevel: "all",
        moderatorIds: null,
        createdAt: new Date()
      },
      {
        id: "forum-cat-2", 
        name: "Annonces Officielles",
        description: "Communications importantes de la direction",
        color: "#EF4444",
        icon: "📢",
        sortOrder: 2,
        isActive: true,
        isModerated: true,
        accessLevel: "all",
        moderatorIds: '["user-1", "user-2"]',
        createdAt: new Date()
      },
      {
        id: "forum-cat-3",
        name: "Entraide & Support",
        description: "Questions techniques et demandes d'aide",
        color: "#10B981",
        icon: "🤝",
        sortOrder: 3,
        isActive: true,
        isModerated: false,
        accessLevel: "employee",
        moderatorIds: null,
        createdAt: new Date()
      },
      {
        id: "forum-cat-4",
        name: "Événements & Social",
        description: "Organisation d'événements et discussions sociales",
        color: "#F59E0B",
        icon: "🎉",
        sortOrder: 4,
        isActive: true,
        isModerated: false,
        accessLevel: "all",
        moderatorIds: null,
        createdAt: new Date()
      }
    ];

    const sampleForumTopics: ForumTopic[] = [
      {
        id: "forum-topic-1",
        categoryId: "forum-cat-1",
        title: "Bienvenue sur le nouveau forum IntraSphere !",
        description: "Présentation du nouveau système de forum intégré",
        authorId: "user-1",
        authorName: "Jean Dupont",
        isPinned: true,
        isLocked: false,
        isAnnouncement: true,
        viewCount: 125,
        replyCount: 8,
        lastReplyAt: new Date(Date.now() - 2 * 60 * 60 * 1000),
        lastReplyBy: "user-3",
        lastReplyByName: "Pierre Dubois",
        tags: '["bienvenue", "forum", "nouveau"]',
        createdAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000),
        updatedAt: new Date(Date.now() - 2 * 60 * 60 * 1000)
      },
      {
        id: "forum-topic-2",
        categoryId: "forum-cat-3",
        title: "Problème avec l'imprimante du 2e étage",
        description: "L'imprimante ne répond plus depuis ce matin",
        authorId: "user-3",
        authorName: "Pierre Dubois",
        isPinned: false,
        isLocked: false,
        isAnnouncement: false,
        viewCount: 42,
        replyCount: 3,
        lastReplyAt: new Date(Date.now() - 30 * 60 * 1000),
        lastReplyBy: "user-2",
        lastReplyByName: "Marie Martin",
        tags: '["technique", "imprimante", "problème"]',
        createdAt: new Date(Date.now() - 8 * 60 * 60 * 1000),
        updatedAt: new Date(Date.now() - 30 * 60 * 1000)
      },
      {
        id: "forum-topic-3",
        categoryId: "forum-cat-4",
        title: "Organisation pot de départ Julie",
        description: "Julie quitte l'équipe vendredi, organisons-lui un petit pot !",
        authorId: "user-2",
        authorName: "Marie Martin",
        isPinned: false,
        isLocked: false,
        isAnnouncement: false,
        viewCount: 67,
        replyCount: 12,
        lastReplyAt: new Date(Date.now() - 15 * 60 * 1000),
        lastReplyBy: "user-1",
        lastReplyByName: "Jean Dupont",
        tags: '["social", "départ", "organisation"]',
        createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000),
        updatedAt: new Date(Date.now() - 15 * 60 * 1000)
      }
    ];

    const sampleForumPosts: ForumPost[] = [
      {
        id: "forum-post-1",
        categoryId: "forum-cat-1",
        topicId: "forum-topic-1",
        authorId: "user-1",
        authorName: "Jean Dupont",
        content: "Bonjour à tous ! Je suis ravi de vous présenter notre nouveau système de forum intégré à IntraSphere. Cette nouvelle fonctionnalité va nous permettre d'échanger plus facilement et de créer une vraie communauté au sein de l'entreprise. N'hésitez pas à poser vos questions et à partager vos idées !",
        isFirstPost: true,
        parentPostId: null,
        likeCount: 5,
        isEdited: false,
        editedAt: null,
        editedBy: null,
        isDeleted: false,
        deletedAt: null,
        deletedBy: null,
        attachments: null,
        createdAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000),
        updatedAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000)
      },
      {
        id: "forum-post-2",
        categoryId: "forum-cat-1",
        topicId: "forum-topic-1", 
        authorId: "user-2",
        authorName: "Marie Martin",
        content: "Excellente initiative ! Le forum va vraiment améliorer notre communication interne. J'ai hâte de voir toutes les discussions qui vont naître ici.",
        isFirstPost: false,
        parentPostId: null,
        likeCount: 3,
        isEdited: false,
        editedAt: null,
        editedBy: null,
        isDeleted: false,
        deletedAt: null,
        deletedBy: null,
        attachments: null,
        createdAt: new Date(Date.now() - 4 * 24 * 60 * 60 * 1000),
        updatedAt: new Date(Date.now() - 4 * 24 * 60 * 60 * 1000)
      },
      {
        id: "forum-post-3",
        categoryId: "forum-cat-3",
        topicId: "forum-topic-2",
        authorId: "user-3",
        authorName: "Pierre Dubois",
        content: "Bonjour, j'ai un problème avec l'imprimante du 2e étage. Elle ne répond plus depuis ce matin et j'ai plusieurs documents urgents à imprimer. Est-ce que quelqu'un sait ce qui se passe ?",
        isFirstPost: true,
        parentPostId: null,
        likeCount: 1,
        isEdited: false,
        editedAt: null,
        editedBy: null,
        isDeleted: false,
        deletedAt: null,
        deletedBy: null,
        attachments: null,
        createdAt: new Date(Date.now() - 8 * 60 * 60 * 1000),
        updatedAt: new Date(Date.now() - 8 * 60 * 60 * 1000)
      },
      {
        id: "forum-post-4",
        categoryId: "forum-cat-3",
        topicId: "forum-topic-2",
        authorId: "user-2", 
        authorName: "Marie Martin",
        content: "Salut Pierre, j'ai contacté le service technique. Ils vont passer dans la matinée pour réparer l'imprimante. En attendant, tu peux utiliser celle du 1er étage si c'est urgent !",
        isFirstPost: false,
        parentPostId: null,
        likeCount: 2,
        isEdited: false,
        editedAt: null,
        editedBy: null,
        isDeleted: false,
        deletedAt: null,
        deletedBy: null,
        attachments: null,
        createdAt: new Date(Date.now() - 30 * 60 * 1000),
        updatedAt: new Date(Date.now() - 30 * 60 * 1000)
      }
    ];

    // Initialize forum stats for users
    const sampleForumUserStats: ForumUserStats[] = [
      {
        id: "forum-stats-1",
        userId: "user-1", 
        postCount: 2,
        topicCount: 1,
        likeCount: 5,
        reputationScore: 25,
        badges: '["admin", "pioneer"]',
        joinedAt: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000),
        lastActiveAt: new Date(Date.now() - 60 * 1000)
      },
      {
        id: "forum-stats-2",
        userId: "user-2",
        postCount: 2,
        topicCount: 1, 
        likeCount: 3,
        reputationScore: 18,
        badges: '["moderator", "helper"]',
        joinedAt: new Date(Date.now() - 25 * 24 * 60 * 60 * 1000),
        lastActiveAt: new Date(Date.now() - 30 * 60 * 1000)
      },
      {
        id: "forum-stats-3",
        userId: "user-3",
        postCount: 1,
        topicCount: 1,
        likeCount: 1,
        reputationScore: 5,
        badges: '["newbie"]',
        joinedAt: new Date(Date.now() - 20 * 24 * 60 * 60 * 1000),
        lastActiveAt: new Date(Date.now() - 8 * 60 * 60 * 1000)
      }
    ];

    // Initialize maps
    defaultUsers.forEach(user => this.users.set(user.id, user));
    sampleAnnouncements.forEach(ann => this.announcements.set(ann.id, ann));
    sampleDocuments.forEach(doc => this.documents.set(doc.id, doc));
    sampleEvents.forEach(event => this.events.set(event.id, event));
    sampleMessages.forEach(msg => this.messages.set(msg.id, msg));
    sampleComplaints.forEach(complaint => this.complaints.set(complaint.id, complaint));
    sampleCategories.forEach(cat => this.categories.set(cat.id, cat));
    sampleContent.forEach(content => this.contents.set(content.id, content));
    
    // Initialize sample employee categories
    const sampleEmployeeCategories: EmployeeCategory[] = [
      {
        id: "emp-cat-1",
        name: "Développeurs",
        description: "Équipe de développement logiciel",
        color: "#3B82F6",
        permissions: ["validate_posts"],
        isActive: true,
        createdAt: new Date(),
      },
      {
        id: "emp-cat-2", 
        name: "Managers",
        description: "Personnel d'encadrement",
        color: "#8B5CF6",
        permissions: ["validate_topics", "validate_posts", "manage_employee_categories"],
        isActive: true,
        createdAt: new Date(),
      },
      {
        id: "emp-cat-3",
        name: "RH",
        description: "Ressources humaines",
        color: "#10B981",
        permissions: ["validate_topics", "validate_posts"],
        isActive: true,
        createdAt: new Date(),
      }
    ];
    
    sampleEmployeeCategories.forEach(cat => this.employeeCategories.set(cat.id, cat));
    
    // Initialize forum data
    sampleForumCategories.forEach(cat => this.forumCategories.set(cat.id, cat));
    sampleForumTopics.forEach(topic => this.forumTopics.set(topic.id, topic));
    sampleForumPosts.forEach(post => this.forumPosts.set(post.id, post));
    sampleForumUserStats.forEach(stats => this.forumUserStats.set(stats.id, stats));
  }

  // Users
  async getUser(id: string): Promise<User | undefined> {
    return this.users.get(id);
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    return Array.from(this.users.values()).find(user => user.username === username);
  }

  async getUserByEmployeeId(employeeId: string): Promise<User | undefined> {
    return Array.from(this.users.values()).find(user => user.employeeId === employeeId);
  }

  async getUsers(): Promise<User[]> {
    return Array.from(this.users.values()).filter(user => user.isActive);
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const id = randomUUID();
    const user: User = {
      ...insertUser,
      id,
      role: insertUser.role || "employee",
      avatar: insertUser.avatar || null,
      employeeId: insertUser.employeeId || null,
      department: insertUser.department || null,
      position: insertUser.position || null,
      phone: insertUser.phone || null,
      email: insertUser.email || null,
      isActive: true,
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.users.set(id, user);
    return user;
  }

  async updateUser(id: string, updates: Partial<User>): Promise<User> {
    const user = this.users.get(id);
    if (!user) throw new Error("User not found");
    const updatedUser = { ...user, ...updates, updatedAt: new Date() };
    this.users.set(id, updatedUser);
    return updatedUser;
  }

  // Announcements
  async getAnnouncements(): Promise<Announcement[]> {
    return Array.from(this.announcements.values()).sort(
      (a, b) => b.createdAt.getTime() - a.createdAt.getTime()
    );
  }

  async getAnnouncementById(id: string): Promise<Announcement | undefined> {
    return this.announcements.get(id);
  }

  async createAnnouncement(insertAnnouncement: InsertAnnouncement): Promise<Announcement> {
    const id = randomUUID();
    const announcement: Announcement = {
      ...insertAnnouncement,
      id,
      type: insertAnnouncement.type || "info",
      authorId: "user-1", // Fixed authorId since it's not in the insert schema
      imageUrl: insertAnnouncement.imageUrl || null,
      icon: insertAnnouncement.icon || null,
      createdAt: new Date(),
      isImportant: insertAnnouncement.isImportant || false
    };
    this.announcements.set(id, announcement);
    return announcement;
  }

  async updateAnnouncement(id: string, updates: Partial<Announcement>): Promise<Announcement> {
    const announcement = this.announcements.get(id);
    if (!announcement) throw new Error("Announcement not found");
    const updatedAnnouncement = { ...announcement, ...updates };
    this.announcements.set(id, updatedAnnouncement);
    return updatedAnnouncement;
  }

  async deleteAnnouncement(id: string): Promise<void> {
    this.announcements.delete(id);
  }

  // Documents
  async getDocuments(): Promise<Document[]> {
    return Array.from(this.documents.values()).sort(
      (a, b) => b.updatedAt.getTime() - a.updatedAt.getTime()
    );
  }

  async getDocumentById(id: string): Promise<Document | undefined> {
    return this.documents.get(id);
  }

  async createDocument(insertDocument: InsertDocument): Promise<Document> {
    const id = randomUUID();
    const document: Document = {
      ...insertDocument,
      id,
      description: insertDocument.description || null,
      version: insertDocument.version || "1.0",
      updatedAt: new Date()
    };
    this.documents.set(id, document);
    return document;
  }

  async updateDocument(id: string, updates: Partial<Document>): Promise<Document> {
    const document = this.documents.get(id);
    if (!document) throw new Error("Document not found");
    const updatedDocument = { ...document, ...updates, updatedAt: new Date() };
    this.documents.set(id, updatedDocument);
    return updatedDocument;
  }

  async deleteDocument(id: string): Promise<void> {
    this.documents.delete(id);
  }

  // Events
  async getEvents(): Promise<Event[]> {
    return Array.from(this.events.values()).sort(
      (a, b) => a.date.getTime() - b.date.getTime()
    );
  }

  async getEventById(id: string): Promise<Event | undefined> {
    return this.events.get(id);
  }

  async createEvent(insertEvent: InsertEvent): Promise<Event> {
    const id = randomUUID();
    const event: Event = {
      ...insertEvent,
      id,
      type: insertEvent.type || "meeting",
      description: insertEvent.description || null,
      location: insertEvent.location || null,
      organizerId: insertEvent.organizerId || null,
      createdAt: new Date()
    };
    this.events.set(id, event);
    return event;
  }

  async updateEvent(id: string, updates: Partial<Event>): Promise<Event> {
    const event = this.events.get(id);
    if (!event) throw new Error("Event not found");
    const updatedEvent = { ...event, ...updates };
    this.events.set(id, updatedEvent);
    return updatedEvent;
  }

  async deleteEvent(id: string): Promise<void> {
    this.events.delete(id);
  }

  // Messages
  async getMessages(userId: string): Promise<Message[]> {
    return Array.from(this.messages.values())
      .filter(message => message.senderId === userId || message.recipientId === userId)
      .sort((a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0));
  }

  async getMessageById(id: string): Promise<Message | undefined> {
    return this.messages.get(id);
  }

  async createMessage(insertMessage: InsertMessage): Promise<Message> {
    const id = randomUUID();
    const message: Message = {
      ...insertMessage,
      id,
      isRead: false,
      createdAt: new Date()
    };
    this.messages.set(id, message);
    return message;
  }

  async markMessageAsRead(id: string): Promise<void> {
    const message = this.messages.get(id);
    if (message) {
      message.isRead = true;
      this.messages.set(id, message);
    }
  }

  // Complaints
  async getComplaints(): Promise<Complaint[]> {
    return Array.from(this.complaints.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }

  async getComplaintById(id: string): Promise<Complaint | undefined> {
    return this.complaints.get(id);
  }

  async getComplaintsByUser(userId: string): Promise<Complaint[]> {
    return Array.from(this.complaints.values())
      .filter(complaint => complaint.submitterId === userId)
      .sort((a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0));
  }

  async createComplaint(insertComplaint: InsertComplaint): Promise<Complaint> {
    const id = randomUUID();
    const complaint: Complaint = {
      ...insertComplaint,
      id,
      assignedToId: insertComplaint.assignedToId || null,
      priority: insertComplaint.priority || "medium",
      status: "open",
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.complaints.set(id, complaint);
    return complaint;
  }

  async updateComplaint(id: string, updates: Partial<Complaint>): Promise<Complaint> {
    const complaint = this.complaints.get(id);
    if (!complaint) throw new Error("Complaint not found");
    const updatedComplaint = { ...complaint, ...updates, updatedAt: new Date() };
    this.complaints.set(id, updatedComplaint);
    return updatedComplaint;
  }

  // Permissions
  async getPermissions(userId: string): Promise<Permission[]> {
    return Array.from(this.permissions.values())
      .filter(permission => permission.userId === userId);
  }

  async createPermission(insertPermission: InsertPermission): Promise<Permission> {
    const id = randomUUID();
    const permission: Permission = {
      ...insertPermission,
      id,
      createdAt: new Date()
    };
    this.permissions.set(id, permission);
    return permission;
  }

  async revokePermission(id: string): Promise<void> {
    this.permissions.delete(id);
  }

  async hasPermission(userId: string, permissionType: string): Promise<boolean> {
    const user = await this.getUser(userId);
    if (!user) return false;
    
    if (user.role === "admin") return true;
    
    const userPermissions = await this.getPermissions(userId);
    return userPermissions.some(p => p.permission === permissionType);
  }

  // Contents
  async getContents(): Promise<Content[]> {
    return Array.from(this.contents.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }

  async getContentById(id: string): Promise<Content | undefined> {
    return this.contents.get(id);
  }

  async createContent(insertContent: InsertContent): Promise<Content> {
    const id = randomUUID();
    const content: Content = {
      ...insertContent,
      id,
      description: insertContent.description || null,
      thumbnailUrl: insertContent.thumbnailUrl || null,
      duration: insertContent.duration || null,
      viewCount: 0,
      rating: 0,
      isPopular: insertContent.isPopular || false,
      isFeatured: insertContent.isFeatured || false,
      tags: insertContent.tags || [],
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.contents.set(id, content);
    return content;
  }

  async updateContent(id: string, updates: Partial<Content>): Promise<Content> {
    const content = this.contents.get(id);
    if (!content) throw new Error("Content not found");
    const updatedContent = { ...content, ...updates, updatedAt: new Date() };
    this.contents.set(id, updatedContent);
    return updatedContent;
  }

  async deleteContent(id: string): Promise<void> {
    this.contents.delete(id);
  }

  // Categories
  async getCategories(): Promise<Category[]> {
    return Array.from(this.categories.values()).sort(
      (a, b) => (a.sortOrder || 0) - (b.sortOrder || 0)
    );
  }

  async getCategoryById(id: string): Promise<Category | undefined> {
    return this.categories.get(id);
  }

  async createCategory(insertCategory: InsertCategory): Promise<Category> {
    const id = randomUUID();
    const category: Category = {
      ...insertCategory,
      id,
      icon: insertCategory.icon || null,
      color: insertCategory.color || null,
      description: insertCategory.description || null,
      isVisible: insertCategory.isVisible ?? true,
      sortOrder: insertCategory.sortOrder || 0,
      createdAt: new Date()
    };
    this.categories.set(id, category);
    return category;
  }

  async updateCategory(id: string, updates: Partial<Category>): Promise<Category> {
    const category = this.categories.get(id);
    if (!category) throw new Error("Category not found");
    const updatedCategory = { ...category, ...updates };
    this.categories.set(id, updatedCategory);
    return updatedCategory;
  }

  async deleteCategory(id: string): Promise<void> {
    this.categories.delete(id);
  }

  // Employee Categories implementation
  async getEmployeeCategories(): Promise<EmployeeCategory[]> {
    return Array.from(this.employeeCategories.values()).sort(
      (a, b) => a.name.localeCompare(b.name)
    );
  }

  async getEmployeeCategoryById(id: string): Promise<EmployeeCategory | undefined> {
    return this.employeeCategories.get(id);
  }

  async createEmployeeCategory(insertCategory: InsertEmployeeCategory): Promise<EmployeeCategory> {
    const id = randomUUID();
    const category: EmployeeCategory = {
      ...insertCategory,
      id,
      description: insertCategory.description || null,
      color: insertCategory.color || "#10B981",
      permissions: insertCategory.permissions || [],
      isActive: insertCategory.isActive ?? true,
      createdAt: new Date()
    };
    this.employeeCategories.set(id, category);
    return category;
  }

  async updateEmployeeCategory(id: string, updates: Partial<EmployeeCategory>): Promise<EmployeeCategory> {
    const category = this.employeeCategories.get(id);
    if (!category) throw new Error("Employee category not found");
    const updatedCategory = { ...category, ...updates };
    this.employeeCategories.set(id, updatedCategory);
    return updatedCategory;
  }

  async deleteEmployeeCategory(id: string): Promise<void> {
    this.employeeCategories.delete(id);
  }

  // System Settings implementation
  async getSystemSettings(): Promise<SystemSettings> {
    return this.systemSettings;
  }

  async updateSystemSettings(settings: Partial<SystemSettings>): Promise<SystemSettings> {
    this.systemSettings = {
      ...this.systemSettings,
      ...settings,
      updatedAt: new Date(),
    };
    return this.systemSettings;
  }

  // Trainings
  async getTrainings(): Promise<Training[]> {
    return Array.from(this.trainings.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }

  async getTrainingById(id: string): Promise<Training | undefined> {
    return this.trainings.get(id);
  }

  async createTraining(insertTraining: InsertTraining): Promise<Training> {
    const id = randomUUID();
    const training: Training = {
      ...insertTraining,
      id,
      instructorId: null,
      startDate: insertTraining.startDate || null,
      endDate: insertTraining.endDate || null,
      location: insertTraining.location || null,
      maxParticipants: insertTraining.maxParticipants || null,
      currentParticipants: 0,
      isActive: insertTraining.isActive !== undefined ? insertTraining.isActive : true,
      isVisible: insertTraining.isVisible !== undefined ? insertTraining.isVisible : true,
      isMandatory: insertTraining.isMandatory !== undefined ? insertTraining.isMandatory : false,
      difficulty: insertTraining.difficulty || null,
      description: insertTraining.description || null,
      thumbnailUrl: insertTraining.thumbnailUrl || null,
      documentUrls: insertTraining.documentUrls || [],
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.trainings.set(id, training);
    return training;
  }

  async updateTraining(id: string, updates: Partial<Training>): Promise<Training> {
    const training = this.trainings.get(id);
    if (!training) throw new Error("Training not found");
    const updatedTraining = { ...training, ...updates, updatedAt: new Date() };
    this.trainings.set(id, updatedTraining);
    return updatedTraining;
  }

  async deleteTraining(id: string): Promise<void> {
    // Remove all participants for this training
    const participants = Array.from(this.trainingParticipants.values())
      .filter(p => p.trainingId === id);
    participants.forEach(p => this.trainingParticipants.delete(p.id));
    
    // Delete the training
    this.trainings.delete(id);
  }

  // Training Participants
  async getTrainingParticipants(trainingId: string): Promise<TrainingParticipant[]> {
    return Array.from(this.trainingParticipants.values())
      .filter(p => p.trainingId === trainingId)
      .sort((a, b) => (b.registeredAt?.getTime() || 0) - (a.registeredAt?.getTime() || 0));
  }

  async getUserTrainingParticipations(userId: string): Promise<TrainingParticipant[]> {
    return Array.from(this.trainingParticipants.values())
      .filter(p => p.userId === userId)
      .sort((a, b) => (b.registeredAt?.getTime() || 0) - (a.registeredAt?.getTime() || 0));
  }

  async addTrainingParticipant(insertParticipant: InsertTrainingParticipant): Promise<TrainingParticipant> {
    const id = randomUUID();
    const participant: TrainingParticipant = {
      ...insertParticipant,
      id,
      registeredAt: new Date(),
      status: insertParticipant.status || "registered",
      completionDate: insertParticipant.completionDate || null,
      score: insertParticipant.score || null,
      feedback: insertParticipant.feedback || null
    };
    this.trainingParticipants.set(id, participant);
    
    // Update participant count in training
    const training = this.trainings.get(insertParticipant.trainingId);
    if (training) {
      training.currentParticipants = (training.currentParticipants || 0) + 1;
      this.trainings.set(training.id, training);
    }
    
    return participant;
  }

  async updateTrainingParticipant(id: string, updates: Partial<TrainingParticipant>): Promise<TrainingParticipant> {
    const participant = this.trainingParticipants.get(id);
    if (!participant) throw new Error("Training participant not found");
    const updatedParticipant = { ...participant, ...updates };
    this.trainingParticipants.set(id, updatedParticipant);
    return updatedParticipant;
  }

  async removeTrainingParticipant(trainingId: string, userId: string): Promise<void> {
    const participant = Array.from(this.trainingParticipants.values())
      .find(p => p.trainingId === trainingId && p.userId === userId);
    
    if (participant) {
      this.trainingParticipants.delete(participant.id);
      
      // Update participant count in training
      const training = this.trainings.get(trainingId);
      if (training) {
        training.currentParticipants = Math.max(0, (training.currentParticipants || 0) - 1);
        this.trainings.set(training.id, training);
      }
    }
  }

  // Views Configuration Management
  private viewsConfiguration: Map<string, any> = new Map();

  async getViewsConfig(): Promise<any[]> {
    if (this.viewsConfiguration.size === 0) {
      // Return default configuration if none exists
      const defaultViews = [
        {
          id: "dashboard",
          name: "Tableau de Bord",
          description: "Vue d'accueil avec statistiques et raccourcis",
          icon: "Home",
          isVisible: true,
          sortOrder: 1,
          color: "#3B82F6",
          isCore: true,
          accessLevel: "all",
          category: "main"
        },
        {
          id: "announcements", 
          name: "Annonces",
          description: "Communications officielles de l'entreprise",
          icon: "AlertTriangle",
          isVisible: true,
          sortOrder: 2,
          color: "#EF4444",
          isCore: true,
          accessLevel: "all",
          category: "communication"
        },
        {
          id: "documents",
          name: "Documents",
          description: "Bibliothèque de documents et règlements",
          icon: "FileText",
          isVisible: true,
          sortOrder: 3,
          color: "#10B981",
          isCore: true,
          accessLevel: "all",
          category: "main"
        },
        {
          id: "content",
          name: "Contenu",
          description: "Galerie multimédia et ressources de formation",
          icon: "FolderOpen",
          isVisible: true,
          sortOrder: 4,
          color: "#8B5CF6",
          isCore: false,
          accessLevel: "all",
          category: "main"
        },
        {
          id: "directory",
          name: "Annuaire",
          description: "Répertoire des employés et contacts",
          icon: "Users",
          isVisible: true,
          sortOrder: 5,
          color: "#F59E0B",
          isCore: false,
          accessLevel: "employee",
          category: "tools"
        },
        {
          id: "messages",
          name: "Messages",
          description: "Système de messagerie interne",
          icon: "MessageCircle",
          isVisible: true,
          sortOrder: 6,
          color: "#06B6D4",
          isCore: false,
          accessLevel: "employee",
          category: "communication"
        },
        {
          id: "complaints",
          name: "Réclamations",
          description: "Système de gestion des plaintes et suggestions",
          icon: "AlertTriangle",
          isVisible: false,
          sortOrder: 7,
          color: "#DC2626",
          isCore: false,
          accessLevel: "employee",
          category: "management"
        },
        {
          id: "administration",
          name: "Administration",
          description: "Panneau d'administration et gestion des utilisateurs",
          icon: "Shield",
          isVisible: true,
          sortOrder: 8,
          color: "#7C3AED",
          isCore: true,
          accessLevel: "admin",
          category: "management"
        }
      ];
      
      // Initialize default views
      defaultViews.forEach(view => {
        this.viewsConfiguration.set(view.id, view);
      });
    }
    
    return Array.from(this.viewsConfiguration.values()).sort((a, b) => a.sortOrder - b.sortOrder);
  }

  async saveViewsConfig(views: any[]): Promise<void> {
    this.viewsConfiguration.clear();
    views.forEach(view => {
      this.viewsConfiguration.set(view.id, view);
    });
  }

  async updateViewConfig(viewId: string, updates: any): Promise<void> {
    const existingView = this.viewsConfiguration.get(viewId);
    if (existingView) {
      this.viewsConfiguration.set(viewId, { ...existingView, ...updates });
    }
  }

  // User Settings Management
  private userConfiguration: Map<string, any> = new Map();

  async getUserSettings(userId: string): Promise<any> {
    const settings = this.userConfiguration.get(userId);
    if (!settings) {
      // Return default settings if none exist
      const defaultSettings = {
        companyName: "IntraSphere",
        companyLogo: "",
        welcomeMessage: "Bienvenue sur votre portail d'entreprise",
        contactEmail: "contact@intrasphere.com",
        displayName: "Utilisateur",
        email: "utilisateur@entreprise.com",
        bio: "",
        department: "Non spécifié",
        position: "Employé",
        phoneNumber: "",
        location: "",
        profilePicture: "",
        emailNotifications: true,
        pushNotifications: false,
        desktopNotifications: true,
        announcementNotifications: true,
        messageNotifications: true,
        eventReminders: true,
        documentUpdates: false,
        weeklyDigest: true,
        theme: 'light',
        language: 'fr',
        compactMode: false,
        showSidebar: true,
        animationsEnabled: true,
        colorScheme: 'purple',
        fontSize: 'medium',
        profileVisible: true,
        showOnlineStatus: false,
        allowDirectMessages: true,
        shareActivityStatus: true,
        showEmailInDirectory: false,
        allowProfilePicture: true,
        developerMode: false,
        betaFeatures: false,
        analyticsEnabled: true,
        autoSave: true,
        sessionTimeout: 60
      };
      this.userConfiguration.set(userId, defaultSettings);
      return defaultSettings;
    }
    return settings;
  }

  async saveUserSettings(userId: string, settings: any): Promise<void> {
    try {
      // Validate settings object has required fields
      if (!settings || typeof settings !== 'object') {
        throw new Error('Invalid settings object');
      }
      
      this.userConfiguration.set(userId, { ...settings, updatedAt: new Date().toISOString() });
      console.log('Settings saved successfully for user:', userId);
    } catch (error) {
      console.error('Error saving user settings:', error);
      throw error;
    }
  }

  async getStats(): Promise<{
    totalUsers: number;
    totalAnnouncements: number;
    totalDocuments: number;
    totalEvents: number;
    totalMessages: number;
    totalComplaints: number;
    newAnnouncements: number;
    updatedDocuments: number;
    connectedUsers: number;
    pendingComplaints: number;
  }> {
    const now = new Date();
    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
    
    const newAnnouncements = Array.from(this.announcements.values())
      .filter(ann => ann.createdAt > weekAgo).length;
    
    const updatedDocuments = Array.from(this.documents.values())
      .filter(doc => doc.updatedAt > weekAgo).length;
    
    const connectedUsers = Array.from(this.users.values())
      .filter(user => user.isActive).length;
    
    const pendingComplaints = Array.from(this.complaints.values())
      .filter(complaint => complaint.status === "open").length;

    return {
      totalUsers: this.users.size,
      totalAnnouncements: this.announcements.size,
      totalDocuments: this.documents.size,
      totalEvents: this.events.size,
      totalMessages: this.messages.size,
      totalComplaints: this.complaints.size,
      newAnnouncements,
      updatedDocuments,
      connectedUsers,
      pendingComplaints
    };
  }

  // Forum methods implementation - removed duplicate, using the complete one below

  // Forum topics - removed duplicate, using the complete one below

  // Forum topic by ID - removed duplicate, using the complete one below

  // Forum posts - removed duplicate, using the complete one below

  async getTrainingRecommendations(userId: string): Promise<Course[]> {
    const courses = await this.getCourses();
    return courses.slice(0, 3); // Return first 3 as recommendations
  }

  async getCourseLessons(courseId: string): Promise<Lesson[]> {
    return this.getLessons(courseId);
  }

  async resetToTestData(): Promise<void> {
    // Import test data
    const { testUsers, testAnnouncements, testDocuments, testEvents, testMessages, testComplaints } = await import("../testData");
    
    // Clear existing data
    this.users.clear();
    this.announcements.clear();
    this.documents.clear();
    this.events.clear();
    this.messages.clear();
    this.complaints.clear();
    this.permissions.clear();
    this.contents.clear();
    this.categories.clear();
    
    // Load test data
    testUsers.forEach((user: User) => this.users.set(user.id, user));
    testAnnouncements.forEach((ann: Announcement) => this.announcements.set(ann.id, ann));
    testDocuments.forEach((doc: Document) => this.documents.set(doc.id, doc));
    testEvents.forEach((event: Event) => this.events.set(event.id, event));
    testMessages.forEach((msg: Message) => this.messages.set(msg.id, msg));
    testComplaints.forEach((complaint: Complaint) => this.complaints.set(complaint.id, complaint));
    
    // Re-initialize default categories and content
    this.initializeDefaultCategories();
    this.initializeDefaultContent();
    
    console.log("✅ Test data has been reset successfully");
  }

  private initializeDefaultCategories() {
    const defaultCategories = [
      {
        id: "cat-1",
        name: "Formation",
        color: "#10B981",
        icon: "🎓",
        description: "Matériel de formation et apprentissage",
        isVisible: true,
        sortOrder: 1,
        createdAt: new Date()
      },
      {
        id: "cat-2",
        name: "Actualités",
        color: "#3B82F6",
        icon: "📰",
        description: "Dernières nouvelles et informations",
        isVisible: true,
        sortOrder: 2,
        createdAt: new Date()
      }
    ];
    
    defaultCategories.forEach(cat => this.categories.set(cat.id, cat));
  }

  private initializeDefaultContent() {
    const defaultContent = [
      {
        id: "content-1",
        title: "Guide d'intégration nouveaux employés",
        type: "video",
        category: "Formation",
        description: "Vidéo d'accueil complète pour faciliter l'intégration des nouveaux collaborateurs.",
        thumbnailUrl: "https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=225",
        fileUrl: "/content/integration-guide.mp4",
        duration: "12 min",
        viewCount: 245,
        rating: 4,
        isPopular: true,
        isFeatured: true,
        tags: ["formation", "intégration", "nouveaux employés"],
        createdAt: new Date("2024-01-15T10:00:00Z"),
        updatedAt: new Date("2024-01-15T10:00:00Z")
      }
    ];
    
    defaultContent.forEach(content => this.contents.set(content.id, content));
  }
  
  // E-Learning System Implementation
  
  private initializeELearningData() {
    // Sample courses
    const defaultCourses: Course[] = [
      {
        id: "course-1",
        title: "Introduction aux bonnes pratiques de sécurité informatique",
        description: "Découvrez les fondamentaux de la cybersécurité et protégez vos données professionnelles.",
        category: "compliance",
        difficulty: "beginner",
        duration: 120,
        thumbnailUrl: "/images/security-course.svg",
        authorId: "admin",
        authorName: "Équipe Sécurité IT",
        isPublished: true,
        isMandatory: true,
        prerequisites: "[]",
        tags: '["sécurité", "informatique", "données", "obligatoire"]',
        createdAt: new Date(),
        updatedAt: new Date()
      },
      {
        id: "course-2",
        title: "Développement personnel et leadership",
        description: "Développez vos compétences de leadership et votre potentiel professionnel.",
        category: "leadership",
        difficulty: "intermediate",
        duration: 180,
        thumbnailUrl: "/images/leadership-course.svg",
        authorId: "marie.martin",
        authorName: "Marie Martin",
        isPublished: true,
        isMandatory: false,
        prerequisites: "[]",
        tags: '["leadership", "développement", "management"]',
        createdAt: new Date(),
        updatedAt: new Date()
      },
      {
        id: "course-3",
        title: "Communication efficace en équipe",
        description: "Améliorez votre communication interpersonnelle et vos techniques de présentation.",
        category: "soft-skills",
        difficulty: "beginner",
        duration: 90,
        thumbnailUrl: "/images/communication-course.svg",
        authorId: "pierre.dubois",
        authorName: "Pierre Dubois",
        isPublished: true,
        isMandatory: false,
        prerequisites: "[]",
        tags: '["communication", "équipe", "présentation"]',
        createdAt: new Date(),
        updatedAt: new Date()
      }
    ];

    // Sample lessons
    const defaultLessons: Lesson[] = [
      {
        id: "lesson-1",
        courseId: "course-1",
        title: "Les bases de la cybersécurité",
        description: "Introduction aux concepts fondamentaux",
        content: "<h2>Bienvenue dans le monde de la cybersécurité</h2><p>Dans cette leçon, nous allons explorer les concepts fondamentaux...</p>",
        order: 1,
        duration: 30,
        videoUrl: "/videos/security-basics.mp4",
        documentUrl: "/docs/security-guide.pdf",
        isRequired: true,
        createdAt: new Date(),
        updatedAt: new Date()
      },
      {
        id: "lesson-2",
        courseId: "course-1",
        title: "Mots de passe et authentification",
        description: "Créez des mots de passe sécurisés",
        content: "<h2>L'importance des mots de passe forts</h2><p>Un mot de passe fort est votre première ligne de défense...</p>",
        order: 2,
        duration: 25,
        videoUrl: "/videos/password-security.mp4",
        documentUrl: "/docs/password-policy.pdf",
        isRequired: true,
        createdAt: new Date(),
        updatedAt: new Date()
      }
    ];

    // Sample resources
    const defaultResources: Resource[] = [
      {
        id: "resource-1",
        title: "Guide de référence - Politiques de sécurité",
        description: "Document complet sur les politiques de sécurité de l'entreprise",
        category: "documentation",
        type: "pdf",
        url: "/resources/security-policies.pdf",
        thumbnailUrl: "/images/pdf-icon.svg",
        authorId: "admin",
        authorName: "Équipe IT",
        tags: '["sécurité", "politique", "référence"]',
        downloadCount: 45,
        rating: 4.8,
        isPublic: true,
        createdAt: new Date(),
        updatedAt: new Date()
      },
      {
        id: "resource-2",
        title: "Template - Plan de formation individuel",
        description: "Modèle pour créer votre plan de développement professionnel",
        category: "template",
        type: "document",
        url: "/resources/training-plan-template.docx",
        thumbnailUrl: "/images/doc-icon.svg",
        authorId: "marie.martin",
        authorName: "Marie Martin",
        tags: '["formation", "template", "développement"]',
        downloadCount: 23,
        rating: 4.5,
        isPublic: true,
        createdAt: new Date(),
        updatedAt: new Date()
      }
    ];

    // Initialize maps
    defaultCourses.forEach(course => this.courses.set(course.id, course));
    defaultLessons.forEach(lesson => this.lessons.set(lesson.id, lesson));
    defaultResources.forEach(resource => this.resources.set(resource.id, resource));
  }

  // Courses
  async getCourses(): Promise<Course[]> {
    return Array.from(this.courses.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }

  async getCourseById(id: string): Promise<Course | undefined> {
    return this.courses.get(id);
  }

  async createCourse(insertCourse: InsertCourse): Promise<Course> {
    const id = randomUUID();
    const course: Course = {
      ...insertCourse,
      id,
      authorId: insertCourse.authorName || "Système",
      description: insertCourse.description || null,
      duration: insertCourse.duration || null,
      thumbnailUrl: insertCourse.thumbnailUrl || null,
      isPublished: insertCourse.isPublished || false,
      isMandatory: insertCourse.isMandatory || false,
      prerequisites: insertCourse.prerequisites || "[]",
      tags: insertCourse.tags || "[]",
      difficulty: insertCourse.difficulty || "beginner",
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.courses.set(id, course);
    return course;
  }

  async updateCourse(id: string, updates: Partial<Course>): Promise<Course> {
    const course = this.courses.get(id);
    if (!course) throw new Error("Course not found");
    const updatedCourse = { ...course, ...updates, updatedAt: new Date() };
    this.courses.set(id, updatedCourse);
    return updatedCourse;
  }

  async deleteCourse(id: string): Promise<void> {
    this.courses.delete(id);
    // Also delete related lessons
    Array.from(this.lessons.values())
      .filter(lesson => lesson.courseId === id)
      .forEach(lesson => this.lessons.delete(lesson.id));
  }

  // Lessons
  async getLessons(courseId: string): Promise<Lesson[]> {
    return Array.from(this.lessons.values())
      .filter(lesson => lesson.courseId === courseId)
      .sort((a, b) => (a.order || 0) - (b.order || 0));
  }

  async getLessonById(id: string): Promise<Lesson | undefined> {
    return this.lessons.get(id);
  }

  async createLesson(insertLesson: InsertLesson): Promise<Lesson> {
    const id = randomUUID();
    const lesson: Lesson = {
      ...insertLesson,
      id,
      description: insertLesson.description || null,
      order: insertLesson.order || 0,
      duration: insertLesson.duration || null,
      videoUrl: insertLesson.videoUrl || null,
      documentUrl: insertLesson.documentUrl || null,
      isRequired: insertLesson.isRequired !== false,
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.lessons.set(id, lesson);
    return lesson;
  }

  async updateLesson(id: string, updates: Partial<Lesson>): Promise<Lesson> {
    const lesson = this.lessons.get(id);
    if (!lesson) throw new Error("Lesson not found");
    const updatedLesson = { ...lesson, ...updates, updatedAt: new Date() };
    this.lessons.set(id, updatedLesson);
    return updatedLesson;
  }

  async deleteLesson(id: string): Promise<void> {
    this.lessons.delete(id);
  }

  // Quizzes
  async getQuizzes(courseId: string): Promise<Quiz[]> {
    return Array.from(this.quizzes.values())
      .filter(quiz => quiz.courseId === courseId);
  }

  async getQuizById(id: string): Promise<Quiz | undefined> {
    return this.quizzes.get(id);
  }

  async createQuiz(insertQuiz: InsertQuiz): Promise<Quiz> {
    const id = randomUUID();
    const quiz: Quiz = {
      ...insertQuiz,
      id,
      courseId: insertQuiz.courseId || null,
      lessonId: insertQuiz.lessonId || null,
      description: insertQuiz.description || null,
      passingScore: insertQuiz.passingScore || 70,
      timeLimit: insertQuiz.timeLimit || null,
      allowRetries: insertQuiz.allowRetries !== false,
      maxAttempts: insertQuiz.maxAttempts || 3,
      isRequired: insertQuiz.isRequired || false,
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.quizzes.set(id, quiz);
    return quiz;
  }

  async updateQuiz(id: string, updates: Partial<Quiz>): Promise<Quiz> {
    const quiz = this.quizzes.get(id);
    if (!quiz) throw new Error("Quiz not found");
    const updatedQuiz = { ...quiz, ...updates, updatedAt: new Date() };
    this.quizzes.set(id, updatedQuiz);
    return updatedQuiz;
  }

  async deleteQuiz(id: string): Promise<void> {
    this.quizzes.delete(id);
  }

  // Enrollments and Progress
  async getUserEnrollments(userId: string): Promise<Enrollment[]> {
    return Array.from(this.enrollments.values())
      .filter(enrollment => enrollment.userId === userId);
  }

  async getEnrollmentById(id: string): Promise<Enrollment | undefined> {
    return this.enrollments.get(id);
  }

  async enrollUser(userId: string, courseId: string): Promise<Enrollment> {
    const id = randomUUID();
    const enrollment: Enrollment = {
      id,
      userId,
      courseId,
      enrolledAt: new Date(),
      startedAt: null,
      completedAt: null,
      progress: 0,
      status: "enrolled",
      certificateUrl: null,
      timeSpent: 0,
      score: null,
      courseTitle: null
    };
    this.enrollments.set(id, enrollment);
    return enrollment;
  }

  async updateEnrollmentProgress(id: string, updates: Partial<Enrollment>): Promise<Enrollment> {
    const enrollment = this.enrollments.get(id);
    if (!enrollment) throw new Error("Enrollment not found");
    const updatedEnrollment = { ...enrollment, ...updates };
    this.enrollments.set(id, updatedEnrollment);
    return updatedEnrollment;
  }

  async getUserLessonProgress(userId: string, courseId: string): Promise<LessonProgress[]> {
    return Array.from(this.lessonProgress.values())
      .filter(progress => progress.userId === userId && progress.courseId === courseId);
  }

  async updateLessonProgress(userId: string, lessonId: string, courseId: string, completed: boolean): Promise<LessonProgress> {
    const key = `${userId}-${lessonId}`;
    let progress = Array.from(this.lessonProgress.values())
      .find(p => p.userId === userId && p.lessonId === lessonId);

    if (!progress) {
      const id = randomUUID();
      progress = {
        id,
        userId,
        lessonId,
        courseId,
        isCompleted: completed,
        timeSpent: 0,
        completedAt: completed ? new Date() : null,
        createdAt: new Date()
      };
    } else {
      progress.isCompleted = completed;
      progress.completedAt = completed ? new Date() : null;
    }

    this.lessonProgress.set(progress.id, progress);
    return progress;
  }

  // Quiz Attempts
  async getUserQuizAttempts(userId: string, quizId: string): Promise<QuizAttempt[]> {
    return Array.from(this.quizAttempts.values())
      .filter(attempt => attempt.userId === userId && attempt.quizId === quizId)
      .sort((a, b) => (b.completedAt?.getTime() || 0) - (a.completedAt?.getTime() || 0));
  }

  async createQuizAttempt(attemptData: Omit<QuizAttempt, 'id' | 'completedAt'>): Promise<QuizAttempt> {
    const id = randomUUID();
    const attempt: QuizAttempt = {
      ...attemptData,
      id,
      completedAt: new Date()
    };
    this.quizAttempts.set(id, attempt);
    return attempt;
  }

  // Certificates
  async getUserCertificates(userId: string): Promise<Certificate[]> {
    return Array.from(this.certificates.values())
      .filter(cert => cert.userId === userId)
      .sort((a, b) => (b.issuedAt?.getTime() || 0) - (a.issuedAt?.getTime() || 0));
  }

  async createCertificate(certData: Omit<Certificate, 'id' | 'issuedAt'>): Promise<Certificate> {
    const id = randomUUID();
    const certificate: Certificate = {
      ...certData,
      id,
      issuedAt: new Date()
    };
    this.certificates.set(id, certificate);
    return certificate;
  }

  // Resources
  async getResources(): Promise<Resource[]> {
    return Array.from(this.resources.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }

  async getResourceById(id: string): Promise<Resource | undefined> {
    return this.resources.get(id);
  }

  async createResource(insertResource: InsertResource): Promise<Resource> {
    const id = randomUUID();
    const resource: Resource = {
      ...insertResource,
      id,
      description: insertResource.description || null,
      thumbnailUrl: insertResource.thumbnailUrl || null,
      authorId: insertResource.authorName || "Système",
      tags: insertResource.tags || "[]",
      downloadCount: 0,
      rating: 0,
      isPublic: insertResource.isPublic !== false,
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.resources.set(id, resource);
    return resource;
  }

  async updateResource(id: string, updates: Partial<Resource>): Promise<Resource> {
    const resource = this.resources.get(id);
    if (!resource) throw new Error("Resource not found");
    const updatedResource = { ...resource, ...updates, updatedAt: new Date() };
    this.resources.set(id, updatedResource);
    return updatedResource;
  }

  async deleteResource(id: string): Promise<void> {
    this.resources.delete(id);
  }

  // Training Analytics Implementation
  async getAllTrainingParticipants(): Promise<TrainingParticipant[]> {
    return Array.from(this.trainingParticipants.values());
  }

  async markLessonComplete(userId: string, courseId: string, lessonId: string): Promise<void> {
    await this.updateLessonProgress(userId, lessonId, courseId, true);
  }

  // Search functionality
  async searchUsers(query: string): Promise<User[]> {
    const searchTerm = query.toLowerCase();
    return Array.from(this.users.values()).filter(user => 
      user.isActive && (
        user.name.toLowerCase().includes(searchTerm) ||
        user.username.toLowerCase().includes(searchTerm) ||
        (user.email && user.email.toLowerCase().includes(searchTerm)) ||
        (user.employeeId && user.employeeId.toLowerCase().includes(searchTerm))
      )
    );
  }

  async searchContent(query: string): Promise<Content[]> {
    const lowerQuery = query.toLowerCase();
    return Array.from(this.contents.values()).filter(content =>
      content.title.toLowerCase().includes(lowerQuery) ||
      content.description?.toLowerCase().includes(lowerQuery)
    );
  }

  async searchDocuments(query: string): Promise<Document[]> {
    const lowerQuery = query.toLowerCase();
    return Array.from(this.documents.values()).filter(doc =>
      doc.title.toLowerCase().includes(lowerQuery) ||
      doc.description?.toLowerCase().includes(lowerQuery)
    );
  }

  async searchAnnouncements(query: string): Promise<Announcement[]> {
    const lowerQuery = query.toLowerCase();
    return Array.from(this.announcements.values()).filter(ann =>
      ann.title.toLowerCase().includes(lowerQuery) ||
      ann.content.toLowerCase().includes(lowerQuery)
    );
  }

  // Forum System Implementation

  // Forum Categories
  async getForumCategories(): Promise<ForumCategory[]> {
    return Array.from(this.forumCategories.values())
      .filter(category => category.isActive)
      .sort((a, b) => (a.sortOrder || 0) - (b.sortOrder || 0));
  }

  async getForumCategoryById(id: string): Promise<ForumCategory | undefined> {
    return this.forumCategories.get(id);
  }

  async createForumCategory(insertCategory: InsertForumCategory): Promise<ForumCategory> {
    const id = randomUUID();
    const category: ForumCategory = {
      ...insertCategory,
      id,
      description: insertCategory.description || null,
      color: insertCategory.color || "#3B82F6",
      icon: insertCategory.icon || "💬",
      sortOrder: insertCategory.sortOrder || 0,
      isActive: insertCategory.isActive !== false,
      isModerated: insertCategory.isModerated || false,
      accessLevel: insertCategory.accessLevel || "all",
      moderatorIds: insertCategory.moderatorIds || null,
      createdAt: new Date()
    };
    this.forumCategories.set(id, category);
    return category;
  }

  async updateForumCategory(id: string, updates: Partial<ForumCategory>): Promise<ForumCategory> {
    const category = this.forumCategories.get(id);
    if (!category) throw new Error("Forum category not found");
    const updatedCategory = { ...category, ...updates };
    this.forumCategories.set(id, updatedCategory);
    return updatedCategory;
  }

  async deleteForumCategory(id: string): Promise<void> {
    // Also delete related topics and posts
    const topics = Array.from(this.forumTopics.values()).filter(t => t.categoryId === id);
    for (const topic of topics) {
      await this.deleteForumTopic(topic.id);
    }
    this.forumCategories.delete(id);
  }

  // Forum Topics
  async getForumTopics(categoryId?: string): Promise<ForumTopic[]> {
    let topics = Array.from(this.forumTopics.values());
    if (categoryId) {
      topics = topics.filter(topic => topic.categoryId === categoryId);
    }
    return topics.sort((a, b) => {
      if (a.isPinned && !b.isPinned) return -1;
      if (!a.isPinned && b.isPinned) return 1;
      return (b.lastReplyAt?.getTime() || b.createdAt?.getTime() || 0) - 
             (a.lastReplyAt?.getTime() || a.createdAt?.getTime() || 0);
    });
  }

  async getForumTopicById(id: string): Promise<ForumTopic | undefined> {
    return this.forumTopics.get(id);
  }

  async createForumTopic(insertTopic: InsertForumTopic): Promise<ForumTopic> {
    const id = randomUUID();
    const topic: ForumTopic = {
      ...insertTopic,
      id,
      description: insertTopic.description || null,
      isPinned: insertTopic.isPinned || false,
      isLocked: insertTopic.isLocked || false,
      isAnnouncement: insertTopic.isAnnouncement || false,
      viewCount: 0,
      replyCount: 0,
      lastReplyAt: null,
      lastReplyBy: null,
      lastReplyByName: null,
      tags: insertTopic.tags || null,
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.forumTopics.set(id, topic);

    // Update user stats
    await this.updateUserTopicCount(insertTopic.authorId, 1);

    return topic;
  }

  async updateForumTopic(id: string, updates: Partial<ForumTopic>): Promise<ForumTopic> {
    const topic = this.forumTopics.get(id);
    if (!topic) throw new Error("Forum topic not found");
    const updatedTopic = { ...topic, ...updates, updatedAt: new Date() };
    this.forumTopics.set(id, updatedTopic);
    return updatedTopic;
  }

  async deleteForumTopic(id: string): Promise<void> {
    const topic = this.forumTopics.get(id);
    if (topic) {
      // Delete all posts in this topic
      const posts = Array.from(this.forumPosts.values()).filter(p => p.topicId === id);
      for (const post of posts) {
        this.forumPosts.delete(post.id);
        // Delete likes for this post
        const likes = Array.from(this.forumLikes.values()).filter(l => l.postId === post.id);
        for (const like of likes) {
          this.forumLikes.delete(like.id);
        }
      }
      
      // Update user stats
      await this.updateUserTopicCount(topic.authorId, -1);
      this.forumTopics.delete(id);
    }
  }

  async incrementTopicViews(id: string): Promise<void> {
    const topic = this.forumTopics.get(id);
    if (topic) {
      topic.viewCount = (topic.viewCount || 0) + 1;
      this.forumTopics.set(id, topic);
    }
  }

  // Forum Posts
  async getForumPosts(topicId: string): Promise<ForumPost[]> {
    return Array.from(this.forumPosts.values())
      .filter(post => post.topicId === topicId && !post.isDeleted)
      .sort((a, b) => (a.createdAt?.getTime() || 0) - (b.createdAt?.getTime() || 0));
  }

  async getForumPostById(id: string): Promise<ForumPost | undefined> {
    const post = this.forumPosts.get(id);
    return post && !post.isDeleted ? post : undefined;
  }

  async createForumPost(insertPost: InsertForumPost): Promise<ForumPost> {
    const id = randomUUID();
    const post: ForumPost = {
      ...insertPost,
      id,
      isFirstPost: insertPost.isFirstPost || false,
      parentPostId: insertPost.parentPostId || null,
      likeCount: 0,
      isEdited: false,
      editedAt: null,
      editedBy: null,
      isDeleted: false,
      deletedAt: null,
      deletedBy: null,
      attachments: insertPost.attachments || null,
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.forumPosts.set(id, post);

    // Update topic reply count and last reply info
    const topic = this.forumTopics.get(insertPost.topicId);
    if (topic && !insertPost.isFirstPost) {
      topic.replyCount = (topic.replyCount || 0) + 1;
      topic.lastReplyAt = post.createdAt;
      topic.lastReplyBy = insertPost.authorId;
      topic.lastReplyByName = insertPost.authorName;
      this.forumTopics.set(insertPost.topicId, topic);
    }

    // Update user stats
    await this.updateUserPostCount(insertPost.authorId, 1);

    return post;
  }

  async updateForumPost(id: string, updates: Partial<ForumPost>): Promise<ForumPost> {
    const post = this.forumPosts.get(id);
    if (!post || post.isDeleted) throw new Error("Forum post not found");
    
    const updatedPost = { 
      ...post, 
      ...updates, 
      isEdited: true,
      editedAt: new Date(),
      updatedAt: new Date() 
    };
    this.forumPosts.set(id, updatedPost);
    return updatedPost;
  }

  async deleteForumPost(id: string, deletedBy: string): Promise<void> {
    const post = this.forumPosts.get(id);
    if (post && !post.isDeleted) {
      post.isDeleted = true;
      post.deletedAt = new Date();
      post.deletedBy = deletedBy;
      this.forumPosts.set(id, post);

      // Update topic reply count
      const topic = this.forumTopics.get(post.topicId);
      if (topic && !post.isFirstPost) {
        topic.replyCount = Math.max((topic.replyCount || 0) - 1, 0);
        this.forumTopics.set(post.topicId, topic);
      }

      // Update user stats
      await this.updateUserPostCount(post.authorId, -1);

      // Delete likes for this post
      const likes = Array.from(this.forumLikes.values()).filter(l => l.postId === id);
      for (const like of likes) {
        this.forumLikes.delete(like.id);
      }
    }
  }

  // Forum Likes/Reactions
  async getForumPostLikes(postId: string): Promise<ForumLike[]> {
    return Array.from(this.forumLikes.values()).filter(like => like.postId === postId);
  }

  async toggleForumPostLike(insertLike: InsertForumLike): Promise<ForumLike | null> {
    // Check if user already liked this post
    const existingLike = Array.from(this.forumLikes.values())
      .find(like => like.postId === insertLike.postId && like.userId === insertLike.userId);

    if (existingLike) {
      // Remove like
      this.forumLikes.delete(existingLike.id);
      
      // Update post like count
      const post = this.forumPosts.get(insertLike.postId);
      if (post) {
        post.likeCount = Math.max((post.likeCount || 0) - 1, 0);
        this.forumPosts.set(insertLike.postId, post);
      }
      
      return null;
    } else {
      // Add like
      const id = randomUUID();
      const like: ForumLike = {
        ...insertLike,
        id,
        reactionType: insertLike.reactionType || "like",
        createdAt: new Date()
      };
      this.forumLikes.set(id, like);

      // Update post like count
      const post = this.forumPosts.get(insertLike.postId);
      if (post) {
        post.likeCount = (post.likeCount || 0) + 1;
        this.forumPosts.set(insertLike.postId, post);
      }

      return like;
    }
  }

  // Forum User Stats
  async getForumUserStats(userId: string): Promise<ForumUserStats | undefined> {
    let stats = Array.from(this.forumUserStats.values()).find(s => s.userId === userId);
    
    if (!stats) {
      // Create initial stats
      const id = randomUUID();
      stats = {
        id,
        userId,
        postCount: 0,
        topicCount: 0,
        likeCount: 0,
        reputationScore: 0,
        badges: "[]",
        joinedAt: new Date(),
        lastActiveAt: new Date()
      };
      this.forumUserStats.set(id, stats);
    }

    return stats;
  }

  async updateForumUserStats(userId: string, updates: Partial<ForumUserStats>): Promise<ForumUserStats> {
    let stats = await this.getForumUserStats(userId);
    if (!stats) throw new Error("User stats not found");

    const updatedStats = { ...stats, ...updates, lastActiveAt: new Date() };
    this.forumUserStats.set(stats.id, updatedStats);
    return updatedStats;
  }

  // Helper methods for forum stats
  private async updateUserPostCount(userId: string, change: number): Promise<void> {
    const stats = await this.getForumUserStats(userId);
    if (stats) {
      stats.postCount = Math.max((stats.postCount || 0) + change, 0);
      this.forumUserStats.set(stats.id, stats);
    }
  }

  private async updateUserTopicCount(userId: string, change: number): Promise<void> {
    const stats = await this.getForumUserStats(userId);
    if (stats) {
      stats.topicCount = Math.max((stats.topicCount || 0) + change, 0);
      this.forumUserStats.set(stats.id, stats);
    }
  }
}

export const storage = new MemStorage();