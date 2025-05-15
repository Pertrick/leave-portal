<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

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

    public static function markAsProcessed(string $staffId, ?int $processedBy = null): void
    {
        $request = self::where('staff_id', $staffId)
            ->where('status', config('account.request_status.pending'))
            ->first();

        if (!$request) {
            Log::warning("Attempted to process non-existent account request for staff_id: {$staffId}");
            return;
        }

        $request->update([
            'status' => config('account.request_status.approved'),
            'processed_by' => $processedBy,
            'processed_at' => now()
        ]);
    }
} 