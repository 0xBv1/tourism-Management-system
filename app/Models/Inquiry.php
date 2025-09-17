<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'admin_notes',
        'client_id',
        'assigned_to',
        'booking_file_id',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => InquiryStatus::class,
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function bookingFile(): BelongsTo
    {
        return $this->belongsTo(BookingFile::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', InquiryStatus::PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', InquiryStatus::CONFIRMED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', InquiryStatus::COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', InquiryStatus::CANCELLED);
    }
}
