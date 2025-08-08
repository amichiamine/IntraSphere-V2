import multer from 'multer';
import path from 'path';
import { randomUUID } from 'crypto';
import fs from 'fs/promises';

// Configuration du stockage
const storage = multer.diskStorage({
  destination: async (req, file, cb) => {
    const uploadDir = 'server/public/uploads';
    try {
      await fs.mkdir(uploadDir, { recursive: true });
      cb(null, uploadDir);
    } catch (error) {
      cb(error, uploadDir);
    }
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = `${Date.now()}-${randomUUID()}`;
    const ext = path.extname(file.originalname);
    const baseName = path.basename(file.originalname, ext);
    const sanitizedName = baseName.replace(/[^a-zA-Z0-9]/g, '_');
    cb(null, `${sanitizedName}-${uniqueSuffix}${ext}`);
  }
});

// Filtres de fichiers
const fileFilter = (req: any, file: Express.Multer.File, cb: multer.FileFilterCallback) => {
  // Types de fichiers autorisés
  const allowedTypes = {
    image: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    document: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    video: ['video/mp4', 'video/webm', 'video/ogg'],
    audio: ['audio/mpeg', 'audio/wav', 'audio/ogg']
  };

  const allAllowedTypes = [
    ...allowedTypes.image,
    ...allowedTypes.document,
    ...allowedTypes.video,
    ...allowedTypes.audio
  ];

  if (allAllowedTypes.includes(file.mimetype)) {
    cb(null, true);
  } else {
    cb(new Error(`Type de fichier non autorisé: ${file.mimetype}`));
  }
};

// Configuration multer
export const upload = multer({
  storage,
  fileFilter,
  limits: {
    fileSize: 50 * 1024 * 1024, // 50MB max
    files: 5 // Maximum 5 fichiers par upload
  }
});

// Utilitaires pour la gestion des fichiers
export class FileManager {
  static async deleteFile(filePath: string): Promise<void> {
    try {
      await fs.unlink(filePath);
    } catch (error) {
      console.error('Erreur lors de la suppression du fichier:', error);
    }
  }

  static async getFileInfo(filePath: string): Promise<{ size: number; exists: boolean }> {
    try {
      const stats = await fs.stat(filePath);
      return { size: stats.size, exists: true };
    } catch (error) {
      return { size: 0, exists: false };
    }
  }

  static getFileUrl(filename: string): string {
    return `/uploads/${filename}`;
  }

  static getFileType(mimetype: string): string {
    if (mimetype.startsWith('image/')) return 'image';
    if (mimetype.startsWith('video/')) return 'video';
    if (mimetype.startsWith('audio/')) return 'audio';
    if (mimetype.includes('pdf') || mimetype.includes('document') || mimetype.includes('word')) return 'document';
    return 'other';
  }

  static formatFileSize(bytes: number): string {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }
}

// Middleware pour traiter les uploads
export const handleUpload = (fieldName: string, multiple: boolean = false) => {
  return multiple ? upload.array(fieldName, 5) : upload.single(fieldName);
};

// Middleware pour traiter les uploads multiples avec différents champs
export const handleMultipleUploads = upload.fields([
  { name: 'documents', maxCount: 5 },
  { name: 'images', maxCount: 10 },
  { name: 'avatar', maxCount: 1 }
]);

// Types pour les fichiers uploadés
export interface UploadedFile {
  id: string;
  originalName: string;
  fileName: string;
  filePath: string;
  fileUrl: string;
  mimeType: string;
  fileType: string;
  size: number;
  formattedSize: string;
  uploadedAt: Date;
  uploadedBy: string;
}

export function processUploadedFile(file: Express.Multer.File, userId: string): UploadedFile {
  return {
    id: randomUUID(),
    originalName: file.originalname,
    fileName: file.filename,
    filePath: file.path,
    fileUrl: FileManager.getFileUrl(file.filename),
    mimeType: file.mimetype,
    fileType: FileManager.getFileType(file.mimetype),
    size: file.size,
    formattedSize: FileManager.formatFileSize(file.size),
    uploadedAt: new Date(),
    uploadedBy: userId
  };
}