import { useQuery } from "@tanstack/react-query";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Progress } from "@/core/components/ui/progress";
import { Badge } from "@/core/components/ui/badge";
import { Button } from "@/core/components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/core/components/ui/tabs";
import { ChartContainer, ChartTooltip, ChartTooltipContent } from "@/core/components/ui/chart";
import { LineChart, Line, BarChart, Bar, PieChart, Pie, Cell, XAxis, YAxis, CartesianGrid, ResponsiveContainer, AreaChart, Area } from "recharts";
import { 
  Users, 
  FileText, 
  MessageSquare, 
  BookOpen, 
  TrendingUp, 
  Activity, 
  Clock, 
  Target,
  Award,
  Bell,
  Calendar,
  Download,
  Eye,
  Star,
  MessageCircle
} from "lucide-react";
import { useWebSocket } from "@/core/hooks/useWebSocket";

interface DashboardMetrics {
  totalUsers: number;
  totalAnnouncements: number;
  totalDocuments: number;
  totalEvents: number;
  totalMessages: number;
  totalComplaints: number;
  newAnnouncements: number;
  updatedDocuments: number;
  connectedUsers: number;
  pendingComplaints: number;
}

export function EnhancedDashboard() {
  const { isConnected, connectionState } = useWebSocket();
  
  const { data: stats, isLoading: statsLoading } = useQuery<DashboardMetrics>({
    queryKey: ["/api/stats"]
  });

  const { data: recentActivity } = useQuery<any>({
    queryKey: ["/api/dashboard/activity"]
  });

  const { data: analytics } = useQuery<any>({
    queryKey: ["/api/dashboard/analytics"]
  });

  const { data: topContent } = useQuery<any>({
    queryKey: ["/api/dashboard/top-content"]
  });

  if (statsLoading) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
      </div>
    );
  }

  const quickStats = [
    {
      title: "Utilisateurs Connectés",
      value: isConnected ? (stats?.connectedUsers || 0) : 0,
      change: "+12%",
      icon: Users,
      color: "text-blue-600",
      bgColor: "bg-blue-100"
    },
    {
      title: "Documents Partagés",
      value: stats?.totalDocuments || 0,
      change: `+${stats?.updatedDocuments || 0}`,
      icon: FileText,
      color: "text-green-600",
      bgColor: "bg-green-100"
    },
    {
      title: "Annonces Actives",
      value: stats?.totalAnnouncements || 0,
      change: `+${stats?.newAnnouncements || 0}`,
      icon: MessageSquare,
      color: "text-orange-600",
      bgColor: "bg-orange-100"
    },
    {
      title: "Formations Suivies",
      value: 45,
      change: "+8%",
      icon: BookOpen,
      color: "text-purple-600",
      bgColor: "bg-purple-100"
    }
  ];

  const activityData = recentActivity?.weeklyActivity || [
    { day: "Lun", users: 24, documents: 12, messages: 45 },
    { day: "Mar", users: 28, documents: 15, messages: 52 },
    { day: "Mer", users: 32, documents: 18, messages: 48 },
    { day: "Jeu", users: 35, documents: 22, messages: 65 },
    { day: "Ven", users: 42, documents: 28, messages: 78 },
    { day: "Sam", users: 18, documents: 8, messages: 32 },
    { day: "Dim", users: 15, documents: 5, messages: 28 }
  ];

  const contentEngagement = topContent?.engagement || [
    { name: "Formation React", views: 245, downloads: 89, rating: 4.8 },
    { name: "Guide Sécurité", views: 189, downloads: 67, rating: 4.6 },
    { name: "Proc. Qualité", views: 156, downloads: 54, rating: 4.5 },
    { name: "Manuel Onboarding", views: 134, downloads: 45, rating: 4.7 }
  ];

  const chartConfig = {
    users: { label: "Utilisateurs", color: "hsl(var(--chart-1))" },
    documents: { label: "Documents", color: "hsl(var(--chart-2))" },
    messages: { label: "Messages", color: "hsl(var(--chart-3))" }
  };

  return (
    <div className="space-y-6">
      {/* Connection Status */}
      <div className="flex items-center justify-between">
        <h2 className="text-2xl font-bold">Tableau de Bord</h2>
        <div className="flex items-center gap-2">
          <div className={`w-2 h-2 rounded-full ${isConnected ? 'bg-green-500' : 'bg-red-500'}`} />
          <span className="text-sm text-muted-foreground">
            {isConnected ? 'Connecté' : 'Déconnecté'}
          </span>
        </div>
      </div>

      {/* Quick Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {quickStats.map((stat, index) => {
          const Icon = stat.icon;
          return (
            <Card key={index} className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">{stat.title}</CardTitle>
                <div className={`p-2 rounded-full ${stat.bgColor}`}>
                  <Icon className={`h-4 w-4 ${stat.color}`} />
                </div>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stat.value}</div>
                <p className="text-xs text-muted-foreground">
                  {stat.change} ce mois
                </p>
              </CardContent>
            </Card>
          );
        })}
      </div>

      {/* Analytics Tabs */}
      <Tabs defaultValue="activity" className="space-y-4">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="activity">Activité</TabsTrigger>
          <TabsTrigger value="content">Contenu</TabsTrigger>
          <TabsTrigger value="performance">Performance</TabsTrigger>
        </TabsList>

        {/* Activity Tab */}
        <TabsContent value="activity" className="space-y-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Activity className="h-5 w-5" />
                  Activité Hebdomadaire
                </CardTitle>
                <CardDescription>Évolution de l'engagement utilisateur</CardDescription>
              </CardHeader>
              <CardContent>
                <ChartContainer config={chartConfig} className="h-[300px]">
                  <ResponsiveContainer width="100%" height="100%">
                    <AreaChart data={activityData}>
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="day" />
                      <YAxis />
                      <ChartTooltip content={<ChartTooltipContent />} />
                      <Area 
                        type="monotone" 
                        dataKey="users" 
                        stackId="1"
                        stroke="var(--color-users)" 
                        fill="var(--color-users)"
                        fillOpacity={0.6}
                      />
                      <Area 
                        type="monotone" 
                        dataKey="messages" 
                        stackId="1"
                        stroke="var(--color-messages)" 
                        fill="var(--color-messages)"
                        fillOpacity={0.6}
                      />
                    </AreaChart>
                  </ResponsiveContainer>
                </ChartContainer>
              </CardContent>
            </Card>

            <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <TrendingUp className="h-5 w-5" />
                  Évolution Mensuelle
                </CardTitle>
                <CardDescription>Croissance des indicateurs clés</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">Nouveaux Utilisateurs</span>
                    <span className="text-sm font-bold text-green-600">+23%</span>
                  </div>
                  <Progress value={75} className="h-2" />
                </div>
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">Engagement Contenu</span>
                    <span className="text-sm font-bold text-blue-600">+18%</span>
                  </div>
                  <Progress value={68} className="h-2" />
                </div>
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">Formations Complétées</span>
                    <span className="text-sm font-bold text-purple-600">+31%</span>
                  </div>
                  <Progress value={82} className="h-2" />
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Content Tab */}
        <TabsContent value="content" className="space-y-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Star className="h-5 w-5" />
                  Contenu Populaire
                </CardTitle>
                <CardDescription>Les ressources les plus consultées</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {contentEngagement.map((item, index) => (
                    <div key={index} className="flex items-center justify-between p-3 border rounded-lg">
                      <div className="flex-1">
                        <h4 className="font-medium text-sm">{item.name}</h4>
                        <div className="flex items-center gap-4 mt-1 text-xs text-muted-foreground">
                          <span className="flex items-center gap-1">
                            <Eye className="h-3 w-3" />
                            {item.views}
                          </span>
                          <span className="flex items-center gap-1">
                            <Download className="h-3 w-3" />
                            {item.downloads}
                          </span>
                          <span className="flex items-center gap-1">
                            <Star className="h-3 w-3" />
                            {item.rating}
                          </span>
                        </div>
                      </div>
                      <Badge variant="secondary">#{index + 1}</Badge>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <MessageCircle className="h-5 w-5" />
                  Engagement Forum
                </CardTitle>
                <CardDescription>Discussions et interactions</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-sm">Nouveaux Topics</span>
                    <Badge variant="outline">+12 cette semaine</Badge>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm">Messages Postés</span>
                    <Badge variant="outline">+87 cette semaine</Badge>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm">Utilisateurs Actifs</span>
                    <Badge variant="outline">34 participants</Badge>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm">Taux de Réponse</span>
                    <Badge variant="default">94%</Badge>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Performance Tab */}
        <TabsContent value="performance" className="space-y-4">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Target className="h-5 w-5" />
                  Objectifs
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="space-y-2">
                    <div className="flex justify-between text-sm">
                      <span>Adoption Formations</span>
                      <span>78%</span>
                    </div>
                    <Progress value={78} />
                  </div>
                  <div className="space-y-2">
                    <div className="flex justify-between text-sm">
                      <span>Satisfaction Utilisateurs</span>
                      <span>91%</span>
                    </div>
                    <Progress value={91} />
                  </div>
                  <div className="space-y-2">
                    <div className="flex justify-between text-sm">
                      <span>Utilisation Documents</span>
                      <span>65%</span>
                    </div>
                    <Progress value={65} />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Clock className="h-5 w-5" />
                  Temps Moyen
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="text-center">
                    <div className="text-3xl font-bold">8.5min</div>
                    <p className="text-sm text-muted-foreground">par session</p>
                  </div>
                  <div className="text-center">
                    <div className="text-3xl font-bold">2.3</div>
                    <p className="text-sm text-muted-foreground">pages visitées</p>
                  </div>
                  <div className="text-center">
                    <div className="text-3xl font-bold">87%</div>
                    <p className="text-sm text-muted-foreground">taux de rétention</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Award className="h-5 w-5" />
                  Accomplissements
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-3">
                  <div className="flex items-center gap-2">
                    <div className="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                      🏆
                    </div>
                    <div>
                      <div className="text-sm font-medium">Top Contributeur</div>
                      <div className="text-xs text-muted-foreground">Marie Martin</div>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <div className="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                      ⭐
                    </div>
                    <div>
                      <div className="text-sm font-medium">Expert Formation</div>
                      <div className="text-xs text-muted-foreground">15 certifiés</div>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <div className="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                      🎯
                    </div>
                    <div>
                      <div className="text-sm font-medium">Objectif Atteint</div>
                      <div className="text-xs text-muted-foreground">100% engagement</div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
}