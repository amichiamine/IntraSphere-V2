import type { Express } from "express";
import { createServer, type Server } from "http";
import { registerAuthRoutes } from "./auth";
import { registerUsersRoutes } from "./users";
import { registerContentRoutes } from "./content";
import { registerMessagingRoutes } from "./messaging";
import { registerTrainingRoutes } from "./training";
import { registerAdminRoutes } from "./admin";

export async function registerRoutes(app: Express): Promise<Server> {
  // Register all route modules
  registerAuthRoutes(app);
  registerUsersRoutes(app);
  registerContentRoutes(app);
  registerMessagingRoutes(app);
  registerTrainingRoutes(app);
  registerAdminRoutes(app);

  return createServer(app);
}