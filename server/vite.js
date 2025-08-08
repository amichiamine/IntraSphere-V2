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
    app.use(vite.middlewares);
  }
}

export function serveStatic(app) {
  const distPath = path.resolve(process.cwd(), "server/public");
  app.use(express.static(distPath));
  
  app.get("*", (req, res, next) => {
    if (req.path.startsWith("/api") || req.path.startsWith("/health") || req.path.startsWith("/ws")) {
      return next();
    }
    res.sendFile(path.join(distPath, "index.html"));
  });
}

export function log(message) {
  console.log(message);
}