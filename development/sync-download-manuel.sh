#!/bin/bash

# 📥 Script de Synchronisation Download Manuel
# Met à jour automatiquement le dossier "Download Manuel" avec les derniers packages

echo "🔄 Synchronisation du dossier 'Download Manuel'..."

# Créer le dossier s'il n'existe pas
mkdir -p "../Download Manuel"

# Générer le package universel s'il n'existe pas
if [ ! -f "intrasphere-universal-ready.zip" ]; then
    echo "📦 Génération du package universel..."
    ./create-universal-ready-package.sh
fi

# Copier le package vers Download Manuel
if [ -f "intrasphere-universal-ready.zip" ]; then
    echo "📥 Copie du package universel (155MB)..."
    cp intrasphere-universal-ready.zip "../Download Manuel/"
    echo "✅ Package universel copié"
else
    echo "❌ Package universel non trouvé"
fi

# Copier le dossier universal-ready s'il existe
if [ -d "universal-ready" ]; then
    echo "📁 Copie du dossier décompressé..."
    cp -r universal-ready "../Download Manuel/"
    echo "✅ Dossier universal-ready copié"
else
    echo "⚠️ Dossier universal-ready non trouvé"
fi

# Vérifier la taille du dossier Download Manuel
echo "📊 Taille du dossier 'Download Manuel':"
du -sh "../Download Manuel/"

echo ""
echo "✅ Synchronisation terminée !"
echo "📁 Dossier: Download Manuel/"
echo "🔗 Contenu disponible pour téléchargement manuel"