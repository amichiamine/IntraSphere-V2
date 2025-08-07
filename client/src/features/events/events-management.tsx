import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { apiRequest } from "@/core/lib/queryClient";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Button } from "@/core/components/ui/button";
import { Badge } from "@/core/components/ui/badge";
import { Input } from "@/core/components/ui/input";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/core/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/core/components/ui/select";
import { Avatar, AvatarFallback, AvatarImage } from "@/core/components/ui/avatar";
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from "@/core/components/ui/dialog";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/core/components/ui/form";
import { Textarea } from "@/core/components/ui/textarea";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { insertEventSchema } from "@shared/schema";
import { useToast } from "@/core/hooks/use-toast";
import { z } from "zod";
import { 
  Calendar,
  Clock,
  MapPin,
  Users,
  Plus,
  Filter,
  Search,
  Eye,
  Edit,
  Trash2,
  CheckCircle,
  XCircle,
  AlertCircle,
  Share2,
  Bell,
  Download
} from "lucide-react";
import type { Event } from "@shared/schema";

const createEventSchema = z.object({
  title: z.string().min(1, "Le titre est requis"),
  description: z.string().optional(),
  location: z.string().optional(),
  startDate: z.date(),
  endDate: z.date(),
  maxAttendees: z.number().min(1).default(50),
  isPublic: z.boolean().default(true),
  requiresApproval: z.boolean().default(false),
  reminderTime: z.number().optional(),
  isRecurring: z.boolean().optional(),
  recurringPattern: z.string().optional()
});

export function EventsManagementPage() {
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedStatus, setSelectedStatus] = useState("all");
  const [viewMode, setViewMode] = useState("calendar");
  const [selectedDate, setSelectedDate] = useState<Date>(new Date());
  const [isCreateDialogOpen, setIsCreateDialogOpen] = useState(false);
  const queryClient = useQueryClient();
  const { toast } = useToast();

  // Form setup
  const form = useForm<z.infer<typeof createEventSchema>>({
    resolver: zodResolver(createEventSchema),
    defaultValues: {
      title: "",
      description: "",
      location: "",
      startDate: new Date(),
      endDate: new Date(),
      maxAttendees: 50,
      isPublic: true,
      requiresApproval: false,
      reminderTime: 60,
      isRecurring: false
    }
  });

  // Fetch events
  const { data: events = [], isLoading } = useQuery<Event[]>({
    queryKey: ['/api/events'],
  });

  // Fetch user's event RSVPs
  const { data: userRsvps = [] } = useQuery<any[]>({
    queryKey: ['/api/events/my-rsvps'],
  });

  // Create event mutation
  const createMutation = useMutation({
    mutationFn: async (data: z.infer<typeof createEventSchema>) => {
      return apiRequest('/api/events', "POST", data);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/events'] });
      setIsCreateDialogOpen(false);
      form.reset();
      toast({
        title: "Événement créé",
        description: "L'événement a été créé avec succès"
      });
    }
  });

  // RSVP mutation
  const rsvpMutation = useMutation({
    mutationFn: async ({ eventId, status }: { eventId: string, status: string }) => {
      return apiRequest(`/api/events/${eventId}/rsvp`, "POST", { status });
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/events'] });
      queryClient.invalidateQueries({ queryKey: ['/api/events/my-rsvps'] });
      toast({
        title: "RSVP mis à jour",
        description: "Votre réponse a été enregistrée"
      });
    }
  });

  // Delete event mutation
  const deleteMutation = useMutation({
    mutationFn: async (eventId: string) => {
      return apiRequest(`/api/events/${eventId}`, "DELETE");
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/events'] });
      toast({
        title: "Événement supprimé",
        description: "L'événement a été supprimé avec succès"
      });
    }
  });

  const filteredEvents = events.filter(event => {
    const matchesSearch = event.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         event.description?.toLowerCase().includes(searchQuery.toLowerCase());
    const eventStatus = "active"; // Default status since event doesn't have status field
    const matchesStatus = selectedStatus === "all" || eventStatus === selectedStatus;
    return matchesSearch && matchesStatus;
  });

  const getStatusColor = (status: string) => {
    switch (status) {
      case "active": return "bg-green-100 text-green-800";
      case "cancelled": return "bg-red-100 text-red-800";
      case "completed": return "bg-blue-100 text-blue-800";
      default: return "bg-gray-100 text-gray-800";
    }
  };

  const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
  };

  const formatDate = (date: Date | string) => {
    return new Intl.DateTimeFormat('fr-FR', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(new Date(date));
  };

  const formatNumber = (num: number) => {
    return num.toLocaleString('fr-FR');
  };

  const onSubmit = (data: z.infer<typeof createEventSchema>) => {
    createMutation.mutate(data);
  };

  if (isLoading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-8 w-64 bg-gray-200 dark:bg-gray-700 rounded"></div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {[...Array(6)].map((_, i) => (
              <div key={i} className="h-64 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            ))}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="p-6 space-y-6" data-testid="events-management-page">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
            Gestion des Événements
          </h1>
          <p className="text-gray-600 dark:text-gray-400 mt-1">
            Organisez et participez aux événements de l'entreprise
          </p>
        </div>
        
        <div className="flex items-center gap-3">
          <Badge variant="outline" className="text-sm">
            {formatNumber(filteredEvents.length)} événements
          </Badge>
          
          <Dialog open={isCreateDialogOpen} onOpenChange={setIsCreateDialogOpen}>
            <DialogTrigger asChild>
              <Button className="bg-purple-600 hover:bg-purple-700">
                <Plus className="h-4 w-4 mr-2" />
                Nouvel événement
              </Button>
            </DialogTrigger>
            <DialogContent className="max-w-2xl">
              <DialogHeader>
                <DialogTitle>Créer un nouvel événement</DialogTitle>
                <DialogDescription>
                  Organisez un événement pour votre équipe ou toute l'entreprise
                </DialogDescription>
              </DialogHeader>
              
              <Form {...form}>
                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="title"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Titre de l'événement</FormLabel>
                          <FormControl>
                            <Input placeholder="Réunion d'équipe..." {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={form.control}
                      name="location"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Lieu</FormLabel>
                          <FormControl>
                            <Input placeholder="Salle de conférence A..." {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                  
                  <FormField
                    control={form.control}
                    name="description"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Description</FormLabel>
                        <FormControl>
                          <Textarea 
                            placeholder="Décrivez l'événement..."
                            className="min-h-[100px]"
                            {...field}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormField
                      control={form.control}
                      name="startDate"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Date de début</FormLabel>
                          <FormControl>
                            <Input 
                              type="datetime-local"
                              value={field.value ? new Date(field.value).toISOString().slice(0, 16) : ''}
                              onChange={(e) => field.onChange(new Date(e.target.value))}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={form.control}
                      name="endDate"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Date de fin</FormLabel>
                          <FormControl>
                            <Input 
                              type="datetime-local"
                              value={field.value ? new Date(field.value).toISOString().slice(0, 16) : ''}
                              onChange={(e) => field.onChange(new Date(e.target.value))}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={form.control}
                      name="maxAttendees"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Participants max</FormLabel>
                          <FormControl>
                            <Input 
                              type="number"
                              min="1"
                              max="1000"
                              value={field.value}
                              onChange={(e) => field.onChange(parseInt(e.target.value))}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                  
                  <div className="flex justify-end gap-3">
                    <Button 
                      type="button" 
                      variant="outline" 
                      onClick={() => setIsCreateDialogOpen(false)}
                    >
                      Annuler
                    </Button>
                    <Button 
                      type="submit" 
                      disabled={createMutation.isPending}
                      className="bg-purple-600 hover:bg-purple-700"
                    >
                      {createMutation.isPending ? "Création..." : "Créer l'événement"}
                    </Button>
                  </div>
                </form>
              </Form>
            </DialogContent>
          </Dialog>
        </div>
      </div>

      {/* Filters */}
      <div className="flex flex-col md:flex-row gap-4">
        <div className="flex-1">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
            <Input
              placeholder="Rechercher des événements..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10"
              data-testid="input-search-events"
            />
          </div>
        </div>
        
        <div className="flex gap-2">
          <Select value={selectedStatus} onValueChange={setSelectedStatus}>
            <SelectTrigger className="w-40">
              <SelectValue placeholder="Statut" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Tous les statuts</SelectItem>
              <SelectItem value="active">Actif</SelectItem>
              <SelectItem value="completed">Terminé</SelectItem>
              <SelectItem value="cancelled">Annulé</SelectItem>
            </SelectContent>
          </Select>
          
          <Select value={viewMode} onValueChange={setViewMode}>
            <SelectTrigger className="w-32">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="calendar">Calendrier</SelectItem>
              <SelectItem value="list">Liste</SelectItem>
              <SelectItem value="grid">Grille</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <Tabs defaultValue="all" className="space-y-4">
        <TabsList>
          <TabsTrigger value="all">Tous les événements</TabsTrigger>
          <TabsTrigger value="my-events">Mes événements</TabsTrigger>
          <TabsTrigger value="attending">Je participe</TabsTrigger>
          <TabsTrigger value="calendar">Calendrier</TabsTrigger>
        </TabsList>

        <TabsContent value="all" className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredEvents.map((event) => {
              const userRsvp = userRsvps.find(rsvp => rsvp.eventId === event.id);
              
              return (
                <Card key={event.id} className="group hover:shadow-lg transition-all duration-300">
                  <CardHeader className="pb-3">
                    <div className="flex items-start justify-between">
                      <div className="flex-1 min-w-0">
                        <CardTitle className="text-lg font-semibold text-gray-900 dark:text-white truncate">
                          {event.title}
                        </CardTitle>
                        <div className="flex items-center gap-2 mt-1">
                          <Badge className="bg-green-100 text-green-800">
                            Actif
                          </Badge>
                          <Badge variant="outline" className="text-xs">
                            Public
                          </Badge>
                        </div>
                      </div>
                      
                      <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <Button size="sm" variant="ghost" className="h-8 w-8 p-0">
                          <Share2 className="h-4 w-4" />
                        </Button>
                        <Button size="sm" variant="ghost" className="h-8 w-8 p-0">
                          <Bell className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  </CardHeader>
                  
                  <CardContent className="space-y-4">
                    {event.description && (
                      <p className="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                        {event.description}
                      </p>
                    )}
                    
                    {/* Event details */}
                    <div className="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                      <div className="flex items-center gap-2">
                        <Calendar className="h-4 w-4" />
                        <span>{formatDate(event.date)}</span>
                      </div>
                      {event.location && (
                        <div className="flex items-center gap-2">
                          <MapPin className="h-4 w-4" />
                          <span>{event.location}</span>
                        </div>
                      )}
                      <div className="flex items-center gap-2">
                        <Users className="h-4 w-4" />
                        <span>
                          0 / 50 participants
                        </span>
                      </div>
                    </div>
                    
                    {/* Organizer */}
                    <div className="flex items-center gap-2 text-sm">
                      <Avatar className="h-6 w-6">
                        <AvatarFallback className="text-xs">
                          AD
                        </AvatarFallback>
                      </Avatar>
                      <span className="text-gray-600 dark:text-gray-400">
                        Organisé par Admin
                      </span>
                    </div>
                    
                    {/* RSVP buttons */}
                    <div className="flex items-center gap-2">
                      <Button
                        size="sm"
                        variant={userRsvp?.status === "attending" ? "default" : "outline"}
                        onClick={() => rsvpMutation.mutate({ 
                          eventId: event.id, 
                          status: userRsvp?.status === "attending" ? "not_attending" : "attending" 
                        })}
                        disabled={rsvpMutation.isPending}
                        className="flex-1"
                      >
                        <CheckCircle className="h-4 w-4 mr-1" />
                        {userRsvp?.status === "attending" ? "Participe" : "Participer"}
                      </Button>
                      
                      <Button
                        size="sm"
                        variant={userRsvp?.status === "maybe" ? "default" : "outline"}
                        onClick={() => rsvpMutation.mutate({ 
                          eventId: event.id, 
                          status: "maybe" 
                        })}
                        disabled={rsvpMutation.isPending}
                      >
                        <AlertCircle className="h-4 w-4" />
                      </Button>
                      
                      <Button
                        size="sm"
                        variant={userRsvp?.status === "not_attending" ? "default" : "outline"}
                        onClick={() => rsvpMutation.mutate({ 
                          eventId: event.id, 
                          status: "not_attending" 
                        })}
                        disabled={rsvpMutation.isPending}
                      >
                        <XCircle className="h-4 w-4" />
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              );
            })}
          </div>
          
          {filteredEvents.length === 0 && (
            <div className="text-center py-12 text-gray-500 dark:text-gray-400">
              <Calendar className="h-12 w-12 mx-auto mb-4 opacity-50" />
              <p className="text-lg font-medium mb-2">Aucun événement trouvé</p>
              <p className="text-sm">
                {searchQuery || selectedStatus !== "all"
                  ? "Essayez de modifier vos filtres de recherche"
                  : "Aucun événement planifié pour le moment"
                }
              </p>
            </div>
          )}
        </TabsContent>

        <TabsContent value="my-events" className="space-y-4">
          <Card>
            <CardContent className="p-12 text-center">
              <Calendar className="h-12 w-12 mx-auto mb-4 opacity-50 text-gray-400" />
              <p className="text-lg font-medium mb-2 text-gray-500">Mes événements</p>
              <p className="text-sm text-gray-400">
                Les événements que vous organisez apparaîtront ici
              </p>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="attending" className="space-y-4">
          <Card>
            <CardContent className="p-12 text-center">
              <Users className="h-12 w-12 mx-auto mb-4 opacity-50 text-gray-400" />
              <p className="text-lg font-medium mb-2 text-gray-500">Événements auxquels je participe</p>
              <p className="text-sm text-gray-400">
                Vos participations confirmées seront affichées ici
              </p>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="calendar" className="space-y-4">
          <Card>
            <CardContent className="p-12 text-center">
              <Calendar className="h-12 w-12 mx-auto mb-4 opacity-50 text-gray-400" />
              <p className="text-lg font-medium mb-2 text-gray-500">Vue calendrier</p>
              <p className="text-sm text-gray-400">
                Calendrier interactif des événements (fonctionnalité avancée)
              </p>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}