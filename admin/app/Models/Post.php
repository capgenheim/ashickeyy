<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Post extends Model
{
    protected $connection = 'mongodb';
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    protected $collection = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'coverImage',
        'category',
        'tags',
        'status',
        'publishedAt',
        'author',
        'readTime',
        'views',
    ];

    protected $casts = [
        'tags' => 'array',
        'publishedAt' => 'datetime',
        'views' => 'integer',
        'readTime' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function categoryRef()
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public static function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int) ceil($wordCount / 200));
    }
}
