import type { Express } from "express";
import { storage } from "../data/storage";
import { 
  insertPermissionSchema, 
  insertEmployeeCategorySchema,
  insertSystemSettingsSchema
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

export function registerAdminRoutes(app: Express): void {
  // Permissions routes - Complete implementation for Admin panel
  app.get("/api/permissions", requireRole(['admin', 'moderator']), async (req, res) => {
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

  app.get("/api/permissions/:userId", requireAuth, async (req, res) => {
    try {
      const { userId } = req.params;
      const currentUserId = req.session.userId!;
      
      // Users can only view their own permissions unless they're admin/moderator
      if (userId !== currentUserId) {
        const currentUser = await storage.getUser(currentUserId);
        if (!currentUser || (currentUser.role !== 'admin' && currentUser.role !== 'moderator')) {
          return res.status(403).json({ message: "Access denied" });
        }
      }
      
      const permissions = await storage.getPermissions(userId);
      res.json(permissions);
    } catch (error) {
      console.error("Error fetching user permissions:", error);
      res.status(500).json({ error: "Failed to fetch user permissions" });
    }
  });

  app.post("/api/permissions", requireRole(['admin']), async (req, res) => {
    try {
      const result = insertPermissionSchema.safeParse({
        ...req.body,
        grantedBy: req.session.userId!
      });
      
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

  app.delete("/api/permissions/:id", requireRole(['admin']), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.revokePermission(id);
      res.json({ message: "Permission revoked successfully" });
    } catch (error) {
      console.error("Error revoking permission:", error);
      res.status(500).json({ error: "Failed to revoke permission" });
    }
  });

  // Bulk permissions management
  app.post("/api/admin/bulk-permissions", requireRole(['admin']), async (req, res) => {
    try {
      const { userId, permissions, action } = req.body; // action: 'grant' or 'revoke'
      const grantedBy = req.session.userId!;
      
      if (action === 'grant') {
        for (const permission of permissions) {
          await storage.createPermission({
            userId: userId,
            grantedBy: grantedBy,
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

  app.get("/api/admin/permission-check/:userId/:permission", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { userId, permission } = req.params;
      const hasPermission = await storage.hasPermission(userId, permission);
      res.json({ hasPermission });
    } catch (error) {
      console.error("Error checking permission:", error);
      res.status(500).json({ error: "Failed to check permission" });
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
      const result = insertSystemSettingsSchema.partial().safeParse(req.body);
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

  // Views Configuration API endpoints
  app.get("/api/views-config", requireRole(['admin']), async (req, res) => {
    try {
      const viewsConfig = await storage.getViewsConfig();
      res.json(viewsConfig);
    } catch (error) {
      console.error("Error fetching views config:", error);
      res.status(500).json({ error: "Failed to fetch views configuration" });
    }
  });

  app.post("/api/views-config", requireRole(['admin']), async (req, res) => {
    try {
      const { views } = req.body;
      await storage.saveViewsConfig(views);
      res.json({ message: "Views configuration saved successfully" });
    } catch (error) {
      console.error("Error saving views config:", error);
      res.status(500).json({ error: "Failed to save views configuration" });
    }
  });

  app.patch("/api/views-config/:viewId", requireRole(['admin']), async (req, res) => {
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
  app.get("/api/user/settings", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const userSettings = await storage.getUserSettings(userId);
      res.json(userSettings);
    } catch (error) {
      console.error("Error fetching user settings:", error);
      res.status(500).json({ error: "Failed to fetch user settings" });
    }
  });

  app.post("/api/user/settings", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const settings = req.body;
      
      console.log('Received settings:', JSON.stringify(settings, null, 2));
      
      if (!settings || typeof settings !== 'object') {
        return res.status(400).json({ error: "Invalid settings data" });
      }
      
      await storage.saveUserSettings(userId, settings);
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

  // Analytics and Statistics
  app.get("/api/admin/analytics/overview", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const stats = await storage.getStats();
      res.json(stats);
    } catch (error) {
      console.error("Error fetching analytics overview:", error);
      res.status(500).json({ error: "Failed to fetch analytics overview" });
    }
  });

  app.get("/api/admin/analytics/users", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const users = await storage.getUsers();
      const totalUsers = users.length;
      const activeUsers = users.filter(u => u.isActive).length;
      const usersByRole = users.reduce((acc: Record<string, number>, user) => {
        acc[user.role] = (acc[user.role] || 0) + 1;
        return acc;
      }, {});

      res.json({
        totalUsers,
        activeUsers,
        inactiveUsers: totalUsers - activeUsers,
        usersByRole
      });
    } catch (error) {
      console.error("Error fetching user analytics:", error);
      res.status(500).json({ error: "Failed to fetch user analytics" });
    }
  });

  app.get("/api/admin/analytics/content", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const [announcements, documents, events, trainings] = await Promise.all([
        storage.getAnnouncements(),
        storage.getDocuments(),
        storage.getEvents(),
        storage.getTrainings()
      ]);

      res.json({
        announcements: announcements.length,
        documents: documents.length,
        events: events.length,
        trainings: trainings.length
      });
    } catch (error) {
      console.error("Error fetching content analytics:", error);
      res.status(500).json({ error: "Failed to fetch content analytics" });
    }
  });

  // Reset test data endpoint (admin only)
  app.post("/api/admin/reset-test-data", requireRole(['admin']), async (req, res) => {
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

  // Search functionality
  app.get("/api/search/users", requireAuth, async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== 'string') {
        return res.status(400).json({ error: "Search query required" });
      }
      
      const users = await storage.getUsers();
      const searchResults = users.filter(user => 
        user.name.toLowerCase().includes(q.toLowerCase()) ||
        user.username.toLowerCase().includes(q.toLowerCase()) ||
        user.email?.toLowerCase().includes(q.toLowerCase()) ||
        user.department?.toLowerCase().includes(q.toLowerCase())
      );
      
      // Remove passwords from results
      const safeResults = searchResults.map(({ password, ...user }) => user);
      res.json(safeResults);
    } catch (error) {
      console.error("Error searching users:", error);
      res.status(500).json({ error: "Failed to search users" });
    }
  });

  app.get("/api/search/content", requireAuth, async (req, res) => {
    try {
      const { q } = req.query;
      if (!q || typeof q !== 'string') {
        return res.status(400).json({ error: "Search query required" });
      }
      
      const [announcements, documents, events] = await Promise.all([
        storage.getAnnouncements(),
        storage.getDocuments(),
        storage.getEvents()
      ]);
      
      const results = {
        announcements: announcements.filter(item => 
          item.title.toLowerCase().includes(q.toLowerCase()) ||
          item.content?.toLowerCase().includes(q.toLowerCase())
        ),
        documents: documents.filter(item => 
          item.title.toLowerCase().includes(q.toLowerCase()) ||
          item.description?.toLowerCase().includes(q.toLowerCase())
        ),
        events: events.filter(item => 
          item.title.toLowerCase().includes(q.toLowerCase()) ||
          item.description?.toLowerCase().includes(q.toLowerCase())
        )
      };
      
      res.json(results);
    } catch (error) {
      console.error("Error searching content:", error);
      res.status(500).json({ error: "Failed to search content" });
    }
  });
}