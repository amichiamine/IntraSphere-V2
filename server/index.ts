import express, { type Request, Response, NextFunction } from "express";
import session from "express-session";
import { registerRoutes } from "./routes/api";
import { setupVite, serveStatic, log } from "./vite";
import { configureSecurity, sanitizeInput, getSessionConfig } from "./middleware/security";
import { runMigrations } from "./migrations";
import { ensurePortAvailable, ServerMonitor } from "./utils/process-monitor";

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

      log(logLine);
    }
  });

  next();
});

(async () => {
  // Ensure port is available before starting
  const port = parseInt(process.env.PORT || '5000', 10);
  
  try {
    await ensurePortAvailable(port);
    log(`Port ${port} is available for use`);
  } catch (error: any) {
    log(`Port cleanup failed: ${error.message}`);
    process.exit(1);
  }

  // Run security migrations on startup
  await runMigrations();
  
  const server = await registerRoutes(app);

  app.use((err: any, _req: Request, res: Response, _next: NextFunction) => {
    const status = err.status || err.statusCode || 500;
    const message = err.message || "Internal Server Error";

    res.status(status).json({ message });
    throw err;
  });

  // importantly only setup vite in development and after
  // setting up all the other routes so the catch-all route
  // doesn't interfere with the other routes
  const nodeEnv = process.env.NODE_ENV || "development";
  log(`Environment detected: ${nodeEnv}`);
  
  // Add health check endpoint before Vite setup
  app.get('/health', (_req, res) => {
    res.status(200).json({ 
      status: 'ok', 
      timestamp: new Date().toISOString(),
      port,
      environment: nodeEnv,
      version: '1.0.0'
    });
  });

  if (nodeEnv === "development") {
    log("Setting up Vite development server...");
    await setupVite(app, server);
  } else {
    log("Setting up static file serving...");
    serveStatic(app);
  }

  // ALWAYS serve the app on the port specified in the environment variable PORT
  // Other ports are firewalled. Default to 5000 if not specified.
  // this serves both the API and the client.
  // It is the only port that is not firewalled.
  // Port is already validated and cleaned above

  const httpServer = server.listen({
    port,
    host: "0.0.0.0",
    reusePort: true,
  }, () => {
    log(`serving on port ${port}`);
    log(`Health check available at http://0.0.0.0:${port}/health`);
    
    // Start server monitoring
    const monitor = new ServerMonitor(port);
    monitor.start();
    log('Server monitoring started');
  });

  // Initialize WebSocket after server creation
  try {
    const { initializeWebSocket } = await import("./services/websocket");
    initializeWebSocket(httpServer);
  } catch (error: any) {
    log(`WebSocket initialization skipped: ${error?.message || 'Unknown error'}`);
  }

  // Graceful shutdown handling
  const gracefulShutdown = (signal: string) => {
    log(`Received ${signal}. Graceful shutdown...`);
    httpServer.close((err) => {
      if (err) {
        log(`Error during server shutdown: ${err.message}`);
        process.exit(1);
      }
      log('Server closed successfully.');
      process.exit(0);
    });
  };

  // Handle process termination signals
  process.on('SIGTERM', () => gracefulShutdown('SIGTERM'));
  process.on('SIGINT', () => gracefulShutdown('SIGINT'));

  // Handle uncaught exceptions
  process.on('uncaughtException', (error) => {
    log(`Uncaught Exception: ${error.message}`);
    gracefulShutdown('uncaughtException');
  });

  process.on('unhandledRejection', (reason) => {
    log(`Unhandled Rejection: ${reason}`);
    gracefulShutdown('unhandledRejection');
  });
})().catch((error) => {
  console.error('Failed to start server:', error);
  process.exit(1);
});
