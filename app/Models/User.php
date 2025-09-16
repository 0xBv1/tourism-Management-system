<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's first name.
     *
     * @return Attribute
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => Str::of($attributes['name'])->upper()->explode(' ')[0],
        );
    }

    /**
     * Get the user's phone number.
     *
     * @return Attribute
     */
    protected function phoneWithCode(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->phone ? $this->phone : 'N/A',
        );
    }

    /**
     * Get the supplier profile for this user.
     */
    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class);
    }

    /**
     * Check if the user is a supplier.
     */
    public function isSupplier(): bool
    {
        return $this->hasRole('Supplier') || $this->hasRole('Supplier Admin');
    }

    /**
     * Check if the user is a supplier admin.
     */
    public function isSupplierAdmin(): bool
    {
        return $this->hasRole('Supplier Admin');
    }
}
