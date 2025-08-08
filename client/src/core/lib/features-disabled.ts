/**
 * Disabled Features Stub
 * Prevents automatic loading of advanced features that cause unhandled promise rejections
 */

// This file replaces the automatic imports to prevent errors
// Advanced features are available via manual activation only

export const disabledFeatures = {
  reason: 'Preventing unhandled promise rejections during development',
  manualActivation: 'Use IntraSphere.activate() in console to enable',
  availableFeatures: [
    'WebSocket Real-time Communication',
    'Service Worker PWA Capabilities', 
    'Intelligent Client-side Caching',
    'Offline Mode Support',
    'Background Sync'
  ]
};

// Log information about disabled features
console.log('🔒 Advanced features disabled by default');
console.log('💡 Type IntraSphere.activate() in console to enable all features');
console.log('📖 See browser console for full activation commands');