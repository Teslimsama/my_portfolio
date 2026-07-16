<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceTier extends Model
{
    protected $fillable = [
        'licensable_product_id',
        'tier_key',
        'duration_days',
        'discount_pct',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'discount_pct' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function licensableProduct(): BelongsTo
    {
        return $this->belongsTo(LicensableProduct::class);
    }

    /** Scope for active tiers only. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
