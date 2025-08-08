import { createServer as createViteServer } from "vite";
import express from "express";
import path from "path";

export async function setupVite(app, server) {
  if (process.env.NODE_ENV === "production") {
    serveStatic(app);
  } else {
    const vite = await createViteServer({
      server: { 
        middlewareMode: true,
        host: '0.0.0.0',
        port: 5000
      },
      appType: "spa",
      configFile: path.resolve(process.cwd(), "vite.config.ts")
    });
    
    app.use(vite.ssrFixStacktrace);
    
    // Don't handle root here - let Express handle it before Vite middleware
    
    app.use(vite.middlewares);
  }
}

export function serveStatic(app) {
  // Serve static files from the dist directory first, then fallback to server/public
  const distPath = path.resolve(process.cwd(), "dist/public");
  const serverPublicPath = path.resolve(process.cwd(), "server/public");
  
  // Try dist first (production build), then server/public
  app.use(express.static(distPath));
  app.use(express.static(serverPublicPath));
  
  app.get("*", (req, res, next) => {
    if (req.path.startsWith("/api") || req.path.startsWith("/health") || req.path.startsWith("/ws")) {
      return next();
    }
    
    // Try to serve from dist first, then server/public
    const fs = require('fs');
    const distIndex = path.join(distPath, "index.html");
    const serverIndex = path.join(serverPublicPath, "index.html");
    
    if (fs.existsSync(distIndex)) {
      res.sendFile(distIndex);
    } else if (fs.existsSync(serverIndex)) {
      res.sendFile(serverIndex);
    } else {
      res.status(404).send('Application files not found');
    }
  });
}

export function log(message) {
  console.log(message);
}