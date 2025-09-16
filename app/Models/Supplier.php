<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Models\Enabled;

class Supplier extends Model
{
    use HasFactory, Enabled;

    /**
     * Override the Enabled scope to use is_active instead of enabled
     */
    protected static function booted(): void
    {
        // Don't add the global scope for suppliers since we use is_active
    }

    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'phone',
        'address',
        'payment_info',
        'commission_rate',
        'wallet_balance',
        'logo',
        'banner',
        'description',
        'website',
        'tax_number',
        'business_license',
        'is_verified',
        'is_active',
        'verified_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'wallet_balance' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_wallet_balance',
        'formatted_commission_rate',
        'status_label',
        'status_color',
    ];

    /**
     * Get the user that owns the supplier.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hotels for this supplier.
     */
    public function hotels(): HasMany
    {
        return $this->hasMany(SupplierHotel::class);
    }

    /**
     * Get the trips for this supplier.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(SupplierTrip::class);
    }

    /**
     * Get the tours for this supplier.
     */
    public function tours(): HasMany
    {
        return $this->hasMany(SupplierTour::class);
    }

    /**
     * Get the transports for this supplier.
     */
    public function transports(): HasMany
    {
        return $this->hasMany(SupplierTransport::class);
    }

    /**
     * Get the formatted wallet balance attribute.
     */
    public function getFormattedWalletBalanceAttribute(): string
    {
        return number_format($this->wallet_balance, 2) . ' EGP';
    }

    /**
     * Get the formatted commission rate attribute.
     */
    public function getFormattedCommissionRateAttribute(): string
    {
        return $this->commission_rate . '%';
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_verified) {
            return 'Pending Verification';
        }
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get the status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->is_verified) {
            return 'warning';
        }
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * Scope to filter by verified suppliers.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter by active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by company name.
     */
    public function scopeByCompanyName($query, $name)
    {
        return $query->where('company_name', 'LIKE', '%' . $name . '%');
    }

    /**
     * Add amount to wallet balance.
     */
    public function addToWallet(float $amount): void
    {
        $this->increment('wallet_balance', $amount);
    }

    /**
     * Deduct amount from wallet balance.
     */
    public function deductFromWallet(float $amount): bool
    {
        if ($this->wallet_balance >= $amount) {
            $this->decrement('wallet_balance', $amount);
            return true;
        }
        return false;
    }

    /**
     * Calculate commission amount for a given price.
     */
    public function calculateCommission(float $price): float
    {
        return ($price * $this->commission_rate) / 100;
    }

    /**
     * Mark supplier as verified.
     */
    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }
}
