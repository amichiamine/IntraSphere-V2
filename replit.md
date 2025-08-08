# Replit.md

## Overview
IntraSphere is a React-based enterprise intranet portal for corporate communication, offering features like announcements, document management, employee directory, internal messaging, and complaints/reclamations management. The platform aims to provide a comprehensive, modern, and user-friendly experience for internal communication and management within an organization, with a focus on a polished UI/UX using advanced design principles like glass morphism. Its vision is to be a comprehensive internal communication and management solution, enhancing corporate efficiency and employee engagement.

## User Preferences
Preferred communication style: Simple, everyday language.

## Recent Changes
- **2025-08-08**: Completed full compatibility analysis and 5% enhancement implementation
- Added advanced state management system with persistence and cross-tab sync
- Implemented intelligent client-side caching with LRU/LFU strategies
- Enhanced WebSocket client with auto-reconnection and real-time features
- Added Service Worker for PWA capabilities and offline support
- Created integration layer orchestrating all enhanced systems
- Achieved 100% backend-frontend compatibility
- Cleaned up project structure, removing redundant documentation

## System Architecture

### Frontend Architecture
- **Framework**: React 18 with TypeScript and Vite.
- **UI Library**: shadcn/ui components built on Radix UI primitives with Tailwind CSS.
- **Design System**: Glass morphism with custom CSS variables and gradient overlays, featuring rounded corners, responsiveness (mobile-first), custom CSS animations, and a theme system using CSS custom properties.
- **State Management**: TanStack React Query for server state management and caching.
- **Routing**: Wouter for lightweight client-side routing.
- **Form Handling**: React Hook Form with Zod validation.

### Backend Architecture
- **Runtime**: Node.js with Express.js server.
- **Language**: TypeScript with ES modules.
- **Database ORM**: Drizzle ORM for type-safe database operations.
- **Validation**: Zod schemas shared between client and server.
- **Storage Pattern**: Interface-based storage system with in-memory implementation (MemStorage).
- **API Design**: RESTful API with structured error handling and request logging. Backend modularized into specialized modules (auth, users, content, messaging, training, admin).
- **Universal Installation System**: Complete multi-environment deployment with 5 specialized packages (Universal Auto-Detect, cPanel Node.js, Windows, Linux/Mac, VS Code Development), automatic environment detection, and intelligent platform recognition.
- **Universal API Response System**: Compatible with PHP for seamless cross-platform communication.
- **WebSocket Real-Time System**: Comprehensive service for real-time events.
- **Advanced File Upload System**: Secure multer-based service with MIME validation and size limits.

### Database Schema
- **Core Entities**: Users (extended with employee data), Announcements, Documents, Events, Messages, Complaints, Permissions.
- **Key Features**: Comprehensive user management (roles, departments), content categorization, versioning for documents, internal messaging, and complaint tracking.
- **Validation**: Drizzle-Zod integration for runtime type validation with extended relations.

### Feature Specifications
- **Content Management**: Advanced personalization including customizable view modes, featured content, ratings, category visibility, download permissions, sorting, and filtering.
- **Views Management System**: Configuration, layout, and permissions tabs for platform sections with real-time preview and access control.
- **E-Learning Platform**: Full e-learning database schema, extended storage interface, comprehensive API, student learning interface, and training administration system.
- **Permissions System**: Comprehensive permission groups, role-based access control, bulk permission management.
- **Forum System**: Full CRUD operations for categories, topics, posts, and likes with role-based access control and moderation.
- **Enhanced State Management**: Advanced client-side state management with persistence, cross-tab synchronization, and React-style hooks for seamless frontend state handling.
- **Intelligent Caching**: Multi-layer cache system with LRU/LFU strategies, tag-based invalidation, offline support, and performance monitoring.
- **Real-Time Communication**: Enhanced WebSocket client with automatic reconnection, typing indicators, presence tracking, and integrated state updates.
- **Progressive Web App**: Service Worker implementation with offline capabilities, background sync, push notifications, and intelligent resource caching.

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