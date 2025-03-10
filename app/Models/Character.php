<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'description',
        'user_id',
        'project_id',
        'type',
        'goals',
        'fears',
        'history',
        'personality',
        'notes'
    ];

    protected $appends = ['act_contents'];

    protected $casts = [
        'act_contents' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scenes(): BelongsToMany
    {
        return $this->belongsToMany(Scene::class, 'character_scene')
            ->withPivot('dialogue')
            ->withTimestamps();
    }

    public function getActContentsAttribute()
    {
        if (!isset($this->attributes['act_contents'])) {
            $this->attributes['act_contents'] = [];
        }
        return $this->attributes['act_contents'];
    }

    public function setActContentsAttribute($value)
    {
        $this->attributes['act_contents'] = $value;
    }
} 