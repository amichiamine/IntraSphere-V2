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
import { Progress } from "@/core/components/ui/progress";
import { useToast } from "@/core/hooks/use-toast";
import { 
  Search,
  Filter,
  Star,
  Download,
  Eye,
  Heart,
  Clock,
  FileText,
  Video,
  Link as LinkIcon,
  Image,
  TrendingUp,
  BookOpen,
  Users,
  Calendar,
  Tag,
  Share2,
  MessageSquare
} from "lucide-react";
import type { Content } from "@shared/schema";

export function AdvancedContentPage() {
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("all");
  const [sortBy, setSortBy] = useState("recent");
  const [viewMode, setViewMode] = useState("grid");
  const queryClient = useQueryClient();
  const { toast } = useToast();

  // Fetch all content with enhanced data
  const { data: contents = [], isLoading } = useQuery<Content[]>({
    queryKey: ['/api/content'],
  });

  // Fetch content categories
  const { data: categories = [] } = useQuery<any[]>({
    queryKey: ['/api/content/categories'],
  });

  // Like content mutation
  const likeMutation = useMutation({
    mutationFn: async (contentId: string) => {
      return apiRequest(`/api/content/${contentId}/like`, "POST");
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/content'] });
      toast({
        title: "Like ajouté",
        description: "Votre appréciation a été enregistrée"
      });
    }
  });

  // Download content mutation
  const downloadMutation = useMutation({
    mutationFn: async (contentId: string) => {
      return apiRequest(`/api/content/${contentId}/download`, "POST");
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/content'] });
    }
  });

  // Share content mutation
  const shareMutation = useMutation({
    mutationFn: async (contentId: string) => {
      return apiRequest(`/api/content/${contentId}/share`, "POST");
    },
    onSuccess: () => {
      toast({
        title: "Contenu partagé",
        description: "Le lien de partage a été copié dans le presse-papiers"
      });
    }
  });

  const filteredContents = contents.filter(content => {
    const matchesSearch = content.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         content.description?.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCategory = selectedCategory === "all" || content.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  const sortedContents = [...filteredContents].sort((a, b) => {
    switch (sortBy) {
      case "recent":
        return new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime();
      case "popular":
        return (b.viewCount || 0) - (a.viewCount || 0);
      case "rating":
        return (b.rating || 0) - (a.rating || 0);
      case "downloads":
        return (b.downloadCount || 0) - (a.downloadCount || 0);
      default:
        return 0;
    }
  });

  const getContentIcon = (type: string) => {
    switch (type) {
      case "document": return FileText;
      case "video": return Video;
      case "link": return LinkIcon;
      case "image": return Image;
      default: return FileText;
    }
  };

  const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
  };

  const formatDate = (date: Date | string) => {
    return new Intl.DateTimeFormat('fr-FR', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    }).format(new Date(date));
  };

  const formatNumber = (num: number) => {
    return num.toLocaleString('fr-FR');
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
    <div className="p-6 space-y-6" data-testid="advanced-content-page">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
            Bibliothèque de Contenu
          </h1>
          <p className="text-gray-600 dark:text-gray-400 mt-1">
            Découvrez et explorez tous les contenus de l'entreprise
          </p>
        </div>
        
        <div className="flex items-center gap-3">
          <Badge variant="outline" className="text-sm">
            {formatNumber(filteredContents.length)} contenus
          </Badge>
          
          <Select value={viewMode} onValueChange={setViewMode}>
            <SelectTrigger className="w-32">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="grid">Grille</SelectItem>
              <SelectItem value="list">Liste</SelectItem>
              <SelectItem value="compact">Compact</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      {/* Filters */}
      <div className="flex flex-col md:flex-row gap-4">
        <div className="flex-1">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
            <Input
              placeholder="Rechercher du contenu..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10"
              data-testid="input-search-content"
            />
          </div>
        </div>
        
        <div className="flex gap-2">
          <Select value={selectedCategory} onValueChange={setSelectedCategory}>
            <SelectTrigger className="w-40">
              <SelectValue placeholder="Catégorie" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Toutes catégories</SelectItem>
              {categories.map((category) => (
                <SelectItem key={category.id} value={category.id}>
                  {category.name}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          
          <Select value={sortBy} onValueChange={setSortBy}>
            <SelectTrigger className="w-40">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="recent">Plus récent</SelectItem>
              <SelectItem value="popular">Plus populaire</SelectItem>
              <SelectItem value="rating">Mieux noté</SelectItem>
              <SelectItem value="downloads">Plus téléchargé</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <Tabs defaultValue="content" className="space-y-4">
        <TabsList>
          <TabsTrigger value="content">Contenu</TabsTrigger>
          <TabsTrigger value="trending">Tendances</TabsTrigger>
          <TabsTrigger value="recent">Récent</TabsTrigger>
          <TabsTrigger value="favorites">Favoris</TabsTrigger>
        </TabsList>

        <TabsContent value="content" className="space-y-4">
          {/* Content Grid/List */}
          <div className={`grid gap-6 ${
            viewMode === "grid" ? "grid-cols-1 md:grid-cols-2 lg:grid-cols-3" :
            viewMode === "list" ? "grid-cols-1" :
            "grid-cols-1 md:grid-cols-2"
          }`}>
            {sortedContents.map((content) => {
              const IconComponent = getContentIcon(content.type);
              
              return (
                <Card 
                  key={content.id} 
                  className="group hover:shadow-lg transition-all duration-300 cursor-pointer"
                  data-testid={`content-${content.id}`}
                >
                  <CardHeader className="pb-3">
                    <div className="flex items-start justify-between">
                      <div className="flex items-center gap-3">
                        <div className="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                          <IconComponent className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div className="flex-1 min-w-0">
                          <CardTitle className="text-lg font-semibold text-gray-900 dark:text-white truncate">
                            {content.title}
                          </CardTitle>
                          <Badge variant="outline" className="mt-1 text-xs">
                            {content.category}
                          </Badge>
                        </div>
                      </div>
                      
                      <div className="flex items-center gap-1">
                        <Star className={`h-4 w-4 ${content.rating ? 'text-yellow-500 fill-yellow-500' : 'text-gray-300'}`} />
                        <span className="text-sm text-gray-600 dark:text-gray-400">
                          {content.rating ? content.rating.toFixed(1) : '0.0'}
                        </span>
                      </div>
                    </div>
                  </CardHeader>
                  
                  <CardContent className="space-y-4">
                    {content.description && (
                      <p className="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                        {content.description}
                      </p>
                    )}
                    
                    {/* Author and date */}
                    <div className="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                      <Avatar className="h-6 w-6">
                        <AvatarFallback className="text-xs">
                          {getInitials(content.authorName)}
                        </AvatarFallback>
                      </Avatar>
                      <span>{content.authorName}</span>
                      <span>•</span>
                      <Clock className="h-3 w-3" />
                      <span>{formatDate(content.createdAt)}</span>
                    </div>
                    
                    {/* Engagement metrics */}
                    <div className="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                      <div className="flex items-center gap-4">
                        <div className="flex items-center gap-1">
                          <Eye className="h-4 w-4" />
                          <span>{formatNumber(content.viewCount || 0)}</span>
                        </div>
                        <div className="flex items-center gap-1">
                          <Download className="h-4 w-4" />
                          <span>{formatNumber(content.downloadCount || 0)}</span>
                        </div>
                        <div className="flex items-center gap-1">
                          <Heart className="h-4 w-4" />
                          <span>{formatNumber(content.likesCount || 0)}</span>
                        </div>
                      </div>
                      
                      {/* Quick actions */}
                      <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <Button
                          size="sm"
                          variant="ghost"
                          className="h-8 w-8 p-0"
                          onClick={(e) => {
                            e.stopPropagation();
                            likeMutation.mutate(content.id);
                          }}
                        >
                          <Heart className="h-4 w-4" />
                        </Button>
                        <Button
                          size="sm"
                          variant="ghost"
                          className="h-8 w-8 p-0"
                          onClick={(e) => {
                            e.stopPropagation();
                            downloadMutation.mutate(content.id);
                          }}
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                        <Button
                          size="sm"
                          variant="ghost"
                          className="h-8 w-8 p-0"
                          onClick={(e) => {
                            e.stopPropagation();
                            shareMutation.mutate(content.id);
                          }}
                        >
                          <Share2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                    
                    {/* Engagement progress bar */}
                    {content.rating && (
                      <div className="space-y-1">
                        <div className="flex justify-between text-xs text-gray-500">
                          <span>Engagement</span>
                          <span>{Math.round((content.rating / 5) * 100)}%</span>
                        </div>
                        <Progress value={(content.rating / 5) * 100} className="h-1" />
                      </div>
                    )}
                  </CardContent>
                </Card>
              );
            })}
          </div>
          
          {sortedContents.length === 0 && (
            <div className="text-center py-12 text-gray-500 dark:text-gray-400">
              <FileText className="h-12 w-12 mx-auto mb-4 opacity-50" />
              <p className="text-lg font-medium mb-2">Aucun contenu trouvé</p>
              <p className="text-sm">
                {searchQuery || selectedCategory !== "all"
                  ? "Essayez de modifier vos filtres de recherche"
                  : "Aucun contenu disponible pour le moment"
                }
              </p>
            </div>
          )}
        </TabsContent>

        <TabsContent value="trending" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <TrendingUp className="h-5 w-5" />
                Contenu en tendance
              </CardTitle>
              <CardDescription>
                Les contenus les plus populaires cette semaine
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {sortedContents
                  .filter(content => (content.viewCount || 0) > 50)
                  .slice(0, 5)
                  .map((content, index) => (
                    <div key={content.id} className="flex items-center gap-4 p-3 rounded-lg border">
                      <Badge variant="outline" className="w-8 h-8 rounded-full flex items-center justify-center">
                        {index + 1}
                      </Badge>
                      <div className="flex-1">
                        <div className="font-medium text-gray-900 dark:text-white">
                          {content.title}
                        </div>
                        <div className="text-sm text-gray-500 dark:text-gray-400">
                          {formatNumber(content.viewCount || 0)} vues • {content.authorName}
                        </div>
                      </div>
                      <div className="text-right">
                        <div className="flex items-center gap-1 text-yellow-500">
                          <Star className="h-4 w-4 fill-current" />
                          <span className="text-sm">{(content.rating || 0).toFixed(1)}</span>
                        </div>
                      </div>
                    </div>
                  ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="recent" className="space-y-4">
          <div className="grid grid-cols-1 gap-4">
            {sortedContents.slice(0, 10).map((content) => (
              <Card key={content.id} className="p-4">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div className="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                      {(() => {
                        const IconComponent = getContentIcon(content.type);
                        return <IconComponent className="h-4 w-4 text-green-600 dark:text-green-400" />;
                      })()}
                    </div>
                    <div>
                      <div className="font-medium text-gray-900 dark:text-white">
                        {content.title}
                      </div>
                      <div className="text-sm text-gray-500 dark:text-gray-400">
                        {content.authorName} • {formatDate(content.createdAt)}
                      </div>
                    </div>
                  </div>
                  <Badge variant="outline">{content.category}</Badge>
                </div>
              </Card>
            ))}
          </div>
        </TabsContent>

        <TabsContent value="favorites" className="space-y-4">
          <Card>
            <CardContent className="p-12 text-center">
              <Heart className="h-12 w-12 mx-auto mb-4 opacity-50 text-gray-400" />
              <p className="text-lg font-medium mb-2 text-gray-500">Aucun favori</p>
              <p className="text-sm text-gray-400">
                Ajoutez des contenus à vos favoris en cliquant sur le cœur
              </p>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}