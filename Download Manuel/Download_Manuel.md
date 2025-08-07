# 📋 Instructions de Téléchargement Manuel

## 🎯 Package Principal

**Téléchargez :** `intrasphere-universal-ready.zip` (154MB)

## 🚀 Démarrage Rapide

### Étapes de Base
1. **Téléchargez** le fichier ZIP depuis ce dossier
2. **Décompressez** dans votre répertoire de travail
3. **Ouvrez** `deploy-universal.php` dans votre navigateur
4. **Suivez** l'assistant graphique

### URLs d'Accès Typiques
- **Développement Local :** http://localhost:5000
- **cPanel Hébergement :** https://votre-domaine.com/intrasphere
- **Serveur Dédié :** http://votre-ip:5000

## 🔧 Déploiement par Environnement

### cPanel (Hébergement Web)
```bash
# Upload via File Manager cPanel
# Extraire dans public_html/intrasphere/
# Ouvrir deploy-universal.php dans navigateur
# Suivre l'assistant
```

### Windows (Local)
```cmd
# Extraire le ZIP
# Double-cliquer start-windows.bat
# Ou: ouvrir deploy-universal.php
```

### Linux/Mac
```bash
# Extraire le ZIP
chmod +x start-linux.sh
./start-linux.sh
# Ou: ouvrir deploy-universal.php
```

## ✅ Points Clés v2.1

- **Structure Directe :** Les fichiers s'extraient directement (pas de sous-dossier)
- **Interface Corrigée :** Formulaires et tests de connexion fonctionnels
- **Fichiers Publics :** Copie automatique vers le bon répertoire
- **Guide Intégré :** Documentation accessible dans l'interface

## 💡 En Cas de Problème

1. **Vérifiez** que Node.js 16+ est installé
2. **Utilisez** l'interface deploy-universal.php
3. **Choisissez** SQLite si problème de base de données
4. **Consultez** les guides dans l'interface

---
**Support :** Utilisez l'assistant graphique intégré