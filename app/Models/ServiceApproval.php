<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'service_type',
        'service_id',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the supplier that owns the service approval.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the admin user who approved/rejected the service.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the actual service model based on service_type and service_id.
     */
    public function service()
    {
        // This method is not suitable for eager loading due to dynamic relationships
        // Use loadServiceRelation() in controller instead
        return null;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by service type.
     */
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope to filter by supplier.
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get the status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get the service type label attribute.
     */
    public function getServiceTypeLabelAttribute(): string
    {
        return ucfirst($this->service_type);
    }

    /**
     * Approve the service.
     */
    public function approve(int $adminId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);
    }

    /**
     * Reject the service.
     */
    public function reject(int $adminId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $adminId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'approved_at' => null,
        ]);
    }

    /**
     * Check if the service is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the service is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the service is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
