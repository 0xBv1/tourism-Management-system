<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class SupplierHotelTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'city',
    ];
}



