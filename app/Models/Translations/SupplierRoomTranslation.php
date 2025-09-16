<?php

namespace App\Models\Translations;

use App\Traits\Models\TranslateOnUpdate;
use Illuminate\Database\Eloquent\Model;

class SupplierRoomTranslation extends Model
{
    use TranslateOnUpdate;

    protected $fillable = [
        'name',
        'description',
    ];

    public $timestamps = false;

    function translationFKName(): string
    {
        return 'supplier_room_id';
    }
}
