import { useQuery } from "@tanstack/react-query";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Badge } from "@/core/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/core/components/ui/tabs";
import { Progress } from "@/core/components/ui/progress";
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer,
  LineChart,
  Line,
  PieChart,
  Pie,
  Cell,
  Legend
} from "recharts";
import { 
  TrendingUp, 
  Users, 
  FileText, 
  MessageSquare,
  Eye,
  Download,
  Star,
  Calendar,
  BarChart3,
  Activity,
  Target,
  Award
} from "lucide-react";

export function AnalyticsDashboard() {
  // Fetch dashboard analytics
  const { data: analytics } = useQuery<any>({
    queryKey: ['/api/dashboard/analytics'],
  });

  // Fetch activity data
  const { data: activityData } = useQuery<any>({
    queryKey: ['/api/dashboard/activity'],
  });

  // Fetch top content
  const { data: topContent } = useQuery<any>({
    queryKey: ['/api/dashboard/top-content'],
  });

  const weeklyActivity = activityData?.weeklyActivity || [];
  const topEngagement = topContent?.engagement || [];

  // Chart colors
  const chartColors = ["#8b5cf6", "#06b6d4", "#10b981", "#f59e0b", "#ef4444"];

  // Prepare pie chart data for user engagement
  const engagementData = analytics ? [
    { name: "Utilisateurs actifs", value: analytics.activeUsers, color: "#8b5cf6" },
    { name: "Utilisateurs inactifs", value: analytics.totalUsers - analytics.activeUsers, color: "#e5e7eb" }
  ] : [];

  // Prepare completion data
  const completionData = [
    { name: "Formation", completion: analytics?.trainingCompletion || 0, target: 80 },
    { name: "Contenu", completion: analytics?.contentEngagement || 0, target: 70 },
    { name: "Participation", completion: ((analytics?.activeUsers / analytics?.totalUsers) * 100) || 0, target: 60 }
  ];

  const formatNumber = (num: number) => {
    return num.toLocaleString('fr-FR');
  };

  const formatPercentage = (value: number) => {
    return `${Math.round(value)}%`;
  };

  return (
    <div className="space-y-6" data-testid="analytics-dashboard">
      {/* Overview Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                <Users className="h-5 w-5 text-purple-600 dark:text-purple-400" />
              </div>
              <div>
                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                  {formatNumber(analytics?.totalUsers || 0)}
                </div>
                <div className="text-sm text-gray-600 dark:text-gray-400">
                  Utilisateurs totaux
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                <Activity className="h-5 w-5 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                  {formatNumber(analytics?.activeUsers || 0)}
                </div>
                <div className="text-sm text-gray-600 dark:text-gray-400">
                  Utilisateurs actifs
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                <FileText className="h-5 w-5 text-blue-600 dark:text-blue-400" />
              </div>
              <div>
                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                  {formatPercentage(analytics?.contentEngagement || 0)}
                </div>
                <div className="text-sm text-gray-600 dark:text-gray-400">
                  Engagement contenu
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                <Award className="h-5 w-5 text-orange-600 dark:text-orange-400" />
              </div>
              <div>
                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                  {formatPercentage(analytics?.trainingCompletion || 0)}
                </div>
                <div className="text-sm text-gray-600 dark:text-gray-400">
                  Formations complétées
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <Tabs defaultValue="activity" className="space-y-4">
        <TabsList>
          <TabsTrigger value="activity">Activité</TabsTrigger>
          <TabsTrigger value="engagement">Engagement</TabsTrigger>
          <TabsTrigger value="content">Contenu populaire</TabsTrigger>
          <TabsTrigger value="objectives">Objectifs</TabsTrigger>
        </TabsList>

        <TabsContent value="activity" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <BarChart3 className="h-5 w-5" />
                Activité hebdomadaire
              </CardTitle>
              <CardDescription>
                Évolution de l'activité sur les 7 derniers jours
              </CardDescription>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={weeklyActivity}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="day" />
                  <YAxis />
                  <Tooltip />
                  <Legend />
                  <Bar dataKey="users" fill="#8b5cf6" name="Utilisateurs" />
                  <Bar dataKey="documents" fill="#06b6d4" name="Documents" />
                  <Bar dataKey="messages" fill="#10b981" name="Messages" />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="engagement" className="space-y-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Répartition des utilisateurs</CardTitle>
                <CardDescription>
                  Utilisateurs actifs vs inactifs
                </CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={250}>
                  <PieChart>
                    <Pie
                      data={engagementData}
                      cx="50%"
                      cy="50%"
                      innerRadius={60}
                      outerRadius={100}
                      paddingAngle={5}
                      dataKey="value"
                    >
                      {engagementData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={entry.color} />
                      ))}
                    </Pie>
                    <Tooltip />
                    <Legend />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Tendance d'engagement</CardTitle>
                <CardDescription>
                  Évolution de l'engagement utilisateur
                </CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={250}>
                  <LineChart data={weeklyActivity}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="day" />
                    <YAxis />
                    <Tooltip />
                    <Line 
                      type="monotone" 
                      dataKey="users" 
                      stroke="#8b5cf6" 
                      strokeWidth={2}
                      name="Utilisateurs actifs"
                    />
                  </LineChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="content" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <TrendingUp className="h-5 w-5" />
                Contenu le plus populaire
              </CardTitle>
              <CardDescription>
                Top des contenus les plus consultés et téléchargés
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {topEngagement.map((item: any, index: number) => (
                  <div 
                    key={index}
                    className="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700"
                  >
                    <div className="flex items-center gap-3">
                      <Badge variant="outline" className="w-8 h-8 rounded-full flex items-center justify-center">
                        {index + 1}
                      </Badge>
                      <div>
                        <div className="font-medium text-gray-900 dark:text-white">
                          {item.name}
                        </div>
                        <div className="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                          <div className="flex items-center gap-1">
                            <Eye className="h-3 w-3" />
                            <span>{formatNumber(item.views)} vues</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <Download className="h-3 w-3" />
                            <span>{formatNumber(item.downloads)} téléchargements</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <Star className="h-3 w-3 text-yellow-500" />
                            <span>{item.rating}/5</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div className="text-right">
                      <div className="text-sm font-medium text-gray-900 dark:text-white">
                        Score engagement
                      </div>
                      <div className="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {Math.round((item.views * 0.3 + item.downloads * 0.5 + item.rating * 20) / 10)}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="objectives" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Target className="h-5 w-5" />
                Objectifs de performance
              </CardTitle>
              <CardDescription>
                Suivi des indicateurs clés de performance
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-6">
                {completionData.map((objective, index) => (
                  <div key={index} className="space-y-2">
                    <div className="flex items-center justify-between">
                      <div className="font-medium text-gray-900 dark:text-white">
                        {objective.name}
                      </div>
                      <div className="text-sm text-gray-600 dark:text-gray-400">
                        {formatPercentage(objective.completion)} / {formatPercentage(objective.target)}
                      </div>
                    </div>
                    <Progress 
                      value={objective.completion} 
                      className="h-2"
                    />
                    <div className="flex items-center justify-between text-xs">
                      <div className={`font-medium ${
                        objective.completion >= objective.target 
                          ? 'text-green-600 dark:text-green-400' 
                          : objective.completion >= objective.target * 0.8
                          ? 'text-yellow-600 dark:text-yellow-400'
                          : 'text-red-600 dark:text-red-400'
                      }`}>
                        {objective.completion >= objective.target ? '✓ Objectif atteint' : 
                         objective.completion >= objective.target * 0.8 ? '⚠ Proche de l\'objectif' : 
                         '✗ En dessous de l\'objectif'}
                      </div>
                      <div className="text-gray-500">
                        {formatPercentage(Math.abs(objective.completion - objective.target))} 
                        {objective.completion >= objective.target ? ' au-dessus' : ' restant'}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}