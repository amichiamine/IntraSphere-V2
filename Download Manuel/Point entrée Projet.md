Point d'entrée de l'application IntraSphere en production
Voici la structure complète du point d'entrée de votre application :

🚀 Serveur principal
Fichier d'entrée : server/index.ts
Port de production : 5000 (configurable via variable PORT)
URL d'accès : https://votre-domaine.com:5000
🌐 Page de démarrage selon l'utilisateur
Si l'utilisateur N'EST PAS connecté :

Route : / (racine)
Page : PublicDashboard (client/src/pages/public-dashboard.tsx)
Description : Page d'accueil publique avec présentation d'IntraSphere
Si l'utilisateur EST connecté :

Route : / (racine)
Page varie selon le rôle :
Admin/Moderator → Dashboard (tableau de bord complet)
Employee → EmployeeDashboard (tableau de bord simplifié)
🔐 Flux d'authentification
Première visite → PublicDashboard (page d'accueil)
Clic "Se connecter" → /login → LoginPage
Après connexion → Redirection vers / avec le bon dashboard selon le rôle
📁 Structure de production
En développement (npm run dev):
├── server/index.ts (port 5000)
└── client/index.html via Vite (intégré)
En production:
├── server/index.ts (port 5000)
└── server/public/ (fichiers statiques buildés)
    └── index.html (point d'entrée frontend)
⚙️ Configuration production
Le serveur :

Sert l'API sur /api/*
Sert les fichiers statiques depuis server/public/
Redirige toutes les autres routes vers index.html (SPA)
Gère l'authentification et les sessions
En résumé : Le point d'entrée unique est server/index.ts sur le port 5000, qui sert à la fois l'API et le frontend React. La page de démarrage dépend de l'état d'authentification de l'utilisateur.