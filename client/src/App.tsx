import { Switch, Route } from "wouter";
import { queryClient } from "./lib/queryClient";
import { QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import { AuthProvider, useAuth } from "@/hooks/useAuth";
import { ThemeLoader } from "@/components/ThemeLoader";


// Public pages
import PublicDashboard from "@/pages/public-dashboard";
import LoginPage from "@/pages/login";

// Authenticated pages
import Dashboard from "@/pages/dashboard"; // Admin dashboard
import EmployeeDashboard from "@/pages/employee-dashboard";
import Announcements from "@/pages/announcements";
import Content from "@/pages/content";
import Documents from "@/pages/documents";
import Directory from "@/pages/directory";
import Settings from "@/pages/settings";
import Messages from "@/pages/messages";
import Complaints from "@/pages/complaints";
import Admin from "@/pages/admin";
import ViewsManagement from "@/pages/views-management";
import CreateAnnouncement from "@/pages/create-announcement";
import CreateContent from "@/pages/create-content";
import Training from "@/pages/training";
import TrainingAdmin from "@/pages/training-admin";
import Trainings from "@/pages/trainings";
import { ForumPage } from "@/pages/forum";
import { ForumTopicPage } from "@/pages/forum-topic";
import { ForumNewTopicPage } from "@/pages/forum-new-topic";
import NotFound from "@/pages/not-found";

function Router() {
  const { user, isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-purple-600 mx-auto mb-4"></div>
          <h2 className="text-xl font-semibold text-gray-700">Chargement...</h2>
          <p className="text-gray-500 mt-2">Initialisation d'IntraSphere</p>
        </div>
      </div>
    );
  }

  // Public routes (not authenticated)
  if (!user) {
    return (
      <Switch>
        <Route path="/login" component={LoginPage} />
        <Route path="/" component={PublicDashboard} />
        {/* Redirect everything else to public dashboard */}
        <Route component={PublicDashboard} />
      </Switch>
    );
  }

  // Authenticated routes - different dashboards based on role
  const getDashboardComponent = () => {
    switch (user.role) {
      case "admin":
        return Dashboard; // Admin dashboard with all features
      case "moderator":
        return Dashboard; // Same as admin but with some restrictions
      case "employee":
      default:
        return EmployeeDashboard; // Simplified employee dashboard
    }
  };

  const DashboardComponent = getDashboardComponent();

  return (
    <Switch>
      <Route path="/" component={DashboardComponent} />
      <Route path="/announcements" component={Announcements} />
      <Route path="/content" component={Content} />
      <Route path="/documents" component={Documents} />
      <Route path="/directory" component={Directory} />
      <Route path="/training" component={Training} />
      <Route path="/trainings" component={Trainings} />
      <Route path="/messages" component={Messages} />
      <Route path="/complaints" component={Complaints} />
      
      {/* Forum routes */}
      <Route path="/forum" component={ForumPage} />
      <Route path="/forum/topic/:id" component={ForumTopicPage} />
      <Route path="/forum/new-topic" component={ForumNewTopicPage} />
      
      {/* Admin-only routes */}
      {(user.role === "admin" || user.role === "moderator") && (
        <>
          <Route path="/admin" component={Admin} />
          <Route path="/views-management" component={ViewsManagement} />
          <Route path="/create-announcement" component={CreateAnnouncement} />
          <Route path="/create-content" component={CreateContent} />
          <Route path="/training-admin" component={TrainingAdmin} />
        </>
      )}
      
      <Route path="/settings" component={Settings} />
      
      {/* Fallback to 404 */}
      <Route component={NotFound} />
    </Switch>
  );
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <ThemeLoader>
          <TooltipProvider>
            <Toaster />
            <Router />
          </TooltipProvider>
        </ThemeLoader>
      </AuthProvider>
    </QueryClientProvider>
  );
}

export default App;
