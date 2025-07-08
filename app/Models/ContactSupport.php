<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactSupport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'category',
        'priority',
        'status',
        'admin_response',
        'responded_by',
        'responded_at',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'responded_at' => 'datetime',
    ];

    protected $appends = [
        'category_label',
        'priority_label', 
        'status_label',
        'category_color',
        'priority_color',
        'status_color',
    ];

    // Categories for different types of requests
    const CATEGORIES = [
        'technical' => 'Technical Issue',
        'leave_system' => 'Leave System',
        'account' => 'Account Access',
        'policy' => 'Policy Question',
        'complaint' => 'Complaint',
        'suggestion' => 'Suggestion',
        'other' => 'Other',
    ];

    // Priority levels
    const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    // Status options
    const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Unknown';
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? 'Unknown';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'technical' => 'bg-red-800 text-white',
            'leave_system' => 'bg-indigo-100 text-indigo-800',
            'account' => 'bg-blue-100 text-blue-800',
            'policy' => 'bg-green-100 text-green-800',
            'complaint' => 'bg-red-100 text-red-800',
            'suggestion' => 'bg-yellow-100 text-yellow-800',
            'other' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-gray-400 text-gray-800',
            'medium' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
} 