# 🚀 Guide de Déploiement Universel IntraSphere

## 📋 Vue d'Ensemble

Ce guide vous accompagne pour déployer IntraSphere sur n'importe quel environnement en quelques minutes.

## 🎯 Options de Déploiement

### 🌟 Package Universel Ready (Recommandé)

Le package universel est la solution la plus simple : tout est inclus et pré-configuré.

#### Contenu du Package
- ✅ **Application complète** buildée et prête
- ✅ **node_modules inclus** (pas besoin de npm install)
- ✅ **Scripts multi-plateforme** (Windows, Linux, Mac)
- ✅ **Déployeur universel PHP** avec interface graphique
- ✅ **Support multi-base** (SQLite, MySQL, PostgreSQL)
- ✅ **Documentation complète** pour débutants

#### Utilisation
1. **Télécharger** `intrasphere-universal-ready.zip`
2. **Décompresser** dans votre répertoire souhaité
3. **Ouvrir** `deploy-universal.php` dans un navigateur
4. **Suivre** l'assistant graphique
5. **Accéder** à votre application

### 🔧 Package de Développement

Pour les développeurs qui souhaitent modifier le code.

#### Prérequis
- Node.js 16+
- npm ou yarn
- Git (optionnel)

#### Installation
```bash
# Cloner ou télécharger le code source
npm install
npm run dev
```

## 🌍 Déploiement par Environnement

### 🏢 cPanel Node.js (Hébergement Web)

#### Méthode 1 : Package Universel
1. **Upload** du package via File Manager cPanel
2. **Décompresser** dans `public_html/intrasphere/`
3. **Ouvrir** `deploy-universal.php` dans le navigateur
4. **Suivre** l'assistant pour configuration cPanel
5. **Accéder** via votre domaine

#### Méthode 2 : Configuration Manuelle
1. **Créer une application Node.js** dans cPanel
2. **Définir le répertoire** de l'application
3. **Configurer la version** Node.js (18+)
4. **Démarrer** l'application

#### Configuration cPanel Spécifique
- **Port** : Auto-détecté par cPanel
- **Base de données** : MySQL (généralement disponible)
- **SSL** : Configuré automatiquement
- **Domaine** : Configuré selon votre hébergement

### 💻 Windows (Local/Serveur)

#### Package Universel
```cmd
# Décompresser le package
# Double-cliquer start-windows.bat
# Ou via PowerShell :
.\start-windows.bat
```

#### Installation Manuelle
```cmd
# Installer Node.js depuis nodejs.org
# Télécharger le code source
npm install
npm run build
npm run dev
```

#### Configuration Windows
- **Port** : 5000 (par défaut)
- **Base** : SQLite (recommandé) ou SQL Server
- **Service** : Configurable comme service Windows
- **Firewall** : Autoriser le port 5000

### 🐧 Linux (Ubuntu/CentOS/Debian)

#### Package Universel
```bash
# Décompresser le package
chmod +x start-linux.sh
./start-linux.sh
```

#### Installation avec PM2 (Production)
```bash
# Installation des prérequis
sudo apt update
sudo apt install nodejs npm

# Décompression et configuration
cd /path/to/intrasphere/
npm install -g pm2
pm2 start start.js --name "intrasphere"
pm2 startup
pm2 save
```

#### Configuration Linux
- **Service** : Systemd ou PM2
- **Proxy** : Nginx ou Apache recommandé
- **SSL** : Let's Encrypt ou certificat personnalisé
- **Monitoring** : PM2, htop, ou solutions custom

### 🔨 VS Code (Développement)

#### Configuration Développement
1. **Ouvrir** le dossier projet dans VS Code
2. **Installer** les extensions recommandées
3. **Terminal intégré** : `npm run dev`
4. **Hot reload** activé automatiquement

#### Extensions Recommandées
- **TypeScript et JavaScript** - Support langue
- **ES7+ React/Redux** - Snippets React
- **Tailwind CSS IntelliSense** - Support CSS
- **Prettier** - Formatage de code
- **ESLint** - Analyse de code

### 🐳 Docker (Optionnel)

#### Dockerfile Optimisé
```dockerfile
FROM node:18-alpine

WORKDIR /app

# Copier les fichiers de dépendances
COPY package*.json ./
RUN npm ci --only=production

# Copier le code source
COPY . .

# Build si nécessaire
RUN npm run build

# Exposer le port
EXPOSE 5000

# Utilisateur non-root pour sécurité
USER node

# Commande de démarrage
CMD ["npm", "start"]
```

#### Docker Compose
```yaml
version: '3.8'
services:
  intrasphere:
    build: .
    ports:
      - "5000:5000"
    environment:
      - NODE_ENV=production
      - DATABASE_URL=sqlite:./data/database.sqlite
    volumes:
      - ./data:/app/data
    restart: unless-stopped
```

## 🗄️ Configuration Base de Données

### 📄 SQLite (Recommandé pour Débuter)

#### Avantages
- ✅ **Aucune configuration** requise
- ✅ **Fonctionne immédiatement**
- ✅ **Parfait pour tests** et petites équipes
- ✅ **Fichier unique** facile à sauvegarder

#### Configuration
```json
{
  "database": {
    "type": "sqlite",
    "file": "./database.sqlite"
  }
}
```

### 🐬 MySQL (Production)

#### Prérequis
- Serveur MySQL ou MariaDB
- Base de données créée
- Utilisateur avec permissions appropriées

#### Configuration
```json
{
  "database": {
    "type": "mysql",
    "host": "localhost",
    "port": 3306,
    "database": "intrasphere",
    "username": "intrasphere_user",
    "password": "secure_password"
  }
}
```

#### Setup MySQL
```sql
-- Créer la base de données
CREATE DATABASE intrasphere CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Créer l'utilisateur
CREATE USER 'intrasphere_user'@'localhost' IDENTIFIED BY 'secure_password';

-- Donner les permissions
GRANT ALL PRIVILEGES ON intrasphere.* TO 'intrasphere_user'@'localhost';
FLUSH PRIVILEGES;
```

### 🐘 PostgreSQL (Avancé)

#### Prérequis
- Serveur PostgreSQL
- Base et utilisateur configurés

#### Configuration
```json
{
  "database": {
    "type": "postgresql",
    "host": "localhost",
    "port": 5432,
    "database": "intrasphere",
    "username": "intrasphere_user",
    "password": "secure_password"
  }
}
```

#### Setup PostgreSQL
```sql
-- Créer l'utilisateur
CREATE USER intrasphere_user WITH ENCRYPTED PASSWORD 'secure_password';

-- Créer la base de données
CREATE DATABASE intrasphere OWNER intrasphere_user;

-- Donner les permissions
GRANT ALL PRIVILEGES ON DATABASE intrasphere TO intrasphere_user;
```

## 🔧 Configuration Avancée

### Variables d'Environnement

#### Fichier .env
```bash
# Configuration principale
NODE_ENV=production
PORT=5000
HOST=0.0.0.0

# Base de données
DATABASE_URL=sqlite:./database.sqlite
# ou DATABASE_URL=mysql://user:pass@localhost:3306/intrasphere
# ou DATABASE_URL=postgresql://user:pass@localhost:5432/intrasphere

# Sécurité
SESSION_SECRET=your-very-long-random-secret-key
JWT_SECRET=another-very-long-random-secret-key

# Upload et stockage
UPLOAD_MAX_SIZE=10485760
STORAGE_PATH=./uploads

# Email (optionnel)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
```

### Configuration Nginx (Proxy)

```nginx
server {
    listen 80;
    server_name votre-domaine.com;

    location / {
        proxy_pass http://localhost:5000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
}
```

### Configuration Apache (Proxy)

```apache
<VirtualHost *:80>
    ServerName votre-domaine.com
    
    ProxyPass / http://localhost:5000/
    ProxyPassReverse / http://localhost:5000/
    
    ProxyPreserveHost On
    ProxyRequests Off
</VirtualHost>
```

## 🚨 Résolution de Problèmes

### Erreurs Communes

#### "Node.js not found"
```bash
# Vérifier l'installation
node --version
npm --version

# Solutions :
# 1. Installer Node.js depuis nodejs.org
# 2. Vérifier le PATH système
# 3. Redémarrer le terminal/session
```

#### "Port already in use"
```bash
# Identifier le processus utilisant le port
netstat -tulpn | grep :5000
# ou
lsof -i :5000

# Arrêter le processus
kill -9 <PID>

# Ou changer le port dans la configuration
```

#### "Database connection failed"
```bash
# Vérifications :
# 1. Service de base de données actif
# 2. Credentials corrects
# 3. Permissions d'accès
# 4. Firewall/réseau

# Test de connexion
mysql -h localhost -u intrasphere_user -p
# ou
psql -h localhost -U intrasphere_user -d intrasphere
```

#### "Permission denied"
```bash
# Linux/Mac : ajuster les permissions
chmod -R 755 /path/to/intrasphere/
chown -R user:group /path/to/intrasphere/

# Vérifier les permissions de base de données
ls -la database.sqlite
```

### Diagnostic Avancé

#### Mode Debug
```bash
# Activer les logs détaillés
DEBUG=* npm run dev

# Logs spécifiques
DEBUG=express:* npm run dev
DEBUG=drizzle:* npm run dev
```

#### Health Check
```bash
# Vérifier l'état de l'application
curl http://localhost:5000/health

# Test des composants
npm run test:health
npm run test:database
```

## 📊 Monitoring et Maintenance

### Monitoring Production

#### PM2 (Linux)
```bash
# Status des processus
pm2 status

# Monitoring en temps réel
pm2 monit

# Logs
pm2 logs intrasphere

# Redémarrage
pm2 restart intrasphere
```

#### Logs et Métriques
```bash
# Logs d'application
tail -f logs/application.log

# Utilisation système
htop
df -h
free -h
```

### Sauvegardes

#### Base de Données
```bash
# SQLite
cp database.sqlite backup/database-$(date +%Y%m%d).sqlite

# MySQL
mysqldump -u user -p intrasphere > backup/intrasphere-$(date +%Y%m%d).sql

# PostgreSQL
pg_dump -h localhost -U user intrasphere > backup/intrasphere-$(date +%Y%m%d).sql
```

#### Fichiers Uploadés
```bash
# Sauvegarde des uploads
tar -czf backup/uploads-$(date +%Y%m%d).tar.gz uploads/

# Synchronisation vers stockage distant
rsync -av uploads/ user@backup-server:/backups/intrasphere-uploads/
```

### Mises à Jour

#### Mise à Jour Application
```bash
# Sauvegarder avant mise à jour
cp -r . ../intrasphere-backup-$(date +%Y%m%d)/

# Télécharger nouvelle version
# Remplacer les fichiers (sauf config et data)
# Redémarrer l'application

pm2 restart intrasphere
```

#### Migration Base de Données
```bash
# Exécuter les migrations
npm run db:migrate

# Ou pousser le nouveau schéma
npm run db:push
```

Ce guide couvre tous les aspects du déploiement d'IntraSphere. Pour des besoins spécifiques, consultez la documentation technique détaillée.