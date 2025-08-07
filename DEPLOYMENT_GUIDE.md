# 🚀 IntraSphere - Guide de Déploiement Multi-Environnement

## 🎯 Structure Organisée Option R3

### 📁 Architecture Optimisée
```
IntraSphere/
├── 📁 client/                  → Frontend React (développement/production)
│   ├── 📁 src/
│   │   ├── 📁 components/      → Composants UI réutilisables
│   │   │   ├── 📁 ui/          → shadcn/ui components  
│   │   │   ├── 📁 layout/      → Navigation, Header, Sidebar
│   │   │   └── 📁 dashboard/   → Widgets métier
│   │   ├── 📁 pages/           → Pages par fonctionnalité
│   │   │   ├── 📁 auth/        → Login, Settings
│   │   │   ├── 📁 admin/       → Administration
│   │   │   ├── 📁 content/     → Contenu, Documents
│   │   │   └── 📁 training/    → Formation
│   │   ├── 📁 hooks/           → React hooks personnalisés
│   │   └── 📁 lib/             → Utilitaires client
│   └── index.html              → Point d'entrée HTML
├── 📁 server/                  → Backend Node.js/Express
│   ├── 📁 routes/              → API endpoints par domaine
│   ├── 📁 middleware/          → Auth, Security, Logging
│   ├── 📁 services/            → Logique métier
│   └── 📁 data/                → Storage, Migrations
├── 📁 shared/                  → Types TypeScript partagés
├── 📁 deployment/              → Scripts de déploiement
│   ├── 📁 local/               → Windows/Linux/macOS
│   ├── 📁 vscode/              → Configuration VS Code
│   ├── 📁 cpanel/              → Hébergement web
│   └── 📁 production/          → Serveurs dédiés
└── 📁 config/                  → Configurations centralisées
```

## 🔧 Environnements de Déploiement

### 🖥️ **1. Développement Local (Windows/Linux/macOS)**

#### Installation
```bash
# Clone/Download du projet
git clone <repository> && cd IntraSphere

# Installation dépendances
npm install

# Configuration base de données (optionnel)
npm run db:push

# Démarrage développement
npm run dev
```

#### Configuration VS Code
```json
// .vscode/settings.json
{
  "typescript.preferences.path": "./node_modules/typescript/lib",
  "eslint.workingDirectories": ["./client", "./server"],
  "editor.defaultFormatter": "esbenp.prettier-vscode"
}
```

### 🌐 **2. Hébergement Web cPanel**

#### Option A: cPanel avec Node.js
```bash
# Upload via File Manager ou FTP
# Configuration Node.js App
# Entry Point: server/index.ts
# Variables d'environnement dans .env
NODE_ENV=production
DATABASE_URL=mysql://user:pass@localhost/db
```

#### Option B: cPanel sans Node.js (Static)
```bash
# Build production
npm run build

# Upload dossier dist/ vers public_html/
# Configuration .htaccess pour SPA
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.html [L]
```

### 🖥️ **3. Serveur Production (Linux/Ubuntu)**

#### Avec PM2
```bash
# Installation globale
npm install -g pm2

# Configuration production
NODE_ENV=production npm run build
pm2 start server/index.ts --name "intrasphere"
pm2 startup
pm2 save
```

#### Avec Docker (optionnel)
```dockerfile
FROM node:20-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --production
COPY . .
RUN npm run build
EXPOSE 5000
CMD ["npm", "start"]
```

## 🗄️ Base de Données Multi-Environnement

### Développement
- **SQLite** (par défaut) - Fichier local
- **PostgreSQL** (Neon) - Cloud développement

### Production  
- **MySQL** (cPanel/Hébergement web)
- **PostgreSQL** (Serveurs dédiés)
- **SQLite** (Déploiement simple)

## 📦 Scripts de Déploiement Automatisés

### build-all.sh
```bash
#!/bin/bash
# Build pour tous environnements
npm run build
npm run build:server
echo "✅ Build complet terminé"
```

### deploy-cpanel.sh
```bash
#!/bin/bash
# Déploiement automatique cPanel
npm run build
zip -r intrasphere-production.zip dist/ server/ package.json
echo "📦 Package prêt pour cPanel"
```

### deploy-local.bat (Windows)
```bat
@echo off
npm install
npm run db:push
npm run dev
pause
```

## 🔑 Variables d'Environnement

### .env.development
```env
NODE_ENV=development
DATABASE_URL=file:./dev.db
PORT=5000
VITE_API_URL=http://localhost:5000
```

### .env.production
```env
NODE_ENV=production  
DATABASE_URL=mysql://user:pass@host/db
PORT=5000
TRUST_PROXY=true
SESSION_SECRET=your-secret-key
```

## 🚦 Tests de Déploiement

### Vérification API
```bash
curl http://localhost:5000/api/stats
# Réponse attendue: {"totalUsers": X, ...}
```

### Vérification Frontend
```bash
curl http://localhost:5173
# Réponse: Page HTML React
```

## 🛠️ Maintenance & Monitoring

### Logs Production
```bash
# PM2 logs
pm2 logs intrasphere

# Logs système
tail -f /var/log/nodejs/app.log
```

### Sauvegarde Base de Données
```bash
# MySQL
mysqldump -u user -p database > backup.sql

# PostgreSQL  
pg_dump DATABASE_URL > backup.sql
```

## 🎯 Recommandations par Environnement

### 🏠 **Développement Local**
- ✅ Structure complète client/server
- ✅ Hot reload Vite + Nodemon
- ✅ SQLite pour simplicité
- ✅ Configuration VS Code optimisée

### 🌐 **Hébergement Web (cPanel)**
- ✅ Build statique si pas Node.js
- ✅ MySQL partagé
- ✅ Variables via .env
- ✅ Monitoring basique

### 🖥️ **Production (Serveurs)**
- ✅ PM2 + Nginx reverse proxy
- ✅ PostgreSQL dédié
- ✅ SSL/TLS automatique
- ✅ Monitoring avancé

---

**Structure Option R3 optimisée pour tous environnements de déploiement !**