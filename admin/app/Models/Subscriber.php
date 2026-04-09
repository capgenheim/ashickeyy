<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Subscriber extends Model
{
    protected $connection = 'mongodb';

    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    protected $collection = 'email_subscriptions';

    protected $fillable = [
        'email',
        'subscribed_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
