import nodemailer from 'nodemailer';
import type { Transporter } from 'nodemailer';

export interface EmailConfig {
  host: string;
  port: number;
  secure: boolean;
  auth: {
    user: string;
    pass: string;
  };
}

export interface EmailOptions {
  to: string | string[];
  subject: string;
  text?: string;
  html?: string;
  from?: string;
}

export class EmailService {
  private transporter: Transporter | null = null;
  private isConfigured = false;

  /**
   * Configure email service with SMTP settings
   */
  configure(config: EmailConfig): void {
    try {
      this.transporter = nodemailer.createTransport({
        host: config.host,
        port: config.port,
        secure: config.secure,
        auth: config.auth,
      });

      this.isConfigured = true;
      console.log('Email service configured successfully');
    } catch (error) {
      console.error('Failed to configure email service:', error);
      this.isConfigured = false;
    }
  }

  /**
   * Send an email
   */
  async sendEmail(options: EmailOptions): Promise<boolean> {
    if (!this.isConfigured || !this.transporter) {
      console.warn('Email service not configured. Email not sent.');
      return false;
    }

    try {
      const mailOptions = {
        from: options.from || process.env.EMAIL_FROM || 'noreply@intrasphere.com',
        to: Array.isArray(options.to) ? options.to.join(', ') : options.to,
        subject: options.subject,
        text: options.text,
        html: options.html || options.text,
      };

      const result = await this.transporter.sendMail(mailOptions);
      console.log('Email sent successfully:', result.messageId);
      return true;
    } catch (error) {
      console.error('Failed to send email:', error);
      return false;
    }
  }

  /**
   * Send welcome email to new user
   */
  async sendWelcomeEmail(userEmail: string, userName: string, tempPassword?: string): Promise<boolean> {
    const subject = 'Bienvenue sur IntraSphere';
    const html = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #8B5CF6;">Bienvenue sur IntraSphere !</h2>
        <p>Bonjour <strong>${userName}</strong>,</p>
        <p>Votre compte IntraSphere a été créé avec succès. Vous pouvez maintenant accéder à votre portail d'entreprise.</p>
        ${tempPassword ? `
          <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Mot de passe temporaire :</strong> <code>${tempPassword}</code></p>
            <p style="color: #ef4444; font-size: 14px;">⚠️ Veuillez changer votre mot de passe lors de votre première connexion.</p>
          </div>
        ` : ''}
        <p>Fonctionnalités disponibles :</p>
        <ul>
          <li>📢 Annonces et communications</li>
          <li>📁 Bibliothèque documentaire</li>
          <li>💬 Forum et discussions</li>
          <li>📧 Messagerie interne</li>
          <li>📚 Plateforme e-learning</li>
        </ul>
        <p>Cordialement,<br>L'équipe IntraSphere</p>
      </div>
    `;

    return this.sendEmail({
      to: userEmail,
      subject,
      html,
    });
  }

  /**
   * Send password reset email
   */
  async sendPasswordResetEmail(userEmail: string, userName: string, resetLink: string): Promise<boolean> {
    const subject = 'Réinitialisation de votre mot de passe IntraSphere';
    const html = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #8B5CF6;">Réinitialisation de mot de passe</h2>
        <p>Bonjour <strong>${userName}</strong>,</p>
        <p>Vous avez demandé la réinitialisation de votre mot de passe IntraSphere.</p>
        <div style="text-align: center; margin: 30px 0;">
          <a href="${resetLink}" style="background: #8B5CF6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block;">
            Réinitialiser mon mot de passe
          </a>
        </div>
        <p style="color: #6b7280; font-size: 14px;">
          Ce lien expire dans 1 heure. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
        </p>
        <p>Cordialement,<br>L'équipe IntraSphere</p>
      </div>
    `;

    return this.sendEmail({
      to: userEmail,
      subject,
      html,
    });
  }

  /**
   * Send notification email
   */
  async sendNotificationEmail(
    userEmail: string, 
    userName: string, 
    notificationType: string, 
    content: string
  ): Promise<boolean> {
    const subject = `IntraSphere - ${notificationType}`;
    const html = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #8B5CF6;">${notificationType}</h2>
        <p>Bonjour <strong>${userName}</strong>,</p>
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
          ${content}
        </div>
        <p>Connectez-vous à IntraSphere pour plus de détails.</p>
        <p>Cordialement,<br>L'équipe IntraSphere</p>
      </div>
    `;

    return this.sendEmail({
      to: userEmail,
      subject,
      html,
    });
  }

  /**
   * Test email configuration
   */
  async testConfiguration(): Promise<boolean> {
    if (!this.isConfigured || !this.transporter) {
      return false;
    }

    try {
      await this.transporter.verify();
      console.log('Email configuration test successful');
      return true;
    } catch (error) {
      console.error('Email configuration test failed:', error);
      return false;
    }
  }
}

// Singleton instance
export const emailService = new EmailService();

// Auto-configure if environment variables are available
if (process.env.SMTP_HOST && process.env.SMTP_USER && process.env.SMTP_PASS) {
  emailService.configure({
    host: process.env.SMTP_HOST,
    port: parseInt(process.env.SMTP_PORT || '587'),
    secure: process.env.SMTP_SECURE === 'true',
    auth: {
      user: process.env.SMTP_USER,
      pass: process.env.SMTP_PASS,
    },
  });
}