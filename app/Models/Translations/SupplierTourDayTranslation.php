<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class SupplierTourDayTranslation extends Model
{
    protected $table = 'supplier_tour_day_translations';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'description',
    ];
}

