<?php

namespace App\Models;

use App\Observers\OrderObserver;
use App\Traits\Model\HasFiles;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    protected $casts = [
        'attachment' => 'array',
    ];

    use HasFiles;
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }
}
