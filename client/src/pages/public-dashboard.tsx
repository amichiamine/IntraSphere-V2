import { useQuery } from "@tanstack/react-query";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Button } from "@/core/components/ui/button";
import { Badge } from "@/core/components/ui/badge";
import { useLocation } from "wouter";
import { 
  Building2, 
  Users, 
  Globe, 
  ArrowRight, 
  Shield, 
  Clock,
  Bell,
  FileText,
  Calendar,
  MessageSquare
} from "lucide-react";

interface PublicStats {
  totalAnnouncements: number;
  totalDocuments: number;
  totalUsers: number;
  totalEvents: number;
}

export default function PublicDashboard() {
  const [, setLocation] = useLocation();
  
  const { data: stats } = useQuery<PublicStats>({
    queryKey: ["/api/stats"],
  });

  const { data: announcements = [] } = useQuery({
    queryKey: ["/api/announcements"],
  });

  const publicAnnouncements = (announcements as any[]).filter((a: any) => a.isPublic).slice(0, 3);

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 relative overflow-hidden">
      {/* Glass Morphism Background Pattern */}
      <div className="absolute inset-0 bg-gradient-to-br from-purple-50/50 via-blue-50/30 to-indigo-50/50"></div>
      <div className="absolute top-20 left-20 w-72 h-72 bg-purple-300/20 rounded-full blur-3xl"></div>
      <div className="absolute bottom-20 right-20 w-96 h-96 bg-indigo-300/20 rounded-full blur-3xl"></div>
      
      <div className="relative z-10 container mx-auto px-4 py-8 space-y-12">
        {/* Glass Morphism Hero Section */}
        <div className="text-center space-y-8">
          <div className="flex justify-center">
            <div className="relative">
              <div className="w-24 h-24 backdrop-blur-lg bg-gradient-to-r from-purple-500/80 to-indigo-600/80 rounded-3xl flex items-center justify-center shadow-2xl border border-white/20">
                <Building2 className="w-12 h-12 text-white drop-shadow-lg" />
              </div>
              <div className="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-indigo-600/20 rounded-3xl blur-xl"></div>
            </div>
          </div>
          
          <div className="space-y-6">
            <h1 className="text-6xl font-bold relative" style={{
              background: `linear-gradient(135deg, var(--color-primary, #8B5CF6), var(--color-secondary, #A78BFA))`,
              WebkitBackgroundClip: 'text',
              WebkitTextFillColor: 'transparent',
              backgroundClip: 'text',
              filter: 'drop-shadow(2px 2px 4px rgba(139, 92, 246, 0.1))'
            }}>
              IntraSphere
            </h1>
            <p className="text-xl text-gray-700 max-w-2xl mx-auto font-medium">
              Votre portail d'entreprise moderne avec interface Glass Morphism
            </p>
            <div className="inline-flex items-center gap-2 backdrop-blur-sm bg-white/40 rounded-full px-4 py-2 border border-white/30">
              <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              <span className="text-sm font-medium text-gray-700">Système en ligne</span>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Button 
              size="lg"
              className="relative overflow-hidden bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-8 shadow-2xl border border-white/20 backdrop-blur-sm hover-lift"
              onClick={() => setLocation("/login")}
            >
              <Shield className="w-5 h-5 mr-2" />
              Se connecter
              <div className="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
            </Button>
            <Button 
              size="lg" 
              variant="outline"
              className="backdrop-blur-lg bg-white/60 border-2 border-white/40 hover:bg-white/70 shadow-xl hover-lift"
            >
              <Globe className="w-5 h-5 mr-2" />
              Explorer en tant qu'invité
            </Button>
          </div>
        </div>

        {/* Glass Morphism Stats Section */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card className="glass-card hover-lift backdrop-blur-xl bg-white/50 border border-white/30 shadow-2xl hover:shadow-purple-500/10 transition-all duration-300">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-semibold text-gray-800">Annonces</CardTitle>
              <div className="p-2 rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-600/20 backdrop-blur-sm">
                <Bell className="h-4 w-4 text-blue-600" />
              </div>
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent">
                {stats?.totalAnnouncements || 0}
              </div>
              <p className="text-sm text-gray-600 font-medium">Communications actives</p>
            </CardContent>
          </Card>

          <Card className="backdrop-blur-lg bg-white/60 border border-white/20 shadow-lg hover:shadow-xl transition-all duration-300">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Documents</CardTitle>
              <FileText className="h-4 w-4 text-green-600" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold text-green-600">{stats?.totalDocuments || 0}</div>
              <p className="text-xs text-gray-600">Ressources disponibles</p>
            </CardContent>
          </Card>

          <Card className="backdrop-blur-lg bg-white/60 border border-white/20 shadow-lg hover:shadow-xl transition-all duration-300">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Équipe</CardTitle>
              <Users className="h-4 w-4 text-purple-600" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold text-purple-600">{stats?.totalUsers || 0}</div>
              <p className="text-xs text-gray-600">Collaborateurs</p>
            </CardContent>
          </Card>

          <Card className="backdrop-blur-lg bg-white/60 border border-white/20 shadow-lg hover:shadow-xl transition-all duration-300">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Événements</CardTitle>
              <Calendar className="h-4 w-4 text-indigo-600" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold text-indigo-600">{stats?.totalEvents || 0}</div>
              <p className="text-xs text-gray-600">À venir</p>
            </CardContent>
          </Card>
        </div>

        {/* Features Section */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Public Announcements */}
          <Card className="backdrop-blur-lg bg-white/60 border border-white/20 shadow-lg">
            <CardHeader>
              <CardTitle className="flex items-center space-x-2">
                <Bell className="w-5 h-5 text-blue-600" />
                <span>Annonces publiques</span>
              </CardTitle>
              <CardDescription>
                Les dernières nouvelles de l'entreprise
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              {publicAnnouncements.length > 0 ? (
                publicAnnouncements.map((announcement: any) => (
                  <div key={announcement.id} className="p-4 rounded-xl bg-white/50 border border-white/30">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <h4 className="font-semibold text-gray-900 mb-1">
                          {announcement.title}
                        </h4>
                        <p className="text-sm text-gray-600 mb-2 line-clamp-2">
                          {announcement.content}
                        </p>
                        <div className="flex items-center space-x-2">
                          <Badge 
                            variant="secondary"
                            className={
                              announcement.category === "important" 
                                ? "bg-red-100 text-red-800" 
                                : announcement.category === "info"
                                ? "bg-blue-100 text-blue-800"
                                : "bg-green-100 text-green-800"
                            }
                          >
                            {announcement.category}
                          </Badge>
                          <span className="text-xs text-gray-500">
                            <Clock className="w-3 h-3 inline mr-1" />
                            {new Date(announcement.createdAt).toLocaleDateString("fr-FR")}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="text-center py-8 text-gray-500">
                  <Bell className="w-12 h-12 mx-auto mb-3 opacity-50" />
                  <p>Aucune annonce publique pour le moment</p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Features Overview */}
          <Card className="backdrop-blur-lg bg-white/60 border border-white/20 shadow-lg">
            <CardHeader>
              <CardTitle className="flex items-center space-x-2">
                <Globe className="w-5 h-5 text-green-600" />
                <span>Fonctionnalités</span>
              </CardTitle>
              <CardDescription>
                Découvrez nos outils de collaboration
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-4">
                <div className="flex items-center space-x-3 p-3 rounded-lg bg-white/40">
                  <MessageSquare className="w-8 h-8 text-purple-600" />
                  <div>
                    <h4 className="font-semibold text-gray-900">Messagerie interne</h4>
                    <p className="text-sm text-gray-600">Communication sécurisée entre équipes</p>
                  </div>
                </div>
                
                <div className="flex items-center space-x-3 p-3 rounded-lg bg-white/40">
                  <FileText className="w-8 h-8 text-blue-600" />
                  <div>
                    <h4 className="font-semibold text-gray-900">Gestion documentaire</h4>
                    <p className="text-sm text-gray-600">Centralisation et partage de fichiers</p>
                  </div>
                </div>
                
                <div className="flex items-center space-x-3 p-3 rounded-lg bg-white/40">
                  <Users className="w-8 h-8 text-green-600" />
                  <div>
                    <h4 className="font-semibold text-gray-900">Annuaire d'entreprise</h4>
                    <p className="text-sm text-gray-600">Trouvez facilement vos collègues</p>
                  </div>
                </div>
                
                <div className="flex items-center space-x-3 p-3 rounded-lg bg-white/40">
                  <Calendar className="w-8 h-8 text-indigo-600" />
                  <div>
                    <h4 className="font-semibold text-gray-900">Événements</h4>
                    <p className="text-sm text-gray-600">Suivez les actualités de l'entreprise</p>
                  </div>
                </div>
              </div>
              
              <Button 
                className="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700"
                onClick={() => setLocation("/login")}
              >
                Rejoindre la plateforme
                <ArrowRight className="w-4 h-4 ml-2" />
              </Button>
            </CardContent>
          </Card>
        </div>

        {/* Call to Action */}
        <Card className="backdrop-blur-lg bg-white/60 border border-white/20 shadow-lg">
          <CardContent className="p-8">
            <div className="text-center space-y-6">
              <div>
                <h2 className="text-3xl font-bold text-gray-900 mb-2">
                  Prêt à commencer ?
                </h2>
                <p className="text-gray-600 max-w-2xl mx-auto">
                  Rejoignez votre équipe sur IntraSphere et découvrez une nouvelle façon de collaborer. 
                  Accédez à tous vos outils professionnels depuis une seule plateforme.
                </p>
              </div>
              
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <Button 
                  size="lg"
                  className="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-8"
                  onClick={() => setLocation("/login")}
                >
                  <Shield className="w-5 h-5 mr-2" />
                  Connexion employé
                </Button>
                <Button 
                  size="lg" 
                  variant="outline"
                  className="border-2 border-gray-300 hover:bg-gray-50"
                >
                  <Users className="w-5 h-5 mr-2" />
                  Demander l'accès
                </Button>
              </div>
              
              <div className="text-sm text-gray-500">
                <p>Besoin d'aide ? Contactez votre administrateur système</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}