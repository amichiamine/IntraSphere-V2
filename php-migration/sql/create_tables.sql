-- Script de création des tables pour IntraSphere PHP
-- Compatible MySQL et PostgreSQL

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(50) PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    role ENUM('employee', 'moderator', 'admin') DEFAULT 'employee',
    avatar TEXT,
    employee_id VARCHAR(50) UNIQUE,
    department VARCHAR(255),
    position VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    phone VARCHAR(50),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des annonces
CREATE TABLE IF NOT EXISTS announcements (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('info', 'important', 'event', 'formation') DEFAULT 'info',
    author_id VARCHAR(50),
    author_name VARCHAR(255) NOT NULL,
    image_url TEXT,
    icon VARCHAR(10) DEFAULT '📢',
    is_important BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des documents
CREATE TABLE IF NOT EXISTS documents (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('regulation', 'policy', 'guide', 'procedure') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_url TEXT NOT NULL,
    version VARCHAR(20) DEFAULT '1.0',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des événements
CREATE TABLE IF NOT EXISTS events (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    date DATETIME NOT NULL,
    location VARCHAR(255),
    type ENUM('meeting', 'training', 'social', 'other') DEFAULT 'meeting',
    organizer_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des messages
CREATE TABLE IF NOT EXISTS messages (
    id VARCHAR(50) PRIMARY KEY,
    sender_id VARCHAR(50),
    recipient_id VARCHAR(50),
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des réclamations
CREATE TABLE IF NOT EXISTS complaints (
    id VARCHAR(50) PRIMARY KEY,
    submitter_id VARCHAR(50),
    assigned_to_id VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des permissions
CREATE TABLE IF NOT EXISTS permissions (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    permission VARCHAR(100) NOT NULL,
    granted_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_permission (user_id, permission)
);

-- Table du contenu multimédia
CREATE TABLE IF NOT EXISTS contents (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type ENUM('video', 'image', 'document', 'audio') NOT NULL,
    category VARCHAR(100),
    description TEXT,
    file_url TEXT NOT NULL,
    thumbnail_url TEXT,
    duration INT, -- en secondes
    view_count INT DEFAULT 0,
    rating DECIMAL(2,1) DEFAULT 0.0,
    tags JSON,
    is_popular BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des catégories de contenu
CREATE TABLE IF NOT EXISTS categories (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7), -- Code couleur hex
    is_visible BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des catégories d'employés
CREATE TABLE IF NOT EXISTS employee_categories (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7),
    permissions JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des paramètres système
CREATE TABLE IF NOT EXISTS system_settings (
    id VARCHAR(50) PRIMARY KEY DEFAULT 'main',
    show_announcements BOOLEAN DEFAULT TRUE,
    show_content BOOLEAN DEFAULT TRUE,
    show_documents BOOLEAN DEFAULT TRUE,
    show_forum BOOLEAN DEFAULT TRUE,
    show_messages BOOLEAN DEFAULT TRUE,
    show_complaints BOOLEAN DEFAULT TRUE,
    show_training BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des formations
CREATE TABLE IF NOT EXISTS trainings (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    duration INT, -- en heures
    instructor_id VARCHAR(50),
    instructor_name VARCHAR(255) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    location VARCHAR(255),
    max_participants INT DEFAULT 20,
    current_participants INT DEFAULT 0,
    is_mandatory BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_visible BOOLEAN DEFAULT TRUE,
    thumbnail_url TEXT,
    document_urls JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des participants aux formations
CREATE TABLE IF NOT EXISTS training_participants (
    id VARCHAR(50) PRIMARY KEY,
    training_id VARCHAR(50) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('registered', 'in_progress', 'completed', 'cancelled') DEFAULT 'registered',
    completion_date DATETIME,
    score INT, -- Note sur 100
    feedback TEXT,
    FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_training_user (training_id, user_id)
);

-- Tables pour le système e-learning étendu

-- Table des cours
CREATE TABLE IF NOT EXISTS courses (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructor_id VARCHAR(50),
    category VARCHAR(100),
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    duration INT, -- durée totale en minutes
    price DECIMAL(10,2) DEFAULT 0.00,
    is_published BOOLEAN DEFAULT FALSE,
    thumbnail_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des leçons
CREATE TABLE IF NOT EXISTS lessons (
    id VARCHAR(50) PRIMARY KEY,
    course_id VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    video_url TEXT,
    duration INT, -- en minutes
    sort_order INT DEFAULT 0,
    is_published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Table des quiz
CREATE TABLE IF NOT EXISTS quizzes (
    id VARCHAR(50) PRIMARY KEY,
    lesson_id VARCHAR(50),
    course_id VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    questions JSON NOT NULL, -- Stockage des questions en JSON
    passing_score INT DEFAULT 70,
    time_limit INT, -- en minutes
    max_attempts INT DEFAULT 3,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Table des inscriptions aux cours
CREATE TABLE IF NOT EXISTS enrollments (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    course_id VARCHAR(50) NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress DECIMAL(5,2) DEFAULT 0.00, -- Pourcentage de completion
    completed_at DATETIME,
    certificate_issued BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_course (user_id, course_id)
);

-- Table du progrès par leçon
CREATE TABLE IF NOT EXISTS lesson_progress (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    lesson_id VARCHAR(50) NOT NULL,
    enrollment_id VARCHAR(50) NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME,
    watch_time INT DEFAULT 0, -- temps regardé en secondes
    is_completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_lesson (user_id, lesson_id)
);

-- Table des tentatives de quiz
CREATE TABLE IF NOT EXISTS quiz_attempts (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    quiz_id VARCHAR(50) NOT NULL,
    enrollment_id VARCHAR(50) NOT NULL,
    attempt_number INT NOT NULL,
    answers JSON, -- Réponses en JSON
    score DECIMAL(5,2),
    passed BOOLEAN DEFAULT FALSE,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME,
    time_taken INT, -- temps pris en secondes
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
);

-- Table des certificats
CREATE TABLE IF NOT EXISTS certificates (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    course_id VARCHAR(50) NOT NULL,
    enrollment_id VARCHAR(50) NOT NULL,
    certificate_number VARCHAR(100) UNIQUE NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valid_until DATE,
    certificate_data JSON, -- Données du certificat
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
);

-- Table des ressources pédagogiques
CREATE TABLE IF NOT EXISTS resources (
    id VARCHAR(50) PRIMARY KEY,
    lesson_id VARCHAR(50),
    course_id VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('pdf', 'video', 'audio', 'link', 'exercise') NOT NULL,
    file_url TEXT,
    file_size INT, -- en bytes
    is_downloadable BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Tables pour le système de forum

-- Table des catégories de forum
CREATE TABLE IF NOT EXISTS forum_categories (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    color VARCHAR(7),
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des sujets de forum
CREATE TABLE IF NOT EXISTS forum_topics (
    id VARCHAR(50) PRIMARY KEY,
    category_id VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id VARCHAR(50),
    is_pinned BOOLEAN DEFAULT FALSE,
    is_locked BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    reply_count INT DEFAULT 0,
    last_post_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_post_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES forum_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (last_post_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des posts de forum
CREATE TABLE IF NOT EXISTS forum_posts (
    id VARCHAR(50) PRIMARY KEY,
    topic_id VARCHAR(50) NOT NULL,
    author_id VARCHAR(50),
    content TEXT NOT NULL,
    like_count INT DEFAULT 0,
    is_edited BOOLEAN DEFAULT FALSE,
    edited_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des likes sur les posts
CREATE TABLE IF NOT EXISTS forum_likes (
    id VARCHAR(50) PRIMARY KEY,
    post_id VARCHAR(50) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_post_user (post_id, user_id)
);

-- Table des statistiques utilisateur forum
CREATE TABLE IF NOT EXISTS forum_user_stats (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL UNIQUE,
    post_count INT DEFAULT 0,
    topic_count INT DEFAULT 0,
    like_received INT DEFAULT 0,
    like_given INT DEFAULT 0,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Index pour optimiser les performances
CREATE INDEX idx_announcements_type ON announcements(type);
CREATE INDEX idx_announcements_important ON announcements(is_important);
CREATE INDEX idx_announcements_created ON announcements(created_at);
CREATE INDEX idx_documents_category ON documents(category);
CREATE INDEX idx_events_date ON events(date);
CREATE INDEX idx_events_organizer ON events(organizer_id);
CREATE INDEX idx_messages_recipient ON messages(recipient_id);
CREATE INDEX idx_messages_sender ON messages(sender_id);
CREATE INDEX idx_messages_read ON messages(is_read);
CREATE INDEX idx_complaints_status ON complaints(status);
CREATE INDEX idx_complaints_submitter ON complaints(submitter_id);
CREATE INDEX idx_contents_type ON contents(type);
CREATE INDEX idx_contents_category ON contents(category);
CREATE INDEX idx_trainings_active ON trainings(is_active);
CREATE INDEX idx_trainings_start_date ON trainings(start_date);
CREATE INDEX idx_forum_topics_category ON forum_topics(category_id);
CREATE INDEX idx_forum_posts_topic ON forum_posts(topic_id);

-- Insertion des données initiales

-- Paramètres système par défaut
INSERT INTO system_settings (id) VALUES ('main') ON DUPLICATE KEY UPDATE id=id;

-- Catégories de forum par défaut
INSERT INTO forum_categories (id, name, description, color, sort_order) VALUES 
('fc-1', 'Général', 'Discussions générales', '#3B82F6', 1),
('fc-2', 'Annonces', 'Annonces officielles', '#F59E0B', 2),
('fc-3', 'Support', 'Aide et support technique', '#10B981', 3),
('fc-4', 'Suggestions', 'Suggestions d\'amélioration', '#8B5CF6', 4)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO users (id, username, password, name, role, is_active) VALUES 
('admin-1', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 'admin', TRUE)
ON DUPLICATE KEY UPDATE username=VALUES(username);

-- Permissions par défaut pour l'admin
INSERT INTO permissions (id, user_id, permission, granted_by) VALUES 
('perm-1', 'admin-1', 'manage_announcements', 'admin-1'),
('perm-2', 'admin-1', 'manage_documents', 'admin-1'),
('perm-3', 'admin-1', 'manage_events', 'admin-1'),
('perm-4', 'admin-1', 'manage_users', 'admin-1'),
('perm-5', 'admin-1', 'manage_trainings', 'admin-1')
ON DUPLICATE KEY UPDATE permission=VALUES(permission);
?>