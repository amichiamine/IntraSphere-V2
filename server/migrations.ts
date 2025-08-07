import { AuthService } from './auth';
import { storage } from './storage';
import type { InsertUser } from '@shared/schema';

/**
 * Migration to hash existing plain text passwords
 */
export async function migratePasswordsToHash(): Promise<void> {
  console.log('Starting password migration...');

  try {
    const users = await storage.getUsers();
    
    for (const user of users) {
      // Check if password is already hashed (bcrypt hashes start with $2b$)
      if (!user.password.startsWith('$2b$')) {
        console.log(`Migrating password for user: ${user.username}`);
        
        // Hash the plain text password
        const hashedPassword = await AuthService.hashPassword(user.password);
        
        // Update user with hashed password
        await storage.updateUser(user.id, { password: hashedPassword });
        
        console.log(`✅ Password migrated for user: ${user.username}`);
      } else {
        console.log(`⏭️  Password already hashed for user: ${user.username}`);
      }
    }
    
    console.log('✅ Password migration completed successfully');
  } catch (error) {
    console.error('❌ Password migration failed:', error);
    throw error;
  }
}

/**
 * Create default admin user if none exists
 */
export async function ensureDefaultAdmin(): Promise<void> {
  try {
    const users = await storage.getUsers();
    const adminExists = users.some(user => user.role === 'admin');
    
    if (!adminExists) {
      console.log('Creating default admin user...');
      
      const hashedPassword = await AuthService.hashPassword('admin123!');
      
      const adminUser: InsertUser = {
        username: 'admin',
        password: hashedPassword,
        name: 'Administrateur',
        role: 'admin',
        email: 'admin@intrasphere.com',
        isActive: true,
        employeeId: 'ADMIN001',
        department: 'Direction',
        position: 'Administrateur système',
      };
      
      await storage.createUser(adminUser);
      
      console.log('✅ Default admin user created');
      console.log('   Username: admin');
      console.log('   Password: admin123!');
      console.log('   ⚠️  Please change the default password after first login');
    }
  } catch (error) {
    console.error('❌ Failed to create default admin:', error);
  }
}

/**
 * Run all migrations
 */
export async function runMigrations(): Promise<void> {
  console.log('🔄 Running database migrations...');
  
  await migratePasswordsToHash();
  await ensureDefaultAdmin();
  
  console.log('✅ All migrations completed');
}