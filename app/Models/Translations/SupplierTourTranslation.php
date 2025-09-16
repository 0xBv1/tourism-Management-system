<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class SupplierTourTranslation extends Model
{
    protected $table = 'supplier_tour_translations';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'overview',
        'highlights',
        'excluded',
        'included',
        'duration',
        'type',
        'run',
        'pickup_time',
    ];
}
