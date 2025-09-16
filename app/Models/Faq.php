<?php

namespace App\Models;

use App\Traits\Models\Activated;
use App\Traits\Models\AutoTranslate;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $question
 * @property string $answer
 */
class Faq extends Model
{
    use SoftDeletes, Translatable, Activated, AutoTranslate;

    protected $fillable = [
        'active',
        'tag',
    ];

    public array $translatedAttributes = [
        'question',
        'answer',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'active',
//        'tag',
    ];
}
