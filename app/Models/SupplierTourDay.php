<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class SupplierTourDay extends Model
{
    use Translatable;

    protected $table = 'supplier_tour_days';

    public $translatedAttributes = [
        'title',
        'description',
    ];

    protected $fillable = [
        'supplier_tour_id',
        'day_number',
    ];

    protected $casts = [
        'day_number' => 'integer',
    ];

    public function tour()
    {
        return $this->belongsTo(SupplierTour::class, 'supplier_tour_id');
    }
}

