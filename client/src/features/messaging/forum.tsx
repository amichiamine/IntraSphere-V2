import { useState } from "react";
import { Link, useLocation } from "wouter";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { apiRequest } from "@/core/lib/queryClient";
import { Button } from "@/core/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/core/components/ui/card";
import { Badge } from "@/core/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/core/components/ui/avatar";
import { Input } from "@/core/components/ui/input";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/core/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/core/components/ui/select";
import { 
  MessageCircle, 
  Pin, 
  Users, 
  Eye, 
  Search,
  Plus,
  Clock,
  TrendingUp,
  Heart,
  Star,
  Filter,
  ThumbsUp,
  Flame,
  Calendar,
  BarChart3
} from "lucide-react";
import type { ForumCategory, ForumTopic } from "@shared/schema";

export function ForumPage() {
  const [, navigate] = useLocation();
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [sortBy, setSortBy] = useState("recent");
  const [filterBy, setFilterBy] = useState("all");
  const queryClient = useQueryClient();

  // Fetch forum categories
  const { data: categories = [], isLoading: categoriesLoading } = useQuery<ForumCategory[]>({
    queryKey: ['/api/forum/categories'],
  });

  // Fetch forum topics (filtered by category if selected)
  const { data: topics = [], isLoading: topicsLoading } = useQuery<ForumTopic[]>({
    queryKey: ['/api/forum/topics', selectedCategory],
    queryFn: async () => {
      const url = selectedCategory 
        ? `/api/forum/topics?categoryId=${selectedCategory}`
        : '/api/forum/topics';
      const response = await fetch(url);
      if (!response.ok) throw new Error('Failed to fetch topics');
      return response.json();
    }
  });

  // Fetch current user stats
  const { data: userStats } = useQuery<any>({
    queryKey: ['/api/forum/stats/me'],
  });

  // Like topic mutation
  const likeMutation = useMutation({
    mutationFn: async ({ topicId, reactionType }: { topicId: string, reactionType?: string }) => {
      return apiRequest(`/api/forum/posts/${topicId}/like`, "POST", { reactionType });
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/forum/topics'] });
    }
  });

  const filteredTopics = searchQuery
    ? topics.filter(topic => 
        topic.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        topic.description?.toLowerCase().includes(searchQuery.toLowerCase())
      )
    : topics;

  const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
  };

  const formatDate = (date: Date | string | null) => {
    if (!date) return "Non défini";
    return new Intl.DateTimeFormat('fr-FR', {
      day: 'numeric',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit'
    }).format(new Date(date));
  };

  const formatNumber = (num: number) => {
    return num.toLocaleString('fr-FR');
  };

  if (categoriesLoading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-8 w-64 bg-gray-200 dark:bg-gray-700 rounded"></div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {[...Array(6)].map((_, i) => (
              <div key={i} className="h-32 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            ))}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="p-6 space-y-6" data-testid="forum-page">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
            Forum IntraSphere
          </h1>
          <p className="text-gray-600 dark:text-gray-400 mt-1">
            Échangez et collaborez avec vos collègues
          </p>
        </div>
        
        <div className="flex items-center gap-3">
          {userStats && (
            <div className="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
              <div className="flex items-center gap-1">
                <MessageCircle className="h-4 w-4" />
                <span>{userStats.postCount || 0} posts</span>
              </div>
              <div className="flex items-center gap-1">
                <TrendingUp className="h-4 w-4" />
                <span>{userStats.reputationScore || 0} points</span>
              </div>
            </div>
          )}
          
          <Button 
            onClick={() => navigate('/forum/new-topic')}
            className="bg-purple-600 hover:bg-purple-700"
            data-testid="button-new-topic"
          >
            <Plus className="h-4 w-4 mr-2" />
            Nouveau sujet
          </Button>
        </div>
      </div>

      {/* Search and filters */}
      <div className="flex items-center justify-between gap-4">
        <div className="flex-1 max-w-md">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
            <Input
              placeholder="Rechercher dans les discussions..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10"
              data-testid="input-search-topics"
            />
          </div>
        </div>

        <div className="flex gap-2 overflow-x-auto">
          <Button
            variant={!selectedCategory ? "default" : "outline"}
            size="sm"
            onClick={() => setSelectedCategory(null)}
            data-testid="filter-all-categories"
          >
            Toutes
          </Button>
          {categories.map((category) => (
            <Button
              key={category.id}
              variant={selectedCategory === category.id ? "default" : "outline"}
              size="sm"
              onClick={() => setSelectedCategory(category.id)}
              className="whitespace-nowrap"
              data-testid={`filter-category-${category.id}`}
            >
              <span className="mr-1">{category.icon}</span>
              {category.name}
            </Button>
          ))}
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {/* Categories sidebar */}
        <div className="lg:col-span-1">
          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Catégories</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              {categories.map((category) => {
                const categoryTopics = topics.filter(t => t.categoryId === category.id);
                const totalPosts = categoryTopics.reduce((sum, topic) => sum + (topic.replyCount || 0), 0);
                
                return (
                  <button
                    key={category.id}
                    onClick={() => setSelectedCategory(
                      selectedCategory === category.id ? null : category.id
                    )}
                    className={`w-full p-3 rounded-lg text-left transition-colors ${
                      selectedCategory === category.id
                        ? 'bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500'
                        : 'hover:bg-gray-50 dark:hover:bg-gray-800'
                    }`}
                    data-testid={`category-${category.id}`}
                  >
                    <div className="flex items-start justify-between">
                      <div className="flex items-center gap-2">
                        <span className="text-lg">{category.icon}</span>
                        <div>
                          <div className="font-medium text-gray-900 dark:text-white">
                            {category.name}
                          </div>
                          <div className="text-xs text-gray-500 dark:text-gray-400">
                            {category.description}
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div className="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                      <span>{categoryTopics.length} sujets</span>
                      <span>{totalPosts} posts</span>
                    </div>
                  </button>
                );
              })}
            </CardContent>
          </Card>
        </div>

        {/* Topics list */}
        <div className="lg:col-span-3">
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle className="text-lg">
                  {selectedCategory 
                    ? categories.find(c => c.id === selectedCategory)?.name
                    : 'Toutes les discussions'
                  }
                </CardTitle>
                <Badge variant="secondary" data-testid="topics-count">
                  {formatNumber(filteredTopics.length)} sujets
                </Badge>
              </div>
            </CardHeader>
            <CardContent>
              {topicsLoading ? (
                <div className="space-y-4">
                  {[...Array(5)].map((_, i) => (
                    <div key={i} className="animate-pulse">
                      <div className="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    </div>
                  ))}
                </div>
              ) : filteredTopics.length === 0 ? (
                <div className="text-center py-12 text-gray-500 dark:text-gray-400">
                  <MessageCircle className="h-12 w-12 mx-auto mb-4 opacity-50" />
                  <p className="text-lg font-medium mb-2">Aucun sujet trouvé</p>
                  <p className="text-sm">
                    {searchQuery
                      ? `Aucun résultat pour "${searchQuery}"`
                      : 'Soyez le premier à créer un sujet dans cette catégorie !'
                    }
                  </p>
                </div>
              ) : (
                <div className="space-y-3">
                  {filteredTopics.map((topic) => {
                    const category = categories.find(c => c.id === topic.categoryId);
                    
                    return (
                      <div
                        key={topic.id}
                        className="group p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer"
                        onClick={() => navigate(`/forum/topic/${topic.id}`)}
                        data-testid={`topic-${topic.id}`}
                      >
                        <div className="flex items-start gap-3">
                          {/* Topic icon and status */}
                          <div className="flex flex-col items-center gap-1 mt-1">
                            {topic.isPinned && (
                              <Pin className="h-4 w-4 text-purple-500" />
                            )}
                            {topic.isAnnouncement ? (
                              <div className="w-2 h-2 bg-red-500 rounded-full"></div>
                            ) : (
                              <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
                            )}
                          </div>

                          <div className="flex-1 min-w-0">
                            <div className="flex items-start justify-between gap-2">
                              <div className="flex-1 min-w-0">
                                <h3 className="font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                  {topic.title}
                                  {topic.isPinned && (
                                    <Pin className="inline h-4 w-4 ml-2 text-purple-500" />
                                  )}
                                  {topic.isAnnouncement && (
                                    <Badge variant="destructive" className="ml-2 text-xs">
                                      Annonce
                                    </Badge>
                                  )}
                                </h3>
                                {topic.description && (
                                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-1 truncate">
                                    {topic.description}
                                  </p>
                                )}
                                
                                <div className="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                  <div className="flex items-center gap-1">
                                    <Avatar className="h-4 w-4">
                                      <AvatarFallback className="text-xs">
                                        {getInitials(topic.authorName)}
                                      </AvatarFallback>
                                    </Avatar>
                                    <span>{topic.authorName}</span>
                                  </div>
                                  
                                  {category && (
                                    <Badge variant="outline" className="text-xs">
                                      <span className="mr-1">{category.icon}</span>
                                      {category.name}
                                    </Badge>
                                  )}
                                  
                                  <div className="flex items-center gap-1">
                                    <Clock className="h-3 w-3" />
                                    <span>{formatDate(topic.createdAt)}</span>
                                  </div>
                                </div>
                              </div>
                              
                              <div className="flex flex-col items-end gap-2">
                                <div className="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                  <div className="flex items-center gap-1">
                                    <MessageCircle className="h-3 w-3" />
                                    <span>{topic.replyCount || 0}</span>
                                  </div>
                                  <div className="flex items-center gap-1">
                                    <Eye className="h-3 w-3" />
                                    <span>{formatNumber(topic.viewCount || 0)}</span>
                                  </div>
                                  <div className="flex items-center gap-1">
                                    <Heart className="h-3 w-3" />
                                    <span>{topic.replyCount || 0}</span>
                                  </div>
                                </div>
                                
                                {/* Quick action buttons */}
                                <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                  <Button
                                    size="sm"
                                    variant="ghost"
                                    className="h-6 px-2 text-xs"
                                    onClick={(e) => {
                                      e.stopPropagation();
                                      likeMutation.mutate({ topicId: topic.id, reactionType: "like" });
                                    }}
                                  >
                                    <ThumbsUp className="h-3 w-3" />
                                  </Button>
                                  <Button
                                    size="sm"
                                    variant="ghost"
                                    className="h-6 px-2 text-xs"
                                    onClick={(e) => {
                                      e.stopPropagation();
                                      navigate(`/forum/topic/${topic.id}`);
                                    }}
                                  >
                                    <MessageCircle className="h-3 w-3" />
                                  </Button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </div>
      
      {/* Forum Statistics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                <MessageCircle className="h-5 w-5 text-blue-600 dark:text-blue-400" />
              </div>
              <div>
                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                  {formatNumber(topics.length)}
                </div>
                <div className="text-sm text-gray-600 dark:text-gray-400">
                  Sujets de discussion
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                <Users className="h-5 w-5 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                  {formatNumber(categories.length)}
                </div>
                <div className="text-sm text-gray-600 dark:text-gray-400">
                  Catégories actives
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                <TrendingUp className="h-5 w-5 text-purple-600 dark:text-purple-400" />
              </div>
              <div>
                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                  {formatNumber(topics.reduce((sum, topic) => sum + (topic.viewCount || 0), 0))}
                </div>
                <div className="text-sm text-gray-600 dark:text-gray-400">
                  Vues totales
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                <Flame className="h-5 w-5 text-orange-600 dark:text-orange-400" />
              </div>
              <div>
                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                  {userStats?.reputationScore || 0}
                </div>
                <div className="text-sm text-gray-600 dark:text-gray-400">
                  Votre réputation
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}