import type { 
  User, 
  Announcement, 
  Document, 
  Event, 
  Message, 
  Complaint 
} from "@shared/schema";

// Test users with different roles
export const testUsers: User[] = [
  {
    id: "admin-1",
    username: "admin",
    password: "Admin123!",
    name: "Marie Dupont",
    email: "marie.dupont@intrasphere.fr",
    department: "Direction",
    position: "Directrice Générale",
    phone: "01 23 45 67 89",
    role: "admin",
    employeeId: "EMP001",
    avatar: null,
    isActive: true,
    createdAt: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000)
  },
  {
    id: "mod-1",
    username: "moderator",
    password: "Mod123!",
    name: "Pierre Martin",
    email: "pierre.martin@intrasphere.fr",
    department: "Ressources Humaines",
    position: "Responsable RH",
    phone: "01 23 45 67 90",
    role: "moderator",
    employeeId: "EMP002",
    avatar: null,
    isActive: true,
    createdAt: new Date(Date.now() - 25 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000)
  },
  {
    id: "emp-1",
    username: "employee",
    password: "Emp123!",
    name: "Sophie Bernard",
    email: "sophie.bernard@intrasphere.fr",
    department: "Informatique",
    position: "Développeuse",
    phone: "01 23 45 67 91",
    role: "employee",
    employeeId: "EMP003",
    avatar: null,
    isActive: true,
    createdAt: new Date(Date.now() - 20 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000)
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
    createdAt: new Date(Date.now() - 15 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 4 * 24 * 60 * 60 * 1000)
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
    createdAt: new Date(Date.now() - 10 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000)
  },
  {
    id: "emp-4",
    username: "lrobert",
    password: "password123",
    name: "Lucas Robert",
    email: "lucas.robert@intrasphere.fr",
    department: "Comptabilité",
    position: "Comptable",
    phone: "01 23 45 67 94",
    role: "employee",
    employeeId: "EMP006",
    avatar: null,
    isActive: true,
    createdAt: new Date(Date.now() - 8 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 6 * 24 * 60 * 60 * 1000)
  }
];

export const testAnnouncements: Announcement[] = [
  {
    id: "ann-1",
    title: "Nouvelle politique de télétravail",
    content: "À partir du 1er février, tous les employés pourront bénéficier de 2 jours de télétravail par semaine. Cette mesure vise à améliorer l'équilibre vie professionnelle/vie privée et à réduire les temps de transport. Les demandes doivent être validées par votre responsable direct.",
    type: "important",
    authorId: "admin-1",
    authorName: "Marie Dupont",
    imageUrl: null,
    icon: "📢",
    createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000),
    isImportant: true
  },
  {
    id: "ann-2",
    title: "Formation cybersécurité obligatoire",
    content: "Tous les employés doivent suivre la formation cybersécurité avant le 15 février. Cette formation en ligne dure 2 heures et couvre les bonnes pratiques de sécurité informatique. Un certificat sera délivré à l'issue de la formation.",
    type: "formation",
    authorId: "mod-1",
    authorName: "Pierre Martin",
    imageUrl: null,
    icon: "🎓",
    createdAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000),
    isImportant: false
  },
  {
    id: "ann-3",
    title: "Réunion mensuelle - Résultats Q4",
    content: "La réunion mensuelle aura lieu le vendredi 26 janvier à 14h en salle de conférence. Ordre du jour : présentation des résultats Q4, objectifs 2024, et questions diverses. La présence de tous les managers est requise.",
    type: "event",
    authorId: "admin-1",
    authorName: "Marie Dupont",
    imageUrl: null,
    icon: "📅",
    createdAt: new Date(Date.now() - 5 * 60 * 60 * 1000),
    isImportant: true
  },
  {
    id: "ann-4",
    title: "Nouveaux arrivants - Janvier 2024",
    content: "Nous accueillons trois nouveaux collaborateurs ce mois-ci : Emma Leroy (Marketing), Thomas Petit (IT), et Camille Moreau (Commercial). N'hésitez pas à leur souhaiter la bienvenue et à les aider dans leur intégration.",
    type: "info",
    authorId: "mod-1",
    authorName: "Pierre Martin",
    imageUrl: null,
    icon: "👋",
    createdAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000),
    isImportant: false
  },
  {
    id: "ann-5",
    title: "Maintenance serveurs - Weekend du 27-28 janvier",
    content: "Une maintenance programmée des serveurs aura lieu ce weekend. Des coupures intermittentes sont possibles samedi entre 20h et minuit. Tous les services seront pleinement opérationnels lundi matin.",
    type: "important",
    authorId: "emp-1",
    authorName: "Sophie Bernard",
    imageUrl: null,
    icon: "⚠️",
    createdAt: new Date(Date.now() - 4 * 60 * 60 * 1000),
    isImportant: true
  }
];

export const testDocuments: Document[] = [
  {
    id: "doc-1",
    title: "Politique de télétravail 2024",
    description: "Document officiel détaillant les nouvelles modalités de télétravail",
    category: "policy",
    fileName: "politique-teletravail-2024.pdf",
    fileUrl: "/documents/politique-teletravail-2024.pdf",
    updatedAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000),
    version: "2.0"
  },
  {
    id: "doc-2",
    title: "Guide de sécurité informatique",
    description: "Bonnes pratiques et procédures de sécurité pour tous les employés",
    category: "guide",
    fileName: "guide-securite-informatique.pdf",
    fileUrl: "/documents/guide-securite-informatique.pdf",
    updatedAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000),
    version: "1.5"
  },
  {
    id: "doc-3",
    title: "Organigramme 2024",
    description: "Structure organisationnelle mise à jour de l'entreprise",
    category: "organization",
    fileName: "organigramme-2024.pdf",
    fileUrl: "/documents/organigramme-2024.pdf",
    updatedAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000),
    version: "3.0"
  },
  {
    id: "doc-4",
    title: "Procédures RH - Congés et absences",
    description: "Guide complet pour les demandes de congés et la gestion des absences",
    category: "procedure",
    fileName: "procedures-conges.pdf",
    fileUrl: "/documents/procedures-conges.pdf",
    updatedAt: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000),
    version: "1.2"
  }
];

export const testEvents: Event[] = [
  {
    id: "evt-1",
    title: "Réunion mensuelle équipe",
    description: "Point mensuel sur les projets en cours et les objectifs",
    date: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000),
    location: "Salle de conférence A",
    type: "meeting",
    organizerId: "admin-1",
    createdAt: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)
  },
  {
    id: "evt-2",
    title: "Formation cybersécurité",
    description: "Session de formation obligatoire sur la cybersécurité",
    date: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000),
    location: "Salle de formation",
    type: "training",
    organizerId: "mod-1",
    createdAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000)
  },
  {
    id: "evt-3",
    title: "Pot de bienvenue nouveaux arrivants",
    description: "Moment convivial pour accueillir les nouveaux collaborateurs",
    date: new Date(Date.now() + 1 * 24 * 60 * 60 * 1000),
    location: "Espace détente",
    type: "social",
    organizerId: "mod-1",
    createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000)
  }
];

export const testMessages: Message[] = [
  {
    id: "msg-1",
    subject: "Demande d'information - Projet Alpha",
    content: "Bonjour Sophie,\n\nPourrais-tu me faire un point sur l'avancement du projet Alpha ? J'aurais besoin des derniers indicateurs pour la réunion de demain.\n\nMerci d'avance,\nMarie",
    senderId: "admin-1",
    recipientId: "emp-1",
    isRead: false,
    createdAt: new Date(Date.now() - 2 * 60 * 60 * 1000)
  },
  {
    id: "msg-2",
    subject: "Re: Demande d'information - Projet Alpha",
    content: "Bonjour Marie,\n\nBien sûr ! Le projet Alpha avance bien. Nous sommes à 75% de completion. Je prépare un rapport détaillé que je t'enverrai d'ici la fin de journée.\n\nBonne journée,\nSophie",
    senderId: "emp-1",
    recipientId: "admin-1",
    isRead: true,
    createdAt: new Date(Date.now() - 1 * 60 * 60 * 1000)
  },
  {
    id: "msg-3",
    subject: "Formation cybersécurité - Rappel",
    content: "Bonjour à tous,\n\nJe vous rappelle que la formation cybersécurité est obligatoire et doit être complétée avant le 15 février. Le lien d'accès est disponible sur l'intranet.\n\nN'hésitez pas si vous avez des questions.\n\nPierre",
    senderId: "mod-1",
    recipientId: "emp-2",
    isRead: false,
    createdAt: new Date(Date.now() - 6 * 60 * 60 * 1000)
  }
];

export const testComplaints: Complaint[] = [
  {
    id: "comp-1",
    submitterId: "emp-2",
    assignedToId: "admin-1",
    title: "Problème de chauffage bureau 205",
    description: "Le radiateur du bureau 205 ne fonctionne pas correctement depuis une semaine. La température est très basse, ce qui rend le travail difficile.",
    category: "infrastructure",
    priority: "medium",
    status: "open",
    createdAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000)
  },
  {
    id: "comp-2",
    submitterId: "emp-3",
    assignedToId: "mod-1",
    title: "Accès parking - Badge défaillant",
    description: "Mon badge d'accès au parking ne fonctionne plus depuis hier. Je dois attendre qu'un collègue ouvre pour pouvoir entrer.",
    category: "access",
    priority: "low",
    status: "in_progress",
    createdAt: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 4 * 60 * 60 * 1000)
  },
  {
    id: "comp-3",
    submitterId: "emp-4",
    assignedToId: "emp-1",
    title: "Problème réseau wifi - Déconnexions fréquentes",
    description: "Le réseau wifi se déconnecte régulièrement dans l'open space du 2ème étage. Cela perturbe le travail et les visioconférences.",
    category: "technical",
    priority: "high",
    status: "resolved",
    createdAt: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000),
    updatedAt: new Date(Date.now() - 12 * 60 * 60 * 1000)
  }
];

// Function to populate all test data
export function createTestData() {
  return {
    users: testUsers,
    announcements: testAnnouncements,
    documents: testDocuments,
    events: testEvents,
    messages: testMessages,
    complaints: testComplaints
  };
}

// Function to get sample data counts for statistics
export function getTestDataStats() {
  return {
    totalUsers: testUsers.length,
    totalAnnouncements: testAnnouncements.length,
    totalDocuments: testDocuments.length,
    totalEvents: testEvents.length,
    totalMessages: testMessages.length,
    totalComplaints: testComplaints.length,
    newAnnouncements: testAnnouncements.filter(a => 
      new Date(a.createdAt).getTime() > Date.now() - 7 * 24 * 60 * 60 * 1000
    ).length,
    updatedDocuments: testDocuments.filter(d => 
      new Date(d.updatedAt).getTime() > Date.now() - 30 * 24 * 60 * 60 * 1000
    ).length,
    connectedUsers: Math.floor(testUsers.length * 0.6), // 60% connected
    pendingComplaints: testComplaints.filter(c => c.status === "open").length
  };
}