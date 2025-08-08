<?php
namespace Api;

/**
 * Contrôleur API des événements
 * Équivalent aux routes /api/events/* TypeScript
 */

class EventsController extends \BaseController {
    private \Event $eventModel;
    private \User $userModel;
    
    public function __construct() {
        $this->eventModel = new \Event();
        $this->userModel = new \User();
    }
    
    /**
     * GET /api/events
     */
    public function index(): void {
        $this->requireAuth();
        
        $type = $this->getQueryParam('type');
        $upcoming = $this->getQueryParam('upcoming');
        $search = $this->getQueryParam('search');
        $page = $this->getQueryParam('page', 1);
        $limit = $this->getQueryParam('limit', 20);
        
        try {
            if ($search) {
                $events = $this->eventModel->search($search);
            } elseif ($upcoming === 'true') {
                $events = $this->eventModel->getUpcoming($limit);
            } elseif ($type) {
                $events = $this->eventModel->getByType($type);
            } else {
                $events = $this->eventModel->getPaginated($page, $limit);
            }
            
            $this->json($events);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la récupération des événements');
        }
    }
    
    /**
     * GET /api/events/:id
     */
    public function show(string $id): void {
        $this->requireAuth();
        
        $event = $this->eventModel->findWithOrganizer($id);
        if (!$event) {
            $this->error('Événement introuvable', 404);
        }
        
        $this->json($event);
    }
    
    /**
     * POST /api/events
     */
    public function create(): void {
        $user = $this->requirePermission('manage_events');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['title', 'date']);
        
        // Validation du type d'événement
        $allowedTypes = ['meeting', 'training', 'social', 'other'];
        $type = $data['type'] ?? 'meeting';
        if (!in_array($type, $allowedTypes)) {
            $this->error('Type d\'événement invalide');
        }
        
        // Validation de la date (doit être future)
        $eventDate = strtotime($data['date']);
        if ($eventDate === false || $eventDate < time()) {
            $this->error('La date de l\'événement doit être future');
        }
        
        $eventData = $this->sanitizeInput([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'date' => date('Y-m-d H:i:s', $eventDate),
            'location' => $data['location'] ?? null,
            'type' => $type,
            'organizer_id' => $user['id']
        ]);
        
        try {
            $event = $this->eventModel->create($eventData);
            
            // Enrichir avec les informations organisateur
            $eventWithOrganizer = $this->eventModel->findWithOrganizer($event['id']);
            
            $this->logActivity('event_created', [
                'event_id' => $event['id'],
                'title' => $event['title'],
                'date' => $event['date']
            ]);
            
            $this->json($eventWithOrganizer, 201);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la création de l\'événement');
        }
    }
    
    /**
     * PUT /api/events/:id
     */
    public function update(string $id): void {
        $user = $this->requirePermission('manage_events');
        
        $event = $this->eventModel->find($id);
        if (!$event) {
            $this->error('Événement introuvable', 404);
        }
        
        // Vérifier que l'utilisateur peut modifier cet événement
        if ($user['role'] !== 'admin' && $event['organizer_id'] !== $user['id']) {
            $this->error('Vous ne pouvez modifier que vos propres événements', 403);
        }
        
        $data = $this->getJsonInput();
        
        // Validation du type si fourni
        if (isset($data['type'])) {
            $allowedTypes = ['meeting', 'training', 'social', 'other'];
            if (!in_array($data['type'], $allowedTypes)) {
                $this->error('Type d\'événement invalide');
            }
        }
        
        // Validation de la date si fournie
        if (isset($data['date'])) {
            $eventDate = strtotime($data['date']);
            if ($eventDate === false || $eventDate < time()) {
                $this->error('La date de l\'événement doit être future');
            }
            $data['date'] = date('Y-m-d H:i:s', $eventDate);
        }
        
        $updateData = $this->sanitizeInput([
            'title' => $data['title'] ?? $event['title'],
            'description' => $data['description'] ?? $event['description'],
            'date' => $data['date'] ?? $event['date'],
            'location' => $data['location'] ?? $event['location'],
            'type' => $data['type'] ?? $event['type']
        ]);
        
        try {
            $updatedEvent = $this->eventModel->update($id, $updateData);
            
            // Enrichir avec les informations organisateur
            $eventWithOrganizer = $this->eventModel->findWithOrganizer($id);
            
            $this->logActivity('event_updated', [
                'event_id' => $id,
                'title' => $updatedEvent['title']
            ]);
            
            $this->json($eventWithOrganizer);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la mise à jour de l\'événement');
        }
    }
    
    /**
     * DELETE /api/events/:id
     */
    public function delete(string $id): void {
        $user = $this->requirePermission('manage_events');
        
        $event = $this->eventModel->find($id);
        if (!$event) {
            $this->error('Événement introuvable', 404);
        }
        
        // Vérifier que l'utilisateur peut supprimer cet événement
        if ($user['role'] !== 'admin' && $event['organizer_id'] !== $user['id']) {
            $this->error('Vous ne pouvez supprimer que vos propres événements', 403);
        }
        
        try {
            $this->eventModel->delete($id);
            
            $this->logActivity('event_deleted', [
                'event_id' => $id,
                'title' => $event['title']
            ]);
            
            $this->json(['message' => 'Événement supprimé avec succès']);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression de l\'événement');
        }
    }
    
    /**
     * GET /api/events/upcoming
     */
    public function upcoming(): void {
        $this->requireAuth();
        
        $limit = $this->getQueryParam('limit', 5);
        $days = $this->getQueryParam('days', 30);
        
        $upcomingEvents = $this->eventModel->getUpcoming($limit, $days);
        
        $this->json($upcomingEvents);
    }
    
    /**
     * GET /api/events/calendar
     */
    public function calendar(): void {
        $this->requireAuth();
        
        $year = $this->getQueryParam('year', date('Y'));
        $month = $this->getQueryParam('month', date('n'));
        
        // Valider l'année et le mois
        if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
            $this->error('Année ou mois invalide');
        }
        
        $calendarEvents = $this->eventModel->getCalendarEvents($year, $month);
        
        $this->json([
            'year' => (int)$year,
            'month' => (int)$month,
            'events' => $calendarEvents
        ]);
    }
    
    /**
     * GET /api/events/my-events
     */
    public function myEvents(): void {
        $user = $this->requireAuth();
        
        $type = $this->getQueryParam('type', 'all'); // organized, participating, all
        
        $myEvents = $this->eventModel->getMyEvents($user['id'], $type);
        
        $this->json($myEvents);
    }
    
    /**
     * GET /api/events/types
     */
    public function types(): void {
        $this->requireAuth();
        
        $types = [
            'meeting' => 'Réunion',
            'training' => 'Formation',
            'social' => 'Événement social',
            'other' => 'Autre'
        ];
        
        // Compter les événements par type
        $typesWithCount = [];
        foreach ($types as $key => $label) {
            $typesWithCount[] = [
                'key' => $key,
                'label' => $label,
                'count' => $this->eventModel->countByType($key)
            ];
        }
        
        $this->json($typesWithCount);
    }
    
    /**
     * GET /api/events/stats
     */
    public function stats(): void {
        $this->requireRole('moderator');
        
        $stats = [
            'total' => $this->eventModel->count(),
            'upcoming' => $this->eventModel->countUpcoming(),
            'past' => $this->eventModel->countPast(),
            'by_type' => $this->eventModel->countByType(),
            'this_month' => $this->eventModel->countThisMonth(),
            'most_active_organizers' => $this->eventModel->getMostActiveOrganizers(5)
        ];
        
        $this->json($stats);
    }
    
    /**
     * POST /api/events/bulk-delete
     */
    public function bulkDelete(): void {
        $user = $this->requirePermission('manage_events');
        
        $data = $this->getJsonInput();
        $this->validateRequired($data, ['event_ids']);
        
        $eventIds = $data['event_ids'];
        if (!is_array($eventIds) || empty($eventIds)) {
            $this->error('Liste d\'IDs d\'événements invalide');
        }
        
        try {
            // Vérifier les permissions pour chaque événement si pas admin
            if ($user['role'] !== 'admin') {
                foreach ($eventIds as $eventId) {
                    $event = $this->eventModel->find($eventId);
                    if ($event && $event['organizer_id'] !== $user['id']) {
                        $this->error('Vous ne pouvez supprimer que vos propres événements', 403);
                    }
                }
            }
            
            $deletedCount = $this->eventModel->bulkDelete($eventIds);
            
            $this->logActivity('events_bulk_deleted', [
                'count' => $deletedCount,
                'event_ids' => $eventIds
            ]);
            
            $this->json([
                'message' => "$deletedCount événement(s) supprimé(s) avec succès",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (Exception $e) {
            $this->error('Erreur lors de la suppression en masse des événements');
        }
    }
}