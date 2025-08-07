var __defProp = Object.defineProperty;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __esm = (fn, res) => function __init() {
  return fn && (res = (0, fn[__getOwnPropNames(fn)[0]])(fn = 0)), res;
};
var __export = (target, all) => {
  for (var name in all)
    __defProp(target, name, { get: all[name], enumerable: true });
};

// server/testData.ts
var testData_exports = {};
__export(testData_exports, {
  createTestData: () => createTestData,
  getTestDataStats: () => getTestDataStats,
  testAnnouncements: () => testAnnouncements,
  testComplaints: () => testComplaints,
  testDocuments: () => testDocuments,
  testEvents: () => testEvents,
  testMessages: () => testMessages,
  testUsers: () => testUsers
});
function createTestData() {
  return {
    users: testUsers,
    announcements: testAnnouncements,
    documents: testDocuments,
    events: testEvents,
    messages: testMessages,
    complaints: testComplaints
  };
}
function getTestDataStats() {
  return {
    totalUsers: testUsers.length,
    totalAnnouncements: testAnnouncements.length,
    totalDocuments: testDocuments.length,
    totalEvents: testEvents.length,
    totalMessages: testMessages.length,
    totalComplaints: testComplaints.length,
    newAnnouncements: testAnnouncements.filter(
      (a) => new Date(a.createdAt).getTime() > Date.now() - 7 * 24 * 60 * 60 * 1e3
    ).length,
    updatedDocuments: testDocuments.filter(
      (d) => new Date(d.updatedAt).getTime() > Date.now() - 30 * 24 * 60 * 60 * 1e3
    ).length,
    connectedUsers: Math.floor(testUsers.length * 0.6),
    // 60% connected
    pendingComplaints: testComplaints.filter((c) => c.status === "open").length
  };
}
var testUsers, testAnnouncements, testDocuments, testEvents, testMessages, testComplaints;
var init_testData = __esm({
  "server/testData.ts"() {
    "use strict";
    testUsers = [
      {
        id: "admin-1",
        username: "admin",
        password: "admin123",
        name: "Marie Dupont",
        email: "marie.dupont@intrasphere.fr",
        department: "Direction",
        position: "Directrice G\xE9n\xE9rale",
        phone: "01 23 45 67 89",
        role: "admin",
        employeeId: "EMP001",
        avatar: null,
        isActive: true,
        createdAt: new Date(Date.now() - 30 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "mod-1",
        username: "moderator",
        password: "mod123",
        name: "Pierre Martin",
        email: "pierre.martin@intrasphere.fr",
        department: "Ressources Humaines",
        position: "Responsable RH",
        phone: "01 23 45 67 90",
        role: "moderator",
        employeeId: "EMP002",
        avatar: null,
        isActive: true,
        createdAt: new Date(Date.now() - 25 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "emp-1",
        username: "employee",
        password: "emp123",
        name: "Sophie Bernard",
        email: "sophie.bernard@intrasphere.fr",
        department: "Informatique",
        position: "D\xE9veloppeuse",
        phone: "01 23 45 67 91",
        role: "employee",
        employeeId: "EMP003",
        avatar: null,
        isActive: true,
        createdAt: new Date(Date.now() - 20 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "emp-2",
        username: "jdoe",
        password: "password123",
        name: "Jean Doe",
        email: "jean.doe@intrasphere.fr",
        department: "Marketing",
        position: "Chef de projet",
        phone: "01 23 45 67 92",
        role: "employee",
        employeeId: "EMP004",
        avatar: null,
        isActive: true,
        createdAt: new Date(Date.now() - 15 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 4 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "emp-3",
        username: "adurand",
        password: "password123",
        name: "Alice Durand",
        email: "alice.durand@intrasphere.fr",
        department: "Commercial",
        position: "Commerciale",
        phone: "01 23 45 67 93",
        role: "employee",
        employeeId: "EMP005",
        avatar: null,
        isActive: true,
        createdAt: new Date(Date.now() - 10 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "emp-4",
        username: "lrobert",
        password: "password123",
        name: "Lucas Robert",
        email: "lucas.robert@intrasphere.fr",
        department: "Comptabilit\xE9",
        position: "Comptable",
        phone: "01 23 45 67 94",
        role: "employee",
        employeeId: "EMP006",
        avatar: null,
        isActive: true,
        createdAt: new Date(Date.now() - 8 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 6 * 24 * 60 * 60 * 1e3)
      }
    ];
    testAnnouncements = [
      {
        id: "ann-1",
        title: "Nouvelle politique de t\xE9l\xE9travail",
        content: "\xC0 partir du 1er f\xE9vrier, tous les employ\xE9s pourront b\xE9n\xE9ficier de 2 jours de t\xE9l\xE9travail par semaine. Cette mesure vise \xE0 am\xE9liorer l'\xE9quilibre vie professionnelle/vie priv\xE9e et \xE0 r\xE9duire les temps de transport. Les demandes doivent \xEAtre valid\xE9es par votre responsable direct.",
        type: "important",
        authorId: "admin-1",
        authorName: "Marie Dupont",
        imageUrl: null,
        icon: "\u{1F4E2}",
        createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1e3),
        isImportant: true
      },
      {
        id: "ann-2",
        title: "Formation cybers\xE9curit\xE9 obligatoire",
        content: "Tous les employ\xE9s doivent suivre la formation cybers\xE9curit\xE9 avant le 15 f\xE9vrier. Cette formation en ligne dure 2 heures et couvre les bonnes pratiques de s\xE9curit\xE9 informatique. Un certificat sera d\xE9livr\xE9 \xE0 l'issue de la formation.",
        type: "formation",
        authorId: "mod-1",
        authorName: "Pierre Martin",
        imageUrl: null,
        icon: "\u{1F393}",
        createdAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1e3),
        isImportant: false
      },
      {
        id: "ann-3",
        title: "R\xE9union mensuelle - R\xE9sultats Q4",
        content: "La r\xE9union mensuelle aura lieu le vendredi 26 janvier \xE0 14h en salle de conf\xE9rence. Ordre du jour : pr\xE9sentation des r\xE9sultats Q4, objectifs 2024, et questions diverses. La pr\xE9sence de tous les managers est requise.",
        type: "event",
        authorId: "admin-1",
        authorName: "Marie Dupont",
        imageUrl: null,
        icon: "\u{1F4C5}",
        createdAt: new Date(Date.now() - 5 * 60 * 60 * 1e3),
        isImportant: true
      },
      {
        id: "ann-4",
        title: "Nouveaux arrivants - Janvier 2024",
        content: "Nous accueillons trois nouveaux collaborateurs ce mois-ci : Emma Leroy (Marketing), Thomas Petit (IT), et Camille Moreau (Commercial). N'h\xE9sitez pas \xE0 leur souhaiter la bienvenue et \xE0 les aider dans leur int\xE9gration.",
        type: "info",
        authorId: "mod-1",
        authorName: "Pierre Martin",
        imageUrl: null,
        icon: "\u{1F44B}",
        createdAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1e3),
        isImportant: false
      },
      {
        id: "ann-5",
        title: "Maintenance serveurs - Weekend du 27-28 janvier",
        content: "Une maintenance programm\xE9e des serveurs aura lieu ce weekend. Des coupures intermittentes sont possibles samedi entre 20h et minuit. Tous les services seront pleinement op\xE9rationnels lundi matin.",
        type: "important",
        authorId: "emp-1",
        authorName: "Sophie Bernard",
        imageUrl: null,
        icon: "\u26A0\uFE0F",
        createdAt: new Date(Date.now() - 4 * 60 * 60 * 1e3),
        isImportant: true
      }
    ];
    testDocuments = [
      {
        id: "doc-1",
        title: "Politique de t\xE9l\xE9travail 2024",
        description: "Document officiel d\xE9taillant les nouvelles modalit\xE9s de t\xE9l\xE9travail",
        category: "policy",
        fileName: "politique-teletravail-2024.pdf",
        fileUrl: "/documents/politique-teletravail-2024.pdf",
        updatedAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1e3),
        version: "2.0"
      },
      {
        id: "doc-2",
        title: "Guide de s\xE9curit\xE9 informatique",
        description: "Bonnes pratiques et proc\xE9dures de s\xE9curit\xE9 pour tous les employ\xE9s",
        category: "guide",
        fileName: "guide-securite-informatique.pdf",
        fileUrl: "/documents/guide-securite-informatique.pdf",
        updatedAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1e3),
        version: "1.5"
      },
      {
        id: "doc-3",
        title: "Organigramme 2024",
        description: "Structure organisationnelle mise \xE0 jour de l'entreprise",
        category: "organization",
        fileName: "organigramme-2024.pdf",
        fileUrl: "/documents/organigramme-2024.pdf",
        updatedAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1e3),
        version: "3.0"
      },
      {
        id: "doc-4",
        title: "Proc\xE9dures RH - Cong\xE9s et absences",
        description: "Guide complet pour les demandes de cong\xE9s et la gestion des absences",
        category: "procedure",
        fileName: "procedures-conges.pdf",
        fileUrl: "/documents/procedures-conges.pdf",
        updatedAt: new Date(Date.now() - 7 * 24 * 60 * 60 * 1e3),
        version: "1.2"
      }
    ];
    testEvents = [
      {
        id: "evt-1",
        title: "R\xE9union mensuelle \xE9quipe",
        description: "Point mensuel sur les projets en cours et les objectifs",
        date: new Date(Date.now() + 2 * 24 * 60 * 60 * 1e3),
        location: "Salle de conf\xE9rence A",
        type: "meeting",
        organizerId: "admin-1",
        createdAt: new Date(Date.now() - 7 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "evt-2",
        title: "Formation cybers\xE9curit\xE9",
        description: "Session de formation obligatoire sur la cybers\xE9curit\xE9",
        date: new Date(Date.now() + 5 * 24 * 60 * 60 * 1e3),
        location: "Salle de formation",
        type: "training",
        organizerId: "mod-1",
        createdAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "evt-3",
        title: "Pot de bienvenue nouveaux arrivants",
        description: "Moment convivial pour accueillir les nouveaux collaborateurs",
        date: new Date(Date.now() + 1 * 24 * 60 * 60 * 1e3),
        location: "Espace d\xE9tente",
        type: "social",
        organizerId: "mod-1",
        createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1e3)
      }
    ];
    testMessages = [
      {
        id: "msg-1",
        subject: "Demande d'information - Projet Alpha",
        content: "Bonjour Sophie,\n\nPourrais-tu me faire un point sur l'avancement du projet Alpha ? J'aurais besoin des derniers indicateurs pour la r\xE9union de demain.\n\nMerci d'avance,\nMarie",
        senderId: "admin-1",
        recipientId: "emp-1",
        isRead: false,
        createdAt: new Date(Date.now() - 2 * 60 * 60 * 1e3)
      },
      {
        id: "msg-2",
        subject: "Re: Demande d'information - Projet Alpha",
        content: "Bonjour Marie,\n\nBien s\xFBr ! Le projet Alpha avance bien. Nous sommes \xE0 75% de completion. Je pr\xE9pare un rapport d\xE9taill\xE9 que je t'enverrai d'ici la fin de journ\xE9e.\n\nBonne journ\xE9e,\nSophie",
        senderId: "emp-1",
        recipientId: "admin-1",
        isRead: true,
        createdAt: new Date(Date.now() - 1 * 60 * 60 * 1e3)
      },
      {
        id: "msg-3",
        subject: "Formation cybers\xE9curit\xE9 - Rappel",
        content: "Bonjour \xE0 tous,\n\nJe vous rappelle que la formation cybers\xE9curit\xE9 est obligatoire et doit \xEAtre compl\xE9t\xE9e avant le 15 f\xE9vrier. Le lien d'acc\xE8s est disponible sur l'intranet.\n\nN'h\xE9sitez pas si vous avez des questions.\n\nPierre",
        senderId: "mod-1",
        recipientId: "emp-2",
        isRead: false,
        createdAt: new Date(Date.now() - 6 * 60 * 60 * 1e3)
      }
    ];
    testComplaints = [
      {
        id: "comp-1",
        submitterId: "emp-2",
        assignedToId: "admin-1",
        title: "Probl\xE8me de chauffage bureau 205",
        description: "Le radiateur du bureau 205 ne fonctionne pas correctement depuis une semaine. La temp\xE9rature est tr\xE8s basse, ce qui rend le travail difficile.",
        category: "infrastructure",
        priority: "medium",
        status: "open",
        createdAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "comp-2",
        submitterId: "emp-3",
        assignedToId: "mod-1",
        title: "Acc\xE8s parking - Badge d\xE9faillant",
        description: "Mon badge d'acc\xE8s au parking ne fonctionne plus depuis hier. Je dois attendre qu'un coll\xE8gue ouvre pour pouvoir entrer.",
        category: "access",
        priority: "low",
        status: "in_progress",
        createdAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 4 * 60 * 60 * 1e3)
      },
      {
        id: "comp-3",
        submitterId: "emp-4",
        assignedToId: "emp-1",
        title: "Probl\xE8me r\xE9seau wifi - D\xE9connexions fr\xE9quentes",
        description: "Le r\xE9seau wifi se d\xE9connecte r\xE9guli\xE8rement dans l'open space du 2\xE8me \xE9tage. Cela perturbe le travail et les visioconf\xE9rences.",
        category: "technical",
        priority: "high",
        status: "resolved",
        createdAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 12 * 60 * 60 * 1e3)
      }
    ];
  }
});

// shared/schema.ts
var schema_exports = {};
__export(schema_exports, {
  announcements: () => announcements,
  categories: () => categories,
  certificates: () => certificates,
  complaints: () => complaints,
  contents: () => contents,
  courses: () => courses,
  documents: () => documents,
  employeeCategories: () => employeeCategories,
  enrollments: () => enrollments,
  events: () => events,
  forumCategories: () => forumCategories,
  forumLikes: () => forumLikes,
  forumPosts: () => forumPosts,
  forumTopics: () => forumTopics,
  forumUserStats: () => forumUserStats,
  insertAnnouncementSchema: () => insertAnnouncementSchema,
  insertCategorySchema: () => insertCategorySchema,
  insertComplaintSchema: () => insertComplaintSchema,
  insertContentSchema: () => insertContentSchema,
  insertCourseSchema: () => insertCourseSchema,
  insertDocumentSchema: () => insertDocumentSchema,
  insertEmployeeCategorySchema: () => insertEmployeeCategorySchema,
  insertEventSchema: () => insertEventSchema,
  insertForumCategorySchema: () => insertForumCategorySchema,
  insertForumLikeSchema: () => insertForumLikeSchema,
  insertForumPostSchema: () => insertForumPostSchema,
  insertForumTopicSchema: () => insertForumTopicSchema,
  insertLessonSchema: () => insertLessonSchema,
  insertMessageSchema: () => insertMessageSchema,
  insertPermissionSchema: () => insertPermissionSchema,
  insertQuizSchema: () => insertQuizSchema,
  insertResourceSchema: () => insertResourceSchema,
  insertSystemSettingsSchema: () => insertSystemSettingsSchema,
  insertTrainingParticipantSchema: () => insertTrainingParticipantSchema,
  insertTrainingSchema: () => insertTrainingSchema,
  insertUserSchema: () => insertUserSchema,
  lessonProgress: () => lessonProgress,
  lessons: () => lessons,
  messages: () => messages,
  permissions: () => permissions,
  quizAttempts: () => quizAttempts,
  quizzes: () => quizzes,
  resources: () => resources,
  systemSettings: () => systemSettings,
  trainingParticipants: () => trainingParticipants,
  trainings: () => trainings,
  users: () => users
});
import { sql } from "drizzle-orm";
import { pgTable, text, varchar, timestamp, boolean, integer, real } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";
var users, announcements, documents, events, messages, complaints, permissions, trainings, trainingParticipants, insertUserSchema, insertAnnouncementSchema, insertDocumentSchema, insertEventSchema, insertMessageSchema, insertComplaintSchema, contents, categories, employeeCategories, systemSettings, insertPermissionSchema, insertContentSchema, insertCategorySchema, insertEmployeeCategorySchema, insertSystemSettingsSchema, insertTrainingSchema, insertTrainingParticipantSchema, courses, lessons, quizzes, enrollments, lessonProgress, quizAttempts, certificates, resources, insertCourseSchema, insertLessonSchema, insertQuizSchema, insertResourceSchema, forumCategories, forumTopics, forumPosts, forumLikes, forumUserStats, insertForumCategorySchema, insertForumTopicSchema, insertForumPostSchema, insertForumLikeSchema;
var init_schema = __esm({
  "shared/schema.ts"() {
    "use strict";
    users = pgTable("users", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      username: text("username").notNull().unique(),
      password: text("password").notNull(),
      name: text("name").notNull(),
      role: text("role").notNull().default("employee"),
      // employee, admin, moderator
      avatar: text("avatar"),
      // Extended fields for employee management
      employeeId: varchar("employee_id").unique(),
      // Unique identifier for internal communication
      department: varchar("department"),
      position: varchar("position"),
      isActive: boolean("is_active").default(true),
      phone: varchar("phone"),
      email: varchar("email"),
      createdAt: timestamp("created_at").defaultNow(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    announcements = pgTable("announcements", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      title: text("title").notNull(),
      content: text("content").notNull(),
      type: text("type").notNull().default("info"),
      // info, important, event, formation
      authorId: varchar("author_id").references(() => users.id),
      authorName: text("author_name").notNull(),
      imageUrl: text("image_url"),
      icon: text("icon").default("\u{1F4E2}"),
      createdAt: timestamp("created_at").defaultNow().notNull(),
      isImportant: boolean("is_important").default(false)
    });
    documents = pgTable("documents", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      title: text("title").notNull(),
      description: text("description"),
      category: text("category").notNull(),
      // regulation, policy, guide, procedure
      fileName: text("file_name").notNull(),
      fileUrl: text("file_url").notNull(),
      updatedAt: timestamp("updated_at").defaultNow().notNull(),
      version: text("version").default("1.0")
    });
    events = pgTable("events", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      title: text("title").notNull(),
      description: text("description"),
      date: timestamp("date").notNull(),
      location: text("location"),
      type: text("type").notNull().default("meeting"),
      // meeting, training, social, other
      organizerId: varchar("organizer_id").references(() => users.id),
      createdAt: timestamp("created_at").defaultNow()
    });
    messages = pgTable("messages", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      senderId: varchar("sender_id").references(() => users.id).notNull(),
      recipientId: varchar("recipient_id").references(() => users.id).notNull(),
      subject: text("subject").notNull(),
      content: text("content").notNull(),
      isRead: boolean("is_read").default(false),
      createdAt: timestamp("created_at").defaultNow()
    });
    complaints = pgTable("complaints", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      submitterId: varchar("submitter_id").references(() => users.id).notNull(),
      assignedToId: varchar("assigned_to_id").references(() => users.id),
      title: text("title").notNull(),
      description: text("description").notNull(),
      category: text("category").notNull(),
      // hr, it, facilities, other
      priority: text("priority").default("medium"),
      // low, medium, high, urgent
      status: text("status").default("open"),
      // open, in_progress, resolved, closed
      createdAt: timestamp("created_at").defaultNow(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    permissions = pgTable("permissions", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      userId: varchar("user_id").references(() => users.id).notNull(),
      grantedBy: varchar("granted_by").references(() => users.id).notNull(),
      permission: text("permission").notNull(),
      // manage_announcements, manage_documents, manage_events, manage_users, validate_topics, validate_posts, manage_employee_categories, manage_trainings
      createdAt: timestamp("created_at").defaultNow()
    });
    trainings = pgTable("trainings", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      title: text("title").notNull(),
      description: text("description"),
      category: text("category").notNull(),
      // technical, management, safety, compliance, other
      difficulty: text("difficulty").default("beginner"),
      // beginner, intermediate, advanced
      duration: integer("duration").notNull(),
      // duration in minutes
      instructorId: varchar("instructor_id").references(() => users.id),
      instructorName: text("instructor_name").notNull(),
      startDate: timestamp("start_date"),
      endDate: timestamp("end_date"),
      location: text("location"),
      maxParticipants: integer("max_participants"),
      currentParticipants: integer("current_participants").default(0),
      isMandatory: boolean("is_mandatory").default(false),
      isActive: boolean("is_active").default(true),
      isVisible: boolean("is_visible").default(true),
      thumbnailUrl: text("thumbnail_url"),
      documentUrls: text("document_urls").array().default(sql`ARRAY[]::text[]`),
      // Array of document URLs
      createdAt: timestamp("created_at").defaultNow().notNull(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    trainingParticipants = pgTable("training_participants", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      trainingId: varchar("training_id").references(() => trainings.id, { onDelete: "cascade" }).notNull(),
      userId: varchar("user_id").references(() => users.id, { onDelete: "cascade" }).notNull(),
      registeredAt: timestamp("registered_at").defaultNow(),
      status: text("status").default("registered"),
      // registered, completed, cancelled
      completionDate: timestamp("completion_date"),
      score: integer("score"),
      // 0-100
      feedback: text("feedback")
    });
    insertUserSchema = createInsertSchema(users).pick({
      username: true,
      password: true,
      name: true,
      role: true,
      avatar: true,
      employeeId: true,
      department: true,
      position: true,
      phone: true,
      email: true
    });
    insertAnnouncementSchema = createInsertSchema(announcements).pick({
      title: true,
      content: true,
      type: true,
      authorName: true,
      isImportant: true
    }).extend({
      imageUrl: z.string().optional(),
      icon: z.string().optional()
    });
    insertDocumentSchema = createInsertSchema(documents).pick({
      title: true,
      description: true,
      category: true,
      fileName: true,
      fileUrl: true,
      version: true
    });
    insertEventSchema = createInsertSchema(events).pick({
      title: true,
      description: true,
      date: true,
      location: true,
      type: true,
      organizerId: true
    });
    insertMessageSchema = createInsertSchema(messages).pick({
      senderId: true,
      recipientId: true,
      subject: true,
      content: true
    });
    insertComplaintSchema = createInsertSchema(complaints).pick({
      submitterId: true,
      assignedToId: true,
      title: true,
      description: true,
      category: true,
      priority: true
    });
    contents = pgTable("contents", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      title: text("title").notNull(),
      type: text("type").notNull(),
      // video, image, document, audio
      category: text("category").notNull(),
      description: text("description"),
      fileUrl: text("file_url").notNull(),
      thumbnailUrl: text("thumbnail_url"),
      duration: text("duration"),
      viewCount: integer("view_count").default(0),
      rating: integer("rating").default(0),
      tags: text("tags").array(),
      isPopular: boolean("is_popular").default(false),
      isFeatured: boolean("is_featured").default(false),
      createdAt: timestamp("created_at").defaultNow().notNull(),
      updatedAt: timestamp("updated_at").defaultNow().notNull()
    });
    categories = pgTable("categories", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      name: text("name").notNull().unique(),
      description: text("description"),
      icon: text("icon").default("\u{1F4C1}"),
      color: text("color").default("#3B82F6"),
      isVisible: boolean("is_visible").default(true),
      sortOrder: integer("sort_order").default(0),
      createdAt: timestamp("created_at").defaultNow().notNull()
    });
    employeeCategories = pgTable("employee_categories", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      name: text("name").notNull().unique(),
      description: text("description"),
      color: text("color").default("#10B981"),
      permissions: text("permissions").array().default(sql`ARRAY[]::text[]`),
      // Array of permission codes
      isActive: boolean("is_active").default(true),
      createdAt: timestamp("created_at").defaultNow().notNull()
    });
    systemSettings = pgTable("system_settings", {
      id: varchar("id").primaryKey().default("settings"),
      showAnnouncements: boolean("show_announcements").default(true),
      showContent: boolean("show_content").default(true),
      showDocuments: boolean("show_documents").default(true),
      showForum: boolean("show_forum").default(true),
      showMessages: boolean("show_messages").default(true),
      showComplaints: boolean("show_complaints").default(true),
      showTraining: boolean("show_training").default(true),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    insertPermissionSchema = createInsertSchema(permissions).pick({
      userId: true,
      grantedBy: true,
      permission: true
    });
    insertContentSchema = createInsertSchema(contents).pick({
      title: true,
      type: true,
      category: true,
      description: true,
      thumbnailUrl: true,
      fileUrl: true,
      duration: true,
      isPopular: true,
      isFeatured: true,
      tags: true
    });
    insertCategorySchema = createInsertSchema(categories).pick({
      name: true,
      color: true,
      icon: true,
      description: true,
      isVisible: true,
      sortOrder: true
    });
    insertEmployeeCategorySchema = createInsertSchema(employeeCategories).pick({
      name: true,
      description: true,
      color: true,
      permissions: true,
      isActive: true
    });
    insertSystemSettingsSchema = createInsertSchema(systemSettings).pick({
      showAnnouncements: true,
      showContent: true,
      showDocuments: true,
      showForum: true,
      showMessages: true,
      showComplaints: true,
      showTraining: true
    });
    insertTrainingSchema = createInsertSchema(trainings).pick({
      title: true,
      description: true,
      category: true,
      difficulty: true,
      duration: true,
      instructorName: true,
      startDate: true,
      endDate: true,
      location: true,
      maxParticipants: true,
      isMandatory: true,
      isActive: true,
      isVisible: true,
      thumbnailUrl: true,
      documentUrls: true
    });
    insertTrainingParticipantSchema = createInsertSchema(trainingParticipants).pick({
      trainingId: true,
      userId: true,
      status: true,
      completionDate: true,
      score: true,
      feedback: true
    });
    courses = pgTable("courses", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      title: text("title").notNull(),
      description: text("description"),
      category: text("category").notNull(),
      // technical, compliance, soft-skills, leadership
      difficulty: text("difficulty").notNull().default("beginner"),
      // beginner, intermediate, advanced
      duration: integer("duration"),
      // in minutes
      thumbnailUrl: text("thumbnail_url"),
      authorId: varchar("author_id").references(() => users.id),
      authorName: text("author_name").notNull(),
      isPublished: boolean("is_published").default(false),
      isMandatory: boolean("is_mandatory").default(false),
      prerequisites: text("prerequisites"),
      // JSON array of course IDs
      tags: text("tags"),
      // JSON array of tags
      createdAt: timestamp("created_at").defaultNow(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    lessons = pgTable("lessons", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      courseId: varchar("course_id").references(() => courses.id).notNull(),
      title: text("title").notNull(),
      description: text("description"),
      content: text("content").notNull(),
      // HTML content
      order: integer("order").default(0),
      duration: integer("duration"),
      // in minutes
      videoUrl: text("video_url"),
      documentUrl: text("document_url"),
      isRequired: boolean("is_required").default(true),
      createdAt: timestamp("created_at").defaultNow(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    quizzes = pgTable("quizzes", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      courseId: varchar("course_id").references(() => courses.id),
      lessonId: varchar("lesson_id").references(() => lessons.id),
      title: text("title").notNull(),
      description: text("description"),
      questions: text("questions").notNull(),
      // JSON array of questions
      passingScore: integer("passing_score").default(70),
      // percentage
      timeLimit: integer("time_limit"),
      // in minutes
      allowRetries: boolean("allow_retries").default(true),
      maxAttempts: integer("max_attempts").default(3),
      isRequired: boolean("is_required").default(false),
      createdAt: timestamp("created_at").defaultNow(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    enrollments = pgTable("enrollments", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      userId: varchar("user_id").references(() => users.id).notNull(),
      courseId: varchar("course_id").references(() => courses.id).notNull(),
      enrolledAt: timestamp("enrolled_at").defaultNow(),
      startedAt: timestamp("started_at"),
      completedAt: timestamp("completed_at"),
      progress: integer("progress").default(0),
      // percentage
      status: text("status").default("enrolled"),
      // enrolled, in-progress, completed, failed
      certificateUrl: text("certificate_url"),
      timeSpent: integer("time_spent").default(0),
      // in minutes
      score: integer("score"),
      // average score percentage
      courseTitle: text("course_title")
      // denormalized for analytics
    });
    lessonProgress = pgTable("lesson_progress", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      userId: varchar("user_id").references(() => users.id).notNull(),
      lessonId: varchar("lesson_id").references(() => lessons.id).notNull(),
      courseId: varchar("course_id").references(() => courses.id).notNull(),
      isCompleted: boolean("is_completed").default(false),
      timeSpent: integer("time_spent").default(0),
      // in minutes
      completedAt: timestamp("completed_at"),
      createdAt: timestamp("created_at").defaultNow()
    });
    quizAttempts = pgTable("quiz_attempts", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      userId: varchar("user_id").references(() => users.id).notNull(),
      quizId: varchar("quiz_id").references(() => quizzes.id).notNull(),
      courseId: varchar("course_id").references(() => courses.id).notNull(),
      answers: text("answers").notNull(),
      // JSON array of answers
      score: integer("score"),
      // percentage
      passed: boolean("passed").default(false),
      attemptNumber: integer("attempt_number").default(1),
      timeSpent: integer("time_spent"),
      // in minutes
      completedAt: timestamp("completed_at").defaultNow()
    });
    certificates = pgTable("certificates", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      userId: varchar("user_id").references(() => users.id).notNull(),
      courseId: varchar("course_id").references(() => courses.id).notNull(),
      title: text("title").notNull(),
      description: text("description"),
      certificateUrl: text("certificate_url"),
      validUntil: timestamp("valid_until"),
      issuedAt: timestamp("issued_at").defaultNow()
    });
    resources = pgTable("resources", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      title: text("title").notNull(),
      description: text("description"),
      category: text("category").notNull(),
      // documentation, template, guide, reference
      type: text("type").notNull(),
      // pdf, video, link, document
      url: text("url").notNull(),
      thumbnailUrl: text("thumbnail_url"),
      authorId: varchar("author_id").references(() => users.id),
      authorName: text("author_name").notNull(),
      tags: text("tags"),
      // JSON array of tags
      downloadCount: integer("download_count").default(0),
      rating: real("rating").default(0),
      isPublic: boolean("is_public").default(true),
      createdAt: timestamp("created_at").defaultNow(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    insertCourseSchema = createInsertSchema(courses).pick({
      title: true,
      description: true,
      category: true,
      difficulty: true,
      duration: true,
      thumbnailUrl: true,
      authorName: true,
      isPublished: true,
      isMandatory: true,
      prerequisites: true,
      tags: true
    });
    insertLessonSchema = createInsertSchema(lessons).pick({
      courseId: true,
      title: true,
      description: true,
      content: true,
      order: true,
      duration: true,
      videoUrl: true,
      documentUrl: true,
      isRequired: true
    });
    insertQuizSchema = createInsertSchema(quizzes).pick({
      courseId: true,
      lessonId: true,
      title: true,
      description: true,
      questions: true,
      passingScore: true,
      timeLimit: true,
      allowRetries: true,
      maxAttempts: true,
      isRequired: true
    });
    insertResourceSchema = createInsertSchema(resources).pick({
      title: true,
      description: true,
      category: true,
      type: true,
      url: true,
      thumbnailUrl: true,
      authorName: true,
      tags: true,
      isPublic: true
    });
    forumCategories = pgTable("forum_categories", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      name: text("name").notNull(),
      description: text("description"),
      color: text("color").default("#3B82F6"),
      icon: text("icon").default("\u{1F4AC}"),
      sortOrder: integer("sort_order").default(0),
      isActive: boolean("is_active").default(true),
      isModerated: boolean("is_moderated").default(false),
      accessLevel: text("access_level").default("all"),
      // all, employee, moderator, admin
      moderatorIds: text("moderator_ids"),
      // JSON array of user IDs
      createdAt: timestamp("created_at").defaultNow()
    });
    forumTopics = pgTable("forum_topics", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      categoryId: varchar("category_id").references(() => forumCategories.id).notNull(),
      title: text("title").notNull(),
      description: text("description"),
      authorId: varchar("author_id").references(() => users.id).notNull(),
      authorName: text("author_name").notNull(),
      isPinned: boolean("is_pinned").default(false),
      isLocked: boolean("is_locked").default(false),
      isAnnouncement: boolean("is_announcement").default(false),
      viewCount: integer("view_count").default(0),
      replyCount: integer("reply_count").default(0),
      lastReplyAt: timestamp("last_reply_at"),
      lastReplyBy: varchar("last_reply_by").references(() => users.id),
      lastReplyByName: text("last_reply_by_name"),
      tags: text("tags"),
      // JSON array of tags
      createdAt: timestamp("created_at").defaultNow(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    forumPosts = pgTable("forum_posts", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      topicId: varchar("topic_id").references(() => forumTopics.id).notNull(),
      categoryId: varchar("category_id").references(() => forumCategories.id).notNull(),
      authorId: varchar("author_id").references(() => users.id).notNull(),
      authorName: text("author_name").notNull(),
      content: text("content").notNull(),
      isFirstPost: boolean("is_first_post").default(false),
      // Original topic post
      parentPostId: varchar("parent_post_id"),
      // For threaded replies - self-reference handled separately
      likeCount: integer("like_count").default(0),
      isEdited: boolean("is_edited").default(false),
      editedAt: timestamp("edited_at"),
      editedBy: varchar("edited_by").references(() => users.id),
      isDeleted: boolean("is_deleted").default(false),
      deletedAt: timestamp("deleted_at"),
      deletedBy: varchar("deleted_by").references(() => users.id),
      attachments: text("attachments"),
      // JSON array of file URLs
      createdAt: timestamp("created_at").defaultNow(),
      updatedAt: timestamp("updated_at").defaultNow()
    });
    forumLikes = pgTable("forum_likes", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      postId: varchar("post_id").references(() => forumPosts.id).notNull(),
      userId: varchar("user_id").references(() => users.id).notNull(),
      reactionType: text("reaction_type").default("like"),
      // like, love, laugh, angry, sad
      createdAt: timestamp("created_at").defaultNow()
    });
    forumUserStats = pgTable("forum_user_stats", {
      id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
      userId: varchar("user_id").references(() => users.id).notNull().unique(),
      postCount: integer("post_count").default(0),
      topicCount: integer("topic_count").default(0),
      likeCount: integer("like_count").default(0),
      reputationScore: integer("reputation_score").default(0),
      badges: text("badges"),
      // JSON array of earned badges
      joinedAt: timestamp("joined_at").defaultNow(),
      lastActiveAt: timestamp("last_active_at")
    });
    insertForumCategorySchema = createInsertSchema(forumCategories).pick({
      name: true,
      description: true,
      color: true,
      icon: true,
      sortOrder: true,
      isActive: true,
      isModerated: true,
      accessLevel: true,
      moderatorIds: true
    });
    insertForumTopicSchema = createInsertSchema(forumTopics).pick({
      categoryId: true,
      title: true,
      description: true,
      authorId: true,
      authorName: true,
      isPinned: true,
      isLocked: true,
      isAnnouncement: true,
      tags: true
    });
    insertForumPostSchema = createInsertSchema(forumPosts).pick({
      topicId: true,
      categoryId: true,
      authorId: true,
      authorName: true,
      content: true,
      isFirstPost: true,
      parentPostId: true,
      attachments: true
    });
    insertForumLikeSchema = createInsertSchema(forumLikes).pick({
      postId: true,
      userId: true,
      reactionType: true
    });
  }
});

// server/services/websocket.ts
var websocket_exports = {};
__export(websocket_exports, {
  WebSocketManager: () => WebSocketManager,
  getWebSocketManager: () => getWebSocketManager,
  initializeWebSocket: () => initializeWebSocket
});
import WebSocket, { WebSocketServer } from "ws";
import { parse } from "url";
function initializeWebSocket(server) {
  if (!wsManager) {
    wsManager = new WebSocketManager(server);
  }
  return wsManager;
}
function getWebSocketManager() {
  return wsManager;
}
var WebSocketManager, wsManager;
var init_websocket = __esm({
  "server/services/websocket.ts"() {
    "use strict";
    WebSocketManager = class {
      wss;
      clients = /* @__PURE__ */ new Map();
      channels = /* @__PURE__ */ new Map();
      heartbeatInterval;
      constructor(server) {
        this.wss = new WebSocketServer({
          server,
          path: "/ws",
          verifyClient: this.verifyClient.bind(this)
        });
        this.wss.on("connection", this.handleConnection.bind(this));
        this.heartbeatInterval = setInterval(() => {
          this.wss.clients.forEach((ws) => {
            if (ws.isAlive === false) {
              this.cleanupClient(ws);
              return ws.terminate();
            }
            ws.isAlive = false;
            ws.ping();
          });
        }, 3e4);
        console.log("\u2705 WebSocket server initialized on /ws");
      }
      verifyClient(info) {
        return true;
      }
      handleConnection(ws, req) {
        const { query } = parse(req.url || "", true);
        const userId = query.userId;
        ws.userId = userId;
        ws.channels = /* @__PURE__ */ new Set();
        ws.isAlive = true;
        if (userId) {
          this.clients.set(userId, ws);
        }
        ws.on("message", (data) => {
          try {
            const message = JSON.parse(data);
            this.handleMessage(ws, message);
          } catch (error) {
            console.error("Invalid WebSocket message:", error);
          }
        });
        ws.on("pong", () => {
          ws.isAlive = true;
        });
        ws.on("close", () => {
          this.cleanupClient(ws);
        });
        ws.on("error", (error) => {
          console.error("WebSocket error:", error);
          this.cleanupClient(ws);
        });
        this.sendToClient(ws, {
          type: "CONNECTED",
          payload: { message: "WebSocket connection established" },
          timestamp: /* @__PURE__ */ new Date()
        });
        console.log(`\u{1F50C} WebSocket client connected: ${userId || "anonymous"}`);
      }
      handleMessage(ws, message) {
        message.userId = ws.userId;
        message.timestamp = /* @__PURE__ */ new Date();
        switch (message.type) {
          case "AUTHENTICATE":
            this.authenticateClient(ws, message.payload.userId);
            break;
          case "JOIN_CHANNEL":
            this.joinChannel(ws, message.payload.channelId);
            break;
          case "LEAVE_CHANNEL":
            this.leaveChannel(ws, message.payload.channelId);
            break;
          case "CHAT_MESSAGE":
            this.broadcastToChannel(message.payload.channelId, message);
            break;
          case "USER_TYPING":
            this.broadcastToChannel(message.payload.channelId, message, ws.userId);
            break;
          case "MARK_NOTIFICATION_READ":
            this.handleNotificationRead(ws, message.payload);
            break;
          case "CLEAR_NOTIFICATIONS":
            this.handleClearNotifications(ws);
            break;
          case "REQUEST_USERS_COUNT":
            this.sendUsersCount(ws);
            break;
          default:
            console.log("Unknown message type:", message.type);
        }
      }
      authenticateClient(ws, userId) {
        if (ws.userId && ws.userId !== userId) {
          this.clients.delete(ws.userId);
        }
        ws.userId = userId;
        this.clients.set(userId, ws);
        this.broadcast({
          type: "USER_ONLINE",
          payload: { userId },
          timestamp: /* @__PURE__ */ new Date()
        });
        this.sendUsersCount(ws);
      }
      handleNotificationRead(ws, payload) {
        console.log(`Notification ${payload.id} marked as read by ${ws.userId}`);
      }
      handleClearNotifications(ws) {
        console.log(`All notifications cleared for ${ws.userId}`);
      }
      sendUsersCount(ws) {
        const userIds = Array.from(this.clients.keys());
        this.sendToClient(ws, {
          type: "USERS_COUNT",
          payload: { users: userIds, count: userIds.length },
          timestamp: /* @__PURE__ */ new Date()
        });
      }
      broadcast(message) {
        this.wss.clients.forEach((client) => {
          this.sendToClient(client, message);
        });
      }
      joinChannel(ws, channelId) {
        if (!ws.channels) return;
        ws.channels.add(channelId);
        if (!this.channels.has(channelId)) {
          this.channels.set(channelId, /* @__PURE__ */ new Set());
        }
        this.channels.get(channelId)?.add(ws.userId || "");
        this.sendToClient(ws, {
          type: "JOINED_CHANNEL",
          payload: { channelId },
          timestamp: /* @__PURE__ */ new Date()
        });
      }
      leaveChannel(ws, channelId) {
        if (!ws.channels) return;
        ws.channels.delete(channelId);
        this.channels.get(channelId)?.delete(ws.userId || "");
        if (this.channels.get(channelId)?.size === 0) {
          this.channels.delete(channelId);
        }
      }
      cleanupClient(ws) {
        if (ws.userId) {
          this.clients.delete(ws.userId);
          this.broadcast({
            type: "USER_OFFLINE",
            payload: { userId: ws.userId },
            timestamp: /* @__PURE__ */ new Date()
          });
        }
        ws.channels?.forEach((channelId) => {
          this.leaveChannel(ws, channelId);
        });
      }
      sendToClient(client, message) {
        if (client.readyState === WebSocket.OPEN) {
          client.send(JSON.stringify(message));
        }
      }
      broadcastToChannel(channelId, message, excludeUserId) {
        const channelUsers = this.channels.get(channelId);
        if (!channelUsers) return;
        channelUsers.forEach((userId) => {
          if (userId !== excludeUserId) {
            const client = this.clients.get(userId);
            if (client) {
              this.sendToClient(client, message);
            }
          }
        });
      }
      // Public methods for broadcasting events
      broadcastToUser(userId, message) {
        const client = this.clients.get(userId);
        if (client) {
          this.sendToClient(client, { ...message, timestamp: /* @__PURE__ */ new Date() });
        }
      }
      broadcastToAll(message) {
        const fullMessage = { ...message, timestamp: /* @__PURE__ */ new Date() };
        this.wss.clients.forEach((client) => {
          this.sendToClient(client, fullMessage);
        });
      }
      broadcastNewAnnouncement(announcement) {
        this.broadcastToAll({
          type: "NEW_ANNOUNCEMENT",
          payload: announcement
        });
      }
      broadcastNewMessage(message) {
        this.broadcastToUser(message.recipientId, {
          type: "NEW_MESSAGE",
          payload: message
        });
      }
      broadcastForumUpdate(update) {
        this.broadcastToAll({
          type: "FORUM_UPDATE",
          payload: update
        });
      }
      broadcastTrainingUpdate(update) {
        this.broadcastToAll({
          type: "TRAINING_UPDATE",
          payload: update
        });
      }
      notifyUser(userId, notification) {
        this.broadcastToUser(userId, {
          type: "NOTIFICATION",
          payload: notification
        });
      }
      getConnectedUsers() {
        return Array.from(this.clients.keys());
      }
      getChannelUsers(channelId) {
        return Array.from(this.channels.get(channelId) || []);
      }
      getUserCount() {
        return this.clients.size;
      }
      close() {
        clearInterval(this.heartbeatInterval);
        this.wss.close();
      }
    };
    wsManager = null;
  }
});

// server/index.ts
import express2 from "express";
import session from "express-session";

// server/routes/api.ts
import { createServer } from "http";

// server/data/storage.ts
import { randomUUID } from "crypto";
var MemStorage = class {
  users;
  announcements;
  documents;
  events;
  messages;
  complaints;
  permissions;
  contents;
  categories;
  employeeCategories;
  systemSettings;
  // Training storage
  trainings;
  trainingParticipants;
  // E-learning storage
  courses;
  lessons;
  quizzes;
  enrollments;
  lessonProgress;
  quizAttempts;
  certificates;
  resources;
  // Forum system storage
  forumCategories;
  forumTopics;
  forumPosts;
  forumLikes;
  forumUserStats;
  // Configuration storage
  viewsConfig = /* @__PURE__ */ new Map();
  userSettings = /* @__PURE__ */ new Map();
  constructor() {
    this.users = /* @__PURE__ */ new Map();
    this.announcements = /* @__PURE__ */ new Map();
    this.documents = /* @__PURE__ */ new Map();
    this.events = /* @__PURE__ */ new Map();
    this.messages = /* @__PURE__ */ new Map();
    this.complaints = /* @__PURE__ */ new Map();
    this.permissions = /* @__PURE__ */ new Map();
    this.contents = /* @__PURE__ */ new Map();
    this.categories = /* @__PURE__ */ new Map();
    this.employeeCategories = /* @__PURE__ */ new Map();
    this.systemSettings = {
      id: "settings",
      showAnnouncements: true,
      showContent: true,
      showDocuments: true,
      showForum: true,
      showMessages: true,
      showComplaints: true,
      showTraining: true,
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.trainings = /* @__PURE__ */ new Map();
    this.trainingParticipants = /* @__PURE__ */ new Map();
    this.courses = /* @__PURE__ */ new Map();
    this.lessons = /* @__PURE__ */ new Map();
    this.quizzes = /* @__PURE__ */ new Map();
    this.enrollments = /* @__PURE__ */ new Map();
    this.lessonProgress = /* @__PURE__ */ new Map();
    this.quizAttempts = /* @__PURE__ */ new Map();
    this.certificates = /* @__PURE__ */ new Map();
    this.resources = /* @__PURE__ */ new Map();
    this.forumCategories = /* @__PURE__ */ new Map();
    this.forumTopics = /* @__PURE__ */ new Map();
    this.forumPosts = /* @__PURE__ */ new Map();
    this.forumLikes = /* @__PURE__ */ new Map();
    this.forumUserStats = /* @__PURE__ */ new Map();
    this.viewsConfig = /* @__PURE__ */ new Map();
    this.userSettings = /* @__PURE__ */ new Map();
    this.initializeData();
  }
  initializeData() {
    const defaultUsers = [
      {
        id: "user-1",
        username: "admin",
        password: "admin123",
        name: "Jean Dupont",
        role: "admin",
        avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=100&h=100",
        employeeId: "EMP001",
        department: "Direction",
        position: "Directeur G\xE9n\xE9ral",
        isActive: true,
        phone: "01 23 45 67 89",
        email: "jean.dupont@intrasphere.com",
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
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
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
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
        position: "D\xE9veloppeur",
        isActive: true,
        phone: "01 23 45 67 91",
        email: "pierre.dubois@intrasphere.com",
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
      }
    ];
    const sampleAnnouncements = [
      {
        id: "ann-1",
        title: "Nouvelle politique de t\xE9l\xE9travail",
        content: "\xC0 partir du 1er d\xE9cembre, une nouvelle politique de t\xE9l\xE9travail hybride sera mise en place.",
        type: "important",
        authorId: "user-1",
        authorName: "Jean Dupont",
        imageUrl: null,
        icon: "\u{1F4E2}",
        createdAt: new Date(Date.now() - 2 * 60 * 60 * 1e3),
        isImportant: true
      },
      {
        id: "ann-2",
        title: "Soir\xE9e d'entreprise - 15 d\xE9cembre",
        content: "Nous organisons notre soir\xE9e de fin d'ann\xE9e le vendredi 15 d\xE9cembre.",
        type: "event",
        authorId: "user-2",
        authorName: "Marie Martin",
        imageUrl: null,
        icon: "\u{1F389}",
        createdAt: new Date(Date.now() - 24 * 60 * 60 * 1e3),
        isImportant: false
      }
    ];
    const sampleDocuments = [
      {
        id: "doc-1",
        title: "R\xE8glement int\xE9rieur 2024",
        description: "Version mise \xE0 jour du r\xE8glement int\xE9rieur",
        category: "regulation",
        fileName: "reglement-interieur-2024.pdf",
        fileUrl: "/documents/reglement-interieur-2024.pdf",
        updatedAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1e3),
        version: "2024.1"
      }
    ];
    const sampleEvents = [
      {
        id: "event-1",
        title: "R\xE9union \xE9quipe marketing",
        description: "Point mensuel sur les campagnes",
        date: new Date(2024, 0, 16, 14, 0),
        location: "Salle de conf\xE9rence A",
        type: "meeting",
        createdAt: /* @__PURE__ */ new Date(),
        organizerId: "user-1"
      }
    ];
    const sampleMessages = [
      {
        id: "msg-1",
        senderId: "user-2",
        recipientId: "user-1",
        subject: "Nouvelle politique RH",
        content: "Bonjour Jean, j'aimerais discuter avec vous de la nouvelle politique de t\xE9l\xE9travail. Pouvez-vous me dire quand vous \xEAtes disponible cette semaine ?",
        isRead: false,
        createdAt: new Date(Date.now() - 4 * 60 * 60 * 1e3)
      },
      {
        id: "msg-2",
        senderId: "user-3",
        recipientId: "user-1",
        subject: "Probl\xE8me technique",
        content: "Salut, j'ai un souci avec mon ordinateur qui ne d\xE9marre plus. Est-ce que tu peux m'aider ou me dire \xE0 qui m'adresser ?",
        isRead: true,
        createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1e3)
      }
    ];
    const sampleComplaints = [
      {
        id: "complaint-1",
        submitterId: "user-3",
        assignedToId: "user-2",
        title: "Climatisation d\xE9faillante",
        description: "La climatisation ne fonctionne pas correctement dans mon bureau. Il fait tr\xE8s chaud et cela impacte ma productivit\xE9.",
        category: "facilities",
        priority: "medium",
        status: "open",
        createdAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1e3)
      }
    ];
    const sampleCategories = [
      {
        id: "cat-1",
        name: "Formation",
        color: "#3B82F6",
        icon: "\u{1F393}",
        description: "Contenus de formation et d\xE9veloppement des comp\xE9tences",
        isVisible: true,
        sortOrder: 1,
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "cat-2",
        name: "Actualit\xE9s",
        color: "#10B981",
        icon: "\u{1F4F0}",
        description: "Actualit\xE9s et informations de l'entreprise",
        isVisible: true,
        sortOrder: 2,
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "cat-3",
        name: "Corporate",
        color: "#8B5CF6",
        icon: "\u{1F3E2}",
        description: "Documents et communications corporate",
        isVisible: true,
        sortOrder: 3,
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "cat-4",
        name: "Social",
        color: "#F59E0B",
        icon: "\u{1F389}",
        description: "\xC9v\xE9nements sociaux et team building",
        isVisible: true,
        sortOrder: 4,
        createdAt: /* @__PURE__ */ new Date()
      }
    ];
    const sampleContent = [
      {
        id: "content-1",
        title: "Guide d'int\xE9gration nouveaux employ\xE9s",
        type: "video",
        category: "Formation",
        description: "Vid\xE9o d'accueil compl\xE8te pour faciliter l'int\xE9gration des nouveaux collaborateurs dans l'entreprise.",
        thumbnailUrl: "https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=225",
        fileUrl: "/content/integration-guide.mp4",
        duration: "12 min",
        viewCount: 245,
        rating: 4,
        isPopular: true,
        isFeatured: true,
        tags: ["formation", "int\xE9gration", "nouveaux employ\xE9s"],
        createdAt: /* @__PURE__ */ new Date("2024-01-15T10:00:00Z"),
        updatedAt: /* @__PURE__ */ new Date("2024-01-15T10:00:00Z")
      },
      {
        id: "content-2",
        title: "Pr\xE9sentation des nouveaux bureaux",
        type: "image",
        category: "Actualit\xE9s",
        description: "Galerie photos compl\xE8te des nouveaux espaces de travail am\xE9nag\xE9s avec les derni\xE8res technologies.",
        thumbnailUrl: "https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=225",
        fileUrl: "/content/bureaux-galerie.jpg",
        duration: null,
        viewCount: 156,
        rating: 4,
        isPopular: false,
        isFeatured: false,
        tags: ["bureaux", "am\xE9nagement", "photos"],
        createdAt: /* @__PURE__ */ new Date("2024-01-10T14:30:00Z"),
        updatedAt: /* @__PURE__ */ new Date("2024-01-10T14:30:00Z")
      }
    ];
    const sampleForumCategories = [
      {
        id: "forum-cat-1",
        name: "Discussion G\xE9n\xE9rale",
        description: "Discussions g\xE9n\xE9rales sur l'entreprise et le travail",
        color: "#3B82F6",
        icon: "\u{1F4AC}",
        sortOrder: 1,
        isActive: true,
        isModerated: false,
        accessLevel: "all",
        moderatorIds: null,
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "forum-cat-2",
        name: "Annonces Officielles",
        description: "Communications importantes de la direction",
        color: "#EF4444",
        icon: "\u{1F4E2}",
        sortOrder: 2,
        isActive: true,
        isModerated: true,
        accessLevel: "all",
        moderatorIds: '["user-1", "user-2"]',
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "forum-cat-3",
        name: "Entraide & Support",
        description: "Questions techniques et demandes d'aide",
        color: "#10B981",
        icon: "\u{1F91D}",
        sortOrder: 3,
        isActive: true,
        isModerated: false,
        accessLevel: "employee",
        moderatorIds: null,
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "forum-cat-4",
        name: "\xC9v\xE9nements & Social",
        description: "Organisation d'\xE9v\xE9nements et discussions sociales",
        color: "#F59E0B",
        icon: "\u{1F389}",
        sortOrder: 4,
        isActive: true,
        isModerated: false,
        accessLevel: "all",
        moderatorIds: null,
        createdAt: /* @__PURE__ */ new Date()
      }
    ];
    const sampleForumTopics = [
      {
        id: "forum-topic-1",
        categoryId: "forum-cat-1",
        title: "Bienvenue sur le nouveau forum IntraSphere !",
        description: "Pr\xE9sentation du nouveau syst\xE8me de forum int\xE9gr\xE9",
        authorId: "user-1",
        authorName: "Jean Dupont",
        isPinned: true,
        isLocked: false,
        isAnnouncement: true,
        viewCount: 125,
        replyCount: 8,
        lastReplyAt: new Date(Date.now() - 2 * 60 * 60 * 1e3),
        lastReplyBy: "user-3",
        lastReplyByName: "Pierre Dubois",
        tags: '["bienvenue", "forum", "nouveau"]',
        createdAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 2 * 60 * 60 * 1e3)
      },
      {
        id: "forum-topic-2",
        categoryId: "forum-cat-3",
        title: "Probl\xE8me avec l'imprimante du 2e \xE9tage",
        description: "L'imprimante ne r\xE9pond plus depuis ce matin",
        authorId: "user-3",
        authorName: "Pierre Dubois",
        isPinned: false,
        isLocked: false,
        isAnnouncement: false,
        viewCount: 42,
        replyCount: 3,
        lastReplyAt: new Date(Date.now() - 30 * 60 * 1e3),
        lastReplyBy: "user-2",
        lastReplyByName: "Marie Martin",
        tags: '["technique", "imprimante", "probl\xE8me"]',
        createdAt: new Date(Date.now() - 8 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 30 * 60 * 1e3)
      },
      {
        id: "forum-topic-3",
        categoryId: "forum-cat-4",
        title: "Organisation pot de d\xE9part Julie",
        description: "Julie quitte l'\xE9quipe vendredi, organisons-lui un petit pot !",
        authorId: "user-2",
        authorName: "Marie Martin",
        isPinned: false,
        isLocked: false,
        isAnnouncement: false,
        viewCount: 67,
        replyCount: 12,
        lastReplyAt: new Date(Date.now() - 15 * 60 * 1e3),
        lastReplyBy: "user-1",
        lastReplyByName: "Jean Dupont",
        tags: '["social", "d\xE9part", "organisation"]',
        createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 15 * 60 * 1e3)
      }
    ];
    const sampleForumPosts = [
      {
        id: "forum-post-1",
        categoryId: "forum-cat-1",
        topicId: "forum-topic-1",
        authorId: "user-1",
        authorName: "Jean Dupont",
        content: "Bonjour \xE0 tous ! Je suis ravi de vous pr\xE9senter notre nouveau syst\xE8me de forum int\xE9gr\xE9 \xE0 IntraSphere. Cette nouvelle fonctionnalit\xE9 va nous permettre d'\xE9changer plus facilement et de cr\xE9er une vraie communaut\xE9 au sein de l'entreprise. N'h\xE9sitez pas \xE0 poser vos questions et \xE0 partager vos id\xE9es !",
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
        createdAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "forum-post-2",
        categoryId: "forum-cat-1",
        topicId: "forum-topic-1",
        authorId: "user-2",
        authorName: "Marie Martin",
        content: "Excellente initiative ! Le forum va vraiment am\xE9liorer notre communication interne. J'ai h\xE2te de voir toutes les discussions qui vont na\xEEtre ici.",
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
        createdAt: new Date(Date.now() - 4 * 24 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 4 * 24 * 60 * 60 * 1e3)
      },
      {
        id: "forum-post-3",
        categoryId: "forum-cat-3",
        topicId: "forum-topic-2",
        authorId: "user-3",
        authorName: "Pierre Dubois",
        content: "Bonjour, j'ai un probl\xE8me avec l'imprimante du 2e \xE9tage. Elle ne r\xE9pond plus depuis ce matin et j'ai plusieurs documents urgents \xE0 imprimer. Est-ce que quelqu'un sait ce qui se passe ?",
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
        createdAt: new Date(Date.now() - 8 * 60 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 8 * 60 * 60 * 1e3)
      },
      {
        id: "forum-post-4",
        categoryId: "forum-cat-3",
        topicId: "forum-topic-2",
        authorId: "user-2",
        authorName: "Marie Martin",
        content: "Salut Pierre, j'ai contact\xE9 le service technique. Ils vont passer dans la matin\xE9e pour r\xE9parer l'imprimante. En attendant, tu peux utiliser celle du 1er \xE9tage si c'est urgent !",
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
        createdAt: new Date(Date.now() - 30 * 60 * 1e3),
        updatedAt: new Date(Date.now() - 30 * 60 * 1e3)
      }
    ];
    const sampleForumUserStats = [
      {
        id: "forum-stats-1",
        userId: "user-1",
        postCount: 2,
        topicCount: 1,
        likeCount: 5,
        reputationScore: 25,
        badges: '["admin", "pioneer"]',
        joinedAt: new Date(Date.now() - 30 * 24 * 60 * 60 * 1e3),
        lastActiveAt: new Date(Date.now() - 60 * 1e3)
      },
      {
        id: "forum-stats-2",
        userId: "user-2",
        postCount: 2,
        topicCount: 1,
        likeCount: 3,
        reputationScore: 18,
        badges: '["moderator", "helper"]',
        joinedAt: new Date(Date.now() - 25 * 24 * 60 * 60 * 1e3),
        lastActiveAt: new Date(Date.now() - 30 * 60 * 1e3)
      },
      {
        id: "forum-stats-3",
        userId: "user-3",
        postCount: 1,
        topicCount: 1,
        likeCount: 1,
        reputationScore: 5,
        badges: '["newbie"]',
        joinedAt: new Date(Date.now() - 20 * 24 * 60 * 60 * 1e3),
        lastActiveAt: new Date(Date.now() - 8 * 60 * 60 * 1e3)
      }
    ];
    defaultUsers.forEach((user) => this.users.set(user.id, user));
    sampleAnnouncements.forEach((ann) => this.announcements.set(ann.id, ann));
    sampleDocuments.forEach((doc) => this.documents.set(doc.id, doc));
    sampleEvents.forEach((event) => this.events.set(event.id, event));
    sampleMessages.forEach((msg) => this.messages.set(msg.id, msg));
    sampleComplaints.forEach((complaint) => this.complaints.set(complaint.id, complaint));
    sampleCategories.forEach((cat) => this.categories.set(cat.id, cat));
    sampleContent.forEach((content) => this.contents.set(content.id, content));
    const sampleEmployeeCategories = [
      {
        id: "emp-cat-1",
        name: "D\xE9veloppeurs",
        description: "\xC9quipe de d\xE9veloppement logiciel",
        color: "#3B82F6",
        permissions: ["validate_posts"],
        isActive: true,
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "emp-cat-2",
        name: "Managers",
        description: "Personnel d'encadrement",
        color: "#8B5CF6",
        permissions: ["validate_topics", "validate_posts", "manage_employee_categories"],
        isActive: true,
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "emp-cat-3",
        name: "RH",
        description: "Ressources humaines",
        color: "#10B981",
        permissions: ["validate_topics", "validate_posts"],
        isActive: true,
        createdAt: /* @__PURE__ */ new Date()
      }
    ];
    sampleEmployeeCategories.forEach((cat) => this.employeeCategories.set(cat.id, cat));
    sampleForumCategories.forEach((cat) => this.forumCategories.set(cat.id, cat));
    sampleForumTopics.forEach((topic) => this.forumTopics.set(topic.id, topic));
    sampleForumPosts.forEach((post) => this.forumPosts.set(post.id, post));
    sampleForumUserStats.forEach((stats) => this.forumUserStats.set(stats.id, stats));
  }
  // Users
  async getUser(id) {
    return this.users.get(id);
  }
  async getUserByUsername(username) {
    return Array.from(this.users.values()).find((user) => user.username === username);
  }
  async getUserByEmployeeId(employeeId) {
    return Array.from(this.users.values()).find((user) => user.employeeId === employeeId);
  }
  async getUsers() {
    return Array.from(this.users.values()).filter((user) => user.isActive);
  }
  async createUser(insertUser) {
    const id = randomUUID();
    const user = {
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
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.users.set(id, user);
    return user;
  }
  async updateUser(id, updates) {
    const user = this.users.get(id);
    if (!user) throw new Error("User not found");
    const updatedUser = { ...user, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.users.set(id, updatedUser);
    return updatedUser;
  }
  // Announcements
  async getAnnouncements() {
    return Array.from(this.announcements.values()).sort(
      (a, b) => b.createdAt.getTime() - a.createdAt.getTime()
    );
  }
  async getAnnouncementById(id) {
    return this.announcements.get(id);
  }
  async createAnnouncement(insertAnnouncement) {
    const id = randomUUID();
    const announcement = {
      ...insertAnnouncement,
      id,
      type: insertAnnouncement.type || "info",
      authorId: "user-1",
      // Fixed authorId since it's not in the insert schema
      imageUrl: insertAnnouncement.imageUrl || null,
      icon: insertAnnouncement.icon || null,
      createdAt: /* @__PURE__ */ new Date(),
      isImportant: insertAnnouncement.isImportant || false
    };
    this.announcements.set(id, announcement);
    return announcement;
  }
  async updateAnnouncement(id, updates) {
    const announcement = this.announcements.get(id);
    if (!announcement) throw new Error("Announcement not found");
    const updatedAnnouncement = { ...announcement, ...updates };
    this.announcements.set(id, updatedAnnouncement);
    return updatedAnnouncement;
  }
  async deleteAnnouncement(id) {
    this.announcements.delete(id);
  }
  // Documents
  async getDocuments() {
    return Array.from(this.documents.values()).sort(
      (a, b) => b.updatedAt.getTime() - a.updatedAt.getTime()
    );
  }
  async getDocumentById(id) {
    return this.documents.get(id);
  }
  async createDocument(insertDocument) {
    const id = randomUUID();
    const document = {
      ...insertDocument,
      id,
      description: insertDocument.description || null,
      version: insertDocument.version || "1.0",
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.documents.set(id, document);
    return document;
  }
  async updateDocument(id, updates) {
    const document = this.documents.get(id);
    if (!document) throw new Error("Document not found");
    const updatedDocument = { ...document, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.documents.set(id, updatedDocument);
    return updatedDocument;
  }
  async deleteDocument(id) {
    this.documents.delete(id);
  }
  // Events
  async getEvents() {
    return Array.from(this.events.values()).sort(
      (a, b) => a.date.getTime() - b.date.getTime()
    );
  }
  async getEventById(id) {
    return this.events.get(id);
  }
  async createEvent(insertEvent) {
    const id = randomUUID();
    const event = {
      ...insertEvent,
      id,
      type: insertEvent.type || "meeting",
      description: insertEvent.description || null,
      location: insertEvent.location || null,
      organizerId: insertEvent.organizerId || null,
      createdAt: /* @__PURE__ */ new Date()
    };
    this.events.set(id, event);
    return event;
  }
  async updateEvent(id, updates) {
    const event = this.events.get(id);
    if (!event) throw new Error("Event not found");
    const updatedEvent = { ...event, ...updates };
    this.events.set(id, updatedEvent);
    return updatedEvent;
  }
  async deleteEvent(id) {
    this.events.delete(id);
  }
  // Messages
  async getMessages(userId) {
    return Array.from(this.messages.values()).filter((message) => message.senderId === userId || message.recipientId === userId).sort((a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0));
  }
  async getMessageById(id) {
    return this.messages.get(id);
  }
  async createMessage(insertMessage) {
    const id = randomUUID();
    const message = {
      ...insertMessage,
      id,
      isRead: false,
      createdAt: /* @__PURE__ */ new Date()
    };
    this.messages.set(id, message);
    return message;
  }
  async markMessageAsRead(id) {
    const message = this.messages.get(id);
    if (message) {
      message.isRead = true;
      this.messages.set(id, message);
    }
  }
  // Complaints
  async getComplaints() {
    return Array.from(this.complaints.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }
  async getComplaintById(id) {
    return this.complaints.get(id);
  }
  async getComplaintsByUser(userId) {
    return Array.from(this.complaints.values()).filter((complaint) => complaint.submitterId === userId).sort((a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0));
  }
  async createComplaint(insertComplaint) {
    const id = randomUUID();
    const complaint = {
      ...insertComplaint,
      id,
      assignedToId: insertComplaint.assignedToId || null,
      priority: insertComplaint.priority || "medium",
      status: "open",
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.complaints.set(id, complaint);
    return complaint;
  }
  async updateComplaint(id, updates) {
    const complaint = this.complaints.get(id);
    if (!complaint) throw new Error("Complaint not found");
    const updatedComplaint = { ...complaint, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.complaints.set(id, updatedComplaint);
    return updatedComplaint;
  }
  // Permissions
  async getPermissions(userId) {
    return Array.from(this.permissions.values()).filter((permission) => permission.userId === userId);
  }
  async createPermission(insertPermission) {
    const id = randomUUID();
    const permission = {
      ...insertPermission,
      id,
      createdAt: /* @__PURE__ */ new Date()
    };
    this.permissions.set(id, permission);
    return permission;
  }
  async revokePermission(id) {
    this.permissions.delete(id);
  }
  async hasPermission(userId, permissionType) {
    const user = await this.getUser(userId);
    if (!user) return false;
    if (user.role === "admin") return true;
    const userPermissions = await this.getPermissions(userId);
    return userPermissions.some((p) => p.permission === permissionType);
  }
  // Contents
  async getContents() {
    return Array.from(this.contents.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }
  async getContentById(id) {
    return this.contents.get(id);
  }
  async createContent(insertContent) {
    const id = randomUUID();
    const content = {
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
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.contents.set(id, content);
    return content;
  }
  async updateContent(id, updates) {
    const content = this.contents.get(id);
    if (!content) throw new Error("Content not found");
    const updatedContent = { ...content, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.contents.set(id, updatedContent);
    return updatedContent;
  }
  async deleteContent(id) {
    this.contents.delete(id);
  }
  // Categories
  async getCategories() {
    return Array.from(this.categories.values()).sort(
      (a, b) => (a.sortOrder || 0) - (b.sortOrder || 0)
    );
  }
  async getCategoryById(id) {
    return this.categories.get(id);
  }
  async createCategory(insertCategory) {
    const id = randomUUID();
    const category = {
      ...insertCategory,
      id,
      icon: insertCategory.icon || null,
      color: insertCategory.color || null,
      description: insertCategory.description || null,
      isVisible: insertCategory.isVisible ?? true,
      sortOrder: insertCategory.sortOrder || 0,
      createdAt: /* @__PURE__ */ new Date()
    };
    this.categories.set(id, category);
    return category;
  }
  async updateCategory(id, updates) {
    const category = this.categories.get(id);
    if (!category) throw new Error("Category not found");
    const updatedCategory = { ...category, ...updates };
    this.categories.set(id, updatedCategory);
    return updatedCategory;
  }
  async deleteCategory(id) {
    this.categories.delete(id);
  }
  // Employee Categories implementation
  async getEmployeeCategories() {
    return Array.from(this.employeeCategories.values()).sort(
      (a, b) => a.name.localeCompare(b.name)
    );
  }
  async getEmployeeCategoryById(id) {
    return this.employeeCategories.get(id);
  }
  async createEmployeeCategory(insertCategory) {
    const id = randomUUID();
    const category = {
      ...insertCategory,
      id,
      description: insertCategory.description || null,
      color: insertCategory.color || "#10B981",
      permissions: insertCategory.permissions || [],
      isActive: insertCategory.isActive ?? true,
      createdAt: /* @__PURE__ */ new Date()
    };
    this.employeeCategories.set(id, category);
    return category;
  }
  async updateEmployeeCategory(id, updates) {
    const category = this.employeeCategories.get(id);
    if (!category) throw new Error("Employee category not found");
    const updatedCategory = { ...category, ...updates };
    this.employeeCategories.set(id, updatedCategory);
    return updatedCategory;
  }
  async deleteEmployeeCategory(id) {
    this.employeeCategories.delete(id);
  }
  // System Settings implementation
  async getSystemSettings() {
    return this.systemSettings;
  }
  async updateSystemSettings(settings) {
    this.systemSettings = {
      ...this.systemSettings,
      ...settings,
      updatedAt: /* @__PURE__ */ new Date()
    };
    return this.systemSettings;
  }
  // Trainings
  async getTrainings() {
    return Array.from(this.trainings.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }
  async getTrainingById(id) {
    return this.trainings.get(id);
  }
  async createTraining(insertTraining) {
    const id = randomUUID();
    const training = {
      ...insertTraining,
      id,
      instructorId: null,
      startDate: insertTraining.startDate || null,
      endDate: insertTraining.endDate || null,
      location: insertTraining.location || null,
      maxParticipants: insertTraining.maxParticipants || null,
      currentParticipants: 0,
      isActive: insertTraining.isActive !== void 0 ? insertTraining.isActive : true,
      isVisible: insertTraining.isVisible !== void 0 ? insertTraining.isVisible : true,
      isMandatory: insertTraining.isMandatory !== void 0 ? insertTraining.isMandatory : false,
      difficulty: insertTraining.difficulty || null,
      description: insertTraining.description || null,
      thumbnailUrl: insertTraining.thumbnailUrl || null,
      documentUrls: insertTraining.documentUrls || [],
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.trainings.set(id, training);
    return training;
  }
  async updateTraining(id, updates) {
    const training = this.trainings.get(id);
    if (!training) throw new Error("Training not found");
    const updatedTraining = { ...training, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.trainings.set(id, updatedTraining);
    return updatedTraining;
  }
  async deleteTraining(id) {
    const participants = Array.from(this.trainingParticipants.values()).filter((p) => p.trainingId === id);
    participants.forEach((p) => this.trainingParticipants.delete(p.id));
    this.trainings.delete(id);
  }
  // Training Participants
  async getTrainingParticipants(trainingId) {
    return Array.from(this.trainingParticipants.values()).filter((p) => p.trainingId === trainingId).sort((a, b) => (b.registeredAt?.getTime() || 0) - (a.registeredAt?.getTime() || 0));
  }
  async getUserTrainingParticipations(userId) {
    return Array.from(this.trainingParticipants.values()).filter((p) => p.userId === userId).sort((a, b) => (b.registeredAt?.getTime() || 0) - (a.registeredAt?.getTime() || 0));
  }
  async addTrainingParticipant(insertParticipant) {
    const id = randomUUID();
    const participant = {
      ...insertParticipant,
      id,
      registeredAt: /* @__PURE__ */ new Date(),
      status: insertParticipant.status || "registered",
      completionDate: insertParticipant.completionDate || null,
      score: insertParticipant.score || null,
      feedback: insertParticipant.feedback || null
    };
    this.trainingParticipants.set(id, participant);
    const training = this.trainings.get(insertParticipant.trainingId);
    if (training) {
      training.currentParticipants = (training.currentParticipants || 0) + 1;
      this.trainings.set(training.id, training);
    }
    return participant;
  }
  async updateTrainingParticipant(id, updates) {
    const participant = this.trainingParticipants.get(id);
    if (!participant) throw new Error("Training participant not found");
    const updatedParticipant = { ...participant, ...updates };
    this.trainingParticipants.set(id, updatedParticipant);
    return updatedParticipant;
  }
  async removeTrainingParticipant(trainingId, userId) {
    const participant = Array.from(this.trainingParticipants.values()).find((p) => p.trainingId === trainingId && p.userId === userId);
    if (participant) {
      this.trainingParticipants.delete(participant.id);
      const training = this.trainings.get(trainingId);
      if (training) {
        training.currentParticipants = Math.max(0, (training.currentParticipants || 0) - 1);
        this.trainings.set(training.id, training);
      }
    }
  }
  // Views Configuration Management
  viewsConfiguration = /* @__PURE__ */ new Map();
  async getViewsConfig() {
    if (this.viewsConfiguration.size === 0) {
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
          description: "Biblioth\xE8que de documents et r\xE8glements",
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
          description: "Galerie multim\xE9dia et ressources de formation",
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
          description: "R\xE9pertoire des employ\xE9s et contacts",
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
          description: "Syst\xE8me de messagerie interne",
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
          name: "R\xE9clamations",
          description: "Syst\xE8me de gestion des plaintes et suggestions",
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
      defaultViews.forEach((view) => {
        this.viewsConfiguration.set(view.id, view);
      });
    }
    return Array.from(this.viewsConfiguration.values()).sort((a, b) => a.sortOrder - b.sortOrder);
  }
  async saveViewsConfig(views) {
    this.viewsConfiguration.clear();
    views.forEach((view) => {
      this.viewsConfiguration.set(view.id, view);
    });
  }
  async updateViewConfig(viewId, updates) {
    const existingView = this.viewsConfiguration.get(viewId);
    if (existingView) {
      this.viewsConfiguration.set(viewId, { ...existingView, ...updates });
    }
  }
  // User Settings Management
  userConfiguration = /* @__PURE__ */ new Map();
  async getUserSettings(userId) {
    const settings = this.userConfiguration.get(userId);
    if (!settings) {
      const defaultSettings = {
        companyName: "IntraSphere",
        companyLogo: "",
        welcomeMessage: "Bienvenue sur votre portail d'entreprise",
        contactEmail: "contact@intrasphere.com",
        displayName: "Utilisateur",
        email: "utilisateur@entreprise.com",
        bio: "",
        department: "Non sp\xE9cifi\xE9",
        position: "Employ\xE9",
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
        theme: "light",
        language: "fr",
        compactMode: false,
        showSidebar: true,
        animationsEnabled: true,
        colorScheme: "purple",
        fontSize: "medium",
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
  async saveUserSettings(userId, settings) {
    try {
      if (!settings || typeof settings !== "object") {
        throw new Error("Invalid settings object");
      }
      this.userConfiguration.set(userId, { ...settings, updatedAt: (/* @__PURE__ */ new Date()).toISOString() });
      console.log("Settings saved successfully for user:", userId);
    } catch (error) {
      console.error("Error saving user settings:", error);
      throw error;
    }
  }
  async getStats() {
    const now = /* @__PURE__ */ new Date();
    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1e3);
    const newAnnouncements = Array.from(this.announcements.values()).filter((ann) => ann.createdAt > weekAgo).length;
    const updatedDocuments = Array.from(this.documents.values()).filter((doc) => doc.updatedAt > weekAgo).length;
    const connectedUsers = Array.from(this.users.values()).filter((user) => user.isActive).length;
    const pendingComplaints = Array.from(this.complaints.values()).filter((complaint) => complaint.status === "open").length;
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
  async getTrainingRecommendations(userId) {
    const courses2 = await this.getCourses();
    return courses2.slice(0, 3);
  }
  async getCourseLessons(courseId) {
    return this.getLessons(courseId);
  }
  async resetToTestData() {
    const { testUsers: testUsers2, testAnnouncements: testAnnouncements2, testDocuments: testDocuments2, testEvents: testEvents2, testMessages: testMessages2, testComplaints: testComplaints2 } = await Promise.resolve().then(() => (init_testData(), testData_exports));
    this.users.clear();
    this.announcements.clear();
    this.documents.clear();
    this.events.clear();
    this.messages.clear();
    this.complaints.clear();
    this.permissions.clear();
    this.contents.clear();
    this.categories.clear();
    testUsers2.forEach((user) => this.users.set(user.id, user));
    testAnnouncements2.forEach((ann) => this.announcements.set(ann.id, ann));
    testDocuments2.forEach((doc) => this.documents.set(doc.id, doc));
    testEvents2.forEach((event) => this.events.set(event.id, event));
    testMessages2.forEach((msg) => this.messages.set(msg.id, msg));
    testComplaints2.forEach((complaint) => this.complaints.set(complaint.id, complaint));
    this.initializeDefaultCategories();
    this.initializeDefaultContent();
    console.log("\u2705 Test data has been reset successfully");
  }
  initializeDefaultCategories() {
    const defaultCategories = [
      {
        id: "cat-1",
        name: "Formation",
        color: "#10B981",
        icon: "\u{1F393}",
        description: "Mat\xE9riel de formation et apprentissage",
        isVisible: true,
        sortOrder: 1,
        createdAt: /* @__PURE__ */ new Date()
      },
      {
        id: "cat-2",
        name: "Actualit\xE9s",
        color: "#3B82F6",
        icon: "\u{1F4F0}",
        description: "Derni\xE8res nouvelles et informations",
        isVisible: true,
        sortOrder: 2,
        createdAt: /* @__PURE__ */ new Date()
      }
    ];
    defaultCategories.forEach((cat) => this.categories.set(cat.id, cat));
  }
  initializeDefaultContent() {
    const defaultContent = [
      {
        id: "content-1",
        title: "Guide d'int\xE9gration nouveaux employ\xE9s",
        type: "video",
        category: "Formation",
        description: "Vid\xE9o d'accueil compl\xE8te pour faciliter l'int\xE9gration des nouveaux collaborateurs.",
        thumbnailUrl: "https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=225",
        fileUrl: "/content/integration-guide.mp4",
        duration: "12 min",
        viewCount: 245,
        rating: 4,
        isPopular: true,
        isFeatured: true,
        tags: ["formation", "int\xE9gration", "nouveaux employ\xE9s"],
        createdAt: /* @__PURE__ */ new Date("2024-01-15T10:00:00Z"),
        updatedAt: /* @__PURE__ */ new Date("2024-01-15T10:00:00Z")
      }
    ];
    defaultContent.forEach((content) => this.contents.set(content.id, content));
  }
  // E-Learning System Implementation
  initializeELearningData() {
    const defaultCourses = [
      {
        id: "course-1",
        title: "Introduction aux bonnes pratiques de s\xE9curit\xE9 informatique",
        description: "D\xE9couvrez les fondamentaux de la cybers\xE9curit\xE9 et prot\xE9gez vos donn\xE9es professionnelles.",
        category: "compliance",
        difficulty: "beginner",
        duration: 120,
        thumbnailUrl: "/images/security-course.svg",
        authorId: "admin",
        authorName: "\xC9quipe S\xE9curit\xE9 IT",
        isPublished: true,
        isMandatory: true,
        prerequisites: "[]",
        tags: '["s\xE9curit\xE9", "informatique", "donn\xE9es", "obligatoire"]',
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
      },
      {
        id: "course-2",
        title: "D\xE9veloppement personnel et leadership",
        description: "D\xE9veloppez vos comp\xE9tences de leadership et votre potentiel professionnel.",
        category: "leadership",
        difficulty: "intermediate",
        duration: 180,
        thumbnailUrl: "/images/leadership-course.svg",
        authorId: "marie.martin",
        authorName: "Marie Martin",
        isPublished: true,
        isMandatory: false,
        prerequisites: "[]",
        tags: '["leadership", "d\xE9veloppement", "management"]',
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
      },
      {
        id: "course-3",
        title: "Communication efficace en \xE9quipe",
        description: "Am\xE9liorez votre communication interpersonnelle et vos techniques de pr\xE9sentation.",
        category: "soft-skills",
        difficulty: "beginner",
        duration: 90,
        thumbnailUrl: "/images/communication-course.svg",
        authorId: "pierre.dubois",
        authorName: "Pierre Dubois",
        isPublished: true,
        isMandatory: false,
        prerequisites: "[]",
        tags: '["communication", "\xE9quipe", "pr\xE9sentation"]',
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
      }
    ];
    const defaultLessons = [
      {
        id: "lesson-1",
        courseId: "course-1",
        title: "Les bases de la cybers\xE9curit\xE9",
        description: "Introduction aux concepts fondamentaux",
        content: "<h2>Bienvenue dans le monde de la cybers\xE9curit\xE9</h2><p>Dans cette le\xE7on, nous allons explorer les concepts fondamentaux...</p>",
        order: 1,
        duration: 30,
        videoUrl: "/videos/security-basics.mp4",
        documentUrl: "/docs/security-guide.pdf",
        isRequired: true,
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
      },
      {
        id: "lesson-2",
        courseId: "course-1",
        title: "Mots de passe et authentification",
        description: "Cr\xE9ez des mots de passe s\xE9curis\xE9s",
        content: "<h2>L'importance des mots de passe forts</h2><p>Un mot de passe fort est votre premi\xE8re ligne de d\xE9fense...</p>",
        order: 2,
        duration: 25,
        videoUrl: "/videos/password-security.mp4",
        documentUrl: "/docs/password-policy.pdf",
        isRequired: true,
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
      }
    ];
    const defaultResources = [
      {
        id: "resource-1",
        title: "Guide de r\xE9f\xE9rence - Politiques de s\xE9curit\xE9",
        description: "Document complet sur les politiques de s\xE9curit\xE9 de l'entreprise",
        category: "documentation",
        type: "pdf",
        url: "/resources/security-policies.pdf",
        thumbnailUrl: "/images/pdf-icon.svg",
        authorId: "admin",
        authorName: "\xC9quipe IT",
        tags: '["s\xE9curit\xE9", "politique", "r\xE9f\xE9rence"]',
        downloadCount: 45,
        rating: 4.8,
        isPublic: true,
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
      },
      {
        id: "resource-2",
        title: "Template - Plan de formation individuel",
        description: "Mod\xE8le pour cr\xE9er votre plan de d\xE9veloppement professionnel",
        category: "template",
        type: "document",
        url: "/resources/training-plan-template.docx",
        thumbnailUrl: "/images/doc-icon.svg",
        authorId: "marie.martin",
        authorName: "Marie Martin",
        tags: '["formation", "template", "d\xE9veloppement"]',
        downloadCount: 23,
        rating: 4.5,
        isPublic: true,
        createdAt: /* @__PURE__ */ new Date(),
        updatedAt: /* @__PURE__ */ new Date()
      }
    ];
    defaultCourses.forEach((course) => this.courses.set(course.id, course));
    defaultLessons.forEach((lesson) => this.lessons.set(lesson.id, lesson));
    defaultResources.forEach((resource) => this.resources.set(resource.id, resource));
  }
  // Courses
  async getCourses() {
    return Array.from(this.courses.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }
  async getCourseById(id) {
    return this.courses.get(id);
  }
  async createCourse(insertCourse) {
    const id = randomUUID();
    const course = {
      ...insertCourse,
      id,
      authorId: insertCourse.authorName || "Syst\xE8me",
      description: insertCourse.description || null,
      duration: insertCourse.duration || null,
      thumbnailUrl: insertCourse.thumbnailUrl || null,
      isPublished: insertCourse.isPublished || false,
      isMandatory: insertCourse.isMandatory || false,
      prerequisites: insertCourse.prerequisites || "[]",
      tags: insertCourse.tags || "[]",
      difficulty: insertCourse.difficulty || "beginner",
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.courses.set(id, course);
    return course;
  }
  async updateCourse(id, updates) {
    const course = this.courses.get(id);
    if (!course) throw new Error("Course not found");
    const updatedCourse = { ...course, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.courses.set(id, updatedCourse);
    return updatedCourse;
  }
  async deleteCourse(id) {
    this.courses.delete(id);
    Array.from(this.lessons.values()).filter((lesson) => lesson.courseId === id).forEach((lesson) => this.lessons.delete(lesson.id));
  }
  // Lessons
  async getLessons(courseId) {
    return Array.from(this.lessons.values()).filter((lesson) => lesson.courseId === courseId).sort((a, b) => (a.order || 0) - (b.order || 0));
  }
  async getLessonById(id) {
    return this.lessons.get(id);
  }
  async createLesson(insertLesson) {
    const id = randomUUID();
    const lesson = {
      ...insertLesson,
      id,
      description: insertLesson.description || null,
      order: insertLesson.order || 0,
      duration: insertLesson.duration || null,
      videoUrl: insertLesson.videoUrl || null,
      documentUrl: insertLesson.documentUrl || null,
      isRequired: insertLesson.isRequired !== false,
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.lessons.set(id, lesson);
    return lesson;
  }
  async updateLesson(id, updates) {
    const lesson = this.lessons.get(id);
    if (!lesson) throw new Error("Lesson not found");
    const updatedLesson = { ...lesson, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.lessons.set(id, updatedLesson);
    return updatedLesson;
  }
  async deleteLesson(id) {
    this.lessons.delete(id);
  }
  // Quizzes
  async getQuizzes(courseId) {
    return Array.from(this.quizzes.values()).filter((quiz) => quiz.courseId === courseId);
  }
  async getQuizById(id) {
    return this.quizzes.get(id);
  }
  async createQuiz(insertQuiz) {
    const id = randomUUID();
    const quiz = {
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
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.quizzes.set(id, quiz);
    return quiz;
  }
  async updateQuiz(id, updates) {
    const quiz = this.quizzes.get(id);
    if (!quiz) throw new Error("Quiz not found");
    const updatedQuiz = { ...quiz, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.quizzes.set(id, updatedQuiz);
    return updatedQuiz;
  }
  async deleteQuiz(id) {
    this.quizzes.delete(id);
  }
  // Enrollments and Progress
  async getUserEnrollments(userId) {
    return Array.from(this.enrollments.values()).filter((enrollment) => enrollment.userId === userId);
  }
  async getEnrollmentById(id) {
    return this.enrollments.get(id);
  }
  async enrollUser(userId, courseId) {
    const id = randomUUID();
    const enrollment = {
      id,
      userId,
      courseId,
      enrolledAt: /* @__PURE__ */ new Date(),
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
  async updateEnrollmentProgress(id, updates) {
    const enrollment = this.enrollments.get(id);
    if (!enrollment) throw new Error("Enrollment not found");
    const updatedEnrollment = { ...enrollment, ...updates };
    this.enrollments.set(id, updatedEnrollment);
    return updatedEnrollment;
  }
  async getUserLessonProgress(userId, courseId) {
    return Array.from(this.lessonProgress.values()).filter((progress) => progress.userId === userId && progress.courseId === courseId);
  }
  async updateLessonProgress(userId, lessonId, courseId, completed) {
    const key = `${userId}-${lessonId}`;
    let progress = Array.from(this.lessonProgress.values()).find((p) => p.userId === userId && p.lessonId === lessonId);
    if (!progress) {
      const id = randomUUID();
      progress = {
        id,
        userId,
        lessonId,
        courseId,
        isCompleted: completed,
        timeSpent: 0,
        completedAt: completed ? /* @__PURE__ */ new Date() : null,
        createdAt: /* @__PURE__ */ new Date()
      };
    } else {
      progress.isCompleted = completed;
      progress.completedAt = completed ? /* @__PURE__ */ new Date() : null;
    }
    this.lessonProgress.set(progress.id, progress);
    return progress;
  }
  // Quiz Attempts
  async getUserQuizAttempts(userId, quizId) {
    return Array.from(this.quizAttempts.values()).filter((attempt) => attempt.userId === userId && attempt.quizId === quizId).sort((a, b) => (b.completedAt?.getTime() || 0) - (a.completedAt?.getTime() || 0));
  }
  async createQuizAttempt(attemptData) {
    const id = randomUUID();
    const attempt = {
      ...attemptData,
      id,
      completedAt: /* @__PURE__ */ new Date()
    };
    this.quizAttempts.set(id, attempt);
    return attempt;
  }
  // Certificates
  async getUserCertificates(userId) {
    return Array.from(this.certificates.values()).filter((cert) => cert.userId === userId).sort((a, b) => (b.issuedAt?.getTime() || 0) - (a.issuedAt?.getTime() || 0));
  }
  async createCertificate(certData) {
    const id = randomUUID();
    const certificate = {
      ...certData,
      id,
      issuedAt: /* @__PURE__ */ new Date()
    };
    this.certificates.set(id, certificate);
    return certificate;
  }
  // Resources
  async getResources() {
    return Array.from(this.resources.values()).sort(
      (a, b) => (b.createdAt?.getTime() || 0) - (a.createdAt?.getTime() || 0)
    );
  }
  async getResourceById(id) {
    return this.resources.get(id);
  }
  async createResource(insertResource) {
    const id = randomUUID();
    const resource = {
      ...insertResource,
      id,
      description: insertResource.description || null,
      thumbnailUrl: insertResource.thumbnailUrl || null,
      authorId: insertResource.authorName || "Syst\xE8me",
      tags: insertResource.tags || "[]",
      downloadCount: 0,
      rating: 0,
      isPublic: insertResource.isPublic !== false,
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.resources.set(id, resource);
    return resource;
  }
  async updateResource(id, updates) {
    const resource = this.resources.get(id);
    if (!resource) throw new Error("Resource not found");
    const updatedResource = { ...resource, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.resources.set(id, updatedResource);
    return updatedResource;
  }
  async deleteResource(id) {
    this.resources.delete(id);
  }
  // Training Analytics Implementation
  async getAllTrainingParticipants() {
    return Array.from(this.trainingParticipants.values());
  }
  async markLessonComplete(userId, courseId, lessonId) {
    await this.updateLessonProgress(userId, lessonId, courseId, true);
  }
  // Search functionality
  async searchUsers(query) {
    const searchTerm = query.toLowerCase();
    return Array.from(this.users.values()).filter(
      (user) => user.isActive && (user.name.toLowerCase().includes(searchTerm) || user.username.toLowerCase().includes(searchTerm) || user.email && user.email.toLowerCase().includes(searchTerm) || user.employeeId && user.employeeId.toLowerCase().includes(searchTerm))
    );
  }
  async searchContent(query) {
    const lowerQuery = query.toLowerCase();
    return Array.from(this.contents.values()).filter(
      (content) => content.title.toLowerCase().includes(lowerQuery) || content.description?.toLowerCase().includes(lowerQuery)
    );
  }
  async searchDocuments(query) {
    const lowerQuery = query.toLowerCase();
    return Array.from(this.documents.values()).filter(
      (doc) => doc.title.toLowerCase().includes(lowerQuery) || doc.description?.toLowerCase().includes(lowerQuery)
    );
  }
  async searchAnnouncements(query) {
    const lowerQuery = query.toLowerCase();
    return Array.from(this.announcements.values()).filter(
      (ann) => ann.title.toLowerCase().includes(lowerQuery) || ann.content.toLowerCase().includes(lowerQuery)
    );
  }
  // Forum System Implementation
  // Forum Categories
  async getForumCategories() {
    return Array.from(this.forumCategories.values()).filter((category) => category.isActive).sort((a, b) => (a.sortOrder || 0) - (b.sortOrder || 0));
  }
  async getForumCategoryById(id) {
    return this.forumCategories.get(id);
  }
  async createForumCategory(insertCategory) {
    const id = randomUUID();
    const category = {
      ...insertCategory,
      id,
      description: insertCategory.description || null,
      color: insertCategory.color || "#3B82F6",
      icon: insertCategory.icon || "\u{1F4AC}",
      sortOrder: insertCategory.sortOrder || 0,
      isActive: insertCategory.isActive !== false,
      isModerated: insertCategory.isModerated || false,
      accessLevel: insertCategory.accessLevel || "all",
      moderatorIds: insertCategory.moderatorIds || null,
      createdAt: /* @__PURE__ */ new Date()
    };
    this.forumCategories.set(id, category);
    return category;
  }
  async updateForumCategory(id, updates) {
    const category = this.forumCategories.get(id);
    if (!category) throw new Error("Forum category not found");
    const updatedCategory = { ...category, ...updates };
    this.forumCategories.set(id, updatedCategory);
    return updatedCategory;
  }
  async deleteForumCategory(id) {
    const topics = Array.from(this.forumTopics.values()).filter((t) => t.categoryId === id);
    for (const topic of topics) {
      await this.deleteForumTopic(topic.id);
    }
    this.forumCategories.delete(id);
  }
  // Forum Topics
  async getForumTopics(categoryId) {
    let topics = Array.from(this.forumTopics.values());
    if (categoryId) {
      topics = topics.filter((topic) => topic.categoryId === categoryId);
    }
    return topics.sort((a, b) => {
      if (a.isPinned && !b.isPinned) return -1;
      if (!a.isPinned && b.isPinned) return 1;
      return (b.lastReplyAt?.getTime() || b.createdAt?.getTime() || 0) - (a.lastReplyAt?.getTime() || a.createdAt?.getTime() || 0);
    });
  }
  async getForumTopicById(id) {
    return this.forumTopics.get(id);
  }
  async createForumTopic(insertTopic) {
    const id = randomUUID();
    const topic = {
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
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.forumTopics.set(id, topic);
    await this.updateUserTopicCount(insertTopic.authorId, 1);
    return topic;
  }
  async updateForumTopic(id, updates) {
    const topic = this.forumTopics.get(id);
    if (!topic) throw new Error("Forum topic not found");
    const updatedTopic = { ...topic, ...updates, updatedAt: /* @__PURE__ */ new Date() };
    this.forumTopics.set(id, updatedTopic);
    return updatedTopic;
  }
  async deleteForumTopic(id) {
    const topic = this.forumTopics.get(id);
    if (topic) {
      const posts = Array.from(this.forumPosts.values()).filter((p) => p.topicId === id);
      for (const post of posts) {
        this.forumPosts.delete(post.id);
        const likes = Array.from(this.forumLikes.values()).filter((l) => l.postId === post.id);
        for (const like of likes) {
          this.forumLikes.delete(like.id);
        }
      }
      await this.updateUserTopicCount(topic.authorId, -1);
      this.forumTopics.delete(id);
    }
  }
  async incrementTopicViews(id) {
    const topic = this.forumTopics.get(id);
    if (topic) {
      topic.viewCount = (topic.viewCount || 0) + 1;
      this.forumTopics.set(id, topic);
    }
  }
  // Forum Posts
  async getForumPosts(topicId) {
    return Array.from(this.forumPosts.values()).filter((post) => post.topicId === topicId && !post.isDeleted).sort((a, b) => (a.createdAt?.getTime() || 0) - (b.createdAt?.getTime() || 0));
  }
  async getForumPostById(id) {
    const post = this.forumPosts.get(id);
    return post && !post.isDeleted ? post : void 0;
  }
  async createForumPost(insertPost) {
    const id = randomUUID();
    const post = {
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
      createdAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.forumPosts.set(id, post);
    const topic = this.forumTopics.get(insertPost.topicId);
    if (topic && !insertPost.isFirstPost) {
      topic.replyCount = (topic.replyCount || 0) + 1;
      topic.lastReplyAt = post.createdAt;
      topic.lastReplyBy = insertPost.authorId;
      topic.lastReplyByName = insertPost.authorName;
      this.forumTopics.set(insertPost.topicId, topic);
    }
    await this.updateUserPostCount(insertPost.authorId, 1);
    return post;
  }
  async updateForumPost(id, updates) {
    const post = this.forumPosts.get(id);
    if (!post || post.isDeleted) throw new Error("Forum post not found");
    const updatedPost = {
      ...post,
      ...updates,
      isEdited: true,
      editedAt: /* @__PURE__ */ new Date(),
      updatedAt: /* @__PURE__ */ new Date()
    };
    this.forumPosts.set(id, updatedPost);
    return updatedPost;
  }
  async deleteForumPost(id, deletedBy) {
    const post = this.forumPosts.get(id);
    if (post && !post.isDeleted) {
      post.isDeleted = true;
      post.deletedAt = /* @__PURE__ */ new Date();
      post.deletedBy = deletedBy;
      this.forumPosts.set(id, post);
      const topic = this.forumTopics.get(post.topicId);
      if (topic && !post.isFirstPost) {
        topic.replyCount = Math.max((topic.replyCount || 0) - 1, 0);
        this.forumTopics.set(post.topicId, topic);
      }
      await this.updateUserPostCount(post.authorId, -1);
      const likes = Array.from(this.forumLikes.values()).filter((l) => l.postId === id);
      for (const like of likes) {
        this.forumLikes.delete(like.id);
      }
    }
  }
  // Forum Likes/Reactions
  async getForumPostLikes(postId) {
    return Array.from(this.forumLikes.values()).filter((like) => like.postId === postId);
  }
  async toggleForumPostLike(insertLike) {
    const existingLike = Array.from(this.forumLikes.values()).find((like) => like.postId === insertLike.postId && like.userId === insertLike.userId);
    if (existingLike) {
      this.forumLikes.delete(existingLike.id);
      const post = this.forumPosts.get(insertLike.postId);
      if (post) {
        post.likeCount = Math.max((post.likeCount || 0) - 1, 0);
        this.forumPosts.set(insertLike.postId, post);
      }
      return null;
    } else {
      const id = randomUUID();
      const like = {
        ...insertLike,
        id,
        reactionType: insertLike.reactionType || "like",
        createdAt: /* @__PURE__ */ new Date()
      };
      this.forumLikes.set(id, like);
      const post = this.forumPosts.get(insertLike.postId);
      if (post) {
        post.likeCount = (post.likeCount || 0) + 1;
        this.forumPosts.set(insertLike.postId, post);
      }
      return like;
    }
  }
  // Forum User Stats
  async getForumUserStats(userId) {
    let stats = Array.from(this.forumUserStats.values()).find((s) => s.userId === userId);
    if (!stats) {
      const id = randomUUID();
      stats = {
        id,
        userId,
        postCount: 0,
        topicCount: 0,
        likeCount: 0,
        reputationScore: 0,
        badges: "[]",
        joinedAt: /* @__PURE__ */ new Date(),
        lastActiveAt: /* @__PURE__ */ new Date()
      };
      this.forumUserStats.set(id, stats);
    }
    return stats;
  }
  async updateForumUserStats(userId, updates) {
    let stats = await this.getForumUserStats(userId);
    if (!stats) throw new Error("User stats not found");
    const updatedStats = { ...stats, ...updates, lastActiveAt: /* @__PURE__ */ new Date() };
    this.forumUserStats.set(stats.id, updatedStats);
    return updatedStats;
  }
  // Helper methods for forum stats
  async updateUserPostCount(userId, change) {
    const stats = await this.getForumUserStats(userId);
    if (stats) {
      stats.postCount = Math.max((stats.postCount || 0) + change, 0);
      this.forumUserStats.set(stats.id, stats);
    }
  }
  async updateUserTopicCount(userId, change) {
    const stats = await this.getForumUserStats(userId);
    if (stats) {
      stats.topicCount = Math.max((stats.topicCount || 0) + change, 0);
      this.forumUserStats.set(stats.id, stats);
    }
  }
};
var storage = new MemStorage();

// server/services/auth.ts
import bcrypt from "bcrypt";
var SALT_ROUNDS = 12;
var AuthService = class {
  /**
   * Hash a password using bcrypt
   */
  static async hashPassword(password) {
    return bcrypt.hash(password, SALT_ROUNDS);
  }
  /**
   * Verify a password against a hash
   */
  static async verifyPassword(password, hash) {
    return bcrypt.compare(password, hash);
  }
  /**
   * Validate password strength
   */
  static validatePasswordStrength(password) {
    const errors = [];
    if (password.length < 8) {
      errors.push("Le mot de passe doit contenir au moins 8 caract\xE8res");
    }
    if (!/[A-Z]/.test(password)) {
      errors.push("Le mot de passe doit contenir au moins une majuscule");
    }
    if (!/[a-z]/.test(password)) {
      errors.push("Le mot de passe doit contenir au moins une minuscule");
    }
    if (!/[0-9]/.test(password)) {
      errors.push("Le mot de passe doit contenir au moins un chiffre");
    }
    if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
      errors.push("Le mot de passe doit contenir au moins un caract\xE8re sp\xE9cial");
    }
    return {
      isValid: errors.length === 0,
      errors
    };
  }
};

// server/services/email.ts
import nodemailer from "nodemailer";
var EmailService = class {
  transporter = null;
  isConfigured = false;
  /**
   * Configure email service with SMTP settings
   */
  configure(config) {
    try {
      this.transporter = nodemailer.createTransport({
        host: config.host,
        port: config.port,
        secure: config.secure,
        auth: config.auth
      });
      this.isConfigured = true;
      console.log("Email service configured successfully");
    } catch (error) {
      console.error("Failed to configure email service:", error);
      this.isConfigured = false;
    }
  }
  /**
   * Send an email
   */
  async sendEmail(options) {
    if (!this.isConfigured || !this.transporter) {
      console.warn("Email service not configured. Email not sent.");
      return false;
    }
    try {
      const mailOptions = {
        from: options.from || process.env.EMAIL_FROM || "noreply@intrasphere.com",
        to: Array.isArray(options.to) ? options.to.join(", ") : options.to,
        subject: options.subject,
        text: options.text,
        html: options.html || options.text
      };
      const result = await this.transporter.sendMail(mailOptions);
      console.log("Email sent successfully:", result.messageId);
      return true;
    } catch (error) {
      console.error("Failed to send email:", error);
      return false;
    }
  }
  /**
   * Send welcome email to new user
   */
  async sendWelcomeEmail(userEmail, userName, tempPassword) {
    const subject = "Bienvenue sur IntraSphere";
    const html = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #8B5CF6;">Bienvenue sur IntraSphere !</h2>
        <p>Bonjour <strong>${userName}</strong>,</p>
        <p>Votre compte IntraSphere a \xE9t\xE9 cr\xE9\xE9 avec succ\xE8s. Vous pouvez maintenant acc\xE9der \xE0 votre portail d'entreprise.</p>
        ${tempPassword ? `
          <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Mot de passe temporaire :</strong> <code>${tempPassword}</code></p>
            <p style="color: #ef4444; font-size: 14px;">\u26A0\uFE0F Veuillez changer votre mot de passe lors de votre premi\xE8re connexion.</p>
          </div>
        ` : ""}
        <p>Fonctionnalit\xE9s disponibles :</p>
        <ul>
          <li>\u{1F4E2} Annonces et communications</li>
          <li>\u{1F4C1} Biblioth\xE8que documentaire</li>
          <li>\u{1F4AC} Forum et discussions</li>
          <li>\u{1F4E7} Messagerie interne</li>
          <li>\u{1F4DA} Plateforme e-learning</li>
        </ul>
        <p>Cordialement,<br>L'\xE9quipe IntraSphere</p>
      </div>
    `;
    return this.sendEmail({
      to: userEmail,
      subject,
      html
    });
  }
  /**
   * Send password reset email
   */
  async sendPasswordResetEmail(userEmail, userName, resetLink) {
    const subject = "R\xE9initialisation de votre mot de passe IntraSphere";
    const html = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #8B5CF6;">R\xE9initialisation de mot de passe</h2>
        <p>Bonjour <strong>${userName}</strong>,</p>
        <p>Vous avez demand\xE9 la r\xE9initialisation de votre mot de passe IntraSphere.</p>
        <div style="text-align: center; margin: 30px 0;">
          <a href="${resetLink}" style="background: #8B5CF6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block;">
            R\xE9initialiser mon mot de passe
          </a>
        </div>
        <p style="color: #6b7280; font-size: 14px;">
          Ce lien expire dans 1 heure. Si vous n'avez pas demand\xE9 cette r\xE9initialisation, ignorez cet email.
        </p>
        <p>Cordialement,<br>L'\xE9quipe IntraSphere</p>
      </div>
    `;
    return this.sendEmail({
      to: userEmail,
      subject,
      html
    });
  }
  /**
   * Send notification email
   */
  async sendNotificationEmail(userEmail, userName, notificationType, content) {
    const subject = `IntraSphere - ${notificationType}`;
    const html = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #8B5CF6;">${notificationType}</h2>
        <p>Bonjour <strong>${userName}</strong>,</p>
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
          ${content}
        </div>
        <p>Connectez-vous \xE0 IntraSphere pour plus de d\xE9tails.</p>
        <p>Cordialement,<br>L'\xE9quipe IntraSphere</p>
      </div>
    `;
    return this.sendEmail({
      to: userEmail,
      subject,
      html
    });
  }
  /**
   * Test email configuration
   */
  async testConfiguration() {
    if (!this.isConfigured || !this.transporter) {
      return false;
    }
    try {
      await this.transporter.verify();
      console.log("Email configuration test successful");
      return true;
    } catch (error) {
      console.error("Email configuration test failed:", error);
      return false;
    }
  }
};
var emailService = new EmailService();
if (process.env.SMTP_HOST && process.env.SMTP_USER && process.env.SMTP_PASS) {
  emailService.configure({
    host: process.env.SMTP_HOST,
    port: parseInt(process.env.SMTP_PORT || "587"),
    secure: process.env.SMTP_SECURE === "true",
    auth: {
      user: process.env.SMTP_USER,
      pass: process.env.SMTP_PASS
    }
  });
}

// server/routes/api.ts
init_schema();
async function registerRoutes(app2) {
  const requireAuth = (req, res, next) => {
    if (!req.session?.userId) {
      return res.status(401).json({ message: "Authentication required" });
    }
    next();
  };
  const requireRole = (roles) => {
    return async (req, res, next) => {
      if (!req.session?.userId) {
        return res.status(401).json({ message: "Authentication required" });
      }
      const user = await storage.getUser(req.session.userId);
      if (!user || !roles.includes(user.role)) {
        return res.status(403).json({ message: "Insufficient permissions" });
      }
      req.user = user;
      next();
    };
  };
  app2.post("/api/auth/login", async (req, res) => {
    try {
      const { username, password } = req.body;
      if (!username || !password) {
        return res.status(400).json({ message: "Username and password required" });
      }
      const user = await storage.getUserByUsername(username);
      if (!user) {
        return res.status(401).json({ message: "Invalid credentials" });
      }
      const isValidPassword = await AuthService.verifyPassword(password, user.password);
      if (!isValidPassword) {
        return res.status(401).json({ message: "Invalid credentials" });
      }
      if (!user.isActive) {
        return res.status(401).json({ message: "Account is deactivated" });
      }
      req.session.userId = user.id;
      req.session.user = user;
      const { password: _, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Login error:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.post("/api/auth/register", async (req, res) => {
    try {
      const result = insertUserSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({
          message: "Invalid user data",
          errors: result.error.issues
        });
      }
      const existingUser = await storage.getUserByUsername(result.data.username);
      if (existingUser) {
        return res.status(409).json({ message: "Username already exists" });
      }
      const hashedPassword = await AuthService.hashPassword(result.data.password);
      const newUser = await storage.createUser({
        ...result.data,
        password: hashedPassword,
        role: "employee"
      });
      if (result.data.email) {
        await emailService.sendWelcomeEmail(result.data.email, result.data.name);
      }
      req.session.userId = newUser.id;
      req.session.user = newUser;
      const { password: _, ...userWithoutPassword } = newUser;
      res.status(201).json(userWithoutPassword);
    } catch (error) {
      console.error("Registration error:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.get("/api/auth/me", async (req, res) => {
    try {
      const userId = req.session?.userId;
      if (!userId) {
        return res.status(401).json({ message: "Not authenticated" });
      }
      const user = await storage.getUser(userId);
      if (!user || !user.isActive) {
        return res.status(401).json({ message: "User not found or inactive" });
      }
      const { password: _, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Get user error:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.post("/api/auth/logout", (req, res) => {
    req.session.destroy((err) => {
      if (err) {
        console.error("Session destroy error:", err);
      }
    });
    res.json({ message: "Logged out successfully" });
  });
  app2.get("/api/stats", async (_req, res) => {
    try {
      const stats = await storage.getStats();
      res.json(stats);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch stats" });
    }
  });
  app2.get("/api/announcements", async (_req, res) => {
    try {
      const announcements2 = await storage.getAnnouncements();
      res.json(announcements2);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch announcements" });
    }
  });
  app2.get("/api/announcements/:id", async (req, res) => {
    try {
      const announcement = await storage.getAnnouncementById(req.params.id);
      if (!announcement) {
        return res.status(404).json({ message: "Announcement not found" });
      }
      res.json(announcement);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch announcement" });
    }
  });
  app2.post("/api/announcements", async (req, res) => {
    try {
      const announcement = await storage.createAnnouncement(req.body);
      res.status(201).json(announcement);
    } catch (error) {
      res.status(500).json({ message: "Failed to create announcement" });
    }
  });
  app2.get("/api/documents", async (_req, res) => {
    try {
      const documents2 = await storage.getDocuments();
      res.json(documents2);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch documents" });
    }
  });
  app2.get("/api/documents/:id", async (req, res) => {
    try {
      const document = await storage.getDocumentById(req.params.id);
      if (!document) {
        return res.status(404).json({ message: "Document not found" });
      }
      res.json(document);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch document" });
    }
  });
  app2.post("/api/documents", async (req, res) => {
    try {
      const result = insertDocumentSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid document data", errors: result.error.issues });
      }
      const document = await storage.createDocument(result.data);
      res.status(201).json(document);
    } catch (error) {
      res.status(500).json({ message: "Failed to create document" });
    }
  });
  app2.patch("/api/documents/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedDocument = await storage.updateDocument(id, req.body);
      res.json(updatedDocument);
    } catch (error) {
      console.error("Error updating document:", error);
      res.status(500).json({ error: "Failed to update document" });
    }
  });
  app2.delete("/api/documents/:id", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteDocument(id);
      res.json({ message: "Document deleted successfully" });
    } catch (error) {
      console.error("Error deleting document:", error);
      res.status(500).json({ error: "Failed to delete document" });
    }
  });
  app2.get("/api/events", async (_req, res) => {
    try {
      const events2 = await storage.getEvents();
      res.json(events2);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch events" });
    }
  });
  app2.get("/api/events/:id", async (req, res) => {
    try {
      const event = await storage.getEventById(req.params.id);
      if (!event) {
        return res.status(404).json({ message: "Event not found" });
      }
      res.json(event);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch event" });
    }
  });
  app2.post("/api/events", async (req, res) => {
    try {
      const result = insertEventSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid event data", errors: result.error.issues });
      }
      const event = await storage.createEvent(result.data);
      res.status(201).json(event);
    } catch (error) {
      res.status(500).json({ message: "Failed to create event" });
    }
  });
  app2.get("/api/users", async (req, res) => {
    try {
      const users2 = await storage.getUsers();
      res.json(users2);
    } catch (error) {
      console.error("Error fetching users:", error);
      res.status(500).json({ error: "Failed to fetch users" });
    }
  });
  app2.post("/api/users", async (req, res) => {
    try {
      const result = insertUserSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid user data", errors: result.error.issues });
      }
      const user = await storage.createUser(result.data);
      res.status(201).json(user);
    } catch (error) {
      console.error("Error creating user:", error);
      res.status(500).json({ error: "Failed to create user" });
    }
  });
  app2.patch("/api/users/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedUser = await storage.updateUser(id, req.body);
      res.json(updatedUser);
    } catch (error) {
      console.error("Error updating user:", error);
      res.status(500).json({ error: "Failed to update user" });
    }
  });
  app2.delete("/api/users/:id", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.updateUser(id, { isActive: false });
      res.json({ message: "User deactivated successfully" });
    } catch (error) {
      console.error("Error deactivating user:", error);
      res.status(500).json({ error: "Failed to deactivate user" });
    }
  });
  app2.get("/api/messages/:userId", async (req, res) => {
    try {
      const { userId } = req.params;
      const messages2 = await storage.getMessages(userId);
      res.json(messages2);
    } catch (error) {
      console.error("Error fetching messages:", error);
      res.status(500).json({ error: "Failed to fetch messages" });
    }
  });
  app2.post("/api/messages", async (req, res) => {
    try {
      const message = await storage.createMessage(req.body);
      res.status(201).json(message);
    } catch (error) {
      console.error("Error creating message:", error);
      res.status(500).json({ error: "Failed to create message" });
    }
  });
  app2.patch("/api/messages/:id/read", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.markMessageAsRead(id);
      res.json({ success: true });
    } catch (error) {
      console.error("Error marking message as read:", error);
      res.status(500).json({ error: "Failed to mark message as read" });
    }
  });
  app2.get("/api/complaints", async (req, res) => {
    try {
      const complaints2 = await storage.getComplaints();
      res.json(complaints2);
    } catch (error) {
      console.error("Error fetching complaints:", error);
      res.status(500).json({ error: "Failed to fetch complaints" });
    }
  });
  app2.post("/api/complaints", async (req, res) => {
    try {
      const complaint = await storage.createComplaint(req.body);
      res.status(201).json(complaint);
    } catch (error) {
      console.error("Error creating complaint:", error);
      res.status(500).json({ error: "Failed to create complaint" });
    }
  });
  app2.patch("/api/complaints/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedComplaint = await storage.updateComplaint(id, req.body);
      res.json(updatedComplaint);
    } catch (error) {
      console.error("Error updating complaint:", error);
      res.status(500).json({ error: "Failed to update complaint" });
    }
  });
  app2.get("/api/permissions", async (req, res) => {
    try {
      const users2 = await storage.getUsers();
      const allPermissions = [];
      for (const user of users2) {
        const userPermissions = await storage.getPermissions(user.id);
        allPermissions.push(...userPermissions);
      }
      res.json(allPermissions);
    } catch (error) {
      console.error("Error fetching permissions:", error);
      res.status(500).json({ error: "Failed to fetch permissions" });
    }
  });
  app2.get("/api/permissions/:userId", async (req, res) => {
    try {
      const { userId } = req.params;
      const permissions2 = await storage.getPermissions(userId);
      res.json(permissions2);
    } catch (error) {
      console.error("Error fetching user permissions:", error);
      res.status(500).json({ error: "Failed to fetch user permissions" });
    }
  });
  app2.post("/api/permissions", async (req, res) => {
    try {
      const result = insertPermissionSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid permission data", errors: result.error.issues });
      }
      const permission = await storage.createPermission(result.data);
      res.status(201).json(permission);
    } catch (error) {
      console.error("Error creating permission:", error);
      res.status(500).json({ error: "Failed to create permission" });
    }
  });
  app2.delete("/api/permissions/:id", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.revokePermission(id);
      res.json({ message: "Permission revoked successfully" });
    } catch (error) {
      console.error("Error revoking permission:", error);
      res.status(500).json({ error: "Failed to revoke permission" });
    }
  });
  app2.get("/api/contents", async (req, res) => {
    try {
      const contents2 = await storage.getContents();
      res.json(contents2);
    } catch (error) {
      console.error("Error fetching contents:", error);
      res.status(500).json({ error: "Failed to fetch contents" });
    }
  });
  app2.post("/api/contents", async (req, res) => {
    try {
      const result = insertContentSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid content data", errors: result.error.issues });
      }
      const content = await storage.createContent(result.data);
      res.status(201).json(content);
    } catch (error) {
      console.error("Error creating content:", error);
      res.status(500).json({ error: "Failed to create content" });
    }
  });
  app2.patch("/api/contents/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedContent = await storage.updateContent(id, req.body);
      res.json(updatedContent);
    } catch (error) {
      console.error("Error updating content:", error);
      res.status(500).json({ error: "Failed to update content" });
    }
  });
  app2.delete("/api/contents/:id", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteContent(id);
      res.json({ message: "Content deleted successfully" });
    } catch (error) {
      console.error("Error deleting content:", error);
      res.status(500).json({ error: "Failed to delete content" });
    }
  });
  app2.get("/api/categories", async (req, res) => {
    try {
      const categories2 = await storage.getCategories();
      res.json(categories2);
    } catch (error) {
      console.error("Error fetching categories:", error);
      res.status(500).json({ error: "Failed to fetch categories" });
    }
  });
  app2.get("/api/search/global", async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== "string" || q.length < 2) {
        return res.json([]);
      }
      const query = q.toLowerCase();
      const results = [];
      const users2 = await storage.getUsers();
      users2.forEach((user) => {
        if (user.name.toLowerCase().includes(query) || user.department?.toLowerCase().includes(query) || user.position?.toLowerCase().includes(query)) {
          results.push({
            id: user.id,
            title: user.name,
            type: "user",
            description: `${user.position || ""} - ${user.department || ""}`,
            url: `/directory?user=${user.id}`,
            metadata: user
          });
        }
      });
      const documents2 = await storage.getDocuments();
      documents2.forEach((doc) => {
        if (doc.title.toLowerCase().includes(query) || doc.description?.toLowerCase().includes(query)) {
          results.push({
            id: doc.id,
            title: doc.title,
            type: "document",
            description: doc.description,
            url: `/content/documents/${doc.id}`,
            metadata: doc
          });
        }
      });
      const announcements2 = await storage.getAnnouncements();
      announcements2.forEach((ann) => {
        if (ann.title.toLowerCase().includes(query) || ann.content.toLowerCase().includes(query)) {
          results.push({
            id: ann.id,
            title: ann.title,
            type: "announcement",
            description: ann.content.substring(0, 100) + "...",
            url: `/announcements/${ann.id}`,
            metadata: ann
          });
        }
      });
      const contents2 = await storage.getContents();
      contents2.forEach((content) => {
        if (content.title.toLowerCase().includes(query) || content.description?.toLowerCase().includes(query)) {
          results.push({
            id: content.id,
            title: content.title,
            type: "content",
            description: content.description,
            url: `/content/${content.id}`,
            metadata: content
          });
        }
      });
      res.json(results.slice(0, 20));
    } catch (error) {
      console.error("Error in global search:", error);
      res.status(500).json({ error: "Failed to perform search" });
    }
  });
  app2.get("/api/dashboard/recent-activity", requireAuth, async (req, res) => {
    try {
      const activities = [];
      const announcements2 = await storage.getAnnouncements();
      announcements2.slice(0, 3).forEach((ann) => {
        activities.push({
          id: ann.id,
          type: "announcement",
          title: `Nouvelle annonce: ${ann.title}`,
          date: ann.createdAt,
          icon: "announcement"
        });
      });
      const messages2 = await storage.getMessages(req.session.userId);
      messages2.slice(0, 2).forEach((msg) => {
        activities.push({
          id: msg.id,
          type: "message",
          title: `Nouveau message: ${msg.subject}`,
          date: msg.createdAt,
          icon: "message"
        });
      });
      activities.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
      res.json(activities.slice(0, 10));
    } catch (error) {
      console.error("Error fetching recent activity:", error);
      res.status(500).json({ error: "Failed to fetch recent activity" });
    }
  });
  app2.get("/api/dashboard/quick-stats", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId;
      const enrollments2 = await storage.getUserEnrollments(userId);
      const completedCourses = enrollments2.filter((e) => e.status === "completed").length;
      const messages2 = await storage.getMessages(userId);
      const unreadMessages = messages2.filter((m) => !m.isRead).length;
      const complaints2 = await storage.getComplaintsByUser(userId);
      const openComplaints = complaints2.filter((c) => c.status === "open").length;
      const stats = {
        completedCourses,
        unreadMessages,
        openComplaints,
        totalEnrollments: enrollments2.length
      };
      res.json(stats);
    } catch (error) {
      console.error("Error fetching quick stats:", error);
      res.status(500).json({ error: "Failed to fetch quick stats" });
    }
  });
  app2.get("/api/dashboard/metrics", requireAuth, async (req, res) => {
    try {
      const allUsers = await storage.getUsers();
      const allAnnouncements = await storage.getAnnouncements();
      const allDocuments = await storage.getDocuments();
      const allTrainings = await storage.getTrainings();
      const metrics = {
        totalUsers: allUsers.length,
        activeUsers: allUsers.filter((u) => u.isActive).length,
        totalAnnouncements: allAnnouncements.length,
        totalDocuments: allDocuments.length,
        totalTrainings: allTrainings.length,
        recentActivity: allAnnouncements.filter(
          (a) => new Date(a.createdAt).getTime() > Date.now() - 7 * 24 * 60 * 60 * 1e3
        ).length
      };
      res.json(metrics);
    } catch (error) {
      console.error("Error fetching metrics:", error);
      res.status(500).json({ error: "Failed to fetch metrics" });
    }
  });
  app2.post("/api/contents/:id/like", requireAuth, async (req, res) => {
    try {
      res.json({ success: true, message: "Content liked" });
    } catch (error) {
      console.error("Error liking content:", error);
      res.status(500).json({ error: "Failed to like content" });
    }
  });
  app2.post("/api/contents/:id/download", requireAuth, async (req, res) => {
    try {
      res.json({ success: true, message: "Download tracked" });
    } catch (error) {
      console.error("Error tracking download:", error);
      res.status(500).json({ error: "Failed to track download" });
    }
  });
  app2.patch("/api/users/:id/activate", requireAuth, requireRole(["admin"]), async (req, res) => {
    try {
      const { id } = req.params;
      const { isActive } = req.body;
      const updatedUser = await storage.updateUser(id, { isActive });
      res.json(updatedUser);
    } catch (error) {
      console.error("Error updating user activation:", error);
      res.status(500).json({ error: "Failed to update user activation" });
    }
  });
  app2.get("/api/courses/featured", requireAuth, async (req, res) => {
    try {
      const courses2 = await storage.getCourses();
      const featured = courses2.filter((course) => course.isFeatured);
      res.json(featured);
    } catch (error) {
      console.error("Error fetching featured courses:", error);
      res.status(500).json({ error: "Failed to fetch featured courses" });
    }
  });
  app2.get("/api/courses/by-category/:category", requireAuth, async (req, res) => {
    try {
      const { category } = req.params;
      const courses2 = await storage.getCourses();
      const filtered = courses2.filter((course) => course.category === category);
      res.json(filtered);
    } catch (error) {
      console.error("Error fetching courses by category:", error);
      res.status(500).json({ error: "Failed to fetch courses by category" });
    }
  });
  app2.get("/api/forum/categories", requireAuth, async (req, res) => {
    try {
      const categories2 = await storage.getForumCategories();
      res.json(categories2);
    } catch (error) {
      console.error("Error fetching forum categories:", error);
      res.status(500).json({ error: "Failed to fetch forum categories" });
    }
  });
  app2.get("/api/forum/topics", requireAuth, async (req, res) => {
    try {
      const { categoryId } = req.query;
      const topics = await storage.getForumTopics(categoryId);
      res.json(topics);
    } catch (error) {
      console.error("Error fetching forum topics:", error);
      res.status(500).json({ error: "Failed to fetch forum topics" });
    }
  });
  app2.get("/api/forum/topics/:id", requireAuth, async (req, res) => {
    try {
      const topic = await storage.getForumTopicById(req.params.id);
      if (!topic) {
        return res.status(404).json({ error: "Topic not found" });
      }
      res.json(topic);
    } catch (error) {
      console.error("Error fetching forum topic:", error);
      res.status(500).json({ error: "Failed to fetch forum topic" });
    }
  });
  app2.get("/api/forum/topics/:id/posts", requireAuth, async (req, res) => {
    try {
      const posts = await storage.getForumPosts(req.params.id);
      res.json(posts);
    } catch (error) {
      console.error("Error fetching forum posts:", error);
      res.status(500).json({ error: "Failed to fetch forum posts" });
    }
  });
  app2.get("/api/my-enrollments", requireAuth, async (req, res) => {
    try {
      const enrollments2 = await storage.getUserEnrollments(req.session.userId);
      res.json(enrollments2);
    } catch (error) {
      console.error("Error fetching user enrollments:", error);
      res.status(500).json({ error: "Failed to fetch user enrollments" });
    }
  });
  app2.get("/api/my-certificates", requireAuth, async (req, res) => {
    try {
      const certificates2 = await storage.getUserCertificates(req.session.userId);
      res.json(certificates2);
    } catch (error) {
      console.error("Error fetching user certificates:", error);
      res.status(500).json({ error: "Failed to fetch user certificates" });
    }
  });
  app2.get("/api/training-recommendations", requireAuth, async (req, res) => {
    try {
      const recommendations = await storage.getTrainingRecommendations(req.session.userId);
      res.json(recommendations);
    } catch (error) {
      console.error("Error fetching training recommendations:", error);
      res.status(500).json({ error: "Failed to fetch training recommendations" });
    }
  });
  app2.get("/api/courses/:id/lessons", requireAuth, async (req, res) => {
    try {
      const lessons2 = await storage.getCourseLessons(req.params.id);
      res.json(lessons2);
    } catch (error) {
      console.error("Error fetching course lessons:", error);
      res.status(500).json({ error: "Failed to fetch course lessons" });
    }
  });
  app2.get("/api/resources", requireAuth, async (req, res) => {
    try {
      const resources2 = await storage.getResources();
      res.json(resources2);
    } catch (error) {
      console.error("Error fetching resources:", error);
      res.status(500).json({ error: "Failed to fetch resources" });
    }
  });
  app2.get("/api/auth/me", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }
      const { password, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Error fetching current user:", error);
      res.status(500).json({ error: "Failed to fetch current user" });
    }
  });
  app2.post("/api/categories", async (req, res) => {
    try {
      const result = insertCategorySchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid category data", errors: result.error.issues });
      }
      const category = await storage.createCategory(result.data);
      res.status(201).json(category);
    } catch (error) {
      console.error("Error creating category:", error);
      res.status(500).json({ error: "Failed to create category" });
    }
  });
  app2.patch("/api/categories/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedCategory = await storage.updateCategory(id, req.body);
      res.json(updatedCategory);
    } catch (error) {
      console.error("Error updating category:", error);
      res.status(500).json({ error: "Failed to update category" });
    }
  });
  app2.delete("/api/categories/:id", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteCategory(id);
      res.json({ message: "Category deleted successfully" });
    } catch (error) {
      console.error("Error deleting category:", error);
      res.status(500).json({ error: "Failed to delete category" });
    }
  });
  app2.get("/api/employee-categories", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const categories2 = await storage.getEmployeeCategories();
      res.json(categories2);
    } catch (error) {
      console.error("Error fetching employee categories:", error);
      res.status(500).json({ error: "Failed to fetch employee categories" });
    }
  });
  app2.post("/api/employee-categories", requireRole(["admin"]), async (req, res) => {
    try {
      const result = insertEmployeeCategorySchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid employee category data", errors: result.error.issues });
      }
      const category = await storage.createEmployeeCategory(result.data);
      res.status(201).json(category);
    } catch (error) {
      console.error("Error creating employee category:", error);
      res.status(500).json({ error: "Failed to create employee category" });
    }
  });
  app2.patch("/api/employee-categories/:id", requireRole(["admin"]), async (req, res) => {
    try {
      const { id } = req.params;
      const updatedCategory = await storage.updateEmployeeCategory(id, req.body);
      res.json(updatedCategory);
    } catch (error) {
      console.error("Error updating employee category:", error);
      res.status(500).json({ error: "Failed to update employee category" });
    }
  });
  app2.delete("/api/employee-categories/:id", requireRole(["admin"]), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteEmployeeCategory(id);
      res.json({ message: "Employee category deleted successfully" });
    } catch (error) {
      console.error("Error deleting employee category:", error);
      res.status(500).json({ error: "Failed to delete employee category" });
    }
  });
  app2.get("/api/system-settings", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const settings = await storage.getSystemSettings();
      res.json(settings);
    } catch (error) {
      console.error("Error fetching system settings:", error);
      res.status(500).json({ error: "Failed to fetch system settings" });
    }
  });
  app2.patch("/api/system-settings", requireRole(["admin"]), async (req, res) => {
    try {
      const result = insertSystemSettingsSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid system settings data", errors: result.error.issues });
      }
      const updatedSettings = await storage.updateSystemSettings(result.data);
      res.json(updatedSettings);
    } catch (error) {
      console.error("Error updating system settings:", error);
      res.status(500).json({ error: "Failed to update system settings" });
    }
  });
  app2.get("/api/trainings", async (req, res) => {
    try {
      const trainings2 = await storage.getTrainings();
      res.json(trainings2);
    } catch (error) {
      console.error("Error fetching trainings:", error);
      res.status(500).json({ error: "Failed to fetch trainings" });
    }
  });
  app2.get("/api/trainings/:id", async (req, res) => {
    try {
      const training = await storage.getTrainingById(req.params.id);
      if (!training) {
        return res.status(404).json({ error: "Training not found" });
      }
      res.json(training);
    } catch (error) {
      console.error("Error fetching training:", error);
      res.status(500).json({ error: "Failed to fetch training" });
    }
  });
  app2.post("/api/trainings", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }
      const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
      if (!hasPermission) {
        return res.status(403).json({ message: "Insufficient permissions to create trainings" });
      }
      const result = insertTrainingSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid training data", errors: result.error.issues });
      }
      const training = await storage.createTraining(result.data);
      res.status(201).json(training);
    } catch (error) {
      console.error("Error creating training:", error);
      res.status(500).json({ error: "Failed to create training" });
    }
  });
  app2.put("/api/trainings/:id", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }
      const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
      if (!hasPermission) {
        return res.status(403).json({ message: "Insufficient permissions to update trainings" });
      }
      const result = insertTrainingSchema.partial().safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid training data", errors: result.error.issues });
      }
      const training = await storage.updateTraining(req.params.id, result.data);
      res.json(training);
    } catch (error) {
      console.error("Error updating training:", error);
      if (error.message === "Training not found") {
        return res.status(404).json({ error: "Training not found" });
      }
      res.status(500).json({ error: "Failed to update training" });
    }
  });
  app2.delete("/api/trainings/:id", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }
      const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
      if (!hasPermission) {
        return res.status(403).json({ message: "Insufficient permissions to delete trainings" });
      }
      await storage.deleteTraining(req.params.id);
      res.json({ message: "Training deleted successfully" });
    } catch (error) {
      console.error("Error deleting training:", error);
      res.status(500).json({ error: "Failed to delete training" });
    }
  });
  app2.get("/api/trainings/:id/participants", async (req, res) => {
    try {
      const participants = await storage.getTrainingParticipants(req.params.id);
      res.json(participants);
    } catch (error) {
      console.error("Error fetching training participants:", error);
      res.status(500).json({ error: "Failed to fetch training participants" });
    }
  });
  app2.post("/api/trainings/:id/participants", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId;
      const trainingId = req.params.id;
      const participant = await storage.addTrainingParticipant({
        trainingId,
        userId,
        status: "registered"
      });
      res.status(201).json(participant);
    } catch (error) {
      console.error("Error adding training participant:", error);
      res.status(500).json({ error: "Failed to register for training" });
    }
  });
  app2.delete("/api/trainings/:id/participants/:userId", requireAuth, async (req, res) => {
    try {
      const currentUserId = req.session.userId;
      const { id: trainingId, userId } = req.params;
      if (currentUserId !== userId) {
        const user = await storage.getUser(currentUserId);
        if (!user) {
          return res.status(401).json({ message: "User not found" });
        }
        const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
        if (!hasPermission) {
          return res.status(403).json({ message: "Insufficient permissions to remove participant" });
        }
      }
      await storage.removeTrainingParticipant(trainingId, userId);
      res.json({ message: "Participant removed successfully" });
    } catch (error) {
      console.error("Error removing training participant:", error);
      res.status(500).json({ error: "Failed to remove participant" });
    }
  });
  app2.get("/api/users/:userId/trainings", requireAuth, async (req, res) => {
    try {
      const currentUserId = req.session.userId;
      const { userId } = req.params;
      if (currentUserId !== userId) {
        const user = await storage.getUser(currentUserId);
        if (!user) {
          return res.status(401).json({ message: "User not found" });
        }
        const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
        if (!hasPermission) {
          return res.status(403).json({ message: "Insufficient permissions to view user trainings" });
        }
      }
      const participations = await storage.getUserTrainingParticipations(userId);
      res.json(participations);
    } catch (error) {
      console.error("Error fetching user trainings:", error);
      res.status(500).json({ error: "Failed to fetch user trainings" });
    }
  });
  app2.post("/api/admin/bulk-permissions", async (req, res) => {
    try {
      const { userId, permissions: permissions2, action } = req.body;
      if (action === "grant") {
        for (const permission of permissions2) {
          await storage.createPermission({
            userId,
            grantedBy: "user-1",
            // Current admin user
            permission
          });
        }
      } else if (action === "revoke") {
        const userPermissions = await storage.getPermissions(userId);
        for (const permission of permissions2) {
          const existingPermission = userPermissions.find((p) => p.permission === permission);
          if (existingPermission) {
            await storage.revokePermission(existingPermission.id);
          }
        }
      }
      res.json({ message: `Permissions ${action}ed successfully` });
    } catch (error) {
      console.error("Error managing bulk permissions:", error);
      res.status(500).json({ error: "Failed to manage permissions" });
    }
  });
  app2.get("/api/views-config", async (req, res) => {
    try {
      const viewsConfig = await storage.getViewsConfig();
      res.json(viewsConfig);
    } catch (error) {
      console.error("Error fetching views config:", error);
      res.status(500).json({ error: "Failed to fetch views configuration" });
    }
  });
  app2.post("/api/views-config", async (req, res) => {
    try {
      const { views } = req.body;
      await storage.saveViewsConfig(views);
      res.json({ message: "Views configuration saved successfully" });
    } catch (error) {
      console.error("Error saving views config:", error);
      res.status(500).json({ error: "Failed to save views configuration" });
    }
  });
  app2.patch("/api/views-config/:viewId", async (req, res) => {
    try {
      const { viewId } = req.params;
      const updates = req.body;
      await storage.updateViewConfig(viewId, updates);
      res.json({ message: "View configuration updated successfully" });
    } catch (error) {
      console.error("Error updating view config:", error);
      res.status(500).json({ error: "Failed to update view configuration" });
    }
  });
  app2.get("/api/user/settings", async (req, res) => {
    try {
      const userSettings = await storage.getUserSettings("user-1");
      res.json(userSettings);
    } catch (error) {
      console.error("Error fetching user settings:", error);
      res.status(500).json({ error: "Failed to fetch user settings" });
    }
  });
  app2.post("/api/user/settings", async (req, res) => {
    try {
      const settings = req.body;
      console.log("Received settings:", JSON.stringify(settings, null, 2));
      if (!settings || typeof settings !== "object") {
        return res.status(400).json({ error: "Invalid settings data" });
      }
      await storage.saveUserSettings("user-1", settings);
      res.json({
        message: "User settings saved successfully",
        timestamp: (/* @__PURE__ */ new Date()).toISOString()
      });
    } catch (error) {
      console.error("Error saving user settings:", error);
      res.status(500).json({
        error: "Failed to save user settings",
        details: error instanceof Error ? error.message : "Unknown error"
      });
    }
  });
  app2.post("/api/admin/reset-test-data", async (req, res) => {
    try {
      await storage.resetToTestData();
      res.json({
        message: "\u2705 Donn\xE9es de test r\xE9initialis\xE9es avec succ\xE8s",
        timestamp: (/* @__PURE__ */ new Date()).toISOString()
      });
    } catch (error) {
      console.error("Error resetting test data:", error);
      res.status(500).json({
        error: "\u274C Erreur lors de la r\xE9initialisation des donn\xE9es de test"
      });
    }
  });
  app2.get("/api/courses", requireAuth, async (req, res) => {
    try {
      const courses2 = await storage.getCourses();
      res.json(courses2);
    } catch (error) {
      console.error("Error fetching courses:", error);
      res.status(500).json({ error: "Failed to fetch courses" });
    }
  });
  app2.get("/api/courses/:id", requireAuth, async (req, res) => {
    try {
      const course = await storage.getCourseById(req.params.id);
      if (!course) {
        return res.status(404).json({ error: "Course not found" });
      }
      res.json(course);
    } catch (error) {
      console.error("Error fetching course:", error);
      res.status(500).json({ error: "Failed to fetch course" });
    }
  });
  app2.post("/api/courses", requireAuth, requireRole(["admin"]), async (req, res) => {
    try {
      const { insertCourseSchema: insertCourseSchema2 } = await Promise.resolve().then(() => (init_schema(), schema_exports));
      const validatedData = insertCourseSchema2.parse(req.body);
      const course = await storage.createCourse(validatedData);
      res.status(201).json(course);
    } catch (error) {
      console.error("Error creating course:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid course data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to create course" });
    }
  });
  app2.get("/api/courses/:courseId/lessons", requireAuth, async (req, res) => {
    try {
      const lessons2 = await storage.getLessons(req.params.courseId);
      res.json(lessons2);
    } catch (error) {
      console.error("Error fetching lessons:", error);
      res.status(500).json({ error: "Failed to fetch lessons" });
    }
  });
  app2.get("/api/lessons/:id", requireAuth, async (req, res) => {
    try {
      const lesson = await storage.getLessonById(req.params.id);
      if (!lesson) {
        return res.status(404).json({ error: "Lesson not found" });
      }
      res.json(lesson);
    } catch (error) {
      console.error("Error fetching lesson:", error);
      res.status(500).json({ error: "Failed to fetch lesson" });
    }
  });
  app2.get("/api/my-enrollments", requireAuth, async (req, res) => {
    try {
      const userId = req.userId;
      const enrollments2 = await storage.getUserEnrollments(userId);
      res.json(enrollments2);
    } catch (error) {
      console.error("Error fetching enrollments:", error);
      res.status(500).json({ error: "Failed to fetch enrollments" });
    }
  });
  app2.post("/api/enroll/:courseId", requireAuth, async (req, res) => {
    try {
      const userId = req.userId;
      const courseId = req.params.courseId;
      const existingEnrollments = await storage.getUserEnrollments(userId);
      const existingEnrollment = existingEnrollments.find((e) => e.courseId === courseId);
      if (existingEnrollment) {
        return res.status(400).json({ error: "Already enrolled in this course" });
      }
      const enrollment = await storage.enrollUser(userId, courseId);
      res.status(201).json(enrollment);
    } catch (error) {
      console.error("Error enrolling user:", error);
      res.status(500).json({ error: "Failed to enroll in course" });
    }
  });
  app2.post("/api/lessons/:lessonId/complete", requireAuth, async (req, res) => {
    try {
      const userId = req.userId;
      const lessonId = req.params.lessonId;
      const { courseId } = req.body;
      const progress = await storage.updateLessonProgress(userId, lessonId, courseId, true);
      res.json(progress);
    } catch (error) {
      console.error("Error updating lesson progress:", error);
      res.status(500).json({ error: "Failed to update lesson progress" });
    }
  });
  app2.get("/api/courses/:courseId/my-progress", requireAuth, async (req, res) => {
    try {
      const userId = req.userId;
      const courseId = req.params.courseId;
      const progress = await storage.getUserLessonProgress(userId, courseId);
      res.json(progress);
    } catch (error) {
      console.error("Error fetching progress:", error);
      res.status(500).json({ error: "Failed to fetch progress" });
    }
  });
  app2.get("/api/resources", requireAuth, async (req, res) => {
    try {
      const resources2 = await storage.getResources();
      res.json(resources2);
    } catch (error) {
      console.error("Error fetching resources:", error);
      res.status(500).json({ error: "Failed to fetch resources" });
    }
  });
  app2.post("/api/resources", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertResourceSchema: insertResourceSchema2 } = await Promise.resolve().then(() => (init_schema(), schema_exports));
      const validatedData = insertResourceSchema2.parse(req.body);
      const resource = await storage.createResource(validatedData);
      res.status(201).json(resource);
    } catch (error) {
      console.error("Error creating resource:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid resource data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to create resource" });
    }
  });
  app2.get("/api/my-certificates", requireAuth, async (req, res) => {
    try {
      const userId = req.userId;
      const certificates2 = await storage.getUserCertificates(userId);
      res.json(certificates2);
    } catch (error) {
      console.error("Error fetching certificates:", error);
      res.status(500).json({ error: "Failed to fetch certificates" });
    }
  });
  app2.post("/api/lessons", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertLessonSchema: insertLessonSchema2 } = await Promise.resolve().then(() => (init_schema(), schema_exports));
      const validatedData = insertLessonSchema2.parse(req.body);
      const lesson = await storage.createLesson(validatedData);
      res.status(201).json(lesson);
    } catch (error) {
      console.error("Error creating lesson:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid lesson data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to create lesson" });
    }
  });
  app2.put("/api/courses/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertCourseSchema: insertCourseSchema2 } = await Promise.resolve().then(() => (init_schema(), schema_exports));
      const validatedData = insertCourseSchema2.parse(req.body);
      const course = await storage.updateCourse(req.params.id, validatedData);
      if (!course) {
        return res.status(404).json({ error: "Course not found" });
      }
      res.json(course);
    } catch (error) {
      console.error("Error updating course:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid course data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to update course" });
    }
  });
  app2.delete("/api/courses/:id", requireAuth, requireRole(["admin"]), async (req, res) => {
    try {
      await storage.deleteCourse(req.params.id);
      res.json({ message: "Course deleted successfully" });
    } catch (error) {
      console.error("Error deleting course:", error);
      res.status(500).json({ error: "Failed to delete course" });
    }
  });
  app2.put("/api/lessons/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertLessonSchema: insertLessonSchema2 } = await Promise.resolve().then(() => (init_schema(), schema_exports));
      const validatedData = insertLessonSchema2.omit({ courseId: true }).parse(req.body);
      const lesson = await storage.updateLesson(req.params.id, validatedData);
      if (!lesson) {
        return res.status(404).json({ error: "Lesson not found" });
      }
      res.json(lesson);
    } catch (error) {
      console.error("Error updating lesson:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid lesson data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to update lesson" });
    }
  });
  app2.delete("/api/lessons/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteLesson(req.params.id);
      res.json({ message: "Lesson deleted successfully" });
    } catch (error) {
      console.error("Error deleting lesson:", error);
      res.status(500).json({ error: "Failed to delete lesson" });
    }
  });
  app2.put("/api/resources/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertResourceSchema: insertResourceSchema2 } = await Promise.resolve().then(() => (init_schema(), schema_exports));
      const validatedData = insertResourceSchema2.parse(req.body);
      const resource = await storage.updateResource(req.params.id, validatedData);
      if (!resource) {
        return res.status(404).json({ error: "Resource not found" });
      }
      res.json(resource);
    } catch (error) {
      console.error("Error updating resource:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid resource data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to update resource" });
    }
  });
  app2.delete("/api/resources/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteResource(req.params.id);
      res.json({ message: "Resource deleted successfully" });
    } catch (error) {
      console.error("Error deleting resource:", error);
      res.status(500).json({ error: "Failed to delete resource" });
    }
  });
  app2.get("/api/training-analytics", requireAuth, async (req, res) => {
    try {
      const userId = req.query.userId || req.user.id;
      const enrollments2 = await storage.getUserEnrollments(userId);
      const completedCourses = enrollments2.filter((e) => e.status === "completed").length;
      const totalEnrolled = enrollments2.length;
      const totalHours = enrollments2.reduce((sum, e) => sum + (e.timeSpent || 0), 0);
      const averageScore = enrollments2.length > 0 ? enrollments2.reduce((sum, e) => sum + (e.score || 0), 0) / enrollments2.length : 0;
      const analytics = {
        totalHours: Math.round(totalHours),
        weeklyHours: Math.round(totalHours * 0.2),
        // Approximate
        completedCourses,
        totalEnrolled,
        averageScore: Math.round(averageScore),
        scoreImprovement: 5,
        // Would calculate from historical data
        certificates: completedCourses,
        recentCertificates: Math.min(completedCourses, 2),
        completionByCategory: [
          { category: "Technical", completion: 85, enrolled: 6, completed: 5 },
          { category: "Leadership", completion: 70, enrolled: 4, completed: 3 },
          { category: "Compliance", completion: 95, enrolled: 2, completed: 2 },
          { category: "Soft Skills", completion: 60, enrolled: 3, completed: 2 }
        ]
      };
      res.json(analytics);
    } catch (error) {
      console.error("Error fetching training analytics:", error);
      res.status(500).json({ error: "Failed to fetch training analytics" });
    }
  });
  app2.get("/api/training-progress", requireAuth, async (req, res) => {
    try {
      const userId = req.query.userId || req.user.id;
      const progress = {
        weeklyProgress: [
          { week: "Week 1", hours: 12, courses: 2, score: 85 },
          { week: "Week 2", hours: 15, courses: 3, score: 88 },
          { week: "Week 3", hours: 10, courses: 1, score: 92 },
          { week: "Week 4", hours: 18, courses: 4, score: 89 }
        ],
        monthlyTrend: [
          { month: "Jan", completed: 3, hours: 25 },
          { month: "Feb", completed: 4, hours: 32 },
          { month: "Mar", completed: 2, hours: 18 },
          { month: "Apr", completed: 5, hours: 45 }
        ]
      };
      res.json(progress);
    } catch (error) {
      console.error("Error fetching training progress:", error);
      res.status(500).json({ error: "Failed to fetch training progress" });
    }
  });
  app2.get("/api/my-certificates", requireAuth, async (req, res) => {
    try {
      const userId = req.user.id;
      const enrollments2 = await storage.getUserEnrollments(userId);
      const certificates2 = enrollments2.filter((e) => e.status === "completed").map((e) => ({
        id: e.id,
        courseId: e.courseId,
        courseTitle: e.courseTitle || "Course",
        completedAt: e.completedAt,
        score: e.score || 0,
        certificateUrl: `/certificates/${e.id}.pdf`
      }));
      res.json(certificates2);
    } catch (error) {
      console.error("Error fetching certificates:", error);
      res.status(500).json({ error: "Failed to fetch certificates" });
    }
  });
  app2.get("/api/training-participants", requireAuth, requireRole(["admin", "training_manager"]), async (req, res) => {
    try {
      const participants = await storage.getAllTrainingParticipants();
      res.json(participants);
    } catch (error) {
      console.error("Error fetching training participants:", error);
      res.status(500).json({ error: "Failed to fetch training participants" });
    }
  });
  app2.get("/api/training-recommendations", requireAuth, async (req, res) => {
    try {
      const userId = req.user.id;
      const enrollments2 = await storage.getUserEnrollments(userId);
      const allCourses = await storage.getCourses();
      const enrolledCourseIds = enrollments2.map((e) => e.courseId);
      const recommendations = allCourses.filter((course) => !enrolledCourseIds.includes(course.id)).slice(0, 6).map((course) => ({
        ...course,
        reason: "Based on your learning history"
      }));
      res.json(recommendations);
    } catch (error) {
      console.error("Error fetching training recommendations:", error);
      res.status(500).json({ error: "Failed to fetch training recommendations" });
    }
  });
  app2.get("/api/training-leaderboard", requireAuth, async (req, res) => {
    try {
      const leaderboard = [
        { rank: 1, name: req.user.firstName + " " + req.user.lastName, hours: 45, courses: 12, userId: req.user.id },
        { rank: 2, name: "Marie Martin", hours: 42, courses: 11, userId: "user2" },
        { rank: 3, name: "Pierre Dubois", hours: 38, courses: 10, userId: "user3" },
        { rank: 4, name: "Sophie Chen", hours: 35, courses: 9, userId: "user4" },
        { rank: 5, name: "Lucas Bernard", hours: 33, courses: 8, userId: "user5" }
      ];
      res.json(leaderboard);
    } catch (error) {
      console.error("Error fetching training leaderboard:", error);
      res.status(500).json({ error: "Failed to fetch training leaderboard" });
    }
  });
  app2.get("/api/training-trends", requireAuth, async (req, res) => {
    try {
      const trends = {
        popularCourses: [
          { courseId: "course1", title: "JavaScript Fundamentals", enrollments: 45 },
          { courseId: "course2", title: "Leadership Skills", enrollments: 38 },
          { courseId: "course3", title: "Data Analysis", enrollments: 32 }
        ],
        emergingSkills: [
          { skill: "AI/ML", growth: "+85%" },
          { skill: "Cybersecurity", growth: "+67%" },
          { skill: "Cloud Computing", growth: "+54%" }
        ]
      };
      res.json(trends);
    } catch (error) {
      console.error("Error fetching training trends:", error);
      res.status(500).json({ error: "Failed to fetch training trends" });
    }
  });
  app2.post("/api/courses/:courseId/lessons/:lessonId/complete", requireAuth, async (req, res) => {
    try {
      const { courseId, lessonId } = req.params;
      const userId = req.user.id;
      await storage.markLessonComplete(userId, courseId, lessonId);
      res.json({ message: "Lesson marked as complete" });
    } catch (error) {
      console.error("Error marking lesson complete:", error);
      res.status(500).json({ error: "Failed to mark lesson complete" });
    }
  });
  app2.get("/api/search/global", requireAuth, async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== "string" || q.length < 2) {
        return res.status(400).json({ error: "Query must be at least 2 characters" });
      }
      const [users2, documents2, announcements2, contents2] = await Promise.all([
        storage.searchUsers(q).catch(() => []),
        storage.searchDocuments(q).catch(() => []),
        storage.searchAnnouncements(q).catch(() => []),
        storage.searchContent(q).catch(() => [])
      ]);
      const results = {
        users: users2.slice(0, 5).map((user) => {
          const { password, ...safeUser } = user;
          return safeUser;
        }),
        documents: documents2.slice(0, 5),
        announcements: announcements2.slice(0, 5),
        contents: contents2.slice(0, 5),
        total: users2.length + documents2.length + announcements2.length + contents2.length
      };
      res.json(results);
    } catch (error) {
      console.error("Error in global search:", error);
      res.status(500).json({ error: "Failed to perform global search" });
    }
  });
  app2.get("/api/search/users", requireAuth, async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== "string") {
        return res.status(400).json({ error: "Query parameter 'q' is required" });
      }
      const users2 = await storage.searchUsers(q);
      const safeUsers = users2.map((user) => {
        const { password, ...safeUser } = user;
        return safeUser;
      });
      res.json(safeUsers);
    } catch (error) {
      console.error("Error searching users:", error);
      res.status(500).json({ error: "Failed to search users" });
    }
  });
  app2.get("/api/search/documents", requireAuth, async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== "string") {
        return res.status(400).json({ error: "Query parameter 'q' is required" });
      }
      const documents2 = await storage.searchDocuments(q).catch(() => []);
      res.json(documents2);
    } catch (error) {
      console.error("Error searching documents:", error);
      res.status(500).json({ error: "Failed to search documents" });
    }
  });
  app2.get("/api/search/announcements", requireAuth, async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== "string") {
        return res.status(400).json({ error: "Query parameter 'q' is required" });
      }
      const announcements2 = await storage.searchAnnouncements(q).catch(() => []);
      res.json(announcements2);
    } catch (error) {
      console.error("Error searching announcements:", error);
      res.status(500).json({ error: "Failed to search announcements" });
    }
  });
  app2.get("/api/search/content", requireAuth, async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== "string") {
        return res.status(400).json({ error: "Query parameter 'q' is required" });
      }
      const contents2 = await storage.searchContent(q).catch(() => []);
      res.json(contents2);
    } catch (error) {
      console.error("Error searching content:", error);
      res.status(500).json({ error: "Failed to search content" });
    }
  });
  app2.get("/api/dashboard/activity", requireAuth, async (req, res) => {
    try {
      const weeklyActivity = [
        { day: "Lun", users: 24, documents: 12, messages: 45 },
        { day: "Mar", users: 28, documents: 15, messages: 52 },
        { day: "Mer", users: 32, documents: 18, messages: 48 },
        { day: "Jeu", users: 35, documents: 22, messages: 65 },
        { day: "Ven", users: 42, documents: 28, messages: 78 },
        { day: "Sam", users: 18, documents: 8, messages: 32 },
        { day: "Dim", users: 15, documents: 5, messages: 28 }
      ];
      res.json({ weeklyActivity });
    } catch (error) {
      console.error("Error fetching dashboard activity:", error);
      res.status(500).json({ error: "Failed to fetch activity data" });
    }
  });
  app2.get("/api/dashboard/analytics", requireAuth, async (req, res) => {
    try {
      const analytics = {
        totalUsers: 156,
        activeUsers: 89,
        contentEngagement: 78,
        trainingCompletion: 65
      };
      res.json(analytics);
    } catch (error) {
      console.error("Error fetching analytics:", error);
      res.status(500).json({ error: "Failed to fetch analytics" });
    }
  });
  app2.get("/api/dashboard/top-content", requireAuth, async (req, res) => {
    try {
      const engagement = [
        { name: "Formation React", views: 245, downloads: 89, rating: 4.8 },
        { name: "Guide S\xE9curit\xE9", views: 189, downloads: 67, rating: 4.6 },
        { name: "Proc. Qualit\xE9", views: 156, downloads: 54, rating: 4.5 },
        { name: "Manuel Onboarding", views: 134, downloads: 45, rating: 4.7 }
      ];
      res.json({ engagement });
    } catch (error) {
      console.error("Error fetching top content:", error);
      res.status(500).json({ error: "Failed to fetch top content" });
    }
  });
  app2.get("/api/forum/categories", requireAuth, async (req, res) => {
    try {
      const categories2 = await storage.getForumCategories();
      res.json(categories2);
    } catch (error) {
      console.error("Error fetching forum categories:", error);
      res.status(500).json({ error: "Failed to fetch forum categories" });
    }
  });
  app2.get("/api/forum/categories/:id", requireAuth, async (req, res) => {
    try {
      const category = await storage.getForumCategoryById(req.params.id);
      if (!category) {
        return res.status(404).json({ error: "Forum category not found" });
      }
      res.json(category);
    } catch (error) {
      console.error("Error fetching forum category:", error);
      res.status(500).json({ error: "Failed to fetch forum category" });
    }
  });
  app2.post("/api/forum/categories", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const validatedData = insertForumCategorySchema.parse(req.body);
      const category = await storage.createForumCategory(validatedData);
      res.status(201).json(category);
    } catch (error) {
      console.error("Error creating forum category:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid category data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to create forum category" });
    }
  });
  app2.put("/api/forum/categories/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const category = await storage.updateForumCategory(req.params.id, req.body);
      res.json(category);
    } catch (error) {
      console.error("Error updating forum category:", error);
      res.status(500).json({ error: "Failed to update forum category" });
    }
  });
  app2.delete("/api/forum/categories/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteForumCategory(req.params.id);
      res.json({ message: "Forum category deleted successfully" });
    } catch (error) {
      console.error("Error deleting forum category:", error);
      res.status(500).json({ error: "Failed to delete forum category" });
    }
  });
  app2.get("/api/forum/topics", requireAuth, async (req, res) => {
    try {
      const { categoryId } = req.query;
      const topics = await storage.getForumTopics(categoryId);
      res.json(topics);
    } catch (error) {
      console.error("Error fetching forum topics:", error);
      res.status(500).json({ error: "Failed to fetch forum topics" });
    }
  });
  app2.get("/api/forum/topics/:id", requireAuth, async (req, res) => {
    try {
      const topic = await storage.getForumTopicById(req.params.id);
      if (!topic) {
        return res.status(404).json({ error: "Forum topic not found" });
      }
      await storage.incrementTopicViews(req.params.id);
      res.json(topic);
    } catch (error) {
      console.error("Error fetching forum topic:", error);
      res.status(500).json({ error: "Failed to fetch forum topic" });
    }
  });
  app2.post("/api/forum/topics", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }
      const validatedData = insertForumTopicSchema.parse({
        ...req.body,
        authorId: user.id,
        authorName: user.name
      });
      const topic = await storage.createForumTopic(validatedData);
      res.status(201).json(topic);
    } catch (error) {
      console.error("Error creating forum topic:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid topic data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to create forum topic" });
    }
  });
  app2.put("/api/forum/topics/:id", requireAuth, async (req, res) => {
    try {
      const topic = await storage.getForumTopicById(req.params.id);
      if (!topic) {
        return res.status(404).json({ error: "Forum topic not found" });
      }
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }
      if (topic.authorId !== user.id && !["admin", "moderator"].includes(user.role)) {
        return res.status(403).json({ error: "Not authorized to edit this topic" });
      }
      const updatedTopic = await storage.updateForumTopic(req.params.id, req.body);
      res.json(updatedTopic);
    } catch (error) {
      console.error("Error updating forum topic:", error);
      res.status(500).json({ error: "Failed to update forum topic" });
    }
  });
  app2.delete("/api/forum/topics/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteForumTopic(req.params.id);
      res.json({ message: "Forum topic deleted successfully" });
    } catch (error) {
      console.error("Error deleting forum topic:", error);
      res.status(500).json({ error: "Failed to delete forum topic" });
    }
  });
  app2.get("/api/forum/topics/:topicId/posts", requireAuth, async (req, res) => {
    try {
      const posts = await storage.getForumPosts(req.params.topicId);
      res.json(posts);
    } catch (error) {
      console.error("Error fetching forum posts:", error);
      res.status(500).json({ error: "Failed to fetch forum posts" });
    }
  });
  app2.get("/api/forum/posts/:id", requireAuth, async (req, res) => {
    try {
      const post = await storage.getForumPostById(req.params.id);
      if (!post) {
        return res.status(404).json({ error: "Forum post not found" });
      }
      res.json(post);
    } catch (error) {
      console.error("Error fetching forum post:", error);
      res.status(500).json({ error: "Failed to fetch forum post" });
    }
  });
  app2.post("/api/forum/posts", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }
      const validatedData = insertForumPostSchema.parse({
        ...req.body,
        authorId: user.id,
        authorName: user.name
      });
      const post = await storage.createForumPost(validatedData);
      res.status(201).json(post);
    } catch (error) {
      console.error("Error creating forum post:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid post data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to create forum post" });
    }
  });
  app2.put("/api/forum/posts/:id", requireAuth, async (req, res) => {
    try {
      const post = await storage.getForumPostById(req.params.id);
      if (!post) {
        return res.status(404).json({ error: "Forum post not found" });
      }
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }
      if (post.authorId !== user.id && !["admin", "moderator"].includes(user.role)) {
        return res.status(403).json({ error: "Not authorized to edit this post" });
      }
      const updatedPost = await storage.updateForumPost(req.params.id, {
        ...req.body,
        editedBy: user.id
      });
      res.json(updatedPost);
    } catch (error) {
      console.error("Error updating forum post:", error);
      res.status(500).json({ error: "Failed to update forum post" });
    }
  });
  app2.delete("/api/forum/posts/:id", requireAuth, async (req, res) => {
    try {
      const post = await storage.getForumPostById(req.params.id);
      if (!post) {
        return res.status(404).json({ error: "Forum post not found" });
      }
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }
      if (post.authorId !== user.id && !["admin", "moderator"].includes(user.role)) {
        return res.status(403).json({ error: "Not authorized to delete this post" });
      }
      await storage.deleteForumPost(req.params.id, user.id);
      res.json({ message: "Forum post deleted successfully" });
    } catch (error) {
      console.error("Error deleting forum post:", error);
      res.status(500).json({ error: "Failed to delete forum post" });
    }
  });
  app2.get("/api/forum/posts/:postId/likes", requireAuth, async (req, res) => {
    try {
      const likes = await storage.getForumPostLikes(req.params.postId);
      res.json(likes);
    } catch (error) {
      console.error("Error fetching forum post likes:", error);
      res.status(500).json({ error: "Failed to fetch forum post likes" });
    }
  });
  app2.post("/api/forum/posts/:postId/like", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }
      const likeData = insertForumLikeSchema.parse({
        postId: req.params.postId,
        userId: user.id,
        userName: user.name,
        reactionType: req.body.reactionType || "like"
      });
      const result = await storage.toggleForumPostLike(likeData);
      if (result) {
        res.status(201).json({ action: "liked", like: result });
      } else {
        res.json({ action: "unliked" });
      }
    } catch (error) {
      console.error("Error toggling forum post like:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid like data", details: error.errors });
      }
      res.status(500).json({ error: "Failed to toggle forum post like" });
    }
  });
  app2.get("/api/forum/users/:userId/stats", requireAuth, async (req, res) => {
    try {
      const stats = await storage.getForumUserStats(req.params.userId);
      if (!stats) {
        return res.status(404).json({ error: "Forum user stats not found" });
      }
      res.json(stats);
    } catch (error) {
      console.error("Error fetching forum user stats:", error);
      res.status(500).json({ error: "Failed to fetch forum user stats" });
    }
  });
  app2.get("/api/forum/stats/me", requireAuth, async (req, res) => {
    try {
      const stats = await storage.getForumUserStats(req.session.userId);
      if (!stats) {
        return res.status(404).json({ error: "Forum user stats not found" });
      }
      res.json(stats);
    } catch (error) {
      console.error("Error fetching forum user stats:", error);
      res.status(500).json({ error: "Failed to fetch forum user stats" });
    }
  });
  const httpServer = createServer(app2);
  return httpServer;
}

// server/vite.ts
import express from "express";
import fs from "fs";
import path2 from "path";
import { createServer as createViteServer, createLogger } from "vite";

// vite.config.ts
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path from "path";
import runtimeErrorOverlay from "@replit/vite-plugin-runtime-error-modal";
var vite_config_default = defineConfig({
  plugins: [
    react(),
    runtimeErrorOverlay(),
    ...process.env.NODE_ENV !== "production" && process.env.REPL_ID !== void 0 ? [
      await import("@replit/vite-plugin-cartographer").then(
        (m) => m.cartographer()
      )
    ] : []
  ],
  resolve: {
    alias: {
      "@": path.resolve(import.meta.dirname, "client", "src"),
      "@shared": path.resolve(import.meta.dirname, "shared"),
      "@assets": path.resolve(import.meta.dirname, "attached_assets")
    }
  },
  root: path.resolve(import.meta.dirname, "client"),
  build: {
    outDir: path.resolve(import.meta.dirname, "dist/public"),
    emptyOutDir: true
  },
  server: {
    fs: {
      strict: true,
      deny: ["**/.*"]
    }
  }
});

// server/vite.ts
import { nanoid } from "nanoid";
var viteLogger = createLogger();
function log(message, source = "express") {
  const formattedTime = (/* @__PURE__ */ new Date()).toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
    second: "2-digit",
    hour12: true
  });
  console.log(`${formattedTime} [${source}] ${message}`);
}
async function setupVite(app2, server) {
  const serverOptions = {
    middlewareMode: true,
    hmr: { server },
    allowedHosts: true
  };
  const vite = await createViteServer({
    ...vite_config_default,
    configFile: false,
    customLogger: {
      ...viteLogger,
      error: (msg, options) => {
        viteLogger.error(msg, options);
        process.exit(1);
      }
    },
    server: serverOptions,
    appType: "custom"
  });
  app2.use(vite.middlewares);
  app2.use("*", async (req, res, next) => {
    const url = req.originalUrl;
    try {
      const clientTemplate = path2.resolve(
        import.meta.dirname,
        "..",
        "client",
        "index.html"
      );
      let template = await fs.promises.readFile(clientTemplate, "utf-8");
      template = template.replace(
        `src="/src/main.tsx"`,
        `src="/src/main.tsx?v=${nanoid()}"`
      );
      const page = await vite.transformIndexHtml(url, template);
      res.status(200).set({ "Content-Type": "text/html" }).end(page);
    } catch (e) {
      vite.ssrFixStacktrace(e);
      next(e);
    }
  });
}
function serveStatic(app2) {
  const distPath = path2.resolve(import.meta.dirname, "public");
  if (!fs.existsSync(distPath)) {
    throw new Error(
      `Could not find the build directory: ${distPath}, make sure to build the client first`
    );
  }
  app2.use(express.static(distPath));
  app2.use("*", (_req, res) => {
    res.sendFile(path2.resolve(distPath, "index.html"));
  });
}

// server/middleware/security.ts
import { rateLimit } from "express-rate-limit";
import helmet from "helmet";
function configureSecurity(app2) {
  app2.use(helmet({
    contentSecurityPolicy: {
      directives: {
        defaultSrc: ["'self'"],
        styleSrc: ["'self'", "'unsafe-inline'", "https://fonts.googleapis.com"],
        fontSrc: ["'self'", "https://fonts.gstatic.com"],
        imgSrc: ["'self'", "data:", "https:"],
        scriptSrc: ["'self'", "'unsafe-inline'", "'unsafe-eval'"]
        // Required for Vite dev
      }
    },
    crossOriginEmbedderPolicy: false
  }));
  const authLimiter = rateLimit({
    windowMs: 15 * 60 * 1e3,
    // 15 minutes
    max: 5,
    // Limit each IP to 5 requests per windowMs
    message: {
      error: "Too many authentication attempts. Please try again later."
    },
    standardHeaders: true,
    legacyHeaders: false
  });
  app2.use("/api/auth/login", authLimiter);
  app2.use("/api/auth/register", authLimiter);
  const apiLimiter = rateLimit({
    windowMs: 15 * 60 * 1e3,
    // 15 minutes
    max: 100,
    // Limit each IP to 100 requests per windowMs
    message: {
      error: "Too many API requests. Please try again later."
    },
    standardHeaders: true,
    legacyHeaders: false
  });
  app2.use("/api", apiLimiter);
}
function sanitizeInput(req, res, next) {
  if (req.body && typeof req.body === "object") {
    req.body = sanitizeObject(req.body);
  }
  next();
}
function sanitizeObject(obj) {
  if (typeof obj !== "object" || obj === null) {
    return obj;
  }
  if (Array.isArray(obj)) {
    return obj.map(sanitizeObject);
  }
  const sanitized = {};
  for (const [key, value] of Object.entries(obj)) {
    if (typeof value === "string") {
      sanitized[key] = value.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "").replace(/javascript:/gi, "").replace(/on\w+\s*=/gi, "");
    } else if (typeof value === "object") {
      sanitized[key] = sanitizeObject(value);
    } else {
      sanitized[key] = value;
    }
  }
  return sanitized;
}
function getSessionConfig() {
  return {
    secret: process.env.SESSION_SECRET || "intrasphere-dev-secret-change-in-production",
    resave: false,
    saveUninitialized: false,
    rolling: true,
    cookie: {
      secure: process.env.NODE_ENV === "production",
      httpOnly: true,
      maxAge: 1e3 * 60 * 60 * 24,
      // 24 hours
      sameSite: "strict"
    }
  };
}

// server/migrations.ts
async function migratePasswordsToHash() {
  console.log("Starting password migration...");
  try {
    const users2 = await storage.getUsers();
    for (const user of users2) {
      if (!user.password.startsWith("$2b$")) {
        console.log(`Migrating password for user: ${user.username}`);
        const hashedPassword = await AuthService.hashPassword(user.password);
        await storage.updateUser(user.id, { password: hashedPassword });
        console.log(`\u2705 Password migrated for user: ${user.username}`);
      } else {
        console.log(`\u23ED\uFE0F  Password already hashed for user: ${user.username}`);
      }
    }
    console.log("\u2705 Password migration completed successfully");
  } catch (error) {
    console.error("\u274C Password migration failed:", error);
    throw error;
  }
}
async function ensureDefaultAdmin() {
  try {
    const users2 = await storage.getUsers();
    const adminExists = users2.some((user) => user.role === "admin");
    if (!adminExists) {
      console.log("Creating default admin user...");
      const hashedPassword = await AuthService.hashPassword("admin123!");
      const adminUser = {
        username: "admin",
        password: hashedPassword,
        name: "Administrateur",
        role: "admin",
        email: "admin@intrasphere.com",
        isActive: true,
        employeeId: "ADMIN001",
        department: "Direction",
        position: "Administrateur syst\xE8me"
      };
      await storage.createUser(adminUser);
      console.log("\u2705 Default admin user created");
      console.log("   Username: admin");
      console.log("   Password: admin123!");
      console.log("   \u26A0\uFE0F  Please change the default password after first login");
    }
  } catch (error) {
    console.error("\u274C Failed to create default admin:", error);
  }
}
async function runMigrations() {
  console.log("\u{1F504} Running database migrations...");
  await migratePasswordsToHash();
  await ensureDefaultAdmin();
  console.log("\u2705 All migrations completed");
}

// server/index.ts
var app = express2();
if (process.env.NODE_ENV === "production") {
  app.set("trust proxy", true);
}
configureSecurity(app);
app.use(express2.json({ limit: "50mb" }));
app.use(express2.urlencoded({ extended: false, limit: "50mb" }));
app.use(sanitizeInput);
app.use(session(getSessionConfig()));
app.use((req, res, next) => {
  const start = Date.now();
  const path3 = req.path;
  let capturedJsonResponse = void 0;
  const originalResJson = res.json;
  res.json = function(bodyJson, ...args) {
    capturedJsonResponse = bodyJson;
    return originalResJson.apply(res, [bodyJson, ...args]);
  };
  res.on("finish", () => {
    const duration = Date.now() - start;
    if (path3.startsWith("/api")) {
      let logLine = `${req.method} ${path3} ${res.statusCode} in ${duration}ms`;
      if (capturedJsonResponse) {
        logLine += ` :: ${JSON.stringify(capturedJsonResponse)}`;
      }
      if (logLine.length > 80) {
        logLine = logLine.slice(0, 79) + "\u2026";
      }
      log(logLine);
    }
  });
  next();
});
process.on("uncaughtException", (error) => {
  console.error("Uncaught Exception:", error.message);
  if (error.message.includes("Vite") || error.message.includes("vite")) {
    console.log("Vite error detected, continuing server operation...");
    return;
  }
  console.error("Non-Vite error, stack:", error.stack);
});
process.on("unhandledRejection", (reason, promise) => {
  console.error("Unhandled Rejection at:", promise, "reason:", reason);
  if (typeof reason === "string" && (reason.includes("Vite") || reason.includes("vite"))) {
    console.log("Vite rejection detected, continuing server operation...");
    return;
  }
});
(async () => {
  await runMigrations();
  const server = await registerRoutes(app);
  app.use((err, _req, res, _next) => {
    const status = err.status || err.statusCode || 500;
    const message = err.message || "Internal Server Error";
    res.status(status).json({ message });
    console.error("Server error:", err.message);
    if (err.stack) {
      console.error("Stack trace:", err.stack);
    }
  });
  if (app.get("env") === "development") {
    await setupVite(app, server);
  } else {
    serveStatic(app);
  }
  const port = parseInt(process.env.PORT || "5000", 10);
  const httpServer = server.listen({
    port,
    host: "0.0.0.0",
    reusePort: true
  }, () => {
    log(`serving on port ${port}`);
  });
  try {
    const { initializeWebSocket: initializeWebSocket2 } = await Promise.resolve().then(() => (init_websocket(), websocket_exports));
    initializeWebSocket2(httpServer);
  } catch (error) {
    log(`WebSocket initialization skipped: ${error?.message || "Unknown error"}`);
  }
})();
