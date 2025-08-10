# IntraSphere PHP - Package de Déploiement

## Installation Rapide

1. Extrayez tous les fichiers sur votre serveur web
2. Ouvrez votre navigateur et allez sur : http://votre-domaine.com/install.php
3. Suivez l'assistant d'installation automatisé
4. Supprimez le fichier install.php après installation

## Configuration Requise

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur (ou MariaDB 10.2+)
- Extensions PHP : PDO, PDO_MySQL, JSON, OpenSSL
- Serveur web : Apache ou Nginx

## Fonctionnalités Incluses

✅ Système d'authentification complet
✅ Gestion des utilisateurs et rôles
✅ Annonces et communications
✅ Gestion documentaire
✅ Système de messagerie interne
✅ Module de formations
✅ Système de réclamations
✅ Tableau de bord admin
✅ API REST complète
✅ Interface responsive (mobile-friendly)
✅ Sécurité renforcée (CSRF, XSS, SQL injection)

## Support Hébergeurs

- ✅ cPanel (hébergement mutualisé)
- ✅ OVH Mutualisé
- ✅ 1&1 / Ionos
- ✅ Développement local (XAMPP/WAMP)
- ✅ VPS et serveurs dédiés

## Comptes par Défaut

Après installation, vous pourrez vous connecter avec :

**Administrateur :**
- Nom d'utilisateur : admin
- Mot de passe : (défini pendant l'installation)

**Comptes de test :**
- marie.martin / password123 (Employé)
- pierre.dubois / password123 (Modérateur)

## Structure du Projet

```
intrasphere-php/
├── config/              # Configuration application
├── src/
│   ├── controllers/     # Contrôleurs web et API
│   ├── models/         # Modèles de données
│   └── utils/          # Utilitaires et helpers
├── views/              # Templates et vues
├── public/             # Fichiers publics et uploads
├── sql/                # Scripts SQL
├── logs/               # Fichiers de log
└── index.php           # Point d'entrée principal
```

## Sécurité

- Protection CSRF sur tous les formulaires
- Validation et échappement des données
- Hachage sécurisé des mots de passe
- Headers de sécurité HTTP
- Protection contre les injections SQL
- Rate limiting sur les APIs

## Support

Pour toute question ou assistance, consultez la documentation complète ou contactez l'équipe de développement.

---

**Version :** 1.0.0
**Date :** 2025-08-10
**Compatibilité :** PHP 7.4+, MySQL 5.7+
