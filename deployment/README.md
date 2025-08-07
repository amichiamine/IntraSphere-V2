# 🚀 Déploiement Multi-Environnement IntraSphere

## Scripts de Déploiement Automatisés

### 🏠 local/ - Développement Local
- `setup-windows.bat` - Installation Windows  
- `setup-linux.sh` - Installation Linux/macOS
- `dev-start.sh` - Démarrage environnement développement

### 🌐 cpanel/ - Hébergement Web
- `build-static.sh` - Build pour hébergement sans Node.js
- `build-nodejs.sh` - Build pour hébergement avec Node.js  
- `deploy-files.sh` - Upload automatique via FTP

### 🖥️ production/ - Serveurs Dédiés
- `deploy-pm2.sh` - Déploiement avec PM2
- `deploy-docker.sh` - Déploiement containerisé
- `backup-database.sh` - Sauvegarde automatique

## Configuration par Environnement
Chaque dossier contient les configurations spécifiques et les scripts d'installation pour son environnement cible.