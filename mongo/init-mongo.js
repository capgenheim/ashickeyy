// MongoDB initialization script
// Creates application database, user, collections, and indexes

const dbName = process.env.MONGO_DB_NAME || 'ashickey';
const dbUser = process.env.MONGO_DB_USER || 'ashickey_app';
const dbPassword = process.env.MONGO_DB_PASSWORD || 'ashickey_app_password';

const db = db.getSiblingDB(dbName);

// Create application user with readWrite access only
db.createUser({
  user: dbUser,
  pwd: dbPassword,
  roles: [
    { role: 'readWrite', db: dbName }
  ]
});

// Create collections
db.createCollection('posts');
db.createCollection('categories');
db.createCollection('tags');
db.createCollection('users');
db.createCollection('media');

// Posts indexes
db.posts.createIndex({ slug: 1 }, { unique: true });
db.posts.createIndex({ status: 1, publishedAt: -1 });
db.posts.createIndex({ category: 1, status: 1, publishedAt: -1 });
db.posts.createIndex({ tags: 1, status: 1, publishedAt: -1 });
db.posts.createIndex(
  { title: 'text', content: 'text', excerpt: 'text' },
  { weights: { title: 10, excerpt: 5, content: 1 } }
);

// Categories indexes
db.categories.createIndex({ slug: 1 }, { unique: true });
db.categories.createIndex({ name: 1 });

// Tags indexes
db.tags.createIndex({ slug: 1 }, { unique: true });
db.tags.createIndex({ name: 1 });

// Users indexes
db.users.createIndex({ email: 1 }, { unique: true });

// Media indexes
db.media.createIndex({ createdAt: -1 });

// Seed default categories
db.categories.insertMany([
  { name: 'Technology', slug: 'technology', description: 'Latest in tech', createdAt: new Date(), updatedAt: new Date() },
  { name: 'Development', slug: 'development', description: 'Dev tutorials for beginners', createdAt: new Date(), updatedAt: new Date() },
  { name: 'Current Issues', slug: 'current-issues', description: 'Current events and issues', createdAt: new Date(), updatedAt: new Date() },
  { name: 'General', slug: 'general', description: 'General posts and thoughts', createdAt: new Date(), updatedAt: new Date() }
]);

// Seed default tags
db.tags.insertMany([
  { name: 'Flutter', slug: 'flutter', createdAt: new Date(), updatedAt: new Date() },
  { name: 'JavaScript', slug: 'javascript', createdAt: new Date(), updatedAt: new Date() },
  { name: 'Python', slug: 'python', createdAt: new Date(), updatedAt: new Date() },
  { name: 'Web Dev', slug: 'web-dev', createdAt: new Date(), updatedAt: new Date() },
  { name: 'Tutorial', slug: 'tutorial', createdAt: new Date(), updatedAt: new Date() },
  { name: 'Opinion', slug: 'opinion', createdAt: new Date(), updatedAt: new Date() }
]);

print('✓ Database initialized: ' + dbName);
print('✓ User created: ' + dbUser);
print('✓ Collections and indexes created');
print('✓ Default categories and tags seeded');
