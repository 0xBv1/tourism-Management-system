<?php

namespace App\Traits\Models;

use App\Scopes\Activated as ActivatedScope;
use App\Scopes\Enabled as EnabledScope;

trait ActivatedAndEnabled
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ActivatedScope);
        static::addGlobalScope(new EnabledScope);
    }

    public function scopeActive($query, $active = true)
    {
        return $query->where('active', $active);
    }

    public function scopeEnabled($query, $enabled = true)
    {
        return $query->where('enabled', $enabled);
    }
}
