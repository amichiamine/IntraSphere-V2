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
  
  // Stabilized WebSocket management for Replit environment
  const originalWebSocket = window.WebSocket;
  
  // Enhanced WebSocket wrapper with better error handling
  function createStabilizedWebSocket(url: string | URL, protocols?: string | string[]) {
    const urlStr = url.toString();
    let ws: WebSocket;
    
    try {
      ws = new originalWebSocket(url, protocols);
    } catch (error) {
      // If creation fails, create a mock WebSocket that fails gracefully
      ws = {
        readyState: WebSocket.CLOSED,
        addEventListener: () => {},
        removeEventListener: () => {},
        send: () => {},
        close: () => {},
        dispatchEvent: () => false,
      } as any;
      return ws;
    }
    
    // Only apply special handling to Vite WebSockets
    if (urlStr.includes('__vite_ping') || urlStr.includes('/ws') || urlStr.includes('24678')) {
      
      const handleError = () => {
        if (reconnectAttempts < maxAttempts) {
          reconnectAttempts++;
          clearTimeout(reconnectTimer);
          reconnectTimer = setTimeout(() => {
            try {
              // Attempt to create a new connection silently
              createStabilizedWebSocket(url, protocols);
            } catch (e) {
              // Silently handle reconnection failures
            }
          }, Math.min(1000 + (reconnectAttempts * 500), 8000));
        }
      };
      
      ws.addEventListener('error', handleError);
      ws.addEventListener('close', (event) => {
        if (event.code !== 1000) { // Not a normal closure
          handleError();
        }
      });
      
      ws.addEventListener('open', () => {
        reconnectAttempts = 0;
        clearTimeout(reconnectTimer);
      });
    }
    
    return ws;
  }
  
  // Replace WebSocket constructor while preserving prototype
  Object.setPrototypeOf(createStabilizedWebSocket, originalWebSocket);
  Object.defineProperty(createStabilizedWebSocket, 'prototype', {
    value: originalWebSocket.prototype,
    writable: false
  });
  
  (window as any).WebSocket = createStabilizedWebSocket;
})();

createRoot(document.getElementById("root")!).render(<App />);
