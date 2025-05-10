<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'date',
        'type',
        'location_id',
        'description',
        'is_recurring'
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean'
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
} 