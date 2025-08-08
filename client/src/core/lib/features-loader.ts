/**
 * Manual Features Loader for IntraSphere Advanced Features
 * Use this to enable advanced features when ready
 */

import { integrationUtils } from './integration';

export interface FeatureConfig {
  enableWebSocket?: boolean;
  enableServiceWorker?: boolean;
  enableCaching?: boolean;
  enableRealTimeSync?: boolean;
  enableOfflineMode?: boolean;
}

export class FeaturesLoader {
  private loaded = false;

  /**
   * Activate advanced features manually
   * Use this when you want to enable modern capabilities
   */
  public async activateAdvancedFeatures(config: FeatureConfig = {}): Promise<void> {
    if (this.loaded) {
      console.log('Advanced features already loaded');
      return;
    }

    try {
      console.log('🚀 Activating IntraSphere advanced features...');
      
      const finalConfig = {
        enableWebSocket: true,
        enableServiceWorker: true,
        enableCaching: true,
        enableRealTimeSync: true,
        enableOfflineMode: true,
        ...config
      };

      await integrationUtils.initialize(finalConfig);
      
      this.loaded = true;
      console.log('✅ Advanced features activated successfully');
      
      // Show success notification
      this.showActivationNotification();
      
    } catch (error) {
      console.error('❌ Failed to activate advanced features:', error);
      throw error;
    }
  }

  /**
   * Get current status of advanced features
   */
  public getStatus() {
    return {
      loaded: this.loaded,
      stats: this.loaded ? integrationUtils.getStats() : null,
      connectionStatus: this.loaded ? integrationUtils.getConnectionStatus() : null
    };
  }

  /**
   * Preload critical data (works even without advanced features)
   */
  public async preloadData(): Promise<void> {
    try {
      await integrationUtils.preloadData();
      console.log('📦 Critical data preloaded');
    } catch (error) {
      console.warn('Failed to preload data:', error);
    }
  }

  /**
   * Clear all cached data
   */
  public async clearAllData(): Promise<void> {
    try {
      await integrationUtils.clearAllData();
      console.log('🗑️ All cached data cleared');
    } catch (error) {
      console.warn('Failed to clear data:', error);
    }
  }

  /**
   * Refresh cache for specific module
   */
  public refreshCache(module?: string): void {
    integrationUtils.refreshCache(module);
    console.log(module ? `🔄 Cache refreshed for ${module}` : '🔄 All cache refreshed');
  }

  private showActivationNotification(): void {
    // Create a simple notification div
    const notification = document.createElement('div');
    notification.innerHTML = `
      <div style="
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 10000;
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 14px;
        font-weight: 500;
        max-width: 320px;
        animation: slideIn 0.3s ease-out;
      ">
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="font-size: 18px;">🚀</span>
          <div>
            <div style="font-weight: 600; margin-bottom: 4px;">Fonctionnalités Avancées Activées</div>
            <div style="opacity: 0.9; font-size: 12px;">WebSocket • Cache • PWA • Temps Réel</div>
          </div>
        </div>
      </div>
      <style>
        @keyframes slideIn {
          from { transform: translateX(100%); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
        }
      </style>
    `;

    document.body.appendChild(notification);

    // Remove after 4 seconds
    setTimeout(() => {
      notification.style.animation = 'slideIn 0.3s ease-in reverse';
      setTimeout(() => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification);
        }
      }, 300);
    }, 4000);
  }
}

// Global instance
export const featuresLoader = new FeaturesLoader();

// Expose to window for console access
if (typeof window !== 'undefined') {
  (window as any).IntraSphere = {
    // Activate all advanced features
    activate: () => featuresLoader.activateAdvancedFeatures(),
    
    // Activate with custom config
    activateWithConfig: (config: FeatureConfig) => featuresLoader.activateAdvancedFeatures(config),
    
    // Get status
    status: () => featuresLoader.getStatus(),
    
    // Preload data
    preload: () => featuresLoader.preloadData(),
    
    // Clear cache
    clearCache: () => featuresLoader.clearAllData(),
    
    // Refresh cache
    refresh: (module?: string) => featuresLoader.refreshCache(module),
    
    // Quick activation commands
    enableWebSocket: () => featuresLoader.activateAdvancedFeatures({ 
      enableWebSocket: true, 
      enableCaching: true,
      enableServiceWorker: false,
      enableOfflineMode: false,
      enableRealTimeSync: true
    }),
    
    enablePWA: () => featuresLoader.activateAdvancedFeatures({ 
      enableServiceWorker: true, 
      enableOfflineMode: true,
      enableCaching: true,
      enableWebSocket: false,
      enableRealTimeSync: false
    }),
    
    enableAll: () => featuresLoader.activateAdvancedFeatures()
  };
  
  console.log(`
🎯 IntraSphere Advanced Features Available

Pour activer les fonctionnalités avancées, utilisez :

🚀 ACTIVATION COMPLÈTE :
   IntraSphere.activate()

⚡ ACTIVATION SÉLECTIVE :
   IntraSphere.enableWebSocket()  // Temps réel uniquement
   IntraSphere.enablePWA()        // PWA uniquement

🔧 CONFIGURATION CUSTOM :
   IntraSphere.activateWithConfig({
     enableWebSocket: true,
     enableServiceWorker: false,
     enableCaching: true
   })

📊 MONITORING :
   IntraSphere.status()           // État des fonctionnalités
   IntraSphere.refresh()          // Rafraîchir le cache
   IntraSphere.clearCache()       // Vider le cache

Tapez une commande dans la console pour commencer !
  `);
}