# Replit.md

## Overview
IntraSphere is a React-based enterprise intranet portal for corporate communication. It offers features like announcements, document management, an employee directory, internal messaging, complaints/reclamations management, and admin delegation. The platform aims to provide a comprehensive, modern, and user-friendly experience for internal communication and management within an organization. It's built with a focus on a polished UI/UX using advanced design principles like glass morphism.

## User Preferences
Preferred communication style: Simple, everyday language.

## Project Structure

The project is organized into separate development and production environments:

### 📁 Development Environment (`./`)
- **Source Code**: `client/`, `server/`, `shared/` - Core application code
- **Configuration**: Root-level config files for local development (tsconfig.json, vite.config.ts, etc.)
- **Development Tools**: Standard npm scripts and development workflow

### 📁 Development Tools (`development/`)
- **Deployment Scripts**: Universal package generator and installation tools
- **Documentation**: Complete technical documentation and deployment guides
- **Production Templates**: Optimized configurations for different deployment environments

### 📁 Production Packages (`production/`)
- **Ready-to-Deploy Packages**: 5 specialized packages for all environments
- **Installation Guides**: User-friendly documentation for each deployment scenario
- **Universal Installer**: PHP-based interactive installer that eliminates path detection issues

## System Architecture

### Frontend Architecture
- **Framework**: React 18 with TypeScript and Vite.
- **UI Library**: shadcn/ui components built on Radix UI primitives with Tailwind CSS.
- **Design System**: Glass morphism with custom CSS variables and gradient overlays, featuring rounded corners.
- **State Management**: TanStack React Query for server state management and caching.
- **Routing**: Wouter for lightweight client-side routing.
- **Form Handling**: React Hook Form with Zod validation.

### Backend Architecture
- **Runtime**: Node.js with Express.js server.
- **Language**: TypeScript with ES modules.
- **Database ORM**: Drizzle ORM for type-safe database operations.
- **Validation**: Zod schemas shared between client and server.
- **Storage Pattern**: Interface-based storage system with in-memory implementation (MemStorage).
- **API Design**: RESTful API with structured error handling and request logging.

### Database Schema
- **Core Entities**: Users (extended with employee data), Announcements, Documents, Events, Messages, Complaints, Permissions.
- **Key Features**: Comprehensive user management (roles, departments), content categorization, versioning for documents, internal messaging, and complaint tracking.
- **Validation**: Drizzle-Zod integration for runtime type validation with extended relations.

### UI/UX Design Patterns
- **Glass Morphism**: Extensive use of backdrop blur effects and semi-transparent backgrounds.
- **Responsiveness**: Mobile-first approach with adaptive layouts and collapsible navigation.
- **Animation System**: Custom CSS animations for interactive elements.
- **Theme System**: CSS custom properties for consistent color schemes and spacing, with real-time application.

### Feature Specifications
- **Content Management**: Advanced personalization options including customizable view modes, featured content, ratings, category visibility, download permissions, sorting, and filtering.
- **Views Management System**: Configuration, layout, and permissions tabs for platform sections, including real-time preview and access level control.
- **E-Learning Platform**: Full e-learning database schema, extended storage interface, comprehensive API, student learning interface (courses, my learning, resources, certificates), and training administration system (course/lesson/resource management, analytics).
- **Universal Installation System**: Complete multi-environment deployment with 5 specialized packages (Universal Auto-Detect, cPanel Node.js, Windows, Linux/Mac, VS Code Development), automatic environment detection, corrected npm paths (/home/username/nodevenv/public_html/folder/version/bin/npm), intelligent platform recognition, comprehensive beginner-friendly documentation integrated in each package.

### Recent Updates (August 2025)
- **Universal Package System v2.1**: Created corrected universal package (154MB) with complete node_modules, eliminating npm install dependency and 10-20 minute wait times
- **Multi-Environment Deployer v2.1**: Developed PHP-based universal deployer with automatic platform detection (cPanel, Windows, Linux, VS Code) and intelligent database configuration
- **Zero-Configuration Deployment**: Implemented SQLite default with optional MySQL/PostgreSQL setup, enabling instant deployment with graphical assistant
- **Complete Project Restructure**: Cleaned up 500MB+ of redundant files, centralized documentation, and created comprehensive beginner guides
- **Production-Ready Package v2.1**: Generated intrasphere-universal-ready.zip with multi-platform scripts, complete documentation, and deployment assistance
- **Comprehensive Documentation System**: Created detailed inventories of frontend (docs/Inventaire-Frontend.md) and backend (docs/Inventaire-Backend.md) documenting all components, APIs, and architecture

#### Critical Bug Fixes v2.1 (August 2025)
- **Fixed Form Submission Bug**: Corrected missing form tags in deploy-universal.php step 1 that prevented deployment progression
- **Fixed Database Connection Tests**: Implemented functional MySQL/PostgreSQL connection testing with proper error handling and JSON responses
- **Fixed Package Structure Issue**: Resolved subdirectory decompression problem - packages now extract directly without universal-ready/ wrapper
- **Fixed Public Files Problem**: Created automatic copying system from dist/public/ to server/public/ for proper static file serving
- **Enhanced Error Handling**: Added comprehensive error states and user-friendly messages throughout deployment process
- **Integrated Documentation**: Added complete deployment guide accessible directly within the PHP installer interface
- **Fixed API Routing Architecture Issue**: Corrected .htaccess files to handle virtual Express.js API routes (/api/*) properly - traditional cPanel deployments now correctly show 404 for API routes as frontend expects graceful offline mode
- **Created Complete PHP API**: Developed full PHP API equivalent (`api/` directory) with all Express.js functionality - MySQL/SQLite support, authentication, CRUD operations, sessions, CORS - ensuring identical functionality across all deployment environments

#### Project Cleanup v2.1 (August 2025)
- **Eliminated File Duplication**: Removed misplaced files from root directory (deploy-universal-fixed.php, setup-public-files.js)
- **Consolidated Development Scripts**: Single create-universal-ready-package.sh script instead of multiple versions
- **Removed Duplicate Folders**: Cleaned up development/ directory removing universal-ready and universal-ready-fixed folders
- **Centralized Documentation**: Moved all report files (CORRECTIONS-v2.1-RAPPORT.md, SCAN-FINAL-v2.1.md, STRUCTURE-FINALE.md) from root to docs/
- **Streamlined Package Generation**: Clean package creation process with embedded corrections instead of external file dependencies
- **Complete Project Reorganization**: Systematic cleanup resulting in clean, maintainable project structure with 154MB optimized package (26,956 files)
- **Final Structure Validation**: Root directory contains only essential project files, documentation properly organized in docs/, single consolidated packaging script
- **Code Optimization**: Removed all obsolete files (*-old.ts, *-old.tsx), identified unused dependencies (Google Cloud, Uppy, Passport packages)
- **Architecture Validation**: Confirmed coherent frontend-backend communication, proper TypeScript/Vite configuration alignment

## External Dependencies

### Database
- **Neon Database**: Serverless PostgreSQL database.

### UI Component Library
- **Radix UI**: Accessible, unstyled UI primitives.
- **Tailwind CSS**: Utility-first CSS framework.
- **Lucide React**: Icon library.

### Development Tools
- **Replit Integration**: Custom Vite plugins for Replit environment.
- **TypeScript**: Full type safety.
- **PostCSS**: CSS processing.

### Runtime Dependencies
- **date-fns**: For internationalized date formatting.
- **clsx and tailwind-merge**: For conditional styling.
- **Zod**: For schema validation.