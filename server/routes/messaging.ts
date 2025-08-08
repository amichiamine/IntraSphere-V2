import type { Express } from "express";
import { storage } from "../data/storage";
import { 
  insertMessageSchema, 
  insertComplaintSchema,
  insertForumCategorySchema,
  insertForumTopicSchema,
  insertForumPostSchema,
  insertForumLikeSchema
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

export function registerMessagingRoutes(app: Express): void {
  // Messages routes
  app.get("/api/messages", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const messages = await storage.getMessages(userId);
      res.json(messages);
    } catch (error) {
      console.error("Error fetching messages:", error);
      res.status(500).json({ error: "Failed to fetch messages" });
    }
  });

  app.get("/api/messages/sent", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      // Get messages sent by current user
      const allMessages = await storage.getMessages(userId);
      const sentMessages = allMessages.filter(msg => msg.senderId === userId);
      res.json(sentMessages);
    } catch (error) {
      console.error("Error fetching sent messages:", error);
      res.status(500).json({ error: "Failed to fetch sent messages" });
    }
  });

  app.get("/api/messages/:id", requireAuth, async (req, res) => {
    try {
      const message = await storage.getMessageById(req.params.id);
      if (!message) {
        return res.status(404).json({ message: "Message not found" });
      }
      
      // Check if user is sender or recipient
      const userId = req.session.userId!;
      if (message.senderId !== userId && message.recipientId !== userId) {
        return res.status(403).json({ message: "Access denied" });
      }
      
      res.json(message);
    } catch (error) {
      console.error("Error fetching message:", error);
      res.status(500).json({ error: "Failed to fetch message" });
    }
  });

  app.post("/api/messages", requireAuth, async (req, res) => {
    try {
      const result = insertMessageSchema.safeParse({
        ...req.body,
        senderId: req.session.userId!
      });
      
      if (!result.success) {
        return res.status(400).json({ message: "Invalid message data", errors: result.error.issues });
      }
      
      const message = await storage.createMessage(result.data);
      res.status(201).json(message);
    } catch (error) {
      console.error("Error creating message:", error);
      res.status(500).json({ error: "Failed to create message" });
    }
  });

  app.put("/api/messages/:id/read", requireAuth, async (req, res) => {
    try {
      const { id } = req.params;
      const message = await storage.getMessageById(id);
      
      if (!message) {
        return res.status(404).json({ message: "Message not found" });
      }
      
      // Only recipient can mark message as read
      if (message.recipientId !== req.session.userId!) {
        return res.status(403).json({ message: "Access denied" });
      }
      
      await storage.markMessageAsRead(id);
      res.json({ success: true });
    } catch (error) {
      console.error("Error marking message as read:", error);
      res.status(500).json({ error: "Failed to mark message as read" });
    }
  });

  app.delete("/api/messages/:id", requireAuth, async (req, res) => {
    try {
      const { id } = req.params;
      const message = await storage.getMessageById(id);
      
      if (!message) {
        return res.status(404).json({ message: "Message not found" });
      }
      
      // Only sender can delete message
      if (message.senderId !== req.session.userId!) {
        return res.status(403).json({ message: "Access denied" });
      }
      
      await storage.deleteMessage(id);
      res.json({ success: true });
    } catch (error) {
      console.error("Error deleting message:", error);
      res.status(500).json({ error: "Failed to delete message" });
    }
  });

  // FORUM ROUTES IMPLEMENTATION
  
  // Forum Categories
  app.get("/api/forum/categories", async (req, res) => {
    try {
      const categories = await storage.getForumCategories();
      res.json(categories);
    } catch (error) {
      console.error("Error fetching forum categories:", error);
      res.status(500).json({ error: "Failed to fetch forum categories" });
    }
  });

  app.get("/api/forum/categories/:id", async (req, res) => {
    try {
      const category = await storage.getForumCategoryById(req.params.id);
      if (!category) {
        return res.status(404).json({ message: "Category not found" });
      }
      res.json(category);
    } catch (error) {
      console.error("Error fetching forum category:", error);
      res.status(500).json({ error: "Failed to fetch forum category" });
    }
  });

  app.post("/api/forum/categories", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const result = insertForumCategorySchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid category data", errors: result.error.issues });
      }
      
      const category = await storage.createForumCategory(result.data);
      res.status(201).json(category);
    } catch (error) {
      console.error("Error creating forum category:", error);
      res.status(500).json({ error: "Failed to create forum category" });
    }
  });

  // Forum Topics
  app.get("/api/forum/topics", async (req, res) => {
    try {
      const { categoryId } = req.query;
      const topics = await storage.getForumTopics(categoryId as string);
      res.json(topics);
    } catch (error) {
      console.error("Error fetching forum topics:", error);
      res.status(500).json({ error: "Failed to fetch forum topics" });
    }
  });

  app.get("/api/forum/topics/:id", async (req, res) => {
    try {
      const topic = await storage.getForumTopicById(req.params.id);
      if (!topic) {
        return res.status(404).json({ message: "Topic not found" });
      }
      res.json(topic);
    } catch (error) {
      console.error("Error fetching forum topic:", error);
      res.status(500).json({ error: "Failed to fetch forum topic" });
    }
  });

  app.get("/api/forum/topics/:id/posts", async (req, res) => {
    try {
      const posts = await storage.getForumPosts(req.params.id);
      res.json(posts);
    } catch (error) {
      console.error("Error fetching forum posts:", error);
      res.status(500).json({ error: "Failed to fetch forum posts" });
    }
  });

  app.post("/api/forum/topics", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      const result = insertForumTopicSchema.safeParse({
        ...req.body,
        authorId: user.id,
        authorName: user.name
      });
      
      if (!result.success) {
        return res.status(400).json({ message: "Invalid topic data", errors: result.error.issues });
      }
      
      const topic = await storage.createForumTopic(result.data);
      res.status(201).json(topic);
    } catch (error) {
      console.error("Error creating forum topic:", error);
      res.status(500).json({ error: "Failed to create forum topic" });
    }
  });

  // Forum Posts
  app.post("/api/forum/posts", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      const result = insertForumPostSchema.safeParse({
        ...req.body,
        authorId: user.id,
        authorName: user.name
      });
      
      if (!result.success) {
        return res.status(400).json({ message: "Invalid post data", errors: result.error.issues });
      }
      
      const post = await storage.createForumPost(result.data);
      
      // Update topic post count and last activity
      await storage.updateForumTopicActivity(result.data.topicId);
      
      res.status(201).json(post);
    } catch (error) {
      console.error("Error creating forum post:", error);
      res.status(500).json({ error: "Failed to create forum post" });
    }
  });

  app.put("/api/forum/posts/:id", requireAuth, async (req, res) => {
    try {
      const post = await storage.getForumPostById(req.params.id);
      if (!post) {
        return res.status(404).json({ message: "Post not found" });
      }
      
      // Only author or moderator can edit
      const user = await storage.getUser(req.session.userId!);
      if (!user || (post.authorId !== user.id && user.role !== 'admin' && user.role !== 'moderator')) {
        return res.status(403).json({ message: "Access denied" });
      }
      
      const updatedPost = await storage.updateForumPost(req.params.id, {
        content: req.body.content,
        isEdited: true
      });
      
      res.json(updatedPost);
    } catch (error) {
      console.error("Error updating forum post:", error);
      res.status(500).json({ error: "Failed to update forum post" });
    }
  });

  app.delete("/api/forum/posts/:id", requireAuth, async (req, res) => {
    try {
      const post = await storage.getForumPostById(req.params.id);
      if (!post) {
        return res.status(404).json({ message: "Post not found" });
      }
      
      // Only author or moderator can delete
      const user = await storage.getUser(req.session.userId!);
      if (!user || (post.authorId !== user.id && user.role !== 'admin' && user.role !== 'moderator')) {
        return res.status(403).json({ message: "Access denied" });
      }
      
      await storage.deleteForumPost(req.params.id);
      res.json({ success: true });
    } catch (error) {
      console.error("Error deleting forum post:", error);
      res.status(500).json({ error: "Failed to delete forum post" });
    }
  });

  // Forum Likes
  app.post("/api/forum/posts/:id/like", requireAuth, async (req, res) => {
    try {
      const { id: postId } = req.params;
      const userId = req.session.userId!;
      
      // Check if post exists
      const post = await storage.getForumPostById(postId);
      if (!post) {
        return res.status(404).json({ message: "Post not found" });
      }
      
      // Check if user already liked this post
      const existingLike = await storage.getForumLike(postId, userId);
      if (existingLike) {
        // Remove like
        await storage.deleteForumLike(postId, userId);
        res.json({ liked: false, message: "Like removed" });
      } else {
        // Add like
        await storage.createForumLike({
          postId,
          userId,
          reactionType: req.body.reactionType || 'like'
        });
        res.json({ liked: true, message: "Post liked" });
      }
    } catch (error) {
      console.error("Error handling forum like:", error);
      res.status(500).json({ error: "Failed to handle like" });
    }
  });

  app.delete("/api/messages/:id", requireAuth, async (req, res) => {
    try {
      const { id } = req.params;
      const message = await storage.getMessageById(id);
      
      if (!message) {
        return res.status(404).json({ message: "Message not found" });
      }
      
      // Only sender or recipient can delete message
      const userId = req.session.userId!;
      if (message.senderId !== userId && message.recipientId !== userId) {
        return res.status(403).json({ message: "Access denied" });
      }
      
      await storage.deleteMessage(id);
      res.json({ message: "Message deleted successfully" });
    } catch (error) {
      console.error("Error deleting message:", error);
      res.status(500).json({ error: "Failed to delete message" });
    }
  });

  // Complaints routes - Complete CRUD
  app.get("/api/complaints", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const user = await storage.getUser(userId);
      
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }
      
      // Admin and moderators see all complaints, users see only their own
      let complaints;
      if (user.role === 'admin' || user.role === 'moderator') {
        complaints = await storage.getComplaints();
      } else {
        complaints = await storage.getComplaintsByUser(userId);
      }
      
      res.json(complaints);
    } catch (error) {
      console.error("Error fetching complaints:", error);
      res.status(500).json({ error: "Failed to fetch complaints" });
    }
  });

  app.get("/api/complaints/:id", requireAuth, async (req, res) => {
    try {
      const complaint = await storage.getComplaintById(req.params.id);
      if (!complaint) {
        return res.status(404).json({ message: "Complaint not found" });
      }
      
      // Check access permissions
      const userId = req.session.userId!;
      const user = await storage.getUser(userId);
      
      if (user?.role !== 'admin' && user?.role !== 'moderator' && complaint.submitterId !== userId) {
        return res.status(403).json({ message: "Access denied" });
      }
      
      res.json(complaint);
    } catch (error) {
      console.error("Error fetching complaint:", error);
      res.status(500).json({ error: "Failed to fetch complaint" });
    }
  });

  app.post("/api/complaints", requireAuth, async (req, res) => {
    try {
      const result = insertComplaintSchema.safeParse({
        ...req.body,
        submitterId: req.session.userId!
      });
      
      if (!result.success) {
        return res.status(400).json({ message: "Invalid complaint data", errors: result.error.issues });
      }
      
      const complaint = await storage.createComplaint(result.data);
      res.status(201).json(complaint);
    } catch (error) {
      console.error("Error creating complaint:", error);
      res.status(500).json({ error: "Failed to create complaint" });
    }
  });

  app.patch("/api/complaints/:id", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      const updatedComplaint = await storage.updateComplaint(id, req.body);
      res.json(updatedComplaint);
    } catch (error) {
      console.error("Error updating complaint:", error);
      res.status(500).json({ error: "Failed to update complaint" });
    }
  });

  app.put("/api/complaints/:id/assign", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      const { assignedToId } = req.body;
      
      const updatedComplaint = await storage.updateComplaint(id, { assignedToId });
      res.json(updatedComplaint);
    } catch (error) {
      console.error("Error assigning complaint:", error);
      res.status(500).json({ error: "Failed to assign complaint" });
    }
  });

  app.put("/api/complaints/:id/status", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const { id } = req.params;
      const { status } = req.body;
      
      const updatedComplaint = await storage.updateComplaint(id, { 
        status,
        updatedAt: new Date()
      });
      res.json(updatedComplaint);
    } catch (error) {
      console.error("Error updating complaint status:", error);
      res.status(500).json({ error: "Failed to update complaint status" });
    }
  });

  app.delete("/api/complaints/:id", requireRole(['admin']), async (req, res) => {
    try {
      const { id } = req.params;
      await storage.deleteComplaint(id);
      res.json({ message: "Complaint deleted successfully" });
    } catch (error) {
      console.error("Error deleting complaint:", error);
      res.status(500).json({ error: "Failed to delete complaint" });
    }
  });

  // Forum routes
  app.get("/api/forum/categories", async (req, res) => {
    try {
      const categories = await storage.getForumCategories();
      res.json(categories);
    } catch (error) {
      console.error("Error fetching forum categories:", error);
      res.status(500).json({ error: "Failed to fetch forum categories" });
    }
  });

  app.post("/api/forum/categories", requireRole(['admin', 'moderator']), async (req, res) => {
    try {
      const result = insertForumCategorySchema.safeParse(req.body);
      if (!result.success) {
        return res.status(400).json({ message: "Invalid category data", errors: result.error.issues });
      }
      
      const category = await storage.createForumCategory(result.data);
      res.status(201).json(category);
    } catch (error) {
      console.error("Error creating forum category:", error);
      res.status(500).json({ error: "Failed to create forum category" });
    }
  });

  app.get("/api/forum/topics", async (req, res) => {
    try {
      const { categoryId } = req.query;
      const topics = await storage.getForumTopics(categoryId as string);
      res.json(topics);
    } catch (error) {
      console.error("Error fetching forum topics:", error);
      res.status(500).json({ error: "Failed to fetch forum topics" });
    }
  });

  app.get("/api/forum/topics/:id", async (req, res) => {
    try {
      const topic = await storage.getForumTopicById(req.params.id);
      if (!topic) {
        return res.status(404).json({ error: "Topic not found" });
      }
      res.json(topic);
    } catch (error) {
      console.error("Error fetching forum topic:", error);
      res.status(500).json({ error: "Failed to fetch forum topic" });
    }
  });

  app.post("/api/forum/topics", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      const result = insertForumTopicSchema.safeParse({
        ...req.body,
        authorId: user.id,
        authorName: user.name
      });

      if (!result.success) {
        return res.status(400).json({ message: "Invalid topic data", errors: result.error.issues });
      }

      const topic = await storage.createForumTopic(result.data);
      res.status(201).json(topic);
    } catch (error) {
      console.error("Error creating forum topic:", error);
      res.status(500).json({ error: "Failed to create forum topic" });
    }
  });

  app.get("/api/forum/topics/:id/posts", async (req, res) => {
    try {
      const posts = await storage.getForumPosts(req.params.id);
      res.json(posts);
    } catch (error) {
      console.error("Error fetching forum posts:", error);
      res.status(500).json({ error: "Failed to fetch forum posts" });
    }
  });

  app.post("/api/forum/posts", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(401).json({ message: "User not found" });
      }

      const result = insertForumPostSchema.safeParse({
        ...req.body,
        authorId: user.id,
        authorName: user.name
      });

      if (!result.success) {
        return res.status(400).json({ message: "Invalid post data", errors: result.error.issues });
      }

      const post = await storage.createForumPost(result.data);
      res.status(201).json(post);
    } catch (error) {
      console.error("Error creating forum post:", error);
      res.status(500).json({ error: "Failed to create forum post" });
    }
  });

  app.post("/api/forum/posts/:id/like", requireAuth, async (req, res) => {
    try {
      const userId = req.session.userId!;
      const postId = req.params.id;

      const result = insertForumLikeSchema.safeParse({
        postId,
        userId
      });

      if (!result.success) {
        return res.status(400).json({ message: "Invalid like data", errors: result.error.issues });
      }

      const like = await storage.createForumLike(result.data);
      res.status(201).json(like);
    } catch (error) {
      console.error("Error liking forum post:", error);
      res.status(500).json({ error: "Failed to like forum post" });
    }
  });
}