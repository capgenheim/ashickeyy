<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Support\Str;
use App\Models\Category;

$category = Category::firstOrCreate(['name' => 'Insights', 'slug' => 'insights']);

$title = "The Quran is a Love Letter";
$content = "Have you ever thought about what the Quran really is? One of our teachers shared a beautiful thought. He said that the Quran is a series of love letters. It is the only book in the world where every single word has been perfectly kept exactly as it was spoken by the Prophet.

When you look at the Quran as a love letter, it changes how you read it. Allah tells us that He is closer to us than our jugular vein. He constantly calls out to us because He wants us to be close to Him. Writing a love letter shows someone that they are truly important in your life. It allows them to feel special. This is exactly how we should feel when we read the Quran.

When you receive a message from someone you love, you read it slowly. You want to understand every single word. You keep it safe and read it over and over again because it brings you happiness. This is exactly why we recite the Quran beautifully with proper rules. It helps us feel the deep meaning behind the words.

The great angel Gabriel was the postman. The package was the Quran itself. And the special delivery place was the heart of our beloved Prophet. Knowing this makes reading the Quran feel incredibly personal and deeply special.

This understanding changes everything. It brings happiness into your life. You will start reading the Quran more often, keeping it close to you always, and trying your best to understand its true meaning. Read the Quran knowing that it is a precious treasure given directly to you.";

$post = Post::create([
    'title' => $title,
    'slug' => Str::slug($title),
    'content' => $content,
    'excerpt' => 'How to recite and connect with the Quran - think of it as a love letter from Allah. Connect to the words and imagine the beauty of the Words.',
    'coverImage' => '/uploads/quran_love_letter.png',
    'category' => (string) $category->_id,
    'tags' => [],
    'status' => 'published',
    'publishedAt' => now(),
    'author' => 'admin@ashickey.space',
    'readTime' => Post::calculateReadTime($content),
    'views' => 0
]);

echo "Post Created successfully with ID: " . $post->_id . "\n";
