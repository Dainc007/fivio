<?php

declare(strict_types=1);

namespace App\Filament\Auth\Resources\OfferResource\Pages;

use App\Filament\Auth\Resources\OfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

final class ManageOffers extends ManageRecords
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //            Actions\CreateAction::make(),
        ];
    }
}
