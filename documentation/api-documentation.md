# Documentation API IntraSphere

## Vue d'Ensemble

L'API IntraSphere fournit un accès programmatique à toutes les fonctionnalités de la plateforme intranet d'entreprise. Elle est conçue selon les principes REST et utilise JSON pour l'échange de données.

## Configuration Base

- **URL de base** : `/api/v1`
- **Format** : JSON
- **Authentification** : Session-based
- **Rate Limiting** : Oui (voir section Sécurité)

## Authentification

### POST /api/auth/login
Authentifier un utilisateur et créer une session.

**Paramètres :**
```json
{
  "username": "string (requis)",
  "password": "string (requis)"
}
```

**Réponse succès (200) :**
```json
{
  "id": "string",
  "username": "string",
  "name": "string",
  "email": "string",
  "role": "employee|moderator|admin",
  "department": "string",
  "isActive": boolean
}
```

**Rate Limit :** 5 tentatives par 5 minutes

### POST /api/auth/logout
Déconnecter l'utilisateur et détruire la session.

**Réponse :** 200 OK

### GET /api/auth/me
Obtenir les informations de l'utilisateur connecté.

**Réponse (200) :** Objet utilisateur sans mot de passe

### POST /api/auth/register
Créer un nouveau compte utilisateur.

**Paramètres :**
```json
{
  "username": "string (requis, 3-50 chars)",
  "password": "string (requis, voir règles)",
  "name": "string (requis, 2-100 chars)",
  "email": "string (optionnel, valide)",
  "department": "string (optionnel)"
}
```

**Rate Limit :** 3 tentatives par 15 minutes

### POST /api/auth/change-password
Changer le mot de passe de l'utilisateur connecté.

**Paramètres :**
```json
{
  "current_password": "string (requis)",
  "new_password": "string (requis, voir règles)"
}
```

### POST /api/auth/forgot-password
Demander une réinitialisation de mot de passe.

**Paramètres :**
```json
{
  "email": "string (requis)"
}
```

**Rate Limit :** 3 tentatives par heure

## Gestion des Utilisateurs

### GET /api/users
Lister les utilisateurs (admin/moderator uniquement).

**Paramètres de requête :**
- `page` : Numéro de page (défaut: 1)
- `limit` : Éléments par page (défaut: 20, max: 100)
- `role` : Filtrer par rôle
- `department` : Filtrer par département
- `search` : Recherche par nom/username

**Réponse (200) :**
```json
{
  "data": [
    {
      "id": "string",
      "username": "string",
      "name": "string",
      "email": "string",
      "role": "string",
      "department": "string",
      "isActive": boolean,
      "createdAt": "ISO date"
    }
  ],
  "pagination": {
    "current_page": number,
    "per_page": number,
    "total": number,
    "total_pages": number,
    "has_next": boolean,
    "has_prev": boolean
  }
}
```

### GET /api/users/:id
Obtenir un utilisateur spécifique.

**Permission :** Admin/Moderator ou propriétaire

### PUT /api/users/:id
Mettre à jour un utilisateur.

**Permission :** Admin ou propriétaire

### DELETE /api/users/:id
Supprimer un utilisateur.

**Permission :** Admin uniquement

### POST /api/users/:id/toggle-status
Activer/désactiver un utilisateur.

**Permission :** Admin uniquement

## Annonces

### GET /api/announcements
Lister les annonces.

**Paramètres de requête :**
- `page`, `limit` : Pagination
- `category` : Filtrer par catégorie
- `priority` : Filtrer par priorité (low, medium, high)

### POST /api/announcements
Créer une annonce.

**Permission :** Admin/Moderator

**Paramètres :**
```json
{
  "title": "string (requis, 3-200 chars)",
  "content": "string (requis)",
  "category": "string (optionnel)",
  "priority": "low|medium|high (défaut: medium)",
  "isPublished": boolean
}
```

### GET /api/announcements/:id
Obtenir une annonce spécifique.

### PUT /api/announcements/:id
Mettre à jour une annonce.

**Permission :** Admin/Moderator ou auteur

### DELETE /api/announcements/:id
Supprimer une annonce.

**Permission :** Admin/Moderator ou auteur

## Documents

### GET /api/documents
Lister les documents.

**Paramètres de requête :**
- `page`, `limit` : Pagination
- `category` : Filtrer par catégorie
- `type` : Filtrer par type de fichier

### POST /api/documents
Uploader un document.

**Permission :** Admin/Moderator

**Format :** multipart/form-data
- `file` : Fichier (max 5MB)
- `title` : Titre du document
- `description` : Description (optionnel)
- `category` : Catégorie (optionnel)

### GET /api/documents/:id/download
Télécharger un document.

### DELETE /api/documents/:id
Supprimer un document.

**Permission :** Admin/Moderator ou auteur

## Événements

### GET /api/events
Lister les événements.

**Paramètres de requête :**
- `page`, `limit` : Pagination
- `from` : Date de début (ISO)
- `to` : Date de fin (ISO)
- `type` : Type d'événement

### POST /api/events
Créer un événement.

**Permission :** Admin/Moderator

**Paramètres :**
```json
{
  "title": "string (requis)",
  "description": "string (optionnel)",
  "start_date": "ISO date (requis)",
  "end_date": "ISO date (optionnel)",
  "location": "string (optionnel)",
  "type": "meeting|training|social|other"
}
```

## Messages/Notifications

### GET /api/messages
Lister les messages de l'utilisateur.

### POST /api/messages
Envoyer un message.

**Paramètres :**
```json
{
  "recipient_id": "string (requis)",
  "subject": "string (requis)",
  "content": "string (requis)"
}
```

### PUT /api/messages/:id/read
Marquer un message comme lu.

## Formations

### GET /api/trainings
Lister les formations.

### POST /api/trainings
Créer une formation.

**Permission :** Admin/Moderator

### POST /api/trainings/:id/enroll
S'inscrire à une formation.

### GET /api/trainings/:id/participants
Lister les participants d'une formation.

## Statistiques

### GET /api/stats
Obtenir les statistiques générales.

**Permission :** Admin/Moderator

**Réponse :**
```json
{
  "totalUsers": number,
  "totalAnnouncements": number,
  "totalDocuments": number,
  "totalEvents": number,
  "activeUsers": number,
  "recentActivity": array
}
```

## Gestion des Erreurs

### Codes de Statut
- `200` : Succès
- `201` : Créé
- `204` : Pas de contenu
- `400` : Requête invalide
- `401` : Non authentifié
- `403` : Accès refusé
- `404` : Non trouvé
- `422` : Erreur de validation
- `429` : Trop de requêtes
- `500` : Erreur serveur

### Format des Erreurs
```json
{
  "message": "Description de l'erreur",
  "errors": ["Détail 1", "Détail 2"]
}
```

## Sécurité

### Rate Limiting
- **Login** : 5 tentatives / 5 minutes
- **Forgot Password** : 3 tentatives / heure
- **Register** : 3 tentatives / 15 minutes
- **API General** : 100 requêtes / heure

### Validation des Mots de Passe
- Minimum 8 caractères
- Au moins 1 majuscule
- Au moins 1 minuscule
- Au moins 1 chiffre
- Au moins 1 caractère spécial

### En-têtes de Sécurité
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security` (HTTPS uniquement)

## Exemples d'Utilisation

### Authentification et Création d'Annonce
```javascript
// 1. Connexion
const loginResponse = await fetch('/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    username: 'admin',
    password: 'SecurePass123!'
  })
});

const user = await loginResponse.json();

// 2. Créer une annonce
const announcementResponse = await fetch('/api/announcements', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  credentials: 'include', // Important pour les sessions
  body: JSON.stringify({
    title: 'Nouvelle politique',
    content: 'Contenu de l\'annonce...',
    priority: 'high'
  })
});
```

### Upload de Document
```javascript
const formData = new FormData();
formData.append('file', fileInput.files[0]);
formData.append('title', 'Mon document');
formData.append('category', 'Policies');

const response = await fetch('/api/documents', {
  method: 'POST',
  credentials: 'include',
  body: formData
});
```

## Notes de Version

### v1.0 (Actuelle)
- API complète avec toutes les fonctionnalités core
- Authentification par session
- Rate limiting implémenté
- Validation robuste des données
- Gestion d'erreurs unifiée

### Prochaines Versions
- v1.1 : Support WebSocket pour notifications temps réel
- v1.2 : API de recherche avancée
- v2.0 : Support JWT en alternative aux sessions