<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function getFullAddressAttribute(): string
    {
        return sprintf(
            '%s %s, %s, %s %s, %s',
            $this->street,
            $this->street_additional ?? '',
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        );
    }
}
