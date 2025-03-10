<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExcelData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'file_name',
        'headers',
        'data'
    ];

    protected $casts = [
        'headers' => 'array',
        'data' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
