<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LicensableProduct extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'code_prefix',
        'description',
        'daily_rate',
        'is_active',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function priceTiers(): HasMany
    {
        return $this->hasMany(PriceTier::class);
    }

    public function licenseCodes(): HasMany
    {
        return $this->hasMany(LicenseCode::class);
    }

    public function clientCredentials(): HasMany
    {
        return $this->hasMany(ClientCredential::class);
    }

    /** Scope for active products only. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
