import { useState } from "react";
import { Search, Bell, MessageSquare, Menu } from "lucide-react";
import { Button } from "@/core/components/ui/button";
import { Input } from "@/core/components/ui/input";
import { GlobalSearch } from "@/core/components/ui/global-search";
import { NotificationCenter } from "@/core/components/ui/notification-center";

interface HeaderProps {
  onMenuClick: () => void;
}

export function Header({ onMenuClick }: HeaderProps) {

  return (
    <header className="glass-effect border-b border-white/20 h-20 flex-shrink-0">
      <div className="flex items-center justify-between h-full px-6">
        {/* Mobile menu button */}
        <Button
          variant="ghost"
          size="sm"
          className="lg:hidden p-2 rounded-xl hover:bg-white/20"
          onClick={onMenuClick}
        >
          <Menu className="h-6 w-6 text-gray-700" />
        </Button>

        {/* Advanced Global Search */}
        <div className="flex-1 max-w-2xl mx-4">
          <GlobalSearch />
        </div>

        {/* Quick Actions */}
        <div className="flex items-center space-x-3">
          <NotificationCenter />
          
          <Button
            variant="ghost"
            size="sm"
            className="p-3 glass-card rounded-2xl hover:bg-white/80 transition-all duration-300 hover-lift shadow-lg"
          >
            <MessageSquare className="h-5 w-5 text-gray-700" />
          </Button>
        </div>
      </div>
    </header>
  );
}
