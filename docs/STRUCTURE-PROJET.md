# 📁 Structure du Projet IntraSphere

## 🏗️ Architecture Globale

IntraSphere est organisé en modules séparés pour faciliter le développement et le déploiement.

```
📁 IntraSphere/
├── 📁 client/              # Frontend React + TypeScript
├── 📁 server/              # Backend Express + Node.js
├── 📁 shared/              # Types et schémas partagés
├── 📁 development/         # Outils de développement
├── 📁 production/          # Packages de production
├── 📁 docs/               # Documentation complète
├── 📄 package.json        # Dépendances principales
└── 📄 README.md          # Guide de démarrage rapide
```

## 🎨 Frontend (client/)

### Structure du Frontend
```
📁 client/
├── 📁 src/
│   ├── 📁 components/     # Composants réutilisables
│   │   └── 📁 ui/        # Composants shadcn/ui
│   ├── 📁 hooks/         # Hooks React personnalisés
│   ├── 📁 lib/           # Utilitaires et configuration
│   ├── 📁 pages/         # Pages de l'application
│   ├── 📄 App.tsx        # Composant racine
│   ├── 📄 main.tsx       # Point d'entrée
│   └── 📄 index.css      # Styles globaux
├── 📄 index.html         # Template HTML
└── 📄 package.json       # Configuration frontend
```

### Technologies Frontend
- **React 18** - Framework principal
- **TypeScript** - Typage statique
- **Vite** - Build tool et serveur de développement
- **Tailwind CSS** - Framework CSS utilitaire
- **shadcn/ui** - Composants UI modernes
- **TanStack Query** - Gestion d'état serveur
- **Wouter** - Routage léger
- **React Hook Form** - Gestion des formulaires

### Pages Principales
- `login.tsx` - Authentification
- `public-dashboard.tsx` - Tableau de bord public
- `employee-dashboard.tsx` - Tableau de bord employé
- `admin.tsx` - Interface d'administration
- `announcements.tsx` - Gestion des annonces
- `content.tsx` - Gestion de contenu
- `directory.tsx` - Annuaire des employés
- `messages.tsx` - Messagerie interne
- `complaints.tsx` - Gestion des réclamations
- `training.tsx` - Plateforme e-learning
- `settings.tsx` - Paramètres

## ⚙️ Backend (server/)

### Structure du Backend
```
📁 server/
├── 📄 index.ts           # Point d'entrée serveur
├── 📄 routes.ts          # Routes API principales
├── 📄 storage.ts         # Interface de stockage
├── 📄 db.ts             # Configuration base de données
├── 📄 vite.ts           # Intégration Vite
└── 📄 testData.ts       # Données de test
```

### Technologies Backend
- **Node.js** - Runtime JavaScript
- **Express.js** - Framework web
- **TypeScript** - Typage statique
- **Drizzle ORM** - ORM TypeScript
- **PostgreSQL/SQLite** - Base de données
- **Zod** - Validation de schémas
- **Passport.js** - Authentification

### Architecture API
- **RESTful API** - Structure standardisée
- **Validation Zod** - Validation des données
- **Gestion d'erreurs** - Centralisée et structurée
- **Logging** - Traçabilité des requêtes
- **Sessions** - Authentification persistante

## 🔗 Schémas Partagés (shared/)

### Structure Partagée
```
📁 shared/
└── 📄 schema.ts          # Schémas Drizzle et types
```

### Entités Principales
- **Users** - Utilisateurs et employés
- **Announcements** - Annonces et communications
- **Documents** - Gestion documentaire
- **Messages** - Messagerie interne
- **Complaints** - Réclamations et suggestions
- **Training** - Modules e-learning
- **Events** - Événements et calendrier

## 🛠️ Développement (development/)

### Structure de Développement
```
📁 development/
├── 📄 install-universal.php    # Installateur universel
├── 📄 installer-styles.css     # Styles installateur
├── 📄 create-universal-packages.sh  # Générateur packages
├── 📁 config/                  # Configurations
└── 📁 docs/                    # Documentation développeur
```

### Outils de Développement
- **Installateur PHP** - Déploiement automatisé
- **Générateur de packages** - Création multi-environnement
- **Configuration automatique** - Détection d'environnement
- **Documentation** - Guides complets

## 📦 Production (production/)

### Packages de Production
```
📁 production/
├── 📄 intrasphere-universal.zip       # Package universel
├── 📄 intrasphere-cpanel-nodejs.zip   # Package cPanel
├── 📄 intrasphere-windows.zip         # Package Windows
├── 📄 intrasphere-linux.tar.gz        # Package Linux
├── 📄 intrasphere-vscode.zip          # Package VS Code
├── 📄 packages-info.json              # Informations packages
└── 📄 README.md                       # Guide déploiement
```

## 📚 Documentation (docs/)

### Structure Documentation
```
📁 docs/
├── 📄 STRUCTURE-PROJET.md             # Ce fichier
├── 📄 GUIDE-DEPLOIEMENT-UNIVERSEL.md  # Guide déploiement
├── 📄 GUIDE-UTILISATION-DEBUTANT.md   # Guide utilisateur
└── 📄 GUIDE-DEVELOPPEMENT.md          # Guide développeur
```

## 🔧 Configuration Racine

### Fichiers de Configuration
- `package.json` - Dépendances et scripts
- `tsconfig.json` - Configuration TypeScript
- `vite.config.ts` - Configuration Vite
- `tailwind.config.ts` - Configuration Tailwind
- `drizzle.config.ts` - Configuration ORM
- `postcss.config.js` - Configuration PostCSS
- `components.json` - Configuration shadcn/ui

### Scripts Disponibles
- `npm run dev` - Serveur de développement
- `npm run build` - Construction production
- `npm run db:push` - Migration base de données
- `npm run db:studio` - Interface base de données

## 🌟 Fonctionnalités Clés

### Interface Utilisateur
- **Design System** - Glass morphism moderne
- **Responsive** - Mobile-first
- **Thème sombre/clair** - Personnalisable
- **Animations** - Transitions fluides
- **Accessibilité** - Standards WCAG

### Gestion de Contenu
- **Annonces** - Système de publication
- **Documents** - Gestion avec versions
- **Événements** - Calendrier intégré
- **Médias** - Upload et gestion

### Communication
- **Messagerie** - Système interne
- **Notifications** - Temps réel
- **Réclamations** - Processus structuré
- **Commentaires** - Système de feedback

### Administration
- **Gestion utilisateurs** - Rôles et permissions
- **Statistiques** - Tableaux de bord
- **Configuration** - Paramètres système
- **Sauvegarde** - Données et configuration

## 🔄 Flux de Développement

### Développement Local
1. Cloner le repository
2. `npm install` - Installation dépendances
3. `npm run dev` - Serveur développement
4. Développer et tester
5. `npm run build` - Construction

### Déploiement Production
1. Télécharger package universel
2. Décompresser sur serveur
3. Configurer base de données
4. Accéder à l'application

Cette structure modulaire facilite la maintenance, le développement collaboratif et le déploiement sur différents environnements.