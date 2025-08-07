import { useQuery } from "@tanstack/react-query";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Progress } from "@/core/components/ui/progress";
import { Badge } from "@/core/components/ui/badge";
import { Button } from "@/core/components/ui/button";
import { ChartContainer, ChartTooltip, ChartTooltipContent } from "@/core/components/ui/chart";
import { LineChart, Line, BarChart, Bar, PieChart, Pie, Cell, XAxis, YAxis, CartesianGrid, ResponsiveContainer, Area, AreaChart } from "recharts";
import { Clock, Trophy, Target, TrendingUp, Users, BookOpen, Award, BarChart3, Activity } from "lucide-react";

interface TrainingAnalyticsProps {
  userId?: string;
  showGlobalStats?: boolean;
}

export function TrainingAnalytics({ userId, showGlobalStats = false }: TrainingAnalyticsProps) {
  // Fetch comprehensive training analytics
  const { data: analytics, isLoading } = useQuery({
    queryKey: showGlobalStats ? ["/api/training-analytics/global"] : ["/api/training-analytics", userId]
  });

  const { data: progressData } = useQuery({
    queryKey: ["/api/training-progress", userId]
  });

  const { data: leaderboard } = useQuery({
    queryKey: ["/api/training-leaderboard"]
  });

  const { data: trendData } = useQuery({
    queryKey: ["/api/training-trends"]
  });

  if (isLoading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {[...Array(4)].map((_, i) => (
          <Card key={i} className="animate-pulse">
            <CardContent className="p-6">
              <div className="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
              <div className="h-8 bg-gray-200 rounded w-1/3"></div>
            </CardContent>
          </Card>
        ))}
      </div>
    );
  }

  const chartConfig = {
    progress: { label: "Progress", color: "hsl(var(--primary))" },
    completion: { label: "Completion Rate", color: "hsl(var(--secondary))" },
    timeSpent: { label: "Time Spent", color: "hsl(var(--accent))" },
    score: { label: "Score", color: "hsl(var(--chart-1))" }
  };

  const weeklyData = progressData?.weeklyProgress || [
    { week: "Week 1", hours: 12, courses: 2, score: 85 },
    { week: "Week 2", hours: 15, courses: 3, score: 88 },
    { week: "Week 3", hours: 10, courses: 1, score: 92 },
    { week: "Week 4", hours: 18, courses: 4, score: 89 }
  ];

  const categoryData = analytics?.completionByCategory || [
    { category: "Technical", completion: 85, enrolled: 12, completed: 10 },
    { category: "Leadership", completion: 70, enrolled: 8, completed: 6 },
    { category: "Compliance", completion: 95, enrolled: 5, completed: 5 },
    { category: "Soft Skills", completion: 60, enrolled: 10, completed: 6 }
  ];

  const skillsData = analytics?.skillsProgress || [
    { skill: "JavaScript", level: 85, trend: "+15%" },
    { skill: "Leadership", level: 70, trend: "+8%" },
    { skill: "Communication", level: 78, trend: "+12%" },
    { skill: "Project Management", level: 65, trend: "+20%" }
  ];

  return (
    <div className="space-y-6">
      {/* Key Metrics Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Learning Time</CardTitle>
            <Clock className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analytics?.totalHours || 45}h</div>
            <p className="text-xs text-muted-foreground">
              +{analytics?.weeklyHours || 8}h this week
            </p>
            <div className="mt-2">
              <Progress value={(analytics?.weeklyHours || 8) / 20 * 100} className="h-1" />
            </div>
          </CardContent>
        </Card>

        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Courses Completed</CardTitle>
            <Trophy className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analytics?.completedCourses || 12}</div>
            <p className="text-xs text-muted-foreground">
              of {analytics?.totalEnrolled || 18} enrolled
            </p>
            <div className="mt-2">
              <Progress value={((analytics?.completedCourses || 12) / (analytics?.totalEnrolled || 18)) * 100} className="h-1" />
            </div>
          </CardContent>
        </Card>

        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Average Score</CardTitle>
            <Target className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analytics?.averageScore || 87}%</div>
            <p className="text-xs text-muted-foreground">
              +{analytics?.scoreImprovement || 5}% improvement
            </p>
            <div className="mt-2">
              <Progress value={analytics?.averageScore || 87} className="h-1" />
            </div>
          </CardContent>
        </Card>

        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Certificates</CardTitle>
            <Award className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analytics?.certificates || 8}</div>
            <p className="text-xs text-muted-foreground">
              +{analytics?.recentCertificates || 2} this month
            </p>
            <div className="mt-2">
              <Badge variant="secondary" className="text-xs">
                Top 10% performer
              </Badge>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Charts Row */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Weekly Progress Chart */}
        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <TrendingUp className="h-5 w-5" />
              Learning Progress
            </CardTitle>
            <CardDescription>Your weekly learning activity and performance</CardDescription>
          </CardHeader>
          <CardContent>
            <ChartContainer config={chartConfig} className="h-[300px]">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={weeklyData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="week" />
                  <YAxis />
                  <ChartTooltip content={<ChartTooltipContent />} />
                  <Area 
                    type="monotone" 
                    dataKey="hours" 
                    stroke="var(--color-progress)" 
                    fill="var(--color-progress)"
                    fillOpacity={0.3}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="score" 
                    stroke="var(--color-score)" 
                    strokeWidth={2}
                    dot={{ fill: "var(--color-score)" }}
                  />
                </AreaChart>
              </ResponsiveContainer>
            </ChartContainer>
          </CardContent>
        </Card>

        {/* Category Completion Chart */}
        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <BarChart3 className="h-5 w-5" />
              Category Progress
            </CardTitle>
            <CardDescription>Completion rates across learning categories</CardDescription>
          </CardHeader>
          <CardContent>
            <ChartContainer config={chartConfig} className="h-[300px]">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={categoryData} layout="horizontal">
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" domain={[0, 100]} />
                  <YAxis dataKey="category" type="category" width={80} />
                  <ChartTooltip content={<ChartTooltipContent />} />
                  <Bar 
                    dataKey="completion" 
                    fill="var(--color-completion)"
                    radius={[0, 4, 4, 0]}
                  />
                </BarChart>
              </ResponsiveContainer>
            </ChartContainer>
          </CardContent>
        </Card>
      </div>

      {/* Skills Progress & Leaderboard */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Skills Development */}
        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Activity className="h-5 w-5" />
              Skills Development
            </CardTitle>
            <CardDescription>Your skill progression and growth trends</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {skillsData.map((skill, index) => (
                <div key={skill.skill} className="space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">{skill.skill}</span>
                    <div className="flex items-center gap-2">
                      <span className="text-sm text-muted-foreground">{skill.level}%</span>
                      <Badge variant="outline" className="text-xs">
                        {skill.trend}
                      </Badge>
                    </div>
                  </div>
                  <Progress value={skill.level} className="h-2" />
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Leaderboard */}
        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Users className="h-5 w-5" />
              Leaderboard
            </CardTitle>
            <CardDescription>Top performers this month</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {(leaderboard || [
                { rank: 1, name: "You", hours: 45, courses: 12, avatar: "👤" },
                { rank: 2, name: "Marie Martin", hours: 42, courses: 11, avatar: "👤" },
                { rank: 3, name: "Pierre Dubois", hours: 38, courses: 10, avatar: "👤" },
                { rank: 4, name: "Sophie Chen", hours: 35, courses: 9, avatar: "👤" },
                { rank: 5, name: "Lucas Bernard", hours: 33, courses: 8, avatar: "👤" }
              ]).slice(0, 5).map((user: any) => (
                <div key={user.rank} className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50">
                  <div className="flex items-center gap-3">
                    <div className={`w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold ${
                      user.rank === 1 ? 'bg-yellow-500 text-white' :
                      user.rank === 2 ? 'bg-gray-400 text-white' :
                      user.rank === 3 ? 'bg-amber-600 text-white' :
                      'bg-gray-200 text-gray-700'
                    }`}>
                      {user.rank}
                    </div>
                    <div>
                      <p className="text-sm font-medium">{user.name}</p>
                      <p className="text-xs text-muted-foreground">
                        {user.hours}h • {user.courses} courses
                      </p>
                    </div>
                  </div>
                  {user.rank <= 3 && (
                    <Badge variant={user.rank === 1 ? "default" : "secondary"} className="text-xs">
                      {user.rank === 1 ? "🏆" : user.rank === 2 ? "🥈" : "🥉"}
                    </Badge>
                  )}
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Learning Recommendations */}
      {analytics?.recommendations && (
        <Card className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 border border-white/20">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <BookOpen className="h-5 w-5" />
              Personalized Recommendations
            </CardTitle>
            <CardDescription>Based on your learning patterns and goals</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {analytics.recommendations.slice(0, 3).map((rec: any, index: number) => (
                <div key={index} className="border rounded-lg p-4 hover:bg-accent/50 transition-colors">
                  <h4 className="font-medium mb-2">{rec.title}</h4>
                  <p className="text-sm text-muted-foreground mb-3">{rec.reason}</p>
                  <div className="flex items-center justify-between">
                    <Badge variant="outline">{rec.category}</Badge>
                    <Button size="sm" variant="outline">
                      Start Learning
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  );
}