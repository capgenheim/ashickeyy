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

        Category::truncate();
        Tag::truncate();
        Post::truncate();

        // Categories
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Latest in tech'],
            ['name' => 'Development', 'slug' => 'development', 'description' => 'Dev tutorials for beginners'],
        ];

        $catIds = [];
        foreach ($categories as $cat) {
            $model = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
            $catIds[$cat['slug']] = (string) $model->_id;
        }

        // Tags
        $tagNames = ['Tutorial', 'Web Dev', 'Coding', 'Free'];
        $tagIds = [];
        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'slug' => Str::slug($name)]);
            $tagIds[$name] = (string) $tag->_id;
        }

        // Post content
        $content = <<<MD
# Learn to Code for Free: The Best Websites and Resources

Learning to code is one of the best investments you can make. It is a fantastic skill that can open up many new opportunities. Whether you want to build websites, create apps, or just understand the technology around you, knowing how to code gives you a superpower. 

Here are some great ways to start learning for free.

## 1. FreeCodeCamp
FreeCodeCamp is completely free and very beginner friendly. You learn by doing small coding challenges. They cover everything from basic HTML and CSS to complex JavaScript and databases. Plus, you get certifications when you finish their projects.

## 2. Codecademy
Codecademy offers a very interactive experience. You write code straight in your browser and get instant feedback. While they have paid plans, their basic courses are free and perfect to test if coding is something you enjoy.

## 3. Coursera and edX
If you prefer learning from universities, Coursera and edX offer free audit classes from places like Harvard and Stanford. The famous CS50 course from Harvard is a perfect example of high quality education available to anyone with an internet connection.

## 4. YouTube Tutorials
Never underestimate YouTube. There are thousands of developers who post complete courses for free. Channels give you step by step instructions on how to build real world applications from scratch.

## Start Today
You do not need to spend money to start learning. Just pick one platform, stay consistent, and practice every day. Building small projects is the best way to understand how everything works together.
MD;

        Post::create([
            'title' => 'Learn to Code for Free in 2025: Best Resources',
            'excerpt' => 'Discover the best free platforms to learn coding. Start your programming journey today with these simple resources.',
            'content' => $content,
            'category' => $catIds['development'],
            'tags' => [$tagIds['Coding'], $tagIds['Free'], $tagIds['Tutorial']],
            'status' => 'published',
            'coverImage' => '/learning_code_cover.png',
            'slug' => Str::slug('Learn to Code for Free in 2025: Best Resources'),
            'publishedAt' => now()->subYear(), // Posted last year (2025)
            'author' => 'admin@ashickey.com',
            'readTime' => Post::calculateReadTime($content),
            'views' => rand(100, 500),
        ]);

        echo "✓ Seeded new article\n";
    }
}
