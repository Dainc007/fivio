<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use App\Traits\Model\HasFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Offer extends Model
{

    use HasFiles, HasFactory;
    protected $casts = [
        'price' => MoneyCast::class,
        'delivery_price' => MoneyCast::class,
        'attachment' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
