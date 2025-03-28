<?php

namespace App\Filament\Admin\Resources\OfferResource\Pages;

use App\Filament\Admin\Resources\OfferResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Mail;

class ManageOffers extends ManageRecords
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function afterCreate()
    {
        $users = User::all();
        foreach ($users as $user) {
            Mail::to($user->email)
                ->send(
                    new GenericEmail(
                        subject: 'Test',
                        body: 'Test',
                    )
                );
        }
    }
}
