<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AnalyticsEntry extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'analytics_entries';

    protected $fillable = [
        'ip_address',
        'url',
        'method',
        'user_agent',
        'is_robot',
        'device',
        'platform',
        'browser',
        'country',
        'city',
        'latitude',
        'longitude',
        'response_time_ms',
        'response_status',
        'user_id'
    ];

    protected function casts(): array
    {
        return [
            'is_robot' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'response_time_ms' => 'float',
            'response_status' => 'integer',
        ];
    }

    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }
}
