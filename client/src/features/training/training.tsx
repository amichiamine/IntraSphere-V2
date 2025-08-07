import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { apiRequest } from "@/core/lib/queryClient";
import { Button } from "@/core/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Badge } from "@/core/components/ui/badge";
import { Progress } from "@/core/components/ui/progress";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/core/components/ui/tabs";
import { Input } from "@/core/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/core/components/ui/select";
import { BookOpen, Clock, Award, Star, Users, Filter, Search, PlayCircle, FileText, Download } from "lucide-react";
import { TrainingAnalytics } from "@/core/components/dashboard/training-analytics";
import type { Course, Enrollment, Resource } from "@shared/schema";

const DIFFICULTY_COLORS = {
  beginner: "bg-green-100 text-green-800",
  intermediate: "bg-yellow-100 text-yellow-800", 
  advanced: "bg-red-100 text-red-800"
};

const CATEGORY_COLORS = {
  technical: "bg-blue-100 text-blue-800",
  compliance: "bg-purple-100 text-purple-800",
  "soft-skills": "bg-pink-100 text-pink-800",
  leadership: "bg-indigo-100 text-indigo-800"
};

export default function Training() {
  const [searchTerm, setSearchTerm] = useState("");
  const [categoryFilter, setCategoryFilter] = useState("all");
  const [difficultyFilter, setDifficultyFilter] = useState("all");
  const queryClient = useQueryClient();

  // Fetch courses
  const { data: courses = [], isLoading: coursesLoading } = useQuery({
    queryKey: ["/api/courses"]
  });

  // Fetch user enrollments  
  const { data: enrollments = [] } = useQuery<Enrollment[]>({
    queryKey: ["/api/my-enrollments"]
  });

  // Fetch resources
  const { data: resources = [] } = useQuery<Resource[]>({
    queryKey: ["/api/resources"]
  });

  // Fetch certificates
  const { data: certificates = [] } = useQuery({
    queryKey: ["/api/my-certificates"]
  });

  // Fetch training recommendations
  const { data: recommendations = [] } = useQuery({
    queryKey: ["/api/training-recommendations"]
  });

  // Enroll mutation
  const enrollMutation = useMutation({
    mutationFn: async (courseId: string) => {
      return apiRequest(`/api/enroll/${courseId}`, "POST");
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/my-enrollments"] });
    }
  });

  // Filter courses
  const filteredCourses = (courses as Course[]).filter((course: Course) => {
    const matchesSearch = course.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         course.description?.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = categoryFilter === "all" || course.category === categoryFilter;
    const matchesDifficulty = difficultyFilter === "all" || course.difficulty === difficultyFilter;
    
    return matchesSearch && matchesCategory && matchesDifficulty;
  });

  // Get enrollment status for a course
  const getEnrollmentStatus = (courseId: string) => {
    return enrollments.find(e => e.courseId === courseId);
  };

  // Handle enrollment
  const handleEnroll = (courseId: string) => {
    enrollMutation.mutate(courseId);
  };

  if (coursesLoading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-primary"></div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div className="container mx-auto px-4 py-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-4xl font-bold text-gray-900 dark:text-white mb-2">
            🎓 Centre de Formation
          </h1>
          <p className="text-lg text-gray-600 dark:text-gray-300">
            Développez vos compétences avec nos modules de formation interactifs
          </p>
        </div>

        <Tabs defaultValue="my-learning" className="space-y-6">
          <TabsList className="grid w-full grid-cols-4 lg:w-[400px]">
            <TabsTrigger value="my-learning">Mon Apprentissage</TabsTrigger>
            <TabsTrigger value="courses">Cours</TabsTrigger>
            <TabsTrigger value="resources">Ressources</TabsTrigger>
            <TabsTrigger value="certificates">Certificats</TabsTrigger>
          </TabsList>

          {/* My Learning Tab - Enhanced Analytics Dashboard */}
          <TabsContent value="my-learning" className="space-y-6">
            <TrainingAnalytics />
          </TabsContent>

          {/* Courses Tab */}
          <TabsContent value="courses" className="space-y-6">
            {/* Search and Filters */}
            <div className="flex flex-col md:flex-row gap-4 p-6 glass-card rounded-2xl border-white/20">
              <div className="flex-1">
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                  <Input
                    placeholder="Rechercher des cours..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="pl-10 bg-white/50 border-white/30"
                    data-testid="input-search-courses"
                  />
                </div>
              </div>
              <Select value={categoryFilter} onValueChange={setCategoryFilter}>
                <SelectTrigger className="w-full md:w-48 bg-white/50 border-white/30">
                  <SelectValue placeholder="Catégorie" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Toutes catégories</SelectItem>
                  <SelectItem value="technical">Technique</SelectItem>
                  <SelectItem value="compliance">Conformité</SelectItem>
                  <SelectItem value="soft-skills">Soft Skills</SelectItem>
                  <SelectItem value="leadership">Leadership</SelectItem>
                </SelectContent>
              </Select>
              <Select value={difficultyFilter} onValueChange={setDifficultyFilter}>
                <SelectTrigger className="w-full md:w-48 bg-white/50 border-white/30">
                  <SelectValue placeholder="Difficulté" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Tous niveaux</SelectItem>
                  <SelectItem value="beginner">Débutant</SelectItem>
                  <SelectItem value="intermediate">Intermédiaire</SelectItem>
                  <SelectItem value="advanced">Avancé</SelectItem>
                </SelectContent>
              </Select>
            </div>

            {/* Recommended Courses */}
            {(recommendations as Course[]).length > 0 && (
              <div className="space-y-4">
                <h3 className="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                  <Star className="h-5 w-5 text-yellow-500" />
                  Recommandés pour vous
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {(recommendations as Course[]).slice(0, 3).map((course: Course) => (
                    <CourseCard 
                      key={course.id} 
                      course={course} 
                      enrollment={getEnrollmentStatus(course.id)}
                      onEnroll={handleEnroll}
                      isRecommended={true}
                    />
                  ))}
                </div>
              </div>
            )}

            {/* All Courses */}
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <h3 className="text-xl font-semibold text-gray-900 dark:text-white">
                  Tous les cours ({filteredCourses.length})
                </h3>
                <Button variant="outline" size="sm" className="gap-2">
                  <Filter className="h-4 w-4" />
                  Filtres avancés
                </Button>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {filteredCourses.map((course) => (
                  <CourseCard 
                    key={course.id} 
                    course={course} 
                    enrollment={getEnrollmentStatus(course.id)}
                    onEnroll={handleEnroll}
                  />
                ))}
              </div>

              {filteredCourses.length === 0 && (
                <div className="text-center py-12" data-testid="text-no-courses">
                  <BookOpen className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
                  <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Aucun cours trouvé
                  </h3>
                  <p className="text-muted-foreground">
                    Essayez d'ajuster vos filtres de recherche
                  </p>
                </div>
              )}
            </div>
          </TabsContent>

          {/* Resources Tab */}
          <TabsContent value="resources" className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {resources.map((resource: Resource) => (
                <Card key={resource.id} className="glass-card border-white/20 hover:shadow-lg transition-all duration-300">
                  <CardHeader>
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <CardTitle className="text-lg font-semibold">{resource.title}</CardTitle>
                        <CardDescription className="mt-1">
                          {resource.description}
                        </CardDescription>
                      </div>
                      <Badge variant="outline" className="ml-2">
                        {resource.type}
                      </Badge>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-4 text-sm text-muted-foreground">
                        <span className="flex items-center gap-1">
                          <FileText className="h-4 w-4" />
                          {resource.fileType || 'PDF'}
                        </span>
                        <span className="flex items-center gap-1">
                          <Download className="h-4 w-4" />
                          {resource.downloadCount || 0}
                        </span>
                      </div>
                      <Button size="sm" className="gap-2">
                        <Download className="h-4 w-4" />
                        Télécharger
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>

          {/* Certificates Tab */}
          <TabsContent value="certificates" className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {(certificates as any[]).map((cert: any) => (
                <Card key={cert.id} className="glass-card border-white/20 hover:shadow-lg transition-all duration-300">
                  <CardHeader>
                    <div className="flex items-center justify-between">
                      <Award className="h-8 w-8 text-yellow-500" />
                      <Badge variant="secondary">Certificat</Badge>
                    </div>
                    <CardTitle className="text-lg">{cert.courseTitle}</CardTitle>
                    <CardDescription>
                      Complété le {new Date(cert.completedAt).toLocaleDateString('fr-FR')}
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-4">
                      <div className="flex items-center justify-between">
                        <span className="text-sm font-medium">Score obtenu</span>
                        <Badge variant="default" className="bg-green-100 text-green-800">
                          {cert.score}%
                        </Badge>
                      </div>
                      <Button className="w-full gap-2">
                        <Download className="h-4 w-4" />
                        Télécharger le certificat
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>

            {(certificates as any[]).length === 0 && (
              <div className="text-center py-12">
                <Award className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
                <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                  Aucun certificat
                </h3>
                <p className="text-muted-foreground">
                  Complétez des cours pour obtenir vos premiers certificats
                </p>
              </div>
            )}
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
}

// Course Card Component
interface CourseCardProps {
  course: Course;
  enrollment?: any;
  onEnroll: (courseId: string) => void;
  isRecommended?: boolean;
}

function CourseCard({ course, enrollment, onEnroll, isRecommended = false }: CourseCardProps) {
  const isEnrolled = !!enrollment;
  
  return (
    <Card className={`glass-card border-white/20 hover:shadow-lg transition-all duration-300 ${isRecommended ? 'ring-2 ring-yellow-400/50' : ''}`}>
      <div className="aspect-video bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center relative">
        {course.thumbnailUrl ? (
          <img
            src={course.thumbnailUrl}
            alt={course.title}
            className="w-full h-full object-cover"
          />
        ) : (
          <BookOpen className="h-16 w-16 text-primary/50" />
        )}
        {isRecommended && (
          <Badge className="absolute top-2 right-2 bg-yellow-500 hover:bg-yellow-500">
            <Star className="h-3 w-3 mr-1" />
            Recommandé
          </Badge>
        )}
      </div>
      
      <CardHeader className="space-y-3">
        <div className="flex items-start justify-between">
          <CardTitle className="text-lg font-semibold">{course.title}</CardTitle>
          {course.isMandatory && (
            <Badge variant="destructive" className="text-xs">
              Obligatoire
            </Badge>
          )}
        </div>
        
        <CardDescription className="line-clamp-2">
          {course.description}
        </CardDescription>
        
        <div className="flex flex-wrap gap-2">
          <Badge className={CATEGORY_COLORS[course.category as keyof typeof CATEGORY_COLORS] || "bg-gray-100 text-gray-800"}>
            {course.category}
          </Badge>
          <Badge className={DIFFICULTY_COLORS[course.difficulty as keyof typeof DIFFICULTY_COLORS]}>
            {course.difficulty}
          </Badge>
        </div>
      </CardHeader>
      
      <CardContent className="space-y-4">
        <div className="flex items-center justify-between text-sm text-muted-foreground">
          <div className="flex items-center gap-1">
            <Clock className="h-4 w-4" />
            {course.duration} min
          </div>
          <div className="flex items-center gap-1">
            <Users className="h-4 w-4" />
            {course.authorName}
          </div>
        </div>
        
        {isEnrolled ? (
          <div className="space-y-2">
            <div className="flex items-center justify-between text-sm">
              <span>Progression</span>
              <span>{enrollment.progress || 0}%</span>
            </div>
            <Progress value={enrollment.progress || 0} className="h-2" />
            <Button 
              className="w-full" 
              variant={enrollment.status === "completed" ? "secondary" : "default"}
            >
              {enrollment.status === "completed" ? (
                <>
                  <Award className="mr-2 h-4 w-4" />
                  Terminé
                </>
              ) : (
                <>
                  <PlayCircle className="mr-2 h-4 w-4" />
                  Continuer
                </>
              )}
            </Button>
          </div>
        ) : (
          <Button
            onClick={() => onEnroll(course.id)}
            className="w-full"
          >
            <BookOpen className="mr-2 h-4 w-4" />
            S'inscrire
          </Button>
        )}
      </CardContent>
    </Card>
  );
}