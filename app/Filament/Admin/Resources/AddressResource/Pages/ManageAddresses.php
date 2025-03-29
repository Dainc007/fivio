<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AddressResource\Pages;

use App\Filament\Admin\Resources\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

final class ManageAddresses extends ManageRecords
{
    protected static string $resource = AddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
