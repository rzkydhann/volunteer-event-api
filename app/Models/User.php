<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    protected $casts = ['password' => 'hashed'];

    // Event yang DIBUAT user ini
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    // Event yang DIIKUTI user ini (many-to-many)
    public function joinedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')
                    ->withTimestamps();
    }
}