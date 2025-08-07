import { createRoot } from "react-dom/client";
import App from "./App";
import "./index.css";

// Suppression totale des erreurs ResizeObserver
const debounce = (func: Function, wait: number) => {
  let timeout: NodeJS.Timeout;
  return function executedFunction(...args: any[]) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};

// Enhanced error filtering for stable development
const originalError = console.error;
console.error = debounce((...args: any[]) => {
  if (args[0] && typeof args[0] === 'string') {
    // Filter out common development noise
    if (args[0].includes('ResizeObserver') || 
        args[0].includes('[vite] server connection lost') ||
        args[0].includes('WebSocket connection to')) {
      return; // Ignore silently
    }
  }
  originalError.apply(console, args);
}, 100);

// Enhanced global error handler
window.onerror = (msg) => {
  if (typeof msg === 'string') {
    if (msg.includes('ResizeObserver') || 
        msg.includes('WebSocket') ||
        msg.includes('HMR')) {
      return true; // Suppress development noise
    }
  }
  return false;
};

// Handle unhandled promise rejections related to WebSocket
window.addEventListener('unhandledrejection', (event) => {
  if (event.reason && event.reason.message) {
    if (event.reason.message.includes('WebSocket') || 
        event.reason.message.includes('HMR') ||
        event.reason.message.includes('vite')) {
      event.preventDefault(); // Prevent console spam
    }
  }
});

// Stabilize Vite HMR connections
(function stabilizeViteHMR() {
  let reconnectTimer: NodeJS.Timeout;
  let reconnectAttempts = 0;
  const maxAttempts = 15;
  
  // Enhanced WebSocket management for Vite
  const originalWebSocket = window.WebSocket;
  const OriginalWebSocketConstructor = originalWebSocket;
  
  // Create a proper constructor function
  function ViteStabilizedWebSocket(url: string | URL, protocols?: string | string[]) {
    const ws = new OriginalWebSocketConstructor(url, protocols);
    
    // Add connection monitoring
    ws.addEventListener('close', function(event) {
      // Only handle Vite-related WebSockets
      const urlStr = url.toString();
      if ((urlStr.includes('__vite_ping') || urlStr.includes('/ws') || urlStr.includes('5000')) 
          && event.code !== 1000 && reconnectAttempts < maxAttempts) {
        
        reconnectAttempts++;
        const delay = Math.min(500 * reconnectAttempts, 5000); // Progressive backoff
        
        clearTimeout(reconnectTimer);
        reconnectTimer = setTimeout(() => {
          try {
            // Create new connection
            new OriginalWebSocketConstructor(url, protocols);
          } catch (e) {
            // Silent fail, will retry
          }
        }, delay);
      }
    });
    
    ws.addEventListener('open', function() {
      reconnectAttempts = 0; // Reset on successful connection
      clearTimeout(reconnectTimer);
    });
    
    return ws;
  }
  
  // Copy static properties
  Object.assign(ViteStabilizedWebSocket, originalWebSocket);
  ViteStabilizedWebSocket.prototype = originalWebSocket.prototype;
  
  // Replace the global WebSocket
  (window as any).WebSocket = ViteStabilizedWebSocket;
})();

createRoot(document.getElementById("root")!).render(<App />);
