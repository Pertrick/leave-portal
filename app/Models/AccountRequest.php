<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountRequest extends Model
{
    protected $fillable = [
        'staff_id',
        'status',
        'notes',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime'
    ];

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public static function processed(string $staffId, ?int $processedBy = null): void
    {
        self::where('staff_id', $staffId)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'processed_by' => $processedBy,
                'processed_at' => now()
            ]);
    }
} 