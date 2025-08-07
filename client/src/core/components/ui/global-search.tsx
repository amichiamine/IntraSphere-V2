import { useState, useEffect, useMemo } from "react";
import { Search, FileText, User, MessageSquare, BookOpen, Calendar, X } from "lucide-react";
import { Command, CommandDialog, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, CommandSeparator } from "@/core/components/ui/command";
import { Button } from "@/core/components/ui/button";
import { Badge } from "@/core/components/ui/badge";
import { useQuery } from "@tanstack/react-query";
import { useLocation } from "wouter";

interface SearchResult {
  id: string;
  title: string;
  type: 'user' | 'document' | 'announcement' | 'content' | 'course' | 'event' | 'forum';
  description?: string;
  url: string;
  metadata?: any;
}

const SEARCH_TYPES = {
  user: { icon: User, label: "Utilisateurs", color: "bg-blue-100 text-blue-800" },
  document: { icon: FileText, label: "Documents", color: "bg-green-100 text-green-800" },
  announcement: { icon: MessageSquare, label: "Annonces", color: "bg-orange-100 text-orange-800" },
  content: { icon: FileText, label: "Contenus", color: "bg-purple-100 text-purple-800" },
  course: { icon: BookOpen, label: "Formations", color: "bg-indigo-100 text-indigo-800" },
  event: { icon: Calendar, label: "Événements", color: "bg-red-100 text-red-800" },
  forum: { icon: MessageSquare, label: "Forum", color: "bg-yellow-100 text-yellow-800" }
};

export function GlobalSearch() {
  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState("");
  const [, setLocation] = useLocation();

  // Global search endpoint
  const { data: searchResults = [], isLoading } = useQuery({
    queryKey: ["/api/search/global", query],
    enabled: query.length >= 2
  });

  // Quick search for specific types
  const { data: users = [] } = useQuery<any[]>({
    queryKey: ["/api/search/users", query],
    enabled: query.length >= 2
  });

  const { data: documents = [] } = useQuery<any[]>({
    queryKey: ["/api/search/documents", query],
    enabled: query.length >= 2
  });

  const { data: announcements = [] } = useQuery<any[]>({
    queryKey: ["/api/search/announcements", query],
    enabled: query.length >= 2
  });

  const { data: contents = [] } = useQuery<any[]>({
    queryKey: ["/api/search/content", query],
    enabled: query.length >= 2
  });

  // Combine all search results
  const allResults = useMemo(() => {
    const combined: SearchResult[] = [];
    
    // Add users
    users.forEach((user: any) => {
      combined.push({
        id: user.id,
        title: `${user.firstName} ${user.lastName}`,
        type: 'user',
        description: `${user.position || ''} - ${user.department || ''}`,
        url: `/directory?user=${user.id}`,
        metadata: user
      });
    });

    // Add documents
    documents.forEach((doc: any) => {
      combined.push({
        id: doc.id,
        title: doc.title,
        type: 'document',
        description: doc.description,
        url: `/content/documents?doc=${doc.id}`,
        metadata: doc
      });
    });

    // Add announcements
    announcements.forEach((ann: any) => {
      combined.push({
        id: ann.id,
        title: ann.title,
        type: 'announcement',
        description: ann.content.substring(0, 100) + '...',
        url: `/content/announcements?ann=${ann.id}`,
        metadata: ann
      });
    });

    // Add contents
    contents.forEach((content: any) => {
      combined.push({
        id: content.id,
        title: content.title,
        type: 'content',
        description: content.description,
        url: `/content?content=${content.id}`,
        metadata: content
      });
    });

    return combined;
  }, [users, documents, announcements, contents]);

  // Group results by type
  const groupedResults = useMemo(() => {
    const groups: Record<string, SearchResult[]> = {};
    allResults.forEach(result => {
      if (!groups[result.type]) {
        groups[result.type] = [];
      }
      groups[result.type].push(result);
    });
    return groups;
  }, [allResults]);

  // Keyboard shortcut
  useEffect(() => {
    const down = (e: KeyboardEvent) => {
      if (e.key === "k" && (e.metaKey || e.ctrlKey)) {
        e.preventDefault();
        setOpen(true);
      }
    };

    document.addEventListener("keydown", down);
    return () => document.removeEventListener("keydown", down);
  }, []);

  const handleSelect = (result: SearchResult) => {
    setOpen(false);
    setQuery("");
    setLocation(result.url);
  };

  return (
    <>
      <Button
        variant="outline"
        className="relative w-full justify-start text-sm text-muted-foreground sm:pr-12 md:w-40 lg:w-64"
        onClick={() => setOpen(true)}
        data-testid="button-global-search"
      >
        <Search className="mr-2 h-4 w-4" />
        <span className="hidden lg:inline-flex">Rechercher...</span>
        <span className="inline-flex lg:hidden">Recherche</span>
        <kbd className="pointer-events-none absolute right-1.5 top-1.5 hidden h-5 select-none items-center gap-1 rounded border bg-muted px-1.5 font-mono text-[10px] font-medium opacity-100 sm:flex">
          <span className="text-xs">⌘</span>K
        </kbd>
      </Button>

      <CommandDialog open={open} onOpenChange={setOpen}>
        <CommandInput
          placeholder="Rechercher utilisateurs, documents, annonces..."
          value={query}
          onValueChange={setQuery}
          data-testid="input-global-search"
        />
        <CommandList>
          {query.length < 2 ? (
            <CommandEmpty>
              <div className="text-center py-6">
                <Search className="h-8 w-8 mx-auto mb-2 opacity-50" />
                <p className="text-sm text-muted-foreground">
                  Tapez au moins 2 caractères pour rechercher
                </p>
                <p className="text-xs text-muted-foreground mt-1">
                  Utilisez <kbd className="bg-muted px-1 rounded">Cmd/Ctrl + K</kbd> pour ouvrir
                </p>
              </div>
            </CommandEmpty>
          ) : isLoading ? (
            <CommandEmpty>
              <div className="text-center py-6">
                <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-primary mx-auto mb-2"></div>
                <p className="text-sm text-muted-foreground">Recherche en cours...</p>
              </div>
            </CommandEmpty>
          ) : allResults.length === 0 ? (
            <CommandEmpty>
              <div className="text-center py-6" data-testid="text-no-results">
                <Search className="h-8 w-8 mx-auto mb-2 opacity-50" />
                <p className="text-sm text-muted-foreground">
                  Aucun résultat pour "{query}"
                </p>
                <p className="text-xs text-muted-foreground mt-1">
                  Essayez avec d'autres mots-clés
                </p>
              </div>
            </CommandEmpty>
          ) : (
            <>
              {Object.entries(groupedResults).map(([type, results]) => {
                const typeConfig = SEARCH_TYPES[type as keyof typeof SEARCH_TYPES];
                const Icon = typeConfig.icon;
                
                return (
                  <CommandGroup key={type} heading={typeConfig.label}>
                    {results.slice(0, 5).map((result) => (
                      <CommandItem
                        key={result.id}
                        value={result.title}
                        onSelect={() => handleSelect(result)}
                        data-testid={`search-result-${result.type}-${result.id}`}
                      >
                        <div className="flex items-center gap-3 w-full">
                          <Icon className="h-4 w-4 text-muted-foreground" />
                          <div className="flex-1 min-w-0">
                            <div className="flex items-center gap-2 mb-1">
                              <span className="font-medium truncate">{result.title}</span>
                              <Badge variant="secondary" className={`text-xs ${typeConfig.color}`}>
                                {typeConfig.label}
                              </Badge>
                            </div>
                            {result.description && (
                              <p className="text-xs text-muted-foreground truncate">
                                {result.description}
                              </p>
                            )}
                          </div>
                        </div>
                      </CommandItem>
                    ))}
                    {results.length > 5 && (
                      <CommandItem disabled>
                        <span className="text-xs text-muted-foreground">
                          +{results.length - 5} autres résultats...
                        </span>
                      </CommandItem>
                    )}
                  </CommandGroup>
                );
              })}
            </>
          )}
        </CommandList>
      </CommandDialog>
    </>
  );
}