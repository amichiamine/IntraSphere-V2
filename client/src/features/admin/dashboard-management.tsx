import { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { MainLayout } from "@/core/components/layout/main-layout";
import { GlassCard } from "@/core/components/ui/glass-card";
import { Button } from "@/core/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Badge } from "@/core/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/core/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/core/components/ui/select";
import { Switch } from "@/core/components/ui/switch";
import { Label } from "@/core/components/ui/label";
import { 
  Settings, Users, FileText, MessageSquare, BookOpen, 
  TrendingUp, Activity, Calendar, BarChart3, PieChart,
  Zap, Shield, Database, Globe, Palette, Layout
} from "lucide-react";
import { apiRequest, queryClient } from "@/core/lib/queryClient";
import { useToast } from "@/core/hooks/use-toast";

interface SystemSettings {
  id: string;
  siteName: string;
  maintenanceMode: boolean;
  registrationEnabled: boolean;
  emailNotifications: boolean;
  theme: string;
  language: string;
  maxFileSize: number;
  allowedFileTypes: string[];
  backupFrequency: string;
  analyticsEnabled: boolean;
}

interface DashboardMetrics {
  totalUsers: number;
  activeUsers: number;
  totalAnnouncements: number;
  totalDocuments: number;
  totalTrainings: number;
  recentActivity: number;
}

export default function DashboardManagement() {
  const [activeTab, setActiveTab] = useState("overview");
  const { toast } = useToast();

  // Fetch system settings
  const { data: systemSettings, isLoading: isLoadingSettings } = useQuery<SystemSettings>({
    queryKey: ["/api/system-settings"],
  });

  // Fetch dashboard metrics
  const { data: metrics, isLoading: isLoadingMetrics } = useQuery<DashboardMetrics>({
    queryKey: ["/api/dashboard/metrics"],
  });

  // Fetch recent activity
  const { data: recentActivity = [], isLoading: isLoadingActivity } = useQuery({
    queryKey: ["/api/dashboard/recent-activity"],
  });

  // Update system settings mutation
  const updateSettingsMutation = useMutation({
    mutationFn: async (settings: Partial<SystemSettings>) => {
      return apiRequest("/api/system-settings", "PATCH", settings);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/system-settings"] });
      toast({
        title: "Paramètres mis à jour",
        description: "Les paramètres système ont été sauvegardés avec succès.",
      });
    },
    onError: () => {
      toast({
        title: "Erreur",
        description: "Impossible de mettre à jour les paramètres.",
        variant: "destructive",
      });
    },
  });

  const handleSettingsUpdate = (key: string, value: any) => {
    if (systemSettings) {
      updateSettingsMutation.mutate({
        ...systemSettings,
        [key]: value
      });
    }
  };

  if (isLoadingSettings || isLoadingMetrics) {
    return (
      <MainLayout>
        <div className="container mx-auto p-6">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[...Array(6)].map((_, i) => (
              <div key={i} className="h-32 bg-gray-200 dark:bg-gray-700 animate-pulse rounded-lg" />
            ))}
          </div>
        </div>
      </MainLayout>
    );
  }

  return (
    <MainLayout>
      <div className="container mx-auto p-6 space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
              Gestion du Dashboard
            </h1>
            <p className="text-gray-600 dark:text-gray-400 mt-2">
              Configuration et supervision de la plateforme IntraSphere
            </p>
          </div>
          <Badge variant="outline" className="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
            {systemSettings?.maintenanceMode ? "Maintenance" : "En ligne"}
          </Badge>
        </div>

        <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-6">
          <TabsList className="grid w-full grid-cols-4">
            <TabsTrigger value="overview" className="flex items-center gap-2">
              <BarChart3 className="h-4 w-4" />
              Vue d'ensemble
            </TabsTrigger>
            <TabsTrigger value="settings" className="flex items-center gap-2">
              <Settings className="h-4 w-4" />
              Paramètres
            </TabsTrigger>
            <TabsTrigger value="analytics" className="flex items-center gap-2">
              <PieChart className="h-4 w-4" />
              Analytics
            </TabsTrigger>
            <TabsTrigger value="security" className="flex items-center gap-2">
              <Shield className="h-4 w-4" />
              Sécurité
            </TabsTrigger>
          </TabsList>

          <TabsContent value="overview" className="space-y-6">
            {/* Metrics Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <GlassCard className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                      Utilisateurs Actifs
                    </p>
                    <p className="text-3xl font-bold text-gray-900 dark:text-white">
                      {metrics?.activeUsers || 0}
                    </p>
                  </div>
                  <Users className="h-8 w-8 text-blue-500" />
                </div>
                <div className="mt-4 flex items-center text-sm">
                  <TrendingUp className="h-4 w-4 text-green-500 mr-1" />
                  <span className="text-green-600 dark:text-green-400">
                    +12% ce mois
                  </span>
                </div>
              </GlassCard>

              <GlassCard className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                      Documents
                    </p>
                    <p className="text-3xl font-bold text-gray-900 dark:text-white">
                      {metrics?.totalDocuments || 0}
                    </p>
                  </div>
                  <FileText className="h-8 w-8 text-green-500" />
                </div>
                <div className="mt-4 flex items-center text-sm">
                  <TrendingUp className="h-4 w-4 text-green-500 mr-1" />
                  <span className="text-green-600 dark:text-green-400">
                    +8% ce mois
                  </span>
                </div>
              </GlassCard>

              <GlassCard className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                      Formations
                    </p>
                    <p className="text-3xl font-bold text-gray-900 dark:text-white">
                      {metrics?.totalTrainings || 0}
                    </p>
                  </div>
                  <BookOpen className="h-8 w-8 text-purple-500" />
                </div>
                <div className="mt-4 flex items-center text-sm">
                  <TrendingUp className="h-4 w-4 text-green-500 mr-1" />
                  <span className="text-green-600 dark:text-green-400">
                    +15% ce mois
                  </span>
                </div>
              </GlassCard>

              <GlassCard className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                      Activité Récente
                    </p>
                    <p className="text-3xl font-bold text-gray-900 dark:text-white">
                      {metrics?.recentActivity || 0}
                    </p>
                  </div>
                  <Activity className="h-8 w-8 text-orange-500" />
                </div>
                <div className="mt-4 flex items-center text-sm">
                  <span className="text-gray-600 dark:text-gray-400">
                    Cette semaine
                  </span>
                </div>
              </GlassCard>
            </div>

            {/* Recent Activity */}
            <GlassCard className="p-6">
              <CardHeader className="px-0 pt-0">
                <CardTitle className="flex items-center gap-2">
                  <Activity className="h-5 w-5" />
                  Activité Récente
                </CardTitle>
                <CardDescription>
                  Dernières actions sur la plateforme
                </CardDescription>
              </CardHeader>
              <CardContent className="px-0">
                <div className="space-y-4">
                  {recentActivity.map((activity: any, index: number) => (
                    <div key={index} className="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                      <div className="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        {activity.type === 'announcement' ? (
                          <MessageSquare className="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        ) : (
                          <FileText className="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        )}
                      </div>
                      <div className="flex-1">
                        <p className="font-medium text-gray-900 dark:text-white">
                          {activity.title}
                        </p>
                        <p className="text-sm text-gray-600 dark:text-gray-400">
                          {new Date(activity.date).toLocaleDateString('fr-FR')}
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </GlassCard>
          </TabsContent>

          <TabsContent value="settings" className="space-y-6">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <GlassCard className="p-6">
                <CardHeader className="px-0 pt-0">
                  <CardTitle className="flex items-center gap-2">
                    <Globe className="h-5 w-5" />
                    Paramètres Généraux
                  </CardTitle>
                </CardHeader>
                <CardContent className="px-0 space-y-4">
                  <div className="flex items-center justify-between">
                    <Label htmlFor="maintenance">Mode maintenance</Label>
                    <Switch
                      id="maintenance"
                      checked={systemSettings?.maintenanceMode || false}
                      onCheckedChange={(checked) => handleSettingsUpdate('maintenanceMode', checked)}
                    />
                  </div>
                  
                  <div className="flex items-center justify-between">
                    <Label htmlFor="registration">Inscriptions ouvertes</Label>
                    <Switch
                      id="registration"
                      checked={systemSettings?.registrationEnabled || false}
                      onCheckedChange={(checked) => handleSettingsUpdate('registrationEnabled', checked)}
                    />
                  </div>

                  <div className="flex items-center justify-between">
                    <Label htmlFor="notifications">Notifications email</Label>
                    <Switch
                      id="notifications"
                      checked={systemSettings?.emailNotifications || false}
                      onCheckedChange={(checked) => handleSettingsUpdate('emailNotifications', checked)}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="theme">Thème par défaut</Label>
                    <Select
                      value={systemSettings?.theme || 'light'}
                      onValueChange={(value) => handleSettingsUpdate('theme', value)}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder="Sélectionner un thème" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="light">Clair</SelectItem>
                        <SelectItem value="dark">Sombre</SelectItem>
                        <SelectItem value="auto">Automatique</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </CardContent>
              </GlassCard>

              <GlassCard className="p-6">
                <CardHeader className="px-0 pt-0">
                  <CardTitle className="flex items-center gap-2">
                    <Database className="h-5 w-5" />
                    Paramètres Système
                  </CardTitle>
                </CardHeader>
                <CardContent className="px-0 space-y-4">
                  <div className="flex items-center justify-between">
                    <Label htmlFor="analytics">Analytics activés</Label>
                    <Switch
                      id="analytics"
                      checked={systemSettings?.analyticsEnabled || false}
                      onCheckedChange={(checked) => handleSettingsUpdate('analyticsEnabled', checked)}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="backup">Fréquence des sauvegardes</Label>
                    <Select
                      value={systemSettings?.backupFrequency || 'daily'}
                      onValueChange={(value) => handleSettingsUpdate('backupFrequency', value)}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder="Fréquence" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="hourly">Toutes les heures</SelectItem>
                        <SelectItem value="daily">Quotidienne</SelectItem>
                        <SelectItem value="weekly">Hebdomadaire</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="fileSize">Taille max des fichiers (MB)</Label>
                    <Select
                      value={systemSettings?.maxFileSize?.toString() || '10'}
                      onValueChange={(value) => handleSettingsUpdate('maxFileSize', parseInt(value))}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder="Taille maximale" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="5">5 MB</SelectItem>
                        <SelectItem value="10">10 MB</SelectItem>
                        <SelectItem value="25">25 MB</SelectItem>
                        <SelectItem value="50">50 MB</SelectItem>
                        <SelectItem value="100">100 MB</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </CardContent>
              </GlassCard>
            </div>
          </TabsContent>

          <TabsContent value="analytics" className="space-y-6">
            <div className="text-center py-12">
              <BarChart3 className="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <p className="text-gray-600 dark:text-gray-400">
                Module analytics avancé disponible bientôt
              </p>
            </div>
          </TabsContent>

          <TabsContent value="security" className="space-y-6">
            <div className="text-center py-12">
              <Shield className="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <p className="text-gray-600 dark:text-gray-400">
                Paramètres de sécurité avancés disponibles bientôt
              </p>
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </MainLayout>
  );
}