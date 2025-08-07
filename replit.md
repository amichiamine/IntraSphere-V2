## Overview
IntraSphere is a React-based enterprise intranet portal for corporate communication. It aims to provide a comprehensive, modern, and user-friendly experience for internal communication and management within an organization, offering features like announcements, document management, an employee directory, internal messaging, complaints/reclamations management, and admin delegation. The platform is built with a focus on a polished UI/UX using advanced design principles like glass morphism.

## User Preferences
Preferred communication style: Simple, everyday language.

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
- **Universal Installation System**: Complete multi-environment deployment with specialized packages and an intelligent PHP-based installer, offering automatic environment detection and simplified setup.

### Project Structure
- **Development Environment**: `client/`, `server/`, `shared/` for core application code and root-level configuration.
- **Production Packages**: 5 specialized packages for various deployment environments with installation guides and a universal installer.

### Recent Improvements (2025-08-07)
- **Frontend-Backend Compatibility**: Achieved 100% compatibility by implementing all missing API endpoints
- **Complete API Coverage**: Added 25+ new endpoints covering forum, training, resources, user management, and analytics
- **TypeScript Optimization**: Resolved all 26 LSP diagnostics across server and client codebases
- **Express Request Extension**: Added proper TypeScript declarations for user session properties
- **Schema Enhancement**: Extended enrollments table with timeSpent, score, and courseTitle fields for analytics
- **Code Deduplication**: Removed duplicate function implementations in storage layer
- **Forum System**: Full implementation with categories, topics, posts, and real-time interactions
- **E-Learning Platform**: Complete integration with courses, lessons, enrollments, certificates, and progress tracking
- **Administration Interface**: Comprehensive dashboard-management.tsx with system-wide control capabilities
- **Authentication Flow**: Robust session management with role-based access control throughout the platform
- **Real Data Integration**: Complete replacement of mock data with authentic API connections in all components
- **Error Resolution**: Fixed all type mismatches and interface incompatibilities between frontend and backend

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
```