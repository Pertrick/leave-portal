<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserLevel;
use App\Models\Department;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'staff_id',
        'username',
        'firstname',
        'lastname',
        'address',
        'phone',
        'email',
        'department_id',
        'user_level_id',
        'designation',
        'gender',
        'dob',
        'join_date',
        'avatar',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'dob' => 'date',
            'join_date' => 'date',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            $user->uuid = (string) Uuid::uuid4();
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function userLevel(): BelongsTo
    {
        return $this->belongsTo(UserLevel::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveApprovals(): HasMany
    {
        return $this->hasMany(LeaveApproval::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }


    public function getFullNameAttribute(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * Get the user's settings.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get or create user settings.
     */
    public function getSettings(): UserSetting
    {
        return $this->settings ?? $this->settings()->create(UserSetting::getDefaults());
    }


    /**
     * Check if user can manage supervisors
     */
    public function canManageSupervisors(): bool
    {
        return $this->hasRole('supervisor') || $this->hasRole('admin');
    }

    /**
     * Check if user can manage department heads
     */
    public function canManageDepartmentHeads(): bool
    {
        return $this->hasRole('hod') || $this->hasRole('admin');
    }

    /**
     * Check if user can manage departments
     */
    public function canManageDepartments(): bool
    {
        return $this->hasRole('department-manager') || $this->hasRole('admin');
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(): bool
    {
        return $this->hasRole('user-manager') || $this->hasRole('admin');
    }

    /**
     * Check if user can manage leave
     */
    public function canManageLeave(): bool
    {
        return $this->hasRole('leave-manager') || $this->hasRole('admin');
    }

    /**
     * Check if user can view reports
     */
    public function canViewReports(): bool
    {
        return $this->hasRole('report-viewer') || $this->hasRole('admin');
    }

    /**
     * Check if user can manage settings
     */
    public function canManageSettings(): bool
    {
        return $this->hasRole('settings-manager') || $this->hasRole('admin');
    }

    public function supervisor()
    {
        return $this->hasOne(Supervisor::class, 'user_id');
    }

    public function activeSupervisors()
    {
        return $this->supervisor()->where('is_active', true);
    }

    public function primarySupervisor()
    {
        return $this->activeSupervisors()->where('is_primary', true)->first();
    }

    public function supervisedUsers()
    {
        return $this->hasMany(Supervisor::class, 'supervisor_id');
    }

    public function activeSupervisedUsers()
    {
        return $this->supervisedUsers()->where('is_active', true);
    }

    public function departmentHead()
    {
        return $this->department->activeHeads();
    }

    public function isDepartmentHead(): bool
    {
        return $this->department->activeHeads()
            ->where('user_id', $this->id)
            ->exists();
    }

    public function isActingDepartmentHead(): bool
    {
        return $this->department->activeHeads()
            ->where('user_id', $this->id)
            ->where('is_acting', true)
            ->exists();
    }

    public function actingDepartmentHead()
    {
        return $this->department->actingHead();
    }

    public function currentDepartmentHead()
    {
        return $this->department->activeHeads()->first();
    }
}
