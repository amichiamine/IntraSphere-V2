#!/bin/bash
# 🐳 IntraSphere React - Configuration Docker Automatique
# 
# Script de configuration Docker complet avec optimisations
# Support: Docker, Docker Compose, Kubernetes

set -e

# Configuration
APP_NAME="intrasphere-react"
NODE_VERSION="20-alpine"
PORT="5000"
ENV="production"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

print_header() {
    echo -e "${PURPLE}"
    echo "=================================================="
    echo "🐳 IntraSphere React - Configuration Docker"
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

# Vérification Docker
check_docker() {
    if ! command -v docker &> /dev/null; then
        print_error "Docker n'est pas installé"
        echo "Installez Docker depuis https://docker.com/get-started"
        exit 1
    fi
    
    if ! docker info &> /dev/null; then
        print_error "Docker daemon n'est pas en marche"
        echo "Démarrez Docker Desktop ou le service Docker"
        exit 1
    fi
    
    print_success "Docker est prêt"
}

# Génération Dockerfile optimisé
generate_dockerfile() {
    print_step "Génération du Dockerfile optimisé..."
    
    cat > Dockerfile << EOF
# 🐳 IntraSphere React - Dockerfile Multi-Stage Optimisé
FROM node:${NODE_VERSION} AS builder

# Métadonnées
LABEL maintainer="IntraSphere Team"
LABEL description="Plateforme Intranet Moderne React"
LABEL version="2.0.0"

# Variables d'environnement build
ENV NODE_ENV=production
ENV GENERATE_SOURCEMAP=false
ENV INLINE_RUNTIME_CHUNK=false

# Répertoire de travail
WORKDIR /app

# Copie des fichiers de dépendances
COPY package*.json ./
COPY tsconfig.json ./
COPY vite.config.ts ./
COPY tailwind.config.ts ./

# Installation des dépendances (cache Docker optimisé)
RUN npm ci --only=production && npm cache clean --force

# Copie du code source
COPY client/ ./client/
COPY server/ ./server/
COPY shared/ ./shared/

# Build de l'application
RUN npm run build

# ===== IMAGE DE PRODUCTION =====
FROM node:${NODE_VERSION} AS production

# Création utilisateur non-root pour sécurité
RUN addgroup -g 1001 -S nodejs && \\
    adduser -S appuser -u 1001

# Répertoire de travail
WORKDIR /app

# Copie des dépendances
COPY package*.json ./
RUN npm ci --only=production && npm cache clean --force

# Copie des fichiers buildés
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/server ./server
COPY --from=builder /app/shared ./shared

# Permissions
RUN chown -R appuser:nodejs /app
USER appuser

# Variables d'environnement
ENV NODE_ENV=production
ENV PORT=${PORT}
ENV HOST=0.0.0.0

# Port exposé
EXPOSE ${PORT}

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \\
    CMD node -e "require('http').get('http://localhost:${PORT}/api/stats', (res) => { process.exit(res.statusCode === 200 ? 0 : 1) })"

# Commande de démarrage
CMD ["npm", "start"]
EOF

    print_success "Dockerfile généré"
}

# Génération docker-compose.yml
generate_docker_compose() {
    print_step "Génération du docker-compose.yml..."
    
    cat > docker-compose.yml << EOF
# 🐳 IntraSphere React - Docker Compose Configuration
version: '3.8'

services:
  # Application React
  intrasphere-app:
    build:
      context: .
      dockerfile: Dockerfile
      target: production
    container_name: ${APP_NAME}
    restart: unless-stopped
    ports:
      - "${PORT}:${PORT}"
    environment:
      - NODE_ENV=production
      - PORT=${PORT}
      - DATABASE_URL=\${DATABASE_URL:-postgresql://postgres:password@postgres:5432/intrasphere}
      - SESSION_SECRET=\${SESSION_SECRET:-$(openssl rand -hex 32)}
    depends_on:
      - postgres
      - redis
    networks:
      - intrasphere-network
    volumes:
      - app-logs:/app/logs
      - app-uploads:/app/uploads
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:${PORT}/api/stats"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s

  # Base de données PostgreSQL
  postgres:
    image: postgres:15-alpine
    container_name: ${APP_NAME}-db
    restart: unless-stopped
    environment:
      - POSTGRES_DB=intrasphere
      - POSTGRES_USER=postgres
      - POSTGRES_PASSWORD=\${POSTGRES_PASSWORD:-password}
    volumes:
      - postgres-data:/var/lib/postgresql/data
      - ./sql:/docker-entrypoint-initdb.d
    networks:
      - intrasphere-network
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U postgres"]
      interval: 10s
      timeout: 5s
      retries: 5

  # Cache Redis
  redis:
    image: redis:7-alpine
    container_name: ${APP_NAME}-cache
    restart: unless-stopped
    command: redis-server --appendonly yes --maxmemory 256mb --maxmemory-policy allkeys-lru
    volumes:
      - redis-data:/data
    networks:
      - intrasphere-network
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 3s
      retries: 3

  # Reverse Proxy Nginx (optionnel)
  nginx:
    image: nginx:alpine
    container_name: ${APP_NAME}-proxy
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf:ro
      - ./ssl:/etc/nginx/ssl:ro
    depends_on:
      - intrasphere-app
    networks:
      - intrasphere-network
    profiles:
      - production

volumes:
  postgres-data:
    driver: local
  redis-data:
    driver: local
  app-logs:
    driver: local
  app-uploads:
    driver: local

networks:
  intrasphere-network:
    driver: bridge
    ipam:
      config:
        - subnet: 172.20.0.0/16
EOF

    print_success "docker-compose.yml généré"
}

# Configuration Nginx pour Docker
generate_nginx_config() {
    print_step "Génération de la configuration Nginx..."
    
    cat > nginx.conf << EOF
events {
    worker_connections 1024;
}

http {
    upstream intrasphere_app {
        server intrasphere-app:${PORT};
    }

    # Configuration générale
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    client_max_body_size 50M;

    # Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # MIME types
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logs
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # Rate limiting
    limit_req_zone \$binary_remote_addr zone=api:10m rate=10r/s;
    limit_req_zone \$binary_remote_addr zone=static:10m rate=30r/s;

    server {
        listen 80;
        server_name localhost;
        
        # Redirection HTTPS (production)
        # return 301 https://\$server_name\$request_uri;
        
        # API et WebSocket
        location /api/ {
            limit_req zone=api burst=20 nodelay;
            proxy_pass http://intrasphere_app;
            proxy_http_version 1.1;
            proxy_set_header Upgrade \$http_upgrade;
            proxy_set_header Connection 'upgrade';
            proxy_set_header Host \$host;
            proxy_set_header X-Real-IP \$remote_addr;
            proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto \$scheme;
            proxy_cache_bypass \$http_upgrade;
        }

        # WebSocket
        location /ws {
            proxy_pass http://intrasphere_app;
            proxy_http_version 1.1;
            proxy_set_header Upgrade \$http_upgrade;
            proxy_set_header Connection "upgrade";
            proxy_set_header Host \$host;
            proxy_set_header X-Real-IP \$remote_addr;
            proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto \$scheme;
        }

        # Application React
        location / {
            limit_req zone=static burst=50 nodelay;
            proxy_pass http://intrasphere_app;
            proxy_set_header Host \$host;
            proxy_set_header X-Real-IP \$remote_addr;
            proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto \$scheme;
            
            # Cache statique
            location ~* \\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)\$ {
                expires 1y;
                add_header Cache-Control "public, immutable";
            }
        }

        # Health check
        location /health {
            access_log off;
            return 200 "healthy\\n";
            add_header Content-Type text/plain;
        }
    }

    # Configuration HTTPS (à décommenter pour production)
    # server {
    #     listen 443 ssl http2;
    #     server_name localhost;
    #     
    #     ssl_certificate /etc/nginx/ssl/cert.pem;
    #     ssl_certificate_key /etc/nginx/ssl/key.pem;
    #     ssl_session_timeout 1d;
    #     ssl_session_cache shared:SSL:50m;
    #     ssl_session_tickets off;
    #     ssl_protocols TLSv1.2 TLSv1.3;
    #     ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;
    #     ssl_prefer_server_ciphers off;
    #     
    #     # Même configuration que HTTP...
    # }
}
EOF

    print_success "Configuration Nginx générée"
}

# Génération des scripts Docker
generate_docker_scripts() {
    print_step "Génération des scripts Docker..."
    
    # Script de build
    cat > docker-build.sh << 'EOF'
#!/bin/bash
# Build de l'image Docker IntraSphere React

echo "🐳 Build de l'image IntraSphere React..."
docker build -t intrasphere-react:latest .

echo "✅ Image buildée avec succès !"
echo "Démarrage avec: docker run -p 5000:5000 intrasphere-react:latest"
EOF

    # Script de démarrage rapide
    cat > docker-start.sh << 'EOF'
#!/bin/bash
# Démarrage rapide avec Docker Compose

echo "🚀 Démarrage d'IntraSphere React avec Docker Compose..."

# Création du fichier .env s'il n'existe pas
if [ ! -f .env ]; then
    echo "Génération du fichier .env..."
    cat > .env << ENVEOF
NODE_ENV=production
PORT=5000
DATABASE_URL=postgresql://postgres:password@postgres:5432/intrasphere
SESSION_SECRET=$(openssl rand -hex 32)
POSTGRES_PASSWORD=password
ENVEOF
fi

# Démarrage des services
docker-compose up -d

echo "✅ Services démarrés !"
echo "🌐 Application : http://localhost:5000"
echo "📊 Logs : docker-compose logs -f"
echo "🛑 Arrêt : docker-compose down"
EOF

    # Script de développement
    cat > docker-dev.sh << 'EOF'
#!/bin/bash
# Mode développement avec hot reload

echo "🛠️ Démarrage en mode développement..."

# Build de l'image de développement
docker build -t intrasphere-react:dev --target builder .

# Démarrage avec volumes montés pour hot reload
docker run -it --rm \
  -p 5000:5000 \
  -v $(pwd)/client:/app/client \
  -v $(pwd)/server:/app/server \
  -v $(pwd)/shared:/app/shared \
  -e NODE_ENV=development \
  intrasphere-react:dev \
  npm run dev

echo "🌐 Application disponible sur http://localhost:5000"
EOF

    chmod +x docker-build.sh docker-start.sh docker-dev.sh
    print_success "Scripts Docker générés et rendus exécutables"
}

# Configuration Kubernetes (optionnel)
generate_kubernetes_config() {
    print_step "Génération des manifestes Kubernetes..."
    
    mkdir -p k8s
    
    # Deployment
    cat > k8s/deployment.yaml << EOF
apiVersion: apps/v1
kind: Deployment
metadata:
  name: intrasphere-react
  labels:
    app: intrasphere-react
spec:
  replicas: 3
  selector:
    matchLabels:
      app: intrasphere-react
  template:
    metadata:
      labels:
        app: intrasphere-react
    spec:
      containers:
      - name: intrasphere-react
        image: intrasphere-react:latest
        ports:
        - containerPort: 5000
        env:
        - name: NODE_ENV
          value: "production"
        - name: PORT
          value: "5000"
        - name: DATABASE_URL
          valueFrom:
            secretKeyRef:
              name: intrasphere-secrets
              key: database-url
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /api/stats
            port: 5000
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /api/stats
            port: 5000
          initialDelaySeconds: 5
          periodSeconds: 5
EOF

    # Service
    cat > k8s/service.yaml << EOF
apiVersion: v1
kind: Service
metadata:
  name: intrasphere-react-service
spec:
  selector:
    app: intrasphere-react
  ports:
    - protocol: TCP
      port: 80
      targetPort: 5000
  type: ClusterIP
EOF

    # Ingress
    cat > k8s/ingress.yaml << EOF
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: intrasphere-react-ingress
  annotations:
    nginx.ingress.kubernetes.io/rewrite-target: /
spec:
  rules:
  - host: intrasphere.local
    http:
      paths:
      - path: /
        pathType: Prefix
        backend:
          service:
            name: intrasphere-react-service
            port:
              number: 80
EOF

    print_success "Manifestes Kubernetes générés dans k8s/"
}

# Affichage du résumé
show_summary() {
    print_header
    echo -e "${GREEN}🎉 Configuration Docker terminée !${NC}"
    echo ""
    echo "📋 Fichiers générés :"
    echo "  • Dockerfile - Image multi-stage optimisée"
    echo "  • docker-compose.yml - Stack complète avec PostgreSQL et Redis"
    echo "  • nginx.conf - Configuration reverse proxy"
    echo "  • docker-build.sh - Script de build"
    echo "  • docker-start.sh - Démarrage rapide"
    echo "  • docker-dev.sh - Mode développement"
    echo "  • k8s/ - Manifestes Kubernetes"
    echo ""
    echo "🚀 Commandes de démarrage :"
    echo "  • Rapide: ${BLUE}./docker-start.sh${NC}"
    echo "  • Build: ${BLUE}./docker-build.sh${NC}"
    echo "  • Développement: ${BLUE}./docker-dev.sh${NC}"
    echo "  • Production: ${BLUE}docker-compose --profile production up -d${NC}"
    echo ""
    echo "🌐 Accès à l'application :"
    echo "  • Application: http://localhost:${PORT}"
    echo "  • Proxy Nginx: http://localhost (si activé)"
    echo ""
    echo "📊 Monitoring :"
    echo "  • Logs: ${BLUE}docker-compose logs -f${NC}"
    echo "  • Stats: ${BLUE}docker stats${NC}"
    echo "  • Health: ${BLUE}curl http://localhost:${PORT}/api/stats${NC}"
    echo ""
    echo "👤 Identifiants par défaut :"
    echo "  • Utilisateur: admin"
    echo "  • Mot de passe: admin"
    echo ""
    print_warning "Changez les identifiants après la première connexion !"
}

# Programme principal
main() {
    print_header
    
    check_docker
    generate_dockerfile
    generate_docker_compose
    generate_nginx_config
    generate_docker_scripts
    generate_kubernetes_config
    
    show_summary
    
    # Option de démarrage immédiat
    echo ""
    read -p "Voulez-vous démarrer l'application maintenant ? (y/N): " start_now
    if [[ $start_now =~ ^[Yy]$ ]]; then
        ./docker-start.sh
    fi
}

# Exécution
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi