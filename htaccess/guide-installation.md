# Guide d'installation des fichiers .htaccess pour IntraSphere

## 🎯 Objectif

Ce guide vous explique comment installer correctement les fichiers .htaccess selon votre type d'hébergement cPanel.

## 📋 Avant de commencer

### ✅ Vérifications préalables

1. **Accès cPanel** : Vous devez avoir accès à votre panneau de contrôle cPanel
2. **Gestionnaire de fichiers** : Assurez-vous de pouvoir utiliser le gestionnaire de fichiers
3. **Droits d'écriture** : Vérifiez que vous pouvez créer/modifier des fichiers
4. **Sauvegarde** : Sauvegardez toujours vos fichiers existants avant modification

### 📁 Structure des dossiers

Votre hébergement cPanel aura cette structure :
```
/home/votre-username/
├── public_html/                 # Dossier web public
├── intrasphere_config/          # Configuration privée (à créer)
├── logs/                        # Logs du serveur
└── tmp/                         # Fichiers temporaires
```

## 🚀 Installation selon le type d'hébergement

### Option A : Hébergement traditionnel (PHP + MySQL)

#### 1️⃣ Fichier principal

**Fichier** : `htaccess-principal.txt`  
**Destination** : `public_html/.htaccess`

**Étapes :**
1. Ouvrez le gestionnaire de fichiers dans cPanel
2. Naviguez vers `public_html/`
3. Si un fichier `.htaccess` existe, renommez-le en `.htaccess.backup`
4. Créez un nouveau fichier nommé `.htaccess`
5. Copiez le contenu de `htaccess-principal.txt`
6. Collez-le dans le nouveau `.htaccess`
7. **Adaptez** : Remplacez "votre-domaine.com" par votre vrai domaine

#### 2️⃣ ✅ API - MAINTENANT FONCTIONNELLE

**NOUVEAU** : API PHP complète incluse dans le projet !

**En mode traditionnel cPanel :**
- ✅ **Dossier `api/` complet** - API PHP fonctionnelle
- ✅ **htaccess-api.txt FONCTIONNEL** - Protection et routage API  
- ✅ **Toutes fonctionnalités disponibles** - Identique au mode Node.js

**Installation API :**
1. **Copier** le dossier `api/` vers `public_html/api/`
2. **Copier** `htaccess-api.txt` vers `public_html/api/.htaccess`
3. **Visiter** `/api/install.php` pour l'installation automatique
4. **Tester** l'API via `/api/stats`

**Fonctionnalités API :**
- ✅ Authentification serveur sécurisée
- ✅ Base de données MySQL ou SQLite
- ✅ CRUD complet (annonces, utilisateurs, documents)
- ✅ Sessions et sécurité avancée

#### 3️⃣ Sécurité uploads

**Fichier** : `htaccess-uploads.txt`  
**Destination** : `public_html/uploads/.htaccess`

**Étapes :**
1. Créez le dossier `public_html/uploads/` si il n'existe pas
2. Créez le fichier `uploads/.htaccess`
3. Copiez le contenu de `htaccess-uploads.txt`
4. **Adaptez** : Remplacez "votre-domaine.com" par votre domaine

#### 4️⃣ Protection configuration

**Fichier** : `htaccess-config.txt`  
**Destination** : `intrasphere_config/.htaccess`

**Étapes :**
1. Créez le dossier `intrasphere_config/` à la racine (même niveau que public_html)
2. Créez le fichier `intrasphere_config/.htaccess`
3. Copiez le contenu de `htaccess-config.txt`
4. Définissez les permissions du dossier à 700 (lecture/écriture/exécution pour le propriétaire uniquement)

### Option B : Hébergement Node.js

#### 1️⃣ Redirection vers Node.js

**Fichier** : `htaccess-nodejs.txt`  
**Destination** : `public_html/.htaccess`

**Étapes :**
1. Identifiez le port assigné par votre hébergeur pour Node.js
2. Ouvrez le gestionnaire de fichiers
3. Naviguez vers `public_html/`
4. Créez ou éditez le fichier `.htaccess`
5. Copiez le contenu de `htaccess-nodejs.txt`
6. **IMPORTANT** : Remplacez `localhost:3000` par le bon port
7. **Adaptez** : Changez "votre-domaine.com" par votre domaine

## 🔧 Personnalisation requise

### 🌐 Remplacements obligatoires

Dans **TOUS** les fichiers, remplacez :

- `votre-domaine.com` → Votre vrai nom de domaine
- `votre-username` → Votre nom d'utilisateur cPanel
- `admin@votre-domaine.com` → Votre vraie adresse email
- `localhost:3000` → Le port réel assigné par l'hébergeur (Node.js uniquement)

### 🔐 Configuration IP administrateur

Dans certains fichiers, vous pouvez autoriser votre IP :
```apache
# Remplacez XXX.XXX.XXX.XXX par votre IP
RewriteCond %{REMOTE_ADDR} !^XXX\.XXX\.XXX\.XXX$
```

Pour connaître votre IP : [https://www.whatismyipaddress.com/](https://www.whatismyipaddress.com/)

## 🧪 Tests et vérification

### ✅ Vérifications de base

1. **Site accessible** : Votre site doit toujours être accessible
2. **Pas d'erreur 500** : Si erreur 500, vérifiez la syntaxe des .htaccess
3. **HTTPS fonctionne** : Si vous avez SSL, testez les redirections HTTPS
4. **API répond** : Testez les endpoints `/api/` de votre application

### 🔍 Tests de sécurité

1. **Accès direct interdit** :
   - Testez : `votre-site.com/api/config.php` → doit être bloqué
   - Testez : `votre-site.com/.htaccess` → doit être bloqué

2. **Upload sécurisé** :
   - Testez upload d'un fichier .php → doit être refusé
   - Testez upload d'une image → doit fonctionner

3. **Configuration protégée** :
   - Testez : `votre-site.com/../intrasphere_config/` → doit être bloqué

## 🚨 Dépannage

### Erreur 500 (Internal Server Error)

**Causes courantes :**
- Erreur de syntaxe dans .htaccess
- Module Apache non disponible (ex: mod_rewrite)
- Permissions incorrectes

**Solutions :**
1. Renommez `.htaccess` en `.htaccess.disabled` temporairement
2. Vérifiez les logs d'erreur dans cPanel
3. Testez ligne par ligne en ajoutant progressivement le contenu

### Site non accessible

**Vérifications :**
1. Le domaine pointe bien vers votre hébergement
2. Les fichiers sont dans `public_html/`
3. Le fichier index.html existe
4. Les permissions sont correctes (644 pour les fichiers, 755 pour les dossiers)

### Node.js ne fonctionne pas

**Vérifications :**
1. L'application Node.js est bien démarrée
2. Le port dans .htaccess correspond au port assigné
3. Mod_proxy est disponible chez votre hébergeur
4. Les logs Node.js pour identifier les erreurs

## 📞 Support

### Logs utiles à vérifier

- **Logs Apache** : `/home/username/logs/error_log`
- **Logs accès** : `/home/username/logs/access_log`
- **Logs personnalisés** : Selon vos configurations

### Informations à fournir au support

1. Type d'hébergement (traditionnel/Node.js)
2. Message d'erreur exact
3. Contenu du fichier .htaccess problématique
4. Logs d'erreur récents

## 🔄 Mode maintenance

Pour activer temporairement la maintenance :

1. Renommez `.htaccess` actuel en `.htaccess.backup`
2. Utilisez `htaccess-maintenance.txt` comme nouveau `.htaccess`
3. Créez une page `maintenance.html`
4. Pour réactiver : restaurez `.htaccess.backup`

---

## 📚 Ressources supplémentaires

- **Manuel complet** : `docs/manuel-deploiement-cpanel.php`
- **Documentation technique** : Dossier `docs/`
- **Support cPanel** : Interface d'aide de votre hébergeur

---

⚠️ **Important** : Testez toujours sur un environnement de test avant la production !