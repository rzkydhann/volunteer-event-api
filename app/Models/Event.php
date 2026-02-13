<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'event_date', 'user_id'];

    protected $casts = ['event_date' => 'datetime'];

    // User yang membuat event ini
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Semua peserta event ini (many-to-many)
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->withTimestamps();
    }
}