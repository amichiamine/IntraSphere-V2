#!/bin/bash
echo "🚀 IntraSphere - Build pour cPanel (Statique)"
echo "=============================================="

# Vérification Node.js
if ! command -v node &> /dev/null; then
    echo "❌ Node.js non trouvé. Installation requise."
    exit 1
fi

echo "📦 [1/3] Build de l'application..."
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Erreur lors du build"
    exit 1
fi

echo "📁 [2/3] Préparation des fichiers..."
mkdir -p cpanel-deploy
cp -r dist/* cpanel-deploy/

# Création .htaccess pour SPA
cat > cpanel-deploy/.htaccess << 'EOF'
# IntraSphere - Configuration SPA
RewriteEngine On
RewriteBase /

# Gestion des fichiers statiques
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Redirection vers index.html pour les routes React
RewriteRule . /index.html [L]

# Cache des assets
<FilesMatch "\.(css|js|png|jpg|jpeg|gif|ico|svg)$">
    ExpiresActive On
    ExpiresDefault "access plus 1 month"
</FilesMatch>

# Sécurité
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
EOF

echo "📦 [3/3] Création du package..."
cd cpanel-deploy
zip -r ../intrasphere-cpanel-static.zip . -x "*.DS_Store" "*.git*"
cd ..

echo "✅ Package créé: intrasphere-cpanel-static.zip"
echo ""
echo "📋 Instructions de déploiement:"
echo "1. Uploadez intrasphere-cpanel-static.zip dans cPanel File Manager"
echo "2. Extractez dans public_html/ (ou sous-dossier)"
echo "3. L'application sera accessible via votre domaine"
echo ""
echo "🔧 Mode statique: Pas d'API backend, données en mémoire uniquement"