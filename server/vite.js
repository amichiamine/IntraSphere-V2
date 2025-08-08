import { createServer as createViteServer } from "vite";
import express from "express";
import path from "path";

export async function setupVite(app, server) {
  if (process.env.NODE_ENV === "production") {
    serveStatic(app);
  } else {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
      configFile: path.resolve(process.cwd(), "vite.config.ts")
    });
    
    app.use(vite.ssrFixStacktrace);
    
    // Simple direct HTML serving for root
    app.get('/', (req, res) => {
      res.send(`
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1" />
    <title>IntraSphere</title>
    <script type="module" src="/@vite/client"></script>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.tsx"></script>
  </body>
</html>
      `);
    });
    
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