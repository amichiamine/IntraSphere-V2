/**
 * Process monitoring and cleanup utilities
 * Prevents port conflicts and ensures clean startup
 */

import { exec } from 'child_process';
import { promisify } from 'util';

const execAsync = promisify(exec);

/**
 * Check if a port is in use
 */
export async function isPortInUse(port: number): Promise<boolean> {
  try {
    const { stdout } = await execAsync(`ss -tulpn 2>/dev/null | grep :${port} || true`);
    return stdout.trim().length > 0;
  } catch {
    // Fallback method if ss is not available
    try {
      const { stdout } = await execAsync(`netstat -tulpn 2>/dev/null | grep :${port} || true`);
      return stdout.trim().length > 0;
    } catch {
      return false;
    }
  }
}

/**
 * Find processes using a specific port
 */
export async function findProcessesOnPort(port: number): Promise<string[]> {
  try {
    const { stdout } = await execAsync(`lsof -ti:${port} 2>/dev/null || true`);
    return stdout.trim().split('\n').filter(pid => pid.length > 0);
  } catch {
    return [];
  }
}

/**
 * Clean up orphaned Node.js processes
 */
export async function cleanupOrphanedProcesses(): Promise<void> {
  try {
    // Find all tsx/node processes that might be orphaned
    const { stdout } = await execAsync('ps aux | grep -E "(tsx|node.*server)" | grep -v grep || true');
    const processes = stdout.trim().split('\n').filter(line => line.length > 0);
    
    for (const process of processes) {
      const parts = process.trim().split(/\s+/);
      if (parts.length > 1) {
        const pid = parts[1];
        const command = parts.slice(10).join(' ');
        
        // Only kill processes that look like our server
        if (command.includes('server/index.ts') || command.includes('tsx server')) {
          try {
            await execAsync(`kill -TERM ${pid} 2>/dev/null || true`);
            console.log(`Cleaned up orphaned process ${pid}: ${command}`);
          } catch {
            // Process might already be gone
          }
        }
      }
    }
  } catch {
    // Cleanup failed, but continue anyway
  }
}

/**
 * Ensure port is available for use
 */
export async function ensurePortAvailable(port: number, maxRetries = 5): Promise<void> {
  let retries = 0;
  
  while (retries < maxRetries) {
    const inUse = await isPortInUse(port);
    
    if (!inUse) {
      return; // Port is available
    }
    
    console.log(`Port ${port} is in use, attempt ${retries + 1}/${maxRetries} to clean up...`);
    
    // Try to find and terminate processes using the port
    const pids = await findProcessesOnPort(port);
    
    for (const pid of pids) {
      try {
        await execAsync(`kill -TERM ${pid} 2>/dev/null || true`);
        console.log(`Terminated process ${pid} using port ${port}`);
      } catch {
        // Process might already be gone
      }
    }
    
    // Also do general cleanup
    await cleanupOrphanedProcesses();
    
    // Wait a bit for processes to terminate
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    retries++;
  }
  
  // If we get here, port cleanup failed
  throw new Error(`Unable to free port ${port} after ${maxRetries} attempts`);
}

/**
 * Monitor server health and restart if needed
 */
export class ServerMonitor {
  private healthCheckInterval?: NodeJS.Timeout;
  private port: number;
  private healthCheckUrl: string;
  
  constructor(port: number) {
    this.port = port;
    this.healthCheckUrl = `http://localhost:${port}/health`;
  }
  
  start(): void {
    // Check server health every 30 seconds
    this.healthCheckInterval = setInterval(async () => {
      try {
        const response = await fetch(this.healthCheckUrl, { 
          signal: AbortSignal.timeout(5000) 
        });
        
        if (!response.ok) {
          console.warn(`Health check failed with status: ${response.status}`);
        }
      } catch (error) {
        console.warn(`Health check failed: ${error}`);
      }
    }, 30000);
  }
  
  stop(): void {
    if (this.healthCheckInterval) {
      clearInterval(this.healthCheckInterval);
      this.healthCheckInterval = undefined;
    }
  }
}