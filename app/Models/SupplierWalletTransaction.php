<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierWalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'supplier_wallet_transactions';

    protected $fillable = [
        'supplier_id',
        'type',
        'service_name',
        'client_name',
        'amount',
        'commission',
        'status',
        'reference',
        'date',
        'booking_id',
        'booking_type'
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
    ];

    /**
     * Get the supplier that owns the transaction.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Scope to get transactions for a specific supplier.
     */
    public function scopeForSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Scope to get transactions by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get transactions by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' EGP';
    }

    /**
     * Get the formatted commission.
     */
    public function getFormattedCommissionAttribute()
    {
        return number_format($this->commission, 2) . ' EGP';
    }

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $statusColors = [
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'cancelled' => 'secondary'
        ];
        
        $color = $statusColors[$this->status] ?? 'info';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }
}

