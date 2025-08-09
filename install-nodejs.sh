#!/bin/bash
# 🚀 Script d'Installation Automatique Node.js pour IntraSphere React
# Compatible: Ubuntu, Debian, CentOS, RHEL, macOS, Windows (via WSL)

set -e

# Configuration
NODE_VERSION="20"
APP_NAME="IntraSphere React"
REQUIRED_NODE="18.0.0"
REQUIRED_NPM="9.0.0"

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Fonctions utilitaires
print_header() {
    echo -e "${PURPLE}"
    echo "=================================================="
    echo "🚀 $APP_NAME - Installation Node.js"
    echo "=================================================="
    echo -e "${NC}"
}

print_step() {
    echo -e "${BLUE}[ÉTAPE]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCÈS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[ATTENTION]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERREUR]${NC} $1"
}

detect_os() {
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        if [ -f /etc/debian_version ]; then
            echo "debian"
        elif [ -f /etc/redhat-release ]; then
            echo "redhat"
        else
            echo "linux"
        fi
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        echo "macos"
    elif [[ "$OSTYPE" == "msys" ]]; then
        echo "windows"
    else
        echo "unknown"
    fi
}

check_root() {
    if [[ $EUID -eq 0 ]]; then
        print_warning "Script exécuté en tant que root. Certaines étapes seront adaptées."
        return 0
    else
        return 1
    fi
}

install_nodejs_debian() {
    print_step "Installation Node.js $NODE_VERSION sur Debian/Ubuntu..."
    
    # Mise à jour des paquets
    sudo apt update
    
    # Installation de curl si nécessaire
    if ! command -v curl &> /dev/null; then
        sudo apt install -y curl
    fi
    
    # Ajout du repository NodeSource
    curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | sudo -E bash -
    
    # Installation Node.js
    sudo apt install -y nodejs
    
    # Vérification npm
    if ! command -v npm &> /dev/null; then
        sudo apt install -y npm
    fi
    
    print_success "Node.js installé via repository NodeSource"
}

install_nodejs_redhat() {
    print_step "Installation Node.js $NODE_VERSION sur Red Hat/CentOS..."
    
    # Installation curl si nécessaire
    if ! command -v curl &> /dev/null; then
        sudo yum install -y curl || sudo dnf install -y curl
    fi
    
    # Ajout du repository NodeSource
    curl -fsSL https://rpm.nodesource.com/setup_${NODE_VERSION}.x | sudo bash -
    
    # Installation Node.js
    sudo yum install -y nodejs || sudo dnf install -y nodejs
    
    print_success "Node.js installé via repository NodeSource"
}

install_nodejs_macos() {
    print_step "Installation Node.js $NODE_VERSION sur macOS..."
    
    # Vérification de Homebrew
    if command -v brew &> /dev/null; then
        print_step "Homebrew détecté, installation via brew..."
        brew install node@${NODE_VERSION}
        brew link node@${NODE_VERSION} --force
    else
        print_warning "Homebrew non trouvé. Installation manuelle recommandée depuis nodejs.org"
        echo "Ou installez Homebrew avec :"
        echo '/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"'
        return 1
    fi
    
    print_success "Node.js installé via Homebrew"
}

install_nodejs_nvm() {
    print_step "Installation Node.js via NVM (Node Version Manager)..."
    
    # Installation de NVM
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
    
    # Rechargement du profil
    export NVM_DIR="$HOME/.nvm"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
    [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"
    
    # Installation de la dernière version LTS
    nvm install ${NODE_VERSION}
    nvm use ${NODE_VERSION}
    nvm alias default ${NODE_VERSION}
    
    print_success "Node.js installé via NVM"
}

check_nodejs() {
    print_step "Vérification de l'installation Node.js..."
    
    if command -v node &> /dev/null; then
        NODE_CURRENT=$(node --version | sed 's/v//')
        echo "Version Node.js détectée: v$NODE_CURRENT"
        
        if [[ $(echo "$NODE_CURRENT $REQUIRED_NODE" | tr " " "\n" | sort -V | head -n1) == "$REQUIRED_NODE" ]]; then
            print_success "Node.js version compatible ✓"
            return 0
        else
            print_warning "Version Node.js trop ancienne (minimum requis: v$REQUIRED_NODE)"
            return 1
        fi
    else
        print_error "Node.js non trouvé"
        return 1
    fi
}

check_npm() {
    print_step "Vérification de npm..."
    
    if command -v npm &> /dev/null; then
        NPM_CURRENT=$(npm --version)
        echo "Version npm détectée: v$NPM_CURRENT"
        
        if [[ $(echo "$NPM_CURRENT $REQUIRED_NPM" | tr " " "\n" | sort -V | head -n1) == "$REQUIRED_NPM" ]]; then
            print_success "npm version compatible ✓"
            return 0
        else
            print_warning "Version npm trop ancienne (minimum requis: v$REQUIRED_NPM)"
            print_step "Mise à jour de npm..."
            npm install -g npm@latest
            return 0
        fi
    else
        print_error "npm non trouvé"
        return 1
    fi
}

install_global_packages() {
    print_step "Installation des paquets globaux utiles..."
    
    # Liste des paquets recommandés
    PACKAGES=(
        "pm2"           # Process manager
        "nodemon"       # Development auto-restart
        "typescript"    # TypeScript compiler
        "ts-node"       # TypeScript execution
    )
    
    for package in "${PACKAGES[@]}"; do
        if ! npm list -g "$package" &> /dev/null; then
            print_step "Installation de $package..."
            npm install -g "$package"
        else
            echo "$package déjà installé ✓"
        fi
    done
    
    print_success "Paquets globaux installés"
}

setup_project() {
    print_step "Configuration du projet IntraSphere..."
    
    # Vérification si nous sommes dans le bon répertoire
    if [[ ! -f "package.json" ]]; then
        print_warning "package.json non trouvé. Êtes-vous dans le bon répertoire ?"
        read -p "Chemin vers le projet IntraSphere (ou Enter pour répertoire actuel): " PROJECT_PATH
        
        if [[ -n "$PROJECT_PATH" ]]; then
            cd "$PROJECT_PATH" || {
                print_error "Impossible d'accéder au répertoire $PROJECT_PATH"
                exit 1
            }
        fi
    fi
    
    # Installation des dépendances
    if [[ -f "package.json" ]]; then
        print_step "Installation des dépendances du projet..."
        npm install
        print_success "Dépendances installées ✓"
        
        # Vérification de la structure
        if [[ -f "vite.config.ts" && -d "client/src" ]]; then
            print_success "Structure du projet React validée ✓"
        else
            print_warning "Structure de projet non reconnue"
        fi
    else
        print_error "package.json toujours non trouvé dans $(pwd)"
    fi
}

create_startup_scripts() {
    print_step "Création des scripts de démarrage..."
    
    # Script de développement
    cat > start-dev.sh << 'EOF'
#!/bin/bash
echo "🚀 Démarrage d'IntraSphere en mode développement..."
npm run dev
EOF
    chmod +x start-dev.sh
    
    # Script de production
    cat > start-prod.sh << 'EOF'
#!/bin/bash
echo "🚀 Démarrage d'IntraSphere en mode production..."
npm run build
npm start
EOF
    chmod +x start-prod.sh
    
    # Script PM2
    cat > ecosystem.config.js << 'EOF'
module.exports = {
  apps: [{
    name: 'intrasphere',
    script: 'npm',
    args: 'start',
    instances: 'max',
    exec_mode: 'cluster',
    env: {
      NODE_ENV: 'production',
      PORT: 5000
    },
    error_file: './logs/err.log',
    out_file: './logs/out.log',
    log_file: './logs/combined.log',
    time: true
  }]
}
EOF
    
    # Création du dossier logs
    mkdir -p logs
    
    print_success "Scripts de démarrage créés ✓"
}

display_summary() {
    print_header
    echo -e "${GREEN}🎉 Installation terminée avec succès !${NC}"
    echo ""
    echo "📋 Résumé de l'installation :"
    echo "  • Node.js version: $(node --version)"
    echo "  • npm version: $(npm --version)"
    echo "  • Répertoire projet: $(pwd)"
    echo ""
    echo "🚀 Commandes disponibles :"
    echo "  • Développement: ${BLUE}./start-dev.sh${NC} ou ${BLUE}npm run dev${NC}"
    echo "  • Production: ${BLUE}./start-prod.sh${NC} ou ${BLUE}npm start${NC}"
    echo "  • PM2 (cluster): ${BLUE}pm2 start ecosystem.config.js${NC}"
    echo ""
    echo "🌐 L'application sera accessible sur :"
    echo "  • Développement: http://localhost:5000"
    echo "  • Production: http://votre-domaine.com"
    echo ""
    echo "👤 Identifiants par défaut :"
    echo "  • Utilisateur: admin"
    echo "  • Mot de passe: admin"
    echo "  ${YELLOW}⚠️ Changez ces identifiants après la première connexion !${NC}"
    echo ""
    echo "📚 Documentation :"
    echo "  • README.md - Guide d'utilisation"
    echo "  • replit.md - Documentation technique"
    echo ""
}

show_next_steps() {
    echo -e "${PURPLE}📋 Prochaines étapes recommandées :${NC}"
    echo ""
    echo "1. ${BLUE}Configuration de la base de données${NC}"
    echo "   Si vous utilisez PostgreSQL externe :"
    echo "   export DATABASE_URL='postgresql://user:pass@host:port/db'"
    echo ""
    echo "2. ${BLUE}Variables d'environnement${NC}"
    echo "   Créez un fichier .env avec vos paramètres :"
    echo "   cp .env.example .env"
    echo ""
    echo "3. ${BLUE}Configuration SSL (Production)${NC}"
    echo "   Configurez HTTPS avec certbot ou votre fournisseur SSL"
    echo ""
    echo "4. ${BLUE}Monitoring (Optionnel)${NC}"
    echo "   pm2 monitor - Interface de monitoring PM2"
    echo ""
    echo "5. ${BLUE}Sauvegarde${NC}"
    echo "   Configurez des sauvegardes automatiques de votre base de données"
    echo ""
}

# Programme principal
main() {
    print_header
    
    # Détection du système
    OS=$(detect_os)
    print_step "Système détecté: $OS"
    
    # Vérification des droits
    if check_root; then
        print_warning "Exécution en tant que root détectée"
    fi
    
    # Vérification si Node.js est déjà installé
    if check_nodejs && check_npm; then
        print_success "Node.js et npm sont déjà installés et compatibles !"
    else
        print_step "Installation de Node.js nécessaire..."
        
        case $OS in
            "debian")
                install_nodejs_debian
                ;;
            "redhat")
                install_nodejs_redhat
                ;;
            "macos")
                install_nodejs_macos
                ;;
            *)
                print_warning "Système non reconnu, tentative d'installation via NVM..."
                install_nodejs_nvm
                ;;
        esac
        
        # Revérification après installation
        if ! check_nodejs || ! check_npm; then
            print_error "Échec de l'installation Node.js"
            exit 1
        fi
    fi
    
    # Installation des paquets globaux
    install_global_packages
    
    # Configuration du projet
    setup_project
    
    # Création des scripts
    create_startup_scripts
    
    # Affichage du résumé
    display_summary
    show_next_steps
    
    print_success "🎉 Installation complète d'IntraSphere React terminée !"
}

# Vérification des arguments
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    if [[ "$1" == "--help" ]] || [[ "$1" == "-h" ]]; then
        echo "Usage: $0 [options]"
        echo ""
        echo "Options:"
        echo "  --help, -h     Afficher cette aide"
        echo "  --nvm         Forcer l'installation via NVM"
        echo "  --no-global   Ne pas installer les paquets globaux"
        echo ""
        exit 0
    fi
    
    if [[ "$1" == "--nvm" ]]; then
        install_nodejs_nvm
        exit 0
    fi
    
    main "$@"
fi