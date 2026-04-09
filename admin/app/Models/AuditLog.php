<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AuditLog extends Model
{
    protected $connection = 'mongodb';

    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    protected $collection = 'audit_logs';

    protected $fillable = [
        'action',
        'source',
        'user',
        'ip_address',
        'user_agent',
        'resource',
        'resource_id',
        'details',
        'logged_at',
    ];

    protected $casts = [
        'details' => 'array',
        'logged_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Log an audit event.
     */
    public static function record(string $action, string $source, array $extra = []): self
    {
        return static::create(array_merge([
            'action' => $action,
            'source' => $source,
            'user' => auth()->user()?->email ?? 'system',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'logged_at' => now(),
        ], $extra));
    }
}
