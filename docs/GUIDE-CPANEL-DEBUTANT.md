# 🖥️ Guide cPanel pour Débutants - Déploiement IntraSphere

## 🎯 Deux Scénarios de Déploiement cPanel

### Scénario 1 : cPanel avec Support Node.js (Recommandé)
### Scénario 2 : cPanel sans Support Node.js (Hébergement Traditionnel)

---

## 📋 Prérequis - Vérification de votre Hébergement

### Comment Savoir Si Votre cPanel Supporte Node.js ?

#### Méthode 1 : Vérification dans cPanel
1. **Connectez-vous à votre cPanel**
2. **Cherchez** une section nommée :
   - "Node.js" ou "Node.js Selector"
   - "Software" → "Node.js App"
   - "Select Node.js Version"
3. **Si vous trouvez** cette section → **Scénario 1** (Node.js supporté)
4. **Si absent** → **Scénario 2** (hébergement traditionnel)

#### Méthode 2 : Demander à votre Hébergeur
**Contactez** votre support technique avec cette question exacte :
> "Mon hébergement cPanel supporte-t-il les applications Node.js ? Puis-je créer des applications Node.js App dans mon cPanel ?"

---

# 🚀 SCÉNARIO 1 : cPanel avec Support Node.js

## 📥 Étape 1 : Téléchargement et Upload

### 1.1 Télécharger le Package
- **Option A** : Dossier `Download Manuel/intrasphere-universal-ready.zip`
- **Option B** : Générez localement avec `./development/create-universal-ready-package.sh`
- **Option C** : Depuis GitHub Releases *(lien sera ajouté dans une release)*
- **Taille** : environ 155MB
- **Fichier** : `intrasphere-universal-ready.zip`

### 1.2 Upload via File Manager cPanel
1. **Connectez-vous** à votre **cPanel**
2. **Cliquez** sur **"File Manager"** (Gestionnaire de fichiers)
3. **Naviguez** vers le répertoire **`public_html/`**
4. **Créez** un nouveau dossier :
   - **Clic droit** → **"Create Folder"**
   - **Nom** : `intrasphere` (ou le nom de votre choix)
   - **Cliquez** "Create Folder"
5. **Entrez** dans le dossier `intrasphere/`
6. **Upload** du package :
   - **Cliquez** sur **"Upload"** (en haut)
   - **Sélectionnez** `intrasphere-universal-ready.zip`
   - **Attendez** la fin de l'upload (peut prendre 2-5 minutes)

### 1.3 Décompression du Package
1. **Retournez** au File Manager
2. **Clic droit** sur `intrasphere-universal-ready.zip`
3. **Sélectionnez** "Extract" ou "Unzip"
4. **Destination** : Le dossier courant (`/public_html/intrasphere/`)
5. **Cliquez** "Extract Files"
6. **Attendez** la décompression (1-2 minutes)

**✅ Résultat** : Vous devriez voir tous les fichiers décompressés dans votre dossier.

## 🔧 Étape 2 : Configuration de l'Application Node.js

### 2.1 Création de l'Application Node.js
1. **Retournez** au tableau de bord cPanel
2. **Cherchez** et **cliquez** sur :
   - **"Node.js"** ou **"Node.js App"** ou **"Select Node.js Version"**
3. **Cliquez** sur **"Create Application"** ou **"+"**

### 2.2 Configuration de l'Application
Remplissez les champs suivants **exactement** :

#### Paramètres Obligatoires
- **Node.js Version** : Sélectionnez **18.x** ou **20.x** (version la plus récente disponible)
- **Application Mode** : **Production** (ou Development pour tests)
- **Application Root** : `/public_html/intrasphere/` 
  ⚠️ **ATTENTION** : Chemin exact vers votre dossier décompressé
- **Application URL** : `intrasphere` (ou laissez vide si domaine principal)
- **Application Startup File** : `server/index.js`

#### Paramètres Optionnels
- **Environment Variables** : Laissez vide pour l'instant
- **Passenger Log File** : Laissez par défaut

### 2.3 Création et Démarrage
1. **Cliquez** "Create" pour créer l'application
2. **Attendez** la création (30 secondes à 2 minutes)
3. **Vérifiez** que le statut est "Running" ou démarrez l'application

## 🗄️ Étape 3 : Configuration de la Base de Données

### 3.1 Option A : Configuration Automatique via Interface
1. **Ouvrez** votre navigateur
2. **Accédez** à : `https://votre-domaine.com/intrasphere/deploy-universal.php`
   - Remplacez `votre-domaine.com` par votre vrai domaine
   - Exemple : `https://monsite.fr/intrasphere/deploy-universal.php`
3. **Suivez** l'assistant graphique qui s'affiche

### 3.2 Option B : Configuration Manuelle MySQL

#### 3.2.1 Créer la Base de Données MySQL
1. **Retournez** au cPanel
2. **Cliquez** sur **"MySQL Databases"**
3. **Créez** une nouvelle base :
   - **Database Name** : `intrasphere` (ou votre choix)
   - **Cliquez** "Create Database"
4. **Créez** un utilisateur :
   - **Username** : `intrasphere_user`
   - **Password** : Choisissez un mot de passe sécurisé (notez-le !)
   - **Cliquez** "Create User"
5. **Associez** l'utilisateur à la base :
   - **Sélectionnez** l'utilisateur et la base
   - **Cochez** "All Privileges"
   - **Cliquez** "Make Changes"

#### 3.2.2 Noter les Informations de Connexion
**Notez** ces informations (vous en aurez besoin) :
- **Host** : `localhost`
- **Port** : `3306`
- **Database** : `cpanelusername_intrasphere` (exemple)
- **Username** : `cpanelusername_intrasphere_user` (exemple)
- **Password** : Votre mot de passe choisi

*Note : cPanel ajoute automatiquement votre nom d'utilisateur cPanel comme préfixe*

## 🌐 Étape 4 : Accès Final

### 4.1 URL d'Accès
Votre application sera accessible à :
- **URL Principale** : `https://votre-domaine.com/intrasphere/`
- **Ou Sous-domaine** : `https://intrasphere.votre-domaine.com/` (si configuré)

### 4.2 Première Connexion
1. **Ouvrez** l'URL dans votre navigateur
2. **Page de connexion** devrait s'afficher
3. **Identifiants par défaut** :
   - **Utilisateur** : `admin`
   - **Mot de passe** : `admin`
4. **Changez** immédiatement le mot de passe

### 4.3 Vérification du Fonctionnement
- ✅ **Page d'accueil** se charge
- ✅ **Connexion** fonctionne
- ✅ **Menu navigation** est accessible
- ✅ **Pas d'erreurs** dans la console navigateur

---

# 💻 SCÉNARIO 2 : cPanel sans Support Node.js

⚠️ **LIMITATION IMPORTANTE** : Sans Node.js, vous ne pouvez pas faire fonctionner IntraSphere directement car c'est une application Node.js.

## 🔄 Solutions Alternatives

### Solution 1 : Mise à Niveau de l'Hébergement
**Contactez** votre hébergeur pour :
- **Passer** à un plan avec support Node.js
- **Activer** Node.js sur votre plan actuel
- **Migrer** vers un VPS avec Node.js

### Solution 2 : Hébergement Externe Spécialisé
**Considérez** ces options :
- **Heroku** - Hébergement Node.js gratuit/payant
- **Vercel** - Spécialisé dans les applications JavaScript
- **DigitalOcean** - VPS avec Node.js
- **Railway** - Plateforme moderne pour Node.js

### Solution 3 : Installation Locale
**Utilisez** IntraSphere en local :
1. **Téléchargez** le package universel
2. **Décompressez** sur votre ordinateur
3. **Exécutez** `start-windows.bat` (Windows) ou `start-linux.sh` (Linux/Mac)
4. **Accédez** à `http://localhost:5000`

---

# 🚨 Résolution de Problèmes cPanel

## Problème : Application ne Démarre Pas

### Vérifications
1. **Version Node.js** : Utilisez 18.x ou plus récent
2. **Chemin Application Root** : Vérifiez `/public_html/intrasphere/`
3. **Fichier Startup** : Doit être `server/index.js`
4. **Logs d'erreur** : Consultez les logs dans cPanel Node.js App

### Solution
```bash
# Dans le terminal cPanel (si disponible)
cd /public_html/intrasphere/
ls -la  # Vérifier que les fichiers sont présents
node --version  # Vérifier la version Node.js
```

## Problème : Erreur Base de Données

### Vérifications
1. **Credentials** corrects dans la configuration
2. **Permissions** utilisateur sur la base
3. **Préfixe** cPanel ajouté automatiquement aux noms

### Test de Connexion
Utilisez l'assistant graphique `deploy-universal.php` pour tester la connexion automatiquement.

## Problème : Site Inaccessible

### Vérifications
1. **URL** : `https://votre-domaine.com/intrasphere/`
2. **Application** : Status "Running" dans cPanel
3. **DNS** : Domaine pointe vers votre serveur
4. **SSL** : Certificat valide (utilisez https://)

### Solutions
- **Redémarrez** l'application dans cPanel Node.js App
- **Vérifiez** les logs d'erreur
- **Contactez** le support de votre hébergeur

## Problème : Performance Lente

### Optimisations
1. **Plan d'hébergement** : Vérifiez les ressources allouées
2. **Base de données** : Utilisez MySQL local plutôt que distant
3. **Cache** : Configuration Node.js pour la production
4. **CDN** : Considérez un CDN pour les assets statiques

---

# 📞 Support et Aide

## Quand Contacter votre Hébergeur

**Contactez** le support technique si :
- Node.js App n'apparaît pas dans cPanel
- Erreurs de permissions sur les fichiers
- Application ne démarre pas malgré configuration correcte
- Problèmes de base de données MySQL

## Informations à Fournir au Support

**Préparez** ces informations :
- **Type** : "Application Node.js"
- **Version** : Node.js 18+ requise
- **Erreur exacte** : Copie du message d'erreur
- **Configuration** : Chemins et paramètres utilisés

## Communauté et Documentation

- **Documentation** : Consultez les autres guides dans `docs/`
- **GitHub** : Si code source disponible
- **Forums** : Communautés hébergement web pour cPanel

---

**🎉 Félicitations !** Vous avez maintenant toutes les informations pour déployer IntraSphere sur cPanel, que votre hébergement supporte Node.js ou non.