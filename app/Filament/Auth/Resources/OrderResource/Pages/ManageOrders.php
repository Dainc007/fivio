<?php

declare(strict_types=1);

namespace App\Filament\Auth\Resources\OrderResource\Pages;

use App\Filament\Auth\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

final class ManageOrders extends ManageRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //            Actions\CreateAction::make(),
        ];
    }
}
