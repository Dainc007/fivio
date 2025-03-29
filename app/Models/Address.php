<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends Model
{
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
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
