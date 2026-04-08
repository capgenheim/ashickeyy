<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Media extends Model
{
    protected $connection = 'mongodb';
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    protected $collection = 'media';

    protected $fillable = [
        'filename',
        'originalName',
        'url',
        'mimeType',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
