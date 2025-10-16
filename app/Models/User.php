<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 * 
 * This model represents users in the tourism management system.
 * It extends Laravel's Authenticatable class and includes role-based
 * access control through the Spatie Permission package.
 * 
 * Features:
 * - Authentication and authorization
 * - Role-based access control (HasRoles trait)
 * - Soft deletes for data retention
 * - API token authentication (Sanctum)
 * - Computed attributes for display
 * 
 * Traits Used:
 * - HasApiTokens: For API authentication
 * - HasFactory: For model factories
 * - Notifiable: For sending notifications
 * - SoftDeletes: For soft deletion
 * - HasRoles: For role-based permissions
 */
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
     * Get the user's first name from the full name
     * 
     * This accessor extracts the first name from the user's full name.
     * It converts the name to uppercase and takes the first word.
     * 
     * @return Attribute The computed first name attribute
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes): string => 
                $attributes['name'] ? Str::of($attributes['name'])->upper()->explode(' ')[0] : '',
        );
    }

    /**
     * Get the user's phone number with fallback
     * 
     * This accessor returns the user's phone number or 'N/A' if no phone
     * number is set. Useful for display purposes where a phone number
     * is always expected.
     * 
     * @return Attribute The computed phone with code attribute
     */
    protected function phoneWithCode(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->phone ? $this->phone : 'N/A',
        );
    }
}