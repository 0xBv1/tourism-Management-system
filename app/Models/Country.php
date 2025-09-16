<?php

namespace App\Models;

use App\Traits\Models\Activated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use Activated;

    protected $fillable = [
        'name',
        'code',
        'active',
        'flag',
        'phone_code',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
