<?php
/**
 * Modèle Formation
 * Équivalent à la table 'trainings' TypeScript
 */

class Training extends BaseModel {
    protected string $table = 'trainings';
    
    /**
     * Créer une formation
     */
    public function create(array $data): array {
        $data['difficulty'] = $data['difficulty'] ?? 'beginner';
        $data['current_participants'] = $data['current_participants'] ?? 0;
        $data['is_mandatory'] = $data['is_mandatory'] ?? false;
        $data['is_active'] = $data['is_active'] ?? true;
        $data['is_visible'] = $data['is_visible'] ?? true;
        
        return parent::create($this->sanitize($data));
    }
    
    /**
     * Obtenir les formations avec informations instructeur
     */
    public function findAllWithInstructor(): array {
        $sql = "SELECT t.*, u.name as instructor_full_name, u.avatar as instructor_avatar
                FROM {$this->table} t
                LEFT JOIN users u ON t.instructor_id = u.id
                WHERE t.is_visible = true
                ORDER BY t.start_date ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les formations actives
     */
    public function getActive(): array {
        $sql = "SELECT t.*, u.name as instructor_full_name, u.avatar as instructor_avatar
                FROM {$this->table} t
                LEFT JOIN users u ON t.instructor_id = u.id
                WHERE t.is_active = true AND t.is_visible = true
                ORDER BY t.start_date ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les formations par catégorie
     */
    public function getByCategory(string $category): array {
        $sql = "SELECT t.*, u.name as instructor_full_name, u.avatar as instructor_avatar
                FROM {$this->table} t
                LEFT JOIN users u ON t.instructor_id = u.id
                WHERE t.category = ? AND t.is_visible = true
                ORDER BY t.start_date ASC";
        
        return $this->db->fetchAll($sql, [$category]);
    }
    
    /**
     * Obtenir les formations obligatoires
     */
    public function getMandatory(): array {
        $sql = "SELECT t.*, u.name as instructor_full_name, u.avatar as instructor_avatar
                FROM {$this->table} t
                LEFT JOIN users u ON t.instructor_id = u.id
                WHERE t.is_mandatory = true AND t.is_visible = true
                ORDER BY t.start_date ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les formations à venir
     */
    public function getUpcoming(): array {
        $sql = "SELECT t.*, u.name as instructor_full_name, u.avatar as instructor_avatar
                FROM {$this->table} t
                LEFT JOIN users u ON t.instructor_id = u.id
                WHERE t.start_date >= NOW() AND t.is_visible = true
                ORDER BY t.start_date ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Rechercher des formations
     */
    public function search(string $query): array {
        $searchTerm = "%{$query}%";
        $sql = "SELECT t.*, u.name as instructor_full_name, u.avatar as instructor_avatar
                FROM {$this->table} t
                LEFT JOIN users u ON t.instructor_id = u.id
                WHERE t.is_visible = true AND (
                    t.title LIKE ? OR t.description LIKE ? OR 
                    t.category LIKE ? OR u.name LIKE ?
                )
                ORDER BY t.start_date ASC";
        
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Vérifier si un utilisateur peut s'inscrire
     */
    public function canRegister(string $trainingId, string $userId): array {
        $training = $this->find($trainingId);
        if (!$training) {
            return ['can_register' => false, 'reason' => 'Formation introuvable'];
        }
        
        if (!$training['is_active']) {
            return ['can_register' => false, 'reason' => 'Formation inactive'];
        }
        
        if ($training['current_participants'] >= $training['max_participants']) {
            return ['can_register' => false, 'reason' => 'Formation complète'];
        }
        
        if (strtotime($training['start_date']) <= time()) {
            return ['can_register' => false, 'reason' => 'Formation déjà commencée'];
        }
        
        // Vérifier si déjà inscrit
        $participantModel = new TrainingParticipant();
        $existing = $participantModel->where([
            'training_id' => $trainingId,
            'user_id' => $userId
        ]);
        
        if (!empty($existing)) {
            return ['can_register' => false, 'reason' => 'Déjà inscrit'];
        }
        
        return ['can_register' => true, 'reason' => ''];
    }
    
    /**
     * Inscrire un utilisateur à une formation
     */
    public function registerUser(string $trainingId, string $userId): bool {
        $canRegister = $this->canRegister($trainingId, $userId);
        if (!$canRegister['can_register']) {
            return false;
        }
        
        $participantModel = new TrainingParticipant();
        $participant = $participantModel->create([
            'training_id' => $trainingId,
            'user_id' => $userId,
            'status' => 'registered'
        ]);
        
        if ($participant) {
            // Incrémenter le nombre de participants
            $training = $this->find($trainingId);
            $this->update($trainingId, [
                'current_participants' => $training['current_participants'] + 1
            ]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Désinscrire un utilisateur d'une formation
     */
    public function unregisterUser(string $trainingId, string $userId): bool {
        $participantModel = new TrainingParticipant();
        $deleted = $participantModel->removeParticipant($trainingId, $userId);
        
        if ($deleted) {
            // Décrémenter le nombre de participants
            $training = $this->find($trainingId);
            $this->update($trainingId, [
                'current_participants' => max(0, $training['current_participants'] - 1)
            ]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtenir les participants d'une formation
     */
    public function getParticipants(string $trainingId): array {
        $participantModel = new TrainingParticipant();
        return $participantModel->getByTraining($trainingId);
    }
    
    /**
     * Obtenir les statistiques des formations
     */
    public function getStats(): array {
        $total = $this->count();
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = true";
        $active = $this->db->fetchOne($sql)['count'];
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_mandatory = true";
        $mandatory = $this->db->fetchOne($sql)['count'];
        
        $sql = "SELECT category, COUNT(*) as count FROM {$this->table} 
                WHERE is_visible = true GROUP BY category";
        $byCategory = $this->db->fetchAll($sql);
        
        $sql = "SELECT SUM(current_participants) as total FROM {$this->table}";
        $totalParticipants = $this->db->fetchOne($sql)['total'] ?? 0;
        
        return [
            'total' => $total,
            'active' => $active,
            'mandatory' => $mandatory,
            'total_participants' => $totalParticipants,
            'by_category' => $byCategory
        ];
    }
    
    /**
     * Validation des données
     */
    protected function validate(array $data): array {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = "Le titre est requis";
        }
        
        if (empty($data['description'])) {
            $errors[] = "La description est requise";
        }
        
        if (empty($data['instructor_id'])) {
            $errors[] = "L'instructeur est requis";
        }
        
        if (empty($data['start_date'])) {
            $errors[] = "La date de début est requise";
        } elseif (!strtotime($data['start_date'])) {
            $errors[] = "Format de date de début invalide";
        }
        
        if (empty($data['end_date'])) {
            $errors[] = "La date de fin est requise";
        } elseif (!strtotime($data['end_date'])) {
            $errors[] = "Format de date de fin invalide";
        }
        
        if (isset($data['start_date'], $data['end_date']) && 
            strtotime($data['start_date']) >= strtotime($data['end_date'])) {
            $errors[] = "La date de fin doit être après la date de début";
        }
        
        if (isset($data['max_participants']) && 
            (!is_numeric($data['max_participants']) || $data['max_participants'] <= 0)) {
            $errors[] = "Le nombre maximum de participants doit être un nombre positif";
        }
        
        return $errors;
    }
}

/**
 * Modèle TrainingParticipant
 * Équivalent à la table 'training_participants' TypeScript
 */
class TrainingParticipant extends BaseModel {
    protected string $table = 'training_participants';
    
    /**
     * Obtenir les participants d'une formation avec infos utilisateur
     */
    public function getByTraining(string $trainingId): array {
        $sql = "SELECT tp.*, u.name, u.username, u.email, u.avatar
                FROM {$this->table} tp
                LEFT JOIN users u ON tp.user_id = u.id
                WHERE tp.training_id = ?
                ORDER BY tp.registered_at ASC";
        
        return $this->db->fetchAll($sql, [$trainingId]);
    }
    
    /**
     * Obtenir les formations d'un utilisateur
     */
    public function getByUser(string $userId): array {
        $sql = "SELECT tp.*, t.title, t.description, t.start_date, t.end_date,
                       u.name as instructor_name
                FROM {$this->table} tp
                LEFT JOIN trainings t ON tp.training_id = t.id
                LEFT JOIN users u ON t.instructor_id = u.id
                WHERE tp.user_id = ?
                ORDER BY t.start_date ASC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    /**
     * Supprimer un participant
     */
    public function removeParticipant(string $trainingId, string $userId): bool {
        $sql = "DELETE FROM {$this->table} WHERE training_id = ? AND user_id = ?";
        return $this->db->execute($sql, [$trainingId, $userId]);
    }
    
    /**
     * Marquer une formation comme complétée
     */
    public function markCompleted(string $trainingId, string $userId, int $score = null): bool {
        $data = [
            'status' => 'completed',
            'completion_date' => date('Y-m-d H:i:s')
        ];
        
        if ($score !== null) {
            $data['score'] = $score;
        }
        
        $sql = "UPDATE {$this->table} 
                SET status = ?, completion_date = ?" . ($score !== null ? ", score = ?" : "") . "
                WHERE training_id = ? AND user_id = ?";
        
        $params = [$data['status'], $data['completion_date']];
        if ($score !== null) {
            $params[] = $score;
        }
        $params[] = $trainingId;
        $params[] = $userId;
        
        return $this->db->execute($sql, $params);
    }
}
?>