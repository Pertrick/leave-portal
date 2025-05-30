<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supervisor extends Model
{
    protected $fillable = [
        'user_id',
        'supervisor_id',
        'department_id',
        'is_primary',
        'is_active',
        'start_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
} 