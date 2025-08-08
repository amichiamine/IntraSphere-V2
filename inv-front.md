# 📋 INVENTAIRE FRONTEND COMPLET - PHP MIGRATION REFERENCE

## 🎯 Vue d'ensemble
- **Total fichiers TypeScript/React**: 108 fichiers (89 .tsx + 19 .ts)
- **Framework actuel**: React 18 + TypeScript + Vite
- **UI Framework**: shadcn/ui + Radix UI + Tailwind CSS
- **Routing**: Wouter
- **État**: TanStack React Query + Context API
- **Validation**: React Hook Form + Zod

---

## 📱 ARCHITECTURE FRONTEND

### 🔗 Point d'entrée principal
- **`client/src/main.tsx`**: Bootstrap React, gestion erreurs ResizeObserver
- **`client/src/App.tsx`**: Router principal, AuthProvider, QueryClient, ThemeLoader

### 🎨 Design System et Styling
- **`client/src/index.css`**: Variables CSS, glass morphism, dark mode, animations
- **`client/index.html`**: Document HTML principal avec meta tags

---

## 🏗️ STRUCTURE DES COMPOSANTS

### 📁 Core Components (client/src/core/)

#### 🎨 UI Components (52 composants shadcn/ui)
**Localisation**: `client/src/core/components/ui/`
1. **accordion.tsx** - Accordéons pliables
2. **alert-dialog.tsx** - Dialogues d'alerte modaux
3. **alert.tsx** - Messages d'alerte contextuels
4. **aspect-ratio.tsx** - Conteneurs avec ratio fixe
5. **avatar.tsx** - Avatars utilisateurs avec fallback
6. **badge.tsx** - Badges de statut/catégories
7. **breadcrumb.tsx** - Navigation breadcrumb
8. **button.tsx** - Boutons avec variants
9. **calendar.tsx** - Sélecteur de dates
10. **card.tsx** - Cartes de contenu
11. **carousel.tsx** - Carrousels d'images/contenu
12. **chart.tsx** - Graphiques avec Recharts
13. **checkbox.tsx** - Cases à cocher
14. **collapsible.tsx** - Sections repliables
15. **command.tsx** - Interface de commandes
16. **context-menu.tsx** - Menus contextuels
17. **dialog.tsx** - Dialogues modaux
18. **drawer.tsx** - Tiroirs latéraux
19. **dropdown-menu.tsx** - Menus déroulants
20. **file-uploader.tsx** - Upload de fichiers
21. **form.tsx** - Composants de formulaires
22. **glass-card.tsx** - Cartes avec effet glass morphism
23. **hover-card.tsx** - Cartes au survol
24. **icon-picker.tsx** - Sélecteur d'icônes
25. **image-picker.tsx** - Sélecteur d'images
26. **input-otp.tsx** - Saisie de codes OTP
27. **input.tsx** - Champs de saisie
28. **label.tsx** - Labels de formulaires
29. **menubar.tsx** - Barres de menus
30. **navigation-menu.tsx** - Menus de navigation
31. **pagination.tsx** - Pagination de listes
32. **popover.tsx** - Popovers informatifs
33. **progress.tsx** - Barres de progression
34. **radio-group.tsx** - Boutons radio groupés
35. **resizable.tsx** - Panneaux redimensionnables
36. **scroll-area.tsx** - Zones de défilement
37. **select.tsx** - Listes de sélection
38. **separator.tsx** - Séparateurs visuels
39. **sheet.tsx** - Panneaux latéraux
40. **sidebar.tsx** - Barres latérales
41. **simple-modal.tsx** - Modales simplifiées
42. **simple-select.tsx** - Sélecteurs simplifiés
43. **skeleton.tsx** - Placeholders de chargement
44. **slider.tsx** - Curseurs de valeurs
45. **switch.tsx** - Interrupteurs on/off
46. **table.tsx** - Tableaux de données
47. **tabs.tsx** - Onglets de navigation
48. **textarea.tsx** - Zones de texte multi-lignes
49. **toast.tsx** - Notifications temporaires
50. **toaster.tsx** - Container de toasts
51. **toggle-group.tsx** - Groupes de boutons toggle
52. **toggle.tsx** - Boutons toggle
53. **tooltip.tsx** - Info-bulles

#### 📐 Layout Components (3 composants)
**Localisation**: `client/src/core/components/layout/`
1. **header.tsx** - En-tête avec navigation et profil utilisateur
2. **main-layout.tsx** - Layout principal avec sidebar et contenu
3. **sidebar.tsx** - Barre latérale de navigation

#### 📊 Dashboard Components (5 composants)
**Localisation**: `client/src/core/components/dashboard/`
1. **announcements-feed.tsx** - Flux d'annonces sur dashboard
2. **quick-links.tsx** - Liens rapides vers fonctionnalités
3. **recent-documents.tsx** - Documents récents
4. **stats-cards.tsx** - Cartes de statistiques
5. **upcoming-events.tsx** - Événements à venir

#### 🔧 Hooks (4 hooks)
**Localisation**: `client/src/core/hooks/`
1. **useAuth.ts** - Gestion authentification et sessions
2. **use-mobile.tsx** - Détection des appareils mobiles
3. **useTheme.ts** - Gestion des thèmes dark/light
4. **use-toast.ts** - Gestion des notifications toast

#### 📚 Libraries (2 utilitaires)
**Localisation**: `client/src/core/lib/`
1. **queryClient.ts** - Configuration TanStack Query
2. **utils.ts** - Utilitaires généraux (cn, formatters)

#### 🎭 Theme Components (1 composant)
**Localisation**: `client/src/core/components/`
1. **ThemeLoader.tsx** - Chargeur de thèmes avec persistance

---

## 📄 PAGES PRINCIPALES

### 📁 Pages (8 pages)
**Localisation**: `client/src/pages/`

1. **public-dashboard.tsx** - Dashboard public (non-authentifié)
   - **Composants**: Hero section, fonctionnalités, call-to-action
   - **Fonctions**: Affichage promotionnel de l'application

2. **dashboard.tsx** - Dashboard admin/modérateur
   - **Composants**: Stats, annonces, documents récents, événements
   - **Fonctions**: Vue d'ensemble administrative

3. **employee-dashboard.tsx** - Dashboard employé
   - **Composants**: Annonces importantes, formations, messages
   - **Fonctions**: Interface simplifiée pour employés

4. **directory.tsx** - Annuaire des employés
   - **Composants**: Liste, recherche, filtres, détails profils
   - **Fonctions**: Recherche et contact d'employés

5. **events.tsx** - Gestion des événements
   - **Composants**: Calendrier, créer/modifier événements
   - **Fonctions**: Planning et organisation d'événements

6. **permissions-admin.tsx** - Administration des permissions
   - **Composants**: Liste utilisateurs, gestion droits
   - **Fonctions**: Attribution et révocation de permissions

7. **views-management.tsx** - Gestion des vues système
   - **Composants**: Configuration modules, visibilité
   - **Fonctions**: Activation/désactivation des fonctionnalités

8. **not-found.tsx** - Page 404
   - **Composants**: Message d'erreur, lien retour
   - **Fonctions**: Gestion des URLs inexistantes

---

## 🔧 FEATURES MODULAIRES

### 🔐 Auth Module (2 composants)
**Localisation**: `client/src/features/auth/`
1. **login.tsx** - Page de connexion
   - **Formulaires**: Username/password avec validation
   - **Fonctions**: Authentification, redirection, gestion erreurs
   - **Éléments UI**: Input, button, card, form validation

2. **settings.tsx** - Paramètres utilisateur
   - **Formulaires**: Profil, mot de passe, préférences
   - **Fonctions**: Modification des données personnelles
   - **Éléments UI**: Tabs, input, textarea, avatar upload

### 📢 Content Module (5 composants)
**Localisation**: `client/src/features/content/`

1. **announcements.tsx** - Liste des annonces
   - **Composants**: Grid d'annonces, filtres, recherche
   - **Fonctions**: Affichage, tri, filtrage annonces
   - **Éléments UI**: Card, badge, button, input

2. **content.tsx** - Bibliothèque de contenu
   - **Composants**: Grille de contenus, catégories, recherche
   - **Fonctions**: Navigation contenu, filtres, visionneuse
   - **Éléments UI**: Grid layout, filters, modal preview

3. **documents.tsx** - Gestion documentaire
   - **Composants**: Liste documents, versioning, download
   - **Fonctions**: Upload, classification, historique
   - **Éléments UI**: Table, file-uploader, progress

4. **create-announcement.tsx** - Création d'annonces
   - **Formulaires**: Titre, contenu, type, importance
   - **Fonctions**: Rich text editor, upload images
   - **Éléments UI**: Form, textarea, select, file-uploader

5. **create-content.tsx** - Création de contenu
   - **Formulaires**: Métadonnées, upload fichiers
   - **Fonctions**: Classification, tags, preview
   - **Éléments UI**: Multi-step form, drag-drop

### 💬 Messaging Module (5 composants)
**Localisation**: `client/src/features/messaging/`

1. **messages.tsx** - Messagerie interne
   - **Composants**: Liste conversations, chat interface
   - **Fonctions**: Envoi/réception, notifications
   - **Éléments UI**: Chat bubbles, emoji picker, attachment

2. **complaints.tsx** - Système de réclamations
   - **Composants**: Formulaire réclamation, suivi statuts
   - **Fonctions**: Catégorisation, assignation, workflow
   - **Éléments UI**: Form, status badges, timeline

3. **forum.tsx** - Forum de discussion
   - **Composants**: Catégories, sujets récents, statistiques
   - **Fonctions**: Navigation forum, modération
   - **Éléments UI**: Category cards, topic list, stats

4. **forum-topic.tsx** - Sujet de forum
   - **Composants**: Posts, réponses, likes, pagination
   - **Fonctions**: Interaction posts, modération
   - **Éléments UI**: Thread view, vote buttons, editor

5. **forum-new-topic.tsx** - Créer sujet forum
   - **Formulaires**: Titre, contenu, catégorie
   - **Fonctions**: Rich text, attachments, preview
   - **Éléments UI**: Form, rich editor, preview pane

### 🎓 Training Module (3 composants)
**Localisation**: `client/src/features/training/`

1. **training.tsx** - Interface d'apprentissage
   - **Composants**: Cours, progression, certifications
   - **Fonctions**: Suivi formation, quiz, ressources
   - **Éléments UI**: Progress bars, video player, quiz

2. **trainings.tsx** - Catalogue de formations
   - **Composants**: Liste formations, filtres, inscription
   - **Fonctions**: Recherche, catégories, inscription
   - **Éléments UI**: Card grid, filters, enrollment

3. **training-admin.tsx** - Administration formations
   - **Composants**: Création cours, gestion participants
   - **Fonctions**: CRUD formations, analytics
   - **Éléments UI**: Admin panels, charts, user management

### 👥 Admin Module (1 composant)
**Localisation**: `client/src/features/admin/`

1. **admin.tsx** - Panel d'administration
   - **Composants**: Statistiques système, gestion utilisateurs
   - **Fonctions**: CRUD complet, monitoring
   - **Éléments UI**: Dashboard layout, data tables, charts

---

## 🔄 ÉTAT ET DONNÉES

### 📱 Shared State Management
**Localisation**: `client/src/shared/`

#### 🔗 Types (3 fichiers)
1. **types/api.ts** - Types pour API responses
2. **types/components.ts** - Types pour props composants
3. **types/forms.ts** - Types pour formulaires

#### 📋 Constants (3 fichiers)
1. **constants/routes.ts** - Routes et endpoints
2. **constants/permissions.ts** - Permissions et rôles
3. **constants/ui.ts** - Constantes UI (couleurs, tailles)

#### 🛠️ Utils (8 fichiers)
1. **utils/api.ts** - Helpers API et requests
2. **utils/auth.ts** - Utilitaires authentification
3. **utils/date.ts** - Formatage dates et heures
4. **utils/format.ts** - Formatage texte et nombres
5. **utils/permissions.ts** - Vérification permissions
6. **utils/storage.ts** - LocalStorage et SessionStorage
7. **utils/validation.ts** - Schémas de validation Zod
8. **utils/index.ts** - Export central des utilitaires

---

## 🎨 SYSTÈME DE DESIGN

### 🎭 Thème et CSS
- **Variables CSS**: Couleurs, espacement, typographie
- **Glass Morphism**: Effets backdrop-blur et transparence
- **Dark Mode**: Support complet avec variables CSS
- **Responsive**: Mobile-first avec breakpoints Tailwind
- **Animations**: Transitions CSS et Framer Motion

### 🔤 Typographie
- **Police**: Inter font family avec fallbacks
- **Tailles**: Variables CSS pour consistance
- **Weights**: Regular, medium, semibold, bold

### 🎨 Couleurs
- **Primaires**: Purple gradient (#8B5CF6 → #A78BFA)
- **Neutrales**: Slate variations pour textes
- **Sémantiques**: Success, warning, error, info
- **Glass**: Transparencies et blurs

---

## 📱 FONCTIONNALITÉS INTERACTIVES

### 🔄 Navigation
- **Routing**: Wouter avec routes protégées
- **Sidebar**: Navigation principal avec permissions
- **Breadcrumbs**: Fil d'Ariane contextuel
- **Menu Mobile**: Drawer responsive

### 📊 Data Management
- **API Calls**: TanStack Query avec cache
- **Forms**: React Hook Form + Zod validation
- **File Upload**: Drag & drop avec progress
- **Real-time**: WebSocket pour messages

### 🎯 UX Features
- **Loading States**: Skeletons et spinners
- **Error Handling**: Error boundaries et toasts
- **Accessibility**: ARIA labels et keyboard nav
- **Performance**: Code splitting et lazy loading

---

## 🔧 HOOKS ET LOGIQUE

### 🎣 Custom Hooks
1. **useAuth** - État authentification global
2. **useTheme** - Gestion thème dark/light
3. **useMobile** - Détection responsive
4. **useToast** - Notifications système

### 📡 Data Fetching
- **Queries**: Récupération données avec cache
- **Mutations**: Modifications avec invalidation
- **Optimistic Updates**: UX fluide
- **Error Recovery**: Retry automatique

---

## 📝 FORMULAIRES ET VALIDATION

### 📋 Types de Formulaires
1. **Login/Register** - Authentification
2. **Profile Settings** - Paramètres utilisateur
3. **Content Creation** - Création contenu/annonces
4. **User Management** - Administration utilisateurs
5. **Training Forms** - Gestion formations
6. **Message Composition** - Envoi messages

### ✅ Validation
- **Zod Schemas** - Validation côté client
- **Real-time** - Validation temps réel
- **Error Display** - Messages d'erreur contextuels
- **Form State** - Gestion états et soumission

---

## 🔍 MOTEURS DE RECHERCHE

### 🔎 Fonctionnalités de Recherche
1. **Global Search** - Recherche globale dans header
2. **Content Filters** - Filtres par catégorie/type
3. **User Directory** - Recherche employés
4. **Document Search** - Recherche dans documents
5. **Training Search** - Recherche formations

### 🏷️ Filtres
- **Par catégorie** - Classification du contenu
- **Par date** - Filtres temporels
- **Par statut** - États des éléments
- **Par permissions** - Visibilité selon rôles

---

## 🔔 NOTIFICATIONS ET FEEDBACK

### 📢 Types de Notifications
1. **Toast Messages** - Notifications temporaires
2. **Alert Dialogs** - Confirmations importantes
3. **Badges** - Compteurs de notifications
4. **Status Indicators** - États visuels

### 🎯 Feedback Utilisateur
- **Loading States** - Indication progression
- **Success/Error** - Résultats d'actions
- **Hover Effects** - Interactivité visuelle
- **Focus States** - Accessibilité clavier

---

## 📊 MIGRATION PHP - ÉQUIVALENCES RECOMMANDÉES

### 🏗️ Structure PHP équivalente
```
public/
├── index.php (Router principal)
├── assets/ (CSS, JS, images)
├── components/ (Templates PHP)
└── uploads/ (Fichiers utilisateur)

src/
├── controllers/ (Logique métier)
├── models/ (Accès données)
├── views/ (Templates HTML)
├── middleware/ (Auth, validation)
└── utils/ (Helpers)
```

### 🔄 Correspondances fonctionnelles
- **React Components → PHP Templates + JavaScript**
- **React Hooks → Session PHP + JavaScript**
- **TanStack Query → AJAX + PHP**
- **React Router → PHP Router**
- **Zod Validation → PHP Validation + JS**
- **shadcn/ui → CSS Classes + JavaScript**

### 🎨 Conservation du Design
- **Tailwind CSS** - Réutilisable tel quel
- **CSS Variables** - Compatible navigateurs
- **Glass Morphism** - Pure CSS, pas de dépendances
- **Responsive** - Media queries standards
- **Animations** - CSS transitions + JS

---

**📋 RÉSUMÉ QUANTITATIF**
- **Pages principales**: 8
- **Composants UI**: 52
- **Composants métier**: 20
- **Hooks**: 4
- **Utilitaires**: 11
- **Modules features**: 5
- **Total fichiers**: 108

Cette architecture modulaire permet une migration progressive vers PHP en conservant l'intégralité des fonctionnalités et du design.