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
                'content' => "The internet is a massive, open-source university where the only cost of admission is your time, discipline, and curiosity. Years ago, learning to code required an expensive computer science degree. Today, if you are looking for comprehensive learning, here are the top “one-stop shops” for coding that can take you from absolute beginner to a professional level developer without spending a dime.

## 1. freeCodeCamp

The undisputed champion of free coding education. Founded by Quincy Larson, freeCodeCamp has helped thousands of individuals transition into tech careers effortlessly. It offers a full-stack curriculum featuring thousands of hours that are 100% free. What makes freeCodeCamp so powerful is its philosophy: you learn by building real, tangible projects rather than passively watching videos. 

You begin with the absolute basics of Responsive Web Design—mastering HTML semantics, CSS flexbox, and grid layouts. Then, you step through the fire of JavaScript Algorithms and Data Structures, which will prepare you for incredibly tough whiteboard interviews. Eventually, it pushes you into Front End Development Libraries (like React and Redux) and backend architecture using Node.js and MongoDB. It's essentially a free coding bootcamp that rewards you with respected certifications upon completion. Their community forum and YouTube channel are also legendary resources. Ready to start? Check it out at [freeCodeCamp](https://www.freecodecamp.org).

## 2. The Odin Project

For those who want a challenge, there is The Odin Project. A fantastic, open-source curriculum tailored for becoming a full-stack web developer. Instead of handing you all the answers on a single hand-held interactive platform, The Odin Project thrusts you into the real world. It behaves like a true, realistic developer roadmap. 

It guides you through the best free resources on the web, teaching you how to use Git version control, how to set up your own local Linux/Mac coding environment, and how the internet actually works organically. You will be building projects from scratch on your own local text editor, which is exactly how professional developers work in their day-to-day jobs. Whether you choose their Ruby on Rails path or the modern JavaScript/Node path, you will come out of this curriculum battle-hardened. Visit [The Odin Project](https://www.theodinproject.com).

## 3. Codecademy (Free Tier)

For absolute beginners who might feel overly intimidated by setting up environments or installing command-line tools, Codecademy is a gentle introduction. While many of their advanced courses now require a Pro subscription, Codecademy’s free tier offers excellent introductory courses for almost every popular language, including Python, JavaScript, SQL, and Ruby. 

Its interactive in-browser editor makes it incredibly easy to start writing code immediately. You simply read the instructions, type the code in the middle pane, and see your output instantly on the right. It is the absolute best place to “test the waters” and see if coding is something you genuinely enjoy before you commit hundreds of hours to it. Learn more at [Codecademy](https://www.codecademy.com).

## 4. W3Schools & GeeksforGeeks

Once you begin your coding journey, you will realize that developers rarely memorize everything. You will constantly need to look up syntax and documentation. W3Schools is perfect for beginners who want simple, clear, \"try it yourself\" examples. It strips away academic jargon and just shows you how code is supposed to be written. 

GeeksforGeeks goes a layer deeper. It is widely used by computer science students and professionals for understanding core algorithms, complex data structures, and preparing for competitive programming or job interviews. They have an immense repository of well-documented coding solutions in various languages.

*In conclusion: These platforms provide a highly structured learning path. By following them closely, you avoid the trap of \"tutorial hell\"—the dreaded cycle of continuously watching tutorials without ever trying to build software on your own.*",
                'coverImage' => '/uploads/comprehensive_coding_cover.png',
                'tags' => [(string) $tags['Coding']->_id, (string) $tags['Web Dev']->_id],
            ],
            [
                'title' => 'Learn from the Best: University Coding MOOCs',
                'excerpt' => 'Access world-class computer science education from Harvard, MIT, and Stanford for free through MOOCs.',
                'content' => "Did you know you can take classes from the best universities in the world entirely from your living room? Massive Open Online Courses (MOOCs) allow anyone with an internet connection to audit computer science education from top-tier institutions. Many self-taught beginners are held back by the perceived cost of a university degree or elite bootcamp. But here is the profound truth: you can learn everything you need to become a world-class programmer for free.

## 1. Harvard's CS50x

Possibly the most highly regarded introductory computer science course ever created. Led by the charismatic Professor David J. Malan, CS50 is completely free to audit online. It is challenging, remarkably comprehensive, and brilliantly taught with incredible production value. 

CS50 doesn't just teach you syntax or a specific framework; it teaches you how to think algorithmically and solve problems computationally. You will learn low-level languages like C to understand how computer memory works, before scaling up to high-level concepts in Python, SQL, and Web Development. The capstone project allows you to build a piece of software entirely of your own design. It’s an absolute must for anyone serious about understanding what is happening \"under the hood\" of a computer. Dive in right now at [Harvard's CS50x](https://cs50.harvard.edu/x/).

## 2. Coursera and edX

These massive platforms were originally founded by visionary professors from elite academic institutions and have evolved into massive hubs for both free auditing and paid certificates. They host courses from Stanford, University of Michigan, Johns Hopkins, and even major tech companies like Google, Meta, and IBM.

Almost any course on these platforms can be audited for free, granting you access to high-quality lecture videos and premium reading materials. Whether you want to learn cutting-edge machine learning from AI pioneer Andrew Ng, grasp the basics of data visualization in Python, or explore advanced cloud computing protocols, these platforms have a designated path for you. Their academic rigor guarantees that you aren't just learning buzzwords, but the underlying theories of modern tech. Check out directories at [Coursera](https://www.coursera.org) and [edX](https://www.edx.org).

## 3. MIT OpenCourseWare

Massachusetts Institute of Technology (MIT) made headlines when they decided to put the course materials for nearly all their classes online for free. MIT OpenCourseWare is brilliant for accessing raw academic resources—comprehensive lecture notes, recorded classroom lectures, and rigorous unedited assignments. 

While you won't get the interactive \"hand-holding\" or auto-graders afforded by some newer commercial platforms, the sheer depth of knowledge available here is unparalleled. If you truly crave the raw, unfiltered academic experience of an elite polytechnic university, and want to learn algorithms or data structures exactly as MIT undergraduates do, this is your goldmine. Explore the catalog at [MIT OpenCourseWare](https://ocw.mit.edu).

## 4. Full Stack Open by University of Helsinki

If you specifically want to master modern, bleeding-edge web development rather than general computer science theory, this advanced free course is exceptional. Created by the University of Helsinki, it skips the elementary basics and dives straight into building Single Page Applications with React, REST APIs with Node.js, and integrating GraphQL or TypeScript. It is highly respected within the industry and is heavily project-based, ensuring you understand modern deployment practices like Docker and CI/CD.

*Final Thoughts: While you may have to pay a minimal fee if you want a verified certificate to post on your LinkedIn or resume, the knowledge, lectures, and exercises themselves are 100% free. Remember that in the software industry, your portfolio and technical competence matter infinitely more than a printed piece of paper anyway.*",
                'coverImage' => '/uploads/university_moocs_cover.png',
                'tags' => [(string) $tags['Coding']->_id, (string) $tags['MOOC']->_id],
            ],
            [
                'title' => 'Master HTML & CSS: Resources for Web Design',
                'excerpt' => 'Dedicated resources to master the core presentation layers of the web, focusing strictly on HTML structure and CSS styling.',
                'content' => "Whether you want to build blazing-fast interactive websites from scratch, customize third-party WordPress themes, or start diving head-first into complex frontend frameworks like React and Vue, HTML and CSS are your unskippable foundational skills. You cannot build a solid house on an unstable foundation. Here is how to truly master the visual layers of the web according to industry experts:

## 1. MDN Web Docs

Created by Mozilla (the foundation that builds the Firefox browser), MDN Web Docs is widely regarded as the ultimate reference bible for web development. It is not designed to be a traditional step-by-step tutorial, but rather the definitive, official documentation for how HTML, CSS, and Javascript actually communicate with the browser under the hood. 

Whenever you forget a bizarre HTML accessibility attribute or how a certain CSS relative positioning layout behaves on mobile devices, MDN is your source of absolute truth. Every seasoned developer has MDN permanently open in one of their tabs. It offers exhaustive explanations and interactive code snippets for nearly every feature built into the web ecosystem. Bookmark it right now to save future headaches: [MDN Web Docs](https://developer.mozilla.org/).

## 2. CSS-Tricks

If MDN is the encyclopedia, CSS-Tricks is the masterclass. Long considered the definitive resource for all things CSS, it started as a humble blog and has blossomed into a complete almanac of CSS properties, clever techniques, and advanced layout tricks. 

If you have ever struggled to understand how Flexbox genuinely aligns items or how CSS Grid can be used to construct magazine-style responsive designs with only three lines of code, CSS-Tricks has beautifully illustrated, colorful guides that break it down perfectly visually. They also frequently cover modern web trends like CSS animations, SVG manipulation, and CSS variables. Learn more at [CSS-Tricks](https://css-tricks.com).

## 3. Frontend Mentor

A common trap for new developers is learning the syntax but not knowing how to apply it. Frontend Mentor is a brilliant platform that solves this by providing professional design files (like Figma or Sketch mockups) that emulate real-world business requirements. 

Your challenge is to take these images and build the design perfectly using nothing but HTML, CSS, and occasionally a splash of JavaScript. This bridges the critical gap between learning in a sterile vacuum and executing real-world freelance or agency client work. It trains your eye to notice subtle paddings, elegant typography scales, and responsive breakpoints. If you can confidently tackle these challenges, you are ready for a frontend job. Challenge yourself today at [Frontend Mentor](https://www.frontendmentor.io).

## 4. Modern Interactive Games

Did you know one of the most effective ways to master CSS is through gamification? Websites like **CSS Grid Garden** and **Flexbox Froggy** teach you highly complex layout systems by having you complete puzzles. You might be asked to water digital vegetable patches by manipulating grid columns, or maneuver colorful frogs onto their corresponding lilypads using flex-align properties. It might sound silly, but it is demonstrably one of the greatest learning techniques available! It actively builds spatial memory and muscle-memory for CSS alignment logic.

*Always Remember: Drag-and-drop visual builders and AI tools will only get a project 95 percent of the way there. Truly mastering CSS allows you to customize that final, most important 5 percent, thereby delivering truly unique, performant, and pixel-perfect results to users.*",
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
