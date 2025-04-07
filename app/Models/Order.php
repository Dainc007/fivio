<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use App\Observers\OrderObserver;
use App\Traits\Model\HasFiles;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([OrderObserver::class])]
final class Order extends Model
{
    use HasFactory,
        HasFiles;

    protected $casts = [
        'attachment' => 'array',
        'price' => MoneyCast::class,
    ];

    protected $appends = ['userHasSubmittedOffer'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function address(): BelongsTo
    {
        return $this->BelongsTo(Address::class);
    }

    public function getUserHasSubmittedOfferAttribute(): bool
    {
        return $this->userOffers()->exists();
    }

    public function userOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'order_id')
            ->where('user_id', auth()->id());
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }
}
