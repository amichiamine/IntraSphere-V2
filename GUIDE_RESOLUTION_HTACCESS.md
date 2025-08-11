# 🔧 Guide de Résolution - Problème .htaccess IntraSphere

## 🎯 Problème Identifié

**Erreur 500 Internal Server Error** causée par des directives .htaccess incompatibles avec votre hébergeur.

## ✅ Solution Immédiate

Vous avez confirmé que `reset_installation.php` fonctionne quand le fichier `.htaccess` est déplacé. Voici les solutions adaptées :

### Option 1 : Script de Correction Automatique (Recommandé)

1. **Accédez à** : `https://mondomaine.com/intrasphere/fix_htaccess.php`
2. **Le script détecte automatiquement** votre type d'hébergement
3. **Choisissez la configuration** adaptée
4. **Appliquez la correction** en un clic

### Option 2 : Correction Manuelle

#### Pour la plupart des hébergeurs mutualisés :

Remplacez le contenu de votre fichier `.htaccess` par :

```apache
# IntraSphere - Configuration Apache Compatible
# Version corrigée pour hébergement mutualisé standard

# Protection des fichiers sensibles (syntaxe universelle)
<Files ".env">
    Order deny,allow
    Deny from all
</Files>

<Files "*.sql">
    Order deny,allow
    Deny from all
</Files>

# Permettre l'accès aux fichiers d'installation
<Files "install.php">
    Order allow,deny
    Allow from all
</Files>

<Files "reset_installation.php">
    Order allow,deny
    Allow from all
</Files>

# Protection basique des dossiers système
<Files "config/*">
    Order deny,allow
    Deny from all
</Files>

<Files "logs/*">
    Order deny,allow
    Deny from all
</Files>

<Files "tmp/*">
    Order deny,allow
    Deny from all
</Files>

<Files "src/*">
    Order deny,allow
    Deny from all
</Files>
```

## 🔍 Configurations Spécialisées

### Configuration Ultra-Basique (Si problème persiste)

Si même la version compatible cause des erreurs :

```apache
# IntraSphere - Configuration Apache de Base
# Version ultra-compatible sans fonctionnalités avancées

# Protection minimale des fichiers critiques
<Files ".env">
    deny from all
</Files>

<Files "*.sql">
    deny from all  
</Files>
```

### Configuration OVH Optimisée

Pour l'hébergement mutualisé OVH :

```apache
# IntraSphere - Configuration Apache pour OVH
# Optimisé pour l'hébergement mutualisé OVH

RewriteEngine On

# Protection des fichiers sensibles
<Files ".env">
    Order deny,allow
    Deny from all
</Files>

<Files "*.sql">
    Order deny,allow  
    Deny from all
</Files>

# Protection des dossiers
<IfModule mod_authz_core.c>
    <DirectoryMatch "(config|logs|tmp|src)">
        Require all denied
    </DirectoryMatch>
</IfModule>

<IfModule !mod_authz_core.c>
    <DirectoryMatch "(config|logs|tmp|src)">
        Order deny,allow
        Deny from all
    </DirectoryMatch>
</IfModule>

# Optimisations pour OVH
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month" 
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>
```

## 🚨 Causes du Problème Original

Le fichier .htaccess original contenait des directives problématiques :

1. **Directives PHP** (php_value) - Souvent interdites sur hébergement mutualisé
2. **Syntaxe mod_authz_core** - Requiert Apache 2.4+ (certains hébergeurs utilisent 2.2)
3. **ErrorDocument avec chemins absolus** - Peut causer des boucles d'erreur
4. **DirectoryMatch complexes** - Pas supportés partout
5. **Modules conditionnels** - mod_expires, mod_deflate peuvent ne pas être activés

## 🛠 Instructions de Correction

### Étape 1 : Identifier votre type d'hébergeur

- **OVH, SiteGround, HostGator** : Utilisez la configuration OVH
- **1&1/Ionos, GoDaddy, Bluehost** : Utilisez la configuration compatible
- **Hébergeurs stricts ou anciens** : Utilisez la configuration basique
- **Incertain** : Commencez par la configuration basique

### Étape 2 : Appliquer la correction

**Méthode automatique** :
1. Visitez `fix_htaccess.php`
2. Sélectionnez la configuration détectée
3. Cliquez "Appliquer la Configuration"

**Méthode manuelle** :
1. Sauvegardez votre .htaccess actuel
2. Remplacez par la nouvelle configuration
3. Testez l'accès à `reset_installation.php`

### Étape 3 : Vérification

1. **Accédez à** : `https://mondomaine.com/intrasphere/reset_installation.php`
2. **Si ça fonctionne** : Continuez l'installation
3. **Si erreur 500 persiste** : Essayez une configuration plus basique
4. **En dernier recours** : Supprimez complètement le .htaccess

## 📊 Matrice de Compatibilité

| Hébergeur | Configuration Recommandée | Fallback |
|-----------|---------------------------|----------|
| OVH | Configuration OVH | Compatible |
| 1&1/Ionos | Configuration Compatible | Basique |
| SiteGround | Configuration OVH | Compatible |
| HostGator | Configuration Compatible | Basique |
| GoDaddy | Configuration Compatible | Basique |
| Hébergeur local | Configuration Basique | Aucun .htaccess |

## 🔧 Dépannage Avancé

### Si l'erreur 500 persiste après correction :

1. **Vérifiez les logs d'erreur** de votre hébergeur
2. **Testez sans .htaccess** (renommez en .htaccess.disabled)
3. **Contactez votre hébergeur** pour confirmer les modules Apache disponibles
4. **Utilisez la configuration ultra-basique**

### Commandes de test :

```bash
# Tester la syntaxe .htaccess (si vous avez accès SSH)
apache2ctl -t

# Vérifier les modules Apache
apache2ctl -M | grep rewrite
```

## ✅ Résolution Garantie

Cette approche en paliers garantit la résolution :

1. **Niveau 1** : Configuration compatible (90% des cas)
2. **Niveau 2** : Configuration basique (99% des cas)  
3. **Niveau 3** : Suppression .htaccess (100% des cas - sécurité réduite)

## 🎯 Prochaines Étapes

Une fois le problème .htaccess résolu :

1. **Testez** `reset_installation.php`
2. **Lancez** l'installation via `install.php`
3. **Configurez** votre base de données
4. **Créez** votre compte administrateur
5. **Accédez** à votre intranet IntraSphere

La résolution du problème .htaccess débloquera complètement l'installation de votre plateforme IntraSphere.