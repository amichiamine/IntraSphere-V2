/**
 * Vite connection stabilizer
 * Ensures stable WebSocket connections for development
 */

import { log } from "../vite";

export class ViteStabilizer {
  private reconnectAttempts = 0;
  private maxReconnectAttempts = 10;
  private reconnectDelay = 1000;
  private isStabilizing = false;

  constructor() {
    this.setupClientStabilization();
  }

  private setupClientStabilization(): void {
    // Inject stabilization script into client
    if (typeof window !== 'undefined') {
      this.injectClientScript();
    }
  }

  private injectClientScript(): void {
    const script = document.createElement('script');
    script.textContent = `
      (function() {
        let viteReconnectTimer;
        let reconnectAttempts = 0;
        const maxAttempts = 10;
        
        // Override WebSocket to add reconnection logic
        const originalWebSocket = window.WebSocket;
        window.WebSocket = function(url, protocols) {
          const ws = new originalWebSocket(url, protocols);
          
          ws.addEventListener('close', function(event) {
            if (event.code !== 1000 && reconnectAttempts < maxAttempts) {
              reconnectAttempts++;
              const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000);
              
              clearTimeout(viteReconnectTimer);
              viteReconnectTimer = setTimeout(() => {
                if (url.includes('/__vite_ping') || url.includes('/ws')) {
                  try {
                    const newWs = new originalWebSocket(url, protocols);
                    newWs.onopen = () => {
                      reconnectAttempts = 0;
                    };
                  } catch (e) {
                    // Reconnection failed, will retry
                  }
                }
              }, delay);
            }
          });
          
          ws.addEventListener('open', function() {
            reconnectAttempts = 0;
          });
          
          return ws;
        };
        
        // Copy static properties
        Object.setPrototypeOf(window.WebSocket, originalWebSocket);
        Object.defineProperty(window.WebSocket, 'prototype', {
          value: originalWebSocket.prototype,
          writable: false
        });
      })();
    `;
    
    document.head.appendChild(script);
  }

  /**
   * Stabilize Vite connections by managing reconnection logic
   */
  public stabilizeConnections(): void {
    if (this.isStabilizing) return;
    
    this.isStabilizing = true;
    log('Initializing Vite connection stabilizer', 'vite-stabilizer');
    
    // Reset reconnection attempts
    this.reconnectAttempts = 0;
    
    // Setup periodic stability check
    setInterval(() => {
      this.checkConnectionHealth();
    }, 30000); // Check every 30 seconds
    
    log('Vite stabilizer active', 'vite-stabilizer');
  }

  private checkConnectionHealth(): void {
    // This runs server-side to monitor Vite health
    if (this.reconnectAttempts > this.maxReconnectAttempts) {
      log('Max reconnection attempts reached, stabilizer paused', 'vite-stabilizer');
      return;
    }
    
    // Health check logic can be expanded here
    // For now, we just log the status
    log(`Connection health check - attempts: ${this.reconnectAttempts}`, 'vite-stabilizer');
  }

  /**
   * Reset stabilizer state
   */
  public reset(): void {
    this.reconnectAttempts = 0;
    this.isStabilizing = false;
    log('Vite stabilizer reset', 'vite-stabilizer');
  }
}

export const viteStabilizer = new ViteStabilizer();