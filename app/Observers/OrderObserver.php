<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCreated;
use Illuminate\Support\Facades\Notification;

final class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->load('product');

        User::where('has_access', true)->cursor()->each(function ($user) use ($order) {
            $user->notify(new OrderCreated($order));
        });
    }
}
