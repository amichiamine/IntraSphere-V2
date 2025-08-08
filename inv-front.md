# Inventaire Frontend IntraSphere - Version TypeScript/React

## Architecture Générale Frontend

### Technologies
- **Framework**: React 18 avec TypeScript
- **Build Tool**: Vite
- **Routing**: Wouter
- **State Management**: TanStack Query (React Query v5)
- **Styling**: Tailwind CSS + shadcn/ui
- **Authentication**: Hook personnalisé useAuth
- **UI Components**: Radix UI + shadcn/ui
- **Theme**: ThemeProvider avec support dark/light mode

### Structure des Dossiers
```
client/src/
├── App.tsx (Point d'entrée principal avec routing)
├── main.tsx (Bootstrap React)
├── index.css (Styles globaux)
├── core/ (Composants et hooks centraux)
│   ├── components/ (UI components partagés)
│   ├── hooks/ (Hooks personnalisés)
│   └── lib/ (Utilitaires et configuration)
├── features/ (Fonctionnalités métier)
│   ├── admin/
│   ├── auth/
│   ├── content/
│   ├── messaging/
│   └── training/
├── pages/ (Pages principales)
├── shared/ (Types et constantes partagées)
└── assets/ (Ressources statiques)
```

## Pages Principales

### Pages Publiques (Non authentifiées)
1. **PublicDashboard** (`/`)
   - Vue d'ensemble publique
   - Affichage des annonces publiques
   - Statistiques générales

2. **LoginPage** (`/login`)
   - Formulaire de connexion
   - Authentification utilisateur

### Pages Authentifiées

#### Tableaux de Bord
1. **Dashboard** (`/dashboard`) - Admin/Modérateur
   - Statistiques complètes
   - Graphiques et métriques
   - Gestion administrative

2. **EmployeeDashboard** (`/employee-dashboard`) - Employés
   - Vue simplifiée
   - Contenu pertinent pour l'employé
   - Actions rapides

#### Gestion du Contenu
3. **Announcements** (`/announcements`)
   - Liste des annonces
   - Filtres et recherche
   - Actions CRUD selon permissions

4. **CreateAnnouncement** (`/announcements/create`)
   - Formulaire de création d'annonce
   - Upload d'images
   - Aperçu en temps réel

5. **Content** (`/content`)
   - Galerie de contenu multimédia
   - Filtres par type/catégorie
   - Système de notation

6. **CreateContent** (`/content/create`)
   - Upload de contenu multimédia
   - Métadonnées et catégorisation

7. **Documents** (`/documents`)
   - Bibliothèque de documents
   - Téléchargement et prévisualisation
   - Gestion des versions

#### Communication
8. **Messages** (`/messages`)
   - Messagerie interne
   - Conversations et threads
   - Notifications

9. **Complaints** (`/complaints`)
   - Système de réclamations
   - Suivi des statuts
   - Attribution et résolution

10. **ForumPage** (`/forum`)
    - Forum de discussion
    - Catégories et sujets
    - Système de votes

11. **ForumTopicPage** (`/forum/topic/:id`)
    - Discussion détaillée
    - Réponses et interactions

12. **ForumNewTopicPage** (`/forum/new`)
    - Création de nouveau sujet
    - Sélection de catégorie

#### Formation et Apprentissage
13. **Training** (`/training/:id`)
    - Contenu de formation détaillé
    - Progression et suivi

14. **Trainings** (`/trainings`)
    - Catalogue des formations
    - Inscription et planification

15. **TrainingAdmin** (`/training-admin`)
    - Administration des formations
    - Gestion des participants

#### Administration
16. **Admin** (`/admin`)
    - Panneau d'administration
    - Gestion des utilisateurs
    - Configuration système

17. **Directory** (`/directory`)
    - Annuaire des employés
    - Recherche et contacts

18. **Settings** (`/settings`)
    - Paramètres utilisateur
    - Préférences et profil

19. **ViewsManagement** (`/views-management`)
    - Gestion de l'affichage
    - Configuration des modules

20. **PermissionsAdmin** (`/permissions-admin`)
    - Gestion des permissions
    - Attribution des rôles

## Fonctionnalités par Feature

### Auth (`features/auth/`)
- **login.tsx**: Authentification utilisateur
- **settings.tsx**: Paramètres du profil utilisateur

### Content (`features/content/`)
- **announcements.tsx**: Gestion des annonces
- **content.tsx**: Galerie multimédia
- **create-announcement.tsx**: Création d'annonces
- **create-content.tsx**: Upload de contenu
- **documents.tsx**: Bibliothèque documentaire

### Messaging (`features/messaging/`)
- **messages.tsx**: Messagerie interne
- **complaints.tsx**: Système de réclamations
- **forum.tsx**: Forum principal
- **forum-topic.tsx**: Discussion de forum
- **forum-new-topic.tsx**: Nouveau sujet forum

### Training (`features/training/`)
- **training.tsx**: Contenu de formation
- **trainings.tsx**: Catalogue formations
- **training-admin.tsx**: Administration formations

### Admin (`features/admin/`)
- **admin.tsx**: Panneau d'administration

## Composants UI et Hooks

### Hooks Core (`core/hooks/`)
- **useAuth**: Gestion de l'authentification
- Autres hooks métier personnalisés

### Composants Core (`core/components/`)
- **ThemeLoader**: Gestion des thèmes
- **UI Components**: Composants shadcn/ui
- **Toaster**: Système de notifications
- **TooltipProvider**: Bulles d'aide

### Lib (`core/lib/`)
- **queryClient**: Configuration TanStack Query
- Utilitaires et helpers

## Gestion des États

### TanStack Query
- Configuration centralisée dans queryClient
- Gestion du cache et synchronisation
- Mutations pour les opérations CRUD
- Invalidation automatique du cache

### Hooks d'Authentication
- Contexte d'authentification global
- Gestion des sessions
- Protection des routes

## Système de Routing

### Routes Publiques
- `/` → PublicDashboard
- `/login` → LoginPage

### Routes Privées
- Routes conditionnelles selon le rôle utilisateur
- Redirection automatique selon l'authentification
- Protection par permissions

## Gestion des Permissions

### Système de Rôles
- **employee**: Accès de base
- **moderator**: Permissions étendues
- **admin**: Accès complet

### Protection des Composants
- Vérification des permissions en temps réel
- Affichage conditionnel selon les droits
- Redirection automatique si non autorisé

## API et Communication Backend

### Configuration API
- Client HTTP centralisé
- Gestion des erreurs automatique
- Authentification automatique via cookies/sessions

### Endpoints Utilisés
- `/api/auth/*` - Authentification
- `/api/announcements/*` - Annonces
- `/api/content/*` - Contenu multimédia
- `/api/documents/*` - Documents
- `/api/messages/*` - Messagerie
- `/api/complaints/*` - Réclamations
- `/api/training/*` - Formations
- `/api/admin/*` - Administration
- `/api/stats` - Statistiques

## Styling et Thème

### Tailwind CSS
- Configuration personnalisée
- Classes utilitaires
- Responsive design

### Système de Thème
- Mode sombre/clair
- Variables CSS personnalisées
- Cohérence visuelle globale

### Glass Morphism
- Effets visuels modernes
- Transparence et flou
- Animations fluides

## Internationalisation
- Français par défaut
- Structure prête pour extension multilingue

## Points d'Amélioration Identifiés
- Système de notifications temps réel
- Optimisation des performances
- Tests automatisés
- PWA capabilities
- Système de cache plus avancé