<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Model implements AuthenticatableContract, FilamentUser
{
    use Authenticatable;

    protected $connection = 'mongodb';
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    protected $collection = 'users';

    protected $fillable = [
        'email',
        'password',
        'name',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
