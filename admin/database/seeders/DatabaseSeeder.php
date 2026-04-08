<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        if (! User::where('email', env('ADMIN_EMAIL', 'admin@ashickey.com'))->exists()) {
            User::create([
                'email' => env('ADMIN_EMAIL', 'admin@ashickey.com'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin@Ashickey2024!')),
                'name' => 'Admin',
                'role' => 'admin',
            ]);
            echo "✓ Admin user created\n";
        }

        // Categories
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Latest in tech'],
            ['name' => 'Development', 'slug' => 'development', 'description' => 'Dev tutorials for beginners'],
            ['name' => 'Current Issues', 'slug' => 'current-issues', 'description' => 'Current events and issues'],
            ['name' => 'General', 'slug' => 'general', 'description' => 'General posts and thoughts'],
        ];

        $catIds = [];
        foreach ($categories as $cat) {
            $model = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
            $catIds[$cat['slug']] = (string) $model->_id;
        }
        echo "✓ Categories seeded\n";

        // Tags
        $tagNames = ['Flutter', 'JavaScript', 'Python', 'Web Dev', 'Tutorial', 'Opinion', 'Laravel', 'Docker', 'AI'];
        $tagIds = [];
        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'slug' => Str::slug($name)]);
            $tagIds[$name] = (string) $tag->_id;
        }
        echo "✓ Tags seeded\n";

        // Sample articles
        if (Post::count() === 0) {
            $posts = [
                [
                    'title' => 'Getting Started with Flutter Web in 2026',
                    'excerpt' => 'A comprehensive guide to building beautiful web applications using Flutter — from setup to deployment.',
                    'content' => "# Getting Started with Flutter Web in 2026\n\nFlutter has evolved from a mobile-first framework into a truly cross-platform powerhouse. In 2026, Flutter Web has reached a level of maturity that makes it a serious contender for production web applications.\n\n## Why Flutter Web?\n\n- **Single codebase** for mobile, web, and desktop\n- **Material Design 3** out of the box\n- **CanvasKit rendering** for pixel-perfect consistency\n- **Hot reload** for rapid development\n\n## Setting Up Your Environment\n\nFirst, make sure you have the Flutter SDK installed:\n\n```bash\nflutter channel stable\nflutter upgrade\nflutter config --enable-web\n```\n\n## Creating Your First Web App\n\n```bash\nflutter create my_web_app\ncd my_web_app\nflutter run -d chrome\n```\n\n## Key Considerations\n\n### Performance\nFlutter Web uses CanvasKit by default, which provides excellent rendering performance. For SEO-heavy sites, consider server-side rendering alternatives.\n\n### Responsive Design\nUse `LayoutBuilder` and `MediaQuery` to create responsive layouts that work across all screen sizes.\n\n### State Management\nProvider remains the recommended choice for most applications. For complex state, consider Riverpod or BLoC.\n\n## Conclusion\n\nFlutter Web is production-ready and offers a unique advantage for teams already building mobile apps with Flutter. The shared codebase significantly reduces development time and maintenance overhead.",
                    'category' => $catIds['technology'],
                    'tags' => [$tagIds['Flutter'], $tagIds['Web Dev'], $tagIds['Tutorial']],
                    'status' => 'published',
                ],
                [
                    'title' => 'Docker for Beginners: Containerize Everything',
                    'excerpt' => 'Learn the fundamentals of Docker — containers, images, volumes, and docker-compose in simple terms.',
                    'content' => "# Docker for Beginners: Containerize Everything\n\nDocker has revolutionized how we build, ship, and run applications. If you're still deploying directly to servers, you're missing out.\n\n## What is Docker?\n\nDocker packages your application and all its dependencies into a standardized unit called a **container**. Think of it as a lightweight virtual machine.\n\n## Core Concepts\n\n### Images\nAn image is a blueprint for your container. It includes the OS, runtime, dependencies, and your application code.\n\n### Containers\nA container is a running instance of an image. You can have multiple containers from the same image.\n\n### Volumes\nVolumes persist data beyond the container lifecycle. Essential for databases.\n\n## Your First Dockerfile\n\n```dockerfile\nFROM node:20-alpine\nWORKDIR /app\nCOPY package*.json ./\nRUN npm install\nCOPY . .\nEXPOSE 3000\nCMD [\"node\", \"server.js\"]\n```\n\n## Docker Compose\n\nFor multi-service applications, Docker Compose is your best friend:\n\n```yaml\nservices:\n  app:\n    build: .\n    ports:\n      - '3000:3000'\n  db:\n    image: mongo:7\n    volumes:\n      - db-data:/data/db\n```\n\n## Best Practices\n\n1. Use multi-stage builds for smaller images\n2. Never run containers as root\n3. Use `.dockerignore` to exclude unnecessary files\n4. Pin your base image versions\n5. Use health checks\n\nDocker is not just a tool — it's a philosophy of reproducible deployments.",
                    'category' => $catIds['development'],
                    'tags' => [$tagIds['Docker'], $tagIds['Tutorial']],
                    'status' => 'published',
                ],
                [
                    'title' => 'Laravel 13: What\'s New and Why It Matters',
                    'excerpt' => 'Exploring the key features and improvements in Laravel 13 — from performance boosts to new developer experience enhancements.',
                    'content' => "# Laravel 13: What's New and Why It Matters\n\nLaravel continues to be the most popular PHP framework, and version 13 brings significant improvements.\n\n## Performance Improvements\n\nLaravel 13 includes optimized query building, improved caching strategies, and faster route resolution. Benchmarks show 15-20% improvement in request handling.\n\n## MongoDB Integration\n\nWith the `mongodb/laravel-mongodb` package now at version 5.x, MongoDB is a first-class citizen in the Laravel ecosystem. Eloquent works seamlessly with MongoDB collections.\n\n```php\nclass Post extends \\MongoDB\\Laravel\\Eloquent\\Model\n{\n    protected \$connection = 'mongodb';\n    protected \$collection = 'posts';\n}\n```\n\n## Livewire 4\n\nLivewire continues to evolve as the go-to solution for building dynamic interfaces without writing JavaScript. Version 4 brings:\n\n- Improved rendering performance\n- Better TypeScript support\n- Enhanced testing utilities\n\n## Security Enhancements\n\n- Built-in rate limiting with more granular controls\n- Improved CORS handling\n- Enhanced password hashing defaults\n\n## Getting Started\n\n```bash\ncomposer create-project laravel/laravel my-app\ncd my-app\nphp artisan serve\n```\n\nLaravel 13 is the most polished release yet. Whether you're building APIs, web apps, or microservices, it has you covered.",
                    'category' => $catIds['development'],
                    'tags' => [$tagIds['Laravel'], $tagIds['Web Dev'], $tagIds['Tutorial']],
                    'status' => 'published',
                ],
                [
                    'title' => 'The Current State of AI in Software Development',
                    'excerpt' => 'How AI is transforming the way we write, review, and deploy code — and what developers should prepare for.',
                    'content' => "# The Current State of AI in Software Development\n\nAI-assisted coding has moved from novelty to necessity. In 2026, developers who leverage AI tools are significantly more productive.\n\n## Code Generation\n\nModern AI assistants can:\n- Generate boilerplate code in seconds\n- Write unit tests from function signatures\n- Suggest performance optimizations\n- Explain complex codebases\n\n## Code Review\n\nAI-powered code review catches:\n- Security vulnerabilities\n- Performance bottlenecks\n- Style inconsistencies\n- Potential bugs\n\n## The Human Element\n\nDespite AI's capabilities, human developers remain essential for:\n- **Architecture decisions** — AI can suggest, but humans must decide\n- **Business logic** — understanding domain requirements\n- **Creative problem-solving** — novel solutions to unique problems\n- **Code ownership** — accountability and maintenance\n\n## What Developers Should Do\n\n1. **Embrace AI tools** — they're force multipliers, not replacements\n2. **Focus on fundamentals** — algorithms, data structures, design patterns\n3. **Learn prompt engineering** — getting the best output from AI\n4. **Stay updated** — the landscape changes rapidly\n\n## Conclusion\n\nAI is the most significant shift in software development since the internet. Developers who adapt will thrive; those who resist will fall behind.",
                    'category' => $catIds['current-issues'],
                    'tags' => [$tagIds['AI'], $tagIds['Opinion']],
                    'status' => 'published',
                ],
                [
                    'title' => 'Building a Blog Platform: Architecture Decisions',
                    'excerpt' => 'Behind-the-scenes look at the technology choices and architecture of the ashickey blog platform.',
                    'content' => "# Building a Blog Platform: Architecture Decisions\n\nWhen I set out to build ashickey, I had specific goals: fast, beautiful, and maintainable. Here's how I made key architecture decisions.\n\n## Why Flutter for the Frontend?\n\nI chose Flutter Web because:\n- Material Design 3 provides a polished look out of the box\n- Animations are smooth and easy to implement\n- The widget system is incredibly flexible\n- Dark/light theme switching is trivial\n\n## Why Laravel for the Backend?\n\nLaravel offers:\n- Eloquent ORM with MongoDB support\n- Built-in authentication and security\n- Excellent testing utilities\n- Rate limiting and CORS out of the box\n\n## Why MongoDB?\n\nBlog content is inherently document-shaped:\n- Posts have variable-length content\n- Tags and categories are easily embedded or referenced\n- Full-text search is built-in\n- No schema migrations needed\n\n## Docker for Everything\n\nThe entire stack runs in Docker:\n- **Nginx** — reverse proxy, serves Flutter static build\n- **Laravel** — admin panel + REST API\n- **MongoDB** — database\n\nOne `docker compose up` and everything is running.\n\n## Security Considerations\n\n- bcrypt password hashing (12 rounds)\n- Rate limiting on auth and API endpoints\n- Security headers (CSP, X-Frame-Options, HSTS)\n- Non-root Docker containers\n- Input sanitization on all user content\n\n## Lessons Learned\n\n1. Keep it simple — overengineering kills projects\n2. Docker from day one — consistency matters\n3. Test everything — especially auth flows\n4. Design for dark mode first — it's 2026\n\nThe result is a platform I'm proud of. And you're reading it right now.",
                    'category' => $catIds['general'],
                    'tags' => [$tagIds['Flutter'], $tagIds['Laravel'], $tagIds['Docker'], $tagIds['Web Dev']],
                    'status' => 'published',
                ],
            ];

            foreach ($posts as $postData) {
                Post::create(array_merge($postData, [
                    'slug' => Str::slug($postData['title']),
                    'publishedAt' => now()->subDays(rand(1, 30)),
                    'author' => 'admin@ashickey.com',
                    'readTime' => Post::calculateReadTime($postData['content']),
                    'views' => rand(10, 500),
                ]));
            }
            echo "✓ Sample posts seeded\n";
        }
    }
}
