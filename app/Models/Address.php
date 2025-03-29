<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Address extends Model
{
    protected $fillable = [
        'street',
        'street_additional',
        'city',
        'postal_code',
        'country',
    ];

    protected $guarded = [];

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
            $this->postal_code,
            $this->country
        );
    }
}
