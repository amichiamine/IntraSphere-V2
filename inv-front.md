# INVENTAIRE EXHAUSTIF - FRONTEND

## Vue d'ensemble de l'architecture Frontend
- **Framework**: React 18 avec TypeScript
- **Bundler**: Vite
- **Routage**: Wouter
- **Gestion d'État**: React Query (TanStack Query v5)
- **UI Library**: Shadcn/ui avec Radix UI
- **Styling**: TailwindCSS avec animations personnalisées
- **Thèmes**: Support du mode sombre avec next-themes
- **Communication en temps réel**: WebSocket

## Structure des dossiers Frontend

### `/client` - Racine Frontend
```
client/
├── index.html                     # Point d'entrée HTML
├── public/                        # Assets statiques
├── src/                          # Code source principal
│   ├── App.tsx                   # Composant racine et routage
│   ├── main.tsx                  # Point d'entrée React
│   ├── index.css                 # Styles globaux et variables CSS
│   ├── core/                     # Composants et utilitaires centraux
│   ├── features/                 # Fonctionnalités métier par module
│   └── pages/                    # Pages principales de l'application
```

### `/client/src/core` - Infrastructure Frontend
```
core/
├── components/                    # Composants réutilisables
│   ├── ThemeLoader.tsx           # Gestionnaire de thèmes
│   ├── dashboard/                # Composants spécifiques au tableau de bord
│   ├── layout/                   # Composants de mise en page
│   └── ui/                       # Composants UI de base (shadcn/ui)
├── hooks/                        # Hooks React personnalisés
│   ├── use-mobile.tsx           # Détection mobile
│   ├── use-toast.ts             # Système de notifications
│   ├── useAuth.ts               # Gestion d'authentification
│   ├── useTheme.ts              # Gestion des thèmes
│   └── useWebSocket.ts          # WebSocket client
└── lib/                         # Utilitaires et configurations
    ├── queryClient.ts           # Configuration React Query
    └── utils.ts                 # Fonctions utilitaires
```

### `/client/src/features` - Modules Fonctionnels

#### `/admin` - Administration
- **admin.tsx**: Interface d'administration principale
- **analytics-admin.tsx**: Tableau de bord analytique admin
- **dashboard-management.tsx**: Gestion des tableaux de bord

#### `/auth` - Authentification
- **login.tsx**: Page de connexion et inscription
- **settings.tsx**: Paramètres utilisateur

#### `/content` - Gestion de Contenu
- **announcements.tsx**: Affichage des annonces
- **content.tsx**: Navigation du contenu
- **create-announcement.tsx**: Création d'annonces
- **create-content.tsx**: Création de contenu
- **documents.tsx**: Gestion des documents
- **advanced-content.tsx**: Gestion avancée de contenu

#### `/events` - Gestion d'Événements
- **events-management.tsx**: Administration des événements

#### `/messaging` - Communication
- **complaints.tsx**: Système de réclamations
- **forum.tsx**: Forum de discussion principale
- **forum-new-topic.tsx**: Création de nouveaux sujets
- **forum-topic.tsx**: Vue d'un sujet spécifique
- **messages.tsx**: Messagerie interne

#### `/training` - Formation
- **training.tsx**: Centre de formation principal
- **training-admin.tsx**: Administration des formations
- **trainings.tsx**: Liste des formations disponibles

### `/client/src/pages` - Pages Principales
- **dashboard.tsx**: Tableau de bord administrateur
- **directory.tsx**: Annuaire des employés
- **employee-dashboard.tsx**: Tableau de bord employé
- **not-found.tsx**: Page d'erreur 404
- **public-dashboard.tsx**: Page d'accueil publique
- **views-management.tsx**: Gestion des vues

## Composants UI (Shadcn/ui) - `/client/src/core/components/ui`

### Composants de Base
- **accordion.tsx**: Composant accordéon
- **alert.tsx**, **alert-dialog.tsx**: Composants d'alerte
- **avatar.tsx**: Composant avatar utilisateur
- **badge.tsx**: Badges et étiquettes
- **button.tsx**: Boutons avec variantes
- **calendar.tsx**: Sélecteur de date
- **card.tsx**: Cartes de contenu
- **checkbox.tsx**: Cases à cocher
- **dialog.tsx**: Modales et dialogues
- **form.tsx**: Composants de formulaire
- **input.tsx**, **textarea.tsx**: Champs de saisie
- **select.tsx**: Listes déroulantes
- **table.tsx**: Tableaux de données
- **tabs.tsx**: Onglets
- **toast.tsx**, **toaster.tsx**: Notifications

### Composants Avancés
- **carousel.tsx**: Carrousel d'images
- **chart.tsx**: Graphiques et diagrammes
- **command.tsx**: Interface de commande
- **dropdown-menu.tsx**: Menus déroulants
- **navigation-menu.tsx**: Navigation principale
- **pagination.tsx**: Pagination
- **progress.tsx**: Barres de progression
- **resizable.tsx**: Panneaux redimensionnables
- **scroll-area.tsx**: Zones de défilement
- **sheet.tsx**: Panneaux latéraux
- **skeleton.tsx**: Squelettes de chargement
- **slider.tsx**: Curseurs de valeur

### Composants Spécialisés
- **enhanced-dashboard.tsx**: Tableau de bord amélioré
- **file-uploader.tsx**: Téléversement de fichiers
- **glass-card.tsx**: Cartes avec effet verre
- **global-search.tsx**: Recherche globale
- **icon-picker.tsx**: Sélecteur d'icônes
- **image-picker.tsx**: Sélecteur d'images
- **input-otp.tsx**: Saisie de code OTP
- **notification-center.tsx**: Centre de notifications
- **simple-modal.tsx**: Modale simplifiée
- **simple-select.tsx**: Sélecteur simplifié
- **sidebar.tsx**: Barre latérale

## Routage et Navigation

### Routes Publiques
- `/` - Dashboard public
- `/login` - Page de connexion

### Routes Authentifiées - Employés
- `/` - Dashboard employé (EmployeeDashboard)
- `/announcements` - Annonces
- `/content` - Contenu
- `/documents` - Documents
- `/directory` - Annuaire
- `/training` - Formation
- `/trainings` - Liste des formations
- `/messages` - Messages
- `/complaints` - Réclamations
- `/forum` - Forum
- `/forum/topic/:id` - Sujet de forum
- `/forum/new-topic` - Nouveau sujet
- `/settings` - Paramètres

### Routes Administrateurs (Admin/Moderator)
Toutes les routes employés plus :
- `/admin` - Administration
- `/views-management` - Gestion des vues
- `/create-announcement` - Créer une annonce
- `/create-content` - Créer du contenu
- `/training-admin` - Administration formation
- `/analytics` - Analyses
- `/events` - Gestion d'événements
- `/advanced-content` - Contenu avancé

## Hooks et Services Frontend

### Hook d'Authentification (`useAuth`)
**Fonctionnalités :**
- Gestion de l'état de connexion
- Fonctions login/logout/register
- Validation des sessions
- Gestion des rôles utilisateur

**API :**
```typescript
interface AuthContextType {
  user: User | null;
  isLoading: boolean;
  login: (username: string, password: string) => Promise<void>;
  register: (userData: RegisterData) => Promise<void>;
  logout: () => Promise<void>;
  isAuthenticated: boolean;
}
```

### Client React Query (`queryClient`)
**Configuration :**
- Retry: désactivé
- Cache permanent (staleTime: Infinity)
- Gestion d'erreur 401 automatique
- Support des credentials pour les sessions

**Fonctions utilitaires :**
- `apiRequest()`: Requêtes HTTP avec gestion d'erreur
- `getQueryFn()`: Factory de fonctions de requête

### Hook WebSocket (`useWebSocket`)
**Fonctionnalités :**
- Connexion temps réel
- Gestion des canaux
- Messages de chat
- Notifications en temps réel

### Hook de Thème (`useTheme`)
**Fonctionnalités :**
- Basculement mode clair/sombre
- Persistance localStorage
- Application CSS automatique

## Gestion d'État et Données

### React Query - Clés de Cache
```typescript
// Authentification
["/api/auth/me"]

// Statistiques
["/api/stats"]

// Contenu
["/api/announcements"]
["/api/documents"]
["/api/events"]
["/api/contents"]
["/api/categories"]

// Utilisateurs et Communication
["/api/users"]
["/api/messages", userId]
["/api/complaints"]

// Formation
["/api/trainings"]
["/api/training-participants"]
["/api/courses"]
["/api/my-enrollments"]
["/api/my-certificates"]
["/api/resources"]

// Forum
["/api/forum/categories"]
["/api/forum/topics", categoryId]
["/api/forum/posts", topicId]
["/api/forum/stats/me"]

// Administration
["/api/permissions"]
["/api/employee-categories"]
["/api/system-settings"]
```

## Fonctionnalités par Module

### Dashboard Administrateur
**Composants :**
- StatsCards: Cartes de statistiques
- AnnouncementsFeed: Flux d'annonces
- QuickLinks: Liens rapides
- RecentDocuments: Documents récents
- UpcomingEvents: Événements à venir
- EnhancedDashboard: Analytique avancée

### Dashboard Employé
**Fonctionnalités :**
- Vue simplifiée
- Accès limité selon les permissions
- Focus sur la consommation de contenu

### Système de Formation
**Composants :**
- TrainingAnalytics: Analytiques de formation
- Filtres par catégorie/difficulté
- Système d'inscription
- Suivi de progression
- Gestion de certificats

### Forum de Discussion
**Fonctionnalités :**
- Catégories de forum
- Création de sujets
- Système de likes/réactions
- Statistiques utilisateur
- Recherche et filtres

### Gestion de Contenu
**Types de contenu :**
- Annonces (info, important, event, formation)
- Documents (regulation, policy, guide, procedure)
- Médias (video, image, document, audio)
- Événements (meeting, training, social, other)

### Messagerie
**Fonctionnalités :**
- Messages privés
- Système de réclamations
- Statuts de lecture
- Catégorisation

## Système de Permissions Frontend

### Rôles Utilisateur
- **employee**: Accès lecture seule, participation aux formations/forum
- **moderator**: Gestion de contenu, modération forum
- **admin**: Accès complet, administration système

### Contrôle d'Accès Routes
```typescript
// Vérification dans App.tsx
{(user.role === "admin" || user.role === "moderator") && (
  <>
    <Route path="/admin" component={Admin} />
    <Route path="/create-announcement" component={CreateAnnouncement} />
    // ... autres routes admin
  </>
)}
```

## Configuration et Thèmes

### Variables CSS Personnalisées
```css
:root {
  --background: hsl(0 0% 100%);
  --foreground: hsl(222.2 84% 4.9%);
  --primary: hsl(262.1 83.3% 57.8%);
  --secondary: hsl(210 40% 98%);
  /* ... autres variables */
}

.dark {
  --background: hsl(222.2 84% 4.9%);
  --foreground: hsl(210 40% 98%);
  /* ... variables mode sombre */
}
```

### Configuration Tailwind
- Plugin typography
- Animations personnalisées
- Composants UI étendus
- Support mode sombre automatique

## Points d'Intégration Backend

### Endpoints API Utilisés
- **Auth**: `/api/auth/*` (login, register, me, logout)
- **Contenu**: `/api/announcements`, `/api/documents`, `/api/events`
- **Utilisateurs**: `/api/users`, `/api/messages`, `/api/complaints`
- **Formation**: `/api/trainings`, `/api/courses`, `/api/enrollments`
- **Forum**: `/api/forum/*` (categories, topics, posts)
- **Admin**: `/api/permissions`, `/api/settings`

### WebSocket Events
- `CONNECTED`: Confirmation connexion
- `JOIN_CHANNEL`: Rejoindre un canal
- `CHAT_MESSAGE`: Message de chat
- `USER_TYPING`: Utilisateur en train de taper
- `NOTIFICATION`: Nouvelle notification

## Tests et Accessibilité

### Data TestIds Implémentés
- Tous les éléments interactifs ont des `data-testid`
- Format: `{action}-{target}` ou `{type}-{content}`
- Éléments dynamiques: `{type}-{description}-{id}`

### Accessibilité
- Support clavier complet
- ARIA labels appropriés
- Contraste couleurs conforme
- Focus visible
- Screen reader friendly

## Performance et Optimisation

### Stratégies Implémentées
- Code splitting par route
- Lazy loading des composants
- Optimisation des images
- Cache React Query
- Debouncing des recherches
- Skeleton loading states

### Bundle Optimization
- Tree shaking activé
- Chunks séparés pour les vendors
- Compression gzip
- Import dynamique pour les gros composants

## État Actuel et Limitations

### Points Forts
- Architecture modulaire bien structurée
- Système de permission robuste
- UI/UX moderne et responsive
- Intégration WebSocket fonctionnelle
- Système de cache efficace

### Améliorations Possibles
- Tests unitaires à ajouter
- Documentation des composants
- Optimisation des performances mobiles
- Internationalisation (i18n)
- Progressive Web App (PWA)
- Gestion d'erreur plus granulaire

### Compatibilité Technique
- React 18+ 
- TypeScript 5+
- Modern browsers (ES2020+)
- Responsive design (mobile-first)
- Support mode sombre complet