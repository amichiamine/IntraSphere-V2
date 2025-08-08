import type { Express } from "express";
import { storage } from "../data/storage";
import { 
  insertTrainingSchema, 
  insertTrainingParticipantSchema,
  insertCourseSchema,
  insertLessonSchema,
  insertResourceSchema
} from "@shared/schema";

// Authentication middleware
const requireAuth = (req: any, res: any, next: any) => {
  if (!req.session?.userId) {
    return res.status(401).json({ message: "Authentication required" });
  }
  next();
};

const requireRole = (roles: string[]) => {
  return async (req: any, res: any, next: any) => {
    if (!req.session?.userId) {
      return res.status(401).json({ message: "Authentication required" });
    }
    
    const user = await storage.getUser(req.session.userId!);
    if (!user || !roles.includes(user.role)) {
      return res.status(403).json({ message: "Insufficient permissions" });
    }
    
    req.user = user;
    next();
  };
};

export function registerTrainingRoutes(app: Express): void {
  // Training routes
  app.get("/api/trainings", async (req, res) => {
    try {
      const trainings = await storage.getTrainings();
      res.json(trainings);
    } catch (error) {
      console.error("Error fetching trainings:", error);
      res.status(500).json({ error: "Failed to fetch trainings" });
    }
  });

  app.get("/api/trainings/:id", async (req, res) => {
    try {
      const training = await storage.getTrainingById(req.params.id);
      if (!training) {
        return res.status(404).json({ error: "Training not found" });
      }
      res.json(training);
    } catch (error) {
      console.error("Error fetching training:", error);
      res.status(500).json({ error: "Failed to fetch training" });
    }
  });

  app.post("/api/trainings", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      // Check permissions
      const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
      if (!hasPermission) {
        return res.status(403).json({ message: "Insufficient permissions to create trainings" });
      }

      const result = insertTrainingSchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid training data", errors: result.error.issues });
      }

      const training = await storage.createTraining(result.data);
      res.status(201).json(training);
    } catch (error) {
      console.error("Error creating training:", error);
      res.status(500).json({ error: "Failed to create training" });
    }
  });

  app.put("/api/trainings/:id", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      // Check permissions
      const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
      if (!hasPermission) {
        return res.status(403).json({ message: "Insufficient permissions to update trainings" });
      }

      const result = insertTrainingSchema.partial().safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid training data", errors: result.error.issues });
      }

      const training = await storage.updateTraining(req.params.id, result.data);
      res.json(training);
    } catch (error: any) {
      console.error("Error updating training:", error);
      if (error.message === "Training not found") {
        return res.status(404).json({ error: "Training not found" });
      }
      res.status(500).json({ error: "Failed to update training" });
    }
  });

  app.delete("/api/trainings/:id", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      // Check permissions
      const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
      if (!hasPermission) {
        return res.status(403).json({ message: "Insufficient permissions to delete trainings" });
      }

      await storage.deleteTraining(req.params.id);
      res.json({ message: "Training deleted successfully" });
    } catch (error) {
      console.error("Error deleting training:", error);
      res.status(500).json({ error: "Failed to delete training" });
    }
  });

  // Training participants routes
  app.get("/api/trainings/:id/participants", async (req, res) => {
    try {
      const participants = await storage.getTrainingParticipants(req.params.id);
      res.json(participants);
    } catch (error) {
      console.error("Error fetching training participants:", error);
      res.status(500).json({ error: "Failed to fetch training participants" });
    }
  });

  app.post("/api/trainings/:id/participants", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const trainingId = req.params.id;

      const participant = await storage.addTrainingParticipant({
        trainingId,
        userId,
        status: "registered"
      });

      res.status(201).json(participant);
    } catch (error) {
      console.error("Error adding training participant:", error);
      res.status(500).json({ error: "Failed to register for training" });
    }
  });

  app.delete("/api/trainings/:id/participants/:userId", requireAuth, async (req, res) => {
    try {
      const currentUserId = req.session.userId!;
      const { id: trainingId, userId } = req.params;

      // Only allow users to remove themselves or admin/training managers to remove anyone
      if (currentUserId !== userId) {
        const user = await storage.getUser(currentUserId);
        if (!user) {
          return res.status(401).json({ message: "User not found" });
        }
        const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
        if (!hasPermission) {
          return res.status(403).json({ message: "Insufficient permissions to remove participant" });
        }
      }

      await storage.removeTrainingParticipant(trainingId, userId);
      res.json({ message: "Participant removed successfully" });
    } catch (error) {
      console.error("Error removing training participant:", error);
      res.status(500).json({ error: "Failed to remove participant" });
    }
  });

  app.get("/api/users/:userId/trainings", requireAuth, async (req, res) => {
    try {
      const currentUserId = req.session.userId!;
      const { userId } = req.params;

      // Only allow users to view their own trainings or admin/training managers to view anyone's
      if (currentUserId !== userId) {
        const user = await storage.getUser(currentUserId);
        if (!user) {
          return res.status(401).json({ message: "User not found" });
        }
        const hasPermission = await storage.hasPermission(user.id, "manage_trainings") || user.role === "admin";
        if (!hasPermission) {
          return res.status(403).json({ message: "Insufficient permissions to view user trainings" });
        }
      }

      const participations = await storage.getUserTrainingParticipations(userId);
      res.json(participations);
    } catch (error) {
      console.error("Error fetching user trainings:", error);
      res.status(500).json({ error: "Failed to fetch user trainings" });
    }
  });

  // E-Learning API Routes

  // Courses
  app.get("/api/courses", requireAuth, async (req, res) => {
    try {
      const courses = await storage.getCourses();
      res.json(courses);
    } catch (error) {
      console.error("Error fetching courses:", error);
      res.status(500).json({ error: "Failed to fetch courses" });
    }
  });

  app.get("/api/courses/:id", requireAuth, async (req, res) => {
    try {
      const course = await storage.getCourseById(req.params.id);
      if (!course) {
        return res.status(404).json({ error: "Course not found" });
      }
      res.json(course);
    } catch (error) {
      console.error("Error fetching course:", error);
      res.status(500).json({ error: "Failed to fetch course" });
    }
  });

  app.post("/api/courses", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const validatedData = insertCourseSchema.parse(req.body);
      const course = await storage.createCourse(validatedData);
      res.status(201).json(course);
    } catch (error) {
      console.error("Error creating course:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid course data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create course" });
    }
  });

  app.put("/api/courses/:id", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const validatedData = insertCourseSchema.parse(req.body);
      const course = await storage.updateCourse(req.params.id, validatedData);
      if (!course) {
        return res.status(404).json({ error: "Course not found" });
      }
      res.json(course);
    } catch (error) {
      console.error("Error updating course:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid course data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to update course" });
    }
  });

  app.delete("/api/courses/:id", requireRole(["admin"]), async (req, res) => {
    try {
      await storage.deleteCourse(req.params.id);
      res.json({ message: "Course deleted successfully" });
    } catch (error) {
      console.error("Error deleting course:", error);
      res.status(500).json({ error: "Failed to delete course" });
    }
  });

  // Lessons
  app.get("/api/courses/:courseId/lessons", requireAuth, async (req, res) => {
    try {
      const lessons = await storage.getLessons(req.params.courseId);
      res.json(lessons);
    } catch (error) {
      console.error("Error fetching lessons:", error);
      res.status(500).json({ error: "Failed to fetch lessons" });
    }
  });

  app.get("/api/lessons/:id", requireAuth, async (req, res) => {
    try {
      const lesson = await storage.getLessonById(req.params.id);
      if (!lesson) {
        return res.status(404).json({ error: "Lesson not found" });
      }
      res.json(lesson);
    } catch (error) {
      console.error("Error fetching lesson:", error);
      res.status(500).json({ error: "Failed to fetch lesson" });
    }
  });

  app.post("/api/lessons", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const validatedData = insertLessonSchema.parse(req.body);
      const lesson = await storage.createLesson(validatedData);
      res.status(201).json(lesson);
    } catch (error) {
      console.error("Error creating lesson:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid lesson data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create lesson" });
    }
  });

  app.put("/api/lessons/:id", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const validatedData = insertLessonSchema.omit({ courseId: true }).parse(req.body);
      const lesson = await storage.updateLesson(req.params.id, validatedData);
      if (!lesson) {
        return res.status(404).json({ error: "Lesson not found" });
      }
      res.json(lesson);
    } catch (error) {
      console.error("Error updating lesson:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid lesson data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to update lesson" });
    }
  });

  app.delete("/api/lessons/:id", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteLesson(req.params.id);
      res.json({ message: "Lesson deleted successfully" });
    } catch (error) {
      console.error("Error deleting lesson:", error);
      res.status(500).json({ error: "Failed to delete lesson" });
    }
  });

  // Enrollments
  app.get("/api/my-enrollments", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const enrollments = await storage.getUserEnrollments(userId);
      res.json(enrollments);
    } catch (error) {
      console.error("Error fetching enrollments:", error);
      res.status(500).json({ error: "Failed to fetch enrollments" });
    }
  });

  app.post("/api/enroll/:courseId", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const courseId = req.params.courseId;
      
      // Check if already enrolled
      const existingEnrollments = await storage.getUserEnrollments(userId);
      const existingEnrollment = existingEnrollments.find(e => e.courseId === courseId);
      
      if (existingEnrollment) {
        return res.status(400).json({ error: "Already enrolled in this course" });
      }
      
      const enrollment = await storage.enrollUser(userId, courseId);
      res.status(201).json(enrollment);
    } catch (error) {
      console.error("Error enrolling user:", error);
      res.status(500).json({ error: "Failed to enroll in course" });
    }
  });

  // Progress tracking
  app.post("/api/lessons/:lessonId/complete", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const lessonId = req.params.lessonId;
      const { courseId } = req.body;
      
      const progress = await storage.updateLessonProgress(userId, lessonId, courseId, true);
      res.json(progress);
    } catch (error) {
      console.error("Error updating lesson progress:", error);
      res.status(500).json({ error: "Failed to update lesson progress" });
    }
  });

  app.get("/api/courses/:courseId/my-progress", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const courseId = req.params.courseId;
      
      const progress = await storage.getUserLessonProgress(userId, courseId);
      res.json(progress);
    } catch (error) {
      console.error("Error fetching progress:", error);
      res.status(500).json({ error: "Failed to fetch progress" });
    }
  });

  // Resources
  app.get("/api/resources", requireAuth, async (req, res) => {
    try {
      const resources = await storage.getResources();
      res.json(resources);
    } catch (error) {
      console.error("Error fetching resources:", error);
      res.status(500).json({ error: "Failed to fetch resources" });
    }
  });

  app.post("/api/resources", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const validatedData = insertResourceSchema.parse(req.body);
      const resource = await storage.createResource(validatedData);
      res.status(201).json(resource);
    } catch (error) {
      console.error("Error creating resource:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid resource data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to create resource" });
    }
  });

  app.put("/api/resources/:id", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      const validatedData = insertResourceSchema.parse(req.body);
      const resource = await storage.updateResource(req.params.id, validatedData);
      if (!resource) {
        return res.status(404).json({ error: "Resource not found" });
      }
      res.json(resource);
    } catch (error) {
      console.error("Error updating resource:", error);
      if (error instanceof Error && error.name === "ZodError") {
        return res.status(400).json({ error: "Invalid resource data", details: (error as any).errors });
      }
      res.status(500).json({ error: "Failed to update resource" });
    }
  });

  app.delete("/api/resources/:id", requireRole(["admin", "moderator"]), async (req, res) => {
    try {
      await storage.deleteResource(req.params.id);
      res.json({ message: "Resource deleted successfully" });
    } catch (error) {
      console.error("Error deleting resource:", error);
      res.status(500).json({ error: "Failed to delete resource" });
    }
  });

  // Certificates
  app.get("/api/my-certificates", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const certificates = await storage.getUserCertificates(userId);
      res.json(certificates);
    } catch (error) {
      console.error("Error fetching certificates:", error);
      res.status(500).json({ error: "Failed to fetch certificates" });
    }
  });
}