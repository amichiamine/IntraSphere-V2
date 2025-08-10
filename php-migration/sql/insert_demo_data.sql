-- Données de démonstration pour IntraSphere PHP
-- Compatible MySQL et PostgreSQL

-- Insérer des utilisateurs de démonstration
INSERT INTO users (id, username, password, name, role, employee_id, department, position, email, phone) VALUES 
('user-1', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur Système', 'admin', 'EMP-001', 'IT', 'Administrateur Système', 'admin@intrasphere.com', '+33 1 23 45 67 89'),
('user-2', 'marie.martin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie Martin', 'employee', 'EMP-002', 'Marketing', 'Chargée de Communication', 'marie.martin@intrasphere.com', '+33 1 23 45 67 90'),
('user-3', 'pierre.dubois', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pierre Dubois', 'moderator', 'EMP-003', 'RH', 'Responsable RH', 'pierre.dubois@intrasphere.com', '+33 1 23 45 67 91');

-- Insérer des annonces de démonstration
INSERT INTO announcements (id, title, content, type, author_id, author_name, icon, is_important) VALUES 
('ann-1', 'Nouvelle politique de télétravail', 'Nous sommes heureux d\'annoncer la mise en place de notre nouvelle politique de télétravail, offrant plus de flexibilité à tous les employés.', 'important', 'user-1', 'Administrateur Système', '🏠', true),
('ann-2', 'Formation cybersécurité obligatoire', 'Tous les employés doivent suivre la formation cybersécurité avant le 30 de ce mois. Inscrivez-vous via le portail formation.', 'formation', 'user-3', 'Pierre Dubois', '🔒', false);

-- Insérer des documents de démonstration
INSERT INTO documents (id, title, description, category, file_name, file_url, version) VALUES 
('doc-1', 'Règlement intérieur 2024', 'Version mise à jour du règlement intérieur incluant les nouvelles dispositions.', 'regulation', 'reglement_2024.pdf', '/documents/reglement_2024.pdf', '2.1'),
('doc-2', 'Guide d\'utilisation IT', 'Manuel d\'utilisation des outils informatiques de l\'entreprise.', 'guide', 'guide_it.pdf', '/documents/guide_it.pdf', '1.3');

-- Insérer des événements de démonstration
INSERT INTO events (id, title, description, date, location, type, organizer_id) VALUES 
('event-1', 'Réunion équipe marketing', 'Réunion mensuelle de l\'équipe marketing pour faire le point sur les projets en cours.', '2024-12-15 14:00:00', 'Salle de conférence A', 'meeting', 'user-2'),
('event-2', 'Formation Excel avancé', 'Session de formation pour maîtriser les fonctionnalités avancées d\'Excel.', '2024-12-20 09:00:00', 'Salle de formation', 'training', 'user-3');

-- Insérer des messages de démonstration
INSERT INTO messages (id, sender_id, recipient_id, subject, content, is_read) VALUES 
('msg-1', 'user-1', 'user-2', 'Bienvenue dans l\'équipe', 'Bonjour Marie, bienvenue dans notre équipe ! N\'hésitez pas si vous avez des questions.', false),
('msg-2', 'user-2', 'user-1', 'Merci pour l\'accueil', 'Merci beaucoup pour votre message de bienvenue. J\'ai hâte de commencer !', true),
('msg-3', 'user-3', 'user-2', 'Formation cybersécurité', 'N\'oubliez pas de vous inscrire à la formation cybersécurité obligatoire.', false);