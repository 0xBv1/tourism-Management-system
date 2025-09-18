<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasStatuses
{
    /**
     * Get status field name
     */
    protected function getStatusField(): string
    {
        return $this->statusField ?? 'status';
    }

    /**
     * Get status enum class
     */
    protected function getStatusEnum(): string
    {
        return $this->statusEnum ?? 'App\Enums\Status';
    }

    /**
     * Scope for active status
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where($this->getStatusField(), 'active');
    }

    /**
     * Scope for inactive status
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where($this->getStatusField(), 'inactive');
    }

    /**
     * Scope for pending status
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where($this->getStatusField(), 'pending');
    }

    /**
     * Scope for confirmed status
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where($this->getStatusField(), 'confirmed');
    }

    /**
     * Scope for cancelled status
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where($this->getStatusField(), 'cancelled');
    }

    /**
     * Scope for completed status
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where($this->getStatusField(), 'completed');
    }

    /**
     * Check if model is active
     */
    public function isActive(): bool
    {
        return $this->{$this->getStatusField()} === 'active';
    }

    /**
     * Check if model is inactive
     */
    public function isInactive(): bool
    {
        return $this->{$this->getStatusField()} === 'inactive';
    }

    /**
     * Check if model is pending
     */
    public function isPending(): bool
    {
        return $this->{$this->getStatusField()} === 'pending';
    }

    /**
     * Check if model is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->{$this->getStatusField()} === 'confirmed';
    }

    /**
     * Check if model is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->{$this->getStatusField()} === 'cancelled';
    }

    /**
     * Check if model is completed
     */
    public function isCompleted(): bool
    {
        return $this->{$this->getStatusField()} === 'completed';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        $status = $this->{$this->getStatusField()};
        
        return match($status) {
            'active', 'confirmed', 'completed' => 'success',
            'pending' => 'warning',
            'inactive', 'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        $status = $this->{$this->getStatusField()};
        
        return ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Get available statuses
     */
    public function getAvailableStatuses(): array
    {
        $enumClass = $this->getStatusEnum();
        
        if (class_exists($enumClass) && method_exists($enumClass, 'options')) {
            return $enumClass::options();
        }
        
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
        ];
    }

    /**
     * Change status
     */
    public function changeStatus(string $status): bool
    {
        $availableStatuses = $this->getAvailableStatuses();
        
        if (!array_key_exists($status, $availableStatuses)) {
            return false;
        }
        
        $this->{$this->getStatusField()} = $status;
        return $this->save();
    }
}
