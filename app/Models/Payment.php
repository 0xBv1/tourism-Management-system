<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'booking_id',
        'gateway',
        'amount',
        'status',
        'paid_at',
        'transaction_request',
        'transaction_verification',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'transaction_request' => 'array',
        'transaction_verification' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(BookingFile::class, 'booking_id');
    }
}
