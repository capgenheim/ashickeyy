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

        // Base Category
        $cat = Category::firstOrCreate(['slug' => 'development'], ['name' => 'Development', 'description' => 'Dev tutorials for beginners']);

        // Tags
        $tags = [
            'Coding' => Tag::firstOrCreate(['slug' => 'coding'], ['name' => 'Coding']),
            'Web Dev' => Tag::firstOrCreate(['slug' => 'web-dev'], ['name' => 'Web Dev']),
            'Frontend' => Tag::firstOrCreate(['slug' => 'frontend'], ['name' => 'Frontend']),
            'MOOC' => Tag::firstOrCreate(['slug' => 'mooc'], ['name' => 'MOOC']),
        ];

        // Posts
        $posts = [
            [
                'title' => 'Top Platforms for Comprehensive Coding Education',
                'excerpt' => 'Discover the best free platforms like freeCodeCamp and Codecademy that take you from beginner to job-ready developer.',
                'content' => "The internet is a massive, open-source university where the only cost of admission is your time, discipline, and curiosity. If you are looking for comprehensive learning, here are the top “one-stop shops” for coding that can take you from absolute beginner to a professional level developer without spending a dime.

## 1. freeCodeCamp
The undisputed champion of free coding education. It offers a full-stack curriculum featuring thousands of hours that are 100% free. What makes freeCodeCamp so powerful is that you learn by building real projects rather than just watching videos. You start with basic HTML and CSS, move through complex Javascript algorithms, and eventually learn how to handle databases and backend architecture. It's essentially a free coding bootcamp that rewards you with respected certifications upon completion. Check it out at [freeCodeCamp](https://www.freecodecamp.org).

## 2. The Odin Project
A fantastic, open-source curriculum for becoming a full-stack developer. Instead of handing you all the answers on a single platform, it behaves like a true, realistic roadmap. It guides you through the best free resources on the web, teaches you how to use Git, how to set up your own local coding environment, and how things fit together organically. You will be building projects from scratch locally, which is exactly how professional developers work. Visit [The Odin Project](https://www.theodinproject.com).

## 3. Codecademy (Free Tier)
While many of their advanced courses require a Pro subscription, Codecademy’s free tier offers excellent introductory courses for almost every popular language, including Python, Javascript, and Ruby. Its interactive in-browser editor makes it incredibly easy to start writing code without any setup or terminal configuration. It is the absolute best place to “test the waters” and see if coding is something you actually enjoy. Learn more at [Codecademy](https://www.codecademy.com).

## 4. W3Schools & GeeksforGeeks
Once you start coding, you will constantly need to look up documentation. W3Schools is perfect for beginners who want simple, clear, \"try it yourself\" examples. GeeksforGeeks goes a bit deeper and is widely used for understanding computer science fundamentals and preparing for job interviews.

*These platforms provide a highly structured learning path. By following them closely, you avoid the trap of tutorial hell, ensuring your time is spent actually building.*",
                'coverImage' => '/uploads/comprehensive_coding_cover.png',
                'tags' => [(string) $tags['Coding']->_id, (string) $tags['Web Dev']->_id],
            ],
            [
                'title' => 'Learn from the Best: University Coding MOOCs',
                'excerpt' => 'Access world-class computer science education from Harvard, MIT, and Stanford for free through MOOCs.',
                'content' => "Did you know you can take classes from the best universities in the world from your living room? Massive Open Online Courses (MOOCs) allow you to audit computer science education from top institutions. Many people are stopped by the perceived cost of a university degree. But here is the truth: you can learn everything you need to become a world-class programmer for free.

## 1. Harvard's CS50x
Possibly the most famous introductory computer science course in the world. Led by Professor David J. Malan, it is completely free to audit online. It is challenging, comprehensive, and brilliantly taught. It doesn't just teach you a language; it teaches you how to think algorithmically and solve problems efficiently. It’s an absolute must for anyone serious about programming. Dive in at [Harvard's CS50x](https://cs50.harvard.edu/x/).

## 2. Coursera and edX
These platforms, originally founded by leading professors and universities, host courses from Stanford, University of Michigan, and Google. You can audit almost any course to watch the high-quality lecture videos and read the premium materials for free. Whether you want to learn machine learning from Andrew Ng or understand the basics of Python, these platforms have a path for you. Check out [Coursera](https://www.coursera.org) and [edX](https://www.edx.org).

## 3. MIT OpenCourseWare
MIT puts the course materials for nearly all its classes online for free. It is brilliant for accessing lecture notes, examinations, and rigorous assignments. While you won't get the interactive \"hand-holding\" of some newer platforms, the depth of knowledge available here is unparalleled. If you want the raw academic experience of an elite university, try it at [MIT OpenCourseWare](https://ocw.mit.edu).

## 4. Full Stack Open by University of Helsinki
If you specifically want to master modern web development, this advanced free course teaches you React, Node.js, and GraphQL. It is highly respected in the industry and heavily project-based.

*While you may have to pay if you want a verified certificate to put on your LinkedIn, the knowledge, lectures, and exercises themselves are absolutely free. Your portfolio matters far more than the piece of paper anyway.*",
                'coverImage' => '/uploads/university_moocs_cover.png',
                'tags' => [(string) $tags['Coding']->_id, (string) $tags['MOOC']->_id],
            ],
            [
                'title' => 'Master HTML & CSS: Resources for Web Design',
                'excerpt' => 'Dedicated resources to master the core presentation layers of the web, focusing strictly on HTML structure and CSS styling.',
                'content' => "Whether you want to build blazing fast websites from scratch, customize WordPress themes, or start diving into frontend frameworks, HTML and CSS are your unskippable foundational skills. Here is how to truly master them according to the experts:

## 1. MDN Web Docs
Created by Mozilla (the makers of Firefox), this is the ultimate reference bible for web development. It is not a step-by-step tutorial, but rather the official documentation for how HTML, CSS, and Javascript actually work under the hood. Whenever you forget an attribute or how a certain CSS layout behaves, MDN is your source of absolute truth. Bookmark it right now: [MDN Web Docs](https://developer.mozilla.org/).

## 2. CSS-Tricks
The definitive resource for all things CSS. It started as a blog and is now a complete almanac of CSS properties, techniques, and advanced layout tricks. If you are struggling to understand how Flexbox works or how CSS Grid can be used to make complex responsive designs, CSS-Tricks has beautifully illustrated guides that break it down perfectly. Learn more at [CSS-Tricks](https://css-tricks.com).

## 3. Frontend Mentor
A brilliant platform that provides professional design files (like Figma or Sketch). Your challenge is to build the design perfectly using nothing but HTML and CSS. This bridges the critical gap between learning in a vacuum and doing real-world client work. It teaches you how to look at a picture and translate it into clean, semantic code. Challenge yourself at [Frontend Mentor](https://www.frontendmentor.io).

## 4. Modern Interactive Games
Did you know you can learn CSS by playing games? Websites like **CSS Grid Garden** and **Flexbox Froggy** teach you complex layout systems by having you water digital carrots or move frogs onto lilypads. It sounds silly, but it is one of the most effective ways to build spatial memory for CSS!

*Remember: Visual builders get you 95 percent of the way there. Learning CSS allows you to customize that final 5 percent and deliver truly unique, pixel-perfect results.*",
                'coverImage' => '/uploads/html_css_cover.png',
                'tags' => [(string) $tags['Web Dev']->_id, (string) $tags['Frontend']->_id],
            ]
        ];

        foreach ($posts as $idx => $p) {
            Post::create([
                'title' => $p['title'],
                'slug' => Str::slug($p['title']),
                'excerpt' => $p['excerpt'],
                'content' => $p['content'],
                'category' => (string) $cat->_id,
                'tags' => $p['tags'],
                'coverImage' => $p['coverImage'],
                'status' => 'published',
                'publishedAt' => now()->subDays($idx * 5)->subYear(),
                'author' => 'admin@ashickey.com',
                'readTime' => Post::calculateReadTime($p['content']),
                'views' => rand(50, 400),
            ]);
        }

        echo "✓ Seeded new articles dynamically\n";
    }
}
