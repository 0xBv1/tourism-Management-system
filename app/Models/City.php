<?php

namespace App\Models;

use App\Traits\Models\HasAutoSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory, HasAutoSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the trips that depart from this city.
     */
    public function departureTrips(): HasMany
    {
        return $this->hasMany(Trip::class, 'departure_city_id');
    }

    /**
     * Get the trips that arrive at this city.
     */
    public function arrivalTrips(): HasMany
    {
        return $this->hasMany(Trip::class, 'arrival_city_id');
    }

    /**
     * Get the supplier trips that depart from this city.
     */
    public function supplierDepartureTrips(): HasMany
    {
        return $this->hasMany(SupplierTrip::class, 'departure_city_id');
    }

    /**
     * Get the supplier trips that arrive at this city.
     */
    public function supplierArrivalTrips(): HasMany
    {
        return $this->hasMany(SupplierTrip::class, 'arrival_city_id');
    }
}
