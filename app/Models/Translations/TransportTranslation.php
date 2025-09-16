<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class TransportTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];
}
