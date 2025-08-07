import WebSocket, { WebSocketServer } from 'ws';
import { IncomingMessage } from 'http';
import { parse } from 'url';

interface WebSocketClient extends WebSocket {
  userId?: string;
  channels?: Set<string>;
  isAlive?: boolean;
}

interface WebSocketMessage {
  type: string;
  payload: any;
  timestamp: Date;
  userId?: string;
}

export class WebSocketManager {
  private wss: WebSocketServer;
  private clients: Map<string, WebSocketClient> = new Map();
  private channels: Map<string, Set<string>> = new Map();
  private heartbeatInterval: NodeJS.Timeout;

  constructor(server: any) {
    this.wss = new WebSocketServer({ 
      server,
      path: '/ws',
      verifyClient: this.verifyClient.bind(this)
    });

    this.wss.on('connection', this.handleConnection.bind(this));
    
    // Setup heartbeat to detect broken connections
    this.heartbeatInterval = setInterval(() => {
      this.wss.clients.forEach((ws: WebSocketClient) => {
        if (ws.isAlive === false) {
          this.cleanupClient(ws);
          return ws.terminate();
        }
        ws.isAlive = false;
        ws.ping();
      });
    }, 30000);

    console.log('✅ WebSocket server initialized on /ws');
  }

  private verifyClient(info: { origin: string; secure: boolean; req: IncomingMessage }): boolean {
    // Add authentication logic here if needed
    return true;
  }

  private handleConnection(ws: WebSocketClient, req: IncomingMessage) {
    const { query } = parse(req.url || '', true);
    const userId = query.userId as string;
    
    ws.userId = userId;
    ws.channels = new Set();
    ws.isAlive = true;
    
    if (userId) {
      this.clients.set(userId, ws);
    }

    ws.on('message', (data: string) => {
      try {
        const message: WebSocketMessage = JSON.parse(data);
        this.handleMessage(ws, message);
      } catch (error) {
        console.error('Invalid WebSocket message:', error);
      }
    });

    ws.on('pong', () => {
      ws.isAlive = true;
    });

    ws.on('close', () => {
      this.cleanupClient(ws);
    });

    ws.on('error', (error) => {
      console.error('WebSocket error:', error);
      this.cleanupClient(ws);
    });

    // Send welcome message
    this.sendToClient(ws, {
      type: 'CONNECTED',
      payload: { message: 'WebSocket connection established' },
      timestamp: new Date()
    });

    console.log(`🔌 WebSocket client connected: ${userId || 'anonymous'}`);
  }

  private handleMessage(ws: WebSocketClient, message: WebSocketMessage) {
    message.userId = ws.userId;
    message.timestamp = new Date();

    switch (message.type) {
      case 'AUTHENTICATE':
        this.authenticateClient(ws, message.payload.userId);
        break;
        
      case 'JOIN_CHANNEL':
        this.joinChannel(ws, message.payload.channelId);
        break;
        
      case 'LEAVE_CHANNEL':
        this.leaveChannel(ws, message.payload.channelId);
        break;
        
      case 'CHAT_MESSAGE':
        this.broadcastToChannel(message.payload.channelId, message);
        break;
        
      case 'USER_TYPING':
        this.broadcastToChannel(message.payload.channelId, message, ws.userId);
        break;
        
      case 'MARK_NOTIFICATION_READ':
        this.handleNotificationRead(ws, message.payload);
        break;
        
      case 'CLEAR_NOTIFICATIONS':
        this.handleClearNotifications(ws);
        break;
        
      case 'REQUEST_USERS_COUNT':
        this.sendUsersCount(ws);
        break;
        
      default:
        console.log('Unknown message type:', message.type);
    }
  }

  private authenticateClient(ws: WebSocketClient, userId: string) {
    if (ws.userId && ws.userId !== userId) {
      this.clients.delete(ws.userId);
    }
    
    ws.userId = userId;
    this.clients.set(userId, ws);
    
    // Broadcast user online status
    this.broadcast({
      type: 'USER_ONLINE',
      payload: { userId },
      timestamp: new Date()
    });
    
    // Send current users count
    this.sendUsersCount(ws);
  }

  private handleNotificationRead(ws: WebSocketClient, payload: any) {
    // Implementation for marking notifications as read
    console.log(`Notification ${payload.id} marked as read by ${ws.userId}`);
  }

  private handleClearNotifications(ws: WebSocketClient) {
    // Implementation for clearing all notifications
    console.log(`All notifications cleared for ${ws.userId}`);
  }

  private sendUsersCount(ws: WebSocketClient) {
    const userIds = Array.from(this.clients.keys());
    this.sendToClient(ws, {
      type: 'USERS_COUNT',
      payload: { users: userIds, count: userIds.length },
      timestamp: new Date()
    });
  }

  private broadcast(message: WebSocketMessage) {
    this.wss.clients.forEach((client: WebSocketClient) => {
      this.sendToClient(client, message);
    });
  }

  private joinChannel(ws: WebSocketClient, channelId: string) {
    if (!ws.channels) return;
    
    ws.channels.add(channelId);
    
    if (!this.channels.has(channelId)) {
      this.channels.set(channelId, new Set());
    }
    
    this.channels.get(channelId)?.add(ws.userId || '');
    
    this.sendToClient(ws, {
      type: 'JOINED_CHANNEL',
      payload: { channelId },
      timestamp: new Date()
    });
  }

  private leaveChannel(ws: WebSocketClient, channelId: string) {
    if (!ws.channels) return;
    
    ws.channels.delete(channelId);
    this.channels.get(channelId)?.delete(ws.userId || '');
    
    if (this.channels.get(channelId)?.size === 0) {
      this.channels.delete(channelId);
    }
  }

  private cleanupClient(ws: WebSocketClient) {
    if (ws.userId) {
      this.clients.delete(ws.userId);
      
      // Broadcast user offline status
      this.broadcast({
        type: 'USER_OFFLINE',
        payload: { userId: ws.userId },
        timestamp: new Date()
      });
    }
    
    // Remove from all channels
    ws.channels?.forEach(channelId => {
      this.leaveChannel(ws, channelId);
    });
  }

  private sendToClient(client: WebSocketClient, message: WebSocketMessage) {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify(message));
    }
  }

  private broadcastToChannel(channelId: string, message: WebSocketMessage, excludeUserId?: string) {
    const channelUsers = this.channels.get(channelId);
    if (!channelUsers) return;

    channelUsers.forEach(userId => {
      if (userId !== excludeUserId) {
        const client = this.clients.get(userId);
        if (client) {
          this.sendToClient(client, message);
        }
      }
    });
  }

  // Public methods for broadcasting events
  public broadcastToUser(userId: string, message: Omit<WebSocketMessage, 'timestamp'>) {
    const client = this.clients.get(userId);
    if (client) {
      this.sendToClient(client, { ...message, timestamp: new Date() });
    }
  }

  public broadcastToAll(message: Omit<WebSocketMessage, 'timestamp'>) {
    const fullMessage = { ...message, timestamp: new Date() };
    this.wss.clients.forEach((client: WebSocketClient) => {
      this.sendToClient(client, fullMessage);
    });
  }

  public broadcastNewAnnouncement(announcement: any) {
    this.broadcastToAll({
      type: 'NEW_ANNOUNCEMENT',
      payload: announcement
    });
  }

  public broadcastNewMessage(message: any) {
    this.broadcastToUser(message.recipientId, {
      type: 'NEW_MESSAGE',
      payload: message
    });
  }

  public broadcastForumUpdate(update: any) {
    this.broadcastToAll({
      type: 'FORUM_UPDATE',
      payload: update
    });
  }

  public broadcastTrainingUpdate(update: any) {
    this.broadcastToAll({
      type: 'TRAINING_UPDATE',
      payload: update
    });
  }

  public notifyUser(userId: string, notification: any) {
    this.broadcastToUser(userId, {
      type: 'NOTIFICATION',
      payload: notification
    });
  }

  public getConnectedUsers(): string[] {
    return Array.from(this.clients.keys());
  }

  public getChannelUsers(channelId: string): string[] {
    return Array.from(this.channels.get(channelId) || []);
  }

  public getUserCount(): number {
    return this.clients.size;
  }

  public close() {
    clearInterval(this.heartbeatInterval);
    this.wss.close();
  }
}

let wsManager: WebSocketManager | null = null;

export function initializeWebSocket(server: any): WebSocketManager {
  if (!wsManager) {
    wsManager = new WebSocketManager(server);
  }
  return wsManager;
}

export function getWebSocketManager(): WebSocketManager | null {
  return wsManager;
}