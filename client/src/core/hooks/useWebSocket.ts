import { useState, useEffect, useRef, useCallback } from 'react';
import { queryClient } from '@/core/lib/queryClient';

interface WebSocketMessage {
  type: string;
  payload: any;
  timestamp: Date;
  userId?: string;
}

interface UseWebSocketOptions {
  url?: string;
  enabled?: boolean;
  onMessage?: (message: WebSocketMessage) => void;
  onConnect?: () => void;
  onDisconnect?: () => void;
  onError?: (error: Event) => void;
}

export function useWebSocket(options: UseWebSocketOptions = {}) {
  const {
    url = `ws://${window.location.host}/ws`,
    enabled = true,
    onMessage,
    onConnect,
    onDisconnect,
    onError
  } = options;

  const [isConnected, setIsConnected] = useState(false);
  const [connectionState, setConnectionState] = useState<'connecting' | 'connected' | 'disconnected' | 'error'>('disconnected');
  const [lastMessage, setLastMessage] = useState<WebSocketMessage | null>(null);
  const [messageHistory, setMessageHistory] = useState<WebSocketMessage[]>([]);
  
  const wsRef = useRef<WebSocket | null>(null);
  const reconnectTimeoutRef = useRef<NodeJS.Timeout | null>(null);
  const reconnectAttempts = useRef(0);
  const maxReconnectAttempts = 5;

  const connect = useCallback(() => {
    if (!enabled || wsRef.current?.readyState === WebSocket.CONNECTING || wsRef.current?.readyState === WebSocket.OPEN) {
      return;
    }

    try {
      setConnectionState('connecting');
      wsRef.current = new WebSocket(url);

      wsRef.current.onopen = () => {
        setIsConnected(true);
        setConnectionState('connected');
        reconnectAttempts.current = 0;
        onConnect?.();
      };

      wsRef.current.onmessage = (event) => {
        try {
          const message: WebSocketMessage = JSON.parse(event.data);
          setLastMessage(message);
          setMessageHistory(prev => [...prev.slice(-99), message]); // Keep last 100 messages
          
          // Handle different message types and invalidate relevant queries
          switch (message.type) {
            case 'NEW_ANNOUNCEMENT':
              queryClient.invalidateQueries({ queryKey: ['/api/announcements'] });
              break;
            case 'NEW_MESSAGE':
              queryClient.invalidateQueries({ queryKey: ['/api/messages'] });
              break;
            case 'FORUM_UPDATE':
              queryClient.invalidateQueries({ queryKey: ['/api/forum'] });
              break;
            case 'TRAINING_UPDATE':
              queryClient.invalidateQueries({ queryKey: ['/api/training-analytics'] });
              break;
            case 'USER_STATUS_CHANGE':
              queryClient.invalidateQueries({ queryKey: ['/api/users'] });
              break;
            case 'NOTIFICATION':
              // Handle real-time notifications
              break;
          }
          
          onMessage?.(message);
        } catch (error) {
          console.error('Failed to parse WebSocket message:', error);
        }
      };

      wsRef.current.onclose = () => {
        setIsConnected(false);
        setConnectionState('disconnected');
        onDisconnect?.();
        
        // Attempt reconnection if enabled and under limit
        if (enabled && reconnectAttempts.current < maxReconnectAttempts) {
          reconnectAttempts.current++;
          const delay = Math.min(1000 * Math.pow(2, reconnectAttempts.current), 30000); // Exponential backoff
          reconnectTimeoutRef.current = setTimeout(connect, delay);
        }
      };

      wsRef.current.onerror = (error) => {
        setConnectionState('error');
        onError?.(error);
      };

    } catch (error) {
      setConnectionState('error');
      console.error('WebSocket connection failed:', error);
    }
  }, [url, enabled, onConnect, onDisconnect, onError, onMessage]);

  const disconnect = useCallback(() => {
    if (reconnectTimeoutRef.current) {
      clearTimeout(reconnectTimeoutRef.current);
      reconnectTimeoutRef.current = null;
    }
    
    if (wsRef.current) {
      wsRef.current.close();
      wsRef.current = null;
    }
    
    setIsConnected(false);
    setConnectionState('disconnected');
  }, []);

  const sendMessage = useCallback((message: Omit<WebSocketMessage, 'timestamp'>) => {
    if (wsRef.current?.readyState === WebSocket.OPEN) {
      const fullMessage: WebSocketMessage = {
        ...message,
        timestamp: new Date()
      };
      wsRef.current.send(JSON.stringify(fullMessage));
      return true;
    }
    return false;
  }, []);

  const clearHistory = useCallback(() => {
    setMessageHistory([]);
    setLastMessage(null);
  }, []);

  // Auto-connect when enabled
  useEffect(() => {
    if (enabled) {
      connect();
    } else {
      disconnect();
    }

    return () => {
      disconnect();
    };
  }, [enabled, connect, disconnect]);

  // Cleanup on unmount
  useEffect(() => {
    return () => {
      disconnect();
    };
  }, [disconnect]);

  return {
    isConnected,
    connectionState,
    lastMessage,
    messageHistory,
    connect,
    disconnect,
    sendMessage,
    clearHistory,
    reconnectAttempts: reconnectAttempts.current
  };
}

// Specialized hooks for different features
export function useNotifications() {
  const [notifications, setNotifications] = useState<any[]>([]);
  
  const { sendMessage } = useWebSocket({
    onMessage: (message) => {
      if (message.type === 'NOTIFICATION') {
        setNotifications(prev => [message.payload, ...prev.slice(0, 49)]); // Keep last 50
      }
    }
  });

  const markAsRead = useCallback((notificationId: string) => {
    setNotifications(prev => 
      prev.map(notif => 
        notif.id === notificationId ? { ...notif, isRead: true } : notif
      )
    );
    sendMessage({ type: 'MARK_NOTIFICATION_READ', payload: { id: notificationId } });
  }, [sendMessage]);

  const clearAll = useCallback(() => {
    setNotifications([]);
    sendMessage({ type: 'CLEAR_NOTIFICATIONS', payload: {} });
  }, [sendMessage]);

  return {
    notifications,
    unreadCount: notifications.filter(n => !n.isRead).length,
    markAsRead,
    clearAll
  };
}

export function useRealTimeChat(channelId?: string) {
  const [messages, setMessages] = useState<any[]>([]);
  const [typingUsers, setTypingUsers] = useState<string[]>([]);
  
  const { sendMessage, isConnected } = useWebSocket({
    onMessage: (message) => {
      if (message.type === 'CHAT_MESSAGE' && message.payload.channelId === channelId) {
        setMessages(prev => [...prev, message.payload]);
      } else if (message.type === 'USER_TYPING' && message.payload.channelId === channelId) {
        setTypingUsers(prev => {
          const filtered = prev.filter(u => u !== message.payload.userId);
          return message.payload.isTyping ? [...filtered, message.payload.userId] : filtered;
        });
      }
    }
  });

  const sendChatMessage = useCallback((text: string) => {
    if (!channelId) return false;
    return sendMessage({
      type: 'CHAT_MESSAGE',
      payload: { channelId, text, timestamp: new Date() }
    });
  }, [channelId, sendMessage]);

  const sendTypingIndicator = useCallback((isTyping: boolean) => {
    if (!channelId) return;
    sendMessage({
      type: 'USER_TYPING',
      payload: { channelId, isTyping }
    });
  }, [channelId, sendMessage]);

  return {
    messages,
    typingUsers,
    isConnected,
    sendMessage: sendChatMessage,
    sendTypingIndicator
  };
}