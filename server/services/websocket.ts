import { WebSocketServer, WebSocket } from 'ws';
import { Server } from 'http';
import { storage } from '../data/storage';

interface WebSocketClient extends WebSocket {
  userId?: string;
  channels?: Set<string>;
  lastHeartbeat?: number;
}

interface WebSocketMessage {
  type: string;
  payload?: any;
  channel?: string;
}

export class WebSocketManager {
  private wss: WebSocketServer;
  private clients: Map<string, WebSocketClient> = new Map();
  private channels: Map<string, Set<string>> = new Map();
  private heartbeatInterval: NodeJS.Timeout;

  constructor(server: Server) {
    this.wss = new WebSocketServer({ 
      server,
      path: '/ws'
    });

    this.wss.on('connection', this.handleConnection.bind(this));
    
    // Heartbeat to keep connections alive
    this.heartbeatInterval = setInterval(this.heartbeat.bind(this), 30000);
  }

  private handleConnection(ws: WebSocketClient) {
    const clientId = this.generateClientId();
    this.clients.set(clientId, ws);
    ws.channels = new Set();
    ws.lastHeartbeat = Date.now();

    ws.on('message', (data: string) => {
      try {
        const message: WebSocketMessage = JSON.parse(data);
        this.handleMessage(clientId, ws, message);
      } catch (error) {
        console.error('WebSocket message parse error:', error);
      }
    });

    ws.on('close', () => {
      this.handleDisconnect(clientId, ws);
    });

    ws.on('pong', () => {
      ws.lastHeartbeat = Date.now();
    });

    // Send welcome message
    this.sendToClient(clientId, {
      type: 'CONNECTED',
      payload: { clientId, timestamp: Date.now() }
    });
  }

  private handleMessage(clientId: string, ws: WebSocketClient, message: WebSocketMessage) {
    switch (message.type) {
      case 'AUTHENTICATE':
        this.authenticateClient(clientId, ws, message.payload?.userId);
        break;
      
      case 'JOIN_CHANNEL':
        this.joinChannel(clientId, ws, message.payload?.channel);
        break;
      
      case 'LEAVE_CHANNEL':
        this.leaveChannel(clientId, ws, message.payload?.channel);
        break;
      
      case 'HEARTBEAT':
        ws.lastHeartbeat = Date.now();
        this.sendToClient(clientId, { type: 'HEARTBEAT_ACK' });
        break;
      
      case 'TYPING_START':
        this.broadcastToChannel(message.payload?.channel, {
          type: 'USER_TYPING',
          payload: { userId: ws.userId, isTyping: true }
        }, clientId);
        break;
      
      case 'TYPING_STOP':
        this.broadcastToChannel(message.payload?.channel, {
          type: 'USER_TYPING',
          payload: { userId: ws.userId, isTyping: false }
        }, clientId);
        break;
    }
  }

  private authenticateClient(clientId: string, ws: WebSocketClient, userId: string) {
    if (!userId) return;
    
    ws.userId = userId;
    
    // Join user to personal channel
    this.joinChannel(clientId, ws, `user_${userId}`);
    
    // Broadcast user online status
    this.broadcastUserStatus(userId, true);
    
    this.sendToClient(clientId, {
      type: 'AUTHENTICATED',
      payload: { userId, timestamp: Date.now() }
    });
  }

  private joinChannel(clientId: string, ws: WebSocketClient, channel: string) {
    if (!channel || !ws.channels) return;
    
    ws.channels.add(channel);
    
    if (!this.channels.has(channel)) {
      this.channels.set(channel, new Set());
    }
    this.channels.get(channel)!.add(clientId);
    
    this.sendToClient(clientId, {
      type: 'CHANNEL_JOINED',
      payload: { channel, timestamp: Date.now() }
    });
  }

  private leaveChannel(clientId: string, ws: WebSocketClient, channel: string) {
    if (!channel || !ws.channels) return;
    
    ws.channels.delete(channel);
    this.channels.get(channel)?.delete(clientId);
    
    if (this.channels.get(channel)?.size === 0) {
      this.channels.delete(channel);
    }
    
    this.sendToClient(clientId, {
      type: 'CHANNEL_LEFT',
      payload: { channel, timestamp: Date.now() }
    });
  }

  private handleDisconnect(clientId: string, ws: WebSocketClient) {
    // Leave all channels
    if (ws.channels) {
      ws.channels.forEach(channel => {
        this.channels.get(channel)?.delete(clientId);
        if (this.channels.get(channel)?.size === 0) {
          this.channels.delete(channel);
        }
      });
    }
    
    // Broadcast user offline status
    if (ws.userId) {
      this.broadcastUserStatus(ws.userId, false);
    }
    
    this.clients.delete(clientId);
  }

  private heartbeat() {
    const now = Date.now();
    this.clients.forEach((ws, clientId) => {
      if (ws.lastHeartbeat && now - ws.lastHeartbeat > 35000) {
        // Client hasn't responded to heartbeat, close connection
        ws.terminate();
        this.clients.delete(clientId);
      } else {
        ws.ping();
      }
    });
  }

  private generateClientId(): string {
    return Math.random().toString(36).substr(2, 9);
  }

  private sendToClient(clientId: string, message: WebSocketMessage) {
    const client = this.clients.get(clientId);
    if (client && client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify(message));
    }
  }

  private broadcastToChannel(channel: string, message: WebSocketMessage, excludeClientId?: string) {
    const channelClients = this.channels.get(channel);
    if (!channelClients) return;
    
    channelClients.forEach(clientId => {
      if (clientId !== excludeClientId) {
        this.sendToClient(clientId, message);
      }
    });
  }

  private broadcastUserStatus(userId: string, isOnline: boolean) {
    this.broadcast({
      type: 'USER_STATUS',
      payload: { userId, isOnline, timestamp: Date.now() }
    });
  }

  // Public methods for broadcasting events

  public broadcast(message: WebSocketMessage) {
    this.clients.forEach((_, clientId) => {
      this.sendToClient(clientId, message);
    });
  }

  public broadcastToUsers(userIds: string[], message: WebSocketMessage) {
    userIds.forEach(userId => {
      this.broadcastToChannel(`user_${userId}`, message);
    });
  }

  public broadcastNewAnnouncement(announcement: any) {
    this.broadcast({
      type: 'NEW_ANNOUNCEMENT',
      payload: announcement
    });
  }

  public broadcastNewMessage(message: any) {
    // Send to recipient
    this.broadcastToChannel(`user_${message.recipientId}`, {
      type: 'NEW_MESSAGE',
      payload: message
    });
  }

  public broadcastForumUpdate(update: any) {
    this.broadcast({
      type: 'FORUM_UPDATE',
      payload: update
    });
  }

  public broadcastTrainingUpdate(update: any) {
    this.broadcast({
      type: 'TRAINING_UPDATE',
      payload: update
    });
  }

  public broadcastComplaintUpdate(complaint: any) {
    // Send to all admins and moderators
    this.broadcast({
      type: 'COMPLAINT_UPDATE',
      payload: complaint
    });
  }

  public getConnectedUsers(): string[] {
    const users: string[] = [];
    this.clients.forEach(client => {
      if (client.userId) {
        users.push(client.userId);
      }
    });
    return [...new Set(users)]; // Remove duplicates
  }

  public getConnectedUserCount(): number {
    return this.getConnectedUsers().length;
  }

  public isUserOnline(userId: string): boolean {
    return this.getConnectedUsers().includes(userId);
  }

  public close() {
    clearInterval(this.heartbeatInterval);
    this.wss.close();
  }
}

export let wsManager: WebSocketManager;

export function initializeWebSocket(server: Server) {
  wsManager = new WebSocketManager(server);
  console.log('WebSocket server initialized on /ws');
  return wsManager;
}