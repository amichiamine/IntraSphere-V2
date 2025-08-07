# Configuration .htaccess pour IntraSphere

Ce dossier contient les différentes versions du fichier .htaccess selon le type de déploiement cPanel.

## Fichiers disponibles

### Déploiement traditionnel (PHP + MySQL)
- `htaccess-principal.txt` - Fichier principal pour public_html/
- `htaccess-principal-corrected.txt` - ✅ Version optimisée avec API fonctionnelle
- `htaccess-api.txt` - ✅ **API PHP FONCTIONNELLE** - Toutes routes disponibles
- `htaccess-uploads.txt` - Sécurité du dossier uploads (si créé)
- `htaccess-config.txt` - Protection de la configuration

### Déploiement Node.js
- `htaccess-nodejs.txt` - Redirection vers l'application Node.js

## Instructions d'installation

1. **Choisir le bon fichier** selon votre type d'hébergement
2. **Renommer en .htaccess** (sans extension .txt)
3. **Placer dans le bon dossier** selon les indications
4. **Adapter les domaines** et ports selon votre configuration

## Sécurité importante

⚠️ **Attention** : Ces fichiers contiennent des directives de sécurité critiques. Ne pas modifier sans comprendre les implications.

## Support

Consultez le manuel complet : `docs/manuel-deploiement-cpanel.php`