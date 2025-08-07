import express, { type Request, Response, NextFunction } from "express";
import session from "express-session";
import path from "path";
import { registerRoutes } from "./routes/api";
import { configureSecurity, sanitizeInput, getSessionConfig } from "./middleware/security";
import { runMigrations } from "./migrations";

const app = express();

// Configure trust proxy for production rate limiting
if (process.env.NODE_ENV === 'production') {
  app.set('trust proxy', true);
}

// Configure security middleware
configureSecurity(app);

app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ extended: false, limit: '50mb' }));

// Input sanitization
app.use(sanitizeInput);

// Session configuration with security
app.use(session(getSessionConfig()));

// Simple logging middleware
app.use((req, res, next) => {
  const start = Date.now();
  const path = req.path;
  let capturedJsonResponse: Record<string, any> | undefined = undefined;

  const originalResJson = res.json;
  res.json = function (bodyJson, ...args) {
    capturedJsonResponse = bodyJson;
    return originalResJson.apply(res, [bodyJson, ...args]);
  };

  res.on("finish", () => {
    const duration = Date.now() - start;
    if (path.startsWith("/api")) {
      let logLine = `${req.method} ${path} ${res.statusCode} in ${duration}ms`;
      if (capturedJsonResponse) {
        logLine += ` :: ${JSON.stringify(capturedJsonResponse)}`;
      }
      if (logLine.length > 80) {
        logLine = logLine.slice(0, 79) + "…";
      }
      console.log(`${new Date().toLocaleTimeString()} [express] ${logLine}`);
    }
  });

  next();
});

// Global error handlers to prevent process crashes
process.on('uncaughtException', (error) => {
  console.error('Uncaught Exception:', error.message);
  // Don't crash the server for any errors
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('Unhandled Rejection at:', promise, 'reason:', reason);
  // Don't crash the server for any rejections
});

(async () => {
  // Run security migrations on startup
  await runMigrations();
  
  const server = await registerRoutes(app);

  // Error handling middleware
  app.use((err: any, _req: Request, res: Response, _next: NextFunction) => {
    const status = err.status || err.statusCode || 500;
    const message = err.message || "Internal Server Error";
    res.status(status).json({ message });
    console.error('Server error:', err.message);
  });

  // Serve static files from build directory
  const staticPath = path.resolve(import.meta.dirname, "../dist/public");
  console.log(`Static files directory: ${staticPath}`);
  
  app.use(express.static(staticPath));
  
  // Catch-all handler: send back React's index.html file
  app.get("*", (_req, res) => {
    res.sendFile(path.resolve(staticPath, "index.html"));
  });

  // Start server
  const port = parseInt(process.env.PORT || '5000', 10);
  const httpServer = server.listen({
    port,
    host: "0.0.0.0",
    reusePort: true,
  }, () => {
    console.log(`${new Date().toLocaleTimeString()} [express] Stable server running on port ${port}`);
    console.log(`${new Date().toLocaleTimeString()} [express] Serving static files from: ${staticPath}`);
  });

  // Initialize WebSocket after server creation
  try {
    const { initializeWebSocket } = await import("./services/websocket");
    initializeWebSocket(httpServer);
    console.log('✅ WebSocket server initialized on /ws');
  } catch (error: any) {
    console.log(`WebSocket initialization skipped: ${error?.message || 'Unknown error'}`);
  }
})();