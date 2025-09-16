<?php

namespace App\Traits\Models;

use App\Scopes\Enabled as Scope;

trait Enabled
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new Scope);
    }

    public function scopeActive($query, $active = true)
    {
        return $query->where('enabled', $active);
    }
}
