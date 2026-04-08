$cat = \App\Models\Category::firstOrCreate(['slug' => 'development'], ['name' => 'Development', 'description' => 'Dev tutorials for beginners']);

$tags = [
    'Coding' => \App\Models\Tag::firstOrCreate(['slug' => 'coding'], ['name' => 'Coding']),
    'Web Dev' => \App\Models\Tag::firstOrCreate(['slug' => 'web-dev'], ['name' => 'Web Dev']),
    'Frontend' => \App\Models\Tag::firstOrCreate(['slug' => 'frontend'], ['name' => 'Frontend']),
    'MOOC' => \App\Models\Tag::firstOrCreate(['slug' => 'mooc'], ['name' => 'MOOC']),
];

$posts = [
    [
        'title' => 'Top Platforms for Comprehensive Coding Education',
        'excerpt' => 'Discover the best free platforms like freeCodeCamp and Codecademy that take you from beginner to job-ready developer.',
        'content' => "The internet is a massive, open-source university where the only cost of admission is your time. If you're looking for comprehensive learning, here are the top “one-stop shops” for coding:

## 1. freeCodeCamp
The undisputed champion of free coding education. It offers a full-stack curriculum featuring thousands of hours that are 100% free. You learn by building real projects.

## 2. The Odin Project
A fantastic, open-source curriculum for becoming a full-stack developer. It guides you through the best free resources on the web and teaches you how things fit together.

## 3. Codecademy
Codecademy’s free tier offers excellent introductory courses for many languages. Its interactive in-browser editor makes it incredibly easy to start writing code without any setup.

*These platforms provide a structured learning path so you don't end up in tutorial hell.*",
        'coverImage' => '/uploads/comprehensive_coding_cover.png',
        'tags' => [$tags['Coding']->_id, $tags['Web Dev']->_id],
    ],
    [
        'title' => 'Learn from the Best: University Coding MOOCs',
        'excerpt' => 'Access world-class computer science education from Harvard, MIT, and Stanford for free through MOOCs.',
        'content' => "Did you know you can take classes from the best universities in the world from your living room? Massive Open Online Courses (MOOCs) allow you to audit computer science education from top institutions.

## 1. Harvard's CS50x
Possibly the most famous introductory computer science course in the world. It is challenging, comprehensive, and brilliantly taught. It’s a must for anyone serious about programming.

## 2. Coursera and edX
These platforms host courses from Stanford, University of Michigan, and MIT. You can audit almost any course to watch the lectures and read the materials for free.

## 3. MIT OpenCourseWare
MIT puts the course materials for nearly all its classes online for free. It is brilliant for accessing lecture notes and rigorous assignments.

*While you may have to pay for a certificate, the knowledge itself is absolutely free.*",
        'coverImage' => '/uploads/university_moocs_cover.png',
        'tags' => [$tags['Coding']->_id, $tags['MOOC']->_id],
    ],
    [
        'title' => 'Master HTML & CSS: Resources for Web Design',
        'excerpt' => 'Dedicated resources to master the core presentation layers of the web, focusing strictly on HTML structure and CSS styling.',
        'content' => "Whether you want to build websites from scratch or customize WordPress themes, HTML and CSS are your foundational skills. Here is how to master them:

## 1. MDN Web Docs
Created by Mozilla, this is the ultimate reference bible for web development. It’s the official documentation for HTML, CSS, and Javascript.

## 2. CSS-Tricks
The definitive resource for all things CSS. It started as a blog and is now a complete almanac of CSS properties, techniques, and advanced tricks like flexbox and grid.

## 3. Frontend Mentor
A brilliant platform that provides professional design files. Your challenge is to build the design perfectly using HTML and CSS, which bridges the gap between learning and real-world work.

*Start with structure (HTML), then move on to style (CSS).*",
        'coverImage' => '/uploads/html_css_cover.png',
        'tags' => [$tags['Web Dev']->_id, $tags['Frontend']->_id],
    ]
];

foreach ($posts as $idx => $p) {
    \App\Models\Post::create([
        'title' => $p['title'],
        'slug' => \Illuminate\Support\Str::slug($p['title']),
        'excerpt' => $p['excerpt'],
        'content' => $p['content'],
        'category' => (string) $cat->_id,
        'tags' => $p['tags'],
        'coverImage' => $p['coverImage'],
        'status' => 'published',
        'publishedAt' => now()->subDays($idx * 5)->subYear(),
        'author' => 'admin@ashickey.com',
        'readTime' => \App\Models\Post::calculateReadTime($p['content']),
        'views' => rand(50, 400),
    ]);
}
echo "Articles imported safely.\n";
