<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCreated;

final class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        foreach (User::cursor() as $user) {
            $user->notify(new OrderCreated($order));
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(): void
    {
        //
    }

    /**
     * Handle the order "restored" event.
     */
    public function restored(): void
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     */
    public function forceDeleted(): void
    {
        //
    }
}
