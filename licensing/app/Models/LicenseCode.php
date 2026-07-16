<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseCode extends Model
{
    const UPDATED_AT = null; // License codes are immutable after creation (except redemption fields)

    protected $fillable = [
        'licensable_product_id',
        'code',
        'duration_days',
        'price_paid',
        'payment_reference',
        'status',
        'redeemed_by_domain',
        'redeemed_by_client_key',
        'redeemed_at',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'price_paid' => 'decimal:2',
        'redeemed_at' => 'datetime',
    ];

    public function licensableProduct(): BelongsTo
    {
        return $this->belongsTo(LicensableProduct::class);
    }

    /** Scope for unused codes only. */
    public function scopeUnused($query)
    {
        return $query->where('status', 'unused');
    }

    /** Scope for redeemed codes only. */
    public function scopeRedeemed($query)
    {
        return $query->where('status', 'redeemed');
    }

    public function isRedeemed(): bool
    {
        return $this->status === 'redeemed';
    }
}
