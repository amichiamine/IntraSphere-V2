import type { Express } from "express";
import { storage } from "../data/storage";
import { insertUserSchema } from "@shared/schema";

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

export function registerUsersRoutes(app: Express): void {
  // Users - Complete CRUD for Admin panel
  app.get("/api/users", requireAuth, async (req, res) => {
    try {
      const users = await storage.getUsers();
      res.json(users);
    } catch (error) {
      console.error("Error fetching users:", error);
      res.status(500).json({ error: "Failed to fetch users" });
    }
  });

  app.get("/api/users/:id", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.params.id);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      
      // Remove password from response
      const { password: _, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Error fetching user:", error);
      res.status(500).json({ error: "Failed to fetch user" });
    }
  });

  app.post("/api/users", requireRole(['admin']), async (req, res) => {
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

  app.patch("/api/users/:id", requireAuth, async (req, res) => {
    try {
      const { id } = req.params;
      const currentUserId = req.session.userId!;
      
      // Users can only update themselves unless they're admin
      if (id !== currentUserId) {
        const currentUser = await storage.getUser(currentUserId);
        if (!currentUser || currentUser.role !== 'admin') {
          return res.status(403).json({ message: "Insufficient permissions" });
        }
      }
      
      const updatedUser = await storage.updateUser(id, req.body);
      res.json(updatedUser);
    } catch (error) {
      console.error("Error updating user:", error);
      res.status(500).json({ error: "Failed to update user" });
    }
  });

  app.delete("/api/users/:id", requireRole(['admin']), async (req, res) => {
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

  app.put("/api/users/:id/status", requireRole(['admin']), async (req, res) => {
    try {
      const { id } = req.params;
      const { isActive } = req.body;
      
      await storage.updateUser(id, { isActive });
      res.json({ message: `User ${isActive ? 'activated' : 'deactivated'} successfully` });
    } catch (error) {
      console.error("Error updating user status:", error);
      res.status(500).json({ error: "Failed to update user status" });
    }
  });

  app.put("/api/users/:id/password", requireAuth, async (req, res) => {
    try {
      const { id } = req.params;
      const { currentPassword, newPassword } = req.body;
      const currentUserId = req.session.userId!;
      
      // Users can only change their own password unless they're admin
      if (id !== currentUserId) {
        const currentUser = await storage.getUser(currentUserId);
        if (!currentUser || currentUser.role !== 'admin') {
          return res.status(403).json({ message: "Insufficient permissions" });
        }
      }
      
      // For non-admin users, verify current password
      if (id === currentUserId && currentPassword) {
        const user = await storage.getUser(id);
        if (!user) {
          return res.status(404).json({ message: "User not found" });
        }
        
        const { AuthService } = await import("../services/auth");
        const isValidPassword = await AuthService.verifyPassword(currentPassword, user.password);
        if (!isValidPassword) {
          return res.status(400).json({ message: "Current password is incorrect" });
        }
      }
      
      // Hash new password
      const { AuthService } = await import("../services/auth");
      const hashedPassword = await AuthService.hashPassword(newPassword);
      
      await storage.updateUser(id, { password: hashedPassword });
      res.json({ message: "Password updated successfully" });
    } catch (error) {
      console.error("Error updating password:", error);
      res.status(500).json({ error: "Failed to update password" });
    }
  });
}