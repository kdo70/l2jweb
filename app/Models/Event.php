<?php

namespace App\Models;

use App\Casts\SlugCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;


/**
 * @property mixed $created_at
 */
class Event extends Model
{
    use HasFactory;

    protected $casts = [
        'slug' => SlugCast::class,
    ];

    public function getCreatedDateAttribute()
    {
        return $this->created_at->format('d.m.Y в H:i');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'object');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'object');
    }

    public function rating()
    {
        return $this->ratings->sum('value');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
