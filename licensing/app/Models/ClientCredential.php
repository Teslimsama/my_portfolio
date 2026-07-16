<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientCredential extends Model
{
    const UPDATED_AT = null; // Credentials are immutable after creation (except is_active toggle)

    protected $fillable = [
        'licensable_product_id',
        'client_name',
        'client_key',
        'api_secret',
        'domain',
        'is_active',
    ];

    protected $hidden = [
        'api_secret', // Never expose the hashed secret in JSON responses
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function licensableProduct(): BelongsTo
    {
        return $this->belongsTo(LicensableProduct::class);
    }

    /** Scope for active credentials only. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
