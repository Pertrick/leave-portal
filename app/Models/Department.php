<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $fillable = [
        'name',
        'location_id',
        'supervisor_id',
        'hod_id'
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function hod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function heads()
    {
        return $this->hasMany(DepartmentHead::class);
    }

    public function supervisors()
    {
        return $this->hasMany(Supervisor::class);
    }

    public function activeHead()
    {
        return $this->hasOne(DepartmentHead::class)
            ->whereNull('end_date')
            ->latest()  // Get the most recently assigned head
            ->with('user');
    }

    public function regularHead()
    {
        return $this->hasOne(DepartmentHead::class)
            ->where('is_acting', false)
            ->whereNull('end_date');
    }

    public function actingHead()
    {
        return $this->hasOne(DepartmentHead::class)
            ->where('is_acting', true)
            ->whereNull('end_date');
    }
} 