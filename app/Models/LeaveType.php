<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeaveEntitlement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'requires_medical_proof'
    ];

    protected $casts = [
        'requires_medical_proof' => 'boolean'
    ];

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveEntitlements(): HasMany
    {
        return $this->hasMany(LeaveEntitlement::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }
} 