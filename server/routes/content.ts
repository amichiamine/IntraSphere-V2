import type { Express } from "express";
import { storage } from "../data/storage";
import { 
  insertAnnouncementSchema, 
  insertDocumentSchema, 
  insertEventSchema, 
  insertContentSchema, 
  insertCategorySchema 
} from "@shared/schema";

// Authentication middleware
const requireAuth = (req: any, res: any, next: any) => {
  if (!req.session?.userId) {
    return res.status(401).json({ message: "Authentication required" });
  }
  next();
};

const requireRole = (roles: string[]) => {
  return async (req: any, res: any, next: any) => {
    if (!req.session?.userId) {
      return res.status(401).json({ message: "Authentication required" });
    }
    
    const user = await storage.getUser(req.session.userId!);
    if (!user || !roles.includes(user.role)) {
      return res.status(403).json({ message: "Insufficient permissions" });
    }
    
    req.user = user;
    next();
  };
};

export function registerContentRoutes(app: Express): void {
  // Announcements
  app.get("/api/announcements", async (_req, res) => {
    try {
      const announcements = await storage.getAnnouncements();
      res.json(announcements);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch announcements" });
    }
  });

  app.get("/api/announcements/:id", async (req, res) => {
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

  app.post("/api/announcements", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const result = insertAnnouncementSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid announcement data", errors: result.error.issues });
      }
      
      const announcement = await storage.createAnnouncement(result.data);
      res.status(201).json(announcement);
    } catch (error) {
      res.status(500).json({ message: "Failed to create announcement" });
    }
  });

  app.put("/api/announcements/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      const result = insertAnnouncementSchema.partial().safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid announcement data", errors: result.error.issues });
      }
      
      const announcement = await storage.updateAnnouncement(id, result.data);
      res.json(announcement);
    } catch (error) {
      res.status(500).json({ message: "Failed to update announcement" });
    }
  });

  app.delete("/api/announcements/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteAnnouncement(id);
      res.json({ message: "Announcement deleted successfully" });
    } catch (error) {
      res.status(500).json({ message: "Failed to delete announcement" });
    }
  });

  // Documents - Complete CRUD
  app.get("/api/documents", async (_req, res) => {
    try {
      const documents = await storage.getDocuments();
      res.json(documents);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch documents" });
    }
  });

  app.get("/api/documents/:id", async (req, res) => {
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

  app.post("/api/documents", requireRole(['admin', 'moderator']), async (req, res) => {
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

  app.patch("/api/documents/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      const updatedDocument = await storage.updateDocument(id, req.body);
      res.json(updatedDocument);
    } catch (error) {
      console.error("Error updating document:", error);
      res.status(500).json({ error: "Failed to update document" });
    }
  });

  app.delete("/api/documents/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteDocument(id);
      res.json({ message: "Document deleted successfully" });
    } catch (error) {
      console.error("Error deleting document:", error);
      res.status(500).json({ error: "Failed to delete document" });
    }
  });

  // Events
  app.get("/api/events", async (_req, res) => {
    try {
      const events = await storage.getEvents();
      res.json(events);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch events" });
    }
  });

  app.get("/api/events/:id", async (req, res) => {
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

  app.post("/api/events", requireRole(['admin', 'moderator']), async (req, res) => {
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

  app.put("/api/events/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      const result = insertEventSchema.partial().safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid event data", errors: result.error.issues });
      }
      
      const event = await storage.updateEvent(id, result.data);
      res.json(event);
    } catch (error) {
      res.status(500).json({ message: "Failed to update event" });
    }
  });

  app.delete("/api/events/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteEvent(id);
      res.json({ message: "Event deleted successfully" });
    } catch (error) {
      res.status(500).json({ message: "Failed to delete event" });
    }
  });

  // Content management routes
  app.get("/api/contents", async (req, res) => {
    try {
      const contents = await storage.getContents();
      res.json(contents);
    } catch (error) {
      console.error("Error fetching contents:", error);
      res.status(500).json({ error: "Failed to fetch contents" });
    }
  });

  app.post("/api/contents", requireRole(['admin', 'moderator']), async (req, res) => {
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

  app.patch("/api/contents/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      const updatedContent = await storage.updateContent(id, req.body);
      res.json(updatedContent);
    } catch (error) {
      console.error("Error updating content:", error);
      res.status(500).json({ error: "Failed to update content" });
    }
  });

  app.delete("/api/contents/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteContent(id);
      res.json({ message: "Content deleted successfully" });
    } catch (error) {
      console.error("Error deleting content:", error);
      res.status(500).json({ error: "Failed to delete content" });
    }
  });

  // Categories routes
  app.get("/api/categories", async (req, res) => {
    try {
      const categories = await storage.getCategories();
      res.json(categories);
    } catch (error) {
      console.error("Error fetching categories:", error);
      res.status(500).json({ error: "Failed to fetch categories" });
    }
  });

  app.post("/api/categories", requireRole(['admin', 'moderator']), async (req, res) => {
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

  app.patch("/api/categories/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      const updatedCategory = await storage.updateCategory(id, req.body);
      res.json(updatedCategory);
    } catch (error) {
      console.error("Error updating category:", error);
      res.status(500).json({ error: "Failed to update category" });
    }
  });

  app.delete("/api/categories/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteCategory(id);
      res.json({ message: "Category deleted successfully" });
    } catch (error) {
      console.error("Error deleting category:", error);
      res.status(500).json({ error: "Failed to delete category" });
    }
  });
}