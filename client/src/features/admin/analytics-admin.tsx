import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { apiRequest } from "@/core/lib/queryClient";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Button } from "@/core/components/ui/button";
import { Badge } from "@/core/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/core/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/core/components/ui/select";
import { AnalyticsDashboard } from "@/core/components/dashboard/analytics-dashboard";
import { 
  Calendar,
  Download,
  RefreshCw,
  Settings,
  Users,
  BarChart3,
  TrendingUp,
  Activity,
  Target,
  Filter
} from "lucide-react";

export function AnalyticsAdminPage() {
  const [dateRange, setDateRange] = useState("7d");
  const [selectedMetric, setSelectedMetric] = useState("all");
  const queryClient = useQueryClient();

  // Fetch real-time analytics data
  const { data: liveAnalytics, isLoading: analyticsLoading } = useQuery<any>({
    queryKey: ['/api/dashboard/analytics', dateRange, selectedMetric],
    refetchInterval: 30000, // Refresh every 30 seconds
  });

  // Refresh analytics mutation
  const refreshMutation = useMutation({
    mutationFn: async () => {
      return apiRequest('/api/dashboard/refresh-analytics', "POST");
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/dashboard/analytics'] });
      queryClient.invalidateQueries({ queryKey: ['/api/dashboard/activity'] });
      queryClient.invalidateQueries({ queryKey: ['/api/dashboard/top-content'] });
    }
  });

  const exportData = () => {
    // Export analytics data
    const dataToExport = {
      analytics: liveAnalytics,
      exportDate: new Date().toISOString(),
      dateRange,
      selectedMetric
    };
    
    const blob = new Blob([JSON.stringify(dataToExport, null, 2)], {
      type: 'application/json'
    });
    
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `analytics-export-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };

  return (
    <div className="p-6 space-y-6" data-testid="analytics-admin-page">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
            Analytics & Reporting
          </h1>
          <p className="text-gray-600 dark:text-gray-400 mt-1">
            Tableau de bord analytique avancé et rapports détaillés
          </p>
        </div>
        
        <div className="flex items-center gap-3">
          <Select value={dateRange} onValueChange={setDateRange}>
            <SelectTrigger className="w-32">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="1d">24h</SelectItem>
              <SelectItem value="7d">7 jours</SelectItem>
              <SelectItem value="30d">30 jours</SelectItem>
              <SelectItem value="90d">3 mois</SelectItem>
            </SelectContent>
          </Select>
          
          <Select value={selectedMetric} onValueChange={setSelectedMetric}>
            <SelectTrigger className="w-40">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Toutes métriques</SelectItem>
              <SelectItem value="users">Utilisateurs</SelectItem>
              <SelectItem value="content">Contenu</SelectItem>
              <SelectItem value="training">Formation</SelectItem>
              <SelectItem value="forum">Forum</SelectItem>
            </SelectContent>
          </Select>
          
          <Button
            variant="outline"
            size="sm"
            onClick={() => refreshMutation.mutate()}
            disabled={refreshMutation.isPending}
            data-testid="button-refresh-analytics"
          >
            <RefreshCw className={`h-4 w-4 mr-2 ${refreshMutation.isPending ? 'animate-spin' : ''}`} />
            Actualiser
          </Button>
          
          <Button
            variant="outline"
            size="sm"
            onClick={exportData}
            data-testid="button-export-analytics"
          >
            <Download className="h-4 w-4 mr-2" />
            Exporter
          </Button>
        </div>
      </div>

      {/* Real-time Status */}
      <Card>
        <CardContent className="p-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
              <span className="text-sm font-medium text-gray-900 dark:text-white">
                Données en temps réel
              </span>
              <Badge variant="outline" className="text-xs">
                Dernière mise à jour: {new Date().toLocaleTimeString('fr-FR')}
              </Badge>
            </div>
            
            <div className="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
              <div className="flex items-center gap-1">
                <Activity className="h-4 w-4" />
                <span>Connectés: {liveAnalytics?.activeUsers || 0}</span>
              </div>
              <div className="flex items-center gap-1">
                <BarChart3 className="h-4 w-4" />
                <span>Période: {dateRange}</span>
              </div>
              <div className="flex items-center gap-1">
                <Filter className="h-4 w-4" />
                <span>Filtre: {selectedMetric}</span>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Main Analytics Dashboard */}
      <AnalyticsDashboard />

      {/* Advanced Analytics Tabs */}
      <Tabs defaultValue="detailed" className="space-y-4">
        <TabsList>
          <TabsTrigger value="detailed">Analytics détaillées</TabsTrigger>
          <TabsTrigger value="reports">Rapports</TabsTrigger>
          <TabsTrigger value="predictions">Prédictions</TabsTrigger>
          <TabsTrigger value="settings">Configuration</TabsTrigger>
        </TabsList>

        <TabsContent value="detailed" className="space-y-4">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Users className="h-5 w-5" />
                  Analyse utilisateurs
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span>Nouveaux utilisateurs</span>
                    <span className="font-medium">+{liveAnalytics?.newUsers || 12}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Taux de rétention</span>
                    <span className="font-medium text-green-600">85%</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Temps moyen de session</span>
                    <span className="font-medium">24min</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Connexions par jour</span>
                    <span className="font-medium">2.3</span>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <BarChart3 className="h-5 w-5" />
                  Performance contenu
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span>Documents consultés</span>
                    <span className="font-medium">{liveAnalytics?.documentsViewed || 156}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Taux de téléchargement</span>
                    <span className="font-medium text-blue-600">67%</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Évaluations moyennes</span>
                    <span className="font-medium">4.6/5</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Partages sociaux</span>
                    <span className="font-medium">+{liveAnalytics?.socialShares || 28}</span>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <TrendingUp className="h-5 w-5" />
                  Tendances
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span>Croissance hebdomadaire</span>
                    <span className="font-medium text-green-600">+12%</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Engagement moyen</span>
                    <span className="font-medium text-purple-600">78%</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Score satisfaction</span>
                    <span className="font-medium">4.2/5</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span>Objectifs atteints</span>
                    <span className="font-medium text-blue-600">9/12</span>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="reports" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Générateur de rapports</CardTitle>
              <CardDescription>
                Créez des rapports personnalisés pour l'analyse et le suivi
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-gray-500 dark:text-gray-400">
                <BarChart3 className="h-12 w-12 mx-auto mb-4 opacity-50" />
                <p className="text-lg font-medium mb-2">Générateur de rapports</p>
                <p className="text-sm mb-4">
                  Fonctionnalité avancée de génération de rapports personnalisés
                </p>
                <Button variant="outline">
                  <Settings className="h-4 w-4 mr-2" />
                  Configurer les rapports
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="predictions" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Analytics prédictives</CardTitle>
              <CardDescription>
                Prédictions basées sur l'analyse des tendances
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-gray-500 dark:text-gray-400">
                <Target className="h-12 w-12 mx-auto mb-4 opacity-50" />
                <p className="text-lg font-medium mb-2">Analytics prédictives</p>
                <p className="text-sm mb-4">
                  Modèles de prédiction et analyse de tendances avancée
                </p>
                <Button variant="outline">
                  <TrendingUp className="h-4 w-4 mr-2" />
                  Configurer les prédictions
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="settings" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Configuration Analytics</CardTitle>
              <CardDescription>
                Paramètres avancés du système d'analytics
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-gray-500 dark:text-gray-400">
                <Settings className="h-12 w-12 mx-auto mb-4 opacity-50" />
                <p className="text-lg font-medium mb-2">Configuration avancée</p>
                <p className="text-sm mb-4">
                  Paramètres de collecte, rétention et traitement des données
                </p>
                <Button variant="outline">
                  <Settings className="h-4 w-4 mr-2" />
                  Accéder aux paramètres
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}