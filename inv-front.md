# INVENTAIRE FRONTEND - INTRASPHERE LEARNING PLATFORM

## ARCHITECTURE GÉNÉRALE
### Technologies Principales
- **Framework**: React 18 avec TypeScript
- **Build Tool**: Vite (pour le développement et build)
- **Routing**: Wouter (SPA router)
- **State Management**: TanStack React Query v5 pour les données serveur
- **Styling**: Tailwind CSS + shadcn/ui components
- **Forms**: React Hook Form avec Zod validation
- **Icons**: Lucide React + React Icons

### Structure des Dossiers
```
client/src/
├── core/                    # Fonctionnalités centrales
│   ├── components/          # Composants réutilisables
│   │   ├── dashboard/       # Composants de tableau de bord
│   │   ├── layout/          # Layouts et structure
│   │   └── ui/              # Composants UI de base (shadcn)
│   ├── hooks/               # Hooks personnalisés
│   └── lib/                 # Utilitaires et configurations
├── features/                # Fonctionnalités métier
│   ├── admin/               # Administration
│   ├── auth/                # Authentification
│   ├── content/             # Gestion de contenu
│   ├── events/              # Gestion d'événements
│   ├── messaging/           # Messagerie et forum
│   └── training/            # Formation et e-learning
├── pages/                   # Pages principales
└── assets/                  # Ressources statiques
```

## COMPOSANTS UI (SHADCN/UI)

### Composants de Base (44 composants)
- **accordion.tsx** - Accordéons pliables
- **alert-dialog.tsx** - Dialogues d'alerte
- **alert.tsx** - Messages d'alerte
- **aspect-ratio.tsx** - Ratios d'aspect
- **avatar.tsx** - Avatars utilisateur
- **badge.tsx** - Badges et étiquettes
- **breadcrumb.tsx** - Fil d'Ariane
- **button.tsx** - Boutons interactifs
- **calendar.tsx** - Calendriers
- **card.tsx** - Cartes conteneurs
- **carousel.tsx** - Carrousels d'images
- **chart.tsx** - Graphiques et charts
- **checkbox.tsx** - Cases à cocher
- **collapsible.tsx** - Éléments pliables
- **command.tsx** - Interface de commandes
- **context-menu.tsx** - Menus contextuels
- **dialog.tsx** - Dialogues modaux
- **drawer.tsx** - Tiroirs latéraux
- **dropdown-menu.tsx** - Menus déroulants
- **enhanced-dashboard.tsx** - Tableau de bord amélioré
- **file-uploader.tsx** - Upload de fichiers
- **form.tsx** - Formulaires avec validation
- **glass-card.tsx** - Cartes avec effet verre
- **global-search.tsx** - Recherche globale
- **hover-card.tsx** - Cartes au survol
- **icon-picker.tsx** - Sélecteur d'icônes
- **image-picker.tsx** - Sélecteur d'images
- **input-otp.tsx** - Saisie OTP
- **input.tsx** - Champs de saisie
- **label.tsx** - Étiquettes
- **menubar.tsx** - Barres de menus
- **navigation-menu.tsx** - Menus de navigation
- **notification-center.tsx** - Centre de notifications
- **pagination.tsx** - Pagination
- **popover.tsx** - Popovers
- **progress.tsx** - Barres de progression
- **radio-group.tsx** - Groupes radio
- **resizable.tsx** - Éléments redimensionnables
- **scroll-area.tsx** - Zones de défilement
- **select.tsx** - Listes de sélection
- **separator.tsx** - Séparateurs
- **sheet.tsx** - Feuilles modales
- **sidebar.tsx** - Barres latérales
- **simple-modal.tsx** - Modales simples
- **simple-select.tsx** - Sélecteurs simples
- **skeleton.tsx** - Squelettes de chargement
- **slider.tsx** - Curseurs
- **switch.tsx** - Interrupteurs
- **table.tsx** - Tableaux
- **tabs.tsx** - Onglets
- **textarea.tsx** - Zones de texte
- **toast.tsx** - Notifications toast
- **toaster.tsx** - Gestionnaire de toasts
- **toggle-group.tsx** - Groupes de bascules
- **toggle.tsx** - Bascules
- **tooltip.tsx** - Info-bulles

### Composants Dashboard (7 composants)
- **analytics-dashboard.tsx** - Tableau de bord analytique
- **announcements-feed.tsx** - Flux d'annonces
- **quick-links.tsx** - Liens rapides
- **recent-documents.tsx** - Documents récents
- **stats-cards.tsx** - Cartes de statistiques
- **training-analytics.tsx** - Analytiques de formation
- **upcoming-events.tsx** - Événements à venir

### Composants Layout (3 composants)
- **header.tsx** - En-tête principal
- **main-layout.tsx** - Layout principal
- **sidebar.tsx** - Barre latérale

## HOOKS PERSONNALISÉS

### Hooks Core (5 hooks)
- **use-mobile.tsx** - Détection mobile
- **use-toast.ts** - Gestion des toasts
- **useAuth.ts** - Authentification et session
- **useTheme.ts** - Gestion des thèmes
- **useWebSocket.ts** - Communication WebSocket

## FONCTIONNALITÉS MÉTIER

### Administration (3 pages)
- **admin.tsx** - Interface d'administration principale
  - Gestion des utilisateurs
  - Gestion des permissions
  - Gestion des documents système
  - Configuration des catégories d'employés
- **analytics-admin.tsx** - Analytiques administrateur
- **dashboard-management.tsx** - Gestion des tableaux de bord

### Authentification (2 pages)
- **login.tsx** - Page de connexion
- **settings.tsx** - Paramètres utilisateur
  - Paramètres de l'entreprise
  - Profil utilisateur
  - Préférences de notification
  - Paramètres d'apparence
  - Paramètres de confidentialité
  - Paramètres avancés

### Gestion de Contenu (6 pages)
- **announcements.tsx** - Affichage des annonces
- **content.tsx** - Bibliothèque de contenu
- **documents.tsx** - Gestion documentaire
- **advanced-content.tsx** - Gestion avancée du contenu
- **create-announcement.tsx** - Création d'annonces
- **create-content.tsx** - Création de contenu

### Événements (1 page)
- **events-management.tsx** - Gestion des événements

### Messagerie et Forum (5 pages)
- **messages.tsx** - Messagerie interne
- **complaints.tsx** - Système de réclamations
- **forum.tsx** - Forum communautaire
- **forum-topic.tsx** - Sujets de forum
- **forum-new-topic.tsx** - Création de nouveaux sujets

### Formation et E-Learning (3 pages)
- **training.tsx** - Interface de formation
- **training-admin.tsx** - Administration des formations
- **trainings.tsx** - Catalogue de formations

## PAGES PRINCIPALES

### Pages Core (6 pages)
- **dashboard.tsx** - Tableau de bord administrateur
- **employee-dashboard.tsx** - Tableau de bord employé
- **public-dashboard.tsx** - Tableau de bord public
- **directory.tsx** - Annuaire des employés
- **views-management.tsx** - Gestion des vues
- **not-found.tsx** - Page d'erreur 404

## ROUTING ET NAVIGATION

### Routes Publiques
- `/` - Tableau de bord public
- `/login` - Page de connexion

### Routes Authentifiées
#### Routes Communes (tous rôles)
- `/` - Tableau de bord (varie selon le rôle)
- `/announcements` - Annonces
- `/content` - Contenu
- `/documents` - Documents
- `/directory` - Annuaire
- `/training` - Formation
- `/trainings` - Catalogue formations
- `/messages` - Messages
- `/complaints` - Réclamations
- `/forum` - Forum
- `/forum/topic/:id` - Sujet spécifique
- `/forum/new-topic` - Nouveau sujet
- `/settings` - Paramètres

#### Routes Admin/Modérateur
- `/admin` - Administration
- `/views-management` - Gestion vues
- `/create-announcement` - Créer annonce
- `/create-content` - Créer contenu
- `/training-admin` - Admin formations
- `/analytics` - Analytiques
- `/events` - Gestion événements
- `/advanced-content` - Contenu avancé

## ÉTAT ET GESTION DES DONNÉES

### TanStack React Query
- **Configuration centralisée** dans `queryClient.ts`
- **Invalidation automatique** du cache après mutations
- **Gestion des erreurs** intégrée
- **États de chargement** pour toutes les requêtes

### Hooks d'État Principaux
- **useAuth** - Session utilisateur et authentification
- **useTheme** - Thème et apparence
- **useWebSocket** - Communication temps réel
- **use-toast** - Notifications utilisateur

## THÈMES ET STYLING

### Système de Thèmes
- **Thème clair/sombre** avec variables CSS
- **Variables dynamiques** de couleurs
- **Support multi-langues** (FR, EN, ES, DE)
- **Modes compacts** et accessibilité

### Variables CSS Principales
```css
--background, --foreground
--muted, --muted-foreground
--card, --card-foreground
--primary, --primary-foreground
--secondary, --secondary-foreground
--accent, --accent-foreground
--destructive, --destructive-foreground
--border, --input, --ring
--glass-light, --glass-medium, --glass-card
--gradient-primary, --gradient-secondary, --gradient-overlay
```

## INTERNATIONALISATION

### Langues Supportées
- **Français** (par défaut)
- **Anglais**
- **Espagnol**
- **Allemand**

## FONCTIONNALITÉS TRANSVERSALES

### Recherche Globale
- **Recherche multi-entités** (utilisateurs, contenu, documents, annonces)
- **Interface unifiée** dans le header
- **Résultats catégorisés**

### Notifications
- **Centre de notifications** temps réel
- **Toasts** pour actions utilisateur
- **WebSocket** pour notifications push

### Upload et Média
- **File Uploader** avec support multi-formats
- **Image Picker** avec prévisualisation
- **Icon Picker** avec bibliothèque d'icônes

## SÉCURITÉ FRONTEND

### Validation
- **Zod schemas** pour validation des formulaires
- **Sanitisation** des données utilisateur
- **Validation côté client** avant envoi

### Authentification
- **Session management** avec cookies sécurisés
- **Contrôle d'accès** basé sur les rôles
- **Redirection automatique** selon statut auth

## PERFORMANCE

### Optimisations
- **Lazy loading** des composants
- **React Query caching** intelligent
- **Debouncing** pour la recherche
- **Pagination** pour les listes longues

### Chargement
- **Skeleton loaders** pendant le chargement
- **États de loading** granulaires
- **Gestion d'erreurs** gracieuse

## ACCESSIBILITÉ

### Standards
- **ARIA labels** sur les composants interactifs
- **Navigation clavier** complète
- **Contraste** respectant WCAG
- **Support lecteurs d'écran**

### Responsive Design
- **Mobile-first** approach
- **Breakpoints** Tailwind
- **Touch-friendly** interfaces
- **Adaptive layouts**

## TESTING ET DÉVELOPPEMENT

### Test IDs
- **data-testid** sur tous éléments interactifs
- **Naming convention**: `{action}-{target}` ou `{type}-{content}`
- **Identifiants uniques** pour éléments dynamiques

### Développement
- **Hot reload** avec Vite
- **TypeScript strict** mode
- **ESLint** et Prettier
- **Error boundaries** pour récupération

## INTÉGRATIONS EXTERNES

### APIs Externes
- **Google Cloud Storage** pour fichiers
- **Email services** (Nodemailer)
- **WebSocket** pour temps réel

### Bibliothèques Tiers
- **Date-fns** pour manipulation dates
- **Framer Motion** pour animations
- **Recharts** pour graphiques
- **React Hook Form** pour formulaires

## ÉTAT DES FONCTIONNALITÉS

### ✅ Fonctionnalités Complètes
- Système d'authentification multi-rôles
- Gestion complète des annonces
- Bibliothèque de contenu multimédia
- Système de documents avec catégories
- Forum communautaire avec modération
- Messagerie interne
- Système de réclamations
- Formations et e-learning
- Analytics et tableaux de bord
- Paramètres utilisateur avancés
- Thèmes clair/sombre
- Recherche globale

### 🔄 En Développement
- Système de notifications push
- Analytics avancées
- Intégrations API externes

### 📋 Tests et Qualité
- Tous composants ont des test-ids
- Validation Zod sur tous formulaires
- Gestion d'erreurs complète
- États de chargement partout