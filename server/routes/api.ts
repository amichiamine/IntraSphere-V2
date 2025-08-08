import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "../data/storage";
import { AuthService } from "../services/auth";
import { emailService } from "../services/email";
import { 
  insertAnnouncementSchema, 
  insertDocumentSchema, 
  insertEventSchema, 
  insertContentSchema, 
  insertCategorySchema, 
  insertUserSchema, 
  insertPermissionSchema, 
  insertComplaintSchema,
  insertEmployeeCategorySchema,
  insertSystemSettingsSchema,
  insertTrainingSchema,
  insertTrainingParticipantSchema,
  insertForumCategorySchema,
  insertForumTopicSchema,
  insertForumPostSchema,
  insertForumLikeSchema
} from "@shared/schema";

// Extend Express Request type for session
declare module 'express-session' {
  interface SessionData {
    userId: string;
    user: any;
  }
}

export async function registerRoutes(app: Express): Promise<Server> {

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

  // Authentication routes
  app.post("/api/auth/login", async (req, res) => {
    try {
      const { username, password } = req.body;
      
      if (!username || !password) {
        return res.status(400).json({ message: "Username and password required" });
      }

      const user = await storage.getUserByUsername(username);
      if (!user) {
        return res.status(401).json({ message: "Invalid credentials" });
      }

      // Verify password using bcrypt
      const isValidPassword = await AuthService.verifyPassword(password, user.password);
      if (!isValidPassword) {
        return res.status(401).json({ message: "Invalid credentials" });
      }

      if (!user.isActive) {
        return res.status(401).json({ message: "Account is deactivated" });
      }

      // Create session
      req.session.userId! = user.id;
      req.session.user = user;

      // Remove password from response
      const { password: _, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Login error:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post("/api/auth/register", async (req, res) => {
    try {
      const result = insertUserSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ 
          message: "Invalid user data", 
          errors: result.error.issues 
        });
      }

      // Check if username already exists
      const existingUser = await storage.getUserByUsername(result.data.username);
      if (existingUser) {
        return res.status(409).json({ message: "Username already exists" });
      }

      // Hash password before saving
      const hashedPassword = await AuthService.hashPassword(result.data.password);
      
      // Create new user with employee role by default
      const newUser = await storage.createUser({
        ...result.data,
        password: hashedPassword,
        role: "employee"
      });

      // Send welcome email if email service is configured
      if (result.data.email) {
        await emailService.sendWelcomeEmail(result.data.email, result.data.name);
      }

      // Create session
      req.session.userId! = newUser.id;
      req.session.user = newUser;

      // Remove password from response
      const { password: _, ...userWithoutPassword } = newUser;
      res.status(201).json(userWithoutPassword);
    } catch (error) {
      console.error("Registration error:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.get("/api/auth/me", async (req, res) => {
    try {
      const userId = req.session?.userId;
      if (!userId) {
        return res.status(401).json({ message: "Not authenticated" });
      }

      const user = await storage.getUser(userId);
      if (!user || !user.isActive) {
        return res.status(401).json({ message: "User not found or inactive" });
      }

      // Remove password from response
      const { password: _, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Get user error:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post("/api/auth/logout", (req, res) => {
    req.session.destroy((err) => {
      if (err) {
        console.error("Session destroy error:", err);
      }
    });
    res.json({ message: "Logged out successfully" });
  });

  // Dashboard stats
  app.get("/api/stats", async (_req, res) => {
    try {
      const stats = await storage.getStats();
      res.json(stats);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch stats" });
    }
  });

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

  app.post("/api/announcements", async (req, res) => {
    try {
      const announcement = await storage.createAnnouncement(req.body);
      res.status(201).json(announcement);
    } catch (error) {
      res.status(500).json({ message: "Failed to create announcement" });
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

  app.post("/api/documents", async (req, res) => {
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

  app.patch("/api/documents/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedDocument = await storage.updateDocument(id, req.body);
      res.json(updatedDocument);
    } catch (error) {
      console.error("Error updating document:", error);
      res.status(500).json({ error: "Failed to update document" });
    }
  });

  app.delete("/api/documents/:id", async (req, res) => {
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

  app.post("/api/events", async (req, res) => {
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

  // Users - Complete CRUD for Admin panel
  app.get("/api/users", async (req, res) => {
    try {
      const users = await storage.getUsers();
      res.json(users);
    } catch (error) {
      console.error("Error fetching users:", error);
      res.status(500).json({ error: "Failed to fetch users" });
    }
  });

  app.post("/api/users", async (req, res) => {
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

  app.patch("/api/users/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedUser = await storage.updateUser(id, req.body);
      res.json(updatedUser);
    } catch (error) {
      console.error("Error updating user:", error);
      res.status(500).json({ error: "Failed to update user" });
    }
  });

  app.delete("/api/users/:id", async (req, res) => {
    try {
      const { id } = req.params;
      // Soft delete by setting isActive to false
      await storage.updateUser(id, { isActive: false });
      res.json({ message: "User deactivated successfully" });
    } catch (error) {
      console.error("Error deactivating user:", error);
      res.status(500).json({ error: "Failed to deactivate user" });
    }
  });

  // Messages routes
  app.get("/api/messages/:userId", async (req, res) => {
    try {
      const { userId } = req.params;
      const messages = await storage.getMessages(userId);
      res.json(messages);
    } catch (error) {
      console.error("Error fetching messages:", error);
      res.status(500).json({ error: "Failed to fetch messages" });
    }
  });

  app.post("/api/messages", async (req, res) => {
    try {
      const message = await storage.createMessage(req.body);
      res.status(201).json(message);
    } catch (error) {
      console.error("Error creating message:", error);
      res.status(500).json({ error: "Failed to create message" });
    }
  });

  app.patch("/api/messages/:id/read", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.markMessageAsRead(id);
      res.json({ success: true });
    } catch (error) {
      console.error("Error marking message as read:", error);
      res.status(500).json({ error: "Failed to mark message as read" });
    }
  });

  // Complaints routes - Complete CRUD
  app.get("/api/complaints", async (req, res) => {
    try {
      const complaints = await storage.getComplaints();
      res.json(complaints);
    } catch (error) {
      console.error("Error fetching complaints:", error);
      res.status(500).json({ error: "Failed to fetch complaints" });
    }
  });

  app.post("/api/complaints", async (req, res) => {
    try {
      const complaint = await storage.createComplaint(req.body);
      res.status(201).json(complaint);
    } catch (error) {
      console.error("Error creating complaint:", error);
      res.status(500).json({ error: "Failed to create complaint" });
    }
  });

  app.patch("/api/complaints/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedComplaint = await storage.updateComplaint(id, req.body);
      res.json(updatedComplaint);
    } catch (error) {
      console.error("Error updating complaint:", error);
      res.status(500).json({ error: "Failed to update complaint" });
    }
  });

  // Permissions routes - Complete implementation for Admin panel
  app.get("/api/permissions", async (req, res) => {
    try {
      // Return all permissions across all users for admin view
      const users = await storage.getUsers();
      const allPermissions = [];
      
      for (const user of users) {
        const userPermissions = await storage.getPermissions(user.id);
        allPermissions.push(...userPermissions);
      }
      
      res.json(allPermissions);
    } catch (error) {
      console.error("Error fetching permissions:", error);
      res.status(500).json({ error: "Failed to fetch permissions" });
    }
  });

  app.get("/api/permissions/:userId", async (req, res) => {
    try {
      const { userId } = req.params;
      const permissions = await storage.getPermissions(userId);
      res.json(permissions);
    } catch (error) {
      console.error("Error fetching user permissions:", error);
      res.status(500).json({ error: "Failed to fetch user permissions" });
    }
  });

  app.post("/api/permissions", async (req, res) => {
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

  app.delete("/api/permissions/:id", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.revokePermission(id);
      res.json({ message: "Permission revoked successfully" });
    } catch (error) {
      console.error("Error revoking permission:", error);
      res.status(500).json({ error: "Failed to revoke permission" });
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

  app.post("/api/contents", async (req, res) => {
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

  app.patch("/api/contents/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedContent = await storage.updateContent(id, req.body);
      res.json(updatedContent);
    } catch (error) {
      console.error("Error updating content:", error);
      res.status(500).json({ error: "Failed to update content" });
    }
  });

  app.delete("/api/contents/:id", async (req, res) => {
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

  app.post("/api/categories", async (req, res) => {
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

  app.patch("/api/categories/:id", async (req, res) => {
    try {
      const { id } = req.params;
      const updatedCategory = await storage.updateCategory(id, req.body);
      res.json(updatedCategory);
    } catch (error) {
      console.error("Error updating category:", error);
      res.status(500).json({ error: "Failed to update category" });
    }
  });

  app.delete("/api/categories/:id", async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteCategory(id);
      res.json({ message: "Category deleted successfully" });
    } catch (error) {
      console.error("Error deleting category:", error);
      res.status(500).json({ error: "Failed to delete category" });
    }
  });

  // Employee Categories routes
  app.get("/api/employee-categories", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const categories = await storage.getEmployeeCategories();
      res.json(categories);
    } catch (error) {
      console.error("Error fetching employee categories:", error);
      res.status(500).json({ error: "Failed to fetch employee categories" });
    }
  });

  app.post("/api/employee-categories", requireRole(['admin']), async (req, res) => {
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

  app.patch("/api/employee-categories/:id", requireRole(['admin']), async (req, res) => {
    try {
      const { id } = req.params;
      const updatedCategory = await storage.updateEmployeeCategory(id, req.body);
      res.json(updatedCategory);
    } catch (error) {
      console.error("Error updating employee category:", error);
      res.status(500).json({ error: "Failed to update employee category" });
    }
  });

  app.delete("/api/employee-categories/:id", requireRole(['admin']), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteEmployeeCategory(id);
      res.json({ message: "Employee category deleted successfully" });
    } catch (error) {
      console.error("Error deleting employee category:", error);
      res.status(500).json({ error: "Failed to delete employee category" });
    }
  });

  // System Settings routes
  app.get("/api/system-settings", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const settings = await storage.getSystemSettings();
      res.json(settings);
    } catch (error) {
      console.error("Error fetching system settings:", error);
      res.status(500).json({ error: "Failed to fetch system settings" });
    }
  });

  app.patch("/api/system-settings", requireRole(['admin']), async (req, res) => {
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

  // Training routes
  app.get("/api/trainings", async (req, res) => {
    try {
      const trainings = await storage.getTrainings();
      res.json(trainings);
    } catch (error) {
      console.error("Error fetching trainings:", error);
      res.status(500).json({ error: "Failed to fetch trainings" });
    }
  });

  app.get("/api/trainings/:id", async (req, res) => {
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

  app.post("/api/trainings", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      // Check permissions
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

  app.put("/api/trainings/:id", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      // Check permissions
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
    } catch (error: any) {
      console.error("Error updating training:", error);
      if (error.message === "Training not found") {
        return res.status(404).json({ error: "Training not found" });
      }
      res.status(500).json({ error: "Failed to update training" });
    }
  });

  app.delete("/api/trainings/:id", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      // Check permissions
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

  // Training participants routes
  app.get("/api/trainings/:id/participants", async (req, res) => {
    try {
      const participants = await storage.getTrainingParticipants(req.params.id);
      res.json(participants);
    } catch (error) {
      console.error("Error fetching training participants:", error);
      res.status(500).json({ error: "Failed to fetch training participants" });
    }
  });

  app.post("/api/trainings/:id/participants", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
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

  app.delete("/api/trainings/:id/participants/:userId", requireAuth, async (req, res) => {
    try {
      const currentUserId = req.session.userId!;
      const { id: trainingId, userId } = req.params;

      // Only allow users to remove themselves or admin/training managers to remove anyone
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

  app.get("/api/users/:userId/trainings", requireAuth, async (req, res) => {
    try {
      const currentUserId = req.session.userId!;
      const { userId } = req.params;

      // Only allow users to view their own trainings or admin/training managers to view anyone's
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

  // Role-based operations for administration
  app.post("/api/admin/bulk-permissions", async (req, res) => {
    try {
      const { userId, permissions, action } = req.body; // action: 'grant' or 'revoke'
      
      if (action === 'grant') {
        for (const permission of permissions) {
          await storage.createPermission({
            userId: userId,
            grantedBy: 'user-1', // Current admin user
            permission: permission
          });
        }
      } else if (action === 'revoke') {
        const userPermissions = await storage.getPermissions(userId);
        for (const permission of permissions) {
          const existingPermission = userPermissions.find(p => p.permission === permission);
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

  // Views Configuration API endpoints
  app.get("/api/views-config", async (req, res) => {
    try {
      const viewsConfig = await storage.getViewsConfig();
      res.json(viewsConfig);
    } catch (error) {
      console.error("Error fetching views config:", error);
      res.status(500).json({ error: "Failed to fetch views configuration" });
    }
  });

  app.post("/api/views-config", async (req, res) => {
    try {
      const { views } = req.body;
      await storage.saveViewsConfig(views);
      res.json({ message: "Views configuration saved successfully" });
    } catch (error) {
      console.error("Error saving views config:", error);
      res.status(500).json({ error: "Failed to save views configuration" });
    }
  });

  app.patch("/api/views-config/:viewId", async (req, res) => {
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

  // User Settings API endpoints
  app.get("/api/user/settings", async (req, res) => {
    try {
      const userSettings = await storage.getUserSettings('user-1'); // Current user
      res.json(userSettings);
    } catch (error) {
      console.error("Error fetching user settings:", error);
      res.status(500).json({ error: "Failed to fetch user settings" });
    }
  });

  app.post("/api/user/settings", async (req, res) => {
    try {
      const settings = req.body;
      console.log('Received settings:', JSON.stringify(settings, null, 2));
      
      if (!settings || typeof settings !== 'object') {
        return res.status(400).json({ error: "Invalid settings data" });
      }
      
      await storage.saveUserSettings('user-1', settings); // Current user
      res.json({ 
        message: "User settings saved successfully",
        timestamp: new Date().toISOString()
      });
    } catch (error) {
      console.error("Error saving user settings:", error);
      res.status(500).json({ 
        error: "Failed to save user settings",
        details: error instanceof Error ? error.message : 'Unknown error'
      });
    }
  });

  // Reset test data endpoint (admin only)
  app.post("/api/admin/reset-test-data", async (req, res) => {
    try {
      await storage.resetToTestData();
      res.json({ 
        message: "✅ Données de test réinitialisées avec succès",
        timestamp: new Date().toISOString()
      });
    } catch (error) {
      console.error("Error resetting test data:", error);
      res.status(500).json({ 
        error: "❌ Erreur lors de la réinitialisation des données de test" 
      });
    }
  });

  // E-Learning API Routes

  // Courses
  app.get("/api/courses", requireAuth, async (req, res) => {
    try {
      const courses = await storage.getCourses();
      res.json(courses);
    } catch (error) {
      console.error("Error fetching courses:", error);
      res.status(500).json({ error: "Failed to fetch courses" });
    }
  });

  app.get("/api/courses/:id", requireAuth, async (req, res) => {
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

  app.post("/api/courses", requireAuth, requireRole(["admin"]), async (req, res) => {
    try {
      const { insertCourseSchema } = await import("@shared/schema");
      const validatedData = insertCourseSchema.parse(req.body);
      const course = await storage.createCourse(validatedData);
      res.status(201).json(course);
    } catch (error) {
      console.error("Error creating course:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid course data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create course" });
    }
  });

  // Lessons
  app.get("/api/courses/:courseId/lessons", requireAuth, async (req, res) => {
    try {
      const lessons = await storage.getLessons(req.params.courseId);
      res.json(lessons);
    } catch (error) {
      console.error("Error fetching lessons:", error);
      res.status(500).json({ error: "Failed to fetch lessons" });
    }
  });

  app.get("/api/lessons/:id", requireAuth, async (req, res) => {
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

  // Enrollments
  app.get("/api/my-enrollments", requireAuth, async (req, res) => {
    try {
      const userId = (req as any).userId;
      const enrollments = await storage.getUserEnrollments(userId);
      res.json(enrollments);
    } catch (error) {
      console.error("Error fetching enrollments:", error);
      res.status(500).json({ error: "Failed to fetch enrollments" });
    }
  });

  app.post("/api/enroll/:courseId", requireAuth, async (req, res) => {
    try {
      const userId = (req as any).userId;
      const courseId = req.params.courseId;
      
      // Check if already enrolled
      const existingEnrollments = await storage.getUserEnrollments(userId);
      const existingEnrollment = existingEnrollments.find(e => e.courseId === courseId);
      
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

  // Progress tracking
  app.post("/api/lessons/:lessonId/complete", requireAuth, async (req, res) => {
    try {
      const userId = (req as any).userId;
      const lessonId = req.params.lessonId;
      const { courseId } = req.body;
      
      const progress = await storage.updateLessonProgress(userId, lessonId, courseId, true);
      res.json(progress);
    } catch (error) {
      console.error("Error updating lesson progress:", error);
      res.status(500).json({ error: "Failed to update lesson progress" });
    }
  });

  app.get("/api/courses/:courseId/my-progress", requireAuth, async (req, res) => {
    try {
      const userId = (req as any).userId;
      const courseId = req.params.courseId;
      
      const progress = await storage.getUserLessonProgress(userId, courseId);
      res.json(progress);
    } catch (error) {
      console.error("Error fetching progress:", error);
      res.status(500).json({ error: "Failed to fetch progress" });
    }
  });

  // Resources
  app.get("/api/resources", requireAuth, async (req, res) => {
    try {
      const resources = await storage.getResources();
      res.json(resources);
    } catch (error) {
      console.error("Error fetching resources:", error);
      res.status(500).json({ error: "Failed to fetch resources" });
    }
  });

  app.post("/api/resources", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertResourceSchema } = await import("@shared/schema");
      const validatedData = insertResourceSchema.parse(req.body);
      const resource = await storage.createResource(validatedData);
      res.status(201).json(resource);
    } catch (error) {
      console.error("Error creating resource:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid resource data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create resource" });
    }
  });

  // Certificates
  app.get("/api/my-certificates", requireAuth, async (req, res) => {
    try {
      const userId = (req as any).userId;
      const certificates = await storage.getUserCertificates(userId);
      res.json(certificates);
    } catch (error) {
      console.error("Error fetching certificates:", error);
      res.status(500).json({ error: "Failed to fetch certificates" });
    }
  });

  // Training Admin Routes
  app.post("/api/lessons", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertLessonSchema } = await import("@shared/schema");
      const validatedData = insertLessonSchema.parse(req.body);
      const lesson = await storage.createLesson(validatedData);
      res.status(201).json(lesson);
    } catch (error) {
      console.error("Error creating lesson:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid lesson data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create lesson" });
    }
  });

  app.put("/api/courses/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertCourseSchema } = await import("@shared/schema");
      const validatedData = insertCourseSchema.parse(req.body);
      const course = await storage.updateCourse(req.params.id, validatedData);
      if (!course) {
        return res.status(404).json({ error: "Course not found" });
      }
      res.json(course);
    } catch (error) {
      console.error("Error updating course:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid course data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to update course" });
    }
  });

  app.delete("/api/courses/:id", requireAuth, requireRole(["admin"]), async (req, res) => {
    try {
      await storage.deleteCourse(req.params.id);
      res.json({ message: "Course deleted successfully" });
    } catch (error) {
      console.error("Error deleting course:", error);
      res.status(500).json({ error: "Failed to delete course" });
    }
  });

  app.put("/api/lessons/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertLessonSchema } = await import("@shared/schema");
      const validatedData = insertLessonSchema.omit({ courseId: true }).parse(req.body);
      const lesson = await storage.updateLesson(req.params.id, validatedData);
      if (!lesson) {
        return res.status(404).json({ error: "Lesson not found" });
      }
      res.json(lesson);
    } catch (error) {
      console.error("Error updating lesson:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid lesson data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to update lesson" });
    }
  });

  app.delete("/api/lessons/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteLesson(req.params.id);
      res.json({ message: "Lesson deleted successfully" });
    } catch (error) {
      console.error("Error deleting lesson:", error);
      res.status(500).json({ error: "Failed to delete lesson" });
    }
  });

  app.put("/api/resources/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const { insertResourceSchema } = await import("@shared/schema");
      const validatedData = insertResourceSchema.parse(req.body);
      const resource = await storage.updateResource(req.params.id, validatedData);
      if (!resource) {
        return res.status(404).json({ error: "Resource not found" });
      }
      res.json(resource);
    } catch (error) {
      console.error("Error updating resource:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid resource data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to update resource" });
    }
  });

  app.delete("/api/resources/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteResource(req.params.id);
      res.json({ message: "Resource deleted successfully" });
    } catch (error) {
      console.error("Error deleting resource:", error);
      res.status(500).json({ error: "Failed to delete resource" });
    }
  });

  // Search functionality
  app.get("/api/search/users", requireAuth, async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== 'string') {
        return res.status(400).json({ error: "Query parameter 'q' is required" });
      }
      
      const users = await storage.searchUsers(q);
      // Remove passwords from response
      const safeUsers = users.map(user => {
        const { password, ...safeUser } = user;
        return safeUser;
      });
      
      res.json(safeUsers);
    } catch (error) {
      console.error("Error searching users:", error);
      res.status(500).json({ error: "Failed to search users" });
    }
  });

  // Forum System Routes

  // Forum Categories
  app.get("/api/forum/categories", requireAuth, async (req, res) => {
    try {
      const categories = await storage.getForumCategories();
      res.json(categories);
    } catch (error) {
      console.error("Error fetching forum categories:", error);
      res.status(500).json({ error: "Failed to fetch forum categories" });
    }
  });

  app.get("/api/forum/categories/:id", requireAuth, async (req, res) => {
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

  app.post("/api/forum/categories", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const validatedData = insertForumCategorySchema.parse(req.body);
      const category = await storage.createForumCategory(validatedData);
      res.status(201).json(category);
    } catch (error) {
      console.error("Error creating forum category:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid category data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create forum category" });
    }
  });

  app.put("/api/forum/categories/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const category = await storage.updateForumCategory(req.params.id, req.body);
      res.json(category);
    } catch (error) {
      console.error("Error updating forum category:", error);
      res.status(500).json({ error: "Failed to update forum category" });
    }
  });

  app.delete("/api/forum/categories/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteForumCategory(req.params.id);
      res.json({ message: "Forum category deleted successfully" });
    } catch (error) {
      console.error("Error deleting forum category:", error);
      res.status(500).json({ error: "Failed to delete forum category" });
    }
  });

  // Forum Topics
  app.get("/api/forum/topics", requireAuth, async (req, res) => {
    try {
      const { categoryId } = req.query;
      const topics = await storage.getForumTopics(categoryId as string);
      res.json(topics);
    } catch (error) {
      console.error("Error fetching forum topics:", error);
      res.status(500).json({ error: "Failed to fetch forum topics" });
    }
  });

  app.get("/api/forum/topics/:id", requireAuth, async (req, res) => {
    try {
      const topic = await storage.getForumTopicById(req.params.id);
      if (!topic) {
        return res.status(404).json({ error: "Forum topic not found" });
      }
      
      // Increment view count
      await storage.incrementTopicViews(req.params.id);
      
      res.json(topic);
    } catch (error) {
      console.error("Error fetching forum topic:", error);
      res.status(500).json({ error: "Failed to fetch forum topic" });
    }
  });

  app.post("/api/forum/topics", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
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
        return res.status(400).json({ error: "Invalid topic data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create forum topic" });
    }
  });

  app.put("/api/forum/topics/:id", requireAuth, async (req, res) => {
    try {
      const topic = await storage.getForumTopicById(req.params.id);
      if (!topic) {
        return res.status(404).json({ error: "Forum topic not found" });
      }

      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }

      // Check if user can edit (author, admin, or moderator)
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

  app.delete("/api/forum/topics/:id", requireAuth, requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteForumTopic(req.params.id);
      res.json({ message: "Forum topic deleted successfully" });
    } catch (error) {
      console.error("Error deleting forum topic:", error);
      res.status(500).json({ error: "Failed to delete forum topic" });
    }
  });

  // Forum Posts
  app.get("/api/forum/topics/:topicId/posts", requireAuth, async (req, res) => {
    try {
      const posts = await storage.getForumPosts(req.params.topicId);
      res.json(posts);
    } catch (error) {
      console.error("Error fetching forum posts:", error);
      res.status(500).json({ error: "Failed to fetch forum posts" });
    }
  });

  app.get("/api/forum/posts/:id", requireAuth, async (req, res) => {
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

  app.post("/api/forum/posts", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
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
        return res.status(400).json({ error: "Invalid post data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create forum post" });
    }
  });

  app.put("/api/forum/posts/:id", requireAuth, async (req, res) => {
    try {
      const post = await storage.getForumPostById(req.params.id);
      if (!post) {
        return res.status(404).json({ error: "Forum post not found" });
      }

      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }

      // Check if user can edit (author, admin, or moderator)
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

  app.delete("/api/forum/posts/:id", requireAuth, async (req, res) => {
    try {
      const post = await storage.getForumPostById(req.params.id);
      if (!post) {
        return res.status(404).json({ error: "Forum post not found" });
      }

      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }

      // Check if user can delete (author, admin, or moderator)
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

  // Forum Likes/Reactions
  app.get("/api/forum/posts/:postId/likes", requireAuth, async (req, res) => {
    try {
      const likes = await storage.getForumPostLikes(req.params.postId);
      res.json(likes);
    } catch (error) {
      console.error("Error fetching forum post likes:", error);
      res.status(500).json({ error: "Failed to fetch forum post likes" });
    }
  });

  app.post("/api/forum/posts/:postId/like", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
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
        return res.status(400).json({ error: "Invalid like data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to toggle forum post like" });
    }
  });

  // Forum User Stats
  app.get("/api/forum/users/:userId/stats", requireAuth, async (req, res) => {
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

  app.get("/api/forum/stats/me", requireAuth, async (req, res) => {
    try {
      const stats = await storage.getForumUserStats(req.session.userId!);
      if (!stats) {
        return res.status(404).json({ error: "Forum user stats not found" });
      }
      res.json(stats);
    } catch (error) {
      console.error("Error fetching forum user stats:", error);
      res.status(500).json({ error: "Failed to fetch forum user stats" });
    }
  });

  const httpServer = createServer(app);
  return httpServer;
}