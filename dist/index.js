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
  enrollments: () => enrollments,
  events: () => events,
  insertAnnouncementSchema: () => insertAnnouncementSchema,
  insertCategorySchema: () => insertCategorySchema,
  insertComplaintSchema: () => insertComplaintSchema,
  insertContentSchema: () => insertContentSchema,
  insertCourseSchema: () => insertCourseSchema,
  insertDocumentSchema: () => insertDocumentSchema,
  insertEventSchema: () => insertEventSchema,
  insertLessonSchema: () => insertLessonSchema,
  insertMessageSchema: () => insertMessageSchema,
  insertPermissionSchema: () => insertPermissionSchema,
  insertQuizSchema: () => insertQuizSchema,
  insertResourceSchema: () => insertResourceSchema,
  insertUserSchema: () => insertUserSchema,
  lessonProgress: () => lessonProgress,
  lessons: () => lessons,
  messages: () => messages,
  permissions: () => permissions,
  quizAttempts: () => quizAttempts,
  quizzes: () => quizzes,
  resources: () => resources,
  users: () => users
});
import { sql } from "drizzle-orm";
import { pgTable, text, varchar, timestamp, boolean, integer, real } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";
var users, announcements, documents, events, messages, complaints, permissions, insertUserSchema, insertAnnouncementSchema, insertDocumentSchema, insertEventSchema, insertMessageSchema, insertComplaintSchema, contents, categories, insertPermissionSchema, insertContentSchema, insertCategorySchema, courses, lessons, quizzes, enrollments, lessonProgress, quizAttempts, certificates, resources, insertCourseSchema, insertLessonSchema, insertQuizSchema, insertResourceSchema;
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
      // manage_announcements, manage_documents, manage_events, manage_users
      createdAt: timestamp("created_at").defaultNow()
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
      certificateUrl: text("certificate_url")
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
  }
});

// server/index.ts
import express2 from "express";
import session from "express-session";

// server/routes.ts
import { createServer } from "http";

// server/storage.ts
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
  // E-learning storage
  courses;
  lessons;
  quizzes;
  enrollments;
  lessonProgress;
  quizAttempts;
  certificates;
  resources;
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
    this.courses = /* @__PURE__ */ new Map();
    this.lessons = /* @__PURE__ */ new Map();
    this.quizzes = /* @__PURE__ */ new Map();
    this.enrollments = /* @__PURE__ */ new Map();
    this.lessonProgress = /* @__PURE__ */ new Map();
    this.quizAttempts = /* @__PURE__ */ new Map();
    this.certificates = /* @__PURE__ */ new Map();
    this.resources = /* @__PURE__ */ new Map();
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
    defaultUsers.forEach((user) => this.users.set(user.id, user));
    sampleAnnouncements.forEach((ann) => this.announcements.set(ann.id, ann));
    sampleDocuments.forEach((doc) => this.documents.set(doc.id, doc));
    sampleEvents.forEach((event) => this.events.set(event.id, event));
    sampleMessages.forEach((msg) => this.messages.set(msg.id, msg));
    sampleComplaints.forEach((complaint) => this.complaints.set(complaint.id, complaint));
    sampleCategories.forEach((cat) => this.categories.set(cat.id, cat));
    sampleContent.forEach((content) => this.contents.set(content.id, content));
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
  // Views Configuration Management
  viewsConfig = /* @__PURE__ */ new Map();
  async getViewsConfig() {
    if (this.viewsConfig.size === 0) {
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
        this.viewsConfig.set(view.id, view);
      });
    }
    return Array.from(this.viewsConfig.values()).sort((a, b) => a.sortOrder - b.sortOrder);
  }
  async saveViewsConfig(views) {
    this.viewsConfig.clear();
    views.forEach((view) => {
      this.viewsConfig.set(view.id, view);
    });
  }
  async updateViewConfig(viewId, updates) {
    const existingView = this.viewsConfig.get(viewId);
    if (existingView) {
      this.viewsConfig.set(viewId, { ...existingView, ...updates });
    }
  }
  // User Settings Management
  userSettings = /* @__PURE__ */ new Map();
  async getUserSettings(userId) {
    const settings = this.userSettings.get(userId);
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
      this.userSettings.set(userId, defaultSettings);
      return defaultSettings;
    }
    return settings;
  }
  async saveUserSettings(userId, settings) {
    try {
      if (!settings || typeof settings !== "object") {
        throw new Error("Invalid settings object");
      }
      this.userSettings.set(userId, { ...settings, updatedAt: (/* @__PURE__ */ new Date()).toISOString() });
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
      certificateUrl: null
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
};
var storage = new MemStorage();

// server/routes.ts
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
      if (!user || user.password !== password) {
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
      const newUser = await storage.createUser({
        ...result.data,
        role: "employee"
      });
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

// server/index.ts
var app = express2();
app.use(express2.json({ limit: "50mb" }));
app.use(express2.urlencoded({ extended: false, limit: "50mb" }));
app.use(session({
  secret: "intrasphere-secret-key-change-in-production",
  resave: false,
  saveUninitialized: false,
  cookie: {
    secure: false,
    // Set to true in production with HTTPS
    httpOnly: true,
    maxAge: 24 * 60 * 60 * 1e3
    // 24 hours
  }
}));
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
(async () => {
  const server = await registerRoutes(app);
  app.use((err, _req, res, _next) => {
    const status = err.status || err.statusCode || 500;
    const message = err.message || "Internal Server Error";
    res.status(status).json({ message });
    throw err;
  });
  if (app.get("env") === "development") {
    await setupVite(app, server);
  } else {
    serveStatic(app);
  }
  const port = parseInt(process.env.PORT || "5000", 10);
  server.listen({
    port,
    host: "0.0.0.0",
    reusePort: true
  }, () => {
    log(`serving on port ${port}`);
  });
})();
