import { useState, useEffect } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { MainLayout } from "@/core/components/layout/main-layout";
import { GlassCard } from "@/core/components/ui/glass-card";
import { Button } from "@/core/components/ui/button";
import { Switch } from "@/core/components/ui/switch";
import { Input } from "@/core/components/ui/input";
import { Label } from "@/core/components/ui/label";
import { Badge } from "@/core/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/core/components/ui/tabs";
import { useToast } from "@/core/hooks/use-toast";
import { 
  Eye, 
  EyeOff, 
  Settings, 
  Users, 
  FileText, 
  Calendar, 
  MessageCircle,
  AlertTriangle,
  Layout,
  Palette,
  Save,
  RotateCcw,
  Home,
  FolderOpen,
  Shield,
  ArrowUp,
  ArrowDown,
  Edit3,
  Trash2,
  ChevronDown,
  Check
} from "lucide-react";

interface ViewConfig {
  id: string;
  name: string;
  description: string;
  icon: string;
  isVisible: boolean;
  sortOrder: number;
  color: string;
  isCore: boolean;
  accessLevel: 'all' | 'employee' | 'moderator' | 'admin';
  category: 'main' | 'communication' | 'management' | 'tools';
}

const iconMap = {
  Layout: Layout,
  AlertTriangle: AlertTriangle,
  FileText: FileText,
  Users: Users,
  MessageCircle: MessageCircle,
  Calendar: Calendar,
  Home: Home,
  FolderOpen: FolderOpen,
  Shield: Shield
};

const accessLabels = {
  all: "Tous les utilisateurs",
  employee: "Employés et plus",
  moderator: "Modérateurs et plus", 
  admin: "Administrateurs uniquement"
};

// Custom Select Component for Access Level
const AccessLevelSelect = ({ 
  value, 
  onChange, 
  disabled = false, 
  viewId 
}: { 
  value: ViewConfig['accessLevel'], 
  onChange: (value: ViewConfig['accessLevel']) => void, 
  disabled?: boolean,
  viewId: string 
}) => {
  const [isOpen, setIsOpen] = useState(false);

  const handleSelect = (accessLevel: ViewConfig['accessLevel']) => {
    if (!disabled) {
      onChange(accessLevel);
      setIsOpen(false);
    }
  };

  return (
    <div className="relative">
      <button
        type="button"
        onClick={() => !disabled && setIsOpen(!isOpen)}
        className={`px-3 py-2 rounded-lg border text-sm bg-white transition-all min-w-[200px] text-left flex items-center justify-between ${
          disabled 
            ? 'border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed' 
            : 'border-gray-300 hover:border-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 cursor-pointer'
        } outline-none`}
        disabled={disabled}
      >
        <span>{accessLabels[value]}</span>
        <ChevronDown className={`w-4 h-4 transition-transform ${isOpen ? 'rotate-180' : ''} ${disabled ? 'text-gray-400' : 'text-gray-600'}`} />
      </button>

      {isOpen && !disabled && (
        <div className="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-50">
          {Object.entries(accessLabels).map(([accessValue, label]) => (
            <button
              key={accessValue}
              type="button"
              onClick={() => handleSelect(accessValue as ViewConfig['accessLevel'])}
              className={`w-full px-3 py-2 text-left text-sm hover:bg-purple-50 transition-colors flex items-center justify-between ${
                value === accessValue ? 'bg-purple-100 text-purple-700' : 'text-gray-700'
              } first:rounded-t-lg last:rounded-b-lg`}
            >
              <span>{label}</span>
              {value === accessValue && <Check className="w-4 h-4 text-purple-600" />}
            </button>
          ))}
        </div>
      )}
    </div>
  );
};

const categoryLabels = {
  main: "Sections principales",
  communication: "Communication",
  management: "Gestion",
  tools: "Outils"
};

const ViewsManagement = () => {
  const { toast } = useToast();
  const queryClient = useQueryClient();

  const [localViews, setLocalViews] = useState<ViewConfig[]>([]);
  const [hasChanges, setHasChanges] = useState(false);
  const [activeTab, setActiveTab] = useState("configuration");

  // Close dropdowns when clicking outside
  useEffect(() => {
    const handleClickOutside = () => {
      // This will help close any open dropdowns when clicking elsewhere
      const openDropdowns = document.querySelectorAll('[data-dropdown-open="true"]');
      openDropdowns.forEach(dropdown => {
        dropdown.removeAttribute('data-dropdown-open');
      });
    };

    document.addEventListener('click', handleClickOutside);
    return () => document.removeEventListener('click', handleClickOutside);
  }, []);

  // Fetch current view configuration
  const { data: viewsConfig, isLoading } = useQuery({
    queryKey: ['/api/views-config'],
    queryFn: async () => {
      const response = await fetch('/api/views-config');
      if (!response.ok) {
        throw new Error('Failed to fetch views configuration');
      }
      return response.json();
    }
  });

  // Initialize local state when data is loaded
  useEffect(() => {
    if (viewsConfig) {
      setLocalViews(viewsConfig);
      setHasChanges(false);
    }
  }, [viewsConfig]);

  // Save configuration mutation
  const saveConfigMutation = useMutation({
    mutationFn: async (views: ViewConfig[]) => {
      const response = await fetch('/api/views-config', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ views })
      });
      if (!response.ok) throw new Error('Failed to save configuration');
      return response.json();
    },
    onSuccess: () => {
      toast({
        title: "Configuration sauvegardée",
        description: "Les paramètres de vue ont été mis à jour avec succès.",
      });
      queryClient.invalidateQueries({ queryKey: ['/api/views-config'] });
      setHasChanges(false);
    },
    onError: () => {
      toast({
        title: "Erreur",
        description: "Impossible de sauvegarder la configuration.",
        variant: "destructive"
      });
    }
  });

  const toggleViewVisibility = (viewId: string) => {
    const updatedViews = localViews.map(view => 
      view.id === viewId 
        ? { ...view, isVisible: !view.isVisible }
        : view
    );
    setLocalViews(updatedViews);
    setHasChanges(true);
  };

  const updateViewOrder = (viewId: string, newOrder: number) => {
    const updatedViews = localViews.map(view => 
      view.id === viewId 
        ? { ...view, sortOrder: newOrder }
        : view
    );
    setLocalViews(updatedViews);
    setHasChanges(true);
  };

  const updateViewColor = (viewId: string, newColor: string) => {
    const updatedViews = localViews.map(view => 
      view.id === viewId 
        ? { ...view, color: newColor }
        : view
    );
    setLocalViews(updatedViews);
    setHasChanges(true);
  };

  const updateViewAccess = (viewId: string, accessLevel: ViewConfig['accessLevel']) => {
    const updatedViews = localViews.map(view => 
      view.id === viewId 
        ? { ...view, accessLevel }
        : view
    );
    setLocalViews(updatedViews);
    setHasChanges(true);
  };

  const moveView = (viewId: string, direction: 'up' | 'down') => {
    const sortedViews = [...localViews].sort((a, b) => a.sortOrder - b.sortOrder);
    const currentIndex = sortedViews.findIndex(v => v.id === viewId);
    
    if (direction === 'up' && currentIndex > 0) {
      // Swap orders between current and previous view
      const currentView = sortedViews[currentIndex];
      const previousView = sortedViews[currentIndex - 1];
      
      const updatedViews = localViews.map(view => {
        if (view.id === currentView.id) {
          return { ...view, sortOrder: previousView.sortOrder };
        } else if (view.id === previousView.id) {
          return { ...view, sortOrder: currentView.sortOrder };
        }
        return view;
      });
      
      setLocalViews(updatedViews);
      setHasChanges(true);
    } else if (direction === 'down' && currentIndex < sortedViews.length - 1) {
      // Swap orders between current and next view
      const currentView = sortedViews[currentIndex];
      const nextView = sortedViews[currentIndex + 1];
      
      const updatedViews = localViews.map(view => {
        if (view.id === currentView.id) {
          return { ...view, sortOrder: nextView.sortOrder };
        } else if (view.id === nextView.id) {
          return { ...view, sortOrder: currentView.sortOrder };
        }
        return view;
      });
      
      setLocalViews(updatedViews);
      setHasChanges(true);
    }
  };

  const saveConfiguration = () => {
    saveConfigMutation.mutate(localViews);
  };

  const resetToDefault = () => {
    // Reset to default configuration
    const defaultViews: ViewConfig[] = [
      {
        id: "dashboard",
        name: "Tableau de Bord",
        description: "Vue d'accueil avec statistiques et raccourcis",
        icon: "Home",
        isVisible: true,
        sortOrder: 1,
        color: "#3B82F6",
        isCore: true,
        accessLevel: "all",
        category: "main"
      },
      {
        id: "announcements", 
        name: "Annonces",
        description: "Communications officielles de l'entreprise",
        icon: "AlertTriangle",
        isVisible: true,
        sortOrder: 2,
        color: "#EF4444",
        isCore: true,
        accessLevel: "all",
        category: "communication"
      },
      {
        id: "documents",
        name: "Documents",
        description: "Bibliothèque de documents et règlements",
        icon: "FileText",
        isVisible: true,
        sortOrder: 3,
        color: "#10B981",
        isCore: true,
        accessLevel: "all",
        category: "main"
      },
      {
        id: "content",
        name: "Contenu",
        description: "Galerie multimédia et ressources de formation",
        icon: "FolderOpen",
        isVisible: true,
        sortOrder: 4,
        color: "#8B5CF6",
        isCore: false,
        accessLevel: "all",
        category: "main"
      },
      {
        id: "directory",
        name: "Annuaire",
        description: "Répertoire des employés et contacts",
        icon: "Users",
        isVisible: true,
        sortOrder: 5,
        color: "#F59E0B",
        isCore: false,
        accessLevel: "employee",
        category: "tools"
      },
      {
        id: "messages",
        name: "Messages",
        description: "Système de messagerie interne",
        icon: "MessageCircle",
        isVisible: true,
        sortOrder: 6,
        color: "#06B6D4",
        isCore: false,
        accessLevel: "employee",
        category: "communication"
      },
      {
        id: "complaints",
        name: "Réclamations",
        description: "Système de gestion des plaintes et suggestions",
        icon: "AlertTriangle",
        isVisible: false,
        sortOrder: 7,
        color: "#DC2626",
        isCore: false,
        accessLevel: "employee",
        category: "management"
      },
      {
        id: "administration",
        name: "Administration",
        description: "Panneau d'administration et gestion des utilisateurs",
        icon: "Shield",
        isVisible: true,
        sortOrder: 8,
        color: "#7C3AED",
        isCore: true,
        accessLevel: "admin",
        category: "management"
      }
    ];
    
    setLocalViews(defaultViews);
    setHasChanges(true);
    toast({
      title: "Configuration réinitialisée",
      description: "Les paramètres par défaut ont été restaurés. Pensez à sauvegarder.",
    });
  };

  if (isLoading) {
    return (
      <MainLayout>
        <div className="flex items-center justify-center h-64">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
            <p className="mt-4 text-gray-600">Chargement de la configuration...</p>
          </div>
        </div>
      </MainLayout>
    );
  }

  const visibleViews = localViews.filter(view => view.isVisible).sort((a, b) => a.sortOrder - b.sortOrder);
  const viewsByCategory = localViews.reduce((acc, view) => {
    acc[view.category] = acc[view.category] || [];
    acc[view.category].push(view);
    return acc;
  }, {} as Record<string, ViewConfig[]>);

  return (
    <MainLayout>
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-3xl font-bold" style={{
              background: `linear-gradient(to right, var(--color-primary, #8B5CF6), var(--color-secondary, #A78BFA))`,
              WebkitBackgroundClip: 'text',
              WebkitTextFillColor: 'transparent',
              backgroundClip: 'text'
            }}>
              Gestion des Vues
            </h1>
            <p className="text-gray-600 mt-2">
              Configurez l'affichage, l'ordre et les permissions des sections de votre intranet
            </p>
            {hasChanges && (
              <Badge className="mt-2 bg-orange-100 text-orange-800">
                Modifications non sauvegardées
              </Badge>
            )}
          </div>
          <div className="flex space-x-3">
            <Button variant="outline" onClick={resetToDefault}>
              <RotateCcw className="w-4 h-4 mr-2" />
              Réinitialiser
            </Button>
            <Button 
              onClick={saveConfiguration} 
              disabled={!hasChanges || saveConfigMutation.isPending}
              className="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700"
            >
              <Save className="w-4 h-4 mr-2" />
              {saveConfigMutation.isPending ? "Sauvegarde..." : "Sauvegarder"}
            </Button>
          </div>
        </div>

        <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-6">
          <TabsList className="grid w-full grid-cols-3">
            <TabsTrigger value="configuration">Configuration</TabsTrigger>
            <TabsTrigger value="layout">Disposition</TabsTrigger>
            <TabsTrigger value="permissions">Permissions</TabsTrigger>
          </TabsList>

          {/* Configuration Tab */}
          <TabsContent value="configuration" className="space-y-6">
            <GlassCard className="p-6">
              <div className="flex items-center space-x-3 mb-6">
                <Settings className="w-6 h-6 text-purple-600" />
                <h2 className="text-xl font-bold">Configuration des Sections</h2>
              </div>

              <div className="space-y-4">
                {localViews
                  .sort((a, b) => a.sortOrder - b.sortOrder)
                  .map((view) => {
                    const IconComponent = iconMap[view.icon as keyof typeof iconMap] || Layout;
                    return (
                      <div key={view.id} className="flex items-center justify-between p-4 bg-white/50 rounded-xl border border-white/20">
                        <div className="flex items-center space-x-4">
                          <div 
                            className="p-3 rounded-xl"
                            style={{ backgroundColor: `${view.color}20` }}
                          >
                            <IconComponent 
                              className="w-5 h-5" 
                              style={{ color: view.color }}
                            />
                          </div>
                          <div className="flex-1">
                            <div className="flex items-center space-x-3">
                              <h3 className="font-medium">{view.name}</h3>
                              <Badge 
                                variant={view.isVisible ? "default" : "secondary"}
                                className={view.isVisible ? "bg-green-100 text-green-800" : "bg-gray-100 text-gray-600"}
                              >
                                {view.isVisible ? "Visible" : "Masqué"}
                              </Badge>
                              {view.isCore && (
                                <Badge className="bg-blue-100 text-blue-800 text-xs">
                                  Section principale
                                </Badge>
                              )}
                            </div>
                            <p className="text-sm text-gray-600 mt-1">{view.description}</p>
                          </div>
                        </div>

                        <div className="flex items-center space-x-6">
                          {/* Order Control */}
                          <div className="flex items-center space-x-2">
                            <Label className="text-sm">Ordre:</Label>
                            <Input
                              type="number"
                              value={view.sortOrder}
                              onChange={(e) => updateViewOrder(view.id, parseInt(e.target.value))}
                              className="w-16 h-8"
                              min="1"
                              max="15"
                            />
                          </div>

                          {/* Color Picker */}
                          <div className="flex items-center space-x-2">
                            <Label className="text-sm">Couleur:</Label>
                            <input
                              type="color"
                              value={view.color}
                              onChange={(e) => updateViewColor(view.id, e.target.value)}
                              className="w-8 h-8 rounded border cursor-pointer"
                            />
                          </div>

                          {/* Visibility Toggle */}
                          <div className="flex items-center space-x-2">
                            <Label className="text-sm flex items-center space-x-1">
                              {view.isVisible ? <Eye className="w-4 h-4" /> : <EyeOff className="w-4 h-4" />}
                              <span>Visible</span>
                            </Label>
                            <Switch
                              checked={view.isVisible}
                              onCheckedChange={() => toggleViewVisibility(view.id)}
                              disabled={view.isCore && view.isVisible}
                            />
                          </div>
                        </div>
                      </div>
                    );
                  })}
              </div>
            </GlassCard>
          </TabsContent>

          {/* Layout Tab */}
          <TabsContent value="layout" className="space-y-6">
            <GlassCard className="p-6">
              <div className="flex items-center space-x-3 mb-6">
                <Layout className="w-6 h-6 text-purple-600" />
                <h2 className="text-xl font-bold">Disposition et Ordre</h2>
              </div>

              <div className="space-y-4">
                <h3 className="font-medium text-gray-700 border-b pb-2">Toutes les sections visibles</h3>
                <div className="space-y-2">
                  {visibleViews.map((view, index) => {
                    const IconComponent = iconMap[view.icon as keyof typeof iconMap] || Layout;
                    
                    return (
                      <div key={view.id} className="flex items-center justify-between p-3 bg-white/30 rounded-lg border border-white/20">
                        <div className="flex items-center space-x-3">
                          <span className="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-medium text-sm">
                            {index + 1}
                          </span>
                          <IconComponent 
                            className="w-5 h-5" 
                            style={{ color: view.color }}
                          />
                          <span className="font-medium">{view.name}</span>
                          <Badge className="bg-gray-100 text-gray-600 text-xs">
                            {categoryLabels[view.category]}
                          </Badge>
                          <Badge className={`text-xs ${
                            view.accessLevel === 'all' ? 'bg-green-100 text-green-800' : 
                            view.accessLevel === 'employee' ? 'bg-blue-100 text-blue-800' :
                            view.accessLevel === 'moderator' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-red-100 text-red-800'
                          }`}>
                            {accessLabels[view.accessLevel]}
                          </Badge>
                        </div>
                        <div className="flex items-center space-x-2">
                          <Button
                            size="sm"
                            variant="ghost"
                            onClick={() => moveView(view.id, 'up')}
                            disabled={index === 0}
                            className="hover:bg-purple-100 disabled:opacity-50"
                            title="Monter"
                          >
                            <ArrowUp className="w-4 h-4" />
                          </Button>
                          <Button
                            size="sm"
                            variant="ghost"
                            onClick={() => moveView(view.id, 'down')}
                            disabled={index === visibleViews.length - 1}
                            className="hover:bg-purple-100 disabled:opacity-50"
                            title="Descendre"
                          >
                            <ArrowDown className="w-4 h-4" />
                          </Button>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            </GlassCard>

            {/* Preview Section */}
            <GlassCard className="p-6">
              <div className="flex items-center space-x-3 mb-6">
                <Palette className="w-6 h-6 text-purple-600" />
                <h2 className="text-xl font-bold">Aperçu de la Navigation</h2>
              </div>

              <div className="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-6 border-2 border-dashed border-purple-200">
                <h3 className="font-medium mb-4 text-gray-700">Menu de Navigation (Aperçu)</h3>
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                  {visibleViews.map((view) => {
                    const IconComponent = iconMap[view.icon as keyof typeof iconMap] || Layout;
                    return (
                      <div
                        key={view.id}
                        className="flex items-center space-x-3 px-4 py-3 rounded-lg border border-white/30 hover:shadow-sm transition-shadow"
                        style={{ 
                          backgroundColor: `${view.color}15`,
                          borderColor: `${view.color}30`
                        }}
                      >
                        <IconComponent 
                          className="w-5 h-5" 
                          style={{ color: view.color }}
                        />
                        <div className="flex-1">
                          <span 
                            className="text-sm font-medium block"
                            style={{ color: view.color }}
                          >
                            {view.name}
                          </span>
                          <span className="text-xs text-gray-500">
                            Ordre: {view.sortOrder}
                          </span>
                        </div>
                      </div>
                    );
                  })}
                </div>
                
                {visibleViews.length === 0 && (
                  <p className="text-center text-gray-500 py-8">
                    Aucune section visible configurée
                  </p>
                )}
              </div>
            </GlassCard>
          </TabsContent>

          {/* Permissions Tab */}
          <TabsContent value="permissions" className="space-y-6">
            <GlassCard className="p-6">
              <div className="flex items-center space-x-3 mb-6">
                <Shield className="w-6 h-6 text-purple-600" />
                <h2 className="text-xl font-bold">Gestion des Permissions</h2>
              </div>

              <div className="space-y-4">
                {localViews
                  .sort((a, b) => a.sortOrder - b.sortOrder)
                  .map((view) => {
                    const IconComponent = iconMap[view.icon as keyof typeof iconMap] || Layout;
                    return (
                      <div key={view.id} className="flex items-center justify-between p-4 bg-white/50 rounded-xl border border-white/20 hover:bg-white/70 transition-colors">
                        <div className="flex items-center space-x-4">
                          <div 
                            className="p-3 rounded-xl"
                            style={{ backgroundColor: `${view.color}20` }}
                          >
                            <IconComponent 
                              className="w-5 h-5" 
                              style={{ color: view.color }}
                            />
                          </div>
                          <div className="flex-1">
                            <div className="flex items-center space-x-3">
                              <h3 className="font-medium">{view.name}</h3>
                              <Badge className={`${view.accessLevel === 'all' ? 'bg-green-100 text-green-800' : 
                                view.accessLevel === 'employee' ? 'bg-blue-100 text-blue-800' :
                                view.accessLevel === 'moderator' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-red-100 text-red-800'}`}>
                                {accessLabels[view.accessLevel]}
                              </Badge>
                              {view.isCore && (
                                <Badge className="bg-gray-100 text-gray-600 text-xs">
                                  Section protégée
                                </Badge>
                              )}
                            </div>
                            <p className="text-sm text-gray-600 mt-1">{view.description}</p>
                          </div>
                        </div>

                        <div className="flex items-center space-x-4">
                          <div className="flex flex-col items-end space-y-1">
                            <Label className="text-sm font-medium">Niveau d'accès</Label>
                            <AccessLevelSelect
                              value={view.accessLevel}
                              onChange={(accessLevel) => updateViewAccess(view.id, accessLevel)}
                              disabled={view.isCore}
                              viewId={view.id}
                            />
                          </div>
                        </div>
                      </div>
                    );
                  })}
              </div>

              {/* Permissions Info */}
              <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div className="flex items-start space-x-3">
                  <AlertTriangle className="w-5 h-5 text-blue-600 mt-0.5" />
                  <div className="text-sm text-blue-800">
                    <p className="font-medium mb-2">Niveaux d'accès :</p>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3 text-blue-700">
                      <div className="flex items-center space-x-2">
                        <div className="w-3 h-3 rounded-full bg-green-400"></div>
                        <span><strong>Tous les utilisateurs :</strong> Accès libre</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <div className="w-3 h-3 rounded-full bg-blue-400"></div>
                        <span><strong>Employés et plus :</strong> Accès employé+</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <div className="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <span><strong>Modérateurs et plus :</strong> Accès modérateur+</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <div className="w-3 h-3 rounded-full bg-red-400"></div>
                        <span><strong>Administrateurs uniquement :</strong> Accès admin</span>
                      </div>
                    </div>
                    <p className="mt-3 text-xs text-blue-600">
                      Les sections principales (Tableau de Bord, Annonces, Documents, Administration) ont des niveaux d'accès fixes pour garantir la sécurité.
                    </p>
                  </div>
                </div>
              </div>
            </GlassCard>
          </TabsContent>
        </Tabs>
      </div>
    </MainLayout>
  );
};

export default ViewsManagement;