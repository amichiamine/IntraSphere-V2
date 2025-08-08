import type { Express } from "express";
import { storage } from "../data/storage";
import { AuthService } from "../services/auth";
import { emailService } from "../services/email";
import { insertUserSchema } from "@shared/schema";
import { RateLimiter } from "../utils/rate-limiter";

// Extend Express Request type for session
declare module 'express-session' {
  interface SessionData {
    userId: string;
    user: any;
  }
}

export function registerAuthRoutes(app: Express): void {
  // Authentication routes
  app.post("/api/auth/login", RateLimiter.middleware('login'), async (req, res) => {
    try {
      const { username, password } = req.body;
      
      if (!username || !password) {
        return res.status(400).json({ message: "Username and password required" });
      }

      // Validate password strength before checking against database
      const passwordValidation = AuthService.validatePasswordStrength(password);
      if (!passwordValidation.isValid) {
        return res.status(400).json({ 
          message: "Mot de passe invalide", 
          errors: passwordValidation.errors 
        });
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

  app.post("/api/auth/register", RateLimiter.middleware('register'), async (req, res) => {
    try {
      // Validate password strength first
      const passwordValidation = AuthService.validatePasswordStrength(req.body.password);
      if (!passwordValidation.isValid) {
        return res.status(400).json({ 
          message: "Mot de passe invalide", 
          errors: passwordValidation.errors 
        });
      }

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
      if (!req.session?.userId) {
        return res.status(401).json({ message: "Not authenticated" });
      }

      const user = await storage.getUser(req.session.userId);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      // Remove password from response
      const { password: _, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Auth check error:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post("/api/auth/logout", (req, res) => {
    req.session.destroy((err) => {
      if (err) {
        return res.status(500).json({ message: "Could not log out" });
      }
      res.json({ message: "Logged out successfully" });
    });
  });

  // Statistics endpoint - used by dashboard
  app.get("/api/stats", async (req, res) => {
    try {
      const stats = await storage.getStats();
      res.json(stats);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch stats" });
    }
  });
}