<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Offer extends Model
{
    protected $casts = [
        'price' => MoneyCast::class,
    ];

    protected $appends = ['nameWithPrice'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nameWithPriceAttribute(): string
    {
        dd($this);
    }
}
