# Guide de Déploiement IntraSphere v2.1

## 📋 Prérequis

### Serveur
- **Node.js**: Version 18 ou supérieure
- **PostgreSQL**: Version 13 ou supérieure (optionnel)
- **Mémoire**: Minimum 1GB RAM
- **Espace disque**: 2GB minimum

### Domaine et SSL
- Nom de domaine configuré
- Certificat SSL/TLS (Let's Encrypt recommandé)

## 🔧 Configuration Environnement

### Variables d'Environnement Obligatoires

```bash
# Configuration serveur
NODE_ENV=production
PORT=5000

# Sécurité
SESSION_SECRET=votre-clé-secrète-très-sécurisée
BCRYPT_SALT_ROUNDS=12

# Base de données (optionnel - utilise SQLite par défaut)
DATABASE_URL=postgresql://user:password@localhost:5432/intrasphere

# SMTP Email (optionnel)
SMTP_ENABLED=true
SMTP_HOST=smtp.votre-provider.com
SMTP_PORT=587
SMTP_SECURE=false
SMTP_USER=votre-email@domaine.com
SMTP_PASS=votre-mot-de-passe-email
EMAIL_FROM=noreply@votre-domaine.com
```

### Variables d'Environnement Optionnelles

```bash
# Upload de fichiers
MAX_FILE_SIZE=10485760
ALLOWED_FILE_TYPES=.pdf,.doc,.docx,.jpg,.png,.gif
STORAGE_TYPE=local
STORAGE_PATH=./uploads

# Fonctionnalités
ENABLE_REGISTRATION=true
ENABLE_EMAIL_NOTIFICATIONS=true
ENABLE_FILE_UPLOAD=true
ENABLE_FORUM=true
ENABLE_TRAINING=true
```

## 🚀 Déploiement Production

### 1. Préparation du Serveur

```bash
# Mise à jour du système
sudo apt update && sudo apt upgrade -y

# Installation Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Installation PostgreSQL (optionnel)
sudo apt install postgresql postgresql-contrib -y
```

### 2. Déploiement de l'Application

```bash
# Clone du projet
git clone https://github.com/votre-repo/intrasphere.git
cd intrasphere

# Installation des dépendances
npm install --production

# Build de l'application
npm run build

# Configuration des variables d'environnement
cp .env.example .env.production
nano .env.production
# Configurez toutes les variables nécessaires
```

### 3. Configuration Base de Données

```bash
# Si PostgreSQL est utilisé
sudo -u postgres createdb intrasphere
sudo -u postgres createuser --interactive

# Migration automatique au démarrage
# (Les tables seront créées automatiquement)
```

### 4. Configuration SSL et Nginx

```nginx
# /etc/nginx/sites-available/intrasphere
server {
    listen 80;
    server_name votre-domaine.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name votre-domaine.com;
    
    ssl_certificate /path/to/your/certificate.pem;
    ssl_certificate_key /path/to/your/private.key;
    
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

### 5. Service Systemd

```ini
# /etc/systemd/system/intrasphere.service
[Unit]
Description=IntraSphere Application
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/intrasphere
Environment=NODE_ENV=production
EnvironmentFile=/path/to/intrasphere/.env.production
ExecStart=/usr/bin/node server/index.js
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
```

```bash
# Activation du service
sudo systemctl daemon-reload
sudo systemctl enable intrasphere
sudo systemctl start intrasphere
sudo systemctl status intrasphere
```

## 📧 Configuration Email SMTP

### Providers Recommandés

**1. SendGrid**
```bash
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_USER=apikey
SMTP_PASS=votre-api-key-sendgrid
```

**2. Gmail**
```bash
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=votre-email@gmail.com
SMTP_PASS=mot-de-passe-application
```

**3. Mailgun**
```bash
SMTP_HOST=smtp.mailgun.org
SMTP_PORT=587
SMTP_USER=postmaster@votre-domaine.mailgun.org
SMTP_PASS=votre-clé-mailgun
```

### Test de Configuration Email

```bash
# Depuis le serveur
curl -X POST http://localhost:5000/api/test-email \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com"}'
```

## 🛡️ Sécurité Production

### 1. Firewall
```bash
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### 2. Sauvegarde Automatique
```bash
# Script de sauvegarde
#!/bin/bash
# /etc/cron.daily/backup-intrasphere

# Base de données
pg_dump intrasphere > /backup/intrasphere-$(date +%Y%m%d).sql

# Fichiers uploadés
tar -czf /backup/uploads-$(date +%Y%m%d).tar.gz /path/to/intrasphere/uploads

# Nettoyage (garder 30 jours)
find /backup -name "*.sql" -mtime +30 -delete
find /backup -name "*.tar.gz" -mtime +30 -delete
```

### 3. Monitoring
```bash
# Installation PM2 pour monitoring
npm install -g pm2

# Démarrage avec PM2
pm2 start ecosystem.config.js
pm2 startup
pm2 save
```

## 🔍 Vérification Post-Déploiement

### Checklist de Vérification

- [ ] ✅ Application accessible via HTTPS
- [ ] ✅ Connexion admin fonctionnelle (admin/admin123!)
- [ ] ✅ Upload de fichiers opérationnel
- [ ] ✅ Envoi d'emails configuré
- [ ] ✅ Base de données migrée
- [ ] ✅ SSL/TLS configuré
- [ ] ✅ Firewall activé
- [ ] ✅ Sauvegarde programmée
- [ ] ✅ Monitoring actif

### Tests Fonctionnels

```bash
# Test de l'API
curl -I https://votre-domaine.com/api/auth/me

# Test de l'interface
curl -I https://votre-domaine.com

# Test email (si configuré)
curl -X POST https://votre-domaine.com/api/test-email \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@votre-domaine.com"}'
```

## 🚨 Dépannage

### Problèmes Courants

**1. Erreur de démarrage**
```bash
# Vérifier les logs
sudo journalctl -u intrasphere -f

# Vérifier la configuration
node -e "console.log(require('./server/config.js'))"
```

**2. Problème de base de données**
```bash
# Vérifier la connexion PostgreSQL
psql -U user -d intrasphere -c "SELECT 1;"

# Migration manuelle si nécessaire
npm run migrate
```

**3. Problème d'emails**
```bash
# Test SMTP
telnet smtp.votre-provider.com 587
```

## 📞 Support

En cas de problème, vérifiez :

1. **Logs d'application** : `sudo journalctl -u intrasphere`
2. **Logs Nginx** : `sudo tail -f /var/log/nginx/error.log`
3. **Status des services** : `sudo systemctl status intrasphere nginx postgresql`

---

**Version** : v2.1  
**Dernière mise à jour** : Août 2025  
**Support** : Consultez la documentation technique complète