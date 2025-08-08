# INVENTAIRE FRONTEND - INTRASPHERE

## 📁 STRUCTURE GENERALE DU FRONTEND

### Répertoire racine client/
```
client/
├── index.html          # Page HTML principale
├── src/               # Code source principal
│   ├── App.tsx         # Composant racine et routeur principal
│   ├── main.tsx        # Point d'entrée React
│   ├── index.css       # Styles CSS globaux
│   ├── core/          # Composants et utilitaires de base
│   ├── features/      # Fonctionnalités métier par domaine
│   └── pages/         # Pages principales de l'application
```

## 🎯 COMPOSANT PRINCIPAL - App.tsx

### Router et Navigation
- **Router principal** : Utilise `wouter` pour le routage côté client
- **AuthProvider** : Gestion de l'authentification globale
- **QueryClientProvider** : Gestion de l'état serveur avec TanStack React Query
- **ThemeLoader** : Chargement et gestion des thèmes
- **TooltipProvider** : Fournisseur de tooltips globaux
- **Toaster** : Système de notifications

### Routes publiques (non authentifiées)
- `/login` → LoginPage
- `/` → PublicDashboard

### Routes authentifiées - selon le rôle
- **Dashboard principal** (rôle basé) :
  - `admin` / `moderator` → Dashboard (admin complet)
  - `employee` → EmployeeDashboard (simplifié)

### Routes communes authentifiées
- `/announcements` → Announcements
- `/content` → Content
- `/documents` → Documents
- `/directory` → Directory
- `/training` → Training
- `/trainings` → Trainings
- `/messages` → Messages
- `/complaints` → Complaints
- `/forum` → ForumPage
- `/forum/topic/:id` → ForumTopicPage
- `/forum/new-topic` → ForumNewTopicPage
- `/settings` → Settings

### Routes admin/modérateur uniquement
- `/admin` → Admin
- `/views-management` → ViewsManagement
- `/create-announcement` → CreateAnnouncement
- `/create-content` → CreateContent
- `/training-admin` → TrainingAdmin

## 🏗️ CORE - COMPOSANTS DE BASE

### client/src/core/components/ui/ (53 composants UI)
**Composants d'interface utilisateur avec shadcn/ui :**

#### Navigation et Structure
- `navigation-menu.tsx` - Menu de navigation principal
- `menubar.tsx` - Barre de menu
- `sidebar.tsx` - Barre latérale
- `breadcrumb.tsx` - Fil d'Ariane
- `tabs.tsx` - Onglets
- `accordion.tsx` - Accordéon

#### Affichage de données
- `table.tsx` - Tableaux de données
- `card.tsx` - Cartes d'affichage
- `avatar.tsx` - Avatar utilisateur
- `badge.tsx` - Badges et étiquettes
- `calendar.tsx` - Calendrier
- `chart.tsx` - Graphiques
- `progress.tsx` - Barres de progression

#### Formulaires et saisie
- `form.tsx` - Formulaires
- `input.tsx` - Champs de saisie
- `textarea.tsx` - Zone de texte
- `select.tsx` - Sélecteur
- `simple-select.tsx` - Sélecteur simple
- `checkbox.tsx` - Cases à cocher
- `radio-group.tsx` - Boutons radio
- `switch.tsx` - Commutateurs
- `toggle.tsx` - Boutons de basculement
- `toggle-group.tsx` - Groupe de boutons de basculement
- `slider.tsx` - Curseurs
- `input-otp.tsx` - Saisie OTP
- `label.tsx` - Étiquettes

#### Interaction utilisateur
- `button.tsx` - Boutons
- `dropdown-menu.tsx` - Menu déroulant
- `context-menu.tsx` - Menu contextuel
- `hover-card.tsx` - Carte de survol
- `popover.tsx` - Popover
- `tooltip.tsx` - Info-bulles
- `command.tsx` - Interface de commande

#### Modales et dialogues
- `dialog.tsx` - Dialogues
- `alert-dialog.tsx` - Dialogues d'alerte
- `drawer.tsx` - Tiroir
- `sheet.tsx` - Feuille coulissante
- `simple-modal.tsx` - Modal simple

#### Notifications et feedback
- `alert.tsx` - Alertes
- `toast.tsx` - Notifications toast
- `toaster.tsx` - Gestionnaire de notifications

#### Utilitaires et mise en page
- `separator.tsx` - Séparateurs
- `scroll-area.tsx` - Zone de défilement
- `resizable.tsx` - Panneaux redimensionnables
- `collapsible.tsx` - Éléments pliables
- `aspect-ratio.tsx` - Ratio d'aspect
- `skeleton.tsx` - Squelettes de chargement
- `pagination.tsx` - Pagination

#### Média et fichiers
- `image-picker.tsx` - Sélecteur d'images
- `file-uploader.tsx` - Téléchargeur de fichiers
- `icon-picker.tsx` - Sélecteur d'icônes
- `carousel.tsx` - Carrousel

#### Spécialisés
- `glass-card.tsx` - Cartes avec effet verre (design system)

### client/src/core/components/layout/ (3 composants)
- `header.tsx` - En-tête principal
- `main-layout.tsx` - Mise en page principale
- `sidebar.tsx` - Barre latérale de navigation

### client/src/core/components/dashboard/ (5 composants)
- `announcements-feed.tsx` - Flux d'annonces
- `quick-links.tsx` - Liens rapides
- `recent-documents.tsx` - Documents récents
- `stats-cards.tsx` - Cartes de statistiques
- `upcoming-events.tsx` - Événements à venir

### client/src/core/components/
- `ThemeLoader.tsx` - Chargeur de thème

## 🪝 HOOKS PERSONNALISÉS

### client/src/core/hooks/
- `useAuth.ts` - Hook d'authentification (AuthProvider, useAuth)
- `useTheme.ts` - Hook de gestion de thème
- `use-toast.ts` - Hook de notifications toast
- `use-mobile.tsx` - Hook de détection mobile

## 📚 LIBRAIRIES ET UTILITAIRES

### client/src/core/lib/
- `queryClient.ts` - Configuration TanStack React Query
- `utils.ts` - Utilitaires généraux (cn pour className merge)

## 🎯 FEATURES - FONCTIONNALITÉS MÉTIER

### Authentication (client/src/features/auth/)
- `login.tsx` - Page de connexion
- `settings.tsx` - Paramètres utilisateur

### Administration (client/src/features/admin/)
- `admin.tsx` - Interface d'administration

### Gestion de contenu (client/src/features/content/)
- `announcements.tsx` - Gestion des annonces
- `content.tsx` - Gestion du contenu multimédia
- `documents.tsx` - Gestion des documents
- `create-announcement.tsx` - Création d'annonces
- `create-content.tsx` - Création de contenu

### Messagerie et communication (client/src/features/messaging/)
- `messages.tsx` - Messages privés
- `complaints.tsx` - Système de réclamations
- `forum.tsx` - Forum de discussion
- `forum-topic.tsx` - Sujet de forum
- `forum-new-topic.tsx` - Création de nouveau sujet

### Formation et e-learning (client/src/features/training/)
- `training.tsx` - Module de formation
- `training-admin.tsx` - Administration des formations
- `trainings.tsx` - Liste des formations

## 📄 PAGES PRINCIPALES

### client/src/pages/
- `dashboard.tsx` - Tableau de bord administrateur
- `employee-dashboard.tsx` - Tableau de bord employé
- `public-dashboard.tsx` - Tableau de bord public
- `directory.tsx` - Annuaire des utilisateurs
- `views-management.tsx` - Gestion des vues
- `not-found.tsx` - Page 404

## ⚙️ CONFIGURATION ET STYLES

### Fichiers de configuration frontend
- `index.html` - Template HTML principal
- `index.css` - Styles CSS globaux et variables CSS
- `main.tsx` - Point d'entrée React

## 🔧 DÉPENDANCES FRONTEND PRINCIPALES

### Frameworks et libraries UI
- **React 18** - Framework principal
- **Vite** - Build tool et dev server
- **TypeScript** - Typage statique
- **Tailwind CSS** - Framework CSS
- **shadcn/ui** - Composants UI (Radix UI + Tailwind)
- **Radix UI** - Primitives UI accessibles
- **Lucide React** - Icônes
- **React Icons** - Icônes supplémentaires

### Gestion d'état et données
- **TanStack React Query** - Gestion de l'état serveur
- **React Hook Form** - Gestion des formulaires
- **Zod** - Validation de schémas
- **Wouter** - Routage côté client

### UI avancée
- **Framer Motion** - Animations
- **Embla Carousel** - Carrousels
- **React Day Picker** - Sélecteur de dates
- **React Resizable Panels** - Panneaux redimensionnables
- **Recharts** - Graphiques et diagrammes

### Utilitaires
- **class-variance-authority** - Variants de classes CSS
- **clsx** / **tailwind-merge** - Fusion de classes CSS
- **date-fns** - Manipulation de dates
- **cmdk** - Interface de commande

## 🎨 SYSTÈME DE DESIGN

### Thème et apparence
- **Glass morphism** - Design avec effet de verre
- **Variables CSS personnalisées** - Système de couleurs
- **Mode sombre/clair** - Support des thèmes
- **Gradients** - Arrière-plans dégradés
- **Coins arrondis** - Design moderne

### Couleurs principales
- **Primary** : Violet (#8B5CF6)
- **Secondary** : Violet clair (#A78BFA)
- **Backgrounds** : Dégradés slate/blue/indigo

## 🔄 FLUX DE DONNÉES FRONTEND

### Architecture de données
1. **TanStack React Query** - Cache et synchronisation serveur
2. **React Hook Form** - Gestion locale des formulaires
3. **Zod** - Validation côté client
4. **Session/Auth** - État d'authentification global
5. **API REST** - Communication avec le backend

### Pattern de requêtes
- **Queries** : Récupération de données (GET)
- **Mutations** : Modifications de données (POST/PUT/DELETE)
- **Cache invalidation** : Actualisation automatique
- **Loading states** : États de chargement
- **Error handling** : Gestion d'erreurs

## 📱 RESPONSIVE ET ACCESSIBILITÉ

### Responsive Design
- **Mobile-first** - Approche mobile en priorité
- **Breakpoints Tailwind** - Points de rupture standard
- **Hook use-mobile** - Détection d'appareil mobile

### Accessibilité
- **Radix UI** - Composants accessibles par défaut
- **ARIA** - Attributs d'accessibilité
- **Keyboard navigation** - Navigation au clavier
- **Focus management** - Gestion du focus

## 🧪 ATTRIBUTS DE TEST

### Test IDs
- **data-testid** - Attributs pour les tests automatisés
- **Éléments interactifs** - Boutons, inputs, liens
- **Contenu dynamique** - Données utilisateur, statuts
- **Pattern** : `{action}-{target}` ou `{type}-{content}`

## 📊 MÉTRIQUES ET SURVEILLANCE

### Performance
- **React Query DevTools** - Debugging des requêtes
- **Vite HMR** - Hot Module Replacement
- **TypeScript** - Vérification de types

### Logging
- **Console logs** - Logs de développement
- **Error boundaries** - Capture d'erreurs React
- **Theme logging** - Logs d'application de thème